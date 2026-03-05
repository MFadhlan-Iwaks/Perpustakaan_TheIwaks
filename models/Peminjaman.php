<?php

class Peminjaman {
    private $conn;
    private $table_name = "peminjaman";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT p.*, b.judul, b.gambar, u.nama_lengkap 
                  FROM " . $this->table_name . " p 
                  JOIN buku b ON p.id_buku = b.id_buku 
                  JOIN users u ON p.id_user = u.id_user
                  ORDER BY p.status ASC, p.tanggal_pinjam DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($id_user) {
        $query = "SELECT p.*, b.judul, b.gambar, u.nama_lengkap 
                  FROM " . $this->table_name . " p 
                  JOIN buku b ON p.id_buku = b.id_buku 
                  JOIN users u ON p.id_user = u.id_user
                  WHERE p.id_user = :id_user
                  ORDER BY p.status ASC, p.tanggal_pinjam DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_peminjaman = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $this->conn->beginTransaction();

            $queryPinjam = "INSERT INTO " . $this->table_name . " 
                            (id_user, id_buku, tanggal_pinjam, tanggal_tenggat, status) 
                            VALUES (:id_user, :id_buku, :tgl_pinjam, :tgl_tenggat, 'dipinjam')";
            $stmt1 = $this->conn->prepare($queryPinjam);
            $stmt1->bindParam(":id_user", $data['id_user']);
            $stmt1->bindParam(":id_buku", $data['id_buku']);
            $stmt1->bindParam(":tgl_pinjam", $data['tgl_pinjam']);
            $stmt1->bindParam(":tgl_tenggat", $data['tgl_tenggat']);
            $stmt1->execute();
            
            $insertId = $this->conn->lastInsertId();

            $queryBuku = "UPDATE buku SET stok = stok - 1 WHERE id_buku = :id_buku";
            $stmt2 = $this->conn->prepare($queryBuku);
            $stmt2->bindParam(":id_buku", $data['id_buku']);
            $stmt2->execute();

            $this->conn->commit();
            return $insertId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function updateManual($id, $status, $denda) {
        $query = "UPDATE " . $this->table_name . " SET status=:status, denda=:denda WHERE id_peminjaman=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":denda", $denda);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function prosesKembali($id) {
        $peminjaman = $this->getById($id);
        if (!$peminjaman || $peminjaman['status'] == 'dikembalikan') {
            return ['status' => false, 'message' => 'Data tidak valid atau sudah dikembalikan'];
        }

        $selisih = floor((time() - strtotime($peminjaman['tanggal_tenggat'])) / 86400);
        $denda = $selisih > 0 ? $selisih * 20000 : 0;
        $tgl_kembali = date('Y-m-d');

        try {
            $this->conn->beginTransaction();

            $qUpdatePinjam = "UPDATE " . $this->table_name . " SET status='dikembalikan', tanggal_dikembalikan=:tgl, denda=:denda WHERE id_peminjaman=:id";
            $stmt1 = $this->conn->prepare($qUpdatePinjam);
            $stmt1->bindParam(":tgl", $tgl_kembali);
            $stmt1->bindParam(":denda", $denda);
            $stmt1->bindParam(":id", $id);
            $stmt1->execute();

            $qUpdateBuku = "UPDATE buku SET stok = stok + 1 WHERE id_buku = :id_buku";
            $stmt2 = $this->conn->prepare($qUpdateBuku);
            $stmt2->bindParam(":id_buku", $peminjaman['id_buku']);
            $stmt2->execute();

            $this->conn->commit();
            return ['status' => true, 'denda' => $denda];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['status' => false, 'message' => 'Gagal memproses ke database'];
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_peminjaman = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>