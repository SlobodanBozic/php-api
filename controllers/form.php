<?php
class Form{

    // database connection and table name
    private $conn;
    private $table_name = "users";

    // object properties
    private $email;
    private $password;
    private $first_name;
    private $last_name;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }



// login User
  public function getUserEmail() {
    return $this->email;
  }

  public function setUserEmail($email) {
    if(!is_string($email)){
     throw new Exception('$email must be a string!');
    }
    $this->email = $email;
  }

  public function getUserPassword() {
    return $this->password;
  }

  public function setUserPassword($password) {
    if(!is_string($password)){
     throw new Exception('$password must be a string!');
    }
    $this->password = $password;
  }

public function login(){

    // select all query
    $query = "SELECT id, first_name, last_name, password,email FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    $stmt->bind_param("s", $this->email);
    // execute query
     $stmt->execute();

     $stmt = $stmt->get_result();

    return $stmt;

  }

// Register User
  public function setUserFirstName($first_name) {
    if(!is_string($first_name)){
     throw new Exception('$first_name must be a string!');
    }
    $first_name = htmlspecialchars(strip_tags($first_name));
    $this->first_name = $first_name;
  }

  public function setUserLastName($last_name) {
    if(!is_string($last_name)){
     throw new Exception('$last_name must be a string!');
    }
    $last_name = htmlspecialchars(strip_tags($last_name));
    $this->last_name = $last_name;
  }

public function register(){

    // query to insert record
    $query = "INSERT INTO " . $this->table_name . " (first_name, last_name, email, password)
              VALUES (?, ?, ?, ?)";
    // prepare query
    $stmt = $this->conn->prepare($query);

    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

    // bind values
    $stmt->bind_param("ssss", $this->first_name, $this->last_name, $this->email, $password_hash);

    // execute query
    if($stmt->execute()){
      return true;
    }
      return false;
}

// used when filling up the update product form
public function readOne(){

    // query to read single record
    $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created_at
              FROM " . $this->table_name . " p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.id = ? LIMIT 0,1";



    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // bind id of product to be updated
    $stmt->bind_param("i", $this->id);

    // execute query
    $stmt->execute();

    $result = $stmt->get_result();


    // get retrieved row
    $row = $result->fetch_assoc();

    // set values to object properties
    $this->name = $row['name'];
    $this->price = $row['price'];
    $this->description = $row['description'];
    $this->category_id = $row['category_id'];
    $this->category_name = $row['category_name'];

}




// update the product
public function update(){

    // update query
    $query = "UPDATE " . $this->table_name . "
              SET name = ?, price = ?, description = ?, category_id = ?
              WHERE id = ?";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->price=htmlspecialchars(strip_tags($this->price));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    $this->id=htmlspecialchars(strip_tags($this->id));

    // bind new values
    $stmt->bind_param("sisii", $this->name,$this->price,$this->description,$this->category_id,$this->id);


    // execute the query
    if($stmt->execute()){
        return true;
    }

    return false;
}


// delete the product
public function delete(){

    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id=htmlspecialchars(strip_tags($this->id));

    // bind id of record to delete
      $stmt->bind_param("i", $this->id);

    // execute query
    if($stmt->execute()){
        return true;
    }

    return false;

}



// search products
public function search($keywords){

      // select all query
      $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created_at
                  FROM " . $this->table_name . " p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
                  ORDER BY p.created_at DESC";

      // prepare query statement
      $stmt = $this->conn->prepare($query);

      // sanitize
      $keywords = htmlspecialchars(strip_tags($keywords));
      $keywords = "%{$keywords}%";


      // bind
       $stmt->bind_param("sss", $keywords, $keywords, $keywords);

      // execute query
      $stmt->execute();
      $stmt = $stmt->get_result();

      return $stmt;
  }


// read products with pagination
public function readPaging($from_record_num, $records_per_page){

    // select query
    $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created_at
            FROM " . $this->table_name . " p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC LIMIT ? OFFSET ?";

    // prepare query statement
    $stmt = $this->conn->prepare( $query );

    // bind variable valuess
    $stmt->bind_param("ii",$records_per_page, $from_record_num);

    // execute query
    $stmt->execute();
    $stmt = $stmt->get_result();

    // return values from database
    return $stmt;
}

// used for paging products
public function count(){

    $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    $stmt = $stmt->get_result();

    while ($row = $stmt->fetch_assoc()){
      extract($row);
    return $row['total_rows'];

    }
}


}
?>
