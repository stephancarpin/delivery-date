<?php

namespace  Inc\Base;
use DateTime;

class DisplayDate extends BaseController
{
    private $closing_day ;
    private $cut_off_time ;
    private $holiday_dates ;
    private $normal_delivery_day ;


    public function register()
    {
        //Todo:add filter for email and Checkout
        //Todo: Check Server date and time

       // add_filter( 'woocommerce_get_item_data', array($this,'display_delivery_date_in_cart'), 10, 2 );
        add_action( 'woocommerce_cart_totals_after_shipping', array($this,'action_woocommerce_cart_totals_after_shipping'), 25, 0 );
        add_action( 'woocommerce_review_order_before_shipping', array($this,'action_woocommerce_cart_totals_after_shipping'), 25, 0 );
        add_action( 'woocommerce_email_header', array($this,'action_woocommerce_email'), 10, 0 );

        add_filter('woocommerce_thankyou_order_received_text',  array($this,'woo_change_order_received_text'), 10, 2 );



    }

    /**
     * filter to display date in cart total
     */
    public function action_woocommerce_cart_totals_after_shipping( ) {


        $html = "<tr>
                   <th>Estimated Delivery Date</th>
                   <td style='font-weight: bolder'>" . self::calculateDeliveryDate() ."</td>
                 </tr>" ;

        echo $html;

    }

    public function action_woocommerce_email( ) {

        $html = "<div style='text-align: center;padding-top: 10px;padding-bottom: 10px'><h4 style='font-weight: bolder'>Estimated Delivery Date:  " . self::calculateDeliveryDate() ."</h4></div>" ;


        echo $html;

    }

    function woo_change_order_received_text( $str, $order ) {

        $html = "<div style='text-align: center;padding-top: 10px;padding-bottom: 10px'><h4 style='font-weight: bolder'>Estimated Delivery Date:  " . self::calculateDeliveryDate() ."</h4></div>" ;

       // $new_str = 'We have emailed the purchase receipt to you. Please make sure to fill <a href="http://localhost:8888/some-form.pdf">this form</a> before attending the event';
        return $str .$html;
    }


    /**
     * Get data from Delivery Date Plugin
     */
    private function getDataFromPlugin(){

        settings_fields('delivery_options_group');

        $this->closing_day      =  esc_attr(get_option('closing_day'));
        $this->cut_off_time     =  esc_attr(get_option('cut_off_time'));
        $this->normal_delivery_day     =  esc_attr(get_option('normal_delivery_day'));
        $this->holiday_dates    =  explode(',', esc_attr(get_option('holiday_dates')));


    }

    /**
     * @return false|string
     */
    private function calculateDeliveryDate()
    {
        self::getDataFromPlugin();

        $now_date           = new DateTime();
        $nddcheckdateTime   = new DateTime();
        //for debugging date
      //  $now_date->modify(' wednesday 14:01:00.000000'  );
        // $now_date->setTime(23, 00);;

        $timestamp_now =$now_date->getTimestamp();



        if (self::getShippingMethodsNDD()) {//check for next day delivery

            /**
             * Check Cut off time for ndd
             * check for sunday and saturday
             */

            $nddcheckdateTime->modify($this->cut_off_time );

             if ($now_date < $nddcheckdateTime )
             {

                 $display_date = self::findAvailableDateNND($now_date,1);

             } else {

                 $display_date =  self::findAvailableDateNND($now_date,2);
             }


            return date_format($display_date,'d-m-Y');

        }


        if (!self::getShippingMethodsNDD()) {//Not next day Delivery



            if(self::checkIfWeekendDay($now_date))
            {

                $now_date->modify('tuesday');

            }

            //TODO:add situation where customer add to cart on a holiday date(should take next day available)



            $check_dateTime  = self::findNearestDayOfWeek($now_date,$this->closing_day);


            $check_dateTime->modify( $this->cut_off_time );


            $check_dateTimeStamp = $check_dateTime->getTimestamp();


            /**
             * Enable for debug
             */
           // $this->p($now_date);
           // $this->p($check_dateTime);

            if ( $timestamp_now < $check_dateTimeStamp) {

                $display_date =  self::findAvailableNormalDeliveryDay($now_date,$this->normal_delivery_day,true);


                return date_format($display_date,"d-m-Y")   ;


            } else {

                $display_date =  self::findAvailableNormalDeliveryDay($now_date,$this->normal_delivery_day,false);

                return  date_format($display_date,"d-m-Y")  ;
            }


        }

        return "Shipping Method Not found";

    }


