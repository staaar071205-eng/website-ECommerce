<?php $e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>

<h2>Edit Produk</h2>

<form action="<?php echo URL; ?>/produk/update" method="post" enctype="multipart/form-data">
    <input type="hidden" name="produk_id" value="<?php echo $e($data['row']['produk_id']); ?>">
    <table>
        <tr>
            <td>Kategori</td>
            <td>
                <select name="produk_id_kat" required>
                    <?php foreach ($data['listkategori'] as $kat): ?>
                        <option value="<?php echo $e($kat['kat_id']); ?>" <?php echo $kat['kat_id'] == $data['row']['produk_id_kat'] ? 'selected' : ''; ?>>
                            <?php echo $e($kat['kat_nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Kode Produk</td>
            <td><input type="text" name="produk_kode" maxlength="50" value="<?php echo $e($data['row']['produk_kode']); ?>" required></td>
        </tr>
        <tr>
            <td>Nama Produk</td>
            <td><input type="text" name="produk_nama" maxlength="256" value="<?php echo $e($data['row']['produk_nama']); ?>" required></td>
        </tr>
        <tr>
            <td>Harga Produk</td>
            <td><input type="number" name="produk_hrg" min="0" value="<?php echo $e($data['row']['produk_hrg']); ?>" required></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td><textarea name="produk_keterangan" rows="4" cols="50"><?php echo $e($data['row']['produk_keterangan']); ?></textarea></td>
        </tr>
        <tr>
            <td>Stok Produk</td>
            <td><input type="number" name="produk_stock" min="0" value="<?php echo $e($data['row']['produk_stock']); ?>" required></td>
        </tr>
        <tr>
            <td>Foto Produk</td>
            <td>
                <?php if (!empty($data['row']['produk_photo'])): ?>
                    <img src="<?php echo AST; ?>/img/produk/<?php echo $e($data['row']['produk_photo']); ?>" alt="Foto produk" width="120"><br>
                    <small>Pilih file baru hanya jika ingin mengganti foto.</small><br>
                <?php endif; ?>
                <input type="file" name="produk_photo" accept="image/*">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit" name="btn_update" class="btn btn-primary">UPDATE</button>
            </td>
        </tr>
    </table>
</form>