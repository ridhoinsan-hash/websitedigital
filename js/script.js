/**
 * Digital Electronic Ridho - Script + Animations + Mobile Nav
 */

function konfirmasiHapus(nama) {
    return confirm('Apakah Anda yakin ingin menghapus "' + nama + '"?\nData yang dihapus tidak dapat dikembalikan!');
}

function konfirmasiHapusPenjualan(id) {
    return confirm('Hapus transaksi penjualan #' + id + '?\nStok produk akan dikembalikan.');
}

function konfirmasiHapusPengeluaran(id) {
    return confirm('Hapus data pengeluaran #' + id + '?');
}

document.addEventListener('DOMContentLoaded', function () {
    var navToggle = document.getElementById('navToggle');
    var navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            var open = navMenu.classList.toggle('open');
            navToggle.classList.toggle('active', open);
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        navMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('open');
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                navMenu.classList.remove('open');
                navToggle.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.45s, transform 0.45s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-12px)';
            setTimeout(function () { alert.remove(); }, 450);
        }, 4000);
    });

    function stagger(selector, baseClass, delayStep) {
        document.querySelectorAll(selector).forEach(function (el, i) {
            if (!el.classList.contains('slide-up') && !el.classList.contains('fade-scale')) {
                el.classList.add(baseClass || 'slide-up');
            }
            el.style.animationDelay = (i * (delayStep || 0.07)) + 's';
        });
    }

    stagger('.stat-card', 'slide-up', 0.07);
    stagger('.product-card', 'slide-up', 0.06);
    stagger('.content', 'slide-up', 0.08);
    stagger('tbody tr', 'slide-up', 0.04);

    var formProduk = document.getElementById('formProduk');
    if (formProduk) {
        formProduk.addEventListener('submit', function (e) {
            var nama = document.getElementById('nama_produk');
            var harga = document.getElementById('harga');
            var stok = document.getElementById('stok');
            if (nama && nama.value.trim() === '') {
                alert('Nama produk wajib diisi!');
                e.preventDefault();
                return false;
            }
            if (harga && parseFloat(harga.value) <= 0) {
                alert('Harga harus lebih dari 0!');
                e.preventDefault();
                return false;
            }
            if (stok && parseInt(stok.value) < 0) {
                alert('Stok tidak boleh negatif!');
                e.preventDefault();
                return false;
            }
        });
    }

    var qtyInput = document.getElementById('qty');
    var produkSelect = document.getElementById('produk_id');
    var subtotalDisplay = document.getElementById('subtotal_display');

    function updateSubtotal() {
        if (!produkSelect || !qtyInput || !subtotalDisplay) return;
        var opt = produkSelect.options[produkSelect.selectedIndex];
        var harga = parseFloat(opt.getAttribute('data-harga') || 0);
        var qty = parseInt(qtyInput.value) || 0;
        subtotalDisplay.textContent = 'Rp ' + (harga * qty).toLocaleString('id-ID');
    }

    if (produkSelect) produkSelect.addEventListener('change', updateSubtotal);
    if (qtyInput) qtyInput.addEventListener('input', updateSubtotal);
    updateSubtotal();

    var gambarInput = document.getElementById('gambar');
    var previewImg = document.getElementById('preview_gambar');
    if (gambarInput && previewImg) {
        gambarInput.addEventListener('input', function () {
            var url = this.value.trim();
            if (url) {
                previewImg.src = url;
                previewImg.style.display = 'block';
            } else {
                previewImg.style.display = 'none';
            }
        });
    }

    document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript"]):not([target="_blank"])').forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href.indexOf('logout') !== -1 || href.indexOf('hapus') !== -1) return;
        link.addEventListener('click', function (e) {
            if (e.metaKey || e.ctrlKey || e.shiftKey) return;
            document.body.style.transition = 'opacity 0.25s ease';
            document.body.style.opacity = '0.6';
        });
    });
});
