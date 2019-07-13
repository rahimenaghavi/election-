<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/election.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare election object
$election = new election($db);
 
// get election id
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id))
{
    // set ID property of election to be retrived
    $election->id = $data->id;

    // read the details of election to be retrived
    $election->get_election_detail();
    
    if($election->title!=null){

        $choices_arr=explode ("-", $election->list_of_choices);

        // create array
        $election_arr = array(
            "id" =>  $election->id,
            "title" => $election->title,
            "start_time" => $election->start_time,
            "end_time" => $election->end_time,
            "list_of_choices" => $choices_arr,
            "number_of_votes" => $election->number_of_votes
    
        );
    
        // set response code - 200 OK
        http_response_code(200);
    
        // make it json format
        echo json_encode($election_arr);
    }
    
    else{
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user election does not exist
        echo json_encode(array("message" => "election does not exist."));
    }
}
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to get election detail. Data is incomplete."));
}
?>