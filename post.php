<?php

$returnArray = array();

if ( empty( $_REQUEST["id"] ) || empty( $_REQUEST["uuid"] ) || empty( $_REQUEST["text"] ) )
{
  $returnArray["status"] = "400";
  $returnArray["message"] = "Missing required information.";
  echo json_encode( $returnArray );
  return;
}

// Get provided information.
$id = htmlentities( $_REQUEST["id"] );
$uuid = htmlentities( $_REQUEST["uuid"] );
$text = htmlentities( $_REQUEST["text"] );

// Get/create current user posts folder.
$root = $_SERVER["DOCUMENT_ROOT"];
$folder = $root . "/manasocialdata/" . $id . "/posts";

// Check if folder doesn't exist.
if ( !file_exists( $folder ) )
{
  // Create new folder.
  if ( !mkdir( $folder, 0777, true ) )
  {
    // Failed to create a new $folder.
    $returnArray["status"] = "400";
    $returnArray["message"] = "Faild to create a new folder " . $folder;
    echo json_encode( $returnArray );
    return;
  }
}

// Move uploaded file.
$folder = $folder . "/" . basename( $_FILES["file"]["name"] );
$path = "";

if ( move_uploaded_file( $_FILES["file"]["tmp_name"], $folder ) )
{
  $returnArray["status"] = "200";
  $returnArray["message"] = "Post with image has been made successfully.";

  // Create a new path for new uploaded image.
  $path = "http://192.168.64.2/manasocialdata/" . $id . "/posts/post-" . $uuid . ".jpg";
}
else
{
  $returnArray["status"] = "200";
  $returnArray["message"] = "Post has been made successfully.";
}

// Build secure connectionto database.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );
$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();

// Save post detials.
$access->insertPost( $id, $uuid, $text, $path );

// Close connection.
$access->disconnect();

// Send json feedback to our application.
echo json_encode( $returnArray );





?>
