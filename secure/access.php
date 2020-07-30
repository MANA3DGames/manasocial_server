<?php

class access
{
  // Connection global variables.
  var $host = null;
  var $user = null;
  var $pass = null;
  var $name = null;
  var $conn = null;
  var $result = null;

  // Constructor.
  function __construct( $dbHost, $dbUser, $dbPass, $dbName )
  {
    $this->host = $dbHost;
    $this->user = $dbUser;
    $this->pass = $dbPass;
    $this->name = $dbName;
  }

  // Connection function.
  public function connect()
  {
    // Establish connection and store it in $conn.
    $this->conn = new mysqli( $this->host, $this->user, $this->pass, $this->name );

    // Check for error.
    if ( mysqli_connect_errno() )
    {
      echo "Could not connect to database.</br>";
    }

    // Support all languages.
    $this->conn->set_charset( "utf8" );
  }

  // Disconnection function.
  public function disconnect()
  {
    if ( $this->conn != null )
    {
      $this->conn->close();
    }
  }

  // Add a new user to our database.
  public function registerUser( $email, $password, $salt, $firstname, $lastname )
  {
    // Prepare sql query command.
    $sql = "INSERT INTO users SET email=?, password=?, salt=?, firstname=?, lastname=?, ava=?, emailConfirmed=?";

    // Store query result in $sqlPrepared.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for error?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Empty avatar for now.
    $avaVar = "";
    $emailConfirmed = 0;

    // Bind 5 string parameters (sssssi) with our sql command to be executed.
    $sqlPrepared->bind_param( "ssssssi", $email, $password, $salt, $firstname, $lastname, $avaVar, $emailConfirmed );


    // Execute our final binding sql command.
    $result = $sqlPrepared->execute();

    // Return the result of the executed sql command.
    return $result;
  }

  // Gets user by email.
  public function getUserByEmail( $email )
  {
    // Create sql command.
    $sql = "SELECT * FROM users WHERE email='" . $email . "'";

    // Execute the query.
    $result = $this->conn->query( $sql );

    // Check if we received any data?
    if ( $result != null && mysqli_num_rows( $result ) >= 1 )
    {
      // Get retrieved data.
      $row = $result->fetch_array( MYSQLI_ASSOC );

      if ( !empty( $row ) )
      {
        return $row;
      }
    }
  }

  // Returns user info by id
  public function getUserByID( $id )
  {
    // Create select sql command.
    $sql = "SELECT * FROM users WHERE id='" . $id . "'";

    // Execute $sql query.
    $result = $this->conn->query( $sql );

    // Check for error then get return data.
    if ( $result != null && mysqli_num_rows( $result ) >= 1 )
    {
      // Get user info as associated array.
      $row = $result->fetch_array( MYSQLI_ASSOC );

      // Check if row is empty?
      if ( !empty( $row ) )
      {
        return $row;
      }
    }
  }

  // Saves email confirmation message's token.
  public function saveToken( $tableName, $id, $token )
  {
    // Declare sql command.
    $sql = "INSERT INTO " . $tableName . " SET id=?, token=?";

    // Prepare sql statement to be executed.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind parameters (is : int string) to $sqlPrepared.
    $sqlPrepared->bind_param( "is", $id, $token );

    // Execute $sqlPrepared.
    $result = $sqlPrepared->execute();

    return $result;
  }

  // Returns user's id via a given $token which recieved by email.
  public function getUserID( $tableName, $token )
  {
    $returnArray = array();

    // Create select sql statment.
    $sql = "SELECT id FROM " . $tableName . " WHERE token = '" . $token . "'";

    // Execute the sql.
    $result = $this->conn->query( $sql );

    // Check if we have a vaild record.
    if ( $result != null && mysqli_num_rows( $result ) >= 1 )
    {
      // Get the row content as associated array from the result.
      $row = $result->fetch_array( MYSQLI_ASSOC );

      if ( !empty( $row ) )
      {
        $returnArray = $row;
      }
    }

    return  $returnArray;
  }

  // Update emailConfirmed status in the database.
  public function updateEmailConfirmationStatus( $status, $id )
  {
    // Create sql update statment to update the emailConfirmed status.
    $sql = "UPDATE users SET emailConfirmed=? WHERE id=?";
    // Prepare the sql command.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind target values with $sqlPrepared for final execution.
    $sqlPrepared->bind_param( "ii", $status, $id );

    // Execute binded $sqlPrepared.
    $result = $sqlPrepared->execute();

    return $result;
  }

