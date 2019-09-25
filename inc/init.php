<?php

namespace Inc;
final class Init// php will not have the ability to extends(protecting )
{
    /**
     * Store all this classes inside array
     * @return array
     *
     */
    public static function get_services(){
        return [
            Pages\Admin::class,
            Base\Enqueue::class,
            Base\SettingLinks::class,
            Base\DisplayDate::class
        ];
    }

    /**
     * Lloop throught the class ,initialize them
     * and call the register( ) methid
     */
    public static function register_services()
    {
        foreach(self::get_services() as $class)
        {
            $service = self::instantiate($class);
            if (method_exists($service,'register')){
                $service->register();
            }

        }

    }

    /**
     * Inititalis class dynamically
     * @param $class
     * @return mixed new instance of the class
     */
    private static function instantiate($class)
    {
        return new $class;
    }

}


//use Inc\Activate;
//use Inc\Disactivate;
//use Inc\Pages\Admin;
//
//
//if (!class_exists('DeliveryDate')){
//
//    class DeliveryDate  //pascal case
//    {
//        public $plugin_name;
//        //methods
//        function __construct(){
//            $this->plugin_name =   plugin_basename(__FILE__);
//
//        }
//        public static function register()
//        {
//            add_action('admin_enqueue_scripts',array('DeliveryDate','enqueue'));
//            add_action('admin_menu'), array($this,'add_admin_pages'));
//
//     add_filter("plugin_action_links_$this->plugin",array($this, 'setting_link'));
//
//
//  }
//        public function setting_link()
//        {
//
//            $settings_link = '<a href="admin.php?page=deliverydate_plugin">Settings</a>';
//            array_push($links, settings_link );
//            return $links;
//        }
//
//
//
//
//
//        protected function create_post_type(){
//            add_action('init',array( $this , 'custom_post_type' ));//beacuse we cant call wordpress action becaue its procedural
//        }
//
//
//        private function activate(){
//
//            require_once plugin_dir_path(__FILE__) . 'inc/delivery-date-activate.php';
//            DeliveryDateActivate::activate();
//
//            $this->custom_post_type();//in case the wordpress is already initailise
//
//            flush_rewrite_rules();    //when plugin is acting on the database and chaning stuff dont forget to flush
//
//        }
//        private function deactivate(){
//
//            flush_rewrite_rules();
//
//        }
//        private function uninstall(){
//
//        }
//
//        function custom_post_type(){
//
//            register_post_type('book',['public'=>true,'label'=>'Books']);
//
//        }
//
//        static function enqueue(){
//
//            wp_enqueue_style('mypluginstyle',plugins_url('/assets/mystle.css',__FILE__));
//            wp_enqueue_script('mypluginscript',plugins_url('/assets/myscript.js',__FILE__));
//
//        }
//
//
//    }
//    $deliveryDate  = new  DeliveryDate();
//    $deliceryDate->register();
//
////on activation
//
//
//    register_activation_hook(__FILE__,array('Activate','activate'));//autolaoder used
//
////on deactivation
//    register_deactivation_hook(__FILE__,array('Disactivate','deactivate'));
//
////on uninstall
////register_ uninstall_hook(__FILE__,array($deliveryDate,'uninstall'));
//
//
//}


