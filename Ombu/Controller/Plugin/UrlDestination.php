<?php

/**
 * This plugin will redirect to a {url} if a ?destination={url} parameter is
 * present in the current request.  The redirect can be "canceled" if the
 * has a parameter & value matching those in $exceptIfParam.
 */
class Ombu_Controller_Plugin_UrlDestination extends Zend_Controller_Plugin_Abstract
{

    /**
     * The parameters & values used to cancel the redirect
     */
    protected $exceptIfParam;

    /**
     * The parameter to use for redirection
     */
    protected $redirectParam = 'destination';


    public function __construct() {
        $this->exceptIfParam = array();
    }


    /**
     * Adds an exception parameter to the $exceptIfParam array
     */
    public function addException($param, $value) {
        $this->exceptIfParam[$param] = $value;
    }


    /**
     * Sets the parameter used for a url redirect
     */
    public function setRedirectParam($param) {
        $this->redirectParam = $param;
    }


    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $url = $this->_getDestinationUrl();
        $hasParamException = $this->_hasParamException();
        if ($url && !$hasParamException) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl($url)->redirectAndExit();
        }
    }


    /**
     * @return String or FALSE
     */
    protected function _getDestinationUrl() {
        $url = urldecode($this->getRequest()->getParam($this->redirectParam));
        if ($url && !empty($url)) {
            return $url;
        }
        return FALSE;
    }


    /**
     * Decides if there's a parameter exception from $this->exceptIfParam
     *
     * @return bool
     */
    protected function _hasParamException() {
        foreach ($this->exceptIfParam as $p => $v) {
            $value = $this->getRequest()->getParam($p);
            if ($value && ($value === $v)) {
                return TRUE;
            }
        }
        return FALSE;
    }

}
