<?php

namespace Inc\Api\Callbacks;
use \Inc\Base\BaseController;//start looking for this file from the base root dir of the plugin

class AdminCallbacks extends BaseController
{
    public function adminDashboards()
    {
        return require_once "$this->plugin_path/templates/admin.php";
    }

    public function cptSubpage()
    {
        return require_once "$this->plugin_path/templates/cpt.php";

    }
    public function taxonomySubpage()
    {
        return require_once "$this->plugin_path/templates/taxonomy-subpage.php";

    }

    public function deliveryDateOptionsGroup($input)
    {

        return $input;

    }

    public function  deliveryDateAdminSection()
    {

        echo '<hr>';
    }

    public function  holidayAdminSection()
    {
        echo '<hr>';
        echo '<p>Please Select Holiday Dates</p>';
        echo '<div id="datepicker_example"></div>';
        echo '<hr>';
    }

    public function deliveryDateTextExample()
    {
        $value = esc_attr(get_option('text_example'));
        echo '<input type="text" class="regular-text" name="text_example" value="'. $value .'" placeholder = "Write Something here" >';
    }

    public function deliveryCufOffTime()
    {
        $value = esc_attr(get_option('cut_off_time'));
       // echo '<input type="text" class="regular-text" name="cut_off_time" value="'. $value .'" placeholder = "Write you first here" >';

        echo   '<input type="time" name="cut_off_time" value= "'.$value .'"><span style="font-style: italic"> (Use as cut off time for days of the week too)</span>';
    }



    public function NormalDeliveryDayCallback()
    {
        $value = esc_attr(get_option('normal_delivery_day'));


        $option_days = ["Monday","Tuesday","wednesday","Thursday","Friday","Saturday","Sunday"];

        echo '<select name="normal_delivery_day"  value="'. $value .'">';


        foreach ($option_days as $day)
        {
            $selected = '';

            if($value == $day)
            {
                $selected= 'Selected';
            }
            echo '<option  value="'.$day.'" ' . $selected . '>'. $day . '</option>';

        }
        echo  '</select>';


    }

    public function closingDayCallback()
    {
        /**
         * TODO:convert in ENUm for days
         */
        $value = esc_attr(get_option('closing_day'));


        $option_days = ["Monday","Tuesday","wednesday","Thursday","Friday","Saturday","Sunday"];

        echo '<select name="closing_day"  value="'. $value .'">';


        foreach ($option_days as $day)
        {
            $selected = '';

            if($value == $day)
            {
                $selected= 'Selected';
            }
            echo '<option  value="'.$day.'" ' . $selected . '>'. $day . '</option>';

        }
        echo  '</select>';

    }



    //************HOliday Options Group

    public function HolidayOptionsGroup($input)
    {


        return $input;

    }

    public function HolidayDatesCallback()
    {
        $value = esc_attr(get_option('holiday_dates'));

        echo '<input hidden id="setDates" type="text" class="regular-text" name="holiday_dates" value="'. $value .'"                        placeholder = "Select holiday dates" >';



        $value_arr   = explode(',',$value);
        $break_after = 3;

        echo '<div>';

        $counter = 0;
        foreach ($value_arr as $item) {
            if ($counter % $break_after == 0) {
                echo '<ul>';
            }
            echo '<li>'.$item.'</li>';

            if ($counter % $break_after == ($break_after-1)) {
                echo '</ul>';
            }
            ++$counter;
        }

        if ((($counter-1) % $break_after) != ($break_after-1)) {
            echo '</ul>';
        }
        echo '</div>';
    }
}