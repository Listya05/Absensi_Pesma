</div>

<footer class="footer">
    Sistem Informasi Absensi Mahasiswi © 2026
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function tampilJam() {
        let waktu = new Date();
        let jam = waktu.getHours().toString().padStart(2, '0');
        let menit = waktu.getMinutes().toString().padStart(2, '0');
        let detik = waktu.getSeconds().toString().padStart(2, '0');
        document.getElementById("jam").innerHTML = jam + ":" + menit + ":" + detik;
    }

    setInterval(tampilJam, 1000);
    tampilJam();
</script>

<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

<script>
    new DataTable('#myTable');
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#myTable').DataTable({
            dom: 'ftip',
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            language: {
                search: "",
                searchPlaceholder: "🔍 Cari nomor kamar...",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Belum ada data",
                paginate: {
                    previous: "←",
                    next: "→"
                }
            }
        });
    });
</script>

<script>

const menu=document.getElementById("menu-toggle");
const sidebar=document.querySelector(".sidebar");
const overlay=document.getElementById("overlay");

if(menu){

menu.addEventListener("click",function(){

sidebar.classList.toggle("active");
overlay.classList.toggle("active");

});

}

if(overlay){

overlay.addEventListener("click",function(){

sidebar.classList.remove("active");
overlay.classList.remove("active");

});

}

</script>

</script>

</body>

</html>
