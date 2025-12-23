<!-- pages/data-ban.php -->
<div class="page-content">
    <h1>Data Ban Tersedia</h1>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Cari berdasarkan merek atau ukuran...">
    </div>
    <div class="table-wrapper">
        <table id="banTable">
            <thead>
                <tr>
                    <th>Merek</th>
                    <th>Ukuran</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_ban_query = $koneksi->query("
                    SELECT m.nama_merek, b.ukuran, b.harga 
                    FROM ban b
                    JOIN merek m ON b.id_merek = m.id_merek
                    ORDER BY m.nama_merek, b.ukuran
                ");
                while($ban = $all_ban_query->fetch_assoc()){
                    echo "<tr>
                            <td>{$ban['nama_merek']}</td>
                            <td>{$ban['ukuran']}</td>
                            <td>Rp " . number_format($ban['harga'],0,',','.') . "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>