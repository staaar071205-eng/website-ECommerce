<?php

// Update November 2024

namespace App\Models;

use App\Core\Model;

class Produk extends Model
{
      // Folder penyimpanan foto produk (relatif terhadap ROOT) & batas ukuran 2 MB
      const FOLDER_FOTO = "layouts/assets/img/produk/";
      const MAKS_FOTO = 2097152;

      public function tampil()
      {
            $sql = "SELECT p.*, k.kat_nama AS ktgr FROM tb_produk p
            LEFT JOIN tb_kategori k
            ON p.produk_id_kat=k.kat_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function listKategori()
      {
            $query = "SELECT * FROM tb_kategori";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function detail($id)
      {
            $sql = "SELECT p.*, k.kat_nama AS ktgr FROM tb_produk p
            LEFT JOIN tb_kategori k
            ON p.produk_id_kat=k.kat_id
            WHERE p.produk_id=:produk_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":produk_id", (int) $id);
            $stmt->execute();

            return $this->select($stmt);
      }

      public function edit($id)
      {
            $query = "SELECT * FROM tb_produk WHERE produk_id=:produk_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":produk_id", (int) $id);
            $stmt->execute();

            return $this->select($stmt);
      }

      public function simpan()
      {
            try {
                  $this->periksaKiriman();
                  $d = $this->ambilInput();
                  $this->pastikanKategoriAda($d['produk_id_kat']);
                  $this->pastikanKodeUnik($d['produk_kode'], 0);
                  $foto = $this->unggahFoto();

                  try {
                        $sql = "INSERT INTO tb_produk
                        (produk_id_kat, produk_kode, produk_nama, produk_hrg, produk_keterangan, produk_stock, produk_photo)
                        VALUES
                        (:produk_id_kat, :produk_kode, :produk_nama, :produk_hrg, :produk_keterangan, :produk_stock, :produk_photo)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindValue(":produk_id_kat", $d['produk_id_kat']);
                        $stmt->bindValue(":produk_kode", $d['produk_kode']);
                        $stmt->bindValue(":produk_nama", $d['produk_nama']);
                        $stmt->bindValue(":produk_hrg", $d['produk_hrg']);
                        $stmt->bindValue(":produk_keterangan", $d['produk_keterangan']);
                        $stmt->bindValue(":produk_stock", $d['produk_stock']);
                        $stmt->bindValue(":produk_photo", $foto);
                        $stmt->execute();
                  } catch (\Throwable $e) {
                        $this->hapusFile($foto);
                        throw $e;
                  }
            } catch (\InvalidArgumentException $e) {
                  return ['ok' => false, 'pesan' => $e->getMessage()];
            }

            return ['ok' => true, 'pesan' => 'Produk berhasil disimpan.'];
      }

      public function update()
      {
            try {
                  $this->periksaKiriman();
                  $id = $this->angka($_POST['produk_id'] ?? '', 2147483647);
                  $lama = ($id === false) ? false : $this->edit($id);

                  if (!$lama) {
                        throw new \InvalidArgumentException('Data produk tidak ditemukan.');
                  }

                  $d = $this->ambilInput();
                  $this->pastikanKategoriAda($d['produk_id_kat']);
                  $this->pastikanKodeUnik($d['produk_kode'], $id);

                  // Foto baru (jika ada) menggantikan foto lama
                  $fotoBaru = $this->unggahFoto();
                  $foto = $fotoBaru ?? $lama['produk_photo'];

                  try {
                        $sql = "UPDATE tb_produk SET
                        produk_id_kat=:produk_id_kat, produk_kode=:produk_kode, produk_nama=:produk_nama,
                        produk_hrg=:produk_hrg, produk_keterangan=:produk_keterangan, produk_stock=:produk_stock,
                        produk_photo=:produk_photo, updated_at=NOW()
                        WHERE produk_id=:produk_id";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindValue(":produk_id_kat", $d['produk_id_kat']);
                        $stmt->bindValue(":produk_kode", $d['produk_kode']);
                        $stmt->bindValue(":produk_nama", $d['produk_nama']);
                        $stmt->bindValue(":produk_hrg", $d['produk_hrg']);
                        $stmt->bindValue(":produk_keterangan", $d['produk_keterangan']);
                        $stmt->bindValue(":produk_stock", $d['produk_stock']);
                        $stmt->bindValue(":produk_photo", $foto);
                        $stmt->bindValue(":produk_id", $id);
                        $stmt->execute();
                  } catch (\Throwable $e) {
                        $this->hapusFile($fotoBaru);
                        throw $e;
                  }

                  if ($fotoBaru !== null) {
                        $this->hapusFile($lama['produk_photo']);
                  }
            } catch (\InvalidArgumentException $e) {
                  return ['ok' => false, 'pesan' => $e->getMessage()];
            }

            return ['ok' => true, 'pesan' => 'Produk berhasil diperbarui.'];
      }

      public function hapus($id)
      {
            $id = (int) $id;
            $row = $this->edit($id);

            if (!$row) {
                  return ['ok' => false, 'pesan' => 'Data produk tidak ditemukan.'];
            }

            // Produk yang sudah pernah terjual tidak boleh dihapus
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tb_penjualan WHERE penjualan_id_produk=:produk_id");
            $stmt->bindValue(":produk_id", $id);
            $stmt->execute();
            $jumlah = (int) $stmt->fetchColumn();

            if ($jumlah > 0) {
                  return ['ok' => false, 'pesan' => "Produk tidak dapat dihapus karena sudah dipakai di $jumlah data penjualan."];
            }

            $stmt = $this->db->prepare("DELETE FROM tb_produk WHERE produk_id=:produk_id");
            $stmt->bindValue(":produk_id", $id);
            $stmt->execute();

            $this->hapusFile($row['produk_photo']);

            return ['ok' => true, 'pesan' => 'Produk berhasil dihapus.'];
      }

      // ---------------------------------------------------------
      // Fungsi bantu (private)
      // ---------------------------------------------------------

      // Angka bulat >= 0 dan <= $maks. Mengembalikan false bila tidak valid.
      private function angka($nilai, $maks)
      {
            $nilai = trim((string) $nilai);

            if (!preg_match('/^[0-9]{1,10}$/', $nilai)) {
                  return false;
            }

            $angka = (int) $nilai;

            return $angka <= $maks ? $angka : false;
      }

      // Bila data kiriman melebihi post_max_size server, PHP mengosongkan seluruh $_POST
      private function periksaKiriman()
      {
            if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                  throw new \InvalidArgumentException('Data yang dikirim melebihi batas ukuran server. Gunakan foto yang lebih kecil (maksimal 2 MB).');
            }
      }

