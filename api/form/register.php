<?php
include_once '../../config/database.php';
include_once '../../controllers/form.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->getConnection();

$register = new Form($db);
$data = json_decode(file_get_contents("php://input"));

$register->setUserFirstName($data->first_name);
$register->setUserLastName($data->last_name);
$register->setUserEmail($data->email);
$register->setUserPassword($data->password);

if($register->register()){
    http_response_code(200);
    echo json_encode(array("message" => "User was successfully registered."));
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register the user."));
}
?>
