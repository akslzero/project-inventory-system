<?php
class Transaction {
    private $conn;
    private $table_name = "transactions";  

    public $id;
    public $kode_transaksi;
    public $tipe;
    public $tanggal;
    public $user_id;
    public $supplier_id;
    public $customer_id;

    public function __construct($db) {
        $this->conn = $db;
    }

  
    public function generateKode() {
        $prefix = strtoupper(substr($this->tipe, 0, 3)); 
        $date = date('Ymd');
        $random = rand(100, 999);
        return $prefix . $date . $random;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY tanggal DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->kode_transaksi = $row['kode_transaksi'];
            $this->tipe = $row['tipe'];
            $this->tanggal = $row['tanggal'];
            $this->user_id = $row['user_id'];
            $this->supplier_id = $row['supplier_id'];
            $this->customer_id = $row['customer_id'];
            return $row;
        }
        return false;
    }

    public function getTotalItemsByType($start_date = null, $end_date = null, $tipe = null) {
    $query = "SELECT SUM(td.jumlah) as total_item 
              FROM " . $this->table_name . " t
              JOIN transaction_details td ON t.id = td.transaction_id
              WHERE 1=1 ";

    $params = [];

    if ($start_date) {
        $query .= " AND t.tanggal >= :start_date ";
        $params[':start_date'] = $start_date;
    }

    if ($end_date) {
        $query .= " AND t.tanggal <= :end_date ";
        $params[':end_date'] = $end_date;
    }

    if ($tipe) {
        $query .= " AND t.tipe = :tipe ";
        $params[':tipe'] = $tipe;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_item'] ?? 0;
}


    public function getDetailsByTransactionId($id) {
    $query = "SELECT td.*, p.nama_produk 
              FROM transaction_details td 
              JOIN products p ON td.product_id = p.id 
              WHERE td.transaction_id = :id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function update() {
    $query = "UPDATE " . $this->table_name . "
              SET tipe = :tipe,
                  tanggal = :tanggal,
                  user_id = :user_id,
                  supplier_id = :supplier_id,
                  customer_id = :customer_id
              WHERE id = :id";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':tipe', $this->tipe);
    $stmt->bindParam(':tanggal', $this->tanggal);
    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->bindParam(':supplier_id', $this->supplier_id);
    $stmt->bindParam(':customer_id', $this->customer_id);
    $stmt->bindParam(':id', $this->id);

    return $stmt->execute();
}


    
    
    public function delete() {
    try {
      
        $this->conn->beginTransaction();

     
        $queryDetail = "DELETE FROM transaction_details WHERE transaction_id = :id";
        $stmtDetail = $this->conn->prepare($queryDetail);
        $stmtDetail->bindParam(':id', $this->id);
        $stmtDetail->execute();

      
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        
        $this->conn->commit();
        return true;

    } catch (PDOException $e) {
     
        $this->conn->rollBack();
        return false;
    }
}

public function getTotalByType($start_date = null, $end_date = null, $tipe = null) {
    $query = "SELECT SUM(td.jumlah * td.harga_satuan) as total_amount
              FROM " . $this->table_name . " t
              JOIN transaction_details td ON t.id = td.transaction_id
              WHERE 1=1 ";

    $params = [];

    if ($start_date) {
        $query .= " AND t.tanggal >= :start_date ";
        $params[':start_date'] = $start_date;
    }

    if ($end_date) {
        $query .= " AND t.tanggal <= :end_date ";
        $params[':end_date'] = $end_date;
    }

    if ($tipe) {
        $query .= " AND t.tipe = :tipe ";
        $params[':tipe'] = $tipe;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_amount'] ?? 0;
}



public function getReport($start_date = null, $end_date = null, $tipe = null, $user_id = null, $supplier_id = null, $customer_id = null) {
    $query = "SELECT t.*, u.username, s.nama_supplier, c.nama_customer
              FROM " . $this->table_name . " t
              LEFT JOIN users u ON t.user_id = u.id
              LEFT JOIN suppliers s ON t.supplier_id = s.id
              LEFT JOIN customers c ON t.customer_id = c.id
              WHERE 1=1 ";

    $params = [];

    if ($start_date) {
        $query .= " AND t.tanggal >= :start_date ";
        $params[':start_date'] = $start_date;
    }

    if ($end_date) {
        $query .= " AND t.tanggal <= :end_date ";
        $params[':end_date'] = $end_date;
    }

    if ($tipe) {
        $query .= " AND t.tipe = :tipe ";
        $params[':tipe'] = $tipe;
    }

    if ($user_id) {
        $query .= " AND t.user_id = :user_id ";
        $params[':user_id'] = $user_id;
    }

    if ($supplier_id) {
        $query .= " AND t.supplier_id = :supplier_id ";
        $params[':supplier_id'] = $supplier_id;
    }

    if ($customer_id) {
        $query .= " AND t.customer_id = :customer_id ";
        $params[':customer_id'] = $customer_id;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




    public function create() {
        $this->kode_transaksi = $this->generateKode();
        $this->tanggal = date('Y-m-d H:i:s');

        $query = "INSERT INTO " . $this->table_name . " 
                  SET kode_transaksi = :kode_transaksi,
                      tipe = :tipe,
                      tanggal = :tanggal,
                      user_id = :user_id,
                      supplier_id = :supplier_id,
                      customer_id = :customer_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':kode_transaksi', $this->kode_transaksi);
        $stmt->bindParam(':tipe', $this->tipe);
        $stmt->bindParam(':tanggal', $this->tanggal);
        $stmt->bindParam(':user_id', $this->user_id);

  
        $stmt->bindParam(':supplier_id', $this->supplier_id);
        $stmt->bindParam(':customer_id', $this->customer_id);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        

        return false;
    }
}
