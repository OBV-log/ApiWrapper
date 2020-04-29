<?php

include '../src/obv-api-wrapper.php';

// load the wrapper with your key and secret
$wrapper = new ObvApiWrapper('yourkey', 'yoursecret', false);

//call a get method without any parameters
$result = $wrapper->get('anygetmethod', null, true);
if (isset($result->response ) )
    echo $result->response;


//call a get method with parameters
$params = ['orderby'=>'fieldname', 'limit'=>50];
$result = $wrapper->get('paremetergetmethod', $params, true);
if (isset($result->response ) )
    echo $result->response;
