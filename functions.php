<?php
// functions.php — fungsi yang dipakai bersama oleh index.php dan go.php

const DATA_FILE = __DIR__ . '/data/links.json';

// Baca semua link dari file JSON. Kalau file belum ada / rusak, kembalikan array kosong.
function loadLinks(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $data = json_decode(file_get_contents(DATA_FILE), true);
    return is_array($data) ? $data : [];
}

// Simpan semua link ke file JSON. LOCK_EX mencegah dua proses menulis bersamaan.
function saveLinks(array $links): bool
{
    $json = json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(DATA_FILE, $json, LOCK_EX) !== false;
}

// Buat kode acak 6 karakter. Huruf yang mirip (l, I, O, 0, 1) sengaja dibuang.
function generateCode(array $links, int $length = 6): string
{
    $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
    } while (isset($links[$code])); // ulangi kalau kode sudah dipakai
    return $code;
}

// Rapikan dan validasi URL. Kembalikan null kalau tidak valid.
function normalizeUrl(string $input): ?string
{
    $url = trim($input);
    if ($url === '' || strlen($url) > 2000) {
        return null;
    }
    // Pengguna sering lupa "https://", jadi tambahkan otomatis.
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }
    // Domain harus mengandung titik (contoh.com), supaya "https://abc" ditolak.
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host || strpos($host, '.') === false) {
        return null;
    }
    return $url;
}

// Alamat dasar situs ini, mis. https://domainku.com/folder
function baseUrl(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return $scheme . '://' . $host . $dir;
}

// Singkatan htmlspecialchars. WAJIB dipakai setiap menampilkan data dari pengguna.
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
