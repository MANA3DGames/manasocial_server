<?php

// Build secure connectionto database.
$file = parse_ini_file( "../ManaSocialDatabaseInfo.ini" );
$dbHost = trim( $file["dbHost"] );
$dbUser = trim( $file["dbUser"] );
$dbPass = trim( $file["dbPass"] );
$dbName = trim( $file["dbName"] );

require( "secure/access.php" );
$access = new access( $dbHost, $dbUser, $dbPass, $dbName );
$access->connect();

$root = $_SERVER["DOCUMENT_ROOT"];

$returnArray = array();

// INSERT NEW POST.
if ( !empty( $_REQUEST["id"] ) && !empty( $_REQUEST["uuid"] ) && !empty( $_REQUEST["text"] ) )
{
  // Get provided information.
  $id = htmlentities( $_REQUEST["id"] );
  $uuid = htmlentities( $_REQUEST["uuid"] );
  $text = htmlentities( $_REQUEST["text"] );

  // Get/create current user posts folder.
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


  // Save post detials.
  $access->insertPost( $id, $uuid, $text, $path );
}
// DELETE POST.
else if ( !empty( $_REQUEST["uuid"] ) && empty( $_REQUEST["id"] ) )
{
  // Get uuid & path information.
  $uuid = htmlentities( $_REQUEST["uuid"] );
  $path = htmlentities( $_REQUEST["path"] );

  // Delete post according to uuid.
  $result = $access->deletePost( $uuid );

  if ( !empty( $result ) )
  {
    $returnArray["status"] = "200";
    $returnArray["message"] = "Post was deleted successfully.";
    $returnArray["result"] = $result;

    // Delete image file.
    if ( !empty( $path ) )
    {
      $path = str_replace( "http://192.168.64.2/", $root . "/", $path );

      // Delete the file.
      if ( unlink( $path ) )
      {
        $returnArray["status"] = "1000";
      }
      else
      {
        $returnArray["status"] = "400";
        $returnArray["message"] = "Failed to delete post image.";
      }
    }
  }
  else
  {
    $returnArray["status"] = "403";
    $returnArray["message"] = "Failed to delete targe post.";
  }
}
// SELECT ALL POSTS.
else if ( !empty( $_REQUEST["id"] ) )
{
  // Get provided user id.
  $id = htmlentities( $_REQUEST["id"] );

  // Get all user's posts.
  $posts = $access->selectUserPosts( $id );

  // Check if we got any post records?
  if ( !empty( $posts ) )
  {
    $returnArray["status"] = "200";
    $returnArray["message"] = "User's posts have been downloaded successfully.";
    $returnArray["posts"] = $posts;
  }
}
// Close connection.
$access->disconnect();

// Send json feedback to our application.
echo json_encode( $returnArray );





?>
