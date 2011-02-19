<?php

/**
 * Helper for creating URLs for redirects and other tasks
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Ombu
 * @package    Ombu_Controller
 * @subpackage Ombu_Controller_Action_Helper
 */
class Ombu_Controller_Action_Helper_RequestContentType extends Zend_Controller_Action_Helper_Abstract
{
    public function query($query) {
        $type = $this->getRequest()->get('CONTENT_TYPE');
        return strpos($type, $query) > -1;
    }

    public function direct($query)
    {
        return $this->query($query);
    }

}
