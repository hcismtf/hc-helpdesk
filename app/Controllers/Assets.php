<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Assets extends Controller
{
    public function ticket_js()
    {
        // Path file JS di backend (bukan public)
        $path = APPPATH . 'Resources/js/ticket_form.js';
        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->response->setHeader('Content-Type', 'application/javascript')
            ->setBody(file_get_contents($path));
    }
}