<?php

// 2nd load for this page.

// STEP 1: Check provided information.
if ( !empty( $_POST["password"] ) && !empty( $_POST["confirm_password"] ) && !empty( $_POST["token"] ) )
{
  $password = htmlentities( $_POST["password"] );
  $confirm_password = htmlentities( $_POST["confirm_password"] );
  $token = htmlentities( $_POST["token"] );

  // Check if given passwords are matched?
  if ( $password == $confirm_password )
  {
    // STEP 2: Build connection to database.
    $file = parse_ini_file( "../../ManaSocialDatabaseInfo.ini" );

    $dbHost = trim( $file["dbHost"] );
    $dbUser = trim( $file["dbUser"] );
    $dbPass = trim( $file["dbPass"] );
    $dbName = trim( $file["dbName"] );

    // include access.php to start cnnection.
    require( "../secure/access.php" );
    $access = new access( $dbHost, $dbUser, $dbPass, $dbName );
    $access->connect();


    // STEP 3: Get user id by token.
    $user = $access->getUserID( "passwordTokens", $token );

    // Check if we have a vaild record for this token?
    if ( !empty( $user ) )
    {
      // STEP 4: Generate a new secured password.
      $salt = openssl_random_pseudo_bytes( 20 );
      $secured_password = sha1( $password . $salt );

      // STEP 5: Update user password information in our database.
      $result = $access->updateUserPassword( $user["id"], $secured_password, $salt );

      if ( $result )
      {
        // Delete unique token from passwordTokens table.
        $access->deleteToken( "passwordTokens", $token );
        $message = "Your password was updated successfully";

        // Redirect to another page.
        header( "Location:displayResetPasswordResult.php?message=" . $message );
      }
      else
      {
        $message = "Could not find a reset request for this user id";
      }
    }
  }
  else
  {
    $message = "Password do not match each other";
  }
}


?>



<!-- 1st load of this page -->
<html>

  <head>
    <title>Create a new password form</title>

    <!-- CSS style -->
    <style>
      .password_field
      {
        margin: 10px;
      }

      .button
      {
        margin: 10p;
      }
    </style>

  </head>

  <body>

    <h2>Create a new password</h2>

    <?php
      if ( !empty( $message ) )
      {
        echo "</br>" . $message . "</br>";
      }
    ?>

    <!-- Input new password form -->
    <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
      <div><input type="password" name="password" placeholder="new password" class="password_field"></div>
      <div><input type="password" name="confirm_password" placeholder="Confirm new password" class="password_field"></div>
      <div><input type="submit" value="Confirm" class="button"></div>
      <input type="hidden" value="<?php echo $_GET['token']; ?>" name="token">
    </form>

  </body>

</html>
