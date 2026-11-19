# Link Shortener Mini

Pemendek link sederhana dengan HTML, CSS, JavaScript, dan PHP. Data disimpan di file JSON, jadi tidak perlu database.

## Fitur
- Validasi URL (otomatis menambah `https://` jika lupa)
- Kode pendek acak 6 karakter, dijamin tidak kembar
- Redirect lewat `go.php` dengan penghitung klik
- Tombol salin ke clipboard
- Daftar 5 link terbaru
- Responsif dan aman dari XSS (`htmlspecialchars`)

## Menjalankan di komputer
Pastikan sudah terinstall PHP 7.4 atau yang lebih baru.
    php -S localhost:8000


## Struktur
| File | Fungsi |
|---|---|
| `index.php` | Form, hasil, daftar link |
| `go.php` | Membaca kode lalu redirect ke URL asli |
| `functions.php` | Baca/tulis JSON, buat kode, validasi URL |
| `script.js` | Tombol salin |
| `style.css` | Tampilan |
| `data/links.json` | Penyimpanan data |


## Keterbatasan
- Penyimpanan JSON cocok untuk demo, bukan trafik besar. Untuk produksi, ganti ke MySQL.
- Daftar "Link terbaru" tampil ke semua pengunjung. Hapus bagian itu jika link harus privat.
- Perlindungan folder `data/` lewat `.htaccess` hanya berlaku di Apache.

## Ide pengembangan
- Alias kustom (`?c=promo2026`)
- Tanggal kedaluwarsa link