  // Deletes token record from $tableName once the user confirmed his/her email address.
  public function deleteToken( $tableName, $token )
  {
    // Create sql command to delete the target record.
    $sql = "DELETE FROM " . $tableName . " WHERE token=?";

    // prepare the delete sql.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind given values with our $sqlPrepared for final execution.
    $sqlPrepared->bind_param( "s", $token );

    // Execute binded $sqlPrepared command.
    $result = $sqlPrepared->execute();

    return $result;
  }

  // Updates user's password information.
  public function updateUserPassword( $id, $password, $salt )
  {
    // Create $sql command to update password & salt record.
    $sql = "UPDATE users SET password=?, salt=? WHERE id=?";
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind given parameters with our $sqlPrepared statment.
    $sqlPrepared->bind_param( "ssi", $password, $salt, $id );

    // Exectue $sqlPrepared.
    $result = $sqlPrepared->execute();

    return $result;
  }

  // Updates avatar's path for user with the given $id.
  public function updateAvaPath( $path, $id )
  {
    // Create sql command to update ava.
    $sql = "UPDATE users SET ava=? WHERE id=?";

    // Prepare sql command.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind parameters with $sqlPrepared.
    $sqlPrepared->bind_param( "si", $path, $id );

    // Execute $sqlPrepared.
    $result = $sqlPrepared->execute();

    return $result;
  }

  // Inserts a new post to database.
  public function insertPost( $id, $uuid, $text, $path )
  {
    // Create insert sql command to insert post.
    $sql = "INSERT INTO posts SET id=?, uuid=?, text=?, path=?";

    // Prepare $sql to be executed.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind given parameters with $sqlPrepared.
    $sqlPrepared->bind_param( "isss", $id, $uuid, $text, $path );

    // Execute $sqlPrepared.
    $result = $sqlPrepared->execute();

    return $result;
  }

  // Selects all user's posts
  public function selectUserPosts( $id )
  {
    $returnArray = array();

    // Create sql select/join command.
    $sql = "SELECT
    posts.id,
    posts.uuid,
    posts.text,
    posts.path,
    posts.date,
    users.id,
    users.firstname,
    users.lastname,
    users.email,
    users.ava
    FROM ManaSocial.posts JOIN ManaSocial.users ON
    posts.id = $id AND users.id = $id ORDER BY date DESC";

    // Prepare $sql to be executed.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Execute $sqlPrepared.
    $sqlPrepared->execute();

    // Get results from executed $sqlPrepared.
    $result = $sqlPrepared->get_result();

    // Append row one by one.
    while ( $row = $result->fetch_assoc() )
    {
      $returnArray[] = $row;
    }

    return $returnArray;
  }

  // Delete post with given $uuid
  public function deletePost( $uuid )
  {
    // Create sql command to delete the post record.
    $sql = "DELETE FROM posts WHERE uuid=?";

    // Perpare $sql to be executed.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind given paramter(s) with $sqlPrepared.
    $sqlPrepared->bind_param( "s", $uuid );

    // Execute binded $sqlPrepared.
    $sqlPrepared->execute();

    // Get number of affected rows with the previous command (DELETE)
    $result = $sqlPrepared->affected_rows;

    return $result;
  }

  // Search users table with certain keyword
  public function searchUsers( $keyword, $id )
  {
    $returnArray = array();

    // Create sql command to SELECT user(s).
    $sql = "SELECT id, firstname, lastname, ava FROM users WHERE NOT id='" . $id . "'";

    // Check if $keyword in not empty?
    if ( !empty( $keyword ) )
    {
      $sql .= " AND ( firstname LIKE ? OR lastname LIKE ? )";
    }

    // Prepare $sql command to be executed.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for errors?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Again check if we have a $keyword.
    if ( !empty( $keyword ) )
    {
      $keyword = '%' . $keyword . '%';
      $sqlPrepared->bind_param( "ss", $keyword, $keyword );
    }

    // Execute $sqlPrepared.
    $sqlPrepared->execute();

    $result = $sqlPrepared->get_result();

    while ( $row = $result->fetch_assoc() )
    {
      $returnArray[] = $row;
    }

    return $returnArray;
  }

  // Updates user information.
  public function updateUser( $firstname, $lastname, $email, $id )
  {
    // Create sql command to UPDATE user information.
    $sql = "UPDATE users SET firstname=?, lastname=?, email=? WHERE id=?";

    // Prepare $sql command to be executed.
    $sqlPrepared = $this->conn->prepare( $sql );

    // Check for error?
    if ( !$sqlPrepared )
    {
      throw new Exception( $sqlPrepared->error );
    }

    // Bind parameters.
    $sqlPrepared->bind_param( "sssi", $firstname, $lastname, $email, $id );

    // Execute $sqlPrepared.
    $result = $sqlPrepared->execute();

    return $result;
  }

}


















?>
