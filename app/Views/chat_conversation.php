<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= esc($ticket['id']) ?> - Conversation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/chat_conversation.css') ?>">
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="header-left">
                <?php 
                    // Tentukan URL Back berdasarkan role
                    $isAdminAccess = isset($isAdminAccess) && $isAdminAccess == true;
                    $backUrl = $isAdminAccess ? base_url('admin/Ticket_dashboard') : base_url('ticket/' . $ticket['id']);
                ?>
                <a href="<?= $backUrl ?>" class="btn-back" title="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                
                <div class="header-info">
                    <h2>Ticket #<?= esc($ticket['id']) ?></h2>
                    <div class="header-subtitle">
                        <?= esc($ticket['subject'] ?? 'No Subject') ?>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
                <button onclick="window.location.reload()" class="btn-refresh" title="Refresh Chat">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                
                <?php 
                    $statusClass = 'status-open';
                    $status = strtolower($ticket['ticket_status']);
                    if(strpos($status, 'progress') !== false) $statusClass = 'status-progress';
                    if($status == 'closed') $statusClass = 'status-closed';
                ?>
                <span class="status-badge <?= $statusClass ?>">
                    <?= esc(str_replace('_', ' ', $ticket['ticket_status'])) ?>
                </span>
            </div>
        </div>

        <div class="chat-box" id="chatBox">
            <div class="ticket-meta">
                <span>
                    Priority: <strong><?= esc(ucfirst($ticket['ticket_priority'] ?? '-')) ?></strong> &bull; 
                    Created: <?= esc(date('d M Y, H:i', strtotime($ticket['created_date']))) ?>
                </span>
            </div>

            <?php if (!empty($chatMessages)): ?>
                <?php foreach ($chatMessages as $message): ?>
                    <?php 
                        // validasi pengirim pesan
                        $isSenderUser = ($message['reply_by'] === $ticket['emp_name']);

                        $positionClass = '';

                        if ($isAdminAccess) {
                            // VIEW ADMIN:
                            $positionClass = $isSenderUser ? 'msg-left' : 'msg-right';
                        } else {
                            // VIEW USER:
                            $positionClass = $isSenderUser ? 'msg-right' : 'msg-left';
                        }
                    ?>
                    
                    <div class="message-wrapper <?= $positionClass ?>">
                        <span class="sender-name">
                            <?= esc($message['reply_by']) ?>
                        </span>
                        <div class="bubble">
                            <?= nl2br(esc($message['message'])) ?>
                            
                            <?php if(!empty($message['file_attachment'])): ?>
                                <div style="margin-top:8px; padding-top:8px; border-top:1px solid rgba(255,255,255,0.2); display:flex; align-items:center; gap:5px;">
                                    <i class="fa-solid fa-paperclip"></i> Attachment
                                </div>
                            <?php endif; ?>

                            <span class="timestamp">
                                <?= esc(date('H:i', strtotime($message['created_date']))) ?>
                                <?php if($positionClass === 'msg-right'): ?>
                                    <i class="fa-solid fa-check-double" style="margin-left:4px;"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-regular fa-paper-plane" style="font-size:3rem; margin-bottom:15px; color:#d1d5db;"></i>
                    <p>Belum ada percakapan.<br>Mulai diskusi dengan mengirim pesan.</p>
                </div>
            <?php endif ?>
        </div>

        <div class="chat-input-area">
            <?php if(strtolower($ticket['ticket_status']) !== 'closed'): ?>
                <?php 
                    $formAction = $isAdminAccess 
                        ? base_url('admin/send_reply/' . $ticket['id']) 
                        : base_url('send-message'); 
                ?>
                <form action="<?= $formAction ?>" method="post" class="input-form">
                    <input type="hidden" name="ticket_id" value="<?= esc($ticket['id']) ?>">
                    
                    <div class="input-wrapper">
                        <textarea name="<?= $isAdminAccess ? 'reply' : 'message' ?>" id="msgInput" placeholder="Ketik pesan Anda..." required rows="1"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-send" title="Kirim Pesan">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            <?php else: ?>
                <div class="closed-notice">
                    <i class="fa-solid fa-lock"></i> 
                    <span>Tiket ini telah ditutup. Anda tidak dapat membalas lagi.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-scroll saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            var chatBox = document.getElementById("chatBox");
            if(chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });

        // Auto resize textarea (agar input membesar sesuai teks)
        const tx = document.getElementsByTagName("textarea");
        for (let i = 0; i < tx.length; i++) {
            tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
            tx[i].addEventListener("input", OnInput, false);
        }

        function OnInput() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + "px";
            // Batasi tinggi maksimal agar tidak menutupi layar
            if(this.scrollHeight > 120) {
                this.style.overflowY = "scroll";
            } else {
                this.style.overflowY = "hidden";
            }
        }
    </script>
</body>
</html>