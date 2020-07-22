<?php

$returnArray = array();

// STEP 1: Get passed info.
if ( empty( $_REQUEST["id"] ) )
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information.";
  echo json_encode( $returnArray );
  return;
}

$id = htmlentities( $_REQUEST["id"] );


// STEP 2: Get/Create a folder for target user.
$root = $_SERVER["DOCUMENT_ROOT"];
$folder = $root . "/manasocialdata/" . $id;

if ( !file_exists( $folder ) )
{
  if ( !mkdir( $folder, 0777, true ) )
  {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Failed to create a new folder " . $folder;
    echo json_encode( $returnArray );
    return;
  }
}


// STEP 3: move uploaded file.
$folder = $folder . "/" . basename( $_FILES["file"]["name"] );

if ( move_uploaded_file( $_FILES["file"]["tmp_name"], $folder ) )
{
  $returnArray["status"] = "200";
  $returnArray["message"] = "The file has been uploaded.";
}
else
{
  $returnArray["status"] = "300";
  $returnArray["message"] = "Error while uploading";
  echo json_encode( $returnArray );
  return;
}


// STEP 4: Setup conneciton to database.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );

$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

// Include access.php file to create a new access to the database.
require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();



// STEP 5: Save path to uploaded file in our database.
$path = "http://192.168.64.2/manasocialdata/" . $id . "/ava.jpg";
$access->updateAvaPath( $path, $id );


// STEP 6: Get user new information after updating.
$user = $access->getUserByID( $id );

if ( !empty( $user ) )
{
  $returnArray["id"] = $user["id"];
  $returnArray["email"] = $user["email"];
  $returnArray["firstname"] = $user["firstname"];
  $returnArray["lastname"] = $user["lastname"];
  $returnArray["ava"] = $user["ava"];
}
else
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "User with the given id was not found!";
}


// STEP 7: Close connection.
$access->disconnect();


// STEP 8: Send information back to the requester app.
echo json_encode( $returnArray );







?>
