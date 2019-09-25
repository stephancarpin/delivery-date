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
        $check_dateTime     = new DateTime();
        $nddcheckdateTime   = new DateTime();


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


        if (!self::getShippingMethodsNDD()) {//Not next day

            $check_dateTime->modify($this->closing_day . ' ' .$this->cut_off_time );

            if ($now_date < $check_dateTime ) {


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
     * Function to find available Friday  date (exlucing Holiday )
     * @param DateTime $now_day
     * @param $this_week
     * @return DateTime
     */
    private function findAvailableNormalDeliveryDay(DateTime $now_day, $normal_delivery_day, $this_week)
    {
        $holiday_arr = $this->holiday_dates;

        //$this->p($normal_delivery_day);

        if ($this_week)
        {

            $now_day->modify('next '. $normal_delivery_day);

        } else {

            $now_day->modify('next '. $normal_delivery_day.' + 1 week');

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
        print_r($value);
        echo '</pre>';

    }















}