<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$error = '';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// auto columns
$cols = [];
$res = @mysqli_query($conn, "SHOW COLUMNS FROM penjualan");
if ($res) while ($r = mysqli_fetch_assoc($res)) $cols[] = $r['Field'];
foreach (['nama_pelanggan'=>'VARCHAR(100) DEFAULT NULL','diskon'=>"DECIMAL(14,2) NOT NULL DEFAULT 0",'bayar'=>"DECIMAL(14,2) NOT NULL DEFAULT 0",'kembalian'=>"DECIMAL(14,2) NOT NULL DEFAULT 0"] as $c=>$def) {
    if (!in_array($c, $cols)) @mysqli_query($conn, "ALTER TABLE penjualan ADD COLUMN $c $def");
}

if (isset($_GET['cart_add'])) {
    $pid = intval($_GET['cart_add']); $qty = max(1, intval($_GET['qty'] ?? 1));
    $stmt = mysqli_prepare($conn, "SELECT id,nama_produk,harga,stok,gambar,kategori FROM produk WHERE id=?");
    mysqli_stmt_bind_param($stmt,'i',$pid); mysqli_stmt_execute($stmt);
    $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if ($p && $p['stok']>0) {
        $ex = $_SESSION['cart'][$pid]['qty'] ?? 0;
        $nq = min($ex+$qty, (int)$p['stok']);
        $_SESSION['cart'][$pid] = ['id'=>(int)$p['id'],'nama'=>$p['nama_produk'],'harga'=>(float)$p['harga'],'stok'=>(int)$p['stok'],'gambar'=>$p['gambar'],'kategori'=>$p['kategori'],'qty'=>$nq];
    }
    header('Location: tambah_penjualan.php'); exit;
}
if (isset($_GET['cart_update'])) {
    $pid=intval($_GET['cart_update']); $qty=intval($_GET['qty']??0);
    if (isset($_SESSION['cart'][$pid])) {
        if ($qty<=0) unset($_SESSION['cart'][$pid]);
        else $_SESSION['cart'][$pid]['qty']=min($qty,$_SESSION['cart'][$pid]['stok']);
    }
    header('Location: tambah_penjualan.php'); exit;
}
if (isset($_GET['cart_remove'])) { unset($_SESSION['cart'][intval($_GET['cart_remove'])]); header('Location: tambah_penjualan.php'); exit; }
if (isset($_GET['cart_clear'])) { $_SESSION['cart']=[]; header('Location: tambah_penjualan.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['proses_bayar'])) {
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) $error='Keranjang kosong!';
    else {
        $nama_pelanggan=trim($_POST['nama_pelanggan']??'');
        $keterangan=trim($_POST['keterangan']??'');
        $metode=trim($_POST['metode_pembayaran']??'Tunai');
        $diskon=max(0,floatval($_POST['diskon']??0));
        $bayar=max(0,floatval($_POST['bayar']??0));
        $bukti_path=null;
        if (!in_array($metode,['Tunai','Transfer Bank','QRIS','E-Wallet','Kartu Debit/Kredit'])) $metode='Tunai';
        $subtotal=0; foreach($cart as $it) $subtotal+=$it['harga']*$it['qty'];
        if ($diskon>$subtotal) $diskon=$subtotal;
        $total=$subtotal-$diskon;
        foreach ($cart as $it) {
            $q=mysqli_query($conn,"SELECT stok FROM produk WHERE id=".intval($it['id']));
            $row=mysqli_fetch_assoc($q);
            if (!$row || $row['stok']<$it['qty']) { $error='Stok "'.$it['nama'].'" tidak cukup.'; break; }
        }
        if (!$error) {
            if ($metode==='Tunai') {
                if ($bayar<$total) $error='Uang bayar kurang!';
            } else {
                if (empty($_FILES['bukti_pembayaran']['name'])) $error='Upload bukti wajib untuk '.$metode;
                else {
                    $file=$_FILES['bukti_pembayaran']; $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
                    if (!in_array($ext,['jpg','jpeg','png','webp','pdf'])) $error='Format file tidak didukung';
                    elseif ($file['size']>3*1024*1024) $error='Maks 3MB';
                    elseif ($file['error']!==UPLOAD_ERR_OK) $error='Gagal upload';
                    else {
                        $dir=__DIR__.'/uploads/'; if(!is_dir($dir)) mkdir($dir,0755,true);
                        $nn='bukti_'.time().'_'.bin2hex(random_bytes(3)).'.'.$ext;
                        if (move_uploaded_file($file['tmp_name'],$dir.$nn)) $bukti_path='uploads/'.$nn;
                        else $error='Gagal simpan file';
                    }
                }
                $bayar=$total;
            }
        }
        if (!$error) {
            $kembalian=max(0,$bayar-$total); $uid=currentUser()['id']; $status='lunas';
            mysqli_begin_transaction($conn);
            try {
                $stmt=mysqli_prepare($conn,"INSERT INTO penjualan (total,diskon,bayar,kembalian,keterangan,nama_pelanggan,metode_pembayaran,bukti_pembayaran,status,user_id) VALUES (?,?,?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt,'ddddsssssi',$total,$diskon,$bayar,$kembalian,$keterangan,$nama_pelanggan,$metode,$bukti_path,$status,$uid);
                mysqli_stmt_execute($stmt); $pid=mysqli_insert_id($conn); mysqli_stmt_close($stmt);
                foreach ($cart as $it) {
                    $sub=$it['harga']*$it['qty'];
                    $st=mysqli_prepare($conn,"INSERT INTO detail_penjualan (penjualan_id,produk_id,qty,harga_satuan,subtotal) VALUES (?,?,?,?,?)");
                    mysqli_stmt_bind_param($st,'iiidd',$pid,$it['id'],$it['qty'],$it['harga'],$sub);
                    mysqli_stmt_execute($st); mysqli_stmt_close($st);
                    mysqli_query($conn,"UPDATE produk SET stok=stok-".intval($it['qty'])." WHERE id=".intval($it['id']));
                }
                mysqli_commit($conn); $_SESSION['cart']=[];
                header('Location: detail_penjualan.php?id='.$pid.'&sukses=1'); exit;
            } catch(Exception $e) { mysqli_rollback($conn); $error='Gagal: '.$e->getMessage(); }
        }
    }
}

$cari=trim($_GET['q']??''); $katf=trim($_GET['kategori']??'');
$sql="SELECT id,nama_produk,harga,stok,gambar,kategori FROM produk WHERE stok>0";
if ($cari!=='') { $c=mysqli_real_escape_string($conn,$cari); $sql.=" AND (nama_produk LIKE '%$c%' OR kategori LIKE '%$c%')"; }
if ($katf!=='') { $k=mysqli_real_escape_string($conn,$katf); $sql.=" AND kategori='$k'"; }
$sql.=" ORDER BY nama_produk";
$produk_list=mysqli_query($conn,$sql);
$kategori_list=[];
$kq=mysqli_query($conn,"SELECT DISTINCT kategori FROM produk WHERE kategori IS NOT NULL AND kategori!='' ORDER BY kategori");
if ($kq) while($r=mysqli_fetch_assoc($kq)) $kategori_list[]=$r['kategori'];

$cart=$_SESSION['cart']??[]; $cart_count=0; $cart_subtotal=0;
foreach($cart as $it){ $cart_count+=$it['qty']; $cart_subtotal+=$it['harga']*$it['qty']; }
$nama_toko=shopName();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Kasir - <?= htmlspecialchars($nama_toko); ?></title>
<link rel="stylesheet" href="css/style.css">
<style>
.kasir-layout{display:grid;grid-template-columns:1fr 340px;gap:18px;align-items:start}
.search-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
.search-bar input,.search-bar select{padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.95rem}
.search-bar input{flex:1;min-width:140px}
.produk-kasir-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px}
.produk-kasir-item{background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;overflow:hidden;cursor:pointer;transition:.2s;text-decoration:none;color:inherit;display:block}
.produk-kasir-item:hover{border-color:#7c3aed;box-shadow:0 4px 14px rgba(124,58,237,.15)}
.produk-kasir-item img,.produk-kasir-item .no-img{width:100%;height:95px;object-fit:cover;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);display:flex;align-items:center;justify-content:center;font-size:1.8rem}
.produk-kasir-item .body{padding:8px}
.produk-kasir-item h4{font-size:.78rem;margin:0 0 4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.produk-kasir-item .harga{color:#7c3aed;font-weight:700;font-size:.85rem}
.produk-kasir-item .stok{font-size:.7rem;color:#64748b}
.cart-panel{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(30,27,75,.1);position:sticky;top:76px;max-height:calc(100vh - 90px);display:flex;flex-direction:column;overflow:hidden}
.cart-header{padding:14px 16px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#1e1b4b,#4c1d95);color:#fff}
.cart-header h2{margin:0;font-size:1rem}
.cart-badge{background:#fbbf24;color:#1e1b4b;font-size:.72rem;font-weight:700;padding:2px 8px;border-radius:20px}
.cart-body{flex:1;overflow-y:auto;padding:10px 12px}
.cart-item{display:flex;gap:8px;align-items:flex-start;padding:10px 0;border-bottom:1px solid #f1f5f9}
.cart-item .info{flex:1;min-width:0}
.cart-item .info strong{font-size:.82rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cart-qty{display:flex;align-items:center;gap:6px;margin-top:6px}
.cart-qty a{width:30px;height:30px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#1e293b;font-weight:700}
.cart-footer{border-top:1px solid #e2e8f0;padding:12px 14px 16px;background:#fafafa}
.cart-row{display:flex;justify-content:space-between;margin-bottom:4px;font-size:.88rem}
.cart-row.total{font-size:1.1rem;font-weight:700;margin:8px 0;padding-top:8px;border-top:1px dashed #cbd5e1}
.metode-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin:8px 0}
.metode-option input{display:none}
.metode-option label{display:block;text-align:center;padding:10px 4px;border:2px solid #e2e8f0;border-radius:10px;font-size:.75rem;font-weight:600;cursor:pointer;color:#475569}
.metode-option input:checked+label{border-color:#7c3aed;background:#f5f3ff;color:#5b21b6}
.empty-cart{text-align:center;padding:36px 12px;color:#94a3b8}
#buktiWrap{display:none;margin-top:8px}
#kembalianBox{background:#ecfdf5;color:#065f46;padding:10px;border-radius:10px;font-weight:700;margin-top:8px;display:none}
@media(max-width:960px){.kasir-layout{grid-template-columns:1fr}.cart-panel{position:relative;top:auto;max-height:none}}
</style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container">
<header class="page-header">
<div><h1>🛒 Kasir</h1><p>Pilih produk → atur jumlah → bayar</p></div>
<?php if($cart_count>0): ?><a href="tambah_penjualan.php?cart_clear=1" class="btn btn-secondary" onclick="return confirm('Kosongkan keranjang?')">Hapus Keranjang</a><?php endif; ?>
</header>
<?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="kasir-layout">
<div>
<form class="search-bar" method="GET"><input type="text" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($cari); ?>">
<select name="kategori"><option value="">Semua kategori</option>
<?php foreach($kategori_list as $kat): ?><option value="<?= htmlspecialchars($kat); ?>" <?= $katf===$kat?'selected':''; ?>><?= htmlspecialchars($kat); ?></option><?php endforeach; ?>
</select><button type="submit" class="btn btn-primary btn-sm">Cari</button></form>
<div class="produk-kasir-grid">
<?php if($produk_list && mysqli_num_rows($produk_list)>0): while($p=mysqli_fetch_assoc($produk_list)): ?>
<div class="produk-kasir-item" role="button" tabindex="0"
 data-id="<?= (int)$p['id']; ?>" data-nama="<?= htmlspecialchars($p['nama_produk'],ENT_QUOTES); ?>"
 data-harga="<?= (float)$p['harga']; ?>" data-stok="<?= (int)$p['stok']; ?>"
 data-gambar="<?= htmlspecialchars($p['gambar']??'',ENT_QUOTES); ?>"
 onclick="bukaModalProduk(this)">
<?php if(!empty($p['gambar'])): ?><img src="<?= htmlspecialchars($p['gambar']); ?>" alt="" onerror="this.outerHTML='<div class=no-img>📦</div>'">
<?php else: ?><div class="no-img">📦</div><?php endif; ?>
<div class="body"><h4><?= htmlspecialchars($p['nama_produk']); ?></h4>
<div class="harga"><?= formatRupiah($p['harga']); ?></div>
<div class="stok">Stok: <?= (int)$p['stok']; ?></div></div></div>
<?php endwhile; else: ?><p style="color:#64748b;grid-column:1/-1;">Produk tidak ditemukan.</p><?php endif; ?>
</div></div>

<aside class="cart-panel" id="cartPanel">
<div class="cart-header"><h2>Keranjang</h2><span class="cart-badge"><?= $cart_count; ?> item</span></div>
<div class="cart-body">
<?php if(empty($cart)): ?><div class="empty-cart"><div style="font-size:2rem">🛍️</div><p>Keranjang kosong</p></div>
<?php else: foreach($cart as $item): ?>
<div class="cart-item"><div class="info">
<strong><?= htmlspecialchars($item['nama']); ?></strong>
<div style="font-size:.72rem;color:#64748b"><?= formatRupiah($item['harga']); ?></div>
<div class="cart-qty">
<a href="tambah_penjualan.php?cart_update=<?= $item['id']; ?>&qty=<?= $item['qty']-1; ?>">−</a>
<span><?= $item['qty']; ?></span>
<a href="tambah_penjualan.php?cart_update=<?= $item['id']; ?>&qty=<?= $item['qty']+1; ?>">+</a>
<a href="tambah_penjualan.php?cart_remove=<?= $item['id']; ?>" style="color:#ef4444;margin-left:4px">✕</a>
</div></div>
<div style="font-weight:700;color:#7c3aed;font-size:.85rem;white-space:nowrap"><?= formatRupiah($item['harga']*$item['qty']); ?></div>
</div>
<?php endforeach; endif; ?>
</div>
<?php if(!empty($cart)): ?>
<div class="cart-footer">
<form method="POST" enctype="multipart/form-data">
<div class="cart-row"><span>Subtotal</span><span><?= formatRupiah($cart_subtotal); ?></span></div>
<div class="form-group" style="margin:6px 0"><label style="font-size:.75rem">Diskon (Rp)</label>
<input type="number" name="diskon" id="inputDiskon" value="0" min="0" step="1000" style="width:100%;padding:8px;border-radius:8px;border:1.5px solid #e2e8f0" oninput="updateTotal()"></div>
<div class="cart-row total"><span>Total</span><span id="totalVal"><?= formatRupiah($cart_subtotal); ?></span></div>
<div class="form-group"><label style="font-size:.75rem">Nama Pelanggan</label>
<input type="text" name="nama_pelanggan" placeholder="Opsional" style="width:100%;padding:8px;border-radius:8px;border:1.5px solid #e2e8f0"></div>
<div class="form-group"><label style="font-size:.75rem">Metode Bayar</label>
<div class="metode-grid">
<?php foreach(['Tunai'=>'💵 Tunai','QRIS'=>'📱 QRIS','Transfer Bank'=>'🏦 Transfer','E-Wallet'=>'💳 E-Wallet'] as $v=>$l): ?>
<div class="metode-option"><input type="radio" name="metode_pembayaran" id="m<?= md5($v); ?>" value="<?= $v; ?>" <?= $v==='Tunai'?'checked':''; ?> onchange="toggleBayar()">
<label for="m<?= md5($v); ?>"><?= $l; ?></label></div>
<?php endforeach; ?>
</div></div>
<div class="form-group" id="bayarTunaiWrap"><label style="font-size:.75rem">Uang Diterima</label>
<input type="number" name="bayar" id="inputBayar" value="<?= (int)$cart_subtotal; ?>" min="0" step="1000" style="width:100%;padding:8px;border-radius:8px;border:1.5px solid #e2e8f0" oninput="hitungKembalian()">
<div id="kembalianBox">Kembalian: <span id="kembalianVal">Rp 0</span></div></div>
<div id="buktiWrap"><label style="font-size:.75rem">Bukti Pembayaran</label>
<input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.webp,.pdf" style="width:100%;font-size:.8rem"></div>
<div class="form-group" style="margin-top:6px"><label style="font-size:.75rem">Catatan</label>
<input type="text" name="keterangan" placeholder="Opsional" style="width:100%;padding:8px;border-radius:8px;border:1.5px solid #e2e8f0"></div>
<button type="submit" name="proses_bayar" value="1" class="btn btn-success btn-block" style="margin-top:10px;padding:14px;font-size:1rem">✓ Proses Bayar</button>
</form></div>
<?php endif; ?>
</aside>
</div>
</div>

<?php if($cart_count>0): ?>
<a href="#cartPanel" class="fab-cart" id="fabCart"><span>🛒 Keranjang</span><span class="fab-count"><?= $cart_count; ?></span></a>
<?php endif; ?>

<div id="modalProduk" class="modal-qty hidden" onclick="if(event.target===this)tutupModalProduk()">
<div class="modal-qty-box">
<button type="button" class="modal-close" onclick="tutupModalProduk()">✕</button>
<div class="modal-qty-img"><img id="mqGambar" src="" alt=""></div>
<h3 id="mqNama" style="margin:0 0 4px;font-size:1.05rem">Produk</h3>
<p id="mqHarga" style="color:#7c3aed;font-weight:700;margin:0 0 4px">Rp 0</p>
<p style="font-size:.85rem;color:#64748b;margin-bottom:8px">Stok: <strong id="mqStok">0</strong></p>
<div class="mq-qty-row">
<button type="button" class="mq-btn" onclick="ubahQty(-1)">−</button>
<input type="number" id="mqQty" value="1" min="1" onchange="validasiQty()">
<button type="button" class="mq-btn" onclick="ubahQty(1)">+</button>
</div>
<p style="margin-bottom:14px">Subtotal: <strong id="mqSubtotal">Rp 0</strong></p>
<button type="button" class="btn btn-primary btn-block" style="padding:14px;font-size:1rem" onclick="konfirmasiTambahKeranjang()">+ Masukkan Keranjang</button>
</div></div>

<script src="js/script.js"></script>
<script>
const subtotal=<?= (float)$cart_subtotal; ?>;
let mqData={id:0,harga:0,stok:0};
function formatRp(n){return 'Rp '+Math.max(0,Math.round(n)).toLocaleString('id-ID');}
function bukaModalProduk(el){
  mqData={id:+el.dataset.id,harga:+el.dataset.harga,stok:+el.dataset.stok};
  document.getElementById('mqNama').textContent=el.dataset.nama;
  document.getElementById('mqHarga').textContent=formatRp(mqData.harga);
  document.getElementById('mqStok').textContent=mqData.stok;
  document.getElementById('mqQty').value=1; document.getElementById('mqQty').max=mqData.stok;
  const img=document.getElementById('mqGambar');
  if(el.dataset.gambar){img.src=el.dataset.gambar;img.style.display='inline-block';}else img.style.display='none';
  updateMqSubtotal(); document.getElementById('modalProduk').classList.remove('hidden');
}
function tutupModalProduk(){document.getElementById('modalProduk').classList.add('hidden');}
function ubahQty(d){const i=document.getElementById('mqQty');let v=parseInt(i.value)||1;v=Math.min(mqData.stok,Math.max(1,v+d));i.value=v;updateMqSubtotal();}
function validasiQty(){const i=document.getElementById('mqQty');let v=parseInt(i.value)||1;v=Math.min(mqData.stok,Math.max(1,v));i.value=v;updateMqSubtotal();}
function updateMqSubtotal(){const q=parseInt(document.getElementById('mqQty').value)||1;document.getElementById('mqSubtotal').textContent=formatRp(mqData.harga*q);}
function konfirmasiTambahKeranjang(){const q=parseInt(document.getElementById('mqQty').value)||1;if(q<1||q>mqData.stok){alert('Jumlah tidak valid');return;}location.href='tambah_penjualan.php?cart_add='+mqData.id+'&qty='+q;}
function updateTotal(){const d=parseFloat(document.getElementById('inputDiskon')?.value||0)||0;const t=Math.max(0,subtotal-d);const el=document.getElementById('totalVal');if(el)el.textContent=formatRp(t);hitungKembalian();}
function hitungKembalian(){const d=parseFloat(document.getElementById('inputDiskon')?.value||0)||0;const t=Math.max(0,subtotal-d);const b=parseFloat(document.getElementById('inputBayar')?.value||0)||0;const box=document.getElementById('kembalianBox');const val=document.getElementById('kembalianVal');if(!box||!val)return;const k=b-t;if(k>=0&&document.querySelector('input[name=metode_pembayaran]:checked')?.value==='Tunai'){box.style.display='block';val.textContent=formatRp(k);}else box.style.display='none';}
function toggleBayar(){const m=document.querySelector('input[name=metode_pembayaran]:checked')?.value;const tunai=document.getElementById('bayarTunaiWrap');const bukti=document.getElementById('buktiWrap');if(m==='Tunai'){if(tunai)tunai.style.display='block';if(bukti)bukti.style.display='none';}else{if(tunai)tunai.style.display='none';if(bukti)bukti.style.display='block';}hitungKembalian();}
toggleBayar();updateTotal();
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
