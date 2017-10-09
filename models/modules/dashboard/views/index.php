<?php
date_default_timezone_set('America/Los_Angeles');
$is_tech = $this->session->userdata("is_tech");
$i = 0;
$j = 0;
$data_session_start = $this->db->get("365admin_valid_session")->result_array();
if ($data_session_start != null) {
    $this->db->where("session_start", $data_session_start[0]['session_start']);
    $this->db->delete("365admin_valid_session");
}


//GET STORE
$this->db->where("user_id", $this->session->userdata('user_id'));
$users_data = $this->db->get("ip_users")->result_array();

$store_id = $users_data[0]['user_store'];


//CHECK IF THE PAYMENT FROM ONLINE ORDERS IS READY
$this->db->select("*");
$this->db->join("ip_stores", "ip_stores.id = ip_quotes.store");
$this->db->join("orders", "orders.oid = ip_quotes.quote_number");
$this->db->where("ip_quotes.active <>", 1);
$this->db->where("ip_quotes.complete <>", 1);
$this->db->where("ip_quotes.payment_status <>", 0);
$this->db->where("orders.oid_ref", 0);
$this->db->where("ip_quotes.fraud", 0);
$ip_quotes = $this->db->get("ip_quotes")->result_array();

$this->db->select("oid, fraud");
$this->db->where("fraud", 1);
$fraud = $this->db->get("orders")->result_array();
foreach ($fraud as $fraud) {
    $this->db->where("quote_number", $fraud['oid']);
    $this->db->set("fraud", 1);
    $this->db->update("ip_quotes");
}

foreach ($ip_quotes as $ip_quotes) {


    $this->db->select("*");
    $this->db->where("oid", $ip_quotes['quote_number']);
    $orders = $this->db->get("orders")->result_array();
    if ($orders[0]['complete'] == 1) {
        //UPDATE COMPLETE ORDERS IN 365ADMIN
        $this->db->where("quote_number", $ip_quotes['quote_number']);
        $this->db->set("complete", 1);
        $this->db->set("complete_log", $ip_quotes['sysdata']);
        $this->db->update("ip_quotes");

        //INSERT INVOICES FROM COMPLETE ORDER
        $invoice_array = array(
            "user_id" => $this->session->userdata("user_id"),
            "client_id" => $ip_quotes['client_id'],
            "invoice_status_id" => 4,
            "is_read_only" => 1,
            "invoice_date_created" => date("Y-m-d"),
            "invoice_time_created" => date("H:i:s"),
            "invoice_number" => $ip_quotes['quote_number'] . "/1",
            "ticket_id" => $ip_quotes['quote_number'],
            "store" => $ip_quotes['store']
        );
        $this->db->insert("ip_invoices", $invoice_array);



        //INSERT IP_AMOUNTS FROM COMPLETE ORDER
        $this->db->select("invoice_id");
        $this->db->where($invoice_array);
        $invoice_data = $this->db->get("ip_invoices")->result_array();
        $invoice_id = $invoice_data[0]['invoice_id'];

        $ip_amounts_array = array(
            "invoice_id" => $invoice_id,
            "invoice_item_subtotal" => $orders[0]['endprice'],
            "invoice_total" => $orders[0]['endprice'] + $orders[0]['endprice_delivery'],
            "invoice_paid" => $orders[0]['endprice'] + $orders[0]['endprice_delivery'],
            "invoice_balance" => 0
        );
        $this->db->insert("ip_invoice_amounts", $ip_amounts_array);

        //INSERT INVOICES FROM COMPLETE ORDER
        $invoice_history_array = array(
            "id_invoice" => $invoice_id,
            "id_status" => 4,
            "date_changed" => date("Y-m-d H:i:s"),
            "user_id" => $this->session->userdata("user_id")
        );
        $this->db->insert("ip_invoices_status_history", $invoice_history_array);

        //INSERT IP_INVOICE_ITEMS
        //INSERT INVOICE_ITEMS
        $this->db->select("*");
        $this->db->where("invoice_id", $invoice_id);
        $rows_invoice_items = $this->db->get("ip_invoice_items")->num_rows();
        @$item_name = unserialize($orders[0]['order']);
        if ($item_name != "") {



            foreach ($item_name as $item_name) {
                if (is_array(@$item_name['attributes'])) {

                    foreach ($item_name['attributes'] as $attributes) {



                        $array_invoice_items = array(
                            "invoice_id" => $invoice_id,
                            "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                            "item_name" => $item_name['p_title'],
                            "item_description" => $attributes['a_title'],
                            "item_price" => $attributes['a_price']
                        );
                        $delivery = array(
                            "invoice_id" => $invoice_id,
                            "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                            "item_name" => "Shipping",
                            "item_description" => "Shipping",
                            "item_price" => $orders[0]['endprice_delivery']
                        );
                        if ($rows_invoice_items == 0) {


                            $this->db->insert("ip_invoice_items", $array_invoice_items);
                        }
                    }
                } else {
                    $array_invoice_items = array(
                        "invoice_id" => $invoice_id,
                        "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                        "item_name" => $item_name['p_title'],
                        "item_description" => $item_name['p_title'],
                        "item_price" => $item_name['p_price']
                    );
                    $delivery = array(
                        "invoice_id" => $invoice_id,
                        "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                        "item_name" => "Shipping",
                        "item_description" => "Shipping",
                        "item_price" => $orders[0]['endprice_delivery']
                    );
                    if ($rows_invoice_items == 0) {


                        $this->db->insert("ip_invoice_items", $array_invoice_items);
                    }
                }
            }
            if ($orders[0]['endprice_delivery'] != 0) {

                $this->db->insert("ip_invoice_items", $delivery);
            }
        }
        //INSERT IP_PAYMENTS FROM COMPLETE ORDER
        if ($orders[0]['payproc'] == 1) {
            $payproc = "Paypal";
        } else {
            $payproc = "Authorize net";
        }
        $this->db->select("ip_payment_methods.payment_method_id");
        $this->db->join("ip_payment_methods", "ip_payment_methods.payment_method_id = ip_payments.payment_method_id");
        $this->db->where("ip_payment_methods.payment_method_name", $payproc);
        $ip_payments_data = $this->db->get("ip_payments")->result_array();
        $ip_payments_array = array(
            "invoice_id" => $invoice_id,
            "payment_method_id" => $ip_payments_data[0]['payment_method_id'],
            "payment_date" => date("Y-m-d"),
            "payment_time" => date("H:i:s"),
            "payment_amount" => $orders[0]['endprice'] + $orders[0]['endprice_delivery'],
            "store" => $store_id
        );
        $this->db->insert("ip_payments", $ip_payments_array);
    }
}
//VARIABLE DECLARATIONS
$count = 0;
$x = 0;
$y = 0;

