/**
 * Admin Ticket Dashboard Module
 * 
 * Handles search, filters, switcher (Table vs Kanban), per-area pagination & date filtering,
 * drag-and-drop kanban status update via AJAX, and keyboard shortcuts.
 */

// Per-Area Pagination & Filter State for Kanban swimlanes
var areaState = {
    open: { page: 1, pageSize: 12, dateFilter: 'all' },
    in_progress: { page: 1, pageSize: 12, dateFilter: 'all' },
    closed: { page: 1, pageSize: 12, dateFilter: 'all' }
};

/**
 * Filter change: reload page with query params
 */
function applyFilters() {
    var url = new URL(window.location.href);
    var params = url.searchParams;

    var searchEl = document.getElementById('ticketSearchInput');
    var priorityEl = document.getElementById('filterPriority');
    var typeEl = document.getElementById('filterType');
    var statusEl = document.getElementById('filterStatus');
    var perPageEl = document.getElementById('filterPerPage');

    var searchVal = searchEl ? searchEl.value.trim() : '';
    var priorityVal = priorityEl ? priorityEl.value : '';
    var typeVal = typeEl ? typeEl.value : '';
    var statusVal = statusEl ? statusEl.value : '';
    var perPageVal = perPageEl ? perPageEl.value : '';

    if (searchVal) params.set('search', searchVal); else params.delete('search');
    if (priorityVal) params.set('priority', priorityVal); else params.delete('priority');
    if (typeVal) params.set('type', typeVal); else params.delete('type');
    if (statusVal) params.set('status', statusVal); else params.delete('status');
    if (perPageVal) params.set('per_page', perPageVal);

    params.set('page', 1);
    window.location = url.pathname + '?' + params.toString();
}

/**
 * Quick Filter Pills
 */
function setQuickFilter(type) {
    var prioritySelect = document.getElementById('filterPriority');
    var statusSelect = document.getElementById('filterStatus');

    if (type === 'all') {
        if (prioritySelect) prioritySelect.value = '';
        if (statusSelect) statusSelect.value = '';
    } else if (type === 'open') {
        if (statusSelect) statusSelect.value = 'open';
        if (prioritySelect) prioritySelect.value = '';
    } else if (type === 'in_progress') {
        if (statusSelect) statusSelect.value = 'in_progress';
        if (prioritySelect) prioritySelect.value = '';
    } else if (type === 'urgent') {
        if (prioritySelect) prioritySelect.value = 'urgent';
        if (statusSelect) statusSelect.value = '';
    }
    applyFilters();
}

/**
 * Handle Per Page Dropdown Change
 * Applies to all card areas in Grid mode, or Table in Table mode
 */
function handlePerPageChange(val) {
    var num = parseInt(val, 10) || 12;
    var currentMode = localStorage.getItem('ticket_view_mode') || 'table';

    // Apply page size to all 3 card areas
    ['open', 'in_progress', 'closed'].forEach(function(area) {
        if (areaState[area]) {
            areaState[area].pageSize = num;
            areaState[area].page = 1;
        }
    });
    localStorage.setItem('ticket_card_page_size', num);

    if (currentMode === 'grid') {
        // Instant update on grid without page reload
        updateSwimlaneCounts();
    } else {
        // In table mode, reload table with new per_page
        applyFilters();
    }
}

/**
 * Filter cards in a specific area by date
 */
function filterAreaByDate(area, filterVal) {
    if (!areaState[area]) return;
    areaState[area].dateFilter = filterVal;
    areaState[area].page = 1;
    updateAreaView(area);
}

/**
 * Change page in a specific area
 */
function changeAreaPage(area, delta) {
    if (!areaState[area]) return;
    areaState[area].page += delta;
    updateAreaView(area);
}

/**
 * Check if card matches date filter
 */
function matchesDateFilter(card, filterVal) {
    if (filterVal === 'all') return true;

    var ymd = card.getAttribute('data-created-ymd');
    var ts = parseInt(card.getAttribute('data-created-ts'), 10) * 1000;
    if (!ts) return true;

    var now = new Date();
    var cardDate = new Date(ts);

    if (filterVal === 'today') {
        var todayYmd = now.toISOString().split('T')[0];
        return ymd === todayYmd;
    } else if (filterVal === '7days') {
        var sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(now.getDate() - 7);
        return cardDate >= sevenDaysAgo;
    } else if (filterVal === '30days') {
        var startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        return cardDate >= startOfMonth;
    }
    return true;
}

/**
 * Update single area cards, count, and pagination controls
 */
