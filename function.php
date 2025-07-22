<?php

date_default_timezone_set('Asia/Jakarta'); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "stockbarang");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tambah stok baru atau update stok barang yang ada
if (isset($_POST['tambahstok'])) {
    $qty = (int)$_POST['qty'];
    $namasupplier = $_POST['namasupplier'] ?? '-';
    $tanggal = date('Y-m-d');

    if ($_POST['barangnya'] === 'new') {
        $namabarang = $_POST['namabarangbaru'];
        $deskripsi = $_POST['deskripsibaru'];

        // Tambah ke stock
        $addnew = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, namasupplier) VALUES ('$namabarang', '$deskripsi', '$qty', '$namasupplier')");


    } else {
        $idbarang = $_POST['barangnya'];
        $cek = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idbarang'");
        $data = mysqli_fetch_array($cek);
        $stokbaru = $data['stock'] + $qty;
        $update = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idbarang'");

        // Simpan ke tabel masuk juga agar supplier bisa dilacak
        mysqli_query($conn, "INSERT INTO masuk (idbarang, qty, keterangan, tanggal, namasupplier) VALUES ('$idbarang', '$qty', 'Restok', '$tanggal', '$namasupplier')");
    }

    header("Location: index.php");
    exit;
}


// Tambah barang masuk (bisa barang baru atau yang sudah ada)
if (isset($_POST['barangmasuk'])) {
    $qty = (int)$_POST['qty'];
    $penerima = $_POST['penerima'];
    $namasupplier = isset($_POST['namasupplier']) ? $_POST['namasupplier'] : '-';
   $tanggal = date("Y-m-d H:i:s");


    if ($qty <= 0) {
        echo "Jumlah tidak boleh nol atau negatif!";
        exit;
    }

    if ($_POST['barangnya'] === 'new') {
        $namabarang = $_POST['namabarangbaru'];
        $deskripsi = $_POST['deskripsibaru'];

        $addnewbarang = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, namasupplier) 
                                             VALUES ('$namabarang', '$deskripsi', '$qty', '$namasupplier')");
        $idbarang = mysqli_insert_id($conn);
        $jnstransaksi = 'Barang Baru';

        $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, penerima, qty, namasupplier, jnstransaksi, tanggal) 
                                           VALUES ('$idbarang', '$penerima', '$qty', '$namasupplier', '$jnstransaksi', '$tanggal')");

    } else {
        $barangnya = $_POST['barangnya'];
        $cekstocksekarang = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$barangnya'");
        $data = mysqli_fetch_array($cekstocksekarang);
        $stocksekarang = $data['stock'] + $qty;

        $jnstransaksi = 'Restock Barang';

        $updatestock = mysqli_query($conn, "UPDATE stock SET stock='$stocksekarang' WHERE idbarang='$barangnya'");

        $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, penerima, qty, namasupplier, jnstransaksi, tanggal) 
                                           VALUES ('$barangnya', '$penerima', '$qty', '$namasupplier', '$jnstransaksi', '$tanggal')");
    }

    header('location: masuk.php');
    exit;
}



// Menambah Barang keluar
if (isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);
    $stocksekarang = $ambildatanya['stock'];

    if ($stocksekarang >= $qty) {
        $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

        $addtokeluar = mysqli_query($conn, "insert into keluar (idbarang, penerima, qty) values('$barangnya', '$penerima', '$qty')");
        $updatestockmasuk = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");

        if ($addtokeluar && $updatestockmasuk) {
            header('location:keluar.php');
        } else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    } else {
        $_SESSION['stok_tidak_cukup'] = "Stock saat ini tidak mencukupi!";
        header("Location: keluar.php");
        exit;

    }
}

// Update barang dan supplier
if (isset($_POST['updatebarang'])) {
    $id = $_POST['id'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $namasupplier = $_POST['namasupplier'];
    $tanggal = date("Y-m-d H:i:s");

    // Update ke tabel stock
    mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang='$id'");

    // Cek apakah barang ini sudah pernah masuk
    $lastMasuk = mysqli_query($conn, "SELECT idmasuk FROM masuk WHERE idbarang='$id' ORDER BY idmasuk DESC LIMIT 1");

    if (mysqli_num_rows($lastMasuk) > 0) {
        $row = mysqli_fetch_assoc($lastMasuk);
        $idmasuk = $row['idmasuk'];
        mysqli_query($conn, "UPDATE masuk SET namasupplier='$namasupplier' WHERE idmasuk='$idmasuk'");
    } else {
        // Kalau belum ada, tambahkan entri dummy agar supplier bisa disimpan
        mysqli_query($conn, "INSERT INTO masuk (idbarang, qty, penerima, namasupplier, tanggal) 
            VALUES ('$id', 0, 'Update supplier', '$namasupplier', '$tanggal')");
    }

    header('location: index.php');
    exit;
}


// Hapus barang
if (isset($_POST['hapusbarang'])) {
    $id = $_POST['id'];

    $cek = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$id'");
    $data = mysqli_fetch_array($cek);
    $stok = $data['stock'];

    if ($stok <= 0) {
        echo "<script>alert('Stok barang sudah habis. Tidak bisa menghapus!'); window.location.href='index.php';</script>";
        exit;
    }

    mysqli_query($conn, "DELETE FROM stock WHERE idbarang='$id'");
    header('location: index.php');
    exit;
}

if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb']; // id barang
    $idm = $_POST['idm']; // id masuk
    $qtybaru = $_POST['qty']; // jumlah baru
    $penerima = $_POST['penerima'];

    // ambil data lama dari tabel masuk
    $cek = mysqli_query($conn, "SELECT qty FROM masuk WHERE idmasuk='$idm'");
    $data = mysqli_fetch_array($cek);
    $qtylama = $data['qty'];

    // hitung selisih
    $selisih = $qtybaru - $qtylama;

    // update tabel masuk (biar halaman Restock ikut update)
    $updateMasuk = mysqli_query($conn, "UPDATE masuk SET qty='$qtybaru', penerima='$penerima' WHERE idmasuk='$idm'");

    // update tabel stock
    $updateStock = mysqli_query($conn, "UPDATE stock SET stock=stock+'$selisih' WHERE idbarang='$idb'");

    if($updateMasuk && $updateStock){
        header('Location: masuk.php'); // sukses
    } else {
        echo "Gagal update Restock Barang!";
        header('Location: masuk.php');
    }
}



// Hapus Barang Masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idm = $_POST['idm']; // id masuk
    $idb = $_POST['idb']; // id barang

    // Ambil qty dari transaksi yang dihapus
    $getQty = mysqli_query($conn, "SELECT qty FROM masuk WHERE idmasuk='$idm'");
    $data = mysqli_fetch_array($getQty);
    $qty = $data['qty'];

    // Kurangi stok di tabel stock
    $updateStock = mysqli_query($conn, "UPDATE stock SET stock = stock - $qty WHERE idbarang='$idb'");

    if(!$updateStock){
        echo "Gagal update stok: ".mysqli_error($conn);
        exit;
    }

    // Hapus data dari tabel masuk
    $hapusData = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idm'");

    if($hapusData){
        header('Location: masuk.php');
        exit;
    } else {
        echo "Gagal hapus Restock Barang!";
        exit;
    }
}




