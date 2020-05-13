# OBV railsysteem.eu API wrapper

Contains a simple PHP wrapper to call the OBV railsysteem API.

## Official API Documentation
Official API documentation can be found at [Railsysteem API website](https://railsysteem.eu/api) 

## Basic wrapper usage
```php
include 'src/obv-api-wrapper.php';

// load the wrapper with your key and secret
$wrapper = new ObvApiWrapper('yourkey', 'yoursecret');

//call a get method without any parameters
$result = $wrapper->get('anygetmethod');

//call a get method with parameters
$result = $wrapper->get('anygetmethod', ['param1'=>'value1','param2'=>'value2']);

if ( isset($result->response) )
    echo $result->response, '<br/>';

```


## Debug usage
```php
// load the wrapper with your key and secret, set debug to true
$wrapper = new ObvApiWrapper('yourkey', 'yoursecret', true);

//call a get method without any parameters
$result = $wrapper->get('anygetmethod');
```