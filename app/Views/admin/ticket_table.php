
<table class="tickets-table" id="ticketsTable">
    <thead>
        <tr>
            <th>Ticket ID</th>
            <th>NIP</th>
            <th>Type</th>
            <th>Created Date</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($tickets)): ?>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?= isset($ticket['id']) ? esc($ticket['id']) : '-' ?></td>
                <td><?= isset($ticket['emp_nip']) ? esc($ticket['emp_nip']) : '-' ?></td>
                <td><?= isset($ticket['req_type']) ? esc($ticket['req_type']) : '-' ?></td>
                <td>
                    <?php
                        if (!empty($ticket['created_date'])) {
                            echo esc(date('d/m/Y H:i:s', strtotime($ticket['created_date'])));
                        } else {
                            echo '-';
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if (isset($ticket['ticket_status']) && $ticket['ticket_status'] == 'in_progress') {
                            echo 'In Progress';
                        } else {
                            echo isset($ticket['ticket_status']) ? esc($ticket['ticket_status']) : '-';
                        }
                    ?>
                </td>
                <td>
                    <?php
                        $priority = isset($ticket['ticket_priority']) ? ucfirst($ticket['ticket_priority']) : '-';
                        if ($priority !== '-') {
                            echo '<span class="priority-badge priority-' . $priority . '">' . esc($priority) . '</span>';
                        } else {
                            echo '-';
                        }
                    ?>
                </td>
                <td>
                    <a href="<?= base_url('admin/Ticket_detail/' . esc($ticket['id'])) ?>">
                        <button class="btn-open">Open</button>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;">No tickets found.</td>
        </tr>
    <?php endif ?>
    </tbody>
</table>
<?php if ($totalTickets > $perPage): ?>
    <div class="pagination">
        <?= $pager->links('tickets', 'default_full', [
            'per_page' => $perPage,
            'priority' => $priority,
            'start' => $start,
            'end' => $end
        ]) ?>
    </div>
    
<?php endif; ?>