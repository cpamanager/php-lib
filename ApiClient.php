<?php

/* 
 * The class allows you to fetch information from a CPAMANAGER.NET account. If you
 * have any questions or proposals, let us know by sending a message to the author's email.
 * 
 * @author support@cpamanager.net
 */

class ApiClient {

    /**
     * Available methods for using with the API.
     */
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_GET = 'GET';
    
    /**
     * Available statuses for transactions.
     */    
    const TRANSACTION_STATUS_ACCEPTED = 'accepted';
    const TRANSACTION_STATUS_REFUSED = 'refused';
    const TRANSACTION_STATUS_NEW = 'not_verified';

    /**
     * An API key.
     * @var mixed
     */
    private $_apiKey = null;
    
    /**
     * A domain you are tracking the transactions for.
     * @var mixed
     */
    private $_domain = null;
    
    /**
     * The url to call the API through.
     * @var mixed
     */
    private $_endpoint = 'http://tracking.cpamanager.net';
    
    /**
     * Prepares the object.
     * @param mixed $apiKey The key you have obtained after the account on 
     * @param mixed $domain A domain you are tracking the transactions for 
     * the platform has been created
     */
    public function __construct($apiKey, $domain) {
        $this->_apiKey = $apiKey;
        $this->_domain = $domain;
    }
    
    /**
     * Creates a new transaction for a given account. The method does not require
     * authentication.
     * 
     * @param mixed $gid An identifier which allows to join transaction existing
     * in an onlie shop with CPAMANAGER's transaction.
     * @param array $data If the array is given than will replace the internal
     * implementaion of the configuration for the request
     * @return int Id of the created transaction
     */
    public function createTransaction($gid, array $data = []) {
        
        // Fetch information from the cookie
        $cpamtpid = $_COOKIE['cpamtpid'];        
        $data['gid'] = $gid;
        $data['cpamtpid'] = $cpamtpid;

        // Call the endpoint
        $transaction = $this->_curl('partner/api/transaction/create', self::HTTP_METHOD_POST, $data);
        if ($error = $this->_hasResponseError($response)) {
            throw new Exception($error['msg'], $error['code']);
        }
        
        return $transaction['id'];
    }
    
    /**
     * Creates a new transaction for a given account.
     * @param mixed $gid An identifier which allows to join transaction existing
     * in an onlie shop with CPAMANAGER's transaction.
     * @param string $status The status to set for the transaction.
     * @return array
     */
    public function setTransactionStatus($gid, $status) {
        
        // Check if the given status exists
        if ($this->_isStatusCorrect($status) === false) {
            throw new Exception('The given status does not exists.', 404);
        }
        
        // Prepare the params array
        $data = array(
            "domain" => $this->_domain,
            "status" => $status,
            "gid" => $gid,
        );      
        
        // Call the endpoint
        return $this->_curl('account/api/transaction/status', self::HTTP_METHOD_PUT, $data, true);        
    }
    
    /**
     * Fetches recently created transactions
     * @return array
     */
    public function fetchTransactions() {
        // Prepare the params array
        $data = array(
            "domain" => $this->_domain,
        );      
        
        // Call the endpoint
        return $this->_curl('account/api/transaction/fetch', self::HTTP_METHOD_GET, $data, true);                
    }
    
    /**
     * Performs the query to a given endpoint.
     * 
     * @param mixed $endpoint
     * @param string $method
     * @param mixed $data
     * @param boolean $auth Set to true if authorization is required to call
     * an endpoint
     * @return string
     */
    private function _curl($endpoint, $method, $data, $auth = false) {
        // Create a full path to the endpoint
        $url = $this->_endpoint . '/' . $endpoint;
        $ch = curl_init($url);
        
        // If authorization required set additional options
        if ($auth == true) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->_apiKey);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);            
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        $response = curl_exec($ch);
        return json_decode($response, true);
    }
    
    /**
     * Checks whether the given status exists in the system scope
     * @param string $status
     * @return boolean
     */
    private function _isStatusCorrect($status) {
        return in_array($status, [
            self::TRANSACTION_STATUS_ACCEPTED,
            self::TRANSACTION_STATUS_REFUSED,
            self::TRANSACTION_STATUS_NEW
        ]);
    }
    
    /**
     * Checks if the response contains errors
     * @param array $response Response from the endpoint after a request has been
     * made
     */
    private function _hasResponseError(array $response) {
        if ($response['status'] == 500) {
            return [
                'status' => $response['status'],
                'msg' => 'A wrong request has been made. Please check whether the given parameters for the method are correct.',
            ];
        }
        return false;
    }
}

