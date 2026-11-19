<?php
// index.php — halaman utama: form, hasil, dan daftar link terbaru
require __DIR__ . '/functions.php';

$error = '';
$links = loadLinks();

// 1. Proses form kalau dikirim lewat POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = normalizeUrl($_POST['url'] ?? '');

    if ($url === null) {
        $error = 'URL tidak valid. Contoh yang benar: https://contoh.com/halaman-panjang';
    } else {
        $code = generateCode($links);
        $links[$code] = [
            'url'     => $url,
            'created' => date('Y-m-d H:i'),
            'clicks'  => 0,
        ];

        if (saveLinks($links)) {
            // Post-Redirect-Get: cegah link dibuat dobel kalau halaman di-refresh
            header('Location: index.php?new=' . $code);
            exit;
        }
        $error = 'Gagal menyimpan. Pastikan folder data/ bisa ditulis.';
    }
}

// 2. Ambil link yang baru dibuat (kalau ada) untuk ditampilkan
$newCode = $_GET['new'] ?? '';
$newLink = (is_string($newCode) && preg_match('/^[A-Za-z0-9]+$/', $newCode) && isset($links[$newCode]))
    ? $links[$newCode]
    : null;

// 3. Ambil 5 link terbaru
$recent = array_slice(array_reverse($links, true), 0, 5, true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendekin — pemendek link</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">

    <h1>Link panjang jadi pendek.</h1>
    <p class="lead">Tempel alamat web apa saja, dapatkan link yang mudah dibagikan.</p>

    <form method="post" class="shorten-form" novalidate>
        <label for="url" class="sr-only">URL yang ingin dipendekkan</label>
        <input type="text" id="url" name="url" inputmode="url" autocomplete="off"
               placeholder="https://contoh.com/artikel/judul-yang-sangat-panjang"
               value="<?= e($_POST['url'] ?? '') ?>" required autofocus>
        <button type="submit" class="button">Pendekkan</button>
    </form>

    <?php if ($error): ?>
        <p class="error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if ($newLink): ?>
        <?php $short = baseUrl() . '/go.php?c=' . $newCode; ?>
        <section class="result" aria-live="polite">
            <p class="result-label">Link pendekmu siap</p>
            <a class="short-url" href="<?= e($short) ?>"><?= e($short) ?></a>
            <button type="button" class="button button-light" data-copy="<?= e($short) ?>">Salin link</button>
            <p class="original">Mengarah ke <?= e(mb_strimwidth($newLink['url'], 0, 80, '…')) ?></p>
        </section>
    <?php endif; ?>

    <section class="recent">
        <h2>Link terbaru</h2>
        <?php if (empty($recent)): ?>
            <p class="empty">Belum ada link. Tempel URL panjang di atas untuk membuat yang pertama.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($recent as $code => $item): ?>
                    <?php $short = baseUrl() . '/go.php?c=' . $code; ?>
                    <li>
                        <div class="row-main">
                            <a href="<?= e($short) ?>"><?= e((string) $code) ?></a>
                            <span class="row-url"><?= e(mb_strimwidth($item['url'], 0, 60, '…')) ?></span>
                        </div>
                        <span class="row-clicks"><?= (int) $item['clicks'] ?> klik</span>
                        <button type="button" class="copy-small" data-copy="<?= e($short) ?>">Salin</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <footer>
        <?= count($links) ?> link sudah dibuat
    </footer>
</main>
<script src="script.js"></script>
</body>
</html>
