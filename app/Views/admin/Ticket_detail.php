<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/Ticket_detail.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
</head>
<body>
    <?php $active = 'tickets'; include('navbar.php'); ?>
    <div class="main-content" id="main-content">
        <div class="page-title">Ticket</div>
        <div class="breadcrumb">
            <a href="<?= base_url('admin/dashboard') ?>" style="color:#234be7;text-decoration:none;">Home</a> &gt;
            <a href="<?= base_url('admin/Ticket_dashboard') ?>" style="color:#234be7;text-decoration:none;">Ticket</a> &gt;
            <span style="color:#222;"><?= esc($ticket['id']) ?></span>
        </div>
        <button class="btn-back" onclick="window.location.href='<?= base_url('admin/Ticket_dashboard') ?>'">Back to Home</button>
        <div class="ticket-detail-box">
            <div class="header-row">
                <div>
                    <div class="ticket-id-title">Ticket #<?= esc($ticket['id']) ?></div>
                    <?php if (!empty($ticket['ticket_status'])): ?>
                        <span class="ticket-status <?= esc($ticket['ticket_status']) ?>">
                            <?= ucwords(str_replace('_',' ',esc($ticket['ticket_status']))) ?>
                        </span>
                    <?php endif ?>
                    <?php if (!empty($ticket['ticket_priority'])): ?>
                        <span class="ticket-priority <?= esc($ticket['ticket_priority']) ?>">
                            <?= ucfirst(esc($ticket['ticket_priority'])) ?>
                        </span>
                    <?php endif ?>
                </div>
                <button class="btn-update-status" onclick="showStatusModal()">Update Status</button>
            </div>
            <div id="statusModal" class="modal-status" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <span style="font-size:20px;font-weight:600;">Update Status</span>
                        <span class="close-modal" onclick="closeStatusModal()">&times;</span>
                    </div>
                    <form action="<?= base_url('admin/Ticket_update_status/' . esc($ticket['id'])) ?>" method="post">
                        <div class="modal-body">
                            <label for="status" style="font-weight:600;">Status</label>
                            <select name="status" id="status" class="modal-select">
                                <option value="">Select Ticket Status</option>
                                <option value="open" <?= $ticket['ticket_status']=='open'?'selected':'' ?>>Open</option>
                                <option value="in_progress" <?= $ticket['ticket_status']=='in_progress'?'selected':'' ?>>In Progress</option>
                                <option value="closed" <?= $ticket['ticket_status']=='closed'?'selected':'' ?>>Closed</option>
                            </select>
                            <label for="priority" style="font-weight:600; margin-top:16px; display:block;">Priority</label>
                            <select name="priority" id="priority" class="modal-select">
                                <option value="">Select Ticket Priority</option>
                                <option value="low" <?= $ticket['ticket_priority']=='low'?'selected':'' ?>>Low</option>
                                <option value="medium" <?= $ticket['ticket_priority']=='medium'?'selected':'' ?>>Medium</option>
                                <option value="high" <?= $ticket['ticket_priority']=='high'?'selected':'' ?>>High</option>
                                <option value="urgent" <?= $ticket['ticket_priority']=='urgent'?'selected':'' ?>>Urgent</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn-modal-submit">Submit Status</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="ticket-subject"><?= esc($ticket['subject'] ?? '') ?></div>
            <hr class="divider">
            <div style="margin-bottom:12px;">
                
            </div>
            <div style="margin-bottom:12px;">
                <?= esc($ticket['emp_position'] ?? '') ?>
            </div>
            <div class="ticket-info-row">
                <div class="ticket-info-col">
                    <div class="ticket-info-title">Employee Information</div>
                    <div class="ticket-info-list">Name: <?= esc($ticket['emp_name'] ?? '') ?></div>
                    <div class="ticket-info-list">NIP: <?= esc($ticket['emp_nip'] ?? '') ?></div>
                    <div class="ticket-info-list">Email: <?= esc($ticket['email'] ?? '') ?></div>
                    <div class="ticket-info-list">Whatsapp: <?= esc($ticket['wa_no'] ?? '') ?></div>
                </div>
                <div class="ticket-info-col">
                    <div class="ticket-info-title">Ticket Information</div>
                    <div class="ticket-info-list">Request type: <?= esc($ticket['req_type'] ?? '') ?></div>
                    <div class="ticket-info-list">Created Date: <?= !empty($ticket['created_date']) ? esc(date('d/m/Y H:i:s', strtotime($ticket['created_date']))) : '' ?></div>
                </div>
            </div>
            <div class="ticket-section-title">Original Message</div>
            <div class="ticket-message-box"><?= esc($ticket['message'] ?? '') ?></div>
            <hr class="divider">
            <div class="ticket-section-title">Replies</div>
            <div class="reply-list">
                <?php if (!empty($replies)): ?>
                    <?php foreach ($replies as $reply): ?>
                        <div class="reply-item">
                            <div>
                                <span class="reply-author"><?= esc($reply['author']) ?></span>
                                <span class="reply-date"><?= esc(date('d/m/Y H:i:s', strtotime($reply['created_at']))) ?></span>
                            </div>
                            <div class="reply-text"><?= esc($reply['text']) ?></div>
                        </div>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="reply-item" style="color:#888;">No replies yet.</div>
                <?php endif ?>
            </div>
            <div class="ticket-section-title">Add Reply</div>
            <div class="add-reply-box">
                <form action="#" method="post">
                    <textarea class="add-reply-textarea" name="reply" placeholder="Text Area"></textarea>
                    <button type="submit" class="btn-send-reply">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
    <script>
    function showStatusModal() {
        document.getElementById('statusModal').style.display = 'flex';
        document.addEventListener('keydown', escCloseModal);
    }
    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
        document.removeEventListener('keydown', escCloseModal);
    }
    function escCloseModal(e) {
        if (e.key === "Escape") {
            closeStatusModal();
        }
    }
    </script>
</body>
</html>