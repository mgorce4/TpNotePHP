<?php

namespace App\Models;

use PDO;
use PDOException;

class Database{
    private $host = 'db';
    private $port = '3306';
    private $db_name = 'TpNote';
    private $username = 'myuser';
    private $password = 'monpassword';
    private $conn;

    public function getConnection(){
        $this->conn = null;
        try{
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }catch(PDOException $exception){
            echo "Erreur de connexion : " . $exception->getMessage();
        }
        return $this->conn;
    }
}
