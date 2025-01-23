<?php
class Database {
    private $host = "ibmice.czgm84kc6wqr.ap-south-1.rds.amazonaws.com";
    private $username = "iceadmin";
    private $password = "TheAnalytix";
    private $connection;
    private $dbName;

    public function __construct($dbName = "ibmprojects") {
        $this->dbName = $dbName;
    }

    public function getConnection() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            return $this->connection;
        } catch(PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }
}

