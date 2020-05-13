<?php
/**
 * Generic wrapper to call the API at https://railsysteem.eu/api
 */
class ObvApiWrapper {

    private $baseUrl;
    private $apiVersion = '1.0.0';
    private $apiKey;
    private $apiSecret;
    private $apiTimestamp;
    private $debug = true;

    /**
     * ObvApiWrapper constructor.
     * @param null $apiKey
     * @param null $apiSecret
     * @param bool $debug
     */
    public function __construct ($apiKey = null, $apiSecret = null, $debug = false)
    {
        $this->apiKey       = $apiKey;
        $this->apiSecret    = $apiSecret;
        $this->apiTimestamp = time();
        $this->debug        = $debug;
        $this->baseUrl      = 'https://railsysteem.eu/api/';

        if($this->debug)
        {
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(-1);
        }
    }

    /**
     * Invoke API
     * @param string $method API method to call
     * @param array $params parameters
     * @param bool $apiKey  use apikey or not
     * @return object
     */
    public function get ($method, $params = null, $apikey = true)
    {
        if($this->debug) var_dump($params);

        $uri  = $this->uri('get',$method, $params);

        if($this->debug) echo '<br/> uri: ' , $uri, '<br/>';

        $signature = $this->signature('get', $this->baseUrl.$method, $params, $this->apiSecret);

        if($this->debug) echo 'signature: ', $signature, '<br/>';

        return $this->call('get', $uri, $signature, $apikey);

    }

    /**
     * Invoke API
     * @param string $method API method to call
     * @param array $params parameters
     * @param bool $apiKey use apikey or not
     * @return object
     */
    public function post ($method, $params = null, $apikey = true)
    {
        if($this->debug) var_dump($params);

        $uri = $this->uri('post',$method,$params);

        $signature = $this->signature('post', $uri, $params, $this->apiSecret);

        if($this->debug) echo 'signature: ', $signature, '<br/>';

        if($this->debug) echo 'uri: ' , $uri, '<br/>';

        return $this->call('post', $uri, $signature, $apikey, $params);

    }

    /**
     *
     * @param type $httpmethod
     * @param type $url
     * @param type $params
     * @param type $secret
     * @return type
     *
     * Create a hmac signature
     */
    private function signature($httpmethod, $url, $params, $secret)
    {
        $signature_params = [
                'apiversion'   => $this->apiVersion,
                'apikey'       => $this->apiKey,
            ];

        if(is_array($params)) $signature_params = array_merge ($signature_params, $params);
        $params = array_change_key_case($signature_params, CASE_LOWER);
        ksort($params);

        $params = urldecode(http_build_query($params));
        return hash_hmac ('sha512', implode("\n", [strtolower($httpmethod), $url, $params]), $secret);
    }

    /**
     *
     * @param type $httpmethod
     * @param type $method
     * @param type $params
     * @return type
     *
     * return a uri to call
     */
    private function uri($httpmethod, $method, $params)
    {
        if($httpmethod === 'get')
        {
            if(is_array($params))
            {
                $params = urldecode(http_build_query($params));
                return $this->baseUrl.$method.'?'.$params;
            }
            else return $this->baseUrl.$method;
        }
        else return $this->baseUrl.$method;
    }

    /**
     *
     * @param type $httpmethod
     * @param type $uri
     * @param type $signature
     * @param type $apikey
     * @param type $params
     * @return type
     * @throws \Exception
     *
     * Calls the API
     */
    private function call($httpmethod, $uri, $signature, $apikey, $params = null)
    {
        if ($apikey == true)
            $header = array('apisignature: '.$signature,
                'apikey:'.$this->apiKey,
                'apiversion:'.$this->apiVersion,
                'apitimestamp:'. $this->apiTimestamp,
            );

        $ch = curl_init ($uri);
        if ($apikey == true) curl_setopt ($ch, CURLOPT_HTTPHEADER, $header );
        if($httpmethod === 'post')
        {
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER , true); // get http headers, used for downloads

        if($this->debug)
        {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'rw+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

        $r = curl_exec($ch);

        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        $result_array = explode("\n\r", $r , 2); //get first line with header
        $result = $result_array[1];

        //account for file downloads
        if($contentType !=='application/json')
        {
            $header_array = explode("\n", $result_array[0]);
            foreach($header_array as $header_value) {
                $header_pieces = explode(':', $header_value);
                if(count($header_pieces) == 2) {
                    $headers[$header_pieces[0]] = trim($header_pieces[1]);
                }
            }

            //force file download
            header('Content-type: ' . $headers['Content-Type']);
            header('Content-Disposition: ' . $headers['Content-Disposition']);
            header("Cache-Control: public");
            header('Content-Description: File Transfer');
            echo $result;
            exit();
        }

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        $answer = json_decode($result);

        curl_close($ch);
        if($this->debug)
        {
            var_dump($result);
            var_dump($answer);
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo "Debug information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
        }

        if (isset($answer->success) && $answer->success === 'false') {
            echo 'Error code: ', $answer->http_response, '<br/>Error ', $answer->error;
        }
        else
        {
            return $answer;
        }
    }
}
