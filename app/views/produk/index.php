<h2>Produk</h2>

<a href="<?php echo URL; ?>/produk/input" class="btn btn-primary mb-3">
    Input Produk
</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>NO</th>
            <th>KATEGORI</th>
            <th>KODE</th>
            <th>NAMA</th>
            <th>HARGA</th>
            <th>STOK</th>
            <th>DETAIL</th>
            <th>EDIT</th>
            <th>HAPUS</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($data['rows'] as $row) { ?>
            <tr>
                <td><?php echo $row['produk_id']; ?></td>
                <td><?php echo $row['ktgr']; ?></td>
                <td><?php echo $row['produk_kode']; ?></td>
                <td><?php echo $row['produk_nama']; ?></td>
                <td><?php echo $row['produk_hrg']; ?></td>
                <td><?php echo $row['produk_stock']; ?></td>
                <td>
                    <a href="<?php echo URL; ?>/produk/detail/<?php echo $row['produk_id']; ?>" class="btn btn-info btn-sm">Detail</a>
                </td>
                <td>
                    <a href="<?php echo URL; ?>/produk/edit/<?php echo $row['produk_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                </td>
                <td>
                    <form action="<?php echo URL; ?>/produk/hapus" method="post" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                        <input type="hidden" name="produk_id" value="<?php echo $row['produk_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>