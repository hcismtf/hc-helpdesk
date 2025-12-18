<?php 

namespace App\Controllers;

use App\Models\PusatBantuanMessage;

// use CodeIgniter\Controller;

class PusatBantuan extends BaseController
{
    public function pusat_bantuan()
    {
        $model = new PusatBantuanMessage();
        $message = $model->first();

        return view('pusat_bantuan',[
            'message' => $message
        ]);
    }

    public function __construct()
    {
        helper('date','date_indo');
    }
}