<?php


// STEP 1: Check given parameters.
$token = htmlentities( $_GET["token"] );

if ( empty( $token ) )
{
  echo "Missing required information";
}


// STEP 2: Build connection to database.
// using secure way:
$file = parse_ini_file( "../../ManaSocialDatabaseInfo.ini" );

// Store database info in php variables:
$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

// Include access.php.
require( "../secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();


// STEP 3: Get user's id via token.
$id = $access->getUserID( $token );

if ( empty( $id["id"] ) )
{
  echo "Could not find a user with the provided token!";
  return;
}

// STEP 4: Change emailConfirmed status and delete token record from emailTokens table.
$result = $access->updateEmailConfirmationStatus( 1, $id["id"] );

if ( $result )
{
  // STEP 4.1: Delete token record from emailTokens table.
  $access->deleteTokenFromEmailTokensTable( $token );

  echo "Congratulation, Your email is now confirmed</br>";
}


// STEP 5: Close connection.
$access->disconnect();




?>
