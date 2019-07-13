<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate election object
include_once '../objects/election.php';
 
$database = new Database();
$db = $database->getConnection();
 
$election = new election($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
if(
    !empty($data->title) &&
    !empty($data->start_time) &&
    !empty($data->end_time) &&
    !empty($data->list_of_choices)&&
    !empty($data->number_of_votes)
){
 
    // set election property values
    $election->title = $data->title;
    $election->start_time = $data->start_time;
    $election->end_time = $data->end_time;
    $election->list_of_choices = $data->list_of_choices;
    $election->number_of_votes = $data->number_of_votes;
 
    // create the election
    if($election->create_election()){
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Election was created."));
    }
 
    // if unable to create the election, tell the user
    else{
 
        // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Unable to create election."));
    }
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create election. Data is incomplete."));
}
?>
