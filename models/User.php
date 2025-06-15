<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $role;
    public $nama_lengkap;

    public function __construct($db) {
        $this->conn = $db;
    }


    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
              
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->role = $row['role'];
                $this->nama_lengkap = $row['nama_lengkap'];
                return true;
            }
        }

        return false;
    }

   
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readOne() {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function readAll() {
    $query = "SELECT id, username FROM " . $this->table_name . " ORDER BY username ASC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
?>

