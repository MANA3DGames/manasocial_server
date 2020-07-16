<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
    $response = array();
    $response[0] = array(
        'id' => '1',
        'value1'=> 'value1',
        'value2'=> 'value2'
    );

echo json_encode($response);
?>
