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
        <button class="btn-back" onclick="window.location.href='<?= base_url('admin/Ticket_dashboard') ?>'">Back to Tickets</button>
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

            </div>
            
            <!-- Modal Reply Status -->
            <!-- Modal Reply Status -->
            <div id="replyStatusModal" class="modal-user" style="display:none;">
                <div class="modal-user-content modal-user-frame">
                    <div class="modal-user-header" style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                        <span class="modal-user-title">Update Status</span>
                        <span class="modal-user-close" onclick="closeReplyStatusModal()" style="font-size:2rem; cursor:pointer;">&times;</span>
                    </div>
                    <form id="replyStatusForm" method="post" action="<?= base_url('admin/send_reply/' . esc($ticket['id'])) ?>">
                        <div class="modal-form-group">
                            <label class="modal-label">Status <span style="color:red">*</span></label>
                            <select name="status" class="modal-input modal-textbox" required>
                                <option value="">Select Ticket Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <?php if (empty($ticket['ticket_priority'])): ?>
                        <div class="modal-form-group">
                            <label class="modal-label">Priority <span style="color:red">*</span></label>
                            <select name="priority" class="modal-input modal-textbox" required>
                                <option value="">Select Ticket Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <?php if (!$hasReply): ?>
                        <div class="modal-form-group">
                            <label class="modal-label">Assigned To <span style="color:red">*</span></label>
                            <select name="assigned_to" class="modal-input modal-textbox" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= esc($user['id']) ?>"><?= esc($user['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="modal-form-group">
                            <label class="modal-label">Add Reply <span style="color:red">*</span></label>
                            <textarea name="reply" class="modal-input modal-textbox" style="height:100px;" required placeholder="Text Area"></textarea>
                        </div>
                        <div class="modal-user-footer" style="width:100%; display:flex; justify-content:center;">
                            <button type="submit" class="modal-user-submit">Submit Status</button>
                        </div>
                        <?php if (!empty($ticket['ticket_priority'])): ?>
                            <input type="hidden" name="priority" value="<?= esc($ticket['ticket_priority']) ?>">
                        <?php endif; ?>
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
                    <div class="ticket-info-list">Assigned To: <?= esc($assignedName) ?></div>               
                </div>
            </div>
            <div class="ticket-section-title">Original Message</div>
            <div class="ticket-message-box"><?= esc($ticket['message'] ?? '') ?></div>
            <?php if (!empty($attachments)): ?>
                <div class="ticket-section-title" style="margin-top:18px;">Attachment</div>
                <div class="ticket-attachment-list" style="display:flex; gap:18px; flex-wrap:wrap;">
                    <?php foreach ($attachments as $att): ?>
                        <div class="ticket-attachment-item" style="text-align:center;">
                            <a href="javascript:void(0);" onclick="showAttachmentModal('<?= base_url('admin/view/' . esc($att['file_path'])) ?>')">
                                <img src="<?= base_url('admin/view/' . esc($att['file_path'])) ?>" alt="Attachment" style="max-width:80px; max-height:80px; border-radius:8px; border:1px solid #eee; box-shadow:0 2px 8px #eee;">
                            </a>
                            <div style="font-size:13px; margin-top:4px;"><?= esc($att['file_name']) ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif; ?>

            <!-- Modal Popup Attachment -->
            <div id="attachmentModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
                <div id="attachmentModalContent" style="background:#fff; border-radius:18px; padding:24px; box-shadow:0 2px 16px #aaa; max-width:90vw; max-height:90vh; display:flex; flex-direction:column; align-items:center; position:relative;">
                    <span onclick="closeAttachmentModal()" style="position:absolute; top:-5px; right:1px; font-size:2rem; cursor:pointer; width:40px; height:40px; display:flex; align-items:center; justify-content:center;">&times;</span>
                    <img id="attachmentModalImg" src="" alt="Attachment" style="max-width:80vw; max-height:70vh; border-radius:12px; box-shadow:0 2px 12px #eee;">
                    <div id="attachmentModalFilename" style="margin-top:12px; font-size:15px; color:#222;"></div>
                </div>
            </div>
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
            
            <!-- <div class="ticket-section-title">Add Reply</div> -->
            <div class="add-reply-box">
                <form action="#" method="post">
                    <!-- <textarea class="add-reply-textarea" name="reply" placeholder="Text Area"></textarea> -->
                    <button type="button" class="btn-send-reply" onclick="showReplyStatusModal()">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function showAttachmentModal(url) {
            document.getElementById('attachmentModalImg').src = url;
            // Ambil nama file dari url
            var filename = url.split('/').pop();
            document.getElementById('attachmentModalFilename').textContent = filename;
            document.getElementById('attachmentModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.addEventListener('keydown', escCloseAttachmentModal);
        }
        function closeAttachmentModal() {
            document.getElementById('attachmentModal').style.display = 'none';
            document.getElementById('attachmentModalImg').src = '';
            document.body.style.overflow = '';
            document.removeEventListener('keydown', escCloseAttachmentModal);
        }
        function escCloseAttachmentModal(e) {
            if (e.key === "Escape") {
                closeAttachmentModal();
            }
        }
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
    function showReplyStatusModal() {
        document.getElementById('replyStatusModal').style.display = 'flex';
        document.addEventListener('keydown', escCloseReplyModal);
    }
    function closeReplyStatusModal() {
        document.getElementById('replyStatusModal').style.display = 'none';
        document.removeEventListener('keydown', escCloseReplyModal);
    }
    function escCloseReplyModal(e) {
        if (e.key === "Escape") {
            closeReplyStatusModal();
        }
    }
    </script>
</body>
</html>