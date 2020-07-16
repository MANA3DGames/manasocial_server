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
      echo "Could not connect to database.";
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

  // Gets user data.
  public function selectUser( $email )
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

    return null;
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

  // Returns user information from database.
  public function getUser( $email )
  {
    $returnArray = array();

    // Create select sql command.
    $sql = "SELECT * FROM users WHERE email = '" . $email . "'";

    // Execute $sql query.
    $result = $this->conn->query( $sql );

    // Check for error then get return data.
    if ( $result != null && mysqli_num_rows( $result ) >= 1 )
    {
      // Get user info as associated array.
      $returnArray = $result->fetch_array( MYSQLI_ASSOC );

      // Check if row is empty?
      if ( empty( $returnArray ) )
      {
        echo "Could not find a user with this email";
      }
    }

    return $returnArray;
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

}













?>
