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
    public $created;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
function read(){

    // select all query
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY
                p.created DESC";



      // $query = "SELECT * FROM products WHERE name =  'Google Nexus 4' ";
        // $query = "SELECT name FROM products WHERE id =?";

    // $stmt = $this->conn->query($query);

      // $id = 2;


    // prepare query statement
    $stmt = $this->conn->prepare($query);
    // $stmt->bind_param("i",$id);



    // execute query
     $stmt->execute();
     $stmt = $stmt->get_result();


    return $stmt;
}

// create product
function create(){

    // query to insert record
    // $query = "INSERT INTO
    //             " . $this->table_name . "
    //         SET
    //             name=:name, price=:price, description=:description, category_id=:category_id, created=:created";

                $query = "INSERT INTO products (name, price, description, category_id)
                VALUES (?, ?, ?, ?)";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->price=htmlspecialchars(strip_tags($this->price));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    // $this->created=htmlspecialchars(strip_tags($this->created));

    // bind values
    // $stmt->bind_param(":name", $this->name);
    // $stmt->bind_param(":price", $this->price);
    // $stmt->bind_param(":description", $this->description);
    // $stmt->bind_param(":category_id", $this->category_id);
    // $stmt->bind_param(":created", $this->created);

    $stmt->bind_param("sisi", $this->name,$this->price,$this->description,$this->category_id);

    // $stmt->bind_param("d", $this->created);


    // execute query
    if($stmt->execute()){
        return true;
    }

    return false;

}

// used when filling up the update product form
function readOne(){

    // query to read single record
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.id = ?
            LIMIT
                0,1";

    // prepare query statement
    $stmt = $this->conn->prepare( $query );

    // bind id of product to be updated
    $stmt->bind_param("i", $this->id);

    // execute query
    $stmt->execute();

    $result = $stmt->get_result();

    // get retrieved row
    // $row = $stmt->fetch(PDO::FETCH_ASSOC);
    while($row = $result->fetch_assoc()){

    // set values to object properties
    $this->name = $row['name'];
    $this->price = $row['price'];
    $this->description = $row['description'];
    $this->category_id = $row['category_id'];
    $this->category_name = $row['category_name'];
    }

}

// update the product
function update(){

    // update query
    $query = "UPDATE
                " . $this->table_name . "
            SET
                name = ?,
                price = ?,
                description = ?,
                category_id = ?
            WHERE
                id = ?";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->price=htmlspecialchars(strip_tags($this->price));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    $this->id=htmlspecialchars(strip_tags($this->id));

    // bind new values
    // $stmt->bind_param(':name', $this->name);
    // $stmt->bind_param(':price', $this->price);
    // $stmt->bind_param(':description', $this->description);
    // $stmt->bind_param(':category_id', $this->category_id);
    // $stmt->bind_param(':id', $this->id);
    $stmt->bind_param("sisii", $this->name,$this->price,$this->description,$this->category_id,$this->id);


    // execute the query
    if($stmt->execute()){
        return true;
    }

    return false;
}

// delete the product
function delete(){

    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

    // prepare query
    $stmt = $this->conn->prepare($query);

    // sanitize
    $this->id=htmlspecialchars(strip_tags($this->id));

    // bind id of record to delete
    // $stmt->bind_param(1, $this->id);
      $stmt->bind_param("i", $this->id);

    // execute query
    if($stmt->execute()){
        return true;
    }

    return false;

}

// search products
function search($keywords){

    // select all query
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY
                p.created DESC";

    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // sanitize
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";

    // bind
    // $stmt->bind_param(1, $keywords);
    // $stmt->bind_param(2, $keywords);
    // $stmt->bind_param(3, $keywords);
     $stmt->bind_param("sss", $keywords, $keywords, $keywords);

    // execute query
    $stmt->execute();
    $stmt = $stmt->get_result();

    return $stmt;
}

// read products with pagination
public function readPaging($from_record_num, $records_per_page){

    // select query
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY p.created DESC
            LIMIT ?, ?";

    // prepare query statement
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    // $stmt->bind_param(1, $from_record_num, PDO::PARAM_INT);
    // $stmt->bind_param(2, $records_per_page, PDO::PARAM_INT);
    $stmt->bind_param("ii",$from_record_num, $records_per_page);

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

    // $row = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($row = $stmt->fetch_assoc()){
      extract($row);
    return $row['total_rows'];

    }
}

}
?>
