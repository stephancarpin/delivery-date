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
        //Todo: Check Server date and time


        add_action( 'woocommerce_cart_totals_after_shipping', array($this,'action_woocommerce_cart_totals_after_shipping'), 30, 0 );
        add_action( 'woocommerce_review_order_before_shipping', array($this,'action_woocommerce_cart_totals_after_shipping'), 25, 0 );
        add_action( 'woocommerce_email_after_order_table', array($this,'action_woocommerce_email'), 10, 1 );
        add_filter('woocommerce_thankyou_order_received_text',  array($this,'woo_change_order_received_text'), 10, 2 );

        //***Save Data in order post**//
        add_action('woocommerce_review_order_before_payment',  array($this,'custom_checkout_field'), 10, 0 );//but hidden
        add_action('woocommerce_checkout_process',  array($this,'customised_checkout_field_process'),10,0);
        add_action('woocommerce_checkout_update_order_meta', array($this,'custom_checkout_field_update_order_meta'),10,1);
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'display_custom_meta_data_in_backend_orders'), 10, 1 );

    }


    /**
     * Hide custom field to store Estimated Delivery date
     */
    public function custom_checkout_field()
    {
        woocommerce_form_field('custom_field_name', array(

            'type'          => 'hidden',
            'class'         => array('hidden form-row-wide'),
        ) );
    }

    /**
     * Set Output Date to post
     * Notice if null
     */
    public function customised_checkout_field_process()
    {

        $_POST['customised_field_name'] = sanitize_text_field($this->calculate_delivery_date());
        // Show an error message if the field is not set.
        if (!$_POST['customised_field_name']) wc_add_notice(__('Wrong estimated delivery date') , 'error');

    }

    public function custom_checkout_field_update_order_meta($order_id)
    {

        if (!empty($_POST['customised_field_name'])) {

            update_post_meta($order_id, '_estimated_delivery_date',sanitize_text_field($_POST['customised_field_name']));

        }

    }

    /**
     * Initialise Field from wordpress
     */
    public function init()
    {

        settings_fields('delivery_options_group');
        $this->closing_day      =  esc_attr(get_option('closing_day'));
        $this->cut_off_time     =  esc_attr(get_option('cut_off_time'));
        $this->normal_delivery_day     =  esc_attr(get_option('normal_delivery_day'));
        $this->holiday_dates    =  explode(',', esc_attr(get_option('holiday_dates')));

    }

    /**
     * filter to display date in cart total
     */
    public function action_woocommerce_cart_totals_after_shipping( ) {

        $this->init();

        $html = "<tr>
                   <th>Estimated Delivery Date</th>
                   <td style='font-weight: bolder'>" . $this->calculate_delivery_date() ."</td>
                 </tr>" ;

        echo $html;

    }

    /**
     * Display in Emails
     * @param $order
     */
    public function action_woocommerce_email($order) {

        $from_db_estimated_date =  get_post_meta($order->get_order_number(),'_estimated_delivery_date')[0];

        $html = "<p style='margin:0 0 16px'>
                     <strong>Estimated Delivery Date: </strong> 
                     ".$from_db_estimated_date."
                 </p>";


        echo $html;

    }

    /**
     * Review Order
     * @param $str
     * @param $order
     * @return string
     */
    public function woo_change_order_received_text( $str, $order ) {

        $from_db_estimated_date =  get_post_meta($order->get_order_number(),'_estimated_delivery_date')[0];

        $html = "<div style='t;padding-top: 10px;padding-bottom: 10px'>
                     <h4 style='font-weight: bolder'>Estimated Delivery Date:  " .  $from_db_estimated_date ."</h4>
                </div>" ;


        return $str . $html;
    }


    /**
     * To display Estimated date in back office orders
     * @param $order
     */
   public function display_custom_meta_data_in_backend_orders( $order ){
        $domain = 'woocommerce';

       $from_db_estimated_date =  get_post_meta($order->get_order_number(),'_estimated_delivery_date')[0];
        if( ! empty( $from_db_estimated_date ) )
            echo '<p><strong>'.__('Estimated Delivery Date', $domain).': </strong> ' . $from_db_estimated_date . '</p>';
    }

    /**
     * @return false|string
     */
    private function calculate_delivery_date()
    {
        switch($this->get_shipping_methods()){


            case Enums::FREE_SHIPPING:
                return $this->output_delivery_date(Enums::FREE_SHIPPING) ;
                break;
            case Enums::NEXT_DAY_DELIVERY:
                return $this->output_delivery_date(Enums::NEXT_DAY_DELIVERY) ;
                break;
            case Enums::INTERNATIONAL_DELIVERY:
                return $this->output_delivery_date(Enums::INTERNATIONAL_DELIVERY);
                break;

        }
        return "Shipping Method not found";

    }


    /**
     * Function to find available date (excluding Holiday and Weekend)
     *
     * @param DateTime $now
     * @param $num_of_days_needed
     * @return DateTime
     */
    private function find_available_date_NND(DateTime $now, $num_of_days_needed)
    {
        //find day avaible
        $holiday_arr = $this->holiday_dates;
        $temp_dates  = new DateTime();

       for($i =1; $i < $num_of_days_needed+1; $i++)
       {
           $now->modify( '1 day' );


           $this_day = ((date_format($now,"d-m-Y")));


           if (in_array($this_day,$holiday_arr))
           {

               $i--;


           } else {

               if(!self::check_if_weekend_day($now))
               {
                   $temp_dates = $now;

               } else {

                   $i--;

               }
           }

       }

        return $temp_dates;

    }

    /**
     * Function to find available Friday(delivery day in plugin)  date (exlucding Holiday )
     * @param DateTime $now
     * @param $normal_delivery_day
     * @param $this_week
     * @return DateTime
     */
    private function find_available_normal_delivery_day(DateTime $now, $normal_delivery_day, $this_week)
    {
        $holiday_arr = $this->holiday_dates;

        if ($this_week)
        {

            $now->modify('next '. $normal_delivery_day);

        } else {

            $now->modify('next '. 'monday'  );//restart week day
            $now->modify('next '. $normal_delivery_day   );

        }


        for($i =1; $i < 24; $i++) {//maximum loop


            $this_day = ((date_format($now,"d-m-Y")));

            if (in_array($this_day,$holiday_arr)) {//if NOrmal delivery dates fall on holiday  find next day (monday)

                $now->modify('next day');

            } else {

                if(!$this->check_if_weekend_day($now))
                {

                    return $now;

                } else {

                    $now->modify('next day');

                }

            }
        }

        //TODO:add validation if greater than 24

        return $now;
    }


    /**
     * Check If weekend day
     *
     * @param DateTime $date
     * @return bool
     */
    private function check_if_weekend_day (DateTime $date)
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
     * Retrieve shipping methods base on $shipping_id
     * @return mixed
     */
    private function get_shipping_methods()
    {
        $shipping_id = WC()->session->get( 'chosen_shipping_methods' )[0];
        global $woocommerce;

        $Shipping_method_name = $woocommerce->session->shipping_for_package_0['rates'][$shipping_id]->label;
        return $Shipping_method_name;
    }

    /** to delete
     * @param $shipping_id
     * @return mixed
     */
    public  function get_shipping_name_by_id( $shipping_id ) {

//        $packages = WC()->shipping->get_packages();
//        foreach ( $packages as $i => $package ) {
//            if ( isset( $package['rates'] ) && isset( $package['rates'][ $shipping_id ] ) ) {
//               // self::p($package['rates']);
//                $rate = $package['rates'][ $shipping_id ];
//                /* @var $rate WC_Shipping_Rate */
//                return $rate->get_label();
//            }
//        }


        global $woocommerce;

        $packages = $woocommerce->session->shipping_for_package_0['rates'][$shipping_id]->label;

        self::p($packages);
        return $packages;
    }

    public function p($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';

    }

    /**
     * find_Nearest_Day_Of_Week
     *
     * @param DateTime $date
     * @param $dayOfWeek
     * @return DateTime
     */
    public function find_nearest_day_of_week(DateTime $date, $dayOfWeek)
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

    /**
     * To output delivery date based on shipping Methods
     * @param $shipping_method
     * @return false|string
     */
    private function output_delivery_date($shipping_method)
    {
        /**
         * Free shipping (methods)
         * Check Cut off time for ndd
         * check for sunday and saturday
         */

        $now_date             = new DateTime();
        $ndd_check_dateTime   = new DateTime();

        // for debugging date
        // $now_date->modify(' wednesday 14:01:00.000000'  );
        // $now_date->setTime(23, 00);;

        $timestamp_now = $now_date->getTimestamp();


        if ($shipping_method == Enums::FREE_SHIPPING)
        {

            if($this->check_if_weekend_day($now_date))
            {

                $now_date->modify('monday');//reinitialise day of the week

            }

            //TODO:add situation where customer add to cart on a holiday date(should take next day available)



            $check_dateTime  = $this->find_nearest_day_of_week($now_date,$this->closing_day);


            $check_dateTime->modify( $this->cut_off_time );


            $check_dateTimeStamp = $check_dateTime->getTimestamp();


            /**
             * Enable for debug
             */
            // $this->p($now_date);
            // $this->p($check_dateTime);

            if ( $timestamp_now < $check_dateTimeStamp) {

                $display_date =  $this->find_available_normal_delivery_day($now_date,$this->normal_delivery_day,true);


                return date_format($display_date,"d-m-Y")   ;


            } else {

                $display_date =  $this->find_available_normal_delivery_day($now_date,$this->normal_delivery_day,false);

                return  date_format($display_date,"d-m-Y")  ;
            }

        }

        /**
         * Next day delivery
         */

        if ($shipping_method == Enums::NEXT_DAY_DELIVERY)
        {
            $ndd_check_dateTime->modify($this->cut_off_time );

            if ($now_date < $ndd_check_dateTime )
            {

                $display_date = $this->find_available_date_NND($now_date,1);

            } else {

                $display_date =  $this->find_available_date_NND($now_date,2);
            }


            return date_format($display_date,'d-m-Y');
        }

        /**
         * International Delivery*
         */


        if ($shipping_method == Enums::INTERNATIONAL_DELIVERY)
        {
            return '1 week';
        }

        return 'N/A';
    }


}