function updateAreaView(area) {
    var track = document.getElementById('track-' + area);
    if (!track || !areaState[area]) return;

    var state = areaState[area];
    var allCards = Array.from(track.querySelectorAll('.ticket-card'));
    var matchingCards = allCards.filter(function(card) {
        return matchesDateFilter(card, state.dateFilter);
    });

    // Sorting specification:
    // - open & in_progress: created_date ASC (terlama / oldest first)
    // - closed: modified_date DESC (terbaru / newest first)
    matchingCards.sort(function(a, b) {
        if (area === 'open' || area === 'in_progress') {
            var tsA = parseInt(a.getAttribute('data-created-ts'), 10) || 0;
            var tsB = parseInt(b.getAttribute('data-created-ts'), 10) || 0;
            return tsA - tsB; // ASC
        } else {
            var modA = parseInt(a.getAttribute('data-modified-ts'), 10) || parseInt(a.getAttribute('data-created-ts'), 10) || 0;
            var modB = parseInt(b.getAttribute('data-modified-ts'), 10) || parseInt(b.getAttribute('data-created-ts'), 10) || 0;
            return modB - modA; // DESC
        }
    });

    // Re-order matching cards in DOM track
    matchingCards.forEach(function(card) {
        track.appendChild(card);
    });

    // Total count update
    var countBadge = document.getElementById('count-' + area);
    if (countBadge) {
        countBadge.textContent = matchingCards.length + ' tickets';
    }

    // Calculate pagination
    var totalPages = Math.ceil(matchingCards.length / state.pageSize);
    if (totalPages < 1) totalPages = 1;
    if (state.page > totalPages) state.page = totalPages;
    if (state.page < 1) state.page = 1;

    var startIndex = (state.page - 1) * state.pageSize;
    var endIndex = startIndex + state.pageSize;

    // Render visibility
    allCards.forEach(function(card) {
        card.style.display = 'none';
    });

    matchingCards.forEach(function(card, idx) {
        if (idx >= startIndex && idx < endIndex) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });

    // Empty placeholder management
    var placeholder = track.querySelector('.swimlane-empty-placeholder');
    if (matchingCards.length === 0) {
        var emptyMsg = state.dateFilter !== 'all' 
            ? 'No tickets found for selected date' 
            : 'Drag ticket here to set status to ' + (area === 'in_progress' ? 'In Progress' : (area === 'closed' ? 'Closed / Done' : 'Open'));
        if (!placeholder) {
            var p = document.createElement('div');
            p.className = 'swimlane-empty-placeholder';
            p.textContent = emptyMsg;
            track.appendChild(p);
        } else {
            placeholder.textContent = emptyMsg;
        }
    } else if (placeholder) {
        placeholder.remove();
    }

    // Update Pagination UI
    var pageInfo = document.getElementById('page-info-' + area);
    if (pageInfo) {
        pageInfo.textContent = state.page + ' / ' + totalPages;
    }

    var btnPrev = document.getElementById('btn-prev-' + area);
    if (btnPrev) {
        btnPrev.disabled = (state.page <= 1);
    }

    var btnNext = document.getElementById('btn-next-' + area);
    if (btnNext) {
        btnNext.disabled = (state.page >= totalPages || matchingCards.length === 0);
    }
}

/**
 * Update all 3 areas
 */
function updateSwimlaneCounts() {
    ['open', 'in_progress', 'closed'].forEach(updateAreaView);
}

/**
 * View Mode Switcher (Table vs Grid/Kanban)
 */
function switchViewMode(mode) {
    var tableView = document.getElementById('ticketTableView');
    var gridView = document.getElementById('ticketGridView');
    var btnTable = document.getElementById('btnViewTable');
    var btnGrid = document.getElementById('btnViewGrid');

    if (mode === 'grid') {
        if (tableView) tableView.style.display = 'none';
        if (gridView) gridView.style.display = 'block';
        if (btnTable) btnTable.classList.remove('active');
        if (btnGrid) btnGrid.classList.add('active');
        localStorage.setItem('ticket_view_mode', 'grid');
        updateSwimlaneCounts();
    } else {
        if (gridView) gridView.style.display = 'none';
        if (tableView) tableView.style.display = 'block';
        if (btnGrid) btnGrid.classList.remove('active');
        if (btnTable) btnTable.classList.add('active');
        localStorage.setItem('ticket_view_mode', 'table');
    }
}

/**
 * Toast Helper
 */
