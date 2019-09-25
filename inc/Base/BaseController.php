<?php
/*plugin_basename to changes for new plugin
 * These prevent interference with other plugin global variables
 */

namespace Inc\Base;

class  BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin_name;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__,2));//we are two level inside Inc BAse to the plugin dir
        $this->plugin_url  = plugin_dir_url(dirname(__FILE__,2));//we are two level inside Inc BAse to the plugin dir
        $this->plugin_name = plugin_basename(dirname(__FILE__,3)) .'/delivery-date.php';//we are 3 level inside Inc BAse to the plugin dir
    }

}