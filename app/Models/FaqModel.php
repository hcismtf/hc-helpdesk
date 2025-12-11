<?php
namespace App\Models;
use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faq_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'question', 'answer', 'created_by', 'created_date', 'modified_by', 'modified_date'
    ];
}