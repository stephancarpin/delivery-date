<?php

namespace  Inc\Base;


class Enqueue extends BaseController
{
    public function register()
    {
         add_action('admin_enqueue_scripts', array($this,'enqueue'));//use to enqueue script in class

    }

    public function enqueue(){

        wp_enqueue_style('mypluginstyle',$this->plugin_url . 'assets/mycss.css');

        wp_enqueue_script('mypluginjquery','https://code.jquery.com/jquery-2.2.4.min.js');
        wp_enqueue_script('mypluginjqueryui','https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');

        wp_enqueue_style('mycalendarstyle',$this->plugin_url . 'assets/muldatepicker/jquery-ui.multidatespicker.css');
        wp_enqueue_script('mycalendarscript',$this->plugin_url .'assets/muldatepicker/jquery-ui.multidatespicker.js');
        wp_enqueue_script('mypluginscript',$this->plugin_url .'assets/myscript.js');
//        wp_enqueue_script('mycalendarscriptenglish',$this->plugin_url .'assets/datepickers/js/i18n/datepicker.en.js');

    }

}