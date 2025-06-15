<?php
class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $nama_kategori;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_kategori ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nama_kategori = :nama_kategori";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_kategori', $this->nama_kategori);
        return $stmt->execute();
}

}
