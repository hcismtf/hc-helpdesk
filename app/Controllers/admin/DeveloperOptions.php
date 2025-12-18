<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class DeveloperOptions extends BaseController
{
    public function index()
    {
        // Check .env flag first, fallback to environment check
        // You can enable by adding to your project root .env:
        // DEVELOPER_OPTIONS_ENABLED = true
        $enabled = false;

        // Prefer explicit toggle from .env
        if (function_exists('env')) {
            $val = env('DEVELOPER_OPTIONS_ENABLED');
            if ($val !== null) {
                $enabled = in_array(strtolower(trim((string)$val)), ['1', 'true', 'yes', 'on'], true);
            }
        } else {
            // Fallback to getenv
            $val = getenv('DEVELOPER_OPTIONS_ENABLED');
            if ($val !== false) {
                $enabled = in_array(strtolower(trim((string)$val)), ['1', 'true', 'yes', 'on'], true);
            }
        }

        // If no explicit toggle, consider CI environment == development as enabled
        if (! $enabled && defined('CI_ENVIRONMENT') && CI_ENVIRONMENT === 'development') {
            $enabled = true;
        }

        if ($enabled) {
            // Redirect to the local PusatBantuan controller/view instead of external URL
            // Use base_url to build the proper absolute path
            return redirect()->to(base_url('PusatBantuan'));
        }

        // Otherwise show the developer options page
        return view('admin/developer_options');
    }
}