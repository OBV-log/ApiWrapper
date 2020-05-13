<?php

include 'src/obv-api-wrapper.php';

$wrapper = new ObvApiWrapper('c0a4395a-1421-46e9-a223-cabb0c9f3b0', 'GOoSo8LyOkqwvNNDEt82CJvt');

$result = $wrapper->get('test');
if (isset($result->response ) )
    echo $result->response, '<br/>';


$result = $wrapper->post('posttest', ['param1'=>'value', 'param2'=>'true']);
if (isset($result->response ) )
    echo $result->response, '<br/>';