// Update barang keluar
if (isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = (int)$_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $cekqtylama = mysqli_query($conn, "SELECT qty FROM keluar WHERE idkeluar='$idk'");
    $qtylama = mysqli_fetch_array($cekqtylama)['qty'];

    if ($qty > $qtylama) {
        $selisih = $qty - $qtylama;
        $stokbaru = $stockskrg - $selisih;
    } else {
        $selisih = $qtylama - $qty;
        $stokbaru = $stockskrg + $selisih;
    }

    $update_stok = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    $update_keluar = mysqli_query($conn, "UPDATE keluar SET qty='$qty', penerima='$penerima' WHERE idkeluar='$idk'");

    if ($update_stok && $update_keluar) {
        header('location: keluar.php');
    } else {
        echo 'Gagal';
        header('location: keluar.php');
    }
}

// Hapus barang keluar
if (isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok + $qty;
    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idk'");

    if ($update && $hapusdata) {
        header('location: keluar.php');
    } else {
        header("location: keluar.php");
    }
}

// Tambah Supplier
if (isset($_POST['add_supplier'])) {
    $nama = $_POST['nama_supplier'];
    $jenis = $_POST['jenis_barang'];
    $barang = $_POST['nama_barang'];
    $alamat = $_POST['alamat'];
    $notlp = $_POST['no_tlp'];

    $insert = mysqli_query($conn, "INSERT INTO supplier (nama_supplier, jenis_barang, nama_barang, alamat, no_tlp)
                                   VALUES ('$nama','$jenis','$barang','$alamat','$notlp')");
}

// Update Supplier
if (isset($_POST['update_supplier'])) {
    $id = $_POST['id_supplier'];
    $nama = $_POST['nama_supplier'];
    $jenis = $_POST['jenis_barang'];
    $barang = $_POST['nama_barang'];
    $alamat = $_POST['alamat'];
    $notlp = $_POST['no_tlp'];

    $update = mysqli_query($conn, "UPDATE supplier SET 
                nama_supplier='$nama',
                jenis_barang='$jenis',
                nama_barang='$barang',
                alamat='$alamat',
                no_tlp='$notlp'
                WHERE id_supplier='$id'");
}

// Fungsi Tambah Supplier
function tambahSupplier($conn) {
    $namasupplier = $_POST['namasupplier'];
    $namabarang = $_POST['namabarang'];
    $alamat = $_POST['alamat'];
    $no_tlp = $_POST['no_tlp'];

    $query = "INSERT INTO supplier (namasupplier, namabarang, alamat, no_tlp) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $namasupplier, $namabarang, $alamat, $no_tlp);
    mysqli_stmt_execute($stmt);

    echo "<meta http-equiv='refresh' content='0'>";
}

// Fungsi Update Supplier
function updateSupplier($conn) {
    $id = $_POST['id_supplier'];
    $namasupplier = $_POST['namasupplier'];
    $namabarang = $_POST['namabarang'];
    $alamat = $_POST['alamat'];
    $no_tlp = $_POST['no_tlp'];

    $query = "UPDATE supplier SET namasupplier=?, namabarang=?, alamat=?, no_tlp=? WHERE idsupplier=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $namasupplier, $namabarang, $alamat, $no_tlp, $id);
    mysqli_stmt_execute($stmt);

    echo "<meta http-equiv='refresh' content='0'>";
}

// Fungsi Hapus Supplier
function deleteSupplier($conn) {
    $id = $_POST['id_supplier'];
    $query = "DELETE FROM supplier WHERE idsupplier=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    echo "<meta http-equiv='refresh' content='0'>";
}

// Routing Form
if (isset($_POST['add_supplier'])) {
    tambahSupplier($conn);
}

if (isset($_POST['update_supplier'])) {
    updateSupplier($conn);
}

if (isset($_POST['delete_supplier'])) {
    deleteSupplier($conn);
}
