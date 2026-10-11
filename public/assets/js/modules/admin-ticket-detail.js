/**
 * Enterprise SaaS Ticket Detail Controller Module (Admin Experience)
 * Compliant with Clean Architecture, Zero Trust & Anti-Monolith Guidelines
 */
(function (window, document) {
    'use strict';

    var pendingStatusChange = {
        newStatus: null,
        newLabel: null
    };

    var detailToastTimeout = null;

    // 1. Modals Control
    function showReplyStatusModal() {
        var modal = document.getElementById('replyStatusModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeReplyStatusModal() {
        var modal = document.getElementById('replyStatusModal');
        if (modal) modal.style.display = 'none';
    }

    function showAttachmentModal(src) {
        var img = document.getElementById('attachmentModalImg');
        var modal = document.getElementById('attachmentModal');
        if (img) img.src = src;
        if (modal) modal.style.display = 'flex';
    }

    function closeAttachmentModal() {
        var modal = document.getElementById('attachmentModal');
        if (modal) modal.style.display = 'none';
    }

    // 2. Reply Composer Handlers
    function toggleCloseCheckbox(cb) {
        var composerStatus = document.getElementById('composerStatus');
        if (!composerStatus) return;
        if (cb.checked) {
            composerStatus.value = 'closed';
        } else {
            composerStatus.value = 'in_progress';
        }
    }

    function switchComposerTab(tab) {
        var publicBtn = document.getElementById('tab-public-reply');
        var internalBtn = document.getElementById('tab-internal-note');
        var composerCard = document.querySelector('.reply-composer-card');
        var isInternalInput = document.getElementById('composerIsInternal');
        var replyInput = document.getElementById('replyInput');
        var sendBtn = document.querySelector('.btn-send-reply-primary span');

        if (tab === 'internal') {
            if (publicBtn) publicBtn.classList.remove('active');
            if (internalBtn) internalBtn.classList.add('active');
            if (composerCard) composerCard.classList.add('is-internal-mode');
            if (isInternalInput) isInternalInput.value = '1';
            if (replyInput) replyInput.placeholder = '🔒 Tulis catatan internal khusus Admin / Tim Support (tidak terlihat oleh pemohon tiket)...';
            if (sendBtn) sendBtn.textContent = 'Save Internal Note';
        } else {
            if (internalBtn) internalBtn.classList.remove('active');
            if (publicBtn) publicBtn.classList.add('active');
            if (composerCard) composerCard.classList.remove('is-internal-mode');
            if (isInternalInput) isInternalInput.value = '0';
            if (replyInput) replyInput.placeholder = 'Tulis tanggapan untuk user atau tekan Shift+Enter untuk baris baru...';
            if (sendBtn) sendBtn.textContent = 'Send Reply';
        }
    }

    function switchModalNoteTab(tab) {
        var publicBtn = document.getElementById('modal-tab-public');
        var internalBtn = document.getElementById('modal-tab-internal');
        var isInternalInput = document.getElementById('modalIsInternal');
        var textarea = document.getElementById('modalReplyTextarea');
        var submitBtn = document.getElementById('btnModalSubmit');

        if (tab === 'internal') {
            if (publicBtn) publicBtn.classList.remove('active');
            if (internalBtn) internalBtn.classList.add('active');
            if (isInternalInput) isInternalInput.value = '1';
            if (textarea) textarea.placeholder = 'Tulis catatan internal khusus Admin / Tim Support (hanya terlihat oleh internal)...';
            if (submitBtn) submitBtn.textContent = 'Save Internal Note';
        } else {
            if (internalBtn) internalBtn.classList.remove('active');
            if (publicBtn) publicBtn.classList.add('active');
            if (isInternalInput) isInternalInput.value = '0';
            if (textarea) textarea.placeholder = 'Tuliskan pesan penjelasan atau update penyelesaian tiket untuk pemohon (Pesan ini dapat dibaca oleh pemohon)';
            if (submitBtn) submitBtn.textContent = 'Update & Save';
        }
    }

    // 3. Activity Filtering
    function filterActivity(type, btn) {
        document.querySelectorAll('.activity-tab-btn').forEach(function (b) {
            b.classList.remove('active');
        });
        if (btn) btn.classList.add('active');

        var items = document.querySelectorAll('.activity-item-node');
        items.forEach(function (item) {
            var itemType = item.getAttribute('data-type');
            var isInternal = item.getAttribute('data-is-internal') === '1';

            if (type === 'all') {
                item.style.display = 'flex';
            } else if (type === 'internal') {
                item.style.display = isInternal ? 'flex' : 'none';
            } else if (type === 'comments') {
                item.style.display = (itemType === 'comment') ? 'flex' : 'none';
            } else if (type === 'audit') {
                item.style.display = (itemType === 'audit') ? 'flex' : 'none';
            } else {
                item.style.display = (itemType === type) ? 'flex' : 'none';
            }
        });
    }

    // 4. Quick Actions
    function quickAssignToMe(myUserId) {
        if (!myUserId) return;
        var assignedInput = document.getElementById('modal_assigned_select');
        if (assignedInput) {
            assignedInput.value = myUserId;
            var opt = document.querySelector('#select_menu_modal_assigned_select .custom-select-option[data-value="' + myUserId + '"]');
            var label = opt ? opt.textContent.trim() : 'Me';
            if (typeof window.selectCustomOption === 'function') {
                window.selectCustomOption('modal_assigned_select', myUserId, label, '');
            }
        }
        showReplyStatusModal();
    }

    function openEscalateModal() {
        var prioInput = document.getElementById('modal_priority_select');
        if (prioInput) {
            prioInput.value = 'urgent';
            if (typeof window.selectCustomOption === 'function') {
                window.selectCustomOption('modal_priority_select', 'urgent', 'Urgent Priority', '');
            }
        }
        showReplyStatusModal();
    }

    // 5. Dropdown Menus Handlers
    function toggleTicketStatusDropdown(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        var wrapper = document.getElementById('ticketStatusDropdownWrapper');
        if (!wrapper) return;
        var isOpen = wrapper.classList.contains('is-open');
        closeAllActionDropdowns();
        closeAllCustomSelects();
        if (!isOpen) {
            wrapper.classList.add('is-open');
            var btn = document.getElementById('ticketStatusTriggerBtn');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        } else {
            wrapper.classList.remove('is-open');
            var btn = document.getElementById('ticketStatusTriggerBtn');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    }

    function closeTicketStatusDropdown() {
        var wrapper = document.getElementById('ticketStatusDropdownWrapper');
        if (wrapper) {
            wrapper.classList.remove('is-open');
            var btn = document.getElementById('ticketStatusTriggerBtn');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    }

    function toggleActionDropdown(e, id) {
        e.stopPropagation();
        var wrapper = document.getElementById('wrapper_' + id);
        if (!wrapper) return;
        var wasOpen = wrapper.classList.contains('is-open');
        closeAllActionDropdowns();
        if (!wasOpen) wrapper.classList.add('is-open');
    }

    function closeAllActionDropdowns() {
        document.querySelectorAll('.action-dropdown-wrapper.is-open').forEach(function (el) {
            el.classList.remove('is-open');
        });
    }

    function toggleCustomSelect(e, id) {
        e.stopPropagation();
        var wrapper = document.getElementById('select_wrapper_' + id);
        if (!wrapper) return;
        var wasOpen = wrapper.classList.contains('is-open');
        closeAllCustomSelects();
        if (!wasOpen) wrapper.classList.add('is-open');
    }

    function selectCustomOption(id, val, label, onChangeCallback) {
        var input = document.getElementById(id);
        var labelEl = document.getElementById('select_label_' + id);
        var trigger = document.getElementById('select_trigger_' + id);
        var menu = document.getElementById('select_menu_' + id);

        if (input) input.value = val;
        if (labelEl) labelEl.innerText = label;

        if (trigger) {
            if (val !== '') trigger.classList.add('has-value');
            else trigger.classList.remove('has-value');
        }

        if (menu) {
            menu.querySelectorAll('.custom-select-option').forEach(function (opt) {
                if (opt.getAttribute('data-value') === val) opt.classList.add('is-selected');
                else opt.classList.remove('is-selected');
            });
        }

        closeAllCustomSelects();

        if (onChangeCallback && typeof window[onChangeCallback] === 'function') {
            window[onChangeCallback](val, label);
        }
    }

    function closeAllCustomSelects() {
        document.querySelectorAll('.custom-select-wrapper.is-open').forEach(function (el) {
            el.classList.remove('is-open');
        });
    }

    // 6. Interactive Status Change Confirmation & AJAX Execution
    function changeTicketStatusQuick(newStatus, newLabel) {
        closeTicketStatusDropdown();

        var currentItem = document.querySelector('#ticketStatusDropdownMenu .status-dropdown-item.is-selected');
        if (currentItem && currentItem.getAttribute('data-status') === newStatus) {
            return;
        }

        pendingStatusChange.newStatus = newStatus;
        pendingStatusChange.newLabel = newLabel;

        var labelEl = document.getElementById('confirmTargetStatusLabel');
        var badgeEl = document.getElementById('confirmTargetStatusBadge');
        var dotEl = document.getElementById('confirmTargetStatusDot');
        var notesEl = document.getElementById('confirmStatusNotes');

        if (labelEl) labelEl.textContent = newLabel;
        if (notesEl) notesEl.value = '';

        var dotClassMap = {
            'open': '#ea580c',
            'in_progress': '#0284c7',
            'on_hold': '#d97706',
            'resolved': '#059669',
            'closed': '#16a34a',
            'reopened': '#dc2626'
        };
        if (dotEl) {
            dotEl.style.background = dotClassMap[newStatus] || '#64748b';
        }
        if (badgeEl) {
            badgeEl.className = 'status-badge-inline status-' + newStatus;
        }

        var modal = document.getElementById('statusConfirmModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeStatusConfirmModal() {
        var modal = document.getElementById('statusConfirmModal');
        if (modal) modal.style.display = 'none';
        pendingStatusChange.newStatus = null;
        pendingStatusChange.newLabel = null;
    }

    function proceedTicketStatusChange() {
        var newStatus = pendingStatusChange.newStatus;
        var newLabel = pendingStatusChange.newLabel;
        var reasonNotes = (document.getElementById('confirmStatusNotes') ? document.getElementById('confirmStatusNotes').value : '').trim();

        if (!newStatus) return;

        var triggerBtn = document.getElementById('ticketStatusTriggerBtn');
        var triggerText = document.getElementById('ticketStatusTriggerText');
        var confirmBtn = document.getElementById('btnConfirmStatusSubmit');
        var ticketId = (window.TicketDetailConfig && window.TicketDetailConfig.ticketId) || '';
        var updateStatusUrl = (window.TicketDetailConfig && window.TicketDetailConfig.updateStatusUrl) || '';

        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Menyimpan...';
        }
        if (triggerBtn) triggerBtn.classList.add('is-loading');

        var formData = new FormData();
        formData.append('ticket_id', ticketId);
        formData.append('status', newStatus);
        if (reasonNotes) {
            formData.append('reason_notes', reasonNotes);
        }

        fetch(updateStatusUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                closeStatusConfirmModal();
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Ya, Ubah Status';
                }
                if (triggerBtn) triggerBtn.classList.remove('is-loading');

                if (data.status === 'success') {
                    var confirmedStatus = data.new_status || newStatus;
                    var confirmedLabel = data.label || newLabel;

                    // Update Trigger Button Classes & Text
                    var allStatuses = ['open', 'in_progress', 'on_hold', 'resolved', 'closed', 'done', 'reopened', 'rejected', 'cancelled'];
                    allStatuses.forEach(function (s) {
                        triggerBtn.classList.remove('status-' + s);
                    });
                    triggerBtn.classList.add('status-' + confirmedStatus);
                    if (triggerText) {
                        triggerText.innerHTML = 'Status: <strong>' + confirmedLabel + '</strong>';
                    }

                    // Update Menu Selection Checkmark
                    document.querySelectorAll('#ticketStatusDropdownMenu .status-dropdown-item').forEach(function (item) {
                        if (item.getAttribute('data-status') === confirmedStatus) {
                            item.classList.add('is-selected');
                        } else {
                            item.classList.remove('is-selected');
                        }
                    });

                    // Update Headline Status Badge
                    var headlineBadge = document.getElementById('headlineStatusBadge') || document.querySelector('.ticket-headline-status-badge');
                    if (headlineBadge) {
                        allStatuses.forEach(function (s) {
                            headlineBadge.classList.remove('status-' + s);
                        });
                        headlineBadge.classList.add('status-' + confirmedStatus);
                        headlineBadge.textContent = confirmedLabel;
                    }

                    // Sync Form Inputs
                    var compStatus = document.getElementById('composerStatus');
                    if (compStatus) compStatus.value = confirmedStatus;

                    var modalStatusInput = document.getElementById('modal_status_select');
                    if (modalStatusInput) {
                        modalStatusInput.value = confirmedStatus;
                        var opt = document.querySelector('#select_menu_modal_status_select .custom-select-option[data-value="' + confirmedStatus + '"]');
                        if (opt && typeof window.selectCustomOption === 'function') {
                            window.selectCustomOption('modal_status_select', confirmedStatus, confirmedLabel, '');
                        }
                    }

                    showDetailToast('✓ Status tiket berhasil diubah menjadi: ' + confirmedLabel, true);

                    // Reload timeline agar audit log dan response langsung ter-render
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    showDetailToast('✕ ' + (data.message || 'Gagal mengubah status tiket.'), false);
                }
            })
            .catch(function () {
                closeStatusConfirmModal();
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Ya, Ubah Status';
                }
                if (triggerBtn) triggerBtn.classList.remove('is-loading');
                showDetailToast('✕ Gagal terhubung ke server saat memperbarui status.', false);
            });
    }

    // 7. Toast Feedback
    function showDetailToast(msg, isSuccess) {
        var toast = document.getElementById('detailToast');
        if (!toast) return;
        toast.textContent = msg;
        toast.className = 'detail-floating-toast ' + (isSuccess ? 'toast-success' : 'toast-error');
        toast.style.display = 'flex';
        clearTimeout(detailToastTimeout);
        detailToastTimeout = setTimeout(function () {
            toast.style.display = 'none';
        }, 3500);
    }

    // 8. Real-time Live SLA Resolution Timer
    function formatDurationJs(seconds) {
        seconds = Math.max(0, Math.round(seconds));
        if (seconds <= 0) return '0m';

        var d = Math.floor(seconds / 86400);
        var h = Math.floor((seconds % 86400) / 3600);
        var m = Math.floor((seconds % 3600) / 60);

        var parts = [];
        if (d > 0) parts.push(d + 'd');
        if (h > 0) parts.push(h + 'h');
        if (m > 0 || parts.length === 0) parts.push(m + 'm');

        return parts.join(' ');
    }

    function initLiveSlaTimer() {
        var card = document.getElementById('slaProgressCard');
        if (!card) return;

        var createdTs = parseInt(card.getAttribute('data-created-ts'), 10) || 0;
        var targetTs = parseInt(card.getAttribute('data-target-ts'), 10) || 0;
        var resolvedTs = parseInt(card.getAttribute('data-resolved-ts'), 10) || 0;
        var isFinished = card.getAttribute('data-is-finished') === '1';
        var resHours = parseFloat(card.getAttribute('data-resolution-hours')) || 24;

        if (!createdTs) return;

        // Jika tiket sudah resolved/selesai, tidak perlu timer terus berjalan
        if (isFinished && resolvedTs > 0) return;

        function updateTicker() {
            var nowSec = Math.floor(Date.now() / 1000);
            var elapsedSec = Math.max(0, nowSec - createdTs);
            var isCompliant = (nowSec <= targetTs);

            // Update Elapsed text
            var elapsedEl = document.getElementById('slaElapsedText');
            if (elapsedEl) {
                elapsedEl.textContent = formatDurationJs(elapsedSec);
                if (isCompliant) {
                    elapsedEl.className = 'sla-time-compliant';
                } else {
                    elapsedEl.className = 'sla-time-overdue';
                }
            }

            // Update Progress bar percentage
            var totalTargetSec = Math.max(1, resHours * 3600);
            var pct = Math.min(100, Math.max(5, Math.round((elapsedSec / totalTargetSec) * 100)));
            var fillEl = document.getElementById('slaProgressFill');
            if (fillEl) {
                fillEl.style.width = pct + '%';
                fillEl.style.background = isCompliant ? '#10b981' : '#ef4444';
            }

            // Update Status Badge (100% COMPLIANT / BREACHED)
            var badgeEl = document.getElementById('slaPercentBadge');
            if (badgeEl) {
                badgeEl.textContent = isCompliant ? '100% COMPLIANT' : 'BREACHED';
            }

            // Update Headline SLA Banner Badge
            var headlineBadge = document.getElementById('headlineSlaBadge');
            var headlineText = document.getElementById('headlineSlaText');
            if (headlineBadge && headlineText) {
                if (isCompliant) {
                    headlineBadge.className = 'pill-badge pill-sla-pending';
                    headlineText.textContent = 'SLA In Progress';
                } else {
                    headlineBadge.className = 'pill-badge pill-sla-breached';
                    headlineText.textContent = 'SLA Breached';
                }
            }
        }

        // Jalankan sekali saat load, lalu interval setiap 10 detik agar hemat resource dan selalu sinkron
        updateTicker();
        setInterval(updateTicker, 10000);
    }

    // Global Event Listeners
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initLiveSlaTimer();
        });
    } else {
        initLiveSlaTimer();
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#ticketStatusDropdownWrapper')) closeTicketStatusDropdown();
        if (!e.target.closest('.action-dropdown-wrapper')) closeAllActionDropdowns();
        if (!e.target.closest('.custom-select-wrapper')) closeAllCustomSelects();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape") {
            closeTicketStatusDropdown();
            closeAllActionDropdowns();
            closeAllCustomSelects();
            closeReplyStatusModal();
            closeAttachmentModal();
            closeStatusConfirmModal();
        }
    });

    // Expose Functions Globally to Window
    window.showReplyStatusModal = showReplyStatusModal;
    window.closeReplyStatusModal = closeReplyStatusModal;
    window.showAttachmentModal = showAttachmentModal;
    window.closeAttachmentModal = closeAttachmentModal;
    window.toggleCloseCheckbox = toggleCloseCheckbox;
    window.switchComposerTab = switchComposerTab;
    window.switchModalNoteTab = switchModalNoteTab;
    window.filterActivity = filterActivity;
    window.quickAssignToMe = quickAssignToMe;
    window.openEscalateModal = openEscalateModal;
    window.toggleTicketStatusDropdown = toggleTicketStatusDropdown;
    window.closeTicketStatusDropdown = closeTicketStatusDropdown;
    window.toggleActionDropdown = toggleActionDropdown;
    window.closeAllActionDropdowns = closeAllActionDropdowns;
    window.toggleCustomSelect = toggleCustomSelect;
    window.selectCustomOption = selectCustomOption;
    window.closeAllCustomSelects = closeAllCustomSelects;
    window.changeTicketStatusQuick = changeTicketStatusQuick;
    window.closeStatusConfirmModal = closeStatusConfirmModal;
    window.proceedTicketStatusChange = proceedTicketStatusChange;
    window.showDetailToast = showDetailToast;
    window.initLiveSlaTimer = initLiveSlaTimer;

})(window, document);
