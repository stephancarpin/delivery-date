<div class="wrap">
    <h1>Dashboard For Delivery Date</h1>
    <?php settings_errors();?>
    <?php
    $d=strtotime("today");

            echo date("d-m-Y h:i:sa", $d) . "<br>";
    ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">Manage Settings</a></li>
        <li><a href="#tab-3">About</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active" >
            <form method="post" action="options.php">
                <?php

                settings_fields('delivery_options_group');

                do_settings_sections('deliverydate_plugin');

                ?>
                <p><?php submit_button();?></p>

            </form>
        </div>

        <div id="tab-3" class="tab-pane " >
            <h3>About</h3>
            <h5>Custom Delivery Date(Estimation) plugin </h5>
            <div class="">
            <ul>
                <li>Delivery Only on Normal Delivery Day</li>
                <li>Except if holiday then next available day of next week</li>
                <li>Delivery Exclude Weekends and Holidays</li>
                <li>Order after closing day and cut off time will be delivered on next  Normal Delivery Day </li>
                <li>Next day delivery depend on shipping method (Next Day Delivery). Shipping method must exist</li>
                <li>Next day delivery: excluding weekend and holidays</li>
            </ul>
                <div>
                <h3>Requirements for plugin to work</h3>
                    <p>Should create shipping methods :</p>
                    <p>Free shipping</p>
                    <p>Next Day Delivery</p>
                    <p>International Delivery*</p>
                </div>
            </div>
        </div>
    </div>
</div>


