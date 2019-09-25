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
        echo 'Section for closing day and corresponding Cut off time  ';
    }

    public function  holidayAdminSection()
    {
        echo 'Please Select Holiday Dates';
//        echo '<div style="width: 50%" id="demo-multi-day"></div>';
      echo  ' <div id="datepicker_example"></div>';
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

        echo   '<input type="time" name="cut_off_time" value= "'.$value .'">';
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
        //jason ecode dates

//        $string = $input;
//        $str_arr = explode (",", $string);


      //  return json_encode($str_arr,true);
        return $input;

    }

    public function HolidayDatesCallback()
    {
        $value = esc_attr(get_option('holiday_dates'));
        echo '<input  id="setDates" type="text" class="regular-text" name="holiday_dates" value="'. $value .'" placeholder = "Write you first here" >';
        $value_arr = explode(',',$value);
       // var_dump($value_arr);
        $break_after = 3;
        echo '<div style="max-height: 60px">';
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
        echo '</dvi>';



    }





}