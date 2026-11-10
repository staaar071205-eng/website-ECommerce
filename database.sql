CREATE DATABASE IF NOT EXISTS dbproject6 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE dbproject6;

CREATE TABLE IF NOT EXISTS tb_kategori (
	kat_id TINYINT(3) NOT NULL AUTO_INCREMENT,
	kat_nama VARCHAR(50) NOT NULL,
	kat_keterangan TEXT DEFAULT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT NULL,
	PRIMARY KEY(kat_id)
);

CREATE TABLE IF NOT EXISTS tb_produk (
	produk_id INT(11) NOT NULL AUTO_INCREMENT,
	produk_id_kat TINYINT(3) NOT NULL,
	produk_kode VARCHAR(50) NOT NULL,
	produk_nama VARCHAR(256) NOT NULL,
	produk_hrg INT(11) NOT NULL DEFAULT 0,
	produk_keterangan TEXT DEFAULT NULL,
	produk_stock INT(11) NOT NULL DEFAULT 0,
	produk_photo VARCHAR(256) DEFAULT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT NULL,
	PRIMARY KEY(produk_id),
	UNIQUE KEY uq_produk_kode (produk_kode),
	KEY idx_produk_kat (produk_id_kat),
	CONSTRAINT fk_produk_kategori FOREIGN KEY (produk_id_kat)
		REFERENCES tb_kategori (kat_id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tb_penjualan (
	penjualan_id INT(11) NOT NULL AUTO_INCREMENT,
	penjualan_id_produk INT(11) NOT NULL,
	penjualan_tgl DATE NOT NULL,
	penjualan_pembeli VARCHAR(100) NOT NULL,
	penjualan_qty INT(11) NOT NULL,
	penjualan_hrg INT(11) NOT NULL,
	penjualan_total BIGINT NOT NULL,
	penjualan_keterangan TEXT DEFAULT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT NULL,
	PRIMARY KEY(penjualan_id),
	KEY idx_penjualan_produk (penjualan_id_produk),
	CONSTRAINT fk_penjualan_produk FOREIGN KEY (penjualan_id_produk)
		REFERENCES tb_produk (produk_id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) ENGINE=InnoDB;
