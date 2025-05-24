<?php
/**
 * Footer untuk halaman user/guest
 */
?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Tentang Kami</h5>
                    <p><?= substr($settings['deskripsi'], 0, 200) . (strlen($settings['deskripsi']) > 200 ? '...' : '') ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i> <?= $settings['alamat'] ?></li>
                        <li><i class="fas fa-phone me-2"></i> <?= $settings['no_telepon'] ?></li>
                        <li><i class="fas fa-envelope me-2"></i> <?= $settings['email'] ?></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= BASE_URL ?>" class="text-white">Beranda</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/kegiatan.php" class="text-white">Kegiatan</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/pengumuman.php" class="text-white">Pengumuman</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/donasi.php" class="text-white">Donasi</a></li>
                        <li><a href="<?= BASE_URL ?>/pages/masukan.php" class="text-white">Masukan</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?= date('Y') ?> <?= $settings['nama_masjid'] ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init();
        
        // Get current date for jadwal shalat
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        
        // Fetch jadwal shalat for today
        fetch(`<?= BASE_URL ?>/api/jadwal_shalat.php?tanggal=${formattedDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const jadwal = data.data;
                    document.getElementById('waktu-subuh').textContent = jadwal.subuh;
                    document.getElementById('waktu-dzuhur').textContent = jadwal.dzuhur;
                    document.getElementById('waktu-ashar').textContent = jadwal.ashar;
                    document.getElementById('waktu-maghrib').textContent = jadwal.maghrib;
                    document.getElementById('waktu-isya').textContent = jadwal.isya;
                }
            })
            .catch(error => console.error('Error fetching jadwal shalat:', error));
    </script>
</body>
</html>
