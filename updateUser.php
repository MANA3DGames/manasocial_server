<?php

$returnArray = array();

// Check if we have all the required information?
if ( empty( $_REQUEST["firstname"] ) || empty( $_REQUEST["lastname"] ) || empty( $_REQUEST["email"] ) || empty( $_REQUEST["id"] ) )
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information";
  echo json_encode( $returnArray );
  return;
}

// Get provided information.
$firstname = htmlentities( $_REQUEST["firstname"] );
$lastname = htmlentities( $_REQUEST["lastname"] );
$email = htmlentities( $_REQUEST["email"] );
$id = htmlentities( $_REQUEST["id"] );

// Build connection.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );
$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();


// Update user information.
$result = $access->updateUser( $firstname, $lastname, $email, $id );

if ( !empty( $result ) )
{
  $user = $access->getUserByID( $id );

  $returnArray["status"] = "200";
  $returnArray["message"] = "User information has been updated successfully.";
  $returnArray["id"] = $user["id"];
  $returnArray["firstname"] = $user["firstname"];
  $returnArray["lastname"] = $user["lastname"];
  $returnArray["email"] = $user["email"];
  $returnArray["ava"] = $user["ava"];
}
else
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Failed to update user information.";
}


// Close connection.
$access->disconnect();


// Send back json to our application.
echo json_encode( $returnArray );






?>
