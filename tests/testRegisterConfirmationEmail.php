<?php

    require_once "../secure/email.php";

    $email = new email();

    $details = array();
    $details["subject"] = "Email Confirmation on ManaSocial";
    $details["to"] = "kingodesu@gmail.com";
    $details["fromName"] = "ManaSocial Team";
    $details["fromEmail"] = "justfortesting002@gmail.com";
    $details["toFirstname"] = "Mahmoud";
    $details["toLastname"] = "Abu Obaid";

    // Get templte html for confirmation email.
    $template = $email->getConfirmationTemplate();
    // Replace {token} in html $template by $token.
    $template = str_replace( "{token}", "Test token", $template );
    $details["body"] = $template;

    $email->sendEmailWithSwiftMailer( $details );

?>
