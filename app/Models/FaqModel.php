<?php
namespace App\Models;

class FaqModel extends BaseModel
{
    protected $table = 'faq_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'question',
        'answer',
    ];
}