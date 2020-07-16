<?php

// STEP 1: Check variables passing to this file via POST.
$email = htmlentities( $_REQUEST["email"] );
$password = htmlentities( $_REQUEST["password"] );

if ( empty( $email ) || empty( $password ) )
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information";
  echo json_encode( $returnArray );
  return;
}


// STEP 2: Setup conneciton to database.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );

$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

// Include access.php file to create a new access to the database.
require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();


// STEP 3: Get the user from our database.
$user = $access->getUser( $email );

// Cehck if we didn't get any user.
if ( empty( $user ) )
{
  $returnArray["status"] = "403";
  $returnArray["message"] = "User was not found";
  echo json_encode( $returnArray );
  return;
}


// STEP 4: password validity?
$secured_password =  trim( $user["password"] );
$salt = $user["salt"];
$cmpPassword = trim( sha1( trim( $password ) . $salt ) );

// NOTE! for some reaseon '==' operator is not working here so I'm using strcmp func instead of it.
if ( $secured_password == $cmpPassword )
{
  $returnArray["status"] = "200";
  $returnArray["message"] = "Logged in successfully";
  $returnArray["id"] = $user["id"];
  $returnArray["email"] = $user["email"];
  $returnArray["firstname"] = $user["firstname"];
  $returnArray["lastname"] = $user["lastname"];
  $returnArray["ava"] = $user["ava"];
}
else
{
  $returnArray["status"] = "403";
  $returnArray["message"] = "Incorrect password";
}



// STEP 5: Close connection.
$access->disconnect();


// STEP 6: Send back all information to our front-end.
echo json_encode( $returnArray );











?>
