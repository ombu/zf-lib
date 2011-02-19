<?php

/**
 * @category   Ombu
 * @package    Ombu
 *
 * Wraps static convenience methods
 */
class Ombu_Utility {

    /**
     * Converts a string to an HTML ID safe string
     * - Ensure an ID starts with an alpha character by optionally adding an 'n'.
     * - Replaces any character except A-Z, numbers, and underscores with dashes.
     * - Converts entire string to lowercase.
     * @param string $str
     * @return string
     */
    public static function safeId($str) {
        $str = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $str));
        if (!ctype_lower($str{0})) $str = 'n' . $str;
        return $str;
    }

    /**
     * Ensures a filepath won't collide with an existing file.  Will append to
     * filename until an unused filename is found.
     *
     * @param $filepath string path to file
     * @return string unique filepath
     */
    public static function ensureUniqueFilename($filepath) {
        $i = 0;
        $orig = $filepath;
        $pathinfo = null;
        while (file_exists($filepath)) {

            // Cache pathinfo in case we loop here
            if ($pathinfo == null) {
                $pathinfo = pathinfo($orig);
            }

            $filepath = $pathinfo['dirname'] .'/'. $pathinfo['filename'] ."-$i.". $pathinfo['extension'];

            $i++;
        }
        return $filepath;
    }
}
