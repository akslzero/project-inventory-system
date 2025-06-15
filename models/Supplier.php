<?php
class Supplier {
    private $conn;
    private $table_name = "suppliers";

    public $id;
    public $nama_supplier;
    public $alamat;
    public $telp;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nama_supplier = :nama_supplier, alamat = :alamat, telp = :telp, email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_supplier', $this->nama_supplier);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':telp', $this->telp);
        $stmt->bindParam(':email', $this->email);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET nama_supplier = :nama_supplier, alamat = :alamat, telp = :telp, email = :email
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_supplier', $this->nama_supplier);
        $stmt->bindParam(':alamat', $this->alamat);
        $stmt->bindParam(':telp', $this->telp);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function delete() {
        try {
            $this->conn->beginTransaction();
            
            $queryDetails = "DELETE td FROM transaction_details td
                             JOIN transactions t ON td.transaction_id = t.id
                             WHERE t.supplier_id = :id";
            $stmtDetails = $this->conn->prepare($queryDetails);
            $stmtDetails->bindParam(':id', $this->id);
            $stmtDetails->execute();
            
            $queryTrans = "DELETE FROM transactions WHERE supplier_id = :id";
            $stmtTrans = $this->conn->prepare($queryTrans);
            $stmtTrans->bindParam(':id', $this->id);
            $stmtTrans->execute();
            
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $result = $stmt->execute();

            $this->conn->commit();
            return $result;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
