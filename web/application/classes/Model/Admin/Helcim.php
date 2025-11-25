<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Helcim Payment Processor Model
 * 
 * Handles all Helcim API interactions for payment processing.
 * API Documentation: https://devdocs.helcim.com/reference
 */
class Model_Admin_Helcim extends Model {

    /**
     * @var string API base URL
     */
    protected $apiUrl;

    /**
     * @var string API token
     */
    protected $apiToken;

    /**
     * @var array Configuration
     */
    protected $config;

    /**
     * Constructor - loads configuration
     */
    public function __construct() {
        $this->config = Kohana::$config->load('helcim');
        $this->apiUrl = $this->config['api_url'];
        $this->apiToken = $this->config['api_token'];
        
        // Try loading from .env file if not set
        if (empty($this->apiToken)) {
            $envFile = DOCROOT . '.env';
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, 'HELCIM_API_TOKEN=') === 0) {
                        $this->apiToken = trim(substr($line, 17));
                        break;
                    }
                }
            }
        }
    }

    /**
     * Test API connection
     * 
     * @return array Result with 'success' boolean and message
     */
    public function testConnection() {
        $response = $this->makeRequest('GET', '/connection-test');
        
        if (isset($response['error'])) {
            return array(
                'success' => false,
                'message' => $response['error']
            );
        }
        
        return array(
            'success' => true,
            'message' => 'Connection successful'
        );
    }

    /**
     * Process a purchase/sale transaction
     * 
     * @param array $paymentData Payment details
     * @return array Transaction result
     */
    public function processPurchase($paymentData) {
        // Validate required fields
        $required = array('amount', 'cardNumber', 'cardExpiry', 'cardCVV');
        foreach ($required as $field) {
            if (empty($paymentData[$field])) {
                return array('error' => "Missing required field: {$field}");
            }
        }

        // Parse card expiry (expects MM/YY format)
        $expiry = explode('/', $paymentData['cardExpiry']);
        if (count($expiry) !== 2) {
            return array('error' => 'Invalid expiry date format. Use MM/YY');
        }

        $data = array(
            'amount' => number_format((float)$paymentData['amount'], 2, '.', ''),
            'currency' => $this->config['currency'],
            'cardNumber' => preg_replace('/\D/', '', $paymentData['cardNumber']),
            'cardExpiry' => $expiry[0] . $expiry[1], // MMYY format
            'cardCVV' => $paymentData['cardCVV'],
            'cardHolderName' => isset($paymentData['cardHolderName']) ? $paymentData['cardHolderName'] : '',
            'cardHolderAddress' => isset($paymentData['billingAddress']) ? $paymentData['billingAddress'] : '',
            'cardHolderPostalCode' => isset($paymentData['billingZip']) ? $paymentData['billingZip'] : '',
        );

        // Add optional invoice/order reference
        if (!empty($paymentData['invoiceNumber'])) {
            $data['invoiceNumber'] = $paymentData['invoiceNumber'];
        }

        // Add customer info if available
        if (!empty($paymentData['customerCode'])) {
            $data['customerCode'] = $paymentData['customerCode'];
        }

        $response = $this->makeRequest('POST', '/payment/purchase', $data);

        if (isset($response['error'])) {
            return array(
                'success' => false,
                'error' => $response['error'],
                'details' => isset($response['details']) ? $response['details'] : ''
            );
        }

        // Check transaction status
        if (isset($response['status']) && $response['status'] === 'APPROVED') {
            return array(
                'success' => true,
                'transactionId' => isset($response['transactionId']) ? $response['transactionId'] : '',
                'approvalCode' => isset($response['approvalCode']) ? $response['approvalCode'] : '',
                'cardToken' => isset($response['cardToken']) ? $response['cardToken'] : '',
                'message' => 'Payment approved'
            );
        }

        return array(
            'success' => false,
            'error' => isset($response['message']) ? $response['message'] : 'Transaction declined',
            'response' => $response
        );
    }

    /**
     * Process a preauthorization
     * 
     * @param array $paymentData Payment details
     * @return array Transaction result
     */
    public function processPreauth($paymentData) {
        // Similar to purchase but hits preauth endpoint
        $expiry = explode('/', $paymentData['cardExpiry']);
        
        $data = array(
            'amount' => number_format((float)$paymentData['amount'], 2, '.', ''),
            'currency' => $this->config['currency'],
            'cardNumber' => preg_replace('/\D/', '', $paymentData['cardNumber']),
            'cardExpiry' => $expiry[0] . $expiry[1],
            'cardCVV' => $paymentData['cardCVV'],
            'cardHolderName' => isset($paymentData['cardHolderName']) ? $paymentData['cardHolderName'] : '',
        );

        $response = $this->makeRequest('POST', '/payment/preauth', $data);

        if (isset($response['error'])) {
            return array('success' => false, 'error' => $response['error']);
        }

        if (isset($response['status']) && $response['status'] === 'APPROVED') {
            return array(
                'success' => true,
                'transactionId' => $response['transactionId'],
                'message' => 'Preauthorization approved'
            );
        }

        return array('success' => false, 'error' => 'Preauthorization declined');
    }

    /**
     * Capture a preauthorized transaction
     * 
     * @param string $transactionId Original preauth transaction ID
     * @param float $amount Amount to capture
     * @return array Result
     */
    public function capture($transactionId, $amount) {
        $data = array(
            'preAuthTransactionId' => $transactionId,
            'amount' => number_format((float)$amount, 2, '.', ''),
        );

        $response = $this->makeRequest('POST', '/payment/capture', $data);

        if (isset($response['error'])) {
            return array('success' => false, 'error' => $response['error']);
        }

        if (isset($response['status']) && $response['status'] === 'APPROVED') {
            return array(
                'success' => true,
                'transactionId' => $response['transactionId'],
                'message' => 'Capture successful'
            );
        }

        return array('success' => false, 'error' => 'Capture failed');
    }

    /**
     * Process a refund
     * 
     * @param string $originalTransactionId Original transaction ID
     * @param float $amount Refund amount
     * @return array Result
     */
    public function processRefund($originalTransactionId, $amount) {
        $data = array(
            'originalTransactionId' => $originalTransactionId,
            'amount' => number_format((float)$amount, 2, '.', ''),
        );

        $response = $this->makeRequest('POST', '/payment/refund', $data);

        if (isset($response['error'])) {
            return array('success' => false, 'error' => $response['error']);
        }

        if (isset($response['status']) && $response['status'] === 'APPROVED') {
            return array(
                'success' => true,
                'transactionId' => isset($response['transactionId']) ? $response['transactionId'] : '',
                'message' => 'Refund processed successfully'
            );
        }

        return array('success' => false, 'error' => 'Refund failed');
    }

    /**
     * Initialize HelcimPay.js checkout session
     * 
     * @param array $paymentData Payment details including amount and customer info
     * @return array Checkout token and session info
     */
    public function initializeCheckout($paymentData) {
        $data = array(
            'paymentType' => 'purchase',
            'amount' => number_format((float)$paymentData['amount'], 2, '.', ''),
            'currency' => $this->config['currency'],
        );

        // Add customer info if available
        if (!empty($paymentData['customerCode'])) {
            $data['customerCode'] = $paymentData['customerCode'];
        }

        // Add invoice number if available
        if (!empty($paymentData['invoiceNumber'])) {
            $data['invoiceNumber'] = $paymentData['invoiceNumber'];
        }

        $response = $this->makeRequest('POST', '/helcim-pay/initialize', $data);

        if (isset($response['error'])) {
            return array('success' => false, 'error' => $response['error']);
        }

        if (isset($response['secretToken']) && isset($response['checkoutToken'])) {
            return array(
                'success' => true,
                'secretToken' => $response['secretToken'],
                'checkoutToken' => $response['checkoutToken']
            );
        }

        return array('success' => false, 'error' => 'Failed to initialize checkout');
    }

    /**
     * Create or update a customer in Helcim
     * 
     * @param array $customerData Customer details
     * @return array Result with customer code
     */
    public function createCustomer($customerData) {
        $data = array(
            'customerCode' => isset($customerData['code']) ? $customerData['code'] : '',
            'contactName' => isset($customerData['name']) ? $customerData['name'] : '',
            'businessName' => isset($customerData['company']) ? $customerData['company'] : '',
            'cellPhone' => isset($customerData['phone']) ? $customerData['phone'] : '',
            'email' => isset($customerData['email']) ? $customerData['email'] : '',
            'billingAddress' => array(
                'street1' => isset($customerData['address']) ? $customerData['address'] : '',
                'street2' => isset($customerData['address2']) ? $customerData['address2'] : '',
                'city' => isset($customerData['city']) ? $customerData['city'] : '',
                'province' => isset($customerData['state']) ? $customerData['state'] : '',
                'postalCode' => isset($customerData['zip']) ? $customerData['zip'] : '',
                'country' => isset($customerData['country']) ? $customerData['country'] : 'US',
            )
        );

        $response = $this->makeRequest('POST', '/customers', $data);

        if (isset($response['error'])) {
            return array('success' => false, 'error' => $response['error']);
        }

        if (isset($response['customerCode'])) {
            return array(
                'success' => true,
                'customerCode' => $response['customerCode'],
                'customerId' => isset($response['customerId']) ? $response['customerId'] : ''
            );
        }

        return array('success' => false, 'error' => 'Failed to create customer');
    }

    /**
     * Get transaction details
     * 
     * @param string $transactionId Transaction ID
     * @return array Transaction details
     */
    public function getTransaction($transactionId) {
        $response = $this->makeRequest('GET', "/payment/transaction/{$transactionId}");

        if (isset($response['error'])) {
            return array('success' => false, 'error' => $response['error']);
        }

        return array(
            'success' => true,
            'transaction' => $response
        );
    }

    /**
     * Make an API request to Helcim
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Request data for POST requests
     * @return array Response data
     */
    protected function makeRequest($method, $endpoint, $data = array()) {
        if (empty($this->apiToken)) {
            return array('error' => 'Helcim API token not configured. Please set HELCIM_API_TOKEN environment variable.');
        }

        $url = $this->apiUrl . $endpoint;

        $headers = array(
            'api-token: ' . $this->apiToken,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return array('error' => 'cURL Error: ' . $error);
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMessage = 'API Error (HTTP ' . $httpCode . ')';
            if ($decoded && isset($decoded['message'])) {
                $errorMessage .= ': ' . $decoded['message'];
            } elseif ($decoded && isset($decoded['errors'])) {
                $errorMessage .= ': ' . implode(', ', $decoded['errors']);
            }
            return array('error' => $errorMessage, 'details' => $decoded);
        }

        if ($decoded === null) {
            return array('error' => 'Invalid JSON response from API');
        }

        return $decoded;
    }

    /**
     * Check if Helcim is properly configured
     * 
     * @return bool
     */
    public function isConfigured() {
        return !empty($this->apiToken);
    }

    /**
     * Get API token (for debugging - masked)
     * 
     * @return string Masked token
     */
    public function getMaskedToken() {
        if (empty($this->apiToken)) {
            return '(not set)';
        }
        return substr($this->apiToken, 0, 4) . '****' . substr($this->apiToken, -4);
    }
}