$array_aux = array(
    '0' => 'Diagnosing',
    '1' => 'Waiting on approval',
    '2' => 'Ordered Parts',
    '3' => 'Repairing',
    '4' => 'Repair Completed',
    '5' => 'Accepted by client',
    '6' => 'New Order',
    '7' => 'Waiting for package',
    '8' => 'Repair denied',
    '9' => 'Returned to shop'
);

//GET USER STORE
$this->db->select("*");
$this->db->where("user_id", $this->session->userdata('user_id'));
$user_store = $this->db->get("ip_users")->result_array();




//GET NUMBER OF STATUS
$this->db->select("*");
$rows_status = $this->db->get("status")->num_rows();


//GET THE YEARS QUANTITY TO SHOW THE TICKETS
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'years_ticket'");
$get_years_ticket = $this->db->get();
$years_ticket = $get_years_ticket->result_array();

//GET THE DAYS QUANTITY TO SHOW THE TICKETS
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'days_ticket'");
$get_days_ticket = $this->db->get();
$days_ticket = $get_days_ticket->result_array();

//GET THE DAYS QUANTITY TO TURN TICKETS URGENT(GENERAL)
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'days_urgent'");
$get_days_urgent = $this->db->get();
$days_urgent = $get_days_urgent->result_array();


//CREATE THE URGENT ARRAY TO UPDATE THE TABLE IP_QUOTES SETTING
$urgent = array(
    'urgent' => 2
);
//CREATE THE RETURN URGENT ARRAY TO UPDATE THE TABLE IP_QUOTES SETTING
$return_urgent = array(
    'urgent' => 0
);
$middle_urgent = array(
    'urgent' => 1
);

//CHECK WHAT STATUS PARAMETERS ARE SETTED ON SETTINGS AND ADJUST THE DATA
$this->db->select("*");
$this->db->where("setting_key", "show_status_ticket");
$status_ipsettings = $this->db->get("ip_settings")->result_array();

$get_status = str_replace("show_status_ticket[]", "", $status_ipsettings[0]['setting_value']);
$get_status2 = str_replace("=", "", $get_status);
$get_status3 = str_replace("+", " ", $get_status2);
$status_array = explode("&", $get_status3);
$status_array2 = explode("&", $get_status3);











//GET THE TICKETS INFORMATION USING THE SETTINGS AUTOMATIC   

$date_full = date('Y-m-d');
$date_month = $days_ticket[0]['setting_value'];
//$date_year -= $years_ticket[0]['setting_value'];
$date_month_final = date('Y-m-d', strtotime("-$date_month days"));



$this->db->select("*");
$this->db->from("ip_quotes");
$this->db->order_by("urgent", "ASC");
$this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
$this->db->join("status", "status.id = ip_quotes.quote_status_id");


if ($user_store[0]['user_store'] != 1) {

    foreach ($status_array as $status_array) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("status.status", $status_array);
        $this->db->where("ip_quotes.store", $user_store[0]['user_store']);
        $this->db->where("attention", 0);
    }
} else
if ($user_store[0]['user_store'] == 1) {
    foreach ($status_array as $status_array) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("status.status", $status_array);
        $this->db->where("attention", 0);
    }
}

$get_data_ticket = $this->db->get();
$data_ticket = $get_data_ticket->result_array();
$ticket_rows = $get_data_ticket->num_rows();

