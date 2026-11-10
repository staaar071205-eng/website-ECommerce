<?php

// Update November 2024

namespace App\Controllers;

use App\Core\Controller;

class Produk extends Controller
{
     public object $model;

     public function __construct()
     {
          $this->model = new \App\Models\Produk();
     }

     public function index()
     {
          $data['rows'] = $this->model->tampil();
          $this->dashboard('produk/index', $data);
     }

     public function input()
     {
          $data['listkategori'] = $this->model->listkategori();
          $this->dashboard('produk/input', $data);
     }

     public function simpan()
     {
          if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
               header('location:' . URL . '/produk');
               exit;
          }

          $hasil = $this->model->simpan();
          \App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);

          header('location:' . URL . ($hasil['ok'] ? '/produk' : '/produk/input'));
          exit;
     }

     public function detail($id = 0)
     {
          $data['row'] = $this->model->detail($id);

          if (!$data['row']) {
               \App\Core\Flash::set('danger', 'Data produk tidak ditemukan.');
               header('location:' . URL . '/produk');
               exit;
          }

          $this->dashboard('produk/detail', $data);
     }

     public function edit($id = 0)
     {
          $data['row'] = $this->model->edit($id);

          if (!$data['row']) {
               \App\Core\Flash::set('danger', 'Data produk tidak ditemukan.');
               header('location:' . URL . '/produk');
               exit;
          }

          $data['listkategori'] = $this->model->listkategori();
          $this->dashboard('produk/edit', $data);
     }

     public function update()
     {
          if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
               header('location:' . URL . '/produk');
               exit;
          }

          $hasil = $this->model->update();
          \App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);

          if ($hasil['ok']) {
               header('location:' . URL . '/produk');
          } else {
               $id = (int) ($_POST['produk_id'] ?? 0);
               header('location:' . URL . ($id > 0 ? '/produk/edit/' . $id : '/produk'));
          }
          exit;
     }

     public function hapus()
     {
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               $hasil = $this->model->hapus($_POST['produk_id'] ?? 0);
               \App\Core\Flash::set($hasil['ok'] ? 'success' : 'danger', $hasil['pesan']);
          }

          header('location:' . URL . '/produk');
          exit;
     }
}
