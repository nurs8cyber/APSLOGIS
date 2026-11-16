<?php
// Installer sekali jalan: membuat database, tabel, dan data demo.
// Buka: http://localhost/sim_logistik/install.php   (setelah itu file ini dikunci otomatis)
require __DIR__ . '/api/config.php';
header('Content-Type: text/html; charset=utf-8');
$lock = __DIR__ . '/.installed';
$log = [];
$ok = false;

function page($title, $lines, $ok) {
    $c = $ok ? '#16a34a' : '#dc2626';
    echo '<!doctype html><meta charset="utf-8"><title>Install SIM Logistik</title>'
       . '<body style="font-family:system-ui,sans-serif;max-width:640px;margin:40px auto;padding:0 16px;color:#1f2937">'
       . '<h2 style="color:' . $c . '">' . htmlspecialchars($title) . '</h2><ul>';
    foreach ($lines as $l) echo '<li>' . htmlspecialchars($l) . '</li>';
    echo '</ul>' . ($ok ? '<p><a href="index.html" style="background:#114B3E;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none">Buka Aplikasi</a></p>' : '') . '</body>';
    exit;
}

if (file_exists($lock)) page('Sudah terpasang', ['Instalasi sudah pernah dijalankan. Hapus file ".installed" bila ingin memasang ulang (data lama tidak dihapus).'], false);

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $log[] = 'Terhubung ke MySQL.';

    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    $sql = str_replace('sim_logistik', DB_NAME, preg_replace('/^--.*$/m', '', $sql));
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) $pdo->exec($stmt);
    $log[] = 'Database "' . DB_NAME . '" dan semua tabel siap.';

    $pdo->exec('USE `' . DB_NAME . '`');
    if ((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
        $now = gmdate('Y-m-d H:i:s');
        $ins = $pdo->prepare('INSERT INTO users (id, role, name, pass_hash, unit, phone, approved, created_at) VALUES (?,?,?,?,?,?,1,?)');
        foreach ([
            ['U-ADMIN1', 'OPRATOR', 'admin', '123', 'Pusat Logistik', '081'],
            ['U-MGR001', 'MANAGER', 'manager', '123', 'Manajemen', '082'],
            ['U-KU0001', 'KEPALA UNIT', 'kepalaunit', '123', 'UGD', '083'],
            ['U-DIR001', 'DIREKTUR', 'direktur', '123', 'Direksi', '084'],
        ] as $u) $ins->execute([$u[0], $u[1], $u[2], password_hash($u[3], PASSWORD_DEFAULT), $u[4], $u[5], $now]);

        $cat = $pdo->prepare('INSERT INTO catalog (id, code, name, price, stock) VALUES (?,?,?,?,?)');
        foreach ([
            ['CAT-000001', 'ATK015', 'PULPEN HITAM', 2000, 8],
            ['CAT-000002', 'ATK016', 'PULPEN BIRU', 2000, 19],
            ['CAT-000003', 'MED001', 'PLASTIK KUNING 90X120', 15000, 50],
            ['CAT-000004', 'ATK041', 'BUSINESS FILE', 5000, 0],
        ] as $c) $cat->execute($c);

        $pdo->prepare("REPLACE INTO settings (k, v) VALUES ('quotes', ?)")
            ->execute([json_encode(['text' => 'Pelayanan yang baik dimulai dari pengelolaan yang baik.', 'author' => 'Manajemen RS'], JSON_UNESCAPED_UNICODE)]);
        $log[] = 'Data demo dibuat: akun admin, manager, kepalaunit, direktur (semua sandi 123) + 4 barang katalog.';
    } else {
        $log[] = 'Tabel users sudah berisi data, data demo dilewati.';
    }

    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0775, true);
    $log[] = is_writable(UPLOAD_DIR) ? 'Folder uploads dapat ditulis.' : 'PERINGATAN: folder uploads tidak dapat ditulis. Foto/iklan tidak akan tersimpan.';

    file_put_contents($lock, gmdate('c'));
    $log[] = 'Instalasi selesai. Segera ganti sandi akun demo lewat menu Manajemen User.';
    $ok = true;
} catch (Throwable $e) {
    $log[] = 'GAGAL: ' . $e->getMessage();
    $log[] = 'Pastikan MySQL di XAMPP Control Panel berstatus Running dan kredensial di api/config.php benar.';
}
page($ok ? 'Instalasi berhasil' : 'Instalasi gagal', $log, $ok);
