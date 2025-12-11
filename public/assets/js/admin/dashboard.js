function handleSearchKeypress(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchTicketTable();
            }
        }

        function searchTicketTable() {
            var input = document.getElementById("searchTicket");
            var filter = input.value.trim();
            
            if (filter === '') {
                // If search is empty, reload without search parameter
                var url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.set('page', 1);
                window.location = url.pathname + '?' + url.searchParams.toString();
            } else {
                // Perform server-side search
                var url = new URL(window.location.href);
                url.searchParams.set('search', filter);
                url.searchParams.set('page', 1); // Reset to page 1
                window.location = url.pathname + '?' + url.searchParams.toString();
            }
        }
        function openFilterModal() {
            document.getElementById('filterModal').style.display = 'block';
        }
        function closeFilterModal() {
            document.getElementById('filterModal').style.display = 'none';
        }
        function applyFilter() {
            var params = [];
            var form = document.getElementById('filterForm');
            for (var i=0; i<form.elements.length; i++) {
                var el = form.elements[i];
                if (el.name && el.value) params.push(el.name+'='+encodeURIComponent(el.value));
            }
            params.push('per_page='+document.getElementById('perPage').value);
            window.location = '?'+params.join('&');
        }
        function resetFilter() {
            window.location = '?per_page='+document.getElementById('perPage').value;
        }
        // Show modal in center
        function openFilterModal() {
            document.getElementById('filterModal').style.display = 'flex';
            setTimeout(function() {
                document.getElementById('filterModal').focus();
            }, 100);
        }
        // Hide modal
        function closeFilterModal() {
            document.getElementById('filterModal').style.display = 'none';
        }
        // ESC key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                closeFilterModal();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            var startInput = document.getElementById('startDate');
            var endInput = document.getElementById('endDate');
            function updateEndMin() {
                endInput.min = startInput.value;
                // If end < start, set end = start
                if (endInput.value && endInput.value < startInput.value) {
                    endInput.value = startInput.value;
                }
            }
            startInput.addEventListener('change', updateEndMin);
            // Initial set if start already filled
            if (startInput.value) updateEndMin();
        });