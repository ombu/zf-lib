<?php

/**
 * Bootstrap Resource Plugin that allows checking of certain environment settings
 *
 */
class Ombu_Resource_Environment extends Zend_Application_Resource_ResourceAbstract {

    public function init() {

        $options = $this->getOptions();

        if (!empty($options['exists'])) {
            $this->processExists($options['exists']);
        }

        if (!empty($options['fileinfo'])) {
            $this->ensureFileinfo();
        }
    }

    /**
     * Ensures existance of files or directories
     */
    private function processExists($arr) {

        foreach ($arr as $path) {
            if (!file_exists($path)) {
                throw new Zend_Application_Resource_Exception(
                    "Required path '". $path ."' doesn't exist."
                );
            }
        }

    }


    /**
     * Ensures the fileinfo php extension is available
     */
    private function ensureFileinfo() {
        if (!class_exists('finfo')) {
            throw new Zend_Application_Resource_Exception(
                "Fileinfo PHP extension is required."
            );
        }
        $config = Zend_Registry::get('config');
        $finfo = finfo_open(FILEINFO_NONE, $config->digfir->magic_filepath);
        if (!$finfo) {
            throw new Zend_Application_Resource_Exception(
                "Invalid path to magic database file.  Please assign to digfir.magic_filepath in application.ini."
            );
        }
        finfo_close($finfo);
    }
}
