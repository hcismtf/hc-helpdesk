<?php

namespace App\Services;

use Config\Database;
use CodeIgniter\Database\BaseConnection;

class TicketService
{
  protected BaseConnection $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function generateTicketNo(string $prefix = 'HC'): string
  {
    $row = $this->db->query("
      SELECT ticket_no 
      FROM tickets 
      WHERE ticket_no LIKE '{$prefix}-%' 
      ORDER BY LENGTH(ticket_no) DESC, ticket_no DESC 
      LIMIT 1 
      FOR UPDATE
    ")->getRow();

    if (!$row || empty($row->ticket_no)) {
      $nextNumber = 1;
    } else {
      $lastNumber = (int) substr($row->ticket_no, strlen($prefix) + 1);
      $nextNumber = $lastNumber + 1;
    }

    return sprintf('%s-%06d', $prefix, $nextNumber);
  }
}