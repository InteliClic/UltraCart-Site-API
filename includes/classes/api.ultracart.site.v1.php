<?php
/**
 * UltraCart Site REST API PHP wrapper
 *
 * @package UltraCart_Site
 * @author InteliClic <info@inteliclic.com>
**/
class UltraCart_Site {

    public $errors = array();
    public $curl = null;
    public $response = null;
    public $request = null;

    public function __construct() {
        global $config;
        $this->credentials = array('merchantId' => $config['ultracart']['merchantId'], 'login' => $config['ultracart']['login'], 'password' => $config['ultracart']['pass']);
        $this->initialize();
    }
    
    private function initialize() {
        global $config;
        // Lets open our curl class to send the request eficiently
        $this->curl = new Curl;
        $this->curl->options = array('CURLOPT_TIMEOUT' => $config['api_timeout']);
        $this->curl->headers = array('X-UC-Merchant-Id' => $this->credentials['merchantId'], 'cache-control' => 'no-cache');
        // Create the request
        $this->request = new stdClass();
        $this->request->server = $config['ultracart']['server'];
        $this->request->type = 'get';
    }
    
    public function detectErrors() {
        global $lang;
        if($this->response->headers['Status-Code'] == '100' OR $this->response->headers['Status-Code'] == '200'){
            $response = json_decode($this->response->body);
            if (!empty($this->response->headers['UC-REST-ERROR'])){
                $this->errors = array($this->response->headers['UC-REST-ERROR']);
                throw new Exception($lang['ultracart']['cart']['containsErrors'], 2001);
            } else if(count($response->errors) > 0){
                $this->errors = $response->errors;
                throw new Exception($lang['ultracart']['cart']['containsErrors'], 2001);
            } else if (count($response->errorMessages) > 0) {
                $this->errors = $response->errorMessages;
                throw new Exception($lang['ultracart']['cart']['containsErrors'], 2001);
            }
        } else {
            if($this->curl->error()){
                throw new Exception($this->curl->error());
            } else if(count($this->response->headers) > 0){
                throw new Exception($this->response->headers['Status'], $this->response->headers['Status-Code']);
            } else {
                throw new Exception($lang['ultracart']['api']['responseEmpty'], 2003);
            }
        }
    }
    
    private function doCall(){
        global $lang;
        
        if(!is_null($this->request->method)){
            $url = $this->request->server . $this->request->method;
            switch ($this->request->type) {
                case 'put':
                    $this->curl->headers['Content-Type'] = 'application/json';
                    $this->response = $this->curl->put($url, json_encode($this->request->vars));
                    break;
                case 'post':
                    $this->curl->headers['Content-Type'] = 'application/json';
                    $this->response = $this->curl->post($url, json_encode($this->request->vars));
                    break;
                case 'delete':
                    $this->response = $this->curl->delete($url, json_encode($this->request->vars));
                    break;
                case 'get':
                    $this->response = $this->curl->get($url);
                    break;
                default:
                    throw new Exception($lang['ultracart']['api']['invalidRequest'], 1001);
                    break;
            }
            
            $this->detectErrors();

        } else {
            throw new Exception($lang['ultracart']['api']['methodEmpty'], 1002);
        }
    }
    
    public function stateProvinces($country) {
        global $lang;
        if (!empty($country)) {
            $this->request->type = 'get';
            $this->request->method = '/rest/site/stateProvinces';
            $this->request->method .= '?country='.rawurlencode($country);
            $this->doCall();
            return json_decode($this->response->body);
        } else {
            throw new Exception($lang['ultracart']['site']['countryEmpty'], 3000);
        } 
    }

    public function stateProvinceCodes($country) {
        global $lang;
        if (!empty($country)) {
            $this->request->type = 'get';
            $this->request->method = '/rest/site/stateProvinceCodes';
            $this->request->method .= '?country='.rawurlencode($country);
            $this->doCall();
            return json_decode($this->response->body);
        } else {
            throw new Exception($lang['ultracart']['site']['countryEmpty'], 3000);
        } 
    }

    public function unifiedAffiliateCookieScript($secureHostName = NULL) {
        $this->request->type = 'get';
        $this->request->method = '/rest/site/unifiedAffiliateCookieScript';
        if(!is_null($secureHostName))
            $this->request->method = '?secureHostName='.rawurlencode($secureHostName);
        $this->doCall();
        return $this->response->body;
    }

    public function advertisingSources($screenBrandingThemeCode = NULL) {
        $this->request->type = 'get';
        $this->request->method = '/rest/site/advertisingSources';
        if(!is_null($screenBrandingThemeCode))
            $this->request->method = '?screenBrandingThemeCode='.rawurlencode($screenBrandingThemeCode);
        $this->doCall();
        return json_decode($this->response->body);
    }

    public function returnPolicy($screenBrandingThemeCode = NULL) {
        $this->request->type = 'get';
        $this->request->method = '/rest/site/returnPolicy';
        if(!is_null($screenBrandingThemeCode))
            $this->request->method = '?screenBrandingThemeCode='.rawurlencode($screenBrandingThemeCode);
        $this->doCall();
        return $this->response->body;
    }

    public function allowedCountries() {
        $this->request->type = 'get';
        $this->request->method = '/rest/site/allowedCountries';
        $this->doCall();
        return json_decode($this->response->body);
    }

    public function customerIpAddress() {
        $this->request->type = 'get';
        $this->request->method = '/rest/site/customerIpAddress';
        $this->doCall();
        return $this->response->body;
    }

    public function searchItems($vars) {
        $this->request->type = 'get';
        $this->request->method = '/rest/site/items/search';
        if (count($vars) > 0) {
            foreach($vars as $key => $var){
                $params .= "&$key=".rawurlencode($var);
            }
            $this->request->method .= '?' . ltrim($params,'&');
        }
        $this->doCall();
        return json_decode($this->response->body);
    }
    
    public function getItems($vars, $cart = NULL) {
        if(!is_null($cart)){
            $this->request->vars = $cart;
            $this->request->type = 'post';
        } else {
            $this->request->type = 'get';
        }
        $this->request->method = '/rest/site/items';
        if (count($vars) > 0) {
            foreach($vars as $key => $var){
                if((string) $key == 'url'){
                    $params .= "&url=".rawurlencode($var);
                } else {
                    $params .= "&id=".rawurlencode($var);
                }
            }
            $this->request->method .= '?' . ltrim($params,'&');
        }
        $this->doCall();
        return json_decode($this->response->body);
    }
    
    public function getItem($itemId, $cart = NULL) {
        if(!is_null($cart)){
            $this->request->vars = $cart;
            $this->request->type = 'post';
        } else {
            $this->request->type = 'get';
        }
        $this->request->method = '/rest/site/items/'.$itemId;
        $this->doCall();
        return json_decode($this->response->body);
    }
}

?>