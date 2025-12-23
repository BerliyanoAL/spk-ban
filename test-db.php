<?php
// test-db.php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "spk_ban";

$koneksi = new mysqli($host, $user, $password, $db_name);

if ($koneksi->connect_error) {
    die("Koneksi GAGAL: " . $koneksi->connect_error);
} else {
    echo "Koneksi ke database BERHASIL!";
}
?>