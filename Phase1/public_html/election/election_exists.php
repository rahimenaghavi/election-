<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
// include database and object files
include_once '../config/database.php';
include_once '../objects/election.php';
 
// instantiate database and election object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$election = new election($db);

// get election id
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id))
{
 
    // set ID property of election
    $election->id = $data->id;
 

    $stmt = $election->election_exists();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($row['id']!="")
    {
        // set response code - 200 OK
        http_response_code(200);
        echo json_encode(array("message" => "Election Exists."));
    }
    else
    {
        // set response code - 404 Not found
        http_response_code(404);
 
        // tell the user no elections found
        echo json_encode(array("message" => "Election Dosn't Exists."));
    }
}
 
// no elections found will be here
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user no elections found
    echo json_encode(
        array("message" => "Unable to check. Data is incomplete.")
    );
}