<?php

// STEP 1: Get passed information.
if ( empty( $_REQUEST["email"] ) )
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information";
  echo json_encode( $returnArray );
  return;
}

$email = htmlentities( $_REQUEST["email"] );


// STEP 2: Build connection.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );

$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

// include access.php file to start connection to our database.
require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();


// STEP 3: Check if we have a user with the given email?
$user = $access->getUserByEmail( $email );

if ( empty( $user ) )
{
  $returnArray["status"] = "403";
  $returnArray["message"] = "Email was not found";
  echo json_encode( $returnArray );
  return;
}


// STEP 4: Emailing.
require( "secure/email.php" );
$emailInstance = new email();

// generate a unique token.
$token = $emailInstance->generateToken( 20 );

// Store the new generated token in our database.
$access->saveToken( "passwordTokens", $user["id"], $token );

// Prepare resetpassword email details.
$details = array();
$details["subject"] = "Reset Password on ManaSocial";
$details["to"] = $user["email"];
$details["fromName"] = "ManaSocial Team";
$details["fromEmail"] = "justfortesting002@gmail.com";
$details["toFirstname"] = $user["firstname"];
$details["toLastname"] = $user["lastname"];

// Get templte html for reset email.
$template = $emailInstance->getHtmlTemplate( "resetPasswordTemplate" );
// Replace {token} in html $template by $token.
$template = str_replace( "{token}", $token, $template );
$details["body"] = $template;

// Send the email now.
$emailInstance->sendEmailWithSwiftMailer( $details );



// STEP 5: Return json detail to our application.
$returnArray["email"] = $user["email"];
$returnArray["message"] = "We have sent you a reset password email request";
echo json_encode( $returnArray );


// STEP 6: Close connection.
$access->disconnect();










?>
