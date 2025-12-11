<?php

namespace App\Models;
use CodeIgniter\Model;

class PusatBantuanMessage extends Model
{
    protected $table = 'pusban_message';
    protected $allowedFields = [
        'message'
    ];
}