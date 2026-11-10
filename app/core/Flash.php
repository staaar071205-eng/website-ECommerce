<?php

namespace App\Core;

// Pesan sekali tampil (sukses / gagal) yang disimpan di session
class Flash
{
	public static function set($type, $message)
	{
		$_SESSION['flash'] = ['type' => $type, 'message' => $message];
	}

	public static function pull()
	{
		$flash = $_SESSION['flash'] ?? null;
		unset($_SESSION['flash']);

		return $flash;
	}
}
