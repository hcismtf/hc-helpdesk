<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;

class Report extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function sla()
    {
        $query = $this->db->query("
            SELECT t.id, t.title, t.status, 
                   s.priority, s.response_time, s.resolution_time,
                   TIMESTAMPDIFF(HOUR, t.created_date, NOW()) as hours_passed
            FROM ticket_trx t
            JOIN sla_configuration s ON t.priority = s.priority
        ");

        return $this->response->setJSON($query->getResult());
    }
}
