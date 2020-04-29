# OBV railsysteem.eu API wrapper

Contains a simple PHP wrapper to call the OBV railsysteem API.

## Official Documentation
Official documentation can be found at [Railsysteem API website](https://railsysteem.eu/api) 

## Basic usage
```php
include '/src/obv-api-wrapper.php';

// load the wrapper with your key and secret
$wrapper = new ObvApiWrapper('yourkey', 'yoursecret', false);

//call a get method without any parameters
$result = $wrapper->get('anygetmethod', null, true);

//call a get method with parameters
$result = $wrapper->get('anygetmethod', ['param1'=>'value1','param2'=>'value2'], true);
```

`php code test`