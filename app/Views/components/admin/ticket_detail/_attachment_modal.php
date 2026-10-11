<?php
/**
 * Ticket Detail Modal: Attachment Preview
 *
 * Popup lightbox pratinjau gambar attachment.
 */
?>
<div id="attachmentModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal" style="max-width:850px; text-align:center;">
        <div class="modal-header-clean">
            <span class="modal-title-clean" id="attachmentModalFilename">Attachment Preview</span>
            <button type="button" class="modal-close-btn" onclick="closeAttachmentModal()">&times;</button>
        </div>
        <img id="attachmentModalImg" src="" alt="Attachment" style="max-width:100%; max-height:70vh; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    </div>
</div>
