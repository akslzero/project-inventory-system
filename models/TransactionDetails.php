<?php
class TransactionDetail {
    private $conn;
    private $table_name = "transaction_details";

    public $id;
    public $transaction_id;
    public $product_id;
    public $jumlah;
    public $harga_satuan;

    public function __construct($db) {
        $this->conn = $db;
    }

     public function readByTransactionId($transaction_id) {
        $query = "SELECT td.*, p.nama_produk 
                  FROM " . $this->table_name . " td
                  LEFT JOIN products p ON td.product_id = p.id
                  WHERE td.transaction_id = ?
                  ORDER BY td.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $transaction_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET transaction_id = :transaction_id,
                      product_id = :product_id,
                      jumlah = :jumlah,
                      harga_satuan = :harga_satuan";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':transaction_id', $this->transaction_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':jumlah', $this->jumlah);
        $stmt->bindParam(':harga_satuan', $this->harga_satuan);

        return $stmt->execute();
    }
}