//GET UNCOMPLETE ROWS
$this->db->select("*");
$this->db->join("orders", "orders.oid = ip_quotes.quote_number");
$this->db->where("ip_quotes.complete <>", 1);
$this->db->where("ip_quotes.payment_status <>", 0);
$this->db->where("ip_quotes.active", 0);
$this->db->where("orders.oid_ref", 0);
$this->db->where("ip_quotes.fraud", 0);
$ip_quotes_rows = $this->db->get("ip_quotes")->num_rows();
//FRAUD ROWS
$this->db->select("*");
$this->db->join("orders", "orders.oid = ip_quotes.quote_number");
$this->db->where("ip_quotes.complete <>", 1);
$this->db->where("ip_quotes.payment_status <>", 0);
$this->db->where("ip_quotes.active", 0);
$this->db->where("orders.oid_ref", 0);
$this->db->where("ip_quotes.fraud", 1);
$ip_fraud_rows = $this->db->get("ip_quotes")->num_rows();


//GET PRIORITY TICKETS QUANTITY
$this->db->select("*");
$this->db->order_by("urgent", "ASC");
$this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
$this->db->join("status", "status.id = ip_quotes.quote_status_id");
if ($user_store[0]['user_store'] != 1) {

    foreach ($status_array2 as $status_array2) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("ip_quotes.store", $user_store[0]['user_store']);
        $this->db->where("status.status", $status_array2);
        $this->db->where("ip_quotes.urgent = 2");
    }
} else if ($user_store[0]['user_store'] == 1) {
    foreach ($status_array2 as $status_array2) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("status.status", $status_array2);
        $this->db->where("ip_quotes.urgent = 2");
    }
}




$urgent_row = $this->db->get("ip_quotes")->num_rows();


//SET THE CARACTERE LIMIT TO DO NOT BROKE TICKETS TABLE STRUCTURE
$caractere_limit = 16;
?>





<script type="text/javascript">

    Dropzone.autoDiscover = true;
    function setback_attention(order_id)
    {
        var return_info = confirm("Do you want to remove this order attention ?");
        if (return_info == true) {
            $.post("<?php echo site_url('dashboard/dashboard/setback_attention'); ?>", {
                order_id: order_id
            },
                    function (data) {

                        location.reload();

                    });
        } else {

        }
    }
    function set_attention(order_id)
    {
        var retVal = confirm("Do you want to set this order as attention ?");
        if (retVal == true) {
            $.post("<?php echo site_url('dashboard/dashboard/set_attention'); ?>", {
                order_id: order_id
            },
                    function (data) {

                        location.reload();

                    });
        } else {

        }

    }

    $(document).ajaxStop(function () {
        $("#loader").hide();
    });


    $(function () {

        $("#filters_form").submit(function (e) {

            //prevent Default functionality
            e.preventDefault();
            $("#loader").show();
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            $.post("<?php echo site_url('quotes/ajax/info_display'); ?>", {
                date_from: date_from,
                date_to: date_to,
                all_data: $("#data_field").val()
            },
                    function (data) {

                        $("#info_display").hide();
                        $("#info_display_2").html(data);
                        $("#info_display_2").show();




                    });




            $.post("<?php echo site_url('quotes/ajax/recent_tickets'); ?>", {
                date_from: date_from,
                date_to: date_to,
                all_data: $("#data_field").val()
            },
                    function (data) {

                        $("#recent_tickets").hide();
                        $("#recent_tickets_2").html(data);
                        $("#recent_tickets_2").show();



                    });
           /* $.post("<?php echo site_url('quotes/ajax/tickets_quantity'); ?>", {
                date_from: date_from,
                date_to: date_to,
                all_data: $("#data_field").val()
            },
                    function (data) {

                        $("#order_overview").hide();
                        $("#panel-quote-overview-2").html(data);
                        $("#panel-quote-overview-2").show();


                    });*/


        });

        $("#reset_button_2").click(function () {

            var d = new Date();
            var strDate = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();


            $("#loader").show();
            $("#data_field").val('');
            $("#date_from").val(strDate);
            $("#date_to").val('');
            $("#info_display_2").hide();
            $("#recent_tickets_2").hide();
            $("#info_display").show();
            $("#recent_tickets").show();
           
            $(document).ajaxStop(function () {
                $("#loader").hide();
            });
        });

        $("#all_button_2").click(function () {
            $("#loader").show();

            $.post("<?php echo site_url('quotes/ajax/tickets_quantity'); ?>", {
                date_from: null,
                date_to: null,
                all: 1
            },
                    function (data) {


                        $("#panel-quote-overview").hide();
                        $("#panel-quote-overview-2").html(data);
                        $("#panel-quote-overview-2").show();


                    });
            $.post("<?php echo site_url('quotes/ajax/info_display'); ?>", {
                date_from: null,
                date_to: null,
                all: 1

            },
                    function (data) {
                        $("#info_display").hide();
                        $("#info_display_2").html(data);
                        $("#info_display_2").show();

                    });
            $(document).ajaxStop(function () {
                $("#loader").hide();
            });
        });

        $("#select_button_2").click(function () {
            if ($("#date_content").is(":visible"))
            {
                $("#date_content").hide();
                $("#date_content_to").hide();
                $("#date_td").hide();
                $("#with_date").hide();
                $("#without_date").show();
            } else
            {
                $("#without_date").hide();
                $("#date_content").show();
                $("#date_content_to").show();
                $("#date_td").show();
                $("#with_date").show();

            }

        });
        var date_to = $("#date_to").val();

        $.post("<?php echo site_url('quotes/ajax/tickets_quantity'); ?>", {
            date_from: null,
            date_to: date_to

        },
                function (data) {

                    $("#panel-quote-overview-2").html(data);
                    $("#panel-quote-overview-2").show();


                });

        setInterval(function () {
            $('#new_clients_div').load('<?php echo site_url('dashboard/new_clients'); ?>');
        }, 1000);
        setInterval(function () {
            $('#update_tickets').load('<?php echo site_url('dashboard/update_tickets'); ?>');
        }, 1000);

    });






    function getValue(id) {

        $.post("<?php echo site_url('quotes/quotes/set_client_on_turn'); ?>", {
            id_client: id
        },
                function (data) {

                });





        $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
    }




