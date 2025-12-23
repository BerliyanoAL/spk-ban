<?php
// koneksi.php
 $host = "localhost";
 $user = "root";
 $password = "";
 $db_name = "spk_ban";

 $koneksi = new mysqli($host, $user, $password, $db_name);

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}