<?php
// go.php — menerima kode pendek (go.php?c=abc123) lalu mengarahkan ke URL asli
require __DIR__ . '/functions.php';

$code = $_GET['c'] ?? '';

// Kode hanya boleh huruf/angka, 4-10 karakter.
if (is_string($code) && preg_match('/^[A-Za-z0-9]{4,10}$/', $code)) {
    $links = loadLinks();

    if (isset($links[$code])) {
        // Hitung klik
        $links[$code]['clicks'] = ($links[$code]['clicks'] ?? 0) + 1;
        saveLinks($links);

        // 302 = redirect sementara, supaya browser tidak menyimpan dan klik tetap terhitung
        header('Location: ' . $links[$code]['url'], true, 302);
        exit;
    }
}

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link tidak ditemukan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
    <h1>Link ini tidak ditemukan.</h1>
    <p class="lead">Kodenya mungkin salah ketik atau link sudah dihapus.</p>
    <a class="button" href="index.php">Buat link baru</a>
</main>
</body>
</html>
