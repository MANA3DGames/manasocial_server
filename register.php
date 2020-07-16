<?php

// STEP 1: Declare user parameters for database connection:
// *******************************************************
// htmlentities : for security to prevent injection.
$email = htmlentities($_REQUEST["email"]);
$password = htmlentities($_REQUEST["password"]);
$firstname = htmlentities($_REQUEST["firstname"]);
$lastname = htmlentities($_REQUEST["lastname"]);

// Check if there is any missing parameters:
if ( empty( $email ) || empty( $password ) || empty( $firstname ) || empty( $lastname ) )
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information";
  echo json_encode( $returnArray );
  return;
}

// Secure password:
$salt = openssl_random_pseudo_bytes( 20 );
$secured_password = sha1( $password . $salt );


// STEP 2: Build connection.
// *******************************************************
// Get database info from .ini file:
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );

// Get database info.
$host = trim( $file["dbHost"] );
$user = trim( $file["dbUser"] );
$pass = trim( $file["dbPass"] );
$name = trim( $file["dbName"] );

// Include access.php to build the connection.
require( "secure/access.php" );
$access = new access( $host, $user, $pass, $name );
$access->connect();


// STEP 3: Insert user information.
// *******************************************************
$result = $access->registerUser( $email, $secured_password, $salt, $firstname, $lastname );

// Check if we registered successfully.
if ( $result )
{
  // Get current registered user information.
  $user = $access->selectUser( $email );

  // Save user information as a json.
  $returnArray["status"] = "200";
  $returnArray["message"] = "Successfully, registered a new user";
  $returnArray["id"] = $user["id"];
  $returnArray["email"] = $user["email"];
  $returnArray["firstname"] = $user["firstname"];
  $returnArray["lastname"] = $user["lastname"];
  $returnArray["ava"] = $user["ava"];
  echo json_encode( $returnArray );


  // STEP 3.1: Send email confirmation to the current registered user.
  // Include email.php file.
  require( "secure/email.php" );

  // Create a new instance of email.
  $email = new email();

  // Generate a new email $token.
  $token = $email->generateToken( 20 );

  // Register a new $token record in the 'tokenTable'.
  $access->saveToken( $user["id"], $token );

  // Create confirmaton details.
  $details = array();
  $details["subject"] = "Email Confirmation on ManaSocial";
  $details["to"] = $user["email"];
  $details["fromName"] = "ManaSocial Team";
  $details["fromEmail"] = "justfortesting002@gmail.com";
  $details["toFirstname"] = $user["firstname"];
  $details["toLastname"] = $user["lastname"];

  // Get templte html for confirmation email.
  $template = $email->getConfirmationTemplate();
  // Replace {token} in html $template by $token.
  $template = str_replace( "{token}", $token, $template );
  $details["body"] = $template;

  // Send the email now.
  $email->sendEmailWithSwiftMailer( $details );
}
else
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Could not register a new user with provided information";

  echo json_encode( $returnArray );
}


// STEP 4: Close connection.
// *******************************************************
$access->disconnect();


?>
