<?php
/**
 * Ticket Detail Modal: Properties & Reassign Specialist
 *
 * Modal dialog untuk memperbarui status, tingkat prioritas, dan menugaskan kembali specialist.
 *
 * @var array  $ticket             Data tiket utama
 * @var string $uniqueUrl          Token unik URL tiket
 * @var array  $users              Daftar staf/user
 * @var array  $assignedSpecialist Specialist saat ini
 */
?>
<div id="replyStatusModal" class="faq-modal-bg" style="display:none;">
    <div class="faq-modal">
        <div class="modal-header-clean">
            <span class="modal-title-clean">Update Ticket Properties</span>
            <button type="button" class="modal-close-btn" onclick="closeReplyStatusModal()">&times;</button>
        </div>

        <form id="replyStatusForm" method="post" action="<?= base_url('admin/send_reply/' . $uniqueUrl) ?>">
            <?php if (session('isLoggedIn')): ?>
                <!-- Status -->
                <div class="modal-form-group">
                    <label class="modal-form-label">Ticket Status <span style="color:#ef4444">*</span></label>
                    <?php
                        $statusOptions = [];
                        $definitions = !empty($statusDefinitions) ? $statusDefinitions : \App\Entities\TicketStatus::getDefinitions();
                        foreach ($definitions as $sCode => $sDef) {
                            $statusOptions[$sCode] = $sDef['label'] ?? ($sDef['name'] ?? ucwords(str_replace('_', ' ', $sCode)));
                        }
                    ?>
                    <?= view('components/common/_basic_select', [
                        'name'          => 'status',
                        'id'            => 'modal_status_select',
                        'placeholder'   => 'Select Ticket Status',
                        'selectedValue' => $ticket['ticket_status'] ?? 'open',
                        'options'       => $statusOptions,
                        'required'      => true
                    ]) ?>
                </div>

                <!-- Priority -->
                <div class="modal-form-group">
                    <label class="modal-form-label">Priority Level <span style="color:#ef4444">*</span></label>
                    <?= view('components/common/_basic_select', [
                        'name'          => 'priority',
                        'id'            => 'modal_priority_select',
                        'placeholder'   => 'Select Priority Level',
                        'selectedValue' => $ticket['ticket_priority'] ?? 'medium',
                        'options'       => [
                            'low'    => 'Low Priority',
                            'medium' => 'Medium Priority',
                            'high'   => 'High Priority',
                            'urgent' => 'Urgent Priority'
                        ],
                        'required'      => true
                    ]) ?>
                </div>

                <!-- Assigned PIC Helpdesk (Wajib) -->
                <div class="modal-form-group">
                    <label class="modal-form-label">PIC Helpdesk <span style="color:#ef4444">*</span></label>
                    <?php
                        $userOptions = [];
                        if (!empty($users)) {
                            foreach ($users as $u) {
                                $roleId = is_object($u) ? ($u->role_id ?? null) : ($u['role_id'] ?? null);
                                if (!empty($roleId)) {
                                    $uId = is_object($u) ? $u->id : $u['id'];
                                    $uName = is_object($u) ? $u->name : $u['name'];
                                    $userOptions[$uId] = $uName;
                                }
                            }
                        }
                    ?>
                    <?= view('components/common/_basic_select', [
                        'name'          => 'pic_helpdesk_id',
                        'id'            => 'modal_assigned_select',
                        'placeholder'   => 'Select PIC Helpdesk',
                        'selectedValue' => $ticket['pic_helpdesk_id'] ?? ($ticket['assigned_to'] ?? ($picHelpdesk['id'] ?? '')),
                        'options'       => $userOptions,
                        'required'      => true
                    ]) ?>
                </div>

                <!-- Assigned PIC Developer / BA (Opsional) -->
                <div class="modal-form-group">
                    <label class="modal-form-label">PIC Developer / BA <span style="font-weight:400; color:#64748b; font-size:12px;">(Opsional)</span></label>
                    <?= view('components/common/_basic_select', [
                        'name'          => 'pic_dev_ba_id',
                        'id'            => 'modal_pic_dev_select',
                        'placeholder'   => '-- Belum Ditugaskan / None --',
                        'selectedValue' => $ticket['pic_dev_ba_id'] ?? ($picDevBa['id'] ?? ''),
                        'options'       => $userOptions,
                        'required'      => false
                    ]) ?>
                </div>
            <?php else: ?>
                <input type="hidden" name="status" value="<?= esc($ticket['ticket_status'] ?? ($ticket['status'] ?? 'open')) ?>">
                <input type="hidden" name="priority" value="<?= esc($ticket['ticket_priority'] ?? ($ticket['sla_status'] ?? 'medium')) ?>">
            <?php endif; ?>

            <!-- Activity Note / Update Message with Public vs Internal toggle -->
            <div class="modal-form-group">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <label class="modal-form-label" style="margin-bottom:0;">Activity Note / Update Message <span style="color:#ef4444">*</span></label>
                    <div class="modal-note-type-toggle">
                        <button type="button" class="btn-note-toggle active" id="modal-tab-public" onclick="switchModalNoteTab('public')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span>Public</span>
                        </button>
                        <button type="button" class="btn-note-toggle tab-internal" id="modal-tab-internal" onclick="switchModalNoteTab('internal')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span>Internal</span>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="is_internal" id="modalIsInternal" value="0">
                <textarea name="reply" id="modalReplyTextarea" class="modal-form-textarea" required placeholder="Tuliskan pesan penjelasan atau update penyelesaian tiket untuk pemohon... (Pesan ini dapat dibaca oleh pemohon)"></textarea>
            </div>

            <div class="modal-footer-clean">
                <button type="button" class="btn-modal-cancel" onclick="closeReplyStatusModal()">Cancel</button>
                <button type="submit" class="btn-modal-submit" id="btnModalSubmit">Update & Save</button>
            </div>
        </form>
    </div>
</div>
