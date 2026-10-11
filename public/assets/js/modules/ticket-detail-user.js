/**
 * Ticket Detail User Interaction Module
 * High-fidelity interactivity for formatting toolbar, quote message, dropzone upload,
 * draft storage, async reply submission, and ticket closing.
 */

document.addEventListener('DOMContentLoaded', function () {
  const replyForm = document.getElementById('ticketReplyForm');
  const replyTextarea = document.getElementById('replyTextarea');
  const fileInput = document.getElementById('replyFileInput');
  const dropzone = document.getElementById('replyDropzone');
  const filesContainer = document.getElementById('selectedFilesContainer');
  const btnSubmitReply = document.getElementById('btnSubmitReply');
  const btnSaveDraft = document.getElementById('btnSaveDraft');
  const btnShareTicket = document.getElementById('btnShareTicket');
  const btnOpenCloseModal = document.getElementById('btnOpenCloseModal');
  const modalCloseTicket = document.getElementById('modalCloseTicket');
  const btnConfirmCloseTicket = document.getElementById('btnConfirmCloseTicket');
  const closeTicketReason = document.getElementById('closeTicketReason');

  // Determine current ticket ID from URL
  const pathParts = window.location.pathname.split('/');
  const currentTicketId = pathParts[pathParts.length - 1] || 'current';
  const draftStorageKey = 'hc_ticket_draft_' + currentTicketId;

  // 1. Restore Draft if available
  if (replyTextarea) {
    const savedDraft = localStorage.getItem(draftStorageKey);
    if (savedDraft && !replyTextarea.value.trim()) {
      replyTextarea.value = savedDraft;
    }

    // Save draft on button click
    if (btnSaveDraft) {
      btnSaveDraft.addEventListener('click', function () {
        const text = replyTextarea.value.trim();
        if (text) {
          localStorage.setItem(draftStorageKey, text);
          showToast('Draf balasan Anda berhasil disimpan di peramban ini.', 'success');
        } else {
          localStorage.removeItem(draftStorageKey);
          showToast('Draf kosong dihapus.', 'info');
        }
      });
    }
  }

  // 2. Rich Text Toolbar Formatting Helpers
  const toolbarButtons = document.querySelectorAll('.editor-toolbar .btn-tool');
  toolbarButtons.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const action = btn.getAttribute('data-action');
      if (!replyTextarea) return;

      const start = replyTextarea.selectionStart;
      const end = replyTextarea.selectionEnd;
      const val = replyTextarea.value;
      const selected = val.substring(start, end);

      let replacement = '';
      let cursorOffset = 0;

      switch (action) {
        case 'bold':
          replacement = selected ? `**${selected}**` : '**teks tebal**';
          cursorOffset = selected ? replacement.length : 2;
          break;
        case 'italic':
          replacement = selected ? `*${selected}*` : '*teks miring*';
          cursorOffset = selected ? replacement.length : 1;
          break;
        case 'strike':
          replacement = selected ? `~~${selected}~~` : '~~teks dicoret~~';
          cursorOffset = selected ? replacement.length : 2;
          break;
        case 'ul':
          replacement = selected ? `\n- ${selected}\n` : '\n- Poin informasi\n';
          cursorOffset = replacement.length;
          break;
        case 'ol':
          replacement = selected ? `\n1. ${selected}\n` : '\n1. Langkah pertama\n';
          cursorOffset = replacement.length;
          break;
        case 'link':
          const url = prompt('Masukkan tautan URL (misal: https://...):', 'https://');
          if (url) {
            replacement = `[${selected || 'Tautan'}](${url})`;
            cursorOffset = replacement.length;
          } else {
            return;
          }
          break;
        case 'code':
          replacement = selected ? `\`${selected}\`` : '`kode`';
          cursorOffset = selected ? replacement.length : 1;
          break;
      }

      replyTextarea.value = val.substring(0, start) + replacement + val.substring(end);
      replyTextarea.focus();
      replyTextarea.setSelectionRange(start + cursorOffset, start + cursorOffset);
    });
  });

  // 3. Quote Message Handler ("Kutip Pesan Ini")
  const quoteButtons = document.querySelectorAll('.btn-quote-msg');
  quoteButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const quoteText = btn.getAttribute('data-quote') || '';
      const author = btn.getAttribute('data-author') || 'Petugas HC';

      if (!replyTextarea) return;

      const formattedQuote = `> **${author}** menulis:\n> "${quoteText.trim().replace(/\n/g, '\n> ')}"\n\n`;
      const currentVal = replyTextarea.value;

      replyTextarea.value = (currentVal ? currentVal + '\n\n' : '') + formattedQuote;
      replyTextarea.focus();

      // Scroll smoothly to reply card
      const replyCard = document.querySelector('.reply-form-card');
      if (replyCard) {
        replyCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  });

  // 4. File Dropzone & Selection Handling
  let selectedFiles = [];

  if (dropzone && fileInput) {
    // Click dropzone to browse
    dropzone.addEventListener('click', function (e) {
      if (e.target !== fileInput) {
        fileInput.click();
      }
    });

    // Dragover & dragleave styling
    ['dragenter', 'dragover'].forEach(eventName => {
      dropzone.addEventListener(eventName, function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('dragover');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropzone.addEventListener(eventName, function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('dragover');
      }, false);
    });

    // Drop files
    dropzone.addEventListener('drop', function (e) {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length) {
        handleFilesAdded(dt.files);
      }
    });

    // File input change
    fileInput.addEventListener('change', function () {
      if (fileInput.files && fileInput.files.length) {
        handleFilesAdded(fileInput.files);
      }
    });
  }

  function handleFilesAdded(files) {
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      // Max 10MB validation
      if (file.size > 10 * 1024 * 1024) {
        showToast(`Berkas ${file.name} melebihi batas 10MB.`, 'error');
        continue;
      }
      // Check duplicate
      const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
      if (!exists) {
        selectedFiles.push(file);
      }
    }
    renderSelectedFiles();
  }

  function renderSelectedFiles() {
    if (!filesContainer) return;
    filesContainer.innerHTML = '';

    if (selectedFiles.length === 0) return;

    selectedFiles.forEach((file, index) => {
      const chip = document.createElement('div');
      chip.className = 'reply-attachment-chip';
      chip.innerHTML = `
        <i class="fas fa-paperclip"></i>
        <span><strong>${escapeHtml(file.name)}</strong> (${formatSize(file.size)})</span>
        <button type="button" class="btn-remove-chip" data-index="${index}" title="Hapus berkas">&times;</button>
      `;
      filesContainer.appendChild(chip);
    });

    // Add remove listeners
    const removeBtns = filesContainer.querySelectorAll('.btn-remove-chip');
    removeBtns.forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const idx = parseInt(btn.getAttribute('data-index'), 10);
        selectedFiles.splice(idx, 1);
        renderSelectedFiles();
      });
    });
  }

  // 5. Submit Reply Form via AJAX
  if (replyForm) {
    replyForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const replyContent = replyTextarea ? replyTextarea.value.trim() : '';
      if (!replyContent && selectedFiles.length === 0) {
        showToast('Silakan tuliskan tanggapan atau lampirkan berkas sebelum mengirim.', 'error');
        replyTextarea.focus();
        return;
      }

      // Build FormData
      const formData = new FormData(replyForm);
      formData.delete('attachments[]'); // reset default file input
      selectedFiles.forEach(file => {
        formData.append('attachments[]', file, file.name);
      });

      // Loading state
      btnSubmitReply.disabled = true;
      const originalBtnHtml = btnSubmitReply.innerHTML;
      btnSubmitReply.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Mengirimkan...`;

      fetch(window.location.origin + '/my/ticket/' + currentTicketId + '/reply', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            localStorage.removeItem(draftStorageKey);
            showToast('Tanggapan Anda berhasil terkirim!', 'success');
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            showToast(data.message || 'Gagal mengirimkan balasan.', 'error');
            btnSubmitReply.disabled = false;
            btnSubmitReply.innerHTML = originalBtnHtml;
          }
        })
        .catch(err => {
          console.error('Error submitting reply:', err);
          showToast('Terjadi kesalahan jaringan saat mengirim tanggapan.', 'error');
          btnSubmitReply.disabled = false;
          btnSubmitReply.innerHTML = originalBtnHtml;
        });
    });
  }

  // 6. Share Ticket Link
  if (btnShareTicket) {
    btnShareTicket.addEventListener('click', function () {
      const shareUrl = btnShareTicket.getAttribute('data-url') || window.location.href;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(shareUrl).then(function () {
          const origHtml = btnShareTicket.innerHTML;
          btnShareTicket.innerHTML = `<i class="fas fa-check text-success"></i> Tautan Disalin!`;
          setTimeout(() => {
            btnShareTicket.innerHTML = origHtml;
          }, 2500);
        });
      } else {
        prompt('Salin tautan tiket:', shareUrl);
      }
    });
  }

  // 7. Close Ticket Modal Handling
  if (btnOpenCloseModal && modalCloseTicket) {
    btnOpenCloseModal.addEventListener('click', function () {
      modalCloseTicket.classList.add('is-active');
    });
  }

  window.closeCloseTicketModal = function () {
    if (modalCloseTicket) {
      modalCloseTicket.classList.remove('is-active');
    }
  };

  if (btnConfirmCloseTicket) {
    btnConfirmCloseTicket.addEventListener('click', function () {
      const reason = closeTicketReason ? closeTicketReason.value.trim() : '';

      btnConfirmCloseTicket.disabled = true;
      btnConfirmCloseTicket.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...`;

      const formData = new FormData();
      formData.append('reason', reason);

      // CSRF token if exists
      const csrfInput = document.querySelector('input[name="csrf_token_name"], input[name^="csrf_"]');
      if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
      }

      fetch(window.location.origin + '/my/ticket/' + currentTicketId + '/close', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            showToast('Tiket berhasil ditutup.', 'success');
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            showToast(data.message || 'Gagal menutup tiket.', 'error');
            btnConfirmCloseTicket.disabled = false;
            btnConfirmCloseTicket.innerHTML = `<i class="fas fa-check mr-1"></i> Ya, Tutup Tiket`;
          }
        })
        .catch(err => {
          console.error('Error closing ticket:', err);
          showToast('Terjadi kesalahan saat menutup tiket.', 'error');
          btnConfirmCloseTicket.disabled = false;
          btnConfirmCloseTicket.innerHTML = `<i class="fas fa-check mr-1"></i> Ya, Tutup Tiket`;
        });
    });
  }

  // Utilities
  function formatSize(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
    if (bytes >= 1024) return Math.round(bytes / 1024) + ' KB';
    return bytes + ' B';
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function showToast(message, type = 'info') {
    let toast = document.getElementById('hcGlobalToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'hcGlobalToast';
      toast.style.cssText = `
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #ffffff;
        z-index: 99999;
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
      `;
      document.body.appendChild(toast);
    }

    const bgMap = {
      success: '#10b981',
      error: '#ef4444',
      info: '#3b82f6',
      warning: '#f59e0b',
    };
    const iconMap = {
      success: 'fa-check-circle',
      error: 'fa-exclamation-circle',
      info: 'fa-info-circle',
      warning: 'fa-exclamation-triangle',
    };

    toast.style.background = bgMap[type] || '#3b82f6';
    toast.innerHTML = `<i class="fas ${iconMap[type] || 'fa-info-circle'}"></i> <span>${escapeHtml(message)}</span>`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
    }, 3500);
  }
});
