<h2>Input Kategori</h2>

<form action="<?php echo URL; ?>/kategori/simpan" method="post">
    <table class="table table-hover">
        <tr>
            <td>NAMA</td>
            <td><input type="text" name="kat_nama" class="form-control"></td>
        </tr>
        <tr>
            <td>KETERANGAN</td>
            <td><textarea name="kat_keterangan" class="form-control" id="" cols="30" rows="10"></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="btn_simpan" value="SIMPAN" class="btn btn-primary"></td>
        </tr>
    </table>
</form>