function showKanbanToast(message, isSuccess) {
    var toast = document.getElementById('kanbanToast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = 'kanban-toast ' + (isSuccess ? 'toast-success' : 'toast-error');
    toast.style.display = 'flex';
    setTimeout(function() {
        toast.style.display = 'none';
    }, 3000);
}

/**
 * Drag & Drop Handlers for Kanban Board
 */
function initDragAndDrop() {
    var cards = document.querySelectorAll('.ticket-card');
    var tracks = document.querySelectorAll('.swimlane-cards-track');

    cards.forEach(function(card) {
        card.addEventListener('dragstart', function(e) {
            card.classList.add('dragging');
            e.dataTransfer.setData('text/plain', card.getAttribute('data-ticket-id'));
            e.dataTransfer.setData('source-status', card.getAttribute('data-status'));
        });

        card.addEventListener('dragend', function() {
            card.classList.remove('dragging');
        });
    });

    tracks.forEach(function(track) {
        track.addEventListener('dragover', function(e) {
            e.preventDefault();
            track.classList.add('lane-drop-active');
        });

        track.addEventListener('dragleave', function() {
            track.classList.remove('lane-drop-active');
        });

        track.addEventListener('drop', function(e) {
            e.preventDefault();
            track.classList.remove('lane-drop-active');

            var ticketId = e.dataTransfer.getData('text/plain');
            var sourceStatus = e.dataTransfer.getData('source-status');
            var targetStatus = track.getAttribute('data-lane-status');

            if (!ticketId || sourceStatus === targetStatus) return;

            var card = document.getElementById('card-' + ticketId);
            if (!card) return;

            // Move DOM card
            track.appendChild(card);
            card.setAttribute('data-status', targetStatus);
            card.setAttribute('data-modified-ts', Math.floor(Date.now() / 1000));

            // Update card status badge
            var badge = card.querySelector('.status-badge');
            if (badge) {
                badge.className = 'status-badge status-' + targetStatus;
                badge.textContent = (targetStatus === 'in_progress') ? 'In Progress' : (targetStatus === 'closed' ? 'Closed' : 'Open');
            }

            // Re-render both areas immediately
            updateAreaView(sourceStatus);
            updateAreaView(targetStatus);

            // Send AJAX to server
            var formData = new FormData();
            formData.append('ticket_id', ticketId);
            formData.append('status', targetStatus);

            var updateUrl = window.TICKET_UPDATE_STATUS_URL || '/admin/update_ticket_status';

            fetch(updateUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    showKanbanToast('✓ ' + (data.message || 'Status updated successfully!'), true);
                } else {
                    showKanbanToast('✕ ' + (data.message || 'Failed to update status'), false);
                    // Revert on error
                    var origTrack = document.getElementById('track-' + sourceStatus);
                    if (origTrack) {
                        origTrack.appendChild(card);
                        card.setAttribute('data-status', sourceStatus);
                        updateAreaView(sourceStatus);
                        updateAreaView(targetStatus);
                    }
                }
            })
            .catch(function(err) {
                showKanbanToast('✕ Network error while updating ticket status.', false);
                var origTrack = document.getElementById('track-' + sourceStatus);
                if (origTrack) {
                    origTrack.appendChild(card);
                    card.setAttribute('data-status', sourceStatus);
                    updateAreaView(sourceStatus);
                    updateAreaView(targetStatus);
                }
            });
        });
    });
}

// Keyboard Shortcut ⌘K / Ctrl+K
document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        var searchInput = document.getElementById('ticketSearchInput');
        if (searchInput) searchInput.focus();
    }
});

// Initialize saved view mode, per-area pagination & drag drop
document.addEventListener('DOMContentLoaded', function() {
    var savedMode = localStorage.getItem('ticket_view_mode') || 'table';
    var savedCardPageSize = parseInt(localStorage.getItem('ticket_card_page_size'), 10);
    var perPageSelect = document.getElementById('filterPerPage');

    if (savedCardPageSize) {
        ['open', 'in_progress', 'closed'].forEach(function(area) {
            if (areaState[area]) {
                areaState[area].pageSize = savedCardPageSize;
            }
        });
        if (savedMode === 'grid' && perPageSelect) {
            perPageSelect.value = savedCardPageSize;
        }
    } else if (perPageSelect) {
        var currentVal = parseInt(perPageSelect.value, 10) || 12;
        ['open', 'in_progress', 'closed'].forEach(function(area) {
            if (areaState[area]) {
                areaState[area].pageSize = currentVal;
            }
        });
    }

    switchViewMode(savedMode);
    initDragAndDrop();
    initLiveCountdown();
});

/**
 * Real-time Dynamic Frontend Countdown for Time Left
 * Backend only sends seconds_left, frontend decrements it locally every second.
 */
var countdownInterval = null;

