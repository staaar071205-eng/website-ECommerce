<?php

// Update November 2024

namespace App\Controllers;

use App\Core\Controller;

class Kategori extends Controller
{
	public object $model;

	public function __construct()
	{
		$this->model = new \App\Models\Kategori();
	}

	public function index()
	{
		$data['rows'] = $this->model->tampil();
		$this->dashboard('kategori/index', $data);
	}

	public function input()
	{
		$this->dashboard('kategori/input');
	}

	public function simpan()
	{
		$this->model->simpan();
		header('location:kategori');
	}

	public function edit($id)
	{
		$data['row'] = $this->model->edit($id);
		$this->dashboard('kategori/edit', $data);
	}

	public function update()
	{
		$this->model->update();
		header('location:kategori');
	}

	public function hapus()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$hasil = $this->model->hapus($_POST['kat_id'] ?? 0);
			\App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);
		}

		header('location:' . URL . '/kategori');
		exit;
	}
}
