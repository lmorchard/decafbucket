<?php
/**
 * Template text encoding / escaping shortcuts helper.
 *
 * @package OpenInterocitor
 * @author  l.m.orchard@pobox.com
 * @link    http://decafbad.com/
 * @license Share and Enjoy
 */

class out_Core
{

    /**
     * Escape a string for HTML inclusion.
     *
     * @param string content to escape
     * @return string HTML-encoded content
     */
    public static function H($s, $echo=FALSE) {
        $out = htmlentities($s);
        if ($echo) echo $out;
        else return $out;
    }

    /**
     * Encode a string for URL inclusion.
     *
     * @param string content to encode
     * @return string URL-encoded content
     */
    public static function U($s, $echo=FALSE) {
        $out = rawurlencode($s);
        if ($echo) echo $out;
        else return $out;
    }

    /**
     * JSON-encode a value
     *
     * @param mixed some data to be encoded
     * @return string JSON-encoded data
     */
    public static function JSON($s, $echo=FALSE) {
        $out = json_encode($s);
        if ($echo) echo $out;
        else return $out;
    }

}
