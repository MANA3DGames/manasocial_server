<?php

// Build connection to our database.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );

$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

// include access.php
require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();


$returnArray = array();

// Get provided information.
$id = htmlentities( $_REQUEST["id"] );

$keyword = null;
if ( !empty( $_REQUEST["keyword"] ) )
{
  $keyword = htmlentities( $_REQUEST["keyword"] );
}
else
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information.";
  $access->disconnect();
  echo json_encode( $returnArray );
  return;
}

// Start searching for users similar to the given name '$keyword'.
$users = $access->searchUsers( $keyword, $id );


// Check if we have found any users?
if ( !empty( $users ) )
{
  $returnArray["status"] = "200";
  $returnArray["message"] = "Found matched user(s).";
  $returnArray["users"] = $users;
}
else
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Faild to find any user with the provided information.";
}

// Close connection.
$access->disconnect();


// Send feedback to our application.
echo json_encode( $returnArray );

?>
