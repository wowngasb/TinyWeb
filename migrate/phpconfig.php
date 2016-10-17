<?php

define("ROOT_PATH", dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR );
define('DEV_MODEL', 'DEBUG');

function jsonEncode($var) {
    if (function_exists('json_encode')) {
        return json_encode($var);
    } else {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'integer':
            case 'double':
                return $var;
            case 'resource':
            case 'string':
                return '"'. str_replace(array("\r", "\n", "<", ">", "&"),
                    array('\r', '\n', '\x3c', '\x3e', '\x26'),
                    addslashes($var)) .'"';
            case 'array':
                if (empty ($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = jsonEncode($v);
                    }
                    return '[ '. implode(', ', $output) .' ]';
                }
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] = jsonEncode(strval($k)) .': '. jsonEncode($v);
                }
                return '{ '. implode(', ', $output) .' }';
            default:
                return 'null';
        }
    }
}

$env = require(ROOT_PATH . "config/config.php");
echo jsonEncode( $env );