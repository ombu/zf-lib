<?php

/**
 * Bootstrap Resource Plugin that allows toggling cache from application.ini via:
 * resources.cache.useCache = 1
 */
class Ombu_Resource_Mongo extends Zend_Application_Resource_ResourceAbstract {

	private $_cache;
	private $_connection;

	protected $_options = array(
		'host' => 'localhost',
		'port' => 27017,
		'db' =>  null
    );

	public function init() {
		$options = $this->getOptions();
		$connstr = sprintf("mongodb://%s:%d", $options['host'], $options['port']);
		$this->_connection = new Mongo($connstr);
		$this->_db = $this->_connection->selectDb($options['db']);
		Zend_Registry::set('db', $this->_db);
	}

	public function getDb() {
		return  $this->_db;
	}

}
