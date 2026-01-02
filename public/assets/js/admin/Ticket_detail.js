function showAttachmentModal(url) {
            document.getElementById('attachmentModalImg').src = url;
            // Ambil nama file dari url
            var filename = url.split('/').pop();
            document.getElementById('attachmentModalFilename').textContent = filename;
            document.getElementById('attachmentModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.addEventListener('keydown', escCloseAttachmentModal);
        }
        function closeAttachmentModal() {
            document.getElementById('attachmentModal').style.display = 'none';
            document.getElementById('attachmentModalImg').src = '';
            document.body.style.overflow = '';
            document.removeEventListener('keydown', escCloseAttachmentModal);
        }
        function escCloseAttachmentModal(e) {
            if (e.key === "Escape") {
                closeAttachmentModal();
            }
        }
        function showStatusModal() {
            document.getElementById('statusModal').style.display = 'flex';
            document.addEventListener('keydown', escCloseModal);
        }
        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
            document.removeEventListener('keydown', escCloseModal);
        }
        function escCloseModal(e) {
            if (e.key === "Escape") {
                closeStatusModal();
            }
        }
        function showReplyStatusModal() {
            document.getElementById('replyStatusModal').style.display = 'flex';
            document.addEventListener('keydown', escCloseReplyModal);
        }
        function closeReplyStatusModal() {
            document.getElementById('replyStatusModal').style.display = 'none';
            document.removeEventListener('keydown', escCloseReplyModal);
        }
        function escCloseReplyModal(e) {
            if (e.key === "Escape") {
                closeReplyStatusModal();
            }
        }
        function showLoadingModal() {
        document.getElementById('loadingModal').style.display = 'flex';
        }
        function hideLoadingModal() {
        document.getElementById('loadingModal').style.display = 'none';
        }
        function loadingModal(){
                var form = document.getElementById('replyStatusForm');
                
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); 
                        
                        closeReplyStatusModal();
                        showLoadingModal();

                        var formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (response.ok) {
                                window.location.reload(); 
                            } else {
                                throw new Error('Server error');
                            }
                        })
                        .catch(error => {
                            hideLoadingModal();
                            alert('Gagal mengirim balasan. Silakan coba lagi.');
                        });
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                loadingModal();
            });