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
            Base\DisplayDate::class,
            Base\Enums::class
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


