<?php

class Ombu_Filter_RelativeTime implements Zend_Filter_Interface
{

    protected $_time;

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns (string) $value
     *
     * @param  integer $timestamp
     * @return string, time since / until
     */
    public function filter($timestamp)
    {
        return $this->relativeTime($timestamp, $this->_time);
    }

    /**
     * Constructor
     *
     * @param string|array|Zend_Config $options OPTIONAL
     */
    public function __construct($time = NULL)
    {
        if ($time == NULL) {
            $this->_time = time();
        }
        else {
            $this->_time = $time;
        }
    }

    private function relativeTime($timestamp, $time)
    {

        $second = 1;
        $minute = 60 * $second;
        $hour = 60 * $minute;
        $day = 24 * $hour;
        $month = 30 * $day;
        $suffix_ago = 'ago';
        $suffix_from_now = 'from now';

        $delta = $time - $timestamp;
        if ($delta >= 0)
        {
            $suffix = $suffix_ago;
        }
        else
        {
            $delta = abs($delta);
            $suffix = $suffix_from_now;
        }

        if ($delta < 1 * $minute)
        {
            return $delta == 1 ? "one second $suffix" : $delta . " seconds $suffix";
        }
        if ($delta < 2 * $minute)
        {
            return "a minute $suffix";
        }
        if ($delta < 45 * $minute)
        {
            return floor($delta / $minute) . " minutes $suffix";
        }
        if ($delta < 90 * $minute)
        {
            return "an hour $suffix";
        }
        if ($delta < 24 * $hour)
        {
            return floor($delta / $hour) . " hours $suffix";
        }
        if ($delta < 48 * $hour)
        {
            return ($suffix == $suffix_ago) ? "yesterday" : "tomorrow";
        }
        if ($delta < 30 * $day)
        {
            return floor($delta / $day) . " days $suffix";
        }
        if ($delta < 12 * $month)
        {
            $months = floor($delta / $day / 30);
            return $months <= 1 ? "one month $suffix" : $months . " months $suffix";
        }

        $years = floor($delta / $day / 365);
        return $years <= 1 ? "one year $suffix" : $years . " years $suffix";
    }


}
