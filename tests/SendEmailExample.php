<?php

require_once '../../../phpmyadmin/vendor/autoload.php';
require_once '../../vendor/doctrine/lexer/lib/Doctrine/Common/Lexer/AbstractLexer.php';
require_once '../../vendor/egulias/email-validator/src/EmailValidator.php';
require_once '../../vendor/egulias/email-validator/src/EmailLexer.php';
require_once '../../vendor/egulias/email-validator/src/Validation/EmailValidation.php';
require_once '../../vendor/egulias/email-validator/src/Parser/Parser.php';
require_once '../../vendor/egulias/email-validator/src/Warning/Warning.php';
require_once '../../vendor/egulias/email-validator/src/Warning/LocalTooLong.php';
require_once '../../vendor/egulias/email-validator/src/Parser/DomainPart.php';
require_once '../../vendor/egulias/email-validator/src/EmailParser.php';
require_once '../../vendor/egulias/email-validator/src/Parser/LocalPart.php';
require_once '../../vendor/egulias/email-validator/src/Validation/RFCValidation.php';
require_once '../../vendor/swiftmailer/swiftmailer/lib/swift_required.php';

$transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
$transport->setUsername('justfortesting002@gmail.com')->setPassword('??????');

$mailer = new Swift_Mailer($transport);

$message = new Swift_Message('Weekly Hours');
$message
   ->setFrom(['justfortesting002@gmail.com' => 'Mahmoud'])
   ->setTo(['kingodesu@gmail.com' => 'King Odesu'])
   ->setSubject('Test Message Subject')
   ->setBody('Test Message Body', 'text/html');

$result = $mailer->send($message);

?>
