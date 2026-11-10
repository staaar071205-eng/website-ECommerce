<?php
$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$rp = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
?>

<h2>Edit Penjualan</h2>

<form action="<?php echo URL; ?>/penjualan/update" method="post">
    <input type="hidden" name="penjualan_id" value="<?php echo $e($data['row']['penjualan_id']); ?>">
    <table class="table table-hover">
        <tr>
            <td>PRODUK</td>
            <td>
                <select name="penjualan_id_produk" class="form-select" required>
                    <?php foreach ($data['listproduk'] as $p) { ?>
                        <?php $dipilih = $p['produk_id'] == $data['row']['penjualan_id_produk']; ?>
                        <option value="<?php echo $e($p['produk_id']); ?>" <?php echo $dipilih ? 'selected' : ''; ?> <?php echo (!$dipilih && $p['produk_stock'] <= 0) ? 'disabled' : ''; ?>>
                            <?php echo $e($p['produk_kode']); ?> - <?php echo $e($p['produk_nama']); ?>
                            (<?php echo $rp($p['produk_hrg']); ?>, stok: <?php echo $e($p['produk_stock']); ?>)
                        </option>
                    <?php } ?>
                </select>
                <small class="text-muted">Harga transaksi saat ini: <?php echo $rp($data['row']['penjualan_hrg']); ?> per unit.</small>
            </td>
        </tr>
        <tr>
            <td>TANGGAL</td>
            <td><input type="date" name="penjualan_tgl" value="<?php echo $e($data['row']['penjualan_tgl']); ?>" class="form-control" required></td>
        </tr>
        <tr>
            <td>PEMBELI</td>
            <td><input type="text" name="penjualan_pembeli" maxlength="100" value="<?php echo $e($data['row']['penjualan_pembeli']); ?>" class="form-control" required></td>
        </tr>
        <tr>
            <td>QTY</td>
            <td><input type="number" name="penjualan_qty" min="1" value="<?php echo $e($data['row']['penjualan_qty']); ?>" class="form-control" required></td>
        </tr>
        <tr>
            <td>KETERANGAN</td>
            <td><textarea name="penjualan_keterangan" class="form-control" cols="30" rows="5"><?php echo $e($data['row']['penjualan_keterangan']); ?></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="btn_update" value="UPDATE" class="btn btn-primary">
                <a href="<?php echo URL; ?>/penjualan" class="btn btn-secondary">BATAL</a>
            </td>
        </tr>
    </table>
</form>
