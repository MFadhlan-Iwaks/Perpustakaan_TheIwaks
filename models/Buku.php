<?php

class Buku {
    private $conn;
    private $table_name = "buku";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id_buku DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_buku = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (isbn, judul, kategori, penulis, penerbit, tahun_terbit, stok, lokasi_rak, gambar) 
                  VALUES (:isbn, :judul, :kategori, :penulis, :penerbit, :tahun_terbit, :stok, :lokasi_rak, :gambar)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":isbn", $data['isbn']);
        $stmt->bindParam(":judul", $data['judul']);
        $stmt->bindParam(":kategori", $data['kategori']);
        $stmt->bindParam(":penulis", $data['penulis']);
        $stmt->bindParam(":penerbit", $data['penerbit']);
        $stmt->bindParam(":tahun_terbit", $data['tahun_terbit']);
        $stmt->bindParam(":stok", $data['stok']);
        $stmt->bindParam(":lokasi_rak", $data['lokasi_rak']);
        $stmt->bindParam(":gambar", $data['gambar']);

        if($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET isbn=:isbn, judul=:judul, kategori=:kategori, stok=:stok, lokasi_rak=:lokasi_rak 
                  WHERE id_buku=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":isbn", $data['isbn']);
        $stmt->bindParam(":judul", $data['judul']);
        $stmt->bindParam(":kategori", $data['kategori']);
        $stmt->bindParam(":stok", $data['stok']);
        $stmt->bindParam(":lokasi_rak", $data['lokasi_rak']);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_buku = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>