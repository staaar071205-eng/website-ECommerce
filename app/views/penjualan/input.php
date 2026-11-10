<?php
$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$rp = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
?>

<h2>Input Penjualan</h2>

<?php if (empty($data['listproduk'])) { ?>
    <div class="alert alert-warning">
        Belum ada produk. Silakan <a href="<?php echo URL; ?>/produk/input">input produk</a> terlebih dahulu.
    </div>
<?php } else { ?>
<form action="<?php echo URL; ?>/penjualan/simpan" method="post">
    <table class="table table-hover">
        <tr>
            <td>PRODUK</td>
            <td>
                <select name="penjualan_id_produk" class="form-select" required>
                    <option value="">-- Pilih produk --</option>
                    <?php foreach ($data['listproduk'] as $p) { ?>
                        <option value="<?php echo $e($p['produk_id']); ?>" <?php echo $p['produk_stock'] <= 0 ? 'disabled' : ''; ?>>
                            <?php echo $e($p['produk_kode']); ?> - <?php echo $e($p['produk_nama']); ?>
                            (<?php echo $rp($p['produk_hrg']); ?>, stok: <?php echo $e($p['produk_stock']); ?>)
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>TANGGAL</td>
            <td><input type="date" name="penjualan_tgl" value="<?php echo date('Y-m-d'); ?>" class="form-control" required></td>
        </tr>
        <tr>
            <td>PEMBELI</td>
            <td><input type="text" name="penjualan_pembeli" maxlength="100" class="form-control" required></td>
        </tr>
        <tr>
            <td>QTY</td>
            <td><input type="number" name="penjualan_qty" min="1" value="1" class="form-control" required></td>
        </tr>
        <tr>
            <td>KETERANGAN</td>
            <td><textarea name="penjualan_keterangan" class="form-control" cols="30" rows="5"></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="btn_simpan" value="SIMPAN" class="btn btn-primary">
                <a href="<?php echo URL; ?>/penjualan" class="btn btn-secondary">BATAL</a>
            </td>
        </tr>
    </table>
</form>
<?php } ?>
