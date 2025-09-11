function onReportTypeChange() {
            var val = document.getElementById('report_type').value;
            var ticketFields = document.getElementById('ticket-fields');
            var slaFields = document.getElementById('sla-fields');
            if (val === 'Report Ticket Detail') {
                ticketFields.style.display = 'flex';
                slaFields.style.display = 'none';
            } else {
                ticketFields.style.display = 'none';
                slaFields.style.display = 'flex';
            }
        }

        // Fungsi modal error
        function showGlobalError(msg) {
            var errorDiv = document.getElementById('global-error-confirm');
            errorDiv.innerHTML = '<div style="background:#fff;padding:32px 24px;border-radius:18px;box-shadow:0 2px 12px #eee;max-width:350px;margin:auto;text-align:center;"><div style="font-size:22px;color:#ef4444;margin-bottom:12px;">❌ Error</div><div style="font-size:16px;color:#333;margin-bottom:18px;">'+msg+'</div><button onclick="document.getElementById(\'global-error-confirm\').style.display=\'none\'" style="background:#ef4444;color:#fff;border:none;border-radius:12px;padding:8px 24px;font-size:15px;cursor:pointer;">Tutup</button></div>';
            errorDiv.style.display = 'flex';
        }

        window.addEventListener('DOMContentLoaded', function() {
            var exportForm = document.getElementById('exportAsyncForm');
            if (exportForm) {
                exportForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var form = e.target;
                    var formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('global-success-message').innerText = 'Export job berhasil ditambahkan! Silakan cek daftar di bawah.';
                            document.getElementById('global-success-confirm-bg').style.display = 'block';
                            document.getElementById('global-success-confirm').style.display = 'block';
                            setTimeout(function() {
                                document.getElementById('global-success-confirm-bg').style.display = 'none';
                                document.getElementById('global-success-confirm').style.display = 'none';
                                location.reload();
                            }, 1500);
                        } else {
                            showGlobalError(data.message || 'Gagal submit export job.');
                        }
                    })
                    .catch(function() {
                        showGlobalError('Gagal submit export job.');
                    });
                });
            }
            onReportTypeChange();

            // Success modal function
            function showSuccessModal(msg) {
                document.getElementById('global-success-message').innerText = msg;
                document.getElementById('global-success-confirm-bg').style.display = 'block';
                document.getElementById('global-success-confirm').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('global-success-confirm-bg').style.display = 'none';
                    document.getElementById('global-success-confirm').style.display = 'none';
                    location.reload();
                }, 1500);
            }

            // Warning modal function
            function showWarningModal(msg, onOk) {
                document.getElementById('global-warning-message').innerText = msg;
                document.getElementById('global-warning-confirm-bg').style.display = 'block';
                document.getElementById('global-warning-confirm').style.display = 'block';
                document.getElementById('global-warning-cancel-btn').onclick = function() {
                    document.getElementById('global-warning-confirm-bg').style.display = 'none';
                    document.getElementById('global-warning-confirm').style.display = 'none';
                };
                document.getElementById('global-warning-ok-btn').onclick = function() {
                    document.getElementById('global-warning-confirm-bg').style.display = 'none';
                    document.getElementById('global-warning-confirm').style.display = 'none';
                    onOk();
                };
            }

            // Intercept delete form submit
            document.querySelectorAll('.delete-job-form').forEach(function(form) {
                var btn = form.querySelector('button[type="button"]');
                btn.addEventListener('click', function(e) {
                    showWarningModal('Yakin hapus job ini?', function() {
                        // Submit via AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', form.action, true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                showSuccessModal('Job berhasil dihapus!');
                            } else {
                                showGlobalError('Gagal menghapus job.');
                            }
                        };
                        xhr.send(new FormData(form));
                    });
                });
            });
        });