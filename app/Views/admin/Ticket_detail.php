<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/Ticket_detail.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/navbar.css') ?>">
</head>
<body>
    <?php 
        // Hanya tampilkan navbar jika sudah login
        if (session('isLoggedIn')) {
            $active = 'tickets'; 
            include('navbar.php'); 
        }

        $uniqueUrl = basename(current_url());
    ?>
    <div class="main-content" id="main-content">
        <?php if (session('isLoggedIn')): ?>
        <div class="page-title">Ticket</div>
        <div class="breadcrumb">
            <a href="<?= base_url('admin/dashboard') ?>" style="color:#234be7;text-decoration:none;">Home</a> &gt;
            <a href="<?= base_url('admin/Ticket_dashboard') ?>" style="color:#234be7;text-decoration:none;">Ticket</a> &gt;
            <span style="color:#222;"><?= esc($ticket['id']) ?></span>
        </div>
        <button class="btn-back" onclick="window.location.href='<?= base_url('admin/Ticket_dashboard') ?>'">Back to Tickets</button>
        <?php endif; ?>
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
            <div id="replyStatusModal" class="modal-user" style="display:none;">
                <div class="modal-user-content modal-user-frame">
                    <div class="modal-user-header" style="width:100%; display:flex; justify-content:space-between; align-items:center;">
                        <span class="modal-user-title">Update Status</span>
                        <span class="modal-user-close" onclick="closeReplyStatusModal()" style="font-size:2rem; cursor:pointer;">&times;</span>
                    </div>

                    <form id="replyStatusForm" method="post" action="<?= base_url('admin/send_reply/' . $uniqueUrl) ?>">
                        
                        <?php if (session('isLoggedIn')): ?>
                            <div class="modal-form-group">
                                <label class="modal-label">Status <span style="color:red">*</span></label>
                                <select name="status" class="modal-input modal-textbox" required>
                                    <option value="">Select Ticket Status</option>
                                    <option value="open" <?= $ticket['ticket_status'] == 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="in_progress" <?= $ticket['ticket_status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="closed" <?= $ticket['ticket_status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </div>

                            <div class="modal-form-group">
                                <label class="modal-label">Priority <span style="color:red">*</span></label>
                                <select name="priority" class="modal-input modal-textbox" required>
                                    <option value="">Select Ticket Priority</option>
                                    <option value="low" <?= $ticket['ticket_priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= $ticket['ticket_priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= $ticket['ticket_priority'] == 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="urgent" <?= $ticket['ticket_priority'] == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>

                            <?php if (!$hasReply || empty($ticket['assigned_to'])): ?>
                            <div class="modal-form-group">
                                <label class="modal-label">Assigned To <span style="color:red">*</span></label>
                                <select name="assigned_to" class="modal-input modal-textbox" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= esc($user['id']) ?>" <?= (isset($assignedName) && $user['name'] == $assignedName) ? 'selected' : '' ?>><?= esc($user['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <input type="hidden" name="status" value="<?= esc($ticket['ticket_status']) ?>">
                            <input type="hidden" name="priority" value="<?= esc($ticket['ticket_priority']) ?>">
                            <?php endif; ?>

                        <div class="modal-form-group">
                            <label class="modal-label">Message <span style="color:red">*</span></label>
                            <textarea name="reply" class="modal-input modal-textbox" style="height:100px;" required placeholder="Type your message here..."></textarea>
                        </div>

                        <div class="modal-user-footer" style="width:100%; display:flex; justify-content:center;">
                            <button type="submit" class="modal-user-submit">Send Reply</button>
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
                    <div class="ticket-info-list">Assigned To: <?= esc($assignedName) ?></div>                         
                </div>
            </div>
            <div class="ticket-section-title">Original Message</div>
            <div class="reply-list">
                <?php if (!empty($replies)):
                    foreach ($replies as $reply):
                        if ($reply['is_user']): // HANYA USER 
                ?>
                            <div class="reply-item">
                                <div>
                                    <strong><?= esc($reply['author']) ?> (User)</strong>
                                    <span class="reply-date"><?= esc(date('d/m/Y H:i:s', strtotime($reply['created_at']))) ?></span>
                                </div>
                                <div class="reply-text">
                                    <?= nl2br(esc($reply['text'])) ?>
                                </div>
                            </div>
                        <?php 
                        endif;
                    endforeach;
                endif; 
                ?>
            </div>
            <?php if (!empty($attachments)): ?>
                <div class="ticket-section-title" style="margin-top:18px;">Attachment</div>
                <div class="ticket-attachment-list" style="display:flex; gap:18px; flex-wrap:wrap;">
                    <?php foreach ($attachments as $att): ?>
                        <div class="ticket-attachment-item" style="text-align:center;">
                            <a href="javascript:void(0);" onclick="showAttachmentModal('<?= base_url('uploads/images-attachment/' . $att['file_path']) ?>')">
                                <img src="<?= base_url('uploads/images-attachment/' . $att['file_path']) ?>" alt="Attachment" style="max-width:80px; max-height:80px; border-radius:8px; border:1px solid #eee; box-shadow:0 2px 8px #eee;">
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
                <?php 
                    $hasAdminReply = false;
                    if (!empty($replies)): 
                        foreach ($replies as $reply): 
                            if (!$reply['is_user']): // HANYA ADMIN
                                $hasAdminReply = true;
                ?>
                    <div class="reply-item">
                        <div>
                            <span class="reply-author"><?= esc($reply['author']) ?> (Admin)</span>
                            <span class="reply-date"><?= esc(date('d/m/Y H:i:s', strtotime($reply['created_at']))) ?></span>
                        </div>
                        <div class="reply-text"><?= esc($reply['text']) ?></div>
                    </div>
                <?php 
                            endif; 
                        endforeach; 
                    endif; 
                
                if (!$hasAdminReply):
                ?>
                <div class="reply-item" style="color:#888;">No admin responses yet.</div>
                <?php endif; ?>
            </div>
            
            <!-- <div class="ticket-section-title">Add Reply</div> -->
             
        <?php 
            $isClosed = ($ticket['ticket_status'] == 'closed');
            $isAdmin = session('isLoggedIn');
        ?>

        <?php if ($isClosed && !$isAdmin): ?>
            <div class="add-reply-box" style="text-align:center; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:15px; border-radius:8px;">
                <strong>Ticket Closed.</strong><br>
                This conversation has been closed. You cannot send further replies.
            </div>
        <?php else: ?>
            <div class="add-reply-box">
                <form action="#" method="post">
                    <!-- <textarea class="add-reply-textarea" name="reply" placeholder="Text Area"></textarea> -->
                    <button type="button" class="btn-send-reply" onclick="showReplyStatusModal()">Send Reply</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Loading Modal -->
        </div> <div id="loadingModal" class="loading-modal-bg" style="display:none;">
            <div class="loading-modal">
                <div class="spinner"></div>
                <div class="loading-text">Mengirim Pesan Anda...</div>
                <div style="font-size: 13px; color: #999; margin-top: 10px;">Silakan tunggu sebentar</div>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/Admin/Ticket_detail.js') ?>"></script>
</body>
</html>