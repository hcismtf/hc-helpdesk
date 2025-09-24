function searchTicketTable() {
            var input = document.getElementById("searchTicket");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("ticketsTable");
            var tr = table.getElementsByTagName("tr");
            for (var i = 1; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td");
                var show = false;
                for (var j = 0; j < td.length-1; j++) {
                    if (td[j] && td[j].innerText.toUpperCase().indexOf(filter) > -1) {
                        show = true;
                    }
                }
                tr[i].style.display = show ? "" : "none";
            }
        }
       
        function openFilterModal() {
            // Implement modal if needed
        }