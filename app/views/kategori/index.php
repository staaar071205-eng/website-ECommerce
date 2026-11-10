<h2>Kategori</h2>

<a href="<?php echo URL; ?>/kategori/input" class="btn btn-primary mb-3">Input Kategori</a>

<table class="table table-bordered">
      <tr>
            <th>NO</th>
            <th>NAMA</th>
            <th>KETERANGAN</th>
            <th>EDIT</th>
            <th>HAPUS</th>
      </tr>

      <?php foreach ($data['rows'] as $row) { ?>
            <tr>
                  <td><?php echo $row['kat_id']; ?></td>
                  <td><?php echo $row['kat_nama']; ?></td>
                  <td><?php echo $row['kat_keterangan']; ?></td>
                  <td><a href="<?php echo URL; ?>/kategori/edit/<?php echo $row['kat_id']; ?>" class="btn btn-primary">Edit</a></td>
                  <td>
                        <form action="<?php echo URL; ?>/kategori/hapus" method="post" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                              <input type="hidden" name="kat_id" value="<?php echo $row['kat_id']; ?>">
                              <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                  </td>
            </tr>
      <?php } ?>

</table>