      private function ambilInput()
      {
            $d = [
                  'produk_id_kat'     => $this->angka($_POST['produk_id_kat'] ?? '', 127),
                  'produk_kode'       => trim((string) ($_POST['produk_kode'] ?? '')),
                  'produk_nama'       => trim((string) ($_POST['produk_nama'] ?? '')),
                  'produk_hrg'        => $this->angka($_POST['produk_hrg'] ?? '', 2147483647),
                  'produk_keterangan' => trim((string) ($_POST['produk_keterangan'] ?? '')),
                  'produk_stock'      => $this->angka($_POST['produk_stock'] ?? '', 2147483647),
            ];

            if ($d['produk_id_kat'] === false) {
                  throw new \InvalidArgumentException('Kategori wajib dipilih.');
            }
            if ($d['produk_kode'] === '' || strlen($d['produk_kode']) > 50) {
                  throw new \InvalidArgumentException('Kode produk wajib diisi (maksimal 50 karakter).');
            }
            if ($d['produk_nama'] === '' || strlen($d['produk_nama']) > 256) {
                  throw new \InvalidArgumentException('Nama produk wajib diisi (maksimal 256 karakter).');
            }
            if ($d['produk_hrg'] === false) {
                  throw new \InvalidArgumentException('Harga produk harus berupa angka bulat 0 atau lebih.');
            }
            if ($d['produk_stock'] === false) {
                  throw new \InvalidArgumentException('Stok produk harus berupa angka bulat 0 atau lebih.');
            }

            // Keterangan boleh kosong -> simpan NULL
            if ($d['produk_keterangan'] === '') {
                  $d['produk_keterangan'] = null;
            }

            return $d;
      }

      private function pastikanKategoriAda($kat_id)
      {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tb_kategori WHERE kat_id=:kat_id");
            $stmt->bindValue(":kat_id", $kat_id);
            $stmt->execute();

            if ((int) $stmt->fetchColumn() === 0) {
                  throw new \InvalidArgumentException('Kategori yang dipilih tidak ditemukan.');
            }
      }

      private function pastikanKodeUnik($kode, $kecualiId)
      {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tb_produk WHERE produk_kode=:produk_kode AND produk_id<>:produk_id");
            $stmt->bindValue(":produk_kode", $kode);
            $stmt->bindValue(":produk_id", $kecualiId);
            $stmt->execute();

            if ((int) $stmt->fetchColumn() > 0) {
                  throw new \InvalidArgumentException("Kode produk '$kode' sudah dipakai produk lain.");
            }
      }

      // Mengembalikan nama file yang tersimpan, atau null bila tidak ada foto diunggah
      private function unggahFoto()
      {
            if (!isset($_FILES['produk_photo']) || $_FILES['produk_photo']['error'] === UPLOAD_ERR_NO_FILE) {
                  return null;
            }

            $file = $_FILES['produk_photo'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                  throw new \InvalidArgumentException('Upload foto gagal. Pastikan ukuran foto tidak lebih dari 2 MB.');
            }
            if ($file['size'] > self::MAKS_FOTO) {
                  throw new \InvalidArgumentException('Ukuran foto maksimal 2 MB.');
            }

            $ekstensi = [
                  IMAGETYPE_JPEG => 'jpg',
                  IMAGETYPE_PNG  => 'png',
                  IMAGETYPE_GIF  => 'gif',
                  IMAGETYPE_WEBP => 'webp',
            ];
            $info = @getimagesize($file['tmp_name']);

            if ($info === false || !isset($ekstensi[$info[2]])) {
                  throw new \InvalidArgumentException('Format foto harus JPG, PNG, GIF, atau WEBP.');
            }

            $folder = ROOT . self::FOLDER_FOTO;
            if (!is_dir($folder)) {
                  mkdir($folder, 0755, true);
            }

            $nama = 'produk_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ekstensi[$info[2]];

            if (!move_uploaded_file($file['tmp_name'], $folder . $nama)) {
                  throw new \InvalidArgumentException('Foto gagal disimpan ke folder ' . self::FOLDER_FOTO);
            }

            return $nama;
      }

      private function hapusFile($nama)
      {
            if ($nama === null || $nama === '') {
                  return;
            }

            $path = ROOT . self::FOLDER_FOTO . basename($nama);

            if (is_file($path)) {
                  unlink($path);
            }
      }
}
