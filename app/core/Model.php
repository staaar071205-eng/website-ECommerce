<?php

namespace App\Core;

use PDO;

class Model
{
    protected object $db;

    public function __construct()
    {
        try {
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
				DB_USER,
				DB_PASS
            );

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("error! " . $e->getMessage());
        }
    }

    public function select($stmt)
    {
        return $stmt->fetch();
    }

    public function selects($stmt)
    {
        $data = [];

        while ($rows = $stmt->fetch()) {
            $data[] = $rows;
        }

        return $data;
    }
}