</script>



<div id="content" style="height: 1050px;">
    <?php echo $this->layout->load_view('layout/alerts'); ?>









    <div class="column" style="width:auto;">

        <div class="portlet" style=" margin-left:-0.7%;">
            <div class="portlet-header" style="text-align:center; height: 46px;">
                <div class="col-xs-12">
                    <?php if ($this->session->userdata('is_tech') == 0) { ?>
                        <div id="panel-quick-actions" class="panel panel-default quick-actions" style="width:auto;">

                            <div class="panel-heading">
                                <b><?php echo lang('quick_actions'); ?></b>
                            </div>

                            <div class="btn-group btn-group-justified no-margin">
                                <a href="<?php echo site_url('clients/form'); ?>" class="btn btn-default">
                                    <i class="fa fa-user fa-margin"></i>
                                    <span class="hidden-xs"><?php echo lang('add_client'); ?></span>
                                </a>
                                <a href="javascript:void(0)" class="create-quote btn btn-default">
                                    <i class="fa fa-file fa-margin"></i>
                                    <span class="hidden-xs"><?php echo lang('create_quote'); ?></span>
                                </a>
                                <a href="javascript:void(0)" class="create-invoice btn btn-default">
                                    <i class="fa fa-file-text fa-margin"></i>
                                    <span class="hidden-xs"><?php echo lang('create_invoice'); ?></span>
                                </a>
                                <a href="<?php echo site_url('payments/form'); ?>" class="btn btn-default">
                                    <i class="fa fa-credit-card fa-margin"></i>
                                    <span class="hidden-xs"><?php echo lang('enter_payment'); ?></span>
                                </a>
                            </div>

                        </div>
                    <?php } else { ?>  
                        <br><br><br>
                    <?php } ?>
                </div> 
            </div>  


        </div>

        <div class="portlet" style="float:left;">
            <div class="portlet-header">

                <?php if ($this->session->userdata('is_tech') == 0) { ?>
                    <div id="recent_client" class="panel panel-default" >
                        <div class="panel-heading" style="">


                            <b><i class="fa fa-history fa-margin" ></i> <?php echo lang('recent_clients'); ?></b>
                            <span class="pull-right text-muted"><?php //echo lang($quote_status_period);                                                                                                                     ?></span>
                        </div>
                        <div id = "new_clients_div" >



                            <?php $this->load->view('new_clients'); ?>





                            <?php /*
                              <td class="amount">
                              <span class="<?php echo $total['class']; ?>">
                              <?php echo format_currency($total['sum_total']); ?>
                              </span>
                              </td> */ ?>

                            <?php // }                  ?>

                            </table></div>
                    </div>
                <?php } ?>
                <div class="portlet-content" >
                </div>

            </div></div>

        <div  style="width:1056px; float:left; margin-left:-0.5%;" id ="with_date">

            <div class="portlet" style=" ">

                <div class="col-xs-12">

                    <div id="panel-quick-actions" class="panel panel-default">

                        <div class="panel-heading" align="center">
                            <b><?php echo "Filters"; ?></b>
                        </div>

                        <form method="post" action="" id="filters_form">

                            <table class="table table-bordered table-condensed no-margin" border="0" >


                                <tr>
                                    <td>
                                        <div id ="date_content" style="padding-left:15px;">
                                            <div class="input-group" >
                                                <input name="date" id="date_from"
                                                       class="form-control input-sm datepicker"
                                                       value="<?php echo date("m/d/Y") ?>" placeholder = "Date From" style="width:150px; margin-left:-15px;">

                                                <i class="fa fa-calendar fa-fw input-group-addon" style="width:30px; height: 30px; padding-top:8px;"></i>

                                            </div></div></td><td>


                                        <div id ="date_content_to" style="padding-left:15px;">
                                            <div class="input-group" >
                                                <input name="date" id="date_to"
                                                       class="form-control input-sm datepicker"
                                                       value="" placeholder = "Date To" style="width:150px;">
                                                <i class="fa fa-calendar fa-fw input-group-addon" style="width:30px; height: 30px; padding-top:8px;"></i>
                                            </div></div>

                                    </td>
                                    <td align="center"><input type="text" id = "data_field" class="input-sm form-control" placeholder = "Order number, status or client name" style="width:250px;"></td>
                                    <td align="center">
                                        <img src="../../../../assets/default/img/page-loader.gif" alt="" id="loader" style="width:25px; height:25px;" hidden/><button class="btn btn-default" id="search_button" style="margin-left:2px;" >Search</button>
                                        <a class="btn btn-default" id="all_button_2" style="margin-left:5px;">All</a>
                                        <!--<button class="btn btn-default" id="select_button_2" style="margin-left:5px;">Select</button>-->
                                        <a  class="btn btn-default" id="reset_button_2" style="margin-left:5px;">Reset</a>
                                    </td>

                                </tr>
                            </table>
                        </form>
                    </div>
                </div> 



            </div>
        </div>
        <div  style="width:580px; height: 450px; margin-right:15px; float:right; margin-left:10px; border:0px #0000FF solid; overflow: auto;" id ="needing_attention">
            <div id="panel-quick-actions" class="panel panel-default">
                <div class="panel-heading" align="center">
                    <b><?php echo "Needing Attention"; ?></b>
                </div>
                <table align="center">
                    <tr>
                        <?php
                        $ix = 0;
                        if ($attention_orders != null) {
                            foreach ($attention_orders as $attention_orders) {
                                if ($ix < 3) {
                                    ?>
                                    <td  style="border: 1px #ddd solid;  width:190px; float:top; padding:2px;" align="center">
                                        <a href="#" onclick="setback_attention(<?php echo $attention_orders['quote_id']; ?>);"><img src="../../../../assets/default/img/red_circle.png" alt="" style="width:30px; height: 30px; margin-left:-30px; margin-top:-5px;"/></a>
                                        <?php echo "<a href='" . site_url('/quotes/view/' . $attention_orders['quote_id']) . "' style='font-size:24px; font-weight: bold;'>" . $attention_orders['quote_number'] . "</a>"; ?>
                                        <br>
                                        <?php echo "<label style='font-weight: bold;'>" . $attention_orders['client_name'] . "</label>"; ?>
                                        <br>
                                        <?php echo "<span label class='label " . $attention_orders['css_label'] . "'>" . $attention_orders['status'] . "</span>"; ?>
                                    </td>

                                    <?php
                                } else {
                                    ?>
                                </tr>

                                <?php
                                $ix = -1;
                            }
                            $ix++;
                        }
                    } else {
                        ?>
                        <tr><td style="font-style:italic;">
                                No orders needing attention.
                            </td></tr>
                    <?php }
                    ?>

                </table>
            </div>
        </div>
        <div  style="margin-top:-100px; margin-left:-0.7%;" id="info_display_2" hidden></div>
        <div  style="margin-top:-100px;" id="info_display">

            <div class="portlet" >
                <div class="portlet-header" style="width:1210px;">
                    <div class="col-xs-12" style="margin-left:-1.1%;">
                        <div class="panel panel-default" >
                            <div class="panel-heading" style="">

                                <b><i class="fa fa-bar-chart-display fa-margin"></i><?php echo lang('information_display'); ?> | </b><?php
                                if ($data_ticket != NULL) {
                                    echo "Open Tickets(<font color = 'green'><b>" . $ticket_rows . "</b></font>) -    Urgent Tickets(<font color = 'red'><b>" . $urgent_row . "</b></font>)";
                                }
                                ?>
                                <span class="pull-right text-muted"></span>
                            </div>
                            <div class="col-xs-12 col-md-6  smart_panel" style = "margin-left:-16px; width:1195px; height: 330px; border-radius:0px;">

                                <div  class="panel panel-default" style = "width:auto; height: auto;">



                                    <table class="smart_panel">
                                        <tr>

                                            <?php
                                            $x_aux = 0;
                                            $ $i = 0;
                                            $j = $ticket_rows;
                                            if ($data_ticket != NULL) {
                                                while (($i <= $j) || ($i >= $j)) {

                                                    $date_limit = date('Y-m-d', strtotime($data_ticket[$j - 1]['quote_date_created'] . " + " . $data_ticket[$j - 1]['days_urgent'] . " days"));
                                                    ?>


                                                    <?php
                                                    if ($i == 7) {
                                                        echo"</tr><tr>";
                                                        $i = 0;
                                                    }
                                                    $calc_days = $data_ticket[$j - 1]['days_urgent'] / 2;


                                                    $between_date = date_diff(date_create(date('Y-m-d')), date_create($date_limit))->format('%d');
                                                    //echo $between_date;


                                                    if ($date_limit >= date('Y-m-d')) {
                                                        $this->db->where("quote_id", $data_ticket[$j - 1]['quote_id']);
                                                        $this->db->update('ip_quotes', $return_urgent);
                                                    } /* else if ($between_date == $calc_days) {
                                                      $this->db->where("quote_id", $data_ticket[$j - 1]['quote_id']);
                                                      $this->db->update('ip_quotes', $middle_urgent);
                                                      } */ else if ($date_limit < date('Y-m-d')) {
                                                        $this->db->where("quote_id", $data_ticket[$j - 1]['quote_id']);
                                                        $this->db->update('ip_quotes', $urgent);
                                                    }
                                                    ?>


                                                    <td style="background-image:url(../../../../assets/default/img/<?php
                                                    if ($data_ticket[$j - 1]['urgent'] == 2) {


                                                        echo "paper_urgent.png";
                                                    } else if ($data_ticket[$j - 1]['urgent'] == 1) {
                                                        echo "paper_yellow.png";
                                                    } else {

                                                        echo "paper_white.png";
                                                    }
                                                    ?>);<?php ?>" >
                                                        <?php if ($data_ticket[$j - 1]['urgent'] == 2) {
                                                            ?>
                                                            <a href ="#" onclick="set_attention(<?php echo $data_ticket[$j - 1]['quote_id']; ?>);"><img src="../../../../assets/default/img/orange_circle.png" alt="" style="width:30px; height: 30px; margin-left:-30px;"/></a>
                                                            <?php
                                                        } else if ($data_ticket[$j - 1]['urgent'] == 1) {
                                                            ?>
                                                            <a href ="#" onclick="set_attention(<?php echo $data_ticket[$j - 1]['quote_id']; ?>);"><img src="../../../../assets/default/img/yellow_circle.png" alt="" style="width:30px; height: 30px; margin-left:-30px;"/></a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <a href ="#" onclick="set_attention(<?php echo $data_ticket[$j - 1]['quote_id']; ?>);"><img src="../../../../assets/default/img/white_circle.png" alt="" style="width:30px; height: 30px; margin-left:-30px;"/></a>
                                                            <?php
                                                        }
                                                        ?>
                                                        <font style="font-size:24px; font-weight: bold;"><?php echo anchor('quotes/view/' . $data_ticket[$j - 1]['quote_id'], ($data_ticket[$j - 1]['quote_number'] ? $data_ticket[$j - 1]['quote_number'] : $data_ticket[$j - 1]['quote_id'])) . ""; ?></font>
                                                        <br><?php
                                                        if (strlen($data_ticket[$j - 1]['client_name']) < $caractere_limit) {
                                                            echo $data_ticket[$j - 1]['client_name'];
                                                        } else if (strlen($data_ticket[$j - 1]['client_email']) > $caractere_limit) {
                                                            echo substr($data_ticket[$j - 1]['client_name'], 0, $caractere_limit) . '...';
                                                        }
                                                        ?>
                                                        <br><?php
                                                        if (($data_ticket[$j - 1]['client_email'] != "") && (strlen($data_ticket[$j - 1]['client_email']) < $caractere_limit)) {
                                                            echo $data_ticket[$j - 1]['client_email'];
                                                        }
                                                        ?>
                                                        <?php
                                                        if (strlen($data_ticket[$j - 1]['client_email']) > $caractere_limit) {

                                                            echo substr($data_ticket[$j - 1]['client_email'], 0, $caractere_limit) . '...';
                                                        }
                                                        ?>
                                                        <br><?php echo $data_ticket[$j - 1]['client_phone']; ?>
                                                        <br><?php echo "<span label class='label " . $data_ticket[$j - 1]['css_label'] . "' style='width:120px; margin-left:30px;'>" . $data_ticket[$j - 1]['status'] . "</span>"; ?>

                                                    </td>






                                                    <?php
                                                    $i++;
                                                    $j--;
                                                    if ($j == 0) {
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>

                                    </table>
                                </div>
                            </div>


                        </div>



                    </div></div>     
            </div></div>




        <div id="second_line" style="float:left; ">

            <?php if ($is_tech == 0) { ?>
                <div style="padding-top:10px; float:left; height: 0px; ">

                    <div class="panel panel-default overview" style="width:250px;">
                        <div class="panel-heading" style="" >

                            <b><i class="fa fa-bar-chart fa-margin"></i> <?php echo lang('quote_overview'); ?></b>
                            <span class="pull-right text-muted"><?php echo lang($quote_status_period); ?></span>
                        </div>



                    </div>
                    <div id="panel-quote-overview-2" class="panel panel-default overview"  style="margin-top:-21px; width:250px; height: 335px; overflow: auto;" hidden></div>
                    <div id="panel-quote-overview" id='order_overview' class="panel panel-default overview" style="margin-top:-21px; width:250px; height: 335px; overflow: auto;" hidden>



                        <table class="table table-bordered table-condensed no-margin">


                            <?php
                            foreach ($quote_status_totals as $total) {
                                if ($total == '') {
                                    break;
                                }
                                ?>
                                <tr>
                                    <td>

                                        <a href="<?php echo site_url($total['href']); ?>">
                                            <?php
                                            $this->db->select("id");
                                            $this->db->from("status");
                                            $this->db->where("status = '" . $total['label'] . "'");

                                            $result_object = $this->db->get();
                                            $result = $result_object->result_array();

                                            $this->db->select("*");
                                            $this->db->where("quote_status_id ", $result[0]['id']);
                                            if ($user_store[0]['user_store'] != 1) {
                                                $this->db->where("store", $user_store[0]['user_store']);
                                            }
                                            $qtd = $this->db->get("ip_quotes")->num_rows();

                                            echo $total['label'] . "(<font color = 'green'>0</font>)";
                                            ?>
                                        </a>
                                    </td>
                                    <?php /*
                                      <td class="amount">
                                      <span class="<?php echo $total['class']; ?>">
                                      <?php echo format_currency($total['sum_total']); ?>
                                      </span>
                                      </td> */ ?>
                                </tr>
                            <?php } ?>
                        </table>


                    </div>
                </div> <?php } ?>
            <div class="portlet" style="width:948px; float:left; padding-top:10px;" id="recent_tickets_2" hidden></div>
            <?php if ($is_tech == 0) { ?>
                <div class="portlet" style="width:948px; float:left; padding-top:10px; margin-left:-8px;" id="recent_tickets">
                    <div class="portlet-header" >
                        <div class="col-xs-12">
                            <div id="panel-quote-overview" class="panel panel-default" >
                                <div class="panel-heading" style="">
                                    <b><i class="fa fa-history fa-margin"></i> <?php echo lang('recent_quotes'); ?></b> 
                                    <label style="float:right;"><a href = "<?php echo site_url('/quotes/status/uncomplete'); ?>">Uncomplete(<?php echo "<font color ='red'>" . $ip_quotes_rows . "</font>"; ?>) or denied orders</a> | <a href = "<?php echo site_url('/quotes/status/fraud'); ?>">Fraud(<?php echo "<font color ='red'>" . $ip_fraud_rows . "</font>"; ?>)</a></label>

                                </div>

                                <div class="table-responsive" style="height: 330px;">
                                    <table class="table table-striped table-condensed no-margin">
                                        <thead>
                                            <tr>
                                                <th><?php echo lang('status'); ?></th>
                                                <th style="min-width: 15%;"><?php echo lang('date'); ?></th>
                                                <th style="min-width: 15%;"><?php echo lang('quote'); ?></th>
                                                <th style="min-width: 20%;"><?php echo lang('client'); ?></th>
                                                <th style="min-width: 15%;"><?php echo 'Market'; ?></th>
                                                <th><?php echo lang('pdf'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $aux = 0;

                                            $this->db->join("status", "status.id = ip_quotes.quote_status_id");
                                            $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
                                            $this->db->where("ip_quotes.complete", 1);
                                            $this->db->where("ip_quotes.payment_status <>", 0);
                                            if ($store_id != 1) {
                                                $this->db->where("ip_quotes.store", $store_id);
                                            }
                                            $this->db->or_where("status.status", "Repair Completed");
                                            $this->db->or_where("status.status", "Accepted by client");
                                            $this->db->or_where("status.status", "Repair complete");
                                            $this->db->or_where("status.status", "Accepted by client");
                                            $this->db->limit("8");
                                            $this->db->order_by("ip_quotes.quote_number", "desc");

                                            $quotes = $this->db->get("ip_quotes")->result_array();

                                            foreach ($quotes as $quote) {

                                                //GET CSS LABEL
                                                $this->db->select("css_label,status");
                                                $this->db->where("id", $quote['quote_status_id']);
                                                $status_css_label = $this->db->get("status")->result_array();

                                                //GET MARKET(STORE)
                                                $this->db->select("*");
                                                $this->db->where("id", $quote['store']);
                                                $market_data = $this->db->get("ip_stores")->result_array();
                                                ?> <tr>
                                                    <td>
                                                        <span class="label <?php echo $status_css_label[0]['css_label']; ?>">
                                                            <?php
                                                            if ($status_css_label[0]['status'] == "New Order") {
                                                                if (($market_data[0]['store_name'] == "Hawthorne") || ($market_data[0]['store_name'] == "Venice") || ($market_data[0]['store_name'] == "Usc Repair")) {
                                                                    //echo "Draft";
                                                                    echo $status_css_label[0]['status'];
                                                                } else {
                                                                    echo $status_css_label[0]['status'];
                                                                }
                                                            } else if ($status_css_label[0]['status'] == "Accepted by client") {
                                                                if (($market_data[0]['store_name'] == "Hawthorne") || ($market_data[0]['store_name'] == "Venice") || ($market_data[0]['store_name'] == "Usc Repair")) {
                                                                    //echo "Paid";
                                                                    echo $status_css_label[0]['status'];
                                                                } else {
                                                                    echo $status_css_label[0]['status'];
                                                                }
                                                            } else if ($status_css_label[0]['status'] == "Repair Completed") {
                                                                if (($market_data[0]['store_name'] == "Hawthorne") || ($market_data[0]['store_name'] == "Venice") || ($market_data[0]['store_name'] == "Usc Repair")) {
                                                                    //echo "Sent";
                                                                    echo $status_css_label[0]['status'];
                                                                } else {
                                                                    echo $status_css_label[0]['status'];
                                                                }
                                                            } else {
                                                                echo $status_css_label[0]['status'];
                                                            }
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo date_from_mysql($quote['quote_date_created']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo anchor('quotes/view/' . $quote['quote_id'], ($quote['quote_number'] ? $quote['quote_number'] : $quote['quote_id'])); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo anchor('clients/view/' . $quote['client_id'], $quote['client_name']); ?>
                                                    </td>
                                                    <td>
                                                        <a href ="<?php echo site_url("stores"); ?>"> <?php echo $market_data[0]['store_name']; ?>
                                                        </a>
                                                    </td>
                                                    <!-- <td class="amount">
                                                    <?php //echo format_currency($quote->quote_total);                                ?>
                                                    </td> -->
                                                    <td colspan="2" style="text-align: center;">
                                                        <a href="<?php echo site_url('quotes/generate_pdf/' . $quote['quote_id']); ?>"
                                                           title="<?php echo lang('download_pdf'); ?>" target="_blank">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <?php
                                                $aux++;
                                                if ($aux == 8) {
                                                    break;
                                                }
                                            }
                                            ?>

                                            <tr>
                                                <td colspan="6" class="text-right small">
                                                    <?php echo anchor('quotes/status/all', lang('view_all')); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="portlet-content">


                            </div>
                        </div>  
                    </div></div> <?php } ?>
            <div>
                <?php if ($is_tech == 0) { ?>
                    <div id="panel-quote-overview" class="panel panel-default overview" style="float:left; width: 250px; margin-top:-10px;">

                        <div class="panel-heading" style="">
                            <b><i class="fa fa-bar-chart fa-margin"></i> <?php echo lang('invoice_overview'); ?></b>
                            <span class="pull-right text-muted"><?php echo lang($invoice_status_period); ?></span>
                        </div>




                        <table class="table table-bordered table-condensed no-margin">
                            <?php foreach ($invoice_status_totals as $total) { ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo site_url($total['href']); ?>">
                                            <?php
                                            if ($total['label'] == "Draft") {
                                                echo "Estimates";
                                            } else if ($total['label'] == "Viewed") {
                                                echo "Denied";
                                            } else {
                                                echo $total['label'];
                                            }
                                            ?>
                                        </a>
                                    </td>
                                    <td class="amount">
                                        <span class="<?php echo $total['class']; ?>">
                                            <?php echo format_currency($total['sum_total']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>

                    </div><?php } ?>
            </div>



            <div class="portlet-header" style=" padding-left:8px; float:left; margin-top:-10px;  width:926px;">

                <?php if ($is_tech == 0) { ?>
                    <div id="panel-recent-quotes" class="panel panel-default">
                        <div class="panel-heading" style="">
                            <b><i class="fa fa-history fa-margin"></i> <?php echo lang('recent_invoices'); ?></b>
                        </div>
                        <div class="table-responsive" style="height:135px; overflow:auto;">
                            <table class="table table-striped table-condensed no-margin" style="">
                                <thead>
                                    <tr>
                                        <th><?php echo lang('status'); ?></th>
                                        <th style="min-width: 15%;"><?php echo lang('due_date'); ?></th>
                                        <th style="min-width: 15%;"><?php echo lang('invoice'); ?></th>
                                        <th style="min-width: 35%;"><?php echo lang('client'); ?></th>
                                        <th style="text-align: right;"><?php echo lang('balance'); ?></th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    foreach ($invoices as $invoice) {
                                        if ($this->config->item('disable_read_only') == true) {
                                            $invoice->is_read_only = 0;
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <span
                                                    class="label <?php echo $invoice_statuses[$invoice->invoice_status_id]['class']; ?>">
                                                        <?php
                                                        if ($invoice_statuses[$invoice->invoice_status_id]['label'] == "Draft") {
                                                            echo "Estimates";
                                                        } else if ($invoice_statuses[$invoice->invoice_status_id]['label'] == "Viewed") {
                                                            echo "Denied";
                                                        } else {
                                                            echo $invoice_statuses[$invoice->invoice_status_id]['label'];
                                                        }
                                                        if ($invoice->invoice_sign == '-1') {
                                                            ?>
                                                        &nbsp;<i class="fa fa-credit-invoice"
                                                                 title="<?php echo lang('credit_invoice') ?>"></i>
                                                                 <?php
                                                             }
                                                             if ($invoice->is_read_only == 1) {
                                                                 ?>
                                                        &nbsp;<i class="fa fa-read-only"
                                                                 title="<?php echo lang('read_only') ?>"></i>
                                                             <?php }; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="<?php if ($invoice->is_overdue) { ?>font-overdue<?php } ?>">
                                                    <?php echo date_from_mysql($invoice->invoice_date_due); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo anchor('invoices/view/' . $invoice->invoice_id, ($invoice->invoice_number ? $invoice->invoice_number : $invoice->invoice_id)); ?>
                                            </td>
                                            <td>
                                                <?php echo anchor('clients/view/' . $invoice->client_id, $invoice->client_name); ?>
                                            </td>
                                            <td class="amount">
                                                <?php echo format_currency($invoice->invoice_balance * $invoice->invoice_sign); ?>
                                            </td>

                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="6" class="text-right small">
                                            <?php echo anchor('invoices/status/all', lang('view_all')); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div> 
                <?php } ?>


            </div>
        </div>
    </div>
</div>
<style>
    .showme{ 
display: none;
}
.showhim:hover .showme{
display : block;
}
</style>


<div class="showhim">HOVER ME<div class="showme">hai</div></div>




