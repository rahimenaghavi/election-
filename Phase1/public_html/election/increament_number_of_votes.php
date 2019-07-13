<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/election.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare election object
$election = new election($db);
 
// get id of election to be edited
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id))
{
 
    // set ID property of election to be edited
    $election->id = $data->id;
    
    // update the election
    if($election->increament_number_of_votes()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Number of Votes Is Increamented."));
    }
    
    // if unable to update the election, tell the user
    else{
    
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to increamnt Number Of Votes."));
    }
}
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create election. Data is incomplete."));
}
?>