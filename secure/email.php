<?php

class email
{
  // Generates a unique token for user when the user got a confirmation email.
  function generateToken($length)
  {
    // Random pool of characters to used to choose from.
    $characters = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";

    // Get length of the characters pool that we have just defined.
    $charactesLength = strlen( $characters );

    // Declare a $token
    $token = "";

    // Generate random char from $characters in every iteration and append it to $token.
    for ( $i = 0; $i < $length; $i++ )
    {
      $token .= $characters[rand(0, $charactesLength-1)];
    }

    // Return final value of $token.
    return $token;
  }

  // Opens confirmation template file and gets it html content.
  function getConfirmationTemplate()
  {
    $path = "templates/confirmationTemplate.html";

    // Open the file.
    $file = fopen( $path, "r" ) or die( "Unable to open file" );

    // Read html content and save it as string.
    $template = fread( $file, filesize( $path ) );

    // Close the file.
    fclose( $file );

    // Return template content.
    return $template;
  }

  // Sends email with php
  function sendEmail( $details )
  {
    // Information of email.
    $subject = $details["subject"];
    $to = $details["to"];
    $fromName = $details["fromName"];
    $fromEmail = $details["fromEmail"];
    $body = $details["body"];

    // Header required by some smtp or emails.
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;content=UTF-8" . "\r\n";
    $headers .= "From: " . $fromName . " <" . $fromEmail . ">" . "\r\n"; // From: Mahmoud Abu Obaid <mahmoud@outook.com>

    // Send email using mail() php-func.
    mail( $to, $subject, $body, $headers );
  }

  function sendEmailWithSwiftMailer( $details )
  {
    // Include all dependainces in order to use Swift_Mailer since I'm using absoulte path for now.
    require_once '../../phpmyadmin/vendor/autoload.php';
    require_once '../vendor/doctrine/lexer/lib/Doctrine/Common/Lexer/AbstractLexer.php';
    require_once '../vendor/egulias/email-validator/src/EmailValidator.php';
    require_once '../vendor/egulias/email-validator/src/EmailLexer.php';
    require_once '../vendor/egulias/email-validator/src/Validation/EmailValidation.php';
    require_once '../vendor/egulias/email-validator/src/Parser/Parser.php';
    require_once '../vendor/egulias/email-validator/src/Warning/Warning.php';
    require_once '../vendor/egulias/email-validator/src/Warning/LocalTooLong.php';
    require_once '../vendor/egulias/email-validator/src/Parser/DomainPart.php';
    require_once '../vendor/egulias/email-validator/src/EmailParser.php';
    require_once '../vendor/egulias/email-validator/src/Parser/LocalPart.php';
    require_once '../vendor/egulias/email-validator/src/Validation/RFCValidation.php';
    require_once '../vendor/swiftmailer/swiftmailer/lib/swift_required.php';

    $file = parse_ini_file( "../ManaSocialEmailInfo.ini" );
    $fromEmail = trim( $file["email"] );
    $password = trim( $file["pass"] );

    $transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
    $transport->setUsername( $fromEmail )->setPassword( $password );

    $mailer = new Swift_Mailer( $transport );

    $message = new Swift_Message('Weekly Hours');
    $message
       ->setFrom([$fromEmail => $details["fromName"]])
       ->setTo( [$details["to"] => $details["toFirstname"] . $details["toLastname"]])
       ->setSubject( $details["subject"] )
       ->setBody( $details["body"], 'text/html' );

    $result = $mailer->send($message);
  }
}

?>
