/**
 * Global Asynchronous Ticket Toast Manager
 * Memastikan notifikasi tiket berhasil (Gambar 2) tetap tampil mengambang di kanan bawah
 * bahkan setelah pengguna dialihkan ke halaman /dashboard atau halaman lain.
 */

(function (global) {
  'use strict';

  var STORAGE_KEY = 'activeTicketSuccessToast';

  var GlobalTicketToast = {
    init: function () {
      this.checkPersistedToast();
    },

    /**
     * Memeriksa apakah ada data tiket baru yang disimpan saat submit
     */
    checkPersistedToast: function () {
      try {
        var rawData = sessionStorage.getItem(STORAGE_KEY);
        if (!rawData) return;

        var data = JSON.parse(rawData);
        if (data && data.ticket_no) {
          this.renderSuccessToast(data);
        }
      } catch (e) {
        console.warn('[GlobalTicketToast] Gagal memuat toast tersimpan:', e);
      }
    },

    /**
     * Menyimpan data tiket dan mengarahkan ke dashboard
     */
    persistAndRedirect: function (ticketData, redirectUrl) {
      try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ticketData));
      } catch (e) {}

      var target = redirectUrl || '/dashboard';
      window.location.href = target;
    },

    /**
     * Merender tampilan Gambar 2 (Tiket Berhasil Diterbitkan! 100% Selesai)
     */
    renderSuccessToast: function (data) {
      var toast = document.getElementById('asyncUploadToast');
      if (!toast) return;

      var titleEl = document.getElementById('toastTitle');
      var subtitleEl = document.getElementById('toastSubtitle');
      var iconCircle = document.getElementById('toastStatusIcon');
      var badgeEl = document.getElementById('toastPercentageBadge');
      var progressBar = document.getElementById('toastProgressBar');
      var subjText = document.getElementById('toastSubjectText');
      var subjLine = document.getElementById('toastSubjectLine');
      var fileText = document.getElementById('toastFileText');
      var fileLine = document.getElementById('toastFileLine');
      var bytesText = document.getElementById('toastBytesText');
      var completeBox = document.getElementById('toastCompleteBox');
      var ticketNoEl = document.getElementById('toastTicketNo');
      var trackLink = document.getElementById('toastTrackLink');

      if (titleEl) titleEl.textContent = 'Tiket Berhasil Diterbitkan!';
      if (subtitleEl) subtitleEl.textContent = 'Telah masuk dalam sistem antrean HC MTF';

      if (iconCircle) {
        iconCircle.className = 'toast-icon-circle is-success';
        iconCircle.innerHTML = '<i class="fa-solid fa-check"></i>';
      }

      if (badgeEl) {
        badgeEl.className = 'toast-pill-badge is-success';
        badgeEl.textContent = '100%';
      }

      if (progressBar) {
        progressBar.className = 'toast-progress-bar is-success';
        progressBar.style.width = '100%';
      }

      if (subjText && data.subject) {
        subjText.textContent = data.subject;
        if (subjLine) subjLine.style.display = 'block';
      }

      if (fileText && data.file_name) {
        fileText.textContent = data.file_name;
        if (fileLine) fileLine.style.display = 'block';
      } else if (fileLine) {
        fileLine.style.display = 'none';
      }

      if (bytesText) bytesText.textContent = 'Berhasil diproses';

      if (ticketNoEl) ticketNoEl.textContent = data.ticket_no;
      if (trackLink) {
        trackLink.href = '/ticket/track?ticket_no=' + encodeURIComponent(data.ticket_no);
      }

      if (completeBox) completeBox.style.display = 'block';

      toast.style.display = 'block';
    },

    /**
     * Menutup toast dan membersihkan penyimpanan
     */
    close: function () {
      var toast = document.getElementById('asyncUploadToast');
      if (toast) {
        toast.style.display = 'none';
      }
      try {
        sessionStorage.removeItem(STORAGE_KEY);
      } catch (e) {}
    },

    copyTicketNo: function () {
      var ticketNoEl = document.getElementById('toastTicketNo');
      if (!ticketNoEl) return;
      var text = ticketNoEl.textContent.trim();
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function () {
          alert('Nomor tiket ' + text + ' berhasil disalin ke papan klip!');
        });
      } else {
        alert('Nomor tiket: ' + text);
      }
    }
  };

  global.GlobalTicketToast = GlobalTicketToast;

  document.addEventListener('DOMContentLoaded', function () {
    GlobalTicketToast.init();
  });

})(window);
