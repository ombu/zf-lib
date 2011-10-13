<?php

require 'Doctrine/Common/ClassLoader.php';

use Doctrine\Common\ClassLoader,
 Doctrine\Common\Annotations\AnnotationReader,
 Doctrine\ODM\MongoDB\DocumentManager,
 Doctrine\MongoDB\Connection,
 Doctrine\ODM\MongoDB\Configuration,
 Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

 /**
  * @todo add ability to set port as an option
  */
class Ombu_Application_Resource_Odm extends Zend_Application_Resource_ResourceAbstract {

    // Doctrine\ODM\MongoDB\DocumentManager
    protected $_dm;

    protected $_options = array(
        'host' => 'localhost',
        'port' => 27017,
        'db' => null
    );

    public function init() {

        $opts = $this->getOptions();

        // ODM Classes
        $classLoader = new ClassLoader('Doctrine\ODM\MongoDB');
        $classLoader->register();

        // Common Classes
        $classLoader = new ClassLoader('Doctrine\Common');
        $classLoader->register();

        // MongoDB Classes
        $classLoader = new ClassLoader('Doctrine\MongoDB');
        $classLoader->register();

        // Document classes
        $classLoader = new ClassLoader('Models', APPLICATION_PATH);
        $classLoader->register();

        $config = new Configuration();
        $config->setProxyDir(APPLICATION_PATH . '/../cache');
        $config->setProxyNamespace('Proxies');

        $config->setHydratorDir(APPLICATION_PATH . '/../cache');
        $config->setHydratorNamespace('Hydrators');

        $reader = new AnnotationReader();
        $reader->setDefaultAnnotationNamespace('Doctrine\ODM\MongoDB\Mapping\\');
        $config->setMetadataDriverImpl(new AnnotationDriver($reader, APPLICATION_PATH));

        $config->setDefaultDb($opts['db']);

        $this->_dm = DocumentManager::create(new Connection($opts['host']), $config);

        return $this->_dm;
    }

}
