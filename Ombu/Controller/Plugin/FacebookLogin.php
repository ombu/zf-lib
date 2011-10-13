<?php

/**
 * This plugin helps ensure that a user has granted a set of permissions for a
 * certain list of controller actions.
 *
 * The required permissions are set as a comma separated string in application.ini:
 *     plugin.facebookLogin.requiredPerms = user_status,publish_stream,user_photos
 * The restricted controller actions are set as an array also in application.ini:
 *     plugin.facebookLogin.restrictedActions.0 = 'user/profile'
 *     plugin.facebookLogin.restrictedActions.1 = 'entry/list'
 */
class Ombu_Controller_Plugin_FacebookLogin extends Zend_Controller_Plugin_Abstract
{

    protected $restrictedActions;

    /**
     * The permissions required by the app
     * e.g. user_status,publish_stream,user_photos
     **/
    protected $requiredPerms;

    public function __construct($requiredPerms = null, $restrictedActions = array()) {
        $this->requiredPerms = $requiredPerms;
        $this->restrictedActions = $restrictedActions;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

        // Redirect to prior visited if the user hit refresh on Facebook within
        // the past 60 seconds. This prevent the user from always being sent to
        // the homepage when hitting refresh
        // @todo not quite working on FB
        //$goto = $this->_shouldRedirectToLastDestination($request);
        //if($goto) {
        //    $this->_response->setRedirect($goto)->sendResponse();
        //}

        if($this->requiredPerms && $this->_requestRequiresPermissions($request)) {
            $this->_requestPermissions();
        }

        // Set the user (only once per HTTP request)
        $fbHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Facebook');
        $fbHelper->init();
        if(Zend_Registry::isRegistered('user')) {
            return Zend_Registry::get('user');
        }
        else {
            $u = $fbHelper->me();
            Zend_Registry::set('user', $u);
            return $u;
        }
    }

    /**
     * Checks if the current request requires special permissions
     */
    protected function _requestRequiresPermissions($request) {

        $path = implode('/', array(
            $request->getControllerName(), $request->getActionName()
        ));
        return in_array($path, $this->restrictedActions) ? true : false;
    }

    /**
     * Redirects users to FB OAuth dialog if she hasn't granted sufficient
     * permissions
     */
    protected function _requestPermissions() {

        $fbApi = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getResource('fb');
        $result = $fbApi->api(array(
            'method' => 'fql.query',
            'query'  => sprintf('SELECT %s FROM permissions WHERE uid = me()',
            $this->requiredPerms),
        ));

        // not enough permissions
        $numPerms = count(explode(',', $this->requiredPerms));
        if(!isset($result[0]) || array_sum($result[0]) < $numPerms) {
            $cfg = Zend_Controller_Front::getInstance()
                ->getParam('bootstrap')->getResource('config');
            $destination = \Ombu::getRequestUri(FALSE);
            $url = sprintf( // url with extended permissions & destination
                'https://www.facebook.com/dialog/oauth?client_id=%s&redirect_uri=%s&scope=%s',
                $fbApi->getAppId(),
                $cfg->fb->appUrl . 'user/auth?destination='.urlencode($destination),
                $this->requiredPerms
            );
            $body = sprintf("<script>top.location.href = '%s'</script>", $url);
            $this->getResponse()->setBody($body)->sendResponse();
            exit;
        }
    }

    /**
     * Detects if the user hit refresh on the Facebook app within 60 seconds of
     * loading the prior page.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return mixed URL the user should be redirected to | false
     *
     */
    protected function _shouldRedirectToLastDestination(Zend_Controller_Request_Abstract $request) {

        $diNs = new Zend_Session_Namespace('DI');
        if($request->getParam('format') != 'json') {
            //// if the user was redirected or hit refresh, load the prior page
            if( $diNs->lastVisited !== null
                && preg_match('/^\/[^?]/', $diNs->lastVisited[1])
                && $request->getControllerName() == 'index'
                && $request->getActionName() == 'index'
                && time() - $diNs->lastVisited[0] < 60
                && strpos($request->getServer('HTTP_REFERER'), 'http://apps.facebook.com/dressirresponsibly') === 0) {
                    return $diNs->lastVisited[1];
                }
            // otherwise load the page that was requested
            else {
                //var_dump($request->isXmlHttpRequest()); exit;
                if(!$request->isXmlHttpRequest() &&
                   $request->getControllerName() != 'asset') {
                    $diNs->lastVisited = array( time(),  $request->getRequestUri());
                }
            }
        }
    }
}
