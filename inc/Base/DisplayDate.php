<?php

namespace  Inc\Base;
use DateTime;

class DisplayDate extends BaseController
{
    private $closing_day ;
    private $cut_off_time ;
    private $holiday_dates ;


    public function register()
    {
       // add_action( 'woocommerce_single_product_summary', array($this,'display_delivery_date'),25,2);
       // add_action( 'woocommerce_before_add_to_cart_button',  array($this,'display_delivery_date'),10,2 );
        add_filter( 'woocommerce_get_item_data', array($this,'display_delivery_date_in_cart'), 10, 2 );

    }

    public function display_delivery_date() {

        $output = self::calculateDeliveryDate();

        echo $output;
    }


    public function getDataFromPlugin(){

        settings_fields('delivery_options_group');

        $this->closing_day      =  esc_attr(get_option('closing_day'));
        $this->cut_off_time     =  esc_attr(get_option('cut_off_time'));
        $this->holiday_dates    =  explode(',', esc_attr(get_option('holiday_dates')));


    }



    public function calculateDeliveryDate()
    {
        self::getDataFromPlugin();

        $nowdate            = new DateTime();
        $checkdateTime      = new DateTime();
        $nddcheckdateTime   = new DateTime();

        $checkdateTime->modify($this->closing_day . ' ' .$this->cut_off_time );



        if (self::getShippingMethodsNDD()) {//check for next day delivery

            /**
             * Check Cut off time for ndd
             * check for sunday and saturday
             */

            $nddcheckdateTime->modify($this->cut_off_time );

             if ($nowdate < $nddcheckdateTime )
             {
                 //find next avaible 1 day
                 //$nowdate->modify('tomorrow');


                 $display_date = self::findAvailableDate($nowdate,1);

             } else {

                 //$nowdate->modify('2 day');
                 $display_date =  self::findAvailableDate($nowdate,2);
             }


            return date_format($display_date,'d-m-Y');




        }




        if (!self::getShippingMethodsNDD()) {//Not next day

            if ($nowdate < $checkdateTime ) {


                $nowdate->modify('next friday');
                return date_format($nowdate,"Y-m-d") . 'less Than' . $this->cut_off_time  ;


            } else {
                $nowdate->modify( 'next friday + 1 week ' );
                return  date_format($nowdate,"Y-m-d") . ' Greater Than' . $this->cut_off_time ;
            }

        }




    }


    /**
     * Function to find available date (exlucing Holiday and Weekend)
     *
     * @param DateTime $nowday
     * @param $num_of_days_needed
     * @return DateTime
     */
    public function findAvailableDate(DateTime $nowday, $num_of_days_needed)
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




    public function checkIfWeekendDay (DateTime $date)
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
    public function checkforholiday(DateTime $finaldate)
    {
        $nowdate     = new DateTime();
        $holiday_arr = $this->holiday_dates;

        $count_holilday_dates = 0;

        foreach ($holiday_arr as $holiday_day)
        {
            $holiday_date = new DateTime($holiday_day);
            $get_day_name = date( 'l',strtotime(date_format($holiday_date,"d-m-Y")));

            if( ( $holiday_date > $nowdate)  && ( $holiday_date <= $finaldate) )
            {
                if(self::checkIfWeekendDay($get_day_name) <= 0){//if holiday fall in weekend exclude

                    $count_holilday_dates++ ;
                    $finaldate = date_modify($holiday_date, $count_holilday_dates. ' day');
                    //echo date_format($finaldate,"d-m-Y") ;
                }
            }

        }

        $finale_day_name = date( 'l',strtotime(date_format($finaldate,"d-m-Y")));

        $additional_weekend_day = self::checkIfWeekendDay($finale_day_name);
        $count_holilday_dates   =$count_holilday_dates+ $additional_weekend_day;


        return $count_holilday_dates;



    }
    public function getShippingMethodsNDD()
    {

        $Shipping_method_name = self::get_shipping_name_by_id( WC()->session->get( 'chosen_shipping_methods' )[0] );
        if($Shipping_method_name === 'Next Day Delivery')
        {
            return true;

        } else {

            return false;
        }

    }
    public function get_shipping_name_by_id( $shipping_id ) {
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
    public function display_delivery_date_in_cart( $item_data, $cart_item) {


        $item_data[] = array(
            'key'     => __( 'Estimated Delivery Date' ),
            'value'   => self::calculateDeliveryDate(),
            'display' => '',
        );


        return $item_data;
    }
    public function p($value)
    {
        echo '<pre>';
        print_r($value);
        echo '</pre>';

    }










}