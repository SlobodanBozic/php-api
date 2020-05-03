<?php
class Database{

    // specify your own database credentials
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $db_name = "php-api_db";
    public $conn;

    // get the database connection
    public function getConnection(){

            $this->conn = null;

            $this->conn =  new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4");

            // Check database connection
            if ($this->conn === false){
              echo "Failed to connect to MySQL: " . $this->conn->connect_error;
              }

        return $this->conn;
    }
}
?>
