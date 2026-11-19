// Tombol "Salin": salin teks dari atribut data-copy ke clipboard
document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
        const text = btn.dataset.copy;

        try {
            await navigator.clipboard.writeText(text);
        } catch (err) {
            // Cadangan untuk browser lama / koneksi non-HTTPS
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            temp.remove();
        }

        const original = btn.textContent;
        btn.textContent = 'Tersalin';
        setTimeout(() => (btn.textContent = original), 1500);
    });
});
