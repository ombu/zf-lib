<?php

/**
 * @see http://nolotiro.googlecode.com/hg/library/ZFDebug/Controller/Plugin/Debug/Plugin/Xhprof.php?r=0c751429880b7a9fe5cb7fd6cf0a131566fbf843
 * @todo add options for:
 *      xhprof paths
 *      run with built in functions
 */
class Ombu_Controller_Plugin_Xhprof extends Zend_Controller_Plugin_Abstract
{

    const UI_URL = 'http://localhost:3001/php5-xhprof/xhprof_html/index.php';

    protected $xhrlink;

    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (function_exists('xhprof_enable')) {

            //this paths maybe differs on your system, check the paths properly
            // if you get this error , the path is not the right
            // Fatal error: Class 'XHProfRuns_Default' not found
            include_once '/opt/local/www/php5-xhprof/xhprof_lib/utils/xhprof_lib.php';
            include_once '/opt/local/www/php5-xhprof/xhprof_lib/utils/xhprof_runs.php';

            // do not profile builtin functions
            xhprof_enable(XHPROF_FLAGS_NO_BUILTINS + XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY );

            //profile with builtin functions
            //xhprof_enable( XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY );
        }
    }

    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (function_exists('xhprof_enable')) {

            $profiler_namespace = 'ombu';
            $xhprof_data = xhprof_disable();
            $xhprof_runs = new XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace);
            // url to the XHProf UI libraries (change the host name and path)
            $profiler_url = sprintf(self::UI_URL . '?run=%s&source=%s', $run_id, $profiler_namespace);
            $this->xhrlink = '<a target="_blank" href="'. $profiler_url .'" target="_blank">Xhprof report</a>';
        }
    }

    public function dispatchLoopShutdown() {
        $inlineCss = array(
            'padding: 0 1em',
            'clear: both',
            'width: 700px',
            'margin:0 auto',
            'background-color: #eee',
            'border: 3px solid #ccc',
        );
        $html = sprintf("<div style='%s' id='ombu_debug'><ul><li>%s</li><li>%s</li></ul></div></body>",
            implode($inlineCss, '; '),
            'Memory: ' . round(memory_get_peak_usage() / 1048576, 2) . 'M',
            $this->xhrlink);
        $response = $this->getResponse();
        $response->setBody(str_ireplace('</body>', $html, $response->getBody()));
    }

}
