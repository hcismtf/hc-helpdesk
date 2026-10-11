<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use PDO;

class InspectAttachment extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'inspect:attachment';
    protected $description = 'Inspect latest attachments in DB';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $rows = $db->query('SELECT * FROM ticket_attachment ORDER BY id DESC LIMIT 5')->getResultArray();
        $enc = Services::encrypter();

        foreach ($rows as $r) {
            $name = '';
            $path = '';
            try { $name = $enc->decrypt(hex2bin($r['file_name'])); } catch (\Throwable $e) { $name = 'Error: ' . $e->getMessage(); }
            try { $path = $enc->decrypt(hex2bin($r['file_path'])); } catch (\Throwable $e) { $path = 'Error: ' . $e->getMessage(); }

            CLI::write("ID: {$r['id']} | Date: {$r['created_date']}", 'yellow');
            CLI::write("Ticket ID: {$r['ticket_id']}", 'light_gray');
            CLI::write("File Name: {$name}", 'green');
            CLI::write("File Path: {$path}", 'cyan');
            CLI::write("Size in DB: {$r['file_size']} (" . round($r['file_size']/1024) . " KB)", 'magenta');

            $fullPath = FCPATH . 'uploads/' . $path;
            CLI::write("Full Path on disk: {$fullPath}", 'white');
            if (file_exists($fullPath)) {
                $actualSize = filesize($fullPath);
                CLI::write("File exists! Actual size on disk: {$actualSize} bytes (" . round($actualSize/1024) . " KB)", 'green');
            } else {
                CLI::write("File DOES NOT exist on disk!", 'red');
            }
            CLI::newLine();
        }
    }
}