function formatCountdown(seconds) {
    if (seconds <= 0) {
        var absSec = Math.abs(seconds);
        var days = Math.floor(absSec / 86400);
        var hours = Math.floor((absSec % 86400) / 3600);
        var mins = Math.floor((absSec % 3600) / 60);
        var secs = absSec % 60;

        var parts = [];
        if (days > 0) parts.push(days + 'd');
        if (hours > 0 || days > 0) parts.push(hours + 'h');
        parts.push(mins + 'm');
        parts.push(secs + 's');
        return 'OVERDUE (' + parts.join(' ') + ')';
    }

    var days = Math.floor(seconds / 86400);
    var hours = Math.floor((seconds % 86400) / 3600);
    var mins = Math.floor((seconds % 3600) / 60);
    var secs = seconds % 60;

    var parts = [];
    if (days > 0) parts.push(days + 'd');
    if (hours > 0 || days > 0) parts.push(hours + 'h');
    parts.push(mins + 'm');
    parts.push(secs + 's');
    return parts.join(' ');
}

function updateCountdowns() {
    var pills = document.querySelectorAll('.countdown-pill');
    pills.forEach(function(pill) {
        var rawSec = pill.getAttribute('data-seconds-left');
        if (rawSec === null || rawSec === '') return;

        var sec = parseInt(rawSec, 10);
        var textEl = pill.querySelector('.countdown-text');
        if (!textEl) return;

        textEl.textContent = formatCountdown(sec);

        if (sec <= 0) {
            pill.classList.remove('is-warning');
            pill.classList.add('is-overdue');
        } else if (sec <= 7200) { // < 2 hours
            pill.classList.remove('is-overdue');
            pill.classList.add('is-warning');
        } else {
            pill.classList.remove('is-overdue', 'is-warning');
        }

        // Decrement by 1 second
        pill.setAttribute('data-seconds-left', sec - 1);
    });
}

function initLiveCountdown() {
    updateCountdowns();
    if (countdownInterval) clearInterval(countdownInterval);
    countdownInterval = setInterval(updateCountdowns, 1000);
}

/**
 * Quick Update Status from Table Dropdown
 */
function updateTicketStatusFromTable(ticketId, newStatus) {
    if (!ticketId || !newStatus) return;

    var selectEl = document.getElementById('status-select-' + ticketId);
    var prevClass = selectEl ? selectEl.className : '';

    var formData = new FormData();
    formData.append('ticket_id', ticketId);
    formData.append('status', newStatus);

    var updateUrl = window.TICKET_UPDATE_STATUS_URL || '/admin/update_ticket_status';

    fetch(updateUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            showKanbanToast('✓ ' + (data.message || 'Status updated successfully!'), true);
            if (selectEl) {
                selectEl.className = 'table-status-select select-status-' + newStatus;
            }

            // Sync with card in kanban if exists
            var card = document.getElementById('card-' + ticketId);
            if (card) {
                var oldLane = card.getAttribute('data-status');
                card.setAttribute('data-status', newStatus);
                var targetLane = (newStatus === 'done' || newStatus === 'resolved') ? 'closed' : newStatus;
                var track = document.getElementById('track-' + targetLane);
                if (track) {
                    track.appendChild(card);
                    updateAreaView(oldLane);
                    updateAreaView(targetLane);
                }
            }
        } else {
            showKanbanToast('✕ ' + (data.message || 'Failed to update status'), false);
        }
    })
    .catch(function(err) {
        showKanbanToast('✕ Network error while updating ticket status.', false);
    });
}

/**
 * Quick Update SLA Category from Table Dropdown
 */
function updateTicketSlaFromTable(ticketId, newSla) {
    if (!ticketId || !newSla) return;

    var selectEl = document.getElementById('sla-select-' + ticketId);

    var formData = new FormData();
    formData.append('ticket_id', ticketId);
    formData.append('sla', newSla);

    var updateUrl = window.TICKET_UPDATE_SLA_URL || '/admin/update_ticket_sla';

    fetch(updateUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            showKanbanToast('✓ ' + (data.message || 'SLA updated successfully!'), true);
            if (selectEl) {
                selectEl.className = 'table-sla-select select-sla-' + newSla.toLowerCase();
            }

            // Update countdown element if returned
            if (data.seconds_left !== undefined && data.seconds_left !== null) {
                var pill = document.getElementById('countdown-' + ticketId);
                if (pill) {
                    pill.setAttribute('data-seconds-left', data.seconds_left);
                    updateCountdowns();
                }
            }
        } else {
            showKanbanToast('✕ ' + (data.message || 'Failed to update SLA'), false);
        }
    })
    .catch(function(err) {
        showKanbanToast('✕ Network error while updating SLA.', false);
    });
}
