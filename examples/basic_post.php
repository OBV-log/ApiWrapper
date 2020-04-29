<?php

include '../src/obv-api-wrapper.php';

// load the wrapper with our key and secret
$wrapper = new ObvApiWrapper('yourkey', 'yoursecret', false);

//call a post method without any parameters
$result = $wrapper->post('posttest', null, true);
if (isset($result->response ) )
    echo $result->response, '<br/>';


//call a post method with parameters
$params = ['orderby'=>'fieldname', 'limit'=>50];
$result = $wrapper->post('posttest', $params, true);
if (isset($result->response ) )
    echo $result->response, '<br/>';

