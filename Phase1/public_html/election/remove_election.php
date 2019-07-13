<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object file
include_once '../config/database.php';
include_once '../objects/election.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare election object
$election = new election($db);
 
// get election id
$data = json_decode(file_get_contents("php://input"));
 
// set election id to be deleted
$election->id = $data->id;
 
// delete the election
if($election->remove_election()=="2"){
 
    // set response code - 200 ok
    http_response_code(200);
 
    // tell the user
    echo json_encode(array("message" => "election was deleted."));
}
 
// if unable to delete the election
elseif($election->remove_election()=="3"){
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to delete election."));
}
elseif($election->remove_election()=="1"){
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to delete election.The Election Is Running."));
}
?>