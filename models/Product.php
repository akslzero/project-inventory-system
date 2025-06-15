<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $nama_produk; 
    public $merk;
    public $harga;
    public $deskripsi;
    public $gambar;
    public $stok;
    public $kategori_id;

    public function __construct($db) {
        $this->conn = $db;
    }

   
    public function readAll($kategori_id = null, $search = null) {
    $query = "SELECT p.*, c.nama_kategori AS kategori FROM " . $this->table_name . " p
              LEFT JOIN categories c ON p.kategori_id = c.id";
    
    $conditions = [];
    $params = [];

    if ($kategori_id) {
        $conditions[] = "p.kategori_id = :kategori_id";
        $params[':kategori_id'] = $kategori_id;
    }
    if ($search) {
        $conditions[] = "(p.nama_produk LIKE :search OR p.merk LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY p.nama_produk ASC";

    $stmt = $this->conn->prepare($query);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



 
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                  nama_produk = :nama_produk,
                  merk = :merk,
                  harga = :harga,
                  deskripsi = :deskripsi,
                  gambar = :gambar,
                  stok = :stok,
                  kategori_id = :kategori_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nama_produk', $this->nama_produk);
        $stmt->bindParam(':merk', $this->merk);
        $stmt->bindParam(':harga', $this->harga);
        $stmt->bindParam(':deskripsi', $this->deskripsi);
        $stmt->bindParam(':gambar', $this->gambar);
        $stmt->bindParam(':stok', $this->stok);
        $stmt->bindParam(':kategori_id', $this->kategori_id);

        return $stmt->execute();
    }


    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function update() {
        $query = "UPDATE " . $this->table_name . " SET
                  nama_produk = :nama_produk,
                  merk = :merk,
                  harga = :harga,
                  deskripsi = :deskripsi,
                  gambar = :gambar,
                  stok = :stok,
                  kategori_id = :kategori_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nama_produk', $this->nama_produk);
        $stmt->bindParam(':merk', $this->merk);
        $stmt->bindParam(':harga', $this->harga);
        $stmt->bindParam(':deskripsi', $this->deskripsi);
        $stmt->bindParam(':gambar', $this->gambar);
        $stmt->bindParam(':stok', $this->stok);
        $stmt->bindParam(':kategori_id', $this->kategori_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }


    public function delete() {
      
        $query1 = "DELETE FROM transaction_details WHERE product_id = :id";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(':id', $this->id);
        $stmt1->execute();

  
        $query2 = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':id', $this->id);

        return $stmt2->execute();
    }

    public function readByKategori($kategori_id) {
    $query = "SELECT p.*, c.nama_kategori AS kategori FROM " . $this->table_name . " p
              LEFT JOIN categories c ON p.kategori_id = c.id
              WHERE p.kategori_id = :kategori_id
              ORDER BY p.nama_produk ASC";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':kategori_id', $kategori_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
