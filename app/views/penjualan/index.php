<?php
$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$rp = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
$no = 1;
$grand = 0;
?>

<h2>Penjualan</h2>

<a href="<?php echo URL; ?>/penjualan/input" class="btn btn-primary mb-3">Input Penjualan</a>

<table class="table table-bordered">
      <tr>
            <th>NO</th>
            <th>TANGGAL</th>
            <th>PEMBELI</th>
            <th>PRODUK</th>
            <th>KATEGORI</th>
            <th>QTY</th>
            <th>HARGA</th>
            <th>TOTAL</th>
            <th>EDIT</th>
            <th>HAPUS</th>
      </tr>

      <?php foreach ($data['rows'] as $row) { ?>
            <?php $grand += $row['penjualan_total']; ?>
            <tr>
                  <td><?php echo $no++; ?></td>
                  <td><?php echo $e($row['penjualan_tgl']); ?></td>
                  <td><?php echo $e($row['penjualan_pembeli']); ?></td>
                  <td><?php echo $e($row['produk_kode']); ?> - <?php echo $e($row['produk_nama']); ?></td>
                  <td><?php echo $e($row['ktgr']); ?></td>
                  <td><?php echo $e($row['penjualan_qty']); ?></td>
                  <td><?php echo $rp($row['penjualan_hrg']); ?></td>
                  <td><?php echo $rp($row['penjualan_total']); ?></td>
                  <td><a href="<?php echo URL; ?>/penjualan/edit/<?php echo $e($row['penjualan_id']); ?>" class="btn btn-primary">Edit</a></td>
                  <td>
                        <form action="<?php echo URL; ?>/penjualan/hapus" method="post" onsubmit="return confirm('Yakin ingin menghapus penjualan ini? Stok produk akan dikembalikan.');">
                              <input type="hidden" name="penjualan_id" value="<?php echo $e($row['penjualan_id']); ?>">
                              <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                  </td>
            </tr>
      <?php } ?>

      <?php if (empty($data['rows'])) { ?>
            <tr>
                  <td colspan="10" class="text-center text-muted">Belum ada data penjualan.</td>
            </tr>
      <?php } else { ?>
            <tr>
                  <th colspan="7" class="text-end">TOTAL PENJUALAN</th>
                  <th colspan="3"><?php echo $rp($grand); ?></th>
            </tr>
      <?php } ?>

</table>
