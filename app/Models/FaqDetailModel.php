<?php
namespace App\Models;

class FaqDetailModel extends BaseModel
{
    protected $table = 'faq_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'question',
        'answer',
    ];
}