<?php
include_once '../../config/database.php';
require "../../vendor/autoload.php";
include_once '../../controllers/form.php';
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$email = '';
$password = '';

$databaseService = new Database();
$db = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$login = new Form($db);

$login->setUserEmail($data->email);
$login->setUserPassword($data->password);

$stmt = $login->login();
$num = $stmt->num_rows;

if($num > 0){

    $row = $stmt->fetch_assoc();

    extract($row);

    // $id = $row['id'];
    // $firstname = $row['first_name'];
    // $lastname = $row['last_name'];
    // $email = $row['email'];
    // $password2 = $row['password'];

    if(password_verify($login->getUserPassword(), $password))
    {
        $secret_key = "5f2b5cdbe5194f10b3241568fe4e2b24";
        // $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "THE_ISSUER"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 1; //not before in seconds
        $expire_claim = $issuedat_claim + 3600; // expire time in seconds

        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
                "firstname" => $first_name,
                "lastname" => $last_name,
                "email" => $email
        ));

        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "expire_claim" => $expire_claim,
            ));

    }
    else{
        http_response_code(401);
        echo json_encode(array("message" => "Login failed.", "password" => $login->getUserPassword()));
    }
}
?>
