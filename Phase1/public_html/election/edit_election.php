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

if(
    !empty($data->id) &&
    !empty($data->title) &&
    !empty($data->start_time) &&
    !empty($data->end_time) &&
    !empty($data->list_of_choices)&&
    !empty($data->number_of_votes)
){
 
    // set ID property of election to be edited
    $election->id = $data->id;

    // set election property values
    $election->title = $data->title;
    $election->start_time = $data->start_time;
    $election->end_time = $data->end_time;
    $election->list_of_choices = $data->list_of_choices;
    $election->number_of_votes = $data->number_of_votes;
    
    // update the election
    if($election->edit_election()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "election was updated."));
    }
    
    // if unable to update the election, tell the user
    else{
    
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update election."));
    }
}
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to Update election. Data is incomplete."));
}
?>