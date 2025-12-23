<!-- components/navbar.php -->
<nav class="navbar">
    <div class="container">
        <a href="index.php?page=home" class="nav-logo">
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTQgMTJoLTR2NGg0djR6bTItMTJoLTR2NGg0djR6bS0yLTEyaC00djRoNHY0em0yLTBoLTR2NGg0djR6bS0yLThoLTR2NGg0djR6bTItMGgtNHY0aDR2NHptMC0xNmgtNHY0aDR2NHptMTYgMTZoLTR2NGg0djR6bTItMTZoLTR2NGg0djR6bS0yIDhoLTR2NGg0djR6bTItMGgtNHY0aDR2NHoiLz48L3N2Zz4=" alt="Logo" class="logo-icon"> DSS Ban
        </a>
        <ul class="nav-menu">
            <li class="nav-item"><a href="index.php?page=home" class="nav-link <?= (isset($_GET['page']) && $_GET['page']=='home') || !isset($_GET['page']) ? 'active' : '' ?>">Beranda</a></li>
            <li class="nav-item"><a href="index.php?page=data-ban" class="nav-link <?= (isset($_GET['page']) && $_GET['page']=='data-ban') ? 'active' : '' ?>">Data Ban</a></li>
            <li class="nav-item"><a href="index.php?page=hitung" class="nav-link <?= (isset($_GET['page']) && $_GET['page']=='hitung') ? 'active' : '' ?>">Hitung Rekomendasi</a></li>
            <?php if(isset($_SESSION['hasil_akhir'])): ?>
                <li class="nav-item"><a href="index.php?page=hasil" class="nav-link <?= (isset($_GET['page']) && $_GET['page']=='hasil') ? 'active' : '' ?>">Hasil</a></li>
                <li class="nav-item"><a href="index.php?page=rekomendasi" class="nav-link <?= (isset($_GET['page']) && $_GET['page']=='rekomendasi') ? 'active' : '' ?>">Rekomendasi Terbaik</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>