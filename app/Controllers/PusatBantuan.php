<?php 

namespace App\Controllers;

use App\Models\PusatBantuanMessage;
use App\Models\FaqModel;

class PusatBantuan extends BaseController
{
    public function pusat_bantuan()
    {
        $messageModel = new PusatBantuanMessage();
        $message = $messageModel->first();

        $faqModel = new FaqModel();
        $dbFaqs = $faqModel->orderBy('id', 'desc')->findAll();

        return view('pusat_bantuan', [
            'message' => $message,
            'dbFaqs'  => $dbFaqs,
        ]);
    }

    public function __construct()
    {
        helper(['date', 'date_indo']);
    }
}