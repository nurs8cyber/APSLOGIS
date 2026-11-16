SIM LOGISTIK RS KARTIKA HUSADA - VERSI XAMPP (HTML + CSS + JS + PHP + MySQL)

CARA PASANG
1. Salin folder "sim_logistik" ke C:\xampp\htdocs\
2. Buka XAMPP Control Panel -> Start "Apache" dan "MySQL".
3. Buka http://localhost/sim_logistik/install.php  (membuat database, tabel, data demo; sekali saja)
4. Buka http://localhost/sim_logistik/ lalu login.

AKUN DEMO (segera ganti lewat menu Manajemen User)
  admin / 123 (Oprator)   manager / 123 (Manager)
  kepalaunit / 123 (Kepala Unit)   direktur / 123 (Direktur)

LOGO
Simpan logo RS Anda sebagai:  assets/img/logo.png
(Link Facebook/fbcdn kedaluwarsa dan memblokir tampilan dari situs lain, itu penyebab logo rusak.
 Selama logo.png belum ada, otomatis tampil logo cadangan assets/img/logo.svg.)

AKSES DARI KOMPUTER LAIN (LAN)
Buka http://IP-KOMPUTER-SERVER/sim_logistik/  (izinkan Apache di firewall Windows).

CATATAN
- Semua pustaka (Tailwind, Chart.js, Font Awesome, dll.) sudah lokal: tidak perlu internet.
- Sandi disimpan ter-hash (bcrypt); foto & iklan disimpan di folder uploads/.
- Kredensial database ada di api/config.php (default XAMPP: root, tanpa sandi).
- Backup: phpMyAdmin -> Export database sim_logistik + salin folder uploads/.
- Setelah instalasi, file install.php terkunci otomatis oleh file ".installed".

DEPLOY KE GITHUB PAGES (MODE DEMO)
GitHub Pages tidak menjalankan PHP/MySQL. Di sana aplikasi otomatis memakai MODE DEMO
(data tersimpan di browser, semua fitur tetap berjalan; akun sama: admin/manager/kepalaunit/direktur, sandi 123).
1. Buat repositori di GitHub, unggah SELURUH isi folder sim_logistik (termasuk folder .github).
2. Settings -> Pages -> Source: "GitHub Actions".
3. Tunggu tab Actions selesai (hijau). Alamat: https://USERNAME.github.io/NAMA-REPO/
Mode demo juga bisa dicoba lokal: buka index.html?demo=1
