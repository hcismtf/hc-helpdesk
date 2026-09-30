<?php

namespace App\Models;

class PusatBantuanMessage extends BaseModel
{
    protected $table = 'pusban_message';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'message'
    ];
}