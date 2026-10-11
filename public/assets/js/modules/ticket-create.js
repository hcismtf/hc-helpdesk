/**
 * Ticket Create Multi-Step Wizard Module
 * Human Capital Helpdesk - PT Mandiri Tunas Finance
 * 
 * Fitur:
 * 1. 3-Step Wizard Navigation & Step Validation
 * 2. Client-side Smart Compression (Canvas WebP downscaling untuk gambar)
 * 3. Multi-format Document Support (Excel .xlsx/.xls, Word .docx/.doc, PDF, Image)
 * 4. Asynchronous Floating Bottom-Right Toast Upload with Live Percentage Progress
 */

(function (global) {
  'use strict';

  var TicketWizard = {
    currentStep: 1,
    selectedFile: null,
    compressedFileBlob: null,
    activeXhr: null,

    init: function () {
      this.bindEvents();
      this.initAutoFillFromStorage();
      this.initCharacterCounter();
      this.initDropzone();
      this.initUrlCategoryPreselect();
      this.updateStepperUI(1);
    },

    initUrlCategoryPreselect: function () {
      var urlParams = new URLSearchParams(window.location.search);
      var reqTypeParam = urlParams.get('req_type');
      var catIdParam = urlParams.get('category_id');

      if (catIdParam) {
        var targetCard = document.querySelector('.category-card[data-category-id="' + catIdParam + '"]');
        if (targetCard) {
          this.onMainCategorySelected(targetCard, catIdParam, '');
        }
      } else {
        var activeCard = document.querySelector('.category-card.is-selected');
        if (activeCard) {
          var cId = activeCard.getAttribute('data-category-id');
          if (cId) {
            this.filterSubCategories(cId);
          }
        }
      }

      if (reqTypeParam) {
        var selectDb = document.getElementById('select_db_req_type');
        if (selectDb) {
          for (var i = 0; i < selectDb.options.length; i++) {
            if (selectDb.options[i].value === reqTypeParam) {
              selectDb.selectedIndex = i;
              selectDb.options[i].style.display = '';
              selectDb.options[i].disabled = false;
              this.onSelectDropdownType(reqTypeParam);
              break;
            }
          }
        }
      }
    },

    bindEvents: function () {
      var self = this;

      // Priority radio button changes
      var priorityRadios = document.querySelectorAll('input[name="ticket_priority"]');
      priorityRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
          document.querySelectorAll('.priority-card').forEach(function (c) {
            c.classList.remove('is-selected');
          });
          var parentCard = radio.closest('.priority-card');
          if (parentCard) {
            parentCard.classList.add('is-selected');
          }
        });
      });
    },

    /**
     * Cek data userRef yang tersimpan secara terenkripsi di localStorage
     */
    initAutoFillFromStorage: function () {
      if (typeof CryptoHelper === 'undefined') {
        return;
      }

      var userRef = CryptoHelper.getUserRef();
      if (!userRef || typeof userRef !== 'object') {
        return;
      }

      var nipField = document.getElementById('input_emp_id');
      var nameField = document.getElementById('input_emp_name');
      var emailField = document.getElementById('input_email');
      var waField = document.getElementById('input_wa_no');
      var banner = document.getElementById('autofillBanner');

      var nipVal = userRef.employee_no || userRef.nip || userRef.emp_id || '';
      var nameVal = userRef.name || userRef.full_name || '';
      var emailVal = userRef.email || '';
      var phoneVal = userRef.phone_number || userRef.phone || userRef.wa_no || '';

      var filledAny = false;

      if (nipField && !nipField.value && nipVal) {
        nipField.value = nipVal;
        filledAny = true;
      }
      if (nameField && !nameField.value && nameVal) {
        nameField.value = nameVal;
        filledAny = true;
      }
      if (emailField && !emailField.value && emailVal) {
        emailField.value = emailVal;
        filledAny = true;
      }
      if (waField && !waField.value && phoneVal) {
        waField.value = phoneVal;
        filledAny = true;
      }

      if (banner && (filledAny || (nipField && nipField.value))) {
        var userTitle = document.getElementById('autofillUserTitle');
        var userDesc = document.getElementById('autofillUserDesc');
        if (userTitle) {
          userTitle.textContent = 'Terautentikasi: ' + (nameField.value || nameVal || 'Pegawai MTF');
        }
        if (userDesc) {
          userDesc.textContent = 'Data NIK (' + (nipField.value || nipVal) + ') & kontak berhasil disinkronkan secara otomatis.';
        }
        banner.style.display = 'flex';
      }
    },

    resetRequesterFields: function () {
      var banner = document.getElementById('autofillBanner');
      if (banner) banner.style.display = 'none';

      var nip = document.getElementById('input_emp_id');
      if (nip) nip.focus();
    },

    initCharacterCounter: function () {
      var textarea = document.getElementById('input_message');
      var counter = document.getElementById('messageCharCount');
      if (!textarea || !counter) return;

      var updateCount = function () {
        var len = textarea.value.length;
        counter.textContent = len + ' / 2000';
      };

      textarea.addEventListener('input', updateCount);
      updateCount();
    },

    initDropzone: function () {
      var dropzone = document.getElementById('ticketDropzone');
      var fileInput = document.getElementById('input_attachment');
      if (!dropzone || !fileInput) return;

      ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.add('is-dragover');
        });
      });

      ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove('is-dragover');
        });
      });

      dropzone.addEventListener('drop', function (e) {
        var dt = e.dataTransfer;
        var files = dt.files;
        if (files && files.length > 0) {
          fileInput.files = files;
          TicketWizard.handleFileSelected(fileInput);
        }
      });
    },

    /**
     * Memilih Pilar Kategori Utama pada Step 1 (Main Category)
     */
    onMainCategorySelected: function (element, categoryId, categoryTitle) {
      document.querySelectorAll('.category-card').forEach(function (card) {
        card.classList.remove('is-selected');
      });

      if (element) {
        element.classList.add('is-selected');
      }

      var inputCat = document.getElementById('input_category_id');
      if (inputCat) {
        inputCat.value = categoryId;
      }

      this.filterSubCategories(categoryId);
    },

    filterSubCategories: function (categoryId) {
      var selectDb = document.getElementById('select_db_req_type');
      if (!selectDb) return;

      var firstVisibleVal = '';
      var hasMatched = false;

      for (var i = 0; i < selectDb.options.length; i++) {
        var opt = selectDb.options[i];
        var optCatId = opt.getAttribute('data-cat-id');

        if (!optCatId || optCatId === categoryId) {
          opt.style.display = '';
          opt.disabled = false;
          if (!firstVisibleVal) {
            firstVisibleVal = opt.value;
          }
          if (selectDb.value === opt.value) {
            hasMatched = true;
          }
        } else {
          opt.style.display = 'none';
          opt.disabled = true;
        }
      }

      if (!hasMatched && firstVisibleVal) {
        selectDb.value = firstVisibleVal;
        this.onSelectDropdownType(firstVisibleVal);
      }
    },

    /**
     * Memilih Kategori Topik Layanan pada Step 1 (Fallback / Direct)
     */
    selectCategory: function (element, categoryVal, categoryTitle) {
      document.querySelectorAll('.category-card').forEach(function (card) {
        card.classList.remove('is-selected');
      });

      if (element) {
        element.classList.add('is-selected');
      }

      var inputReq = document.getElementById('input_req_type');
      if (inputReq) {
        inputReq.value = categoryVal;
      }

      var selectDb = document.getElementById('select_db_req_type');
      if (selectDb) {
        for (var i = 0; i < selectDb.options.length; i++) {
          if (selectDb.options[i].value === categoryVal) {
            selectDb.selectedIndex = i;
            break;
          }
        }
      }
    },

    onSelectDropdownType: function (val) {
      var inputReq = document.getElementById('input_req_type');
      if (inputReq) {
        inputReq.value = val;
      }
    },

    goToStep: function (targetStep) {
      if (targetStep === this.currentStep) return;

      if (targetStep > this.currentStep) {
        for (var s = this.currentStep; s < targetStep; s++) {
          if (!this.validateStep(s)) {
            return;
          }
        }
      }

      this.switchPanel(targetStep);
    },

    nextStep: function (fromStep) {
      if (!this.validateStep(fromStep)) {
        return;
      }
      var targetStep = fromStep + 1;
      if (targetStep <= 3) {
        this.switchPanel(targetStep);
      }
    },

    prevStep: function (fromStep) {
      var targetStep = fromStep - 1;
      if (targetStep >= 1) {
        this.switchPanel(targetStep);
      }
    },

    validateStep: function (step) {
      if (step === 1) {
        var reqType = document.getElementById('input_req_type');
        if (!reqType || !reqType.value.trim()) {
          alert('Silakan pilih salah satu topik layanan kepegawaian terlebih dahulu.');
          return false;
        }
        return true;
      }

      if (step === 2) {
        var nipField = document.getElementById('input_emp_id');
        var nameField = document.getElementById('input_emp_name');
        var subjectField = document.getElementById('input_subject');
        var emailField = document.getElementById('input_email');
        var errBox = document.getElementById('step2ErrorBox');
        var errMsg = document.getElementById('step2ErrorMessage');

        var errors = [];

        if (!nipField || !nipField.value.trim()) {
          errors.push('Nomor Induk Pegawai (NIP / NIK) wajib diisi.');
        }

        if (!nameField || !nameField.value.trim()) {
          errors.push('Nama Lengkap Pegawai wajib diisi.');
        }

        if (!subjectField || !subjectField.value.trim()) {
          errors.push('Subjek / Ringkasan Masalah wajib diisi.');
        } else if (subjectField.value.trim().length < 5) {
          errors.push('Subjek tiket minimal 5 karakter.');
        }

        if (emailField && emailField.value.trim()) {
          var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(emailField.value.trim())) {
            errors.push('Format email tidak valid (contoh: nama@mtf.co.id).');
          }
        }

        if (errors.length > 0) {
          if (errBox && errMsg) {
            errMsg.innerHTML = errors.join('<br>');
            errBox.style.display = 'block';
            errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
          } else {
            alert(errors.join('\n'));
          }
          return false;
        }

        if (errBox) errBox.style.display = 'none';
        return true;
      }

      return true;
    },

    switchPanel: function (targetStep) {
      for (var i = 1; i <= 3; i++) {
        var panel = document.getElementById('stepPanel' + i);
        if (panel) {
          if (i === targetStep) {
            panel.classList.remove('is-hidden');
            panel.classList.add('is-active');
          } else {
            panel.classList.remove('is-active');
            panel.classList.add('is-hidden');
          }
        }
      }

      this.currentStep = targetStep;
      this.updateStepperUI(targetStep);

      if (targetStep === 3) {
        this.renderReviewSummary();
      }

      var stepperContainer = document.querySelector('.ticket-wizard-stepper-container');
      if (stepperContainer) {
        stepperContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    },

    updateStepperUI: function (activeStep) {
      for (var s = 1; s <= 3; s++) {
        var indicator = document.getElementById('stepIndicator' + s);
        var iconEl = document.getElementById('stepIcon' + s);
        var labelEl = document.getElementById('stepLabel' + s);
        var badgeEl = document.getElementById('stepBadge' + s);

        if (!indicator) continue;

        indicator.classList.remove('is-completed', 'is-active', 'is-pending');

        if (s < activeStep) {
          indicator.classList.add('is-completed');
          if (iconEl) iconEl.innerHTML = '<i class="fa-solid fa-check"></i>';
          if (labelEl) labelEl.textContent = 'LANGKAH ' + s;
          if (badgeEl) badgeEl.style.display = 'none';
        } else if (s === activeStep) {
          indicator.classList.add('is-active');
          if (iconEl) iconEl.textContent = s;
          if (labelEl) labelEl.textContent = 'LANGKAH ' + s + ' (AKTIF)';
          if (badgeEl) badgeEl.style.display = 'inline-block';
        } else {
          indicator.classList.add('is-pending');
          if (iconEl) iconEl.textContent = s;
          if (labelEl) labelEl.textContent = 'LANGKAH ' + s;
          if (badgeEl) badgeEl.style.display = 'none';
        }
      }
    },

    renderReviewSummary: function () {
      var reqType = document.getElementById('input_req_type') ? document.getElementById('input_req_type').value : '—';
      var priorityRadio = document.querySelector('input[name="ticket_priority"]:checked');
      var priorityVal = priorityRadio ? priorityRadio.value : 'medium';

      var priorityLabels = {
        low: 'Biasa / Low (Target SLA: 48 Jam)',
        medium: 'Standar / Medium (Target SLA: 24 Jam)',
        high: 'Mendesak / High (Target SLA: ≤ 4 Jam)'
      };

      var nip = document.getElementById('input_emp_id') ? document.getElementById('input_emp_id').value : '—';
      var name = document.getElementById('input_emp_name') ? document.getElementById('input_emp_name').value : '—';
      var email = document.getElementById('input_email') ? document.getElementById('input_email').value : '—';
      var wa = document.getElementById('input_wa_no') ? document.getElementById('input_wa_no').value : '—';
      var subject = document.getElementById('input_subject') ? document.getElementById('input_subject').value : '—';
      var message = document.getElementById('input_message') ? document.getElementById('input_message').value : '';

      var topicEl = document.getElementById('summaryTopic');
      var priorityEl = document.getElementById('summaryPriority');
      var reqEl = document.getElementById('summaryRequester');
      var contactEl = document.getElementById('summaryContact');
      var subjectEl = document.getElementById('summarySubject');
      var msgEl = document.getElementById('summaryMessage');
      var attachEl = document.getElementById('summaryAttachment');

      if (topicEl) topicEl.textContent = reqType;
      if (priorityEl) priorityEl.textContent = priorityLabels[priorityVal] || priorityVal;
      if (reqEl) reqEl.textContent = name + ' (NIP: ' + nip + ')';

      var contactParts = [];
      if (email && email !== '—') contactParts.push(email);
      if (wa && wa !== '—') contactParts.push(wa);
      if (contactEl) contactEl.textContent = contactParts.length > 0 ? contactParts.join(' • ') : 'Tidak dicantumkan';

      if (subjectEl) subjectEl.textContent = subject;
      if (msgEl) msgEl.textContent = message.trim() ? message : 'Tidak ada kronologi tambahan';

      if (attachEl) {
        if (this.selectedFile) {
          var sizeInfo = this.formatFileSize(this.selectedFile.size);
          if (this.compressedFileBlob) {
            sizeInfo = this.formatFileSize(this.compressedFileBlob.size) + ' (Terkompresi WebP)';
          }
          attachEl.innerHTML = '<i class="fa-solid fa-paperclip mr-1 text-info"></i> ' + this.selectedFile.name + ' (' + sizeInfo + ')';
        } else {
          attachEl.textContent = 'Tidak ada berkas terlampir';
        }
      }
    },

    /**
     * Memproses berkas yang dipilih pengguna
     * Mendukung Gambar, PDF, Word, dan Excel
     */
    handleFileSelected: function (input) {
      if (!input.files || input.files.length === 0) return;

      var file = input.files[0];
      var maxSize = 10 * 1024 * 1024; // 10 MB

      if (file.size > maxSize) {
        alert('Ukuran berkas melebihi batas maksimal 10 MB.');
        input.value = '';
        return;
      }

      this.selectedFile = file;
      this.compressedFileBlob = null;

      var promptBox = document.getElementById('dropzonePrompt');
      var previewBox = document.getElementById('dropzoneFilePreview');
      var fileNameEl = document.getElementById('previewFileName');
      var fileSizeEl = document.getElementById('previewFileSize');
      var fileIconEl = document.getElementById('fileTypeIcon');

      if (promptBox) promptBox.style.display = 'none';
      if (previewBox) previewBox.style.display = 'flex';
      if (fileNameEl) fileNameEl.textContent = file.name;
      if (fileSizeEl) fileSizeEl.textContent = this.formatFileSize(file.size) + ' • Memvalidasi...';

      // Pasang icon sesuai format berkas
      var ext = file.name.split('.').pop().toLowerCase();
      var isImg = file.type.indexOf('image/') !== -1 || ['jpg', 'jpeg', 'png', 'webp'].indexOf(ext) !== -1;

      if (fileIconEl) {
        if (isImg) {
          fileIconEl.innerHTML = '<i class="fa-solid fa-file-image text-primary"></i>';
        } else if (ext === 'pdf') {
          fileIconEl.innerHTML = '<i class="fa-solid fa-file-pdf text-danger"></i>';
        } else if (['xlsx', 'xls', 'csv'].indexOf(ext) !== -1) {
          fileIconEl.innerHTML = '<i class="fa-solid fa-file-excel text-success"></i>';
        } else if (['docx', 'doc'].indexOf(ext) !== -1) {
          fileIconEl.innerHTML = '<i class="fa-solid fa-file-word text-info"></i>';
        } else {
          fileIconEl.innerHTML = '<i class="fa-solid fa-file-lines text-muted"></i>';
        }
      }

      // Jika format gambar, lakukan client-side downscale & WebP pre-compression
      if (isImg) {
        this.compressImageClientSide(file, function (compressedBlob) {
          TicketWizard.compressedFileBlob = compressedBlob;
          TicketWizard.isGzipCompressed = false;
          var originalKb = Math.round(file.size / 1024);
          var compKb = Math.round(compressedBlob.size / 1024);
          var savedPercent = Math.max(0, Math.round(((file.size - compressedBlob.size) / file.size) * 100));

          if (fileSizeEl) {
            fileSizeEl.textContent = compKb + ' KB (Hemat ' + savedPercent + '% dari ' + originalKb + ' KB) • Siap';
          }
        });
      } else {
        var ext = (file.name.split('.').pop() || '').toLowerCase();
        var typeLabel = 'Dokumen';
        if (ext === 'pdf') typeLabel = 'PDF';
        else if (['xlsx', 'xls', 'csv'].indexOf(ext) !== -1) typeLabel = 'Excel Spreadsheet';
        else if (['docx', 'doc'].indexOf(ext) !== -1) typeLabel = 'Word Document';

        this.compressDocumentClientSide(file, function (compressedBlob) {
          if (compressedBlob) {
            TicketWizard.compressedFileBlob = compressedBlob;
            TicketWizard.isGzipCompressed = true;
            var originalKb = Math.round(file.size / 1024);
            var compKb = Math.round(compressedBlob.size / 1024);
            var savedPercent = Math.max(0, Math.round(((file.size - compressedBlob.size) / file.size) * 100));

            if (fileSizeEl) {
              fileSizeEl.textContent = compKb + ' KB (Hemat ' + savedPercent + '% dari ' + originalKb + ' KB) • ' + typeLabel + ' (Kompresi Gzip Lossless)';
            }
          } else {
            TicketWizard.compressedFileBlob = null;
            TicketWizard.isGzipCompressed = false;
            if (fileSizeEl) {
              fileSizeEl.textContent = TicketWizard.formatFileSize(file.size) + ' • ' + typeLabel + ' (Kompresi Server Aktif)';
            }
          }
        });
      }
    },

    /**
     * Kompresi dokumen (PDF, Excel, Word) di browser menggunakan Web API CompressionStream
     * Menghasilkan stream gzip lossless RFC 1952 secara instan (<20ms)
     */
    compressDocumentClientSide: function (file, callback) {
      if (typeof CompressionStream === 'undefined') {
        callback(null);
        return;
      }
      try {
        var cs = new CompressionStream('gzip');
        var stream = file.stream().pipeThrough(cs);
        new Response(stream).blob().then(function (compressedBlob) {
          if (compressedBlob && compressedBlob.size < file.size) {
            callback(compressedBlob);
          } else {
            callback(null);
          }
        }).catch(function () {
          callback(null);
        });
      } catch (err) {
        callback(null);
      }
    },

    /**
     * Kompresi gambar instan di sisi browser menggunakan HTML5 Canvas
     * Menghasilkan WebP beresolusi maks 1280px (Ukuran turun drastis tanpa beban server)
     */
    compressImageClientSide: function (file, callback) {
      var reader = new FileReader();
      reader.onload = function (e) {
        var img = new Image();
        img.onload = function () {
          var maxDimension = 1280;
          var width = img.width;
          var height = img.height;

          if (width > maxDimension || height > maxDimension) {
            if (width >= height) {
              height = Math.round((height / width) * maxDimension);
              width = maxDimension;
            } else {
              width = Math.round((width / height) * maxDimension);
              height = maxDimension;
            }
          }

          var canvas = document.createElement('canvas');
          canvas.width = width;
          canvas.height = height;

          var ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, width, height);

          // Coba WebP 80%, fallback ke JPEG 80%
          canvas.toBlob(function (blob) {
            if (blob) {
              callback(blob);
            } else {
              callback(file);
            }
          }, 'image/webp', 0.8);
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    removeFile: function (event) {
      if (event) {
        event.stopPropagation();
      }

      var fileInput = document.getElementById('input_attachment');
      if (fileInput) fileInput.value = '';

      this.selectedFile = null;
      this.compressedFileBlob = null;

      var promptBox = document.getElementById('dropzonePrompt');
      var previewBox = document.getElementById('dropzoneFilePreview');

      if (promptBox) promptBox.style.display = 'block';
      if (previewBox) previewBox.style.display = 'none';

      var summaryAttach = document.getElementById('summaryAttachment');
      if (summaryAttach) summaryAttach.textContent = 'Tidak ada berkas terlampir';
    },

    formatFileSize: function (bytes) {
      if (!bytes || bytes === 0) return '0 B';
      var k = 1024;
      var sizes = ['B', 'KB', 'MB', 'GB'];
      var i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    },

    /**
     * Submit Formulir Tiket secara Asinkron dengan Floating Toast & Persentase Realtime
     */
    submitTicketForm: function (e) {
      if (e) e.preventDefault();

      var agreeCheck = document.getElementById('checkAgreement');
      if (agreeCheck && !agreeCheck.checked) {
        alert('Mohon centang pernyataan persetujuan sebelum mengirimkan tiket.');
        agreeCheck.focus();
        return;
      }

      var form = document.getElementById('formTicketCreate');
      if (!form) return;

      var subjectVal = document.getElementById('input_subject') ? document.getElementById('input_subject').value : '';
      var fileNameVal = this.selectedFile ? this.selectedFile.name : null;

      var formData = new FormData(form);

      // Jika ada hasil kompresi WebP atau Gzip client-side, gantikan payload file asli
      if (this.compressedFileBlob && this.selectedFile) {
        if (this.isGzipCompressed) {
          formData.set('attachment', this.compressedFileBlob, this.selectedFile.name);
          formData.append('is_client_compressed', 'gzip');
        } else {
          var baseName = this.selectedFile.name.substring(0, this.selectedFile.name.lastIndexOf('.')) || this.selectedFile.name;
          formData.set('attachment', this.compressedFileBlob, baseName + '.webp');
        }
      }

      // CSRF token
      var metaCsrf = document.querySelector('meta[name="csrf-token"]');
      if (metaCsrf) {
        formData.append('csrf_test_name', metaCsrf.getAttribute('content'));
      }

      // Tampilkan Floating Toast di Kanan Bawah
      this.showAsyncToast(subjectVal, fileNameVal);

      // Kirim via XMLHttpRequest untuk mendapatkan event onprogress realtime
      var xhr = new XMLHttpRequest();
      this.activeXhr = xhr;

      xhr.open('POST', form.action, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      // 1. Upload Progress Event (Persentase 0% - 100%)
      xhr.upload.onprogress = function (event) {
        if (event.lengthComputable) {
          var percent = Math.round((event.loaded / event.total) * 100);
          TicketWizard.updateAsyncToastProgress(percent, event.loaded, event.total);
        } else {
          TicketWizard.updateAsyncToastProgress(50, 0, 0);
        }
      };

      // 2. Selesai Terkirim / Respon Server
      xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            var data = JSON.parse(xhr.responseText);
            if (data.status === 'success') {
              var ticketNo = data.ticket_no || 'HC-2026-00001';
              var targetUrl = data.redirect || '/dashboard';
              TicketWizard.setAsyncToastSuccess(ticketNo);

              // Simpan state tiket ke sessionStorage agar Gambar 2 tetap tampil mengambang saat tiba di /dashboard
              var ticketData = {
                ticket_no: ticketNo,
                subject: subjectVal,
                file_name: fileNameVal
              };

              sessionStorage.setItem('activeTicketSuccessToast', JSON.stringify(ticketData));

              // Langsung redirect ke dashboard pengguna
              setTimeout(function () {
                window.location.href = targetUrl;
              }, 350);
            } else {
              TicketWizard.setAsyncToastError(data.message || 'Gagal memproses tiket.');
            }
          } catch (err) {
            TicketWizard.setAsyncToastError('Respon server tidak valid.');
          }
        } else {
          try {
            var errData = JSON.parse(xhr.responseText);
            TicketWizard.setAsyncToastError(errData.message || 'Terjadi kesalahan sistem.');
          } catch (e) {
            TicketWizard.setAsyncToastError('Gagal mengirimkan tiket (HTTP ' + xhr.status + ').');
          }
        }
      };

      // 3. Error Koneksi
      xhr.onerror = function () {
        TicketWizard.setAsyncToastError('Koneksi terputus ke server.');
      };

      xhr.send(formData);
    },

    /**
     * Menampilkan Toast Loading di Kanan Bawah
     */
    showAsyncToast: function (subject, fileName) {
      var toast = document.getElementById('asyncUploadToast');
      if (!toast) return;

      var titleEl = document.getElementById('toastTitle');
      var subtitleEl = document.getElementById('toastSubtitle');
      var iconCircle = document.getElementById('toastStatusIcon');
      var badgeEl = document.getElementById('toastPercentageBadge');
      var progressBar = document.getElementById('toastProgressBar');
      var subjText = document.getElementById('toastSubjectText');
      var fileText = document.getElementById('toastFileText');
      var fileLine = document.getElementById('toastFileLine');
      var bytesText = document.getElementById('toastBytesText');
      var completeBox = document.getElementById('toastCompleteBox');

      if (titleEl) titleEl.textContent = 'Membuat Tiket Layanan';
      if (subtitleEl) subtitleEl.textContent = 'Mengunggah & memproses antrean...';
      if (iconCircle) {
        iconCircle.className = 'toast-icon-circle';
        iconCircle.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-primary"></i>';
      }
      if (badgeEl) {
        badgeEl.className = 'toast-pill-badge';
        badgeEl.textContent = '0%';
      }
      if (progressBar) {
        progressBar.className = 'toast-progress-bar';
        progressBar.style.width = '0%';
      }
      if (subjText) subjText.textContent = subject || 'Pengajuan Tiket HC';
      if (fileText && fileName) {
        fileText.textContent = fileName;
        if (fileLine) fileLine.style.display = 'block';
      } else if (fileLine) {
        fileLine.style.display = 'none';
      }
      if (bytesText) bytesText.textContent = 'Menghubungkan...';
      if (completeBox) completeBox.style.display = 'none';

      toast.style.display = 'block';
    },

    /**
     * Memperbarui visual persentase dan bytes terkirim
     */
    updateAsyncToastProgress: function (percent, loadedBytes, totalBytes) {
      var badgeEl = document.getElementById('toastPercentageBadge');
      var progressBar = document.getElementById('toastProgressBar');
      var bytesText = document.getElementById('toastBytesText');

      if (badgeEl) badgeEl.textContent = percent + '%';
      if (progressBar) progressBar.style.width = percent + '%';

      if (bytesText) {
        if (totalBytes > 0) {
          bytesText.textContent = this.formatFileSize(loadedBytes) + ' / ' + this.formatFileSize(totalBytes);
        } else {
          bytesText.textContent = percent + '% terkirim';
        }
      }
    },

    /**
     * Menampilkan status sukses pada Toast
     */
    setAsyncToastSuccess: function (ticketNo) {
      var titleEl = document.getElementById('toastTitle');
      var subtitleEl = document.getElementById('toastSubtitle');
      var iconCircle = document.getElementById('toastStatusIcon');
      var badgeEl = document.getElementById('toastPercentageBadge');
      var progressBar = document.getElementById('toastProgressBar');
      var completeBox = document.getElementById('toastCompleteBox');
      var ticketNoEl = document.getElementById('toastTicketNo');
      var trackLink = document.getElementById('toastTrackLink');
      var bytesText = document.getElementById('toastBytesText');

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
      if (bytesText) bytesText.textContent = 'Berhasil diproses';

      if (ticketNoEl) ticketNoEl.textContent = ticketNo;
      if (trackLink) trackLink.href = '/ticket/track?ticket_no=' + encodeURIComponent(ticketNo);
      if (completeBox) completeBox.style.display = 'block';

      // Modal tidak lagi dimunculkan agar pengguna langsung dialihkan ke dashboard dengan floating toast
      // this.showSuccessModal(ticketNo);
    },

    setAsyncToastError: function (errorMessage) {
      var titleEl = document.getElementById('toastTitle');
      var subtitleEl = document.getElementById('toastSubtitle');
      var iconCircle = document.getElementById('toastStatusIcon');
      var badgeEl = document.getElementById('toastPercentageBadge');
      var bytesText = document.getElementById('toastBytesText');

      if (titleEl) titleEl.textContent = 'Gagal Mengirimkan Tiket';
      if (subtitleEl) subtitleEl.textContent = errorMessage;
      if (iconCircle) {
        iconCircle.className = 'toast-icon-circle';
        iconCircle.style.background = '#FEE2E2';
        iconCircle.style.color = '#B91C1C';
        iconCircle.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
      }
      if (badgeEl) {
        badgeEl.textContent = 'Error';
        badgeEl.style.background = '#FEE2E2';
        badgeEl.style.color = '#B91C1C';
      }
      if (bytesText) bytesText.textContent = 'Silakan coba lagi';
    },

    closeAsyncToast: function () {
      var toast = document.getElementById('asyncUploadToast');
      if (toast) toast.style.display = 'none';
    },

    copyAsyncTicketNo: function () {
      var codeEl = document.getElementById('toastTicketNo');
      if (!codeEl) return;
      var text = codeEl.textContent.trim();
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function () {
          alert('Nomor tiket ' + text + ' berhasil disalin!');
        });
      } else {
        alert('Nomor tiket: ' + text);
      }
    },

    showSuccessModal: function (ticketNo) {
      var modal = document.getElementById('ticketSuccessModal');
      var codeEl = document.getElementById('successTicketNo');
      var btnTrack = document.getElementById('btnTrackSuccessTicket');

      if (codeEl) codeEl.textContent = ticketNo;
      if (btnTrack) {
        btnTrack.href = '/ticket/track?ticket_no=' + encodeURIComponent(ticketNo);
      }

      if (modal) {
        modal.classList.add('is-active');
      }
    },

    closeSuccessModal: function () {
      var modal = document.getElementById('ticketSuccessModal');
      if (modal) {
        modal.classList.remove('is-active');
      }
      window.location.href = '/dashboard';
    },

    copyTicketNo: function () {
      var codeEl = document.getElementById('successTicketNo');
      if (!codeEl) return;
      var text = codeEl.textContent.trim();
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function () {
          alert('Nomor tiket ' + text + ' berhasil disalin ke papan klip!');
        });
      } else {
        alert('Nomor tiket: ' + text);
      }
    }
  };

  global.TicketWizard = TicketWizard;

  document.addEventListener('DOMContentLoaded', function () {
    TicketWizard.init();
  });

})(window);
