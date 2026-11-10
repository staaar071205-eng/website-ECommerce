<?php

namespace App\Controllers;

use App\Core\Controller;

class Penjualan extends Controller
{
	public object $model;

	public function __construct()
	{
		$this->model = new \App\Models\Penjualan();
	}

	public function index()
	{
		$data['rows'] = $this->model->tampil();
		$this->dashboard('penjualan/index', $data);
	}

	public function input()
	{
		$data['listproduk'] = $this->model->listProduk();
		$this->dashboard('penjualan/input', $data);
	}

	public function simpan()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('location:' . URL . '/penjualan');
			exit;
		}

		$hasil = $this->model->simpan();
		\App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);

		header('location:' . URL . ($hasil['ok'] ? '/penjualan' : '/penjualan/input'));
		exit;
	}

	public function edit($id = 0)
	{
		$data['row'] = $this->model->edit($id);

		if (!$data['row']) {
			\App\Core\Flash::set('danger', 'Data penjualan tidak ditemukan.');
			header('location:' . URL . '/penjualan');
			exit;
		}

		$data['listproduk'] = $this->model->listProduk();
		$this->dashboard('penjualan/edit', $data);
	}

	public function update()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('location:' . URL . '/penjualan');
			exit;
		}

		$hasil = $this->model->update();
		\App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);

		if ($hasil['ok']) {
			header('location:' . URL . '/penjualan');
		} else {
			$id = (int) ($_POST['penjualan_id'] ?? 0);
			header('location:' . URL . '/penjualan/edit/' . $id);
		}
		exit;
	}

	public function hapus()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$hasil = $this->model->hapus($_POST['penjualan_id'] ?? 0);
			\App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);
		}

		header('location:' . URL . '/penjualan');
		exit;
	}
}
