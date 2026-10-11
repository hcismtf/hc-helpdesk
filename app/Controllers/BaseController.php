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

    
    protected function generatePaginationHTML($currentPage, $totalPages, $baseUrl, $additionalParams = '', $totalRecords = null, $perPage = null)
    {
        $currentPage = (int) $currentPage;
        $totalPages = (int) $totalPages;
        
        if ($totalPages <= 0) $totalPages = 1;
        if ($currentPage <= 0) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;

        $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
        
        $html = '<div class="modern-pagination-container">';
        $html .= '<ul class="pagination modern-pagination-list">';

        // Previous button
        if ($currentPage > 1) {
            $html .= '<li class="page-item"><a class="page-link page-nav-arrow page-prev" href="' . $baseUrl . $separator . 'page=' . ($currentPage - 1) . $additionalParams . '" title="Previous Page">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Previous</span>
            </a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link page-nav-arrow page-prev">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Previous</span>
            </span></li>';
        }

        // Smart pagination with window
        $range = 2;
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);

        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link page-num-btn" href="' . $baseUrl . $separator . 'page=1' . $additionalParams . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link page-ellipsis">…</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="page-item active"><span class="page-link page-num-btn is-active">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link page-num-btn" href="' . $baseUrl . $separator . 'page=' . $i . $additionalParams . '">' . $i . '</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link page-ellipsis">…</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link page-num-btn" href="' . $baseUrl . $separator . 'page=' . $totalPages . $additionalParams . '">' . $totalPages . '</a></li>';
        }

        // Next button
        if ($currentPage < $totalPages) {
            $html .= '<li class="page-item"><a class="page-link page-nav-arrow page-next" href="' . $baseUrl . $separator . 'page=' . ($currentPage + 1) . $additionalParams . '" title="Next Page">
                <span>Next</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link page-nav-arrow page-next">
                <span>Next</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </span></li>';
        }

        $html .= '</ul>';

        // Showing results info if total count is provided
        if ($totalRecords !== null) {
            $perPageCount = $perPage ?? 10;
            $startItem = min($totalRecords, (($currentPage - 1) * $perPageCount) + 1);
            $endItem = min($totalRecords, $currentPage * $perPageCount);
            if ($totalRecords == 0) $startItem = 0;
            $html .= '<div class="pagination-results-info">Showing ' . number_format($endItem) . ' of ' . number_format($totalRecords) . ' results</div>';
        }

        $html .= '</div>';
        return $html;
    }
}
