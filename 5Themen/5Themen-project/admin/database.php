<?php
require_once __DIR__ . '/config.php';

class Database {
    public $link;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        $this->link = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME,port:3307);
        if ($this->link->connect_errno) {
            die(" Kết nối CSDL thất bại: " . $this->link->connect_error);
        }
        $this->link->set_charset("utf8mb4");
    }

    public function select($q)  { return $this->link->query($q); }
    public function insert($q)  { return $this->link->query($q); }
    public function update($q)  { return $this->link->query($q); }
    public function delete($q)  { return $this->link->query($q); }
    public function escape($s)  { return $this->link->real_escape_string($s); }
}
?>
