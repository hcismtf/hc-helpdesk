<?php

namespace App\Controllers;

use App\Models\TicketAttachmentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
use Throwable;

/**
 * Controller Penyajian & Pengunduhan Attachment Berkas Terkompresi
 * Menerapkan HTTP Content-Encoding: gzip untuk dekompresi otomatis in-flight pada browser pengguna.
 */
class AttachmentController extends BaseController
{
    protected TicketAttachmentModel $attachmentModel;

    public function __construct()
    {
        $this->attachmentModel = new TicketAttachmentModel();
    }

    /**
     * Preview attachment (inline rendering di tab baru atau modal viewer)
     * Contoh: /attachment/view/attachments/pdf/abc.pdf.gz
     */
    public function view(string ...$pathSegments)
    {
        $relativePath = implode('/', $pathSegments);
        return $this->serveAttachment($relativePath, 'inline');
    }

    /**
     * Download attachment (force download dengan nama asli yang terdekripsi)
     * Contoh: /attachment/download/attachments/pdf/abc.pdf.gz
     */
    public function download(string ...$pathSegments)
    {
        $relativePath = implode('/', $pathSegments);
        return $this->serveAttachment($relativePath, 'attachment');
    }

    /**
     * Melayani berkas secara aman dengan penanganan Content-Encoding gzip
     */
    protected function serveAttachment(string $relativePath, string $disposition)
    {
        $relativePath = ltrim($relativePath, '/\\');

        // Cari berkas di direktori uploads
        $baseUploadDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

        $candidatePaths = [
            $baseUploadDir . str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
            $baseUploadDir . str_replace('/', DIRECTORY_SEPARATOR, $relativePath) . '.gz',
            $baseUploadDir . 'attachments' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
            $baseUploadDir . 'images-attachment' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
        ];

        $filePath = false;
        foreach ($candidatePaths as $candidate) {
            $real = realpath($candidate);
            if ($real !== false && is_file($real) && str_starts_with($real, realpath($baseUploadDir))) {
                $filePath = $real;
                break;
            }
        }

        if ($filePath === false) {
            throw PageNotFoundException::forPageNotFound('Berkas lampiran tidak ditemukan.');
        }

        $fileContent = file_get_contents($filePath);
        $fileSize    = strlen($fileContent);

        // Deteksi apakah berkas terkompresi Gzip (Magic bytes: \x1f\x8b\x08)
        $isGzip = ($fileSize >= 3 && substr($fileContent, 0, 3) === "\x1f\x8b\x08");

        // Coba deteksi nama asli dan MIME type dari database jika ada
        $originalFileName = basename($filePath);
        if (str_ends_with($originalFileName, '.gz')) {
            $originalFileName = substr($originalFileName, 0, -3);
        }

        $mimeType = $this->resolveMimeType($originalFileName, $isGzip, $fileContent);

        // Cari catatan attachment di database untuk mendapatkan nama asli terdekripsi
        $encrypter = Services::encrypter();
        $record = $this->findAttachmentRecord($relativePath, $filePath);
        if ($record && !empty($record->getFileName())) {
            try {
                $decryptedName = $encrypter->decrypt(hex2bin($record->getFileName()));
                if (!empty($decryptedName)) {
                    $originalFileName = $decryptedName;
                }
            } catch (Throwable $e) {}
        }

        $acceptEncoding = $this->request->getHeaderLine('Accept-Encoding');
        $supportsGzip   = stripos($acceptEncoding, 'gzip') !== false;

        $response = $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', $disposition . '; filename="' . rawurlencode($originalFileName) . '"')
            ->setHeader('Cache-Control', 'public, max-age=86400');

        // Jika berkas terkompresi Gzip dan browser mendukung Gzip:
        // Kirimkan header Content-Encoding: gzip sehingga browser mendekompensasi secara otomatis in-flight!
        if ($isGzip && $supportsGzip) {
            return $response
                ->setHeader('Content-Encoding', 'gzip')
                ->setHeader('Content-Length', (string) $fileSize)
                ->setBody($fileContent);
        }

        // Jika berkas terkompresi Gzip namun client tidak mendukung gzip: dekompresi sebelum dikirim
        if ($isGzip && !$supportsGzip) {
            $uncompressed = @gzdecode($fileContent);
            if ($uncompressed !== false) {
                return $response
                    ->setHeader('Content-Length', (string) strlen($uncompressed))
                    ->setBody($uncompressed);
            }
        }

        // Berkas normal / tidak terkompresi
        return $response
            ->setHeader('Content-Length', (string) $fileSize)
            ->setBody($fileContent);
    }

    /**
     * Mencari record attachment berdasarkan file_path
     */
    protected function findAttachmentRecord(string $relativePath, string $realPath)
    {
        $fileName = basename($realPath);
        $encrypter = Services::encrypter();

        $records = $this->attachmentModel->findAll();
        foreach ($records as $r) {
            $storedPath = $r->getFilePath();
            try {
                $decPath = $encrypter->decrypt(hex2bin($storedPath));
                if (str_contains($decPath, $fileName) || str_contains($decPath, $relativePath)) {
                    return $r;
                }
            } catch (Throwable $e) {
                if (str_contains($storedPath, $fileName)) {
                    return $r;
                }
            }
        }

        return null;
    }

    /**
     * Resolusi MIME type yang akurat
     */
    protected function resolveMimeType(string $fileName, bool $isGzip, string $content): string
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $mimeMap = [
            'pdf'  => 'application/pdf',
            'webp' => 'image/webp',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls'  => 'application/vnd.ms-excel',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc'  => 'application/msword',
            'csv'  => 'text/csv',
            'txt'  => 'text/plain',
        ];

        if (isset($mimeMap[$ext])) {
            return $mimeMap[$ext];
        }

        if (!$isGzip) {
            $detected = mime_content_type($content);
            if ($detected) return $detected;
        }

        return 'application/octet-stream';
    }
}
