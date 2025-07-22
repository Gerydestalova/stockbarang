<?php
require 'function.php';
require 'cek.php';
?>
<html>
<head>
  <title>Barang Masuk</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
</head>

<body>
<div class="container">
  <h2>Barang Masuk</h2>
  <h4>(Inventory)</h4>
  <div class="data-tables datatable-dark">
    <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Deskripsi</th>
          <th>Jumlah</th>
          <th>Tanggal</th>
          <th>Penerima</th>
          <th>Supplier</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $ambilsemuadatastock = mysqli_query($conn,"
          SELECT masuk.*, stock.namabarang, stock.deskripsi 
          FROM masuk 
          JOIN stock ON masuk.idbarang = stock.idbarang
        ");
        $i = 1;
        while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
          $namabarang = $data['namabarang'];
          $deskripsi = $data['deskripsi'];
          $qty = $data['qty'];
          $tanggal = $data['tanggal'];
          $penerima = $data['keterangan'];
          $supplier = $data['namasupplier'] ?? '-';
        ?>
        <tr>
          <td><?= $i++; ?></td>
          <td><?= $namabarang; ?></td>
          <td><?= $deskripsi; ?></td>
          <td><?= $qty; ?></td>
          <td><?= $tanggal; ?></td>
          <td><?= $penerima; ?></td>
          <td><?= $supplier; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Script datatables export -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
<script>
$(document).ready(function() {
  $('#mauexport').DataTable({
    dom: 'Bfrtip',
    buttons: ['excel', 'pdf', 'print']
  });
});
</script>

</body>
</html>
