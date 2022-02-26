<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('today')){

    /**
     *
     * Returns the current date without time
     *
     */
    function today(){
        return date('Y-m-d');
    }
}


if(!function_exists('date_short')){

    /**
     *
     * Returns short formatted date without time
     *
     */
    function date_short($date){
        return $date !== '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($date)) : '-';
    }
}

if(!function_exists('now')){

    /**
     *
     * Returns the current timestamp
     *
     */
    function now(){
        return date('Y-m-d H:i:s');
    }
}

if(!function_exists('date_from')){

    /**
     *
     * Returns the start of day format of the provided date
     * eg. 2018-06-06 00:00:00
     *
     */
    function date_from($date=''){
        $date = $date != '' ? $date : date('Y-m-d');
        return date('Y-m-d H:i:s', strtotime($date));
    }
}

if(!function_exists('date_to')){

    /**
     *
     * Give the end of day format of the provided date
     * eg. 2018-06-06 23:59:59
     *
    */
    function date_to($date=''){
        $date = $date != '' ? $date : date('Y-m-d');
        return date('Y-m-d H:i:s', strtotime($date)  + (86400 - 1));
    }
}

if(!function_exists('previous_day')){

    /**
     *
     * Returns the previous day. Receives a currentDay value; but defaults to today if none is passed
     *
     */
    function previous_day($currentDay=''){
        $currentDay = $currentDay != '' ? $currentDay : date('Y-m-d');
        return date('Y-m-d H:i:s', strtotime($currentDay . '-1 day'));
    }
}

if(!function_exists('trim_array')){

    /**
     * 
     * Remove keys from array whose values are empty
     * 
    */
    function trim_array($array){
        foreach($array as $key => $value){
            if(is_null($value) || $value == ''){
                unset($myarray[$key]);
            }
        }

        return $array;
    }
}