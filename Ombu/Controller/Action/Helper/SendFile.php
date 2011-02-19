<?php

/**
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Ombu
 * @package    Ombu_Controller
 * @subpackage Ombu_Controller_Action_Helper
 */
class Ombu_Controller_Action_Helper_SendFile extends Zend_Controller_Action_Helper_Abstract {

    public function direct($name, $body, $mimeType) {

        $ac = $this->getActionController();
        $ac->getHelper('layout')->disableLayout();
        $ac->getHelper('viewRenderer')->setNoRender(TRUE);

        $rs = $this->getResponse();
        $rs->setHeader('Content-type', $mimeType, true);
        $rs->setHeader('Content-disposition', 'attachment; filename='.$name, true);
        $rs->setBody($body);
        $rs->sendResponse();
    }

}
