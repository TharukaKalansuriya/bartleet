<?php
class Database {
    private $host = "localhost";         
    private $username = "root";          
    private $password = "1234";              
    private $dbname = "bartleet";

    public $conn;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Database Connection Failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
