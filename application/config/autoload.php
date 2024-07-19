<?php defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = [];

$autoload['libraries'] = [
    'aauth', 
    'database', 
    'session', 
    'form_validation', 
    'user_agent', 
    'calendar',
    'image_lib', 
    'pagination', 
    'upload'
];

$autoload['drivers'] = [];

$autoload['helper'] = [
    'cookie', 
    'file', 
    'form', 
    'html', 
    'url',
    'reports', 
    'render', 
    'settings', 
    'permissions',
    'user',
    'site'
];

$autoload['config'] = [];

$autoload['language'] = [];

$autoload['model'] = [
    'site_model', 
    'auth_model', 
    'form_model',
    'reports_model',
    'users_model',
    'messages_model'
];
