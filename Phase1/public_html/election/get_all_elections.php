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
 
// read elections will be here
$stmt = $election->get_all_elections();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // elections array
    $electons_arr=array();
    $electons_arr["records"]=array();
 
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['title'] to just $title only
        extract($row);

        $choices_arr=explode ("-", $list_of_choices);
 
        $election_item=array(
            "id" => $id,
            "title" => $title,
            "start_time" => $start_time,
            "end_time" => $end_time,
            "list_of_choices" => $choices_arr,
            "number_of_votes" => $number_of_votes
        );
 
        array_push($electons_arr["records"], $election_item);
    }
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show elections data in json format
    echo json_encode($electons_arr);
}
 
// no elections found will be here
else{
 
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user no elections found
    echo json_encode(
        array("message" => "No elections found.")
    );
}