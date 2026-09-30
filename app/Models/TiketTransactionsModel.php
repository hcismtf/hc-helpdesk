<?php

namespace App\Models;

/**
 * Model Legacy TiketTransactionsModel (Mengarahkan ke ticket_response)
 */
class TiketTransactionsModel extends TicketResponseModel
{
    /**
     * Backward compatibility method
     */
    public function getRepliesByTicketId($ticketId, string $order = 'ASC'): array
    {
        return $this->getResponsesByTicketId($ticketId, $order);
    }
}