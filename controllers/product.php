<?php
class Product{

    // database connection and table name
    private $conn;
    private $table_name = "products";

    // object properties
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $category_name;
    public $created_at;
    public $updated_at;

    // constructor with $db as database connection
    public function __construct($conn){
        $this->conn = $conn;
    }

// read products
public function read(){

    // select all query
    $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created_at
              FROM " . $this->table_name . " as p
              LEFT JOIN categories as c ON p.category_id = c.id
              ORDER BY p.created_at DESC";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // execute query
     $stmt->execute();

     $stmt = $stmt->get_result();

    return $stmt;
}


// create product
public function create(){

    // query to insert record
    $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, created_at, updated_at)
              VALUES (?, ?, ?, ?, ?, ?)";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->name = htmlspecialchars(strip_tags($this->name));
    $this->price = htmlspecialchars(strip_tags($this->price));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->category_id = htmlspecialchars(strip_tags($this->category_id));
    $this->created_at = htmlspecialchars(strip_tags($this->created_at));
    $this->updated_at = NULL;

    // bind values
    $stmt->bind_param("ssiisb", $this->name, $this->description, $this->price, $this->category_id, $this->created_at, $this->updated_at);

    // execute query
    if($stmt->execute()){
        return true;
    }

    return false;

}


// used when filling up the update product form
public function readOne(){

    // query to read single record
    $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created_at,p.updated_at
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
