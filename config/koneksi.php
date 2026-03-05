<?php

class Database {
    private $host = "localhost";
    private $user = "root"; 
    private $pass = "";     
    private $db   = "db_perpustakaan";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->user, $this->pass);
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $exception) {
            echo "Koneksi ke database gagal: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>