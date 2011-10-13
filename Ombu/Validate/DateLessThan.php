<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: StringLength.php 23772 2011-02-28 21:35:29Z ralph $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ombu_Validate_DateLessThan extends Zend_Validate_Abstract
{

    const NOT_LESS = 'notLessThan';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_LESS => "Date not allowed"
    );

    /**
     * Maximum value
     *
     * @var mixed
     */
    protected $_max;

    /**
     * Sets validator options
     *
     * @param  mixed|Zend_Config $max
     * @return void
     */
    public function __construct($max)
    {
        if ($max instanceof Zend_Config) {
            $max = $max->toArray();
        }

        if (is_array($max)) {
            if (array_key_exists('max', $max)) {
                $max = $max['max'];
            } else {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("Missing option 'max'");
            }
        }

        $this->setMax($max);
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  DateTime $max
     * @return Provides a fluent interface
     */
    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     *
     * @param  DateTime $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($value->format('U') < $this->getMax()->format('U')) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}
