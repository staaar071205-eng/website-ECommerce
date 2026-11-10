<?php

namespace App\Models;

use App\Core\Model;

class Penjualan extends Model
{
      public function tampil()
      {
            $sql = "SELECT j.*, p.produk_kode, p.produk_nama, k.kat_nama AS ktgr
            FROM tb_penjualan j
            INNER JOIN tb_produk p ON j.penjualan_id_produk=p.produk_id
            LEFT JOIN tb_kategori k ON p.produk_id_kat=k.kat_id
            ORDER BY j.penjualan_tgl DESC, j.penjualan_id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function listProduk()
      {
            $sql = "SELECT p.*, k.kat_nama AS ktgr FROM tb_produk p
            LEFT JOIN tb_kategori k ON p.produk_id_kat=k.kat_id
            ORDER BY p.produk_nama";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $this->selects($stmt);
      }

      public function edit($id)
      {
            $query = "SELECT * FROM tb_penjualan WHERE penjualan_id=:penjualan_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":penjualan_id", (int) $id);
            $stmt->execute();

            return $this->select($stmt);
      }

      // Simpan penjualan baru + kurangi stok produk (satu transaksi)
      public function simpan()
      {
            try {
                  $d = $this->ambilInput();

                  $this->db->beginTransaction();

                  $produk = $this->kunciProduk($d['penjualan_id_produk']);

                  if ($produk['produk_stock'] < $d['penjualan_qty']) {
                        throw new \InvalidArgumentException("Stok tidak cukup. Stok '{$produk['produk_nama']}' tersisa {$produk['produk_stock']}.");
                  }

                  $hrg = (int) $produk['produk_hrg'];

                  $sql = "INSERT INTO tb_penjualan
                  (penjualan_id_produk, penjualan_tgl, penjualan_pembeli, penjualan_qty, penjualan_hrg, penjualan_total, penjualan_keterangan)
                  VALUES
                  (:id_produk, :tgl, :pembeli, :qty, :hrg, :total, :keterangan)";
                  $stmt = $this->db->prepare($sql);
                  $stmt->bindValue(":id_produk", $d['penjualan_id_produk']);
                  $stmt->bindValue(":tgl", $d['penjualan_tgl']);
                  $stmt->bindValue(":pembeli", $d['penjualan_pembeli']);
                  $stmt->bindValue(":qty", $d['penjualan_qty']);
                  $stmt->bindValue(":hrg", $hrg);
                  $stmt->bindValue(":total", $hrg * $d['penjualan_qty']);
                  $stmt->bindValue(":keterangan", $d['penjualan_keterangan']);
                  $stmt->execute();

                  $this->ubahStok($d['penjualan_id_produk'], -$d['penjualan_qty']);

                  $this->db->commit();
            } catch (\InvalidArgumentException $e) {
                  $this->batalkan();
                  return ['ok' => false, 'pesan' => $e->getMessage()];
            } catch (\Throwable $e) {
                  $this->batalkan();
                  throw $e;
            }

            return ['ok' => true, 'pesan' => 'Penjualan berhasil disimpan dan stok produk diperbarui.'];
      }

      // Ubah penjualan + sesuaikan stok produk (satu transaksi)
      public function update()
      {
            try {
                  $id = $this->angka($_POST['penjualan_id'] ?? '', 2147483647);
                  $d = $this->ambilInput();

                  $this->db->beginTransaction();

                  $stmt = $this->db->prepare("SELECT * FROM tb_penjualan WHERE penjualan_id=:id FOR UPDATE");
                  $stmt->bindValue(":id", $id === false ? 0 : $id);
                  $stmt->execute();
                  $lama = $this->select($stmt);

                  if (!$lama) {
                        throw new \InvalidArgumentException('Data penjualan tidak ditemukan.');
                  }

                  $produkLama = (int) $lama['penjualan_id_produk'];
                  $produkBaru = $d['penjualan_id_produk'];
                  $qtyLama = (int) $lama['penjualan_qty'];
                  $qtyBaru = $d['penjualan_qty'];

                  if ($produkLama === $produkBaru) {
                        // Produk sama: stok tersedia = stok sekarang + qty lama
                        $produk = $this->kunciProduk($produkBaru);
                        $tersedia = $produk['produk_stock'] + $qtyLama;

                        if ($tersedia < $qtyBaru) {
                              throw new \InvalidArgumentException("Stok tidak cukup. Stok '{$produk['produk_nama']}' yang tersedia untuk transaksi ini hanya $tersedia.");
                        }

                        $hrg = (int) $lama['penjualan_hrg']; // harga saat transaksi tetap dipertahankan
                        $this->ubahStok($produkBaru, $qtyLama - $qtyBaru);
                  } else {
                        // Ganti produk: kunci kedua produk berurutan (hindari deadlock)
                        $ids = [$produkLama, $produkBaru];
                        sort($ids);
                        foreach ($ids as $pid) {
                              $kunci[$pid] = $this->kunciProduk($pid, true);
                        }

                        $baru = $kunci[$produkBaru];
                        if ($baru === null) {
                              throw new \InvalidArgumentException('Produk yang dipilih tidak ditemukan.');
                        }
                        if ($baru['produk_stock'] < $qtyBaru) {
                              throw new \InvalidArgumentException("Stok tidak cukup. Stok '{$baru['produk_nama']}' tersisa {$baru['produk_stock']}.");
                        }

                        $hrg = (int) $baru['produk_hrg'];
                        if ($kunci[$produkLama] !== null) {
                              $this->ubahStok($produkLama, $qtyLama);
                        }
                        $this->ubahStok($produkBaru, -$qtyBaru);
                  }

                  $sql = "UPDATE tb_penjualan SET
                  penjualan_id_produk=:id_produk, penjualan_tgl=:tgl, penjualan_pembeli=:pembeli,
                  penjualan_qty=:qty, penjualan_hrg=:hrg, penjualan_total=:total,
                  penjualan_keterangan=:keterangan, updated_at=NOW()
                  WHERE penjualan_id=:id";
                  $stmt = $this->db->prepare($sql);
                  $stmt->bindValue(":id_produk", $produkBaru);
                  $stmt->bindValue(":tgl", $d['penjualan_tgl']);
                  $stmt->bindValue(":pembeli", $d['penjualan_pembeli']);
                  $stmt->bindValue(":qty", $qtyBaru);
                  $stmt->bindValue(":hrg", $hrg);
                  $stmt->bindValue(":total", $hrg * $qtyBaru);
                  $stmt->bindValue(":keterangan", $d['penjualan_keterangan']);
                  $stmt->bindValue(":id", $id);
                  $stmt->execute();

                  $this->db->commit();
            } catch (\InvalidArgumentException $e) {
                  $this->batalkan();
                  return ['ok' => false, 'pesan' => $e->getMessage()];
            } catch (\Throwable $e) {
                  $this->batalkan();
                  throw $e;
            }

            return ['ok' => true, 'pesan' => 'Penjualan berhasil diperbarui dan stok produk disesuaikan.'];
      }

      // Hapus penjualan + kembalikan stok produk (satu transaksi)
      public function hapus($id)
      {
            try {
                  $this->db->beginTransaction();

                  $stmt = $this->db->prepare("SELECT * FROM tb_penjualan WHERE penjualan_id=:id FOR UPDATE");
                  $stmt->bindValue(":id", (int) $id);
                  $stmt->execute();
                  $row = $this->select($stmt);

                  if (!$row) {
                        throw new \InvalidArgumentException('Data penjualan tidak ditemukan.');
                  }

                  $this->kunciProduk((int) $row['penjualan_id_produk'], true);
                  $this->ubahStok((int) $row['penjualan_id_produk'], (int) $row['penjualan_qty']);

                  $stmt = $this->db->prepare("DELETE FROM tb_penjualan WHERE penjualan_id=:id");
                  $stmt->bindValue(":id", (int) $id);
                  $stmt->execute();

                  $this->db->commit();
            } catch (\InvalidArgumentException $e) {
                  $this->batalkan();
                  return ['ok' => false, 'pesan' => $e->getMessage()];
            } catch (\Throwable $e) {
                  $this->batalkan();
                  throw $e;
            }

            return ['ok' => true, 'pesan' => 'Penjualan berhasil dihapus dan stok produk dikembalikan.'];
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

      private function ambilInput()
      {
            $d = [
                  'penjualan_id_produk'  => $this->angka($_POST['penjualan_id_produk'] ?? '', 2147483647),
                  'penjualan_tgl'        => trim((string) ($_POST['penjualan_tgl'] ?? '')),
                  'penjualan_pembeli'    => trim((string) ($_POST['penjualan_pembeli'] ?? '')),
                  'penjualan_qty'        => $this->angka($_POST['penjualan_qty'] ?? '', 2147483647),
                  'penjualan_keterangan' => trim((string) ($_POST['penjualan_keterangan'] ?? '')),
            ];

            if ($d['penjualan_id_produk'] === false) {
                  throw new \InvalidArgumentException('Produk wajib dipilih.');
            }

            $tgl = \DateTime::createFromFormat('Y-m-d', $d['penjualan_tgl']);
            if (!$tgl || $tgl->format('Y-m-d') !== $d['penjualan_tgl']) {
                  throw new \InvalidArgumentException('Tanggal penjualan tidak valid.');
            }

            if ($d['penjualan_pembeli'] === '' || strlen($d['penjualan_pembeli']) > 100) {
                  throw new \InvalidArgumentException('Nama pembeli wajib diisi (maksimal 100 karakter).');
            }

            if ($d['penjualan_qty'] === false || $d['penjualan_qty'] < 1) {
                  throw new \InvalidArgumentException('Jumlah (qty) harus berupa angka bulat minimal 1.');
            }

            // Keterangan boleh kosong -> simpan NULL
            if ($d['penjualan_keterangan'] === '') {
                  $d['penjualan_keterangan'] = null;
            }

            return $d;
      }

      // Ambil produk + kunci barisnya (SELECT ... FOR UPDATE) agar stok aman.
      // $boleh_kosong = true -> kembalikan null jika produk tidak ada.
      private function kunciProduk($id, $boleh_kosong = false)
      {
            $stmt = $this->db->prepare("SELECT produk_id, produk_nama, produk_hrg, produk_stock FROM tb_produk WHERE produk_id=:id FOR UPDATE");
            $stmt->bindValue(":id", (int) $id);
            $stmt->execute();
            $produk = $this->select($stmt);

            if (!$produk) {
                  if ($boleh_kosong) {
                        return null;
                  }
                  throw new \InvalidArgumentException('Produk yang dipilih tidak ditemukan.');
            }

            $produk['produk_stock'] = (int) $produk['produk_stock'];

            return $produk;
      }

      // Tambah (+) atau kurangi (-) stok produk
      private function ubahStok($produk_id, $selisih)
      {
            $stmt = $this->db->prepare("UPDATE tb_produk SET produk_stock = produk_stock + :selisih WHERE produk_id=:id");
            $stmt->bindValue(":selisih", (int) $selisih);
            $stmt->bindValue(":id", (int) $produk_id);
            $stmt->execute();
      }

      private function batalkan()
      {
            if ($this->db->inTransaction()) {
                  $this->db->rollBack();
            }
      }
}