    /**
     * Function to find available date (exlucing Holiday and Weekend)
     *
     * @param DateTime $nowday
     * @param $num_of_days_needed
     * @return DateTime
     */
    private function findAvailableDateNND(DateTime $nowday, $num_of_days_needed)
    {
        //find day avaible
        $holiday_arr = $this->holiday_dates;
        $temp_dates  = new DateTime();

       for($i =1; $i < $num_of_days_needed+1; $i++)
       {
           $nowday->modify( '1 day' );


           $this_day = ((date_format($nowday,"d-m-Y")));


           if (in_array($this_day,$holiday_arr))
           {

               $i--;


           } else {

               if(!self::checkIfWeekendDay($nowday))
               {
                   $temp_dates = $nowday;

               } else {

                   $i--;

               }
           }

       }

        return $temp_dates;

    }

    /**
     * Function to find available Friday(delivery day in plugin)  date (exlucding Holiday )
     * @param DateTime $now_day
     * @param $normal_delivery_day
     * @param $this_week
     * @return DateTime
     */
    private function findAvailableNormalDeliveryDay(DateTime $now_day, $normal_delivery_day, $this_week)
    {
        $holiday_arr = $this->holiday_dates;

        if ($this_week)
        {

            $now_day->modify('next '. $normal_delivery_day);

        } else {

            $now_day->modify('next '. 'monday'  );//restart week day
            $now_day->modify('next '. $normal_delivery_day   );

        }


        for($i =1; $i < 24; $i++) {//maximum loop


            $this_day = ((date_format($now_day,"d-m-Y")));

            if (in_array($this_day,$holiday_arr)) {//if NOrmal delivery dates fall on holiday  find next day (monday)

                $now_day->modify('next day');

            } else {

                if(!self::checkIfWeekendDay($now_day))
                {

                    return $now_day;

                } else {

                    $now_day->modify('next day');

                }

            }
        }

        //TODO:add validation if greater than 24

        return $now_day;
    }


    /**
     * Check If weekend day
     * @param DateTime $date
     * @return bool
     */
    private function checkIfWeekendDay (DateTime $date)
    {
        $get_day_name = date( 'l',strtotime(date_format($date,"d-m-Y")));
        switch($get_day_name){

            case 'Saturday':
                return true;
                break;
            case 'Sunday':
                return true;
                break;

        }

        return false;
    }

    /**
     * Get Shipping method in cart
     * @return bool
     */
    private function getShippingMethodsNDD()
    {

        $Shipping_method_name = self::get_shipping_name_by_id( WC()->session->get( 'chosen_shipping_methods' )[0] );
        if($Shipping_method_name === 'Next Day Delivery')
        {
            return true;

        } else {

            return false;
        }

    }
    public  function get_shipping_name_by_id( $shipping_id ) {
        $packages = WC()->shipping->get_packages();

        foreach ( $packages as $i => $package ) {
            if ( isset( $package['rates'] ) && isset( $package['rates'][ $shipping_id ] ) ) {
                $rate = $package['rates'][ $shipping_id ];
                /* @var $rate WC_Shipping_Rate */
                return $rate->get_label();
            }
        }

        return '';
    }

    public function p($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';

    }

    /**
     * findNearestDayOfWeek
     *
     * @param DateTime $date
     * @param $dayOfWeek
     * @return DateTime
     */
    public function findNearestDayOfWeek(DateTime $date, $dayOfWeek)
    {
        $dayOfWeek = ucfirst($dayOfWeek);
        $daysOfWeek = array(
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        );


        if(!in_array($dayOfWeek, $daysOfWeek)){

            throw new \InvalidArgumentException('Invalid day of week:'.$dayOfWeek);
        }

        if($date->format('l') == $dayOfWeek){

            return $date;
        }

        $previous = clone $date;
        $previous->modify('last '.$dayOfWeek);

        $next = clone $date;
        $next->modify('next '.$dayOfWeek);

        $previousDiff = $date->diff($previous);
        $nextDiff     = $date->diff($next);

        $previousDiffDays = $previousDiff->format('%a');
        $nextDiffDays     = $nextDiff->format('%a');

        if($previousDiffDays < $nextDiffDays){

            return $previous;
        }

        return $next;
    }


}