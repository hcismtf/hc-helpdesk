<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');
        $validApiKey = $this->getValidApiKey();
        
        // In development, allow if no API key is set
        if (empty($validApiKey)) {
            return; // Allow request to proceed
        }
        
        // In production, validate API key
        if ($apiKey !== $validApiKey) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
    
    private function getValidApiKey()
    {
        $keyFilePath = getenv('report.keyPath') ?: 'D:/helpdeskkey/key';
        
        if (file_exists($keyFilePath)) {
            $key = trim(file_get_contents($keyFilePath));
            if (!empty($key)) {
                return $key;
            }
        }
        
        return ''; // Return empty if file not found or empty
    }
}