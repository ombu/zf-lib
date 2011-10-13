<?php

define('FB_API_PATH', APPLICATION_PATH . '/../lib/fb-php-sdk/src/facebook.php');
class Ombu_Application_Resource_Fb extends Zend_Application_Resource_ResourceAbstract {

    /**
    * Instance of FB Application
    */
    protected $fb;

    static $instance;

    protected $_options = array(
        'appId' => false,
        'appSecret' => false,
    );

    public function init() {
        $opt = $this->getOptions();
        if(!$opt['appId'])  {
            throw new Exception('Invalid FB App ID');
        }
        require FB_API_PATH;
        $this->fb = new Facebook(array(
            'appId'  => $opt['appId'],
            'secret' => $opt['appSecret'],
        ));
        $this->appId = $opt['appId'];
        self::$instance = $this;
        return $this;
    }

    /**
     * Logging wrapper for FB SDK
     */
     public function __call($name, $args) {
        $firebug = $this->getBootstrap()->getResource('log');
        if($firebug) {
            $firebug->warn($args, 'Called FB method: ' . $name);
        }
        return call_user_func_array(array($this->fb, $name), $args);
     }


    static function getInstance() {
        return self::$instance;
    }
}

