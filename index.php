<?php

include 'src/obv-api-wrapper.php';

$wrapper = new ObvApiWrapper('abcdefghijklmnopqrstuvwxyz', '1234567890', false);

$result = $wrapper->get('gettest', null, true);
if (isset($result->response ) )
    echo $result->response, '<br/>';


$result = $wrapper->post('posttest', ['blah'=>'iets', 'param2'=>'true'], true);
if (isset($result->response ) )
    echo $result->response, '<br/>';
