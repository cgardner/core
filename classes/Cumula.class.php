<?php
namespace Cumula;

/**
 * Cumula Utility Class
 * @package Cumula
 * @subpackage Utils
 **/
class Cumula {
    /**
     * Store Instances of Classes
     * @var array
     */
    private static $classes = array();

    /**
     * Store an instance of a class
     * @param string $classType
     * @param object $instance
     * @return boolean
     **/
    public static function setInstance($classType, &$instance) {
        if (($instance instanceOf $classType) === TRUE) {
            self::$classes[$classType] = $instance;
            return TRUE;
        }
        return FALSE;
    } // end function setInstance

    /**
     * Get an instance of a class
     * @param string $classType
     * @return mixed
     **/
    public static function getInstance($classType) {
        if (isset(self::$classes[$classType])) {
            return self::$classes[$classType];
        }
        return FALSE;
    } // end function getInstance
    
} // end class Cumula
