<?php

define('AWS_SDK_PATH', APPLICATION_PATH . '/../lib/aws-sdk/sdk.class.php');

class Ombu_Application_Resource_AWS extends Zend_Application_Resource_ResourceAbstract {

    /**
    * Instance of FB Application
    */
    protected $AWS;

    static $instance;

    protected $_options = array(
        'key' => false,
        'secret_key' => false,
    );

    public function init() {
        $opt = $this->getOptions();
        if (!$opt['key'])  {
            throw new Exception('Invalid AWS key');
        }
        if (!$opt['secret_key'])  {
            throw new Exception('Invalid AWS secret_key');
        }
        require AWS_SDK_PATH;
        self::$instance = $this;
        return $this;
    }

    /**
     * Sends an email through AmazonSES
     *
     * @param string $sender
     * @param array $destination, optional keys:
     *      ToAddresses  - string|array
     *      CcAddresses  - string|array
     *      BccAddresses - string|array
     * @param array $message required, keys:
     *      Subject      - string
     *      Body         - array with at least one key of 'Text' or 'Html'
     * @param array $opt, optional keys:
     *      ReplyToAddresses - string|array
     *      ReturnPath       - string
     *
     * @return bool
     */
    public function sendEmail($sender, $destination = array(), $message = array(), $opt = array()) {
        $ASES = new AmazonSES($this->_options['key'], $this->_options['secret_key']);
        $_sender = $sender;
        $_destination = $destination;
        $_message = array('Subject' => array(), 'Body' => array());
        $_message['Subject']['Data'] = $message['Subject'];
        if (isset($message['Body']['Text'])) {
            $_message['Body']['Text'] = array('Data' => $message['Body']['Text']);
        }
        if (isset($message['Body']['Html'])) {
            $_message['Body']['Html'] = array('Data' => $message['Body']['Html']);
        }
        $_opt = $opt;

        $resp = $ASES->send_email($_sender, $_destination, $_message, $_opt);

        return $resp->isOK();
    }

    static function getInstance() {
        return self::$instance;
    }
}

