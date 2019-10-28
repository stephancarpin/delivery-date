<?php

namespace Inc\Pages;

use \Inc\Api\SettingApi;
use \Inc\Base\BaseController;//start looking for this file from the base root dir of the plugin
use \Inc\Api\Callbacks\AdminCallbacks;

class Admin extends BaseController
{
    public $settings;
    public $subpages;
    public $pages;
    public $callbacks;

    public function register()
    {
        $this->settings  = new SettingApi();
        $this->callbacks = new AdminCallbacks();

        $this->setPages();
        $this->setSubPages();
        $this->setSetting();
        $this->setSections();
        $this->setFields();

        $this->settings->addPages($this->pages)->withSubPage('Dashboard')->addSubPages($this->subpages)->register();//methods chaining
    }



    public function setPages()
    {

        $this->pages    =[
            [

                'page_title'  =>'Delivery Date Plugin'
                ,'menu_title' =>'Delivery Date'
                ,'capability' =>'manage_options'
                ,'menu_slug'  =>'deliverydate_plugin'
                ,'callback'   => array($this->callbacks,'adminDashboards')//wordpress require array
                ,'icon_url'   =>'dashicons-store'
                ,'position'   =>110



            ]
            ];

    }

    public function setSubPages()
    {
        $this->subpages = [
            [

                'parent_slug' => 'deliverydate_plugin'
                ,'page_title' => 'Custom Post Type'
                ,'menu_title' => 'CPT'
                ,'capability' => 'manage_options'
                ,'menu_slug'  => 'delivery_cpt'
                ,'callback'   =>array($this->callbacks,'cptSubpage')//wordpress require array

            ],

        ];


    }


    public function setSetting()
    {
        $args = array(
            array(
                'option_group' => 'delivery_options_group',
                'option_name'  => 'normal_delivery_day'
            ),
            array(
                'option_group' => 'delivery_options_group',
                'option_name'  => 'closing_day',
                'callback'     => array($this->callbacks,'deliveryDateOptionsGroup')
            ),
            array(
                'option_group' => 'delivery_options_group',
                'option_name'  => 'cut_off_time'
            ),
            array(
                'option_group' => 'delivery_options_group',
                'option_name'  => 'international_delivery_date'
            ),

            //Holiday_option_group

            array(
                'option_group' => 'delivery_options_group',
                'option_name'  => 'holiday_dates',
                'callback'     => array($this->callbacks,'HolidayOptionsGroup')
            )


        );

        $this->settings->setSettings($args);

    }

    public function setSections()
    {
        $args = array(
            array(
                'id'           => 'delivery_admin_index',
                'title'        => 'Settings',
                'callback'     => array($this->callbacks,'deliveryDateAdminSection'),
                'page'         => 'deliverydate_plugin'
            ),

            array(
                'id'           => 'holiday_admin_index',
                'title'        => 'Settings Holiday',
                'callback'     => array($this->callbacks,'holidayAdminSection'),
                'page'         => 'deliverydate_plugin'
            )


        );

        $this->settings->setSections($args);

    }


    public function setFields()
    {
        $args = array(
            array(
                'id'           => 'normal_delivery_day',//use setting
                'title'        => 'Normal Delivery Day',
                'callback'     => array($this->callbacks,'NormalDeliveryDayCallback'),
                'page'         => 'deliverydate_plugin',
                'section'      => 'delivery_admin_index',
                'args'         => array(
                    'label_for' => 'normal_delivery_day',
                    'class' => 'normal-delivery-day-class'
                )
            ),
            array(
                'id'           => 'closing_day',//use setting
                'title'        => 'Closing day',
                'callback'     => array($this->callbacks,'closingDayCallback'),
                'page'         => 'deliverydate_plugin',
                'section'      => 'delivery_admin_index',
                'args'         => array(
                       'label_for' => 'closing_day',
                       'class' => 'closing-day-class'
                )
            ),
            array(
                'id'           => 'cut_off_time',//use setting
                'title'        => 'Cut Off Time',
                'callback'     => array($this->callbacks,'deliveryCufOffTime'),
                'page'         => 'deliverydate_plugin',
                'section'      => 'delivery_admin_index',
                'args'         => array(
                       'label_for' => 'cut_off_time',
                       'class' => 'cut-off-class'
                )
            ),
            array(
                'id'           => 'international_delivery_date',//use setting
                'title'        => 'International Delivery Date',
                'callback'     => array($this->callbacks,'InternationalDeliveryDate'),
                'page'         => 'deliverydate_plugin',
                'section'      => 'delivery_admin_index',
                'args'         => array(
                    'label_for' => 'international_delivery_date',
                    'class' => 'international-delivery-class'
                )
            ),


            array(
                'id'           => 'holiday_dates',//use setting
                'title'        => 'Holiday Dates',
                'callback'     => array($this->callbacks,'HolidayDatesCallback'),
                'page'         => 'deliverydate_plugin',
                'section'      => 'holiday_admin_index',
                'args'         => array(
                    'label_for' => 'holiday_dates',
                    'class' => 'holiday-date-class'
                )
            )
        );

        $this->settings->setFields($args);

    }
}