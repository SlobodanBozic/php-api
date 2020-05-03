<?php
include_once '../config/database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

$token = null;
if (isset($_GET['token'])) {$token = $_GET['token'];}

// print_r($token);die;

if (!is_null($token)) {

$secret_key = "5f2b5cdbe5194f10b3241568fe4e2b24";


      try {
        http_response_code(200);

          $decoded = JWT::decode($token, $secret_key, array('HS256'));

          $user_email = $decoded->data->email;

          echo json_encode(array(
              "userId" => $user_email,
          ));

      }catch (Exception $e){

      http_response_code(401);

      echo json_encode(array(
          "message" => "Access denied.",
          "error" => $e->getMessage()
      ));
    }



}

 ?>
