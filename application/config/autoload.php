<?php defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();

$autoload['libraries'] = array(
    'aauth', 
    'database', 
    'session', 
    'form_validation', 
    'user_agent', 
    'calendar',
    'image_lib', 
    'pagination', 
    'upload'
);

$autoload['drivers'] = array();

$autoload['helper'] = array('cookie', 'file', 'form', 'html', 'url', 'render');

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array(
    'index_model', 
    'auth_model', 
    'form_model'
);
