<?php

/*
Plugin Name: Delivery Date
Description: Custom Expected delivery date
Version: 1.0
Author: Stephan Carpin
License: GPL2

*/


/**
 *
 *
 */
if(!defined('ABSPATH')) //security to prevent other people outside wordpress to acces this file
{
  die();
}

if (! function_exists('add_action')){//check these param
    echo 'no acess';
    exit;
}

if(file_exists(dirname(__FILE__). '/vendor/autoload.php')){

    require_once dirname(__FILE__). '/vendor/autoload.php';

}

define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL' , plugin_dir_url(__FILE__));
define('PLUGIN' , plugin_basename(__FILE__));


//Can use in class oop cause this is wordpress way
function activate_delivery_date_plugin() {

    Inc\Base\Activate::activate();

}
register_activation_hook(__FILE__,'activate_delivery_date_plugin');

function deactivate_delivery_date_plugin() {

    Inc\Base\Deactivate::deactivate();

}
register_deactivation_hook(__FILE__,'deactivate_delivery_date_plugin');


if (class_exists('Inc\\Init')){

    Inc\Init::register_services();

}

