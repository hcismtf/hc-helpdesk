<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Superadmin;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $superadmin;
    
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['date'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->superadmin = new Superadmin();
        date_default_timezone_set('Asia/Jakarta');

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    
    protected function generatePaginationHTML($currentPage, $totalPages, $baseUrl, $additionalParams = '')
    {
        $currentPage = (int) $currentPage;
        $totalPages = (int) $totalPages;
        
        if ($totalPages <= 0) $totalPages = 1;
        if ($currentPage <= 0) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;

        $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
        $html = '<ul class="pagination">';

        // Previous button
        if ($currentPage > 1) {
            $html .= '<li><a href="' . $baseUrl . $separator . 'page=' . ($currentPage - 1) . $additionalParams . '">Back</a></li>';
        } else {
            $html .= '<li class="disabled"><span>Back</span></li>';
        }

        // Page numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="active"><span>' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="' . $baseUrl . $separator . 'page=' . $i . $additionalParams . '">' . $i . '</a></li>';
            }
        }

        // Next button
        if ($currentPage < $totalPages) {
            $html .= '<li><a href="' . $baseUrl . $separator . 'page=' . ($currentPage + 1) . $additionalParams . '">Next</a></li>';
        } else {
            $html .= '<li class="disabled"><span>Next</span></li>';
        }

        $html .= '</ul>';
        return $html;
    }
}
