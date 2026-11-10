<?php $e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>

<h2>Detail Produk</h2>

<table class="table table-bordered">
    <tr>
        <td><strong>Kategori</strong></td>
        <td><?php echo $e($data['row']['ktgr']); ?></td>
    </tr>
    <tr>
        <td><strong>Kode Produk</strong></td>
        <td><?php echo $e($data['row']['produk_kode']); ?></td>
    </tr>
    <tr>
        <td><strong>Nama Produk</strong></td>
        <td><?php echo $e($data['row']['produk_nama']); ?></td>
    </tr>
    <tr>
        <td><strong>Harga Produk</strong></td>
        <td>Rp <?php echo number_format((float) $data['row']['produk_hrg'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td><strong>Stok Produk</strong></td>
        <td><?php echo $e($data['row']['produk_stock']); ?></td>
    </tr>
    <tr>
        <td><strong>Keterangan</strong></td>
        <td><?php echo nl2br($e($data['row']['produk_keterangan'])); ?></td>
    </tr>
    <tr>
        <td><strong>Foto Produk</strong></td>
        <td>
            <?php if (!empty($data['row']['produk_photo'])): ?>
                <img src="<?php echo AST; ?>/img/produk/<?php echo $e($data['row']['produk_photo']); ?>" alt="Foto produk" width="200">
            <?php else: ?>
                Tidak ada foto
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td><strong>Dibuat</strong></td>
        <td><?php echo $e($data['row']['created_at']); ?></td>
    </tr>
    <tr>
        <td><strong>Terakhir diubah</strong></td>
        <td><?php echo $e($data['row']['updated_at'] ?? '-'); ?></td>
    </tr>
</table>

<p>
    <a href="<?php echo URL; ?>/produk" class="btn btn-primary">Kembali</a>
    <a href="<?php echo URL; ?>/produk/edit/<?php echo $e($data['row']['produk_id']); ?>" class="btn btn-warning">Edit</a>
</p>