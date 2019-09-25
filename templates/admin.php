<div class="wrap">
    <h1>Dashboard For Delivery Date</h1>
    <?php settings_errors();?>



    <?php
    $d=strtotime("next Saturday");

    echo date("d-m-Y h:i:sa", $d) . "<br>";
    ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">Manage Settings</a></li>
        <li><a href="#tab-2">Holidays</a></li>
        <li><a href="#tab-3">About</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active" >
            <form method="post" action="options.php">
                <?php

                settings_fields('delivery_options_group');

                do_settings_sections('deliverydate_plugin');
                submit_button();
                ?>

            </form>


        </div>

        <div id="tab-2" class="tab-pane " >
            <title>Multiple day selection</title>




        </div>

        <div id="tab-3" class="tab-pane " >
            <h3>About</h3>
        </div>


    </div>


</div>

