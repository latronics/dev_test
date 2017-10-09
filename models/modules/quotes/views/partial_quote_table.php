<?php
date_default_timezone_set('America/Los_Angeles');
//CHECK USER STORE
$this->db->select("user_store, show_signature");
$this->db->join("ip_stores", "ip_stores.id = ip_users.user_store");
$this->db->where("user_id", $this->session->userdata("user_id"));
$user_data = $this->db->get("ip_users")->result_array();

//GET NUM_ROWS FROM IP_QUOTES
if ($user_data[0]['user_store'] != 1) {
    $this->db->where("store", $user_data[0]['user_store']);
}
$num_rows = $this->db->get("ip_quotes")->num_rows();

//GET DATA FROM IP_TICKET_NUM_ROWS
$this->db->select("*");
$this->db->where("user_id", $this->session->userdata("user_id"));
$ip_ticket_num_rows = $this->db->get("ip_ticket_num_rows")->result_array();
$ip_ticket_lines = $this->db->get("ip_ticket_num_rows")->num_rows();

$URL = $_SERVER['REQUEST_URI'];
$client_id = str_replace('/index.php/clients/view/', '', $URL);


if ($client_id == "/index.php/quotes/status/" . $this->uri->segment(3)) {
    $client_id = -1;
} else if ($client_id == "/index.php/quotes/status/" . $this->uri->segment(3) . "/" . $this->uri->segment(4)) {
    $client_id = -1;
}

//VALIDATE URL CLIENTS/VIEW
$check_url = stripos($URL, 'clients/view/');
//VALIDATE URL QUOTES/STATUS/UNCOMPLETE
$check_url_2 = stripos($URL, 'quotes/status/uncomplete');
//VALIDATE URL QUOTES/STATUS/FRAUD
$check_url_3 = stripos($URL, 'quotes/status/fraud');




if ($ip_ticket_lines == 0) {
    $create_info = array(
        "user_id" => $this->session->userdata("user_id"),
        "num_rows" => 15,
        "store_id" => $user_data[0]['user_store'],
        "date" => date("Y-m-d")
    );
    $this->db->insert("ip_ticket_num_rows", $create_info);
    echo "<script>location.reload();</script>";
}
?>
<form method="post" action="" id="search_form">
    <br><br><div id = "quantity_to_show" style="padding-top:10px; padding-left:10px;" >
        <table><tr><td><b>Quantity of registers to show: </b></td><td style="padding-left:10px;"><select class="form-control" style="width:100px;" onchange="get_quantity()" id = "quantity_registers">
                        <option value="15" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 15) {
                            echo "selected='selected'";
                        }
                        ?>>15</option>
                        <option value="25" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 25) {
                            echo "selected='selected'";
                        }
                        ?>>25</option>
                        <option  value="35" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 35) {
                            echo "selected='selected'";
                        }
                        ?>>35</option>
                        <option  value="45" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 45) {
                            echo "selected='selected'";
                        }
                        ?>>45</option>
                        <option  value="55" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 55) {
                            echo "selected='selected'";
                        }
                        ?>>55</option>
                        <option  value="100" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 100) {
                            echo "selected='selected'";
                        }
                        ?>>100</option>
                        <option value="200" <?php
                        if ($ip_ticket_num_rows[0]['num_rows'] == 200) {
                            echo "selected='selected'";
                        }
                        ?> >200</option>


                    </select></td>



                <td style="padding-left:10px;" id="date_from_label" hidden><b>Date From: </b></td>
                <td style="padding-left:10px;">
                    <div id="show_date_from" hidden>

                        <div class="input-group">
                            <input name="date" id="date_from"
                                   class="form-control input-sm datepicker"
                                   value="<?php
                                   if ($ip_ticket_num_rows[0]['date'] == "0000-00-00") {
                                       echo $date = date("m/d/Y");
                                   } else {
                                       echo $date = date("m/d/Y", strtotime($ip_ticket_num_rows[0]['date']));
                                   }
                                   ?>">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                        </div></div></td>
                <td style="padding-left:10px;" id="date_to_label" hidden><b>Date To: </b></td>

                <td style="padding-left:10px;">
                    <div id="show_date_to" hidden>
                        <div class="input-group">
                            <input name="date_to" id="date_to"
                                   class="form-control input-sm datepicker"
                                   value="">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                        </div></div></td>
                <td style="padding-left:10px;"><input type="text" placeholder="Ticket ID or Client Name" id = "filter_data" class="input-sm form-control"></td>
                <td style="padding-left:10px;"><button class="btn btn-sm btn-default" id ="button_filter"><b>Search</b></button></td>


            </tr>


        </table>

    </div>
</form>
<table><tr><td style="padding-left:10px;"><div id="checkbox_date">
                <input type="checkbox" id="able_date_range"></div></td>
        <td><div id="search_date"><b>Search by date</b></div></td></tr>

</table>
<?php
if ($check_url == false) {
    if (($check_url_2 == false) && ($check_url_3 == false)) {
        ?>
        <table align="center">
            <tr><td style="padding-right:10px;"><b>Total Registers</b> <font color="green"><?php echo $num_rows; ?></font></td><td></td><td style="padding-left:10px;"><b>Total Pages </b><font color="blue"><?php echo round($num_rows / $ip_ticket_num_rows[0]['num_rows']); ?></font></td></tr>
        </table>

        <table align="center">
            <?php
            $page = $this->uri->segment(4);
            $previous = $page - $ip_ticket_num_rows[0]['num_rows'];
            $next = $page + $ip_ticket_num_rows[0]['num_rows'];

            if ($previous < 0) {
                $previous = 0;
            }

            if ($ip_ticket_num_rows[0]['num_rows'] == $next) {

                $next = $next * 2;
            }
            if ($previous == $ip_ticket_num_rows[0]['num_rows']) {
                $previous = 0;
            }
            ?>
            <tr><td><div class="pull-right visible-lg">
                        <?php echo pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes', $previous, $next); ?>
                    </div></td></tr></table>
        <?php
    }
}
?>
<div class="table-responsive">
    <table class="table table-striped">

        <thead>
            <tr>
                <th><?php echo lang('quote'); ?></th>
                <th><?php echo lang('market'); ?></th>
                <th><?php echo lang('client_name'); ?></th>
                <?php if ($this->session->userdata("is_tech") == 0) { ?><th><?php echo lang('e_payment'); ?></th> <?php } else { ?><th><?php echo "Last technician"; ?></th><th>Problem Description</th><?php } ?>
                <?php if ($this->session->userdata("is_tech") == 0) { ?><th><?php echo lang('processor'); ?></th> <?php } else { ?><th></th><?php } ?>
                <th><?php echo lang('status'); ?></th>


                <th><?php echo lang('created'); ?></th>
                <th><?php echo lang('due_date'); ?></th>
                <?php if ($this->session->userdata("is_tech") == 1) { ?>
                    <th></th>
                    <th></th>
                <?php } ?>
<!-- <th style="text-align: right; padding-right: 25px;"><?php echo lang('amount'); ?></th> -->
                <?php if ($this->session->userdata("is_tech") == 0) { ?><th><?php echo lang('print'); ?></th><?php } ?>

                <?php if ($this->session->userdata("is_tech") == 0) { ?><th><?php
                        if (($check_url_2 == false) && ($check_url_3 == false)) {


                            if ($user_data[0]['show_signature'] == 1) {
                                echo lang('signature');
                            }
                        }
                        ?></th><?php
                }
                if (($this->uri->segment(3) == "uncomplete") || ($this->uri->segment(3) == "fraud")) {
                    ?>
                    <th></th>
                    <th></th>
                <?php } ?>
            </tr>
        </thead>

        <tbody>
            <?php
            $count = 0;
            if ($this->uri->segment(4) == 0) {
                $offset = 0;
            } else {
                $offset = $this->uri->segment(4) - $ip_ticket_num_rows[0]['num_rows'];
            }

            if ($ip_ticket_lines != 0) {

                $date = $ip_ticket_num_rows[0]['date'];

                if ($ip_ticket_num_rows[0]['date_to'] == "0000-00-00") {
                    $date_to = date("Y-m-d");
                } else {
                    $date_to = $ip_ticket_num_rows[0]['date_to'];
                }

                $this->db->select("orders.payproc, ip_stores.show_signature, ip_agreement_terms_x_client.signature, ip_stores.store_name, ip_quotes.fraud,ip_quotes.complete_log, ip_quotes.complete, quote_number,quote_id,store,ip_clients.client_id,client_name,css_label,quote_date_created,quote_date_expires,status.status, payment_status, ip_quotes.user_id, ip_quotes.problem_description_product");
                $this->db->join("ip_agreement_terms_x_client", "ip_agreement_terms_x_client.id_ticket = ip_quotes.quote_id", "left");
                $this->db->join("ip_stores", "ip_stores.id = ip_quotes.store");
                $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
                $this->db->join("status", "status.id = ip_quotes.quote_status_id");
                $this->db->join("orders", "orders.oid = ip_quotes.quote_number");
                $this->db->where("ip_quotes.active <>", 1);
                if ($this->uri->segment(3) == "website") {

                    $this->db->where("ip_quotes.complete", 1);

                    $this->db->where("ip_stores.store_name", "Website Repair");
                    if ($ip_ticket_num_rows[0]['ticket_id'] != 0) {

                        $this->db->where("ip_quotes.quote_number like ", $ip_ticket_num_rows[0]['ticket_id'] . '%');
                    } else {
                        $this->db->where("ip_quotes.quote_date_created >=", date("Y-m-d", strtotime($date)));
                        $this->db->where("ip_quotes.quote_date_created <=", date("Y-m-d", strtotime($date_to)));
                    }
                    $this->db->or_where("ip_stores.store_name", "Website Sale");
                }


                if ($check_url != false) {
                    $this->db->where("status.css_label", $this->uri->segment(3));
                    //$this->db->where("ip_quotes.payment_status", 0);
                    $this->db->or_where("ip_quotes.complete", 1);
                    $this->db->where("orders.oid_ref", 0);
                } else {
                    if ($this->uri->segment(3) == "uncomplete") {
                        $this->db->where("ip_quotes.complete <>", 1);
                        $this->db->where("ip_quotes.payment_status <>", 0);
                        $this->db->where("orders.oid_ref", 0);
                        $this->db->where("ip_quotes.fraud", 0);
                    } else if ($this->uri->segment(3) == "fraud") {
                        $this->db->where("ip_quotes.complete <>", 1);
                        $this->db->where("ip_quotes.payment_status <>", 0);
                        $this->db->where("orders.oid_ref", 0);
                        $this->db->where("ip_quotes.fraud", 1);
                    }
                    if ($this->uri->segment(3) == "all") {
                        $this->db->where("status.css_label", $this->uri->segment(3));
                        $this->db->or_where("ip_quotes.complete", 1);
                        $this->db->where("orders.oid_ref", 0);
                    } else if ($this->uri->segment(3) == "diagnosing") {
                        $this->db->where("ip_quotes.quote_status_id", 1);
                    } else if ($this->uri->segment(3) == "waiting_on_approval") {
                        $this->db->where("ip_quotes.quote_status_id", 2);
                    } else if ($this->uri->segment(3) == "ordered_parts") {
                        $this->db->where("ip_quotes.quote_status_id", 3);
                    } else if ($this->uri->segment(3) == "repair_completed") {
                        $this->db->where("ip_quotes.quote_status_id", 5);
                    } else if ($this->uri->segment(3) == "returned_to_shop") {
                        $this->db->where("ip_quotes.quote_status_id", 10);
                    } else if ($this->uri->segment(3) == "repairing") {
                        $this->db->where("ip_quotes.quote_status_id", 4);
                    } else if ($this->uri->segment(3) == "repair_denied") {
                        $this->db->where("ip_quotes.quote_status_id", 9);
                    } else if ($this->uri->segment(3) == "accepted_by_client") {
                        $this->db->where("ip_quotes.quote_status_id", 6);
                    } else if ($this->uri->segment(3) == "new_order") {
                        $this->db->where("ip_quotes.quote_status_id", 7);
                    } else if ($this->uri->segment(3) == "waiting_for_package") {
                        $this->db->where("ip_quotes.quote_status_id", 8);
                    }
                }

                if ($ip_ticket_num_rows[0]['client_name'] != "") {

                    $this->db->like("ip_clients.client_name", $ip_ticket_num_rows[0]['client_name'], "After");
                } else if ($ip_ticket_num_rows[0]['ticket_id'] != 0) {

                    $this->db->like("ip_quotes.quote_number", $ip_ticket_num_rows[0]['ticket_id']);
                } else {
                    $this->db->where("ip_quotes.quote_date_created >=", date("Y-m-d", strtotime($date)));
                    $this->db->where("ip_quotes.quote_date_created <=", date("Y-m-d", strtotime($date_to)));
                }
                if ($user_data[0]['user_store'] != 1) {
                    $this->db->where("ip_quotes.store", $user_data[0]['user_store']);
                }
                if ($check_url == true) {
                    $this->db->where("ip_clients.client_id", $client_id);
                }
                $this->db->order_by("ip_quotes.quote_number", "DESC");
                $this->db->limit($ip_ticket_num_rows[0]['num_rows']);
                if (($ip_ticket_num_rows[0]['client_name'] == "") && ($ip_ticket_num_rows[0]['ticket_id'] == 0)) {
                    $this->db->offset($offset);
                }


                $quote_info = $this->db->get("ip_quotes")->result_array();


                foreach ($quote_info as $quote) {


                    //CHECK IF ORDER HAS OID_REF FROM ORDERS
                    $this->db->select("oid_ref");
                    $this->db->where("oid", $quote['quote_number']);
                    $orders_data = $this->db->get("orders")->result_array();

                    //GET ORDER_USER INFO
                    $this->db->select("id_user");
                    $this->db->order_by("id", "DESC");
                    $this->db->limit("1");
                    $this->db->where("id_ticket", $quote['quote_id']);
                    $user_history = $this->db->get("ip_quotes_status_history")->result_array();

                    $this->db->select("*");
                    $this->db->where("user_id", $user_history[0]['id_user']);
                    $user_info = $this->db->get("ip_users")->result_array();


                    //GET PAYMENT STATUS
                    if ($quote['oid_ref'] == 0) {
                        $this->db->where("order_id", $quote['quote_number']);
                    } else {
                        $this->db->where("order_id", $quote['oid_ref']);
                    }
                    $payment_status_data = $this->db->get("ip_online_payment_log")->result_array();
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo site_url('quotes/view/' . $quote['quote_id']); ?>"
                               title="<?php echo lang('edit'); ?>">
                                   <?php echo($quote['quote_number'] ? $quote['quote_number'] : $quote['quote_id']); ?>
                            </a>
                        </td>
                        <td><?php
                                   echo $quote['store_name'];
                                   ?>
                        </td>
                        <td>
                            <a href="<?php echo site_url('clients/view/' . $quote['client_id']); ?>"
                               title="<?php echo lang('view_client'); ?>">
                                   <?php echo $quote['client_name']; ?>
                            </a>
                        </td>
                <input type='text' name='complete_log' value='<?php echo $quote['complete_log']; ?>'hidden>
                <?php if ($this->session->userdata("is_tech") == 0) { ?><td><?php
                    if ($quote['payment_status'] == 2) {
                        if ($quote['complete'] == 1) {
                            echo "<font color = '#0C0'>";
                            echo "Completed ";
                            if ($quote['complete_log'] != "") {
                                ?>
                                    <a href="#" onclick="send_message('<?php echo $quote['complete_log']; ?>')"><font color='#F90'>***</font></a>
                                    <?php
                                }
                            } else if ($quote['complete'] == 2) {
                                echo "<font color = '#F00'>Denied</font>";
                            } else if ($quote['complete'] == 3) {
                                echo "<font color = '#F00'>Expired</font>";
                            } else if ($quote['complete'] == 4) {
                                echo "<font color = '#F00'>Failed</font>";
                            } else if ($quote['complete'] == 5) {
                                echo "<font color = '#F90'>In-Progress</font>";
                            } else if ($quote['complete'] == 6) {
                                echo "<font color = '#F90'>Pending</font>";
                            } else if ($quote['complete'] == 7) {
                                echo "<font color = '#F90'>Processed</font>";
                            } else if ($quote['complete'] == 8) {
                                echo "<font color = '#F90'>Voided</font>";
                            } else if ($quote['complete'] == 9) {
                                echo "<font color = '#F90'>Partially_Refunded</font>";
                            } else if ($quote['complete'] == 10) {
                                echo "<font color = '#F90'>Canceled_Reversal</font>";
                            } else if ($quote['complete'] == 11) {
                                echo "<font color = '#F90'>Reversed</font>";
                            } else if ($quote['complete'] == 12) {
                                echo "<font color = '#F90'>Refunded</font>";
                            } else if ($quote['complete'] == 0) {
                                echo "Pending";
                            } else if ($quote['complete'] == -1) {
                                echo "<font color = '#F90'>Canceled</font>";
                            } else {
                                echo "<font color = '#F90'>Unknown</font>";
                            }
                        } else if ($quote['payment_status'] == 1) {
                            if ($quote['complete'] == 1) {
                                echo "<font color = '#0C0'>Completed</font>";
                                if ($quote['complete_log'] != "") {
                                    ?>
                                    <a href="#" onclick="send_message('<?php echo $quote['complete_log']; ?>')"><font color='#F90'>***</font></a>
                                    <?php
                                }
                            } else if ($quote['complete'] == 2) {
                                echo "<font color = '#F00'>Declined</font>";
                            } else if ($quote['complete'] == 3) {
                                echo "<font color = '#F00'>Error</font>";
                            } else if ($quote['complete'] == 4) {
                                echo "<font color = '#F00'>Held for Review</font>";
                            } else if ($quote['complete'] == 0) {
                                echo "Pending";
                            } else {
                                echo "<font color = '#F90'>Unknown</font>";
                            }
                        }
                        ?></font></td><?php } else { ?><td><?php
                            if ($user_info[0]->is_tech == 1) {
                                echo $user_info[0]->user_name;
                            }
                            ?></td><td><?php echo $quote['problem_description_product']; ?></td><?php } ?>
                        <?php if ($this->session->userdata("is_tech") == 0) { ?>
                        <?php if ($quote['payproc'] == 2) { ?>
                        <td><a href="javascript:call_popup('<?php echo site_url('quotes/show_log/' . $quote['quote_number']); ?>')" ><img src="../../../../assets/default/img/payprocpp.gif"></a></td>
                    <?php } else if ($quote['payproc'] == 1) { ?><td><a href="javascript:call_popup('<?php echo site_url('quotes/show_log/' . $quote['quote_number']); ?>')" ><img src="../../../../assets/default/img/payprocanet.gif"></a></td> <?php
                    } else {
                        ?> <td></td><?php
                            }
                        } else {
                            ?><td></td><?php } ?>


                <td>
                    <span
                        class="label <?php echo $quote['css_label']; ?>"><?php
                if ($quote['status'] == "New Order") {
                    if (($quote['store_name'] == "Hawthorne") || ($quote['store_name'] == "Venice") || ($quote['store_name'] == "Usc Repair")) {
                        //echo "Draft";
                        echo $quote['status'];
                    } else {
                        echo $quote['status'];
                    }
                } else if ($quote['status'] == "Repair Completed") {
                    if (($quote['store_name'] == "Hawthorne") || ($quote['store_name'] == "Venice") || ($quote['store_name'] == "Usc Repair")) {
                        //echo "Sent";
                        echo $quote['status'];
                    } else {
                        echo $quote['status'];
                    }
                } else if ($quote['status'] == "Accepted by client") {
                    if (($quote['store_name'] == "Hawthorne") || ($quote['store_name'] == "Venice") || ($quote['store_name'] == "Usc Repair")) {
                        //echo "Paid";
                        echo $quote['status'];
                    } else {
                        echo $quote['status'];
                    }
                } else {
                    echo $quote['status'];
                }
                        ?></span>
                </td>



                <td>
                    <?php echo date_from_mysql($quote['quote_date_created']); ?>
                </td>
                <td>
                    <?php echo date_from_mysql($quote['quote_date_expires']); ?>
                </td>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <!-- <td style="text-align: right; padding-right: 25px;">
                <?php echo format_currency($quote['quote_total']);
                ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </td> -->
                <td>
                    <?php if ($this->session->userdata("is_tech") == 0) { ?><a href = "#" >Label</a> |  <a href = "<?php echo site_url('quotes/generate_receipt/' . $quote['quote_id']); ?>" target="_blank">Receipt</a><?php } ?>
                    <!--<div class="options btn-group">
                        <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                           href="#">
                            <i class="fa fa-cog"></i> <?php echo lang('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('quotes/view/' . $quote['quote_id']); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php echo lang('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('quotes/generate_pdf/' . $quote['quote_id']); ?>"
                                   target="_blank">
                                    <i class="fa fa-print fa-margin"></i> <?php echo lang('download_pdf'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                                    <i class="fa fa-send fa-margin"></i> <?php echo lang('send_email'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('quotes/delete/' . $quote->quote_id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_quote_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div> -->
                </td>

                <?php if (($check_url_2 == true) || ($check_url_3 == true)) { ?>
                    <td><a href = "#" onclick="complete_order(<?php echo $quote['quote_number']; ?>,<?php echo $quote['quote_id']; ?>)"><font color = 'green'>Complete Now</font></a> |  <a href = "#" onclick="mark_fraud(<?php echo $quote['quote_number']; ?>)"><?php if ($quote['fraud'] == 1) { ?></a><font color='red'><b>FRAUD</b></font> <?php } else { ?><font color = 'gray'>Mark Fraud</font> <?php } ?></a></td>
                    <td>
                        <a href="<?php echo site_url('quotes/delete/' . $quote['quote_id'] . '/' . $this->uri->segment(3)); ?>"
                           onclick="return confirm('<?php echo lang('delete_quote_warning'); ?>');">
                            <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                        </a>
                    <?php } ?>
                <td>
                    <?php
                    //CHECK SIGNATURE
                    if ($this->session->userdata("is_tech") == 0) {
                        if ($quote['show_signature'] == 1) {

                            if ($quote['signature'] == null) {
                                echo "<p style='color:red;'><b>Unsigned</b></p>";
                            } else {
                                echo "<p style='color:green;'><b>Signed</b></p>";
                            }
                        }
                    }
                    ?>
                </td>
                </tr>

                <?php
                $count++;
                if ($ip_ticket_lines != 0) {
                    if ($count == $ip_ticket_num_rows[0]['num_rows']) {

                        break;
                    }
                }
            }
        }

//CLEAR THE TABLE
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $this->db->set("date", "0000-00-00");
        $this->db->set("date_to", "0000-00-00");
        $this->db->set("ticket_id", 0);
        $this->db->set("client_name", "");
        $this->db->update("ip_ticket_num_rows");
        ?>
        </tbody>

    </table>
</div>

<script>



    function call_popup(URL) {

        var width = 760;
        var height = 760;

        var left = 99;
        var top = 99;

        window.open(URL, 'Payment Processor Log', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');

    }



    $(document).ready(function () {


        //hang on event of form with id=myform
        $("#search_form").submit(function (e) {
            //prevent Default functionality
            e.preventDefault();

            $.post("<?php echo site_url('quotes/ajax/update_date'); ?>", {
                update_date: $("#date_from").val(),
                date_to: $("#date_to").val(),
                filter_data: $("#filter_data").val()
            },
                    function (data) {
                        //alert(data);
                        location.reload();
                    });





        });




        $('#able_date_range').click(function () {
            if ($(this).is(":checked"))
            {
                $("#date_from_label").show();
                $("#date_to_label").show();
                $("#show_date_from").show();
                $("#show_date_to").show();
            } else {

                $("#date_from_label").hide();
                $("#date_to_label").hide();
                $("#show_date_from").hide();
                $("#show_date_to").hide();
                date_from = "";
                date_to = "";
            }
        });




        $('#filter_data').autocomplete({
            source: '<?php echo site_url('quotes/ajax/customer_ticket_search/') ?>',
            search: function (event, ui) {

                // update source url by adding new GET params
                $(this).autocomplete('option', 'source', '<?php echo site_url('quotes/ajax/customer_ticket_search/') ?>' + '?client_id=' +<?php echo $client_id; ?>);
            }


        });





        // Opera 8.0+
        var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
        // Firefox 1.0+
        var isFirefox = typeof InstallTrigger !== 'undefined';
        // At least Safari 3+: "[object HTMLElementConstructor]"
        var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
        // Internet Explorer 6-11
        var isIE = /*@cc_on!@*/false || !!document.documentMode;
        // Edge 20+
        var isEdge = !isIE && !!window.StyleMedia;
        // Chrome 1+
        var isChrome = !!window.chrome && !!window.chrome.webstore;
        // Blink engine detection
        var isBlink = (isChrome || isOpera) && !!window.CSS;
        var output = 'Detecting browsers by ducktyping:<hr>';
        if (isFirefox == true)
        {
            $("#button_filter").show();
        } else
        {
            // $("#button_filter").hide();
        }
    });
    /*
     $("#date").click(function () {
     $("#date").change(function () {
     
     $.post("<?php echo site_url('quotes/ajax/update_date'); ?>", {
     update_date: $("#date").val()
     },
     function (data) {
     
     location.reload();
     
     });
     });
     
     
     });*/


    function get_quantity()
    {

        var quantity = $("#quantity_registers").val();
        $.post("<?php echo site_url('quotes/ajax/change_limit'); ?>", {
            setting_limit: quantity,
            date: $("#date_from").val(),
            date_to: $("#date_to").val()
        },
                function (data) {

                    location.reload();
                });
    }

    function send_message(message)
    {
        alert(message);
    }
    function complete_order(quote_number, quote_id)
    {
        var confirm_message = confirm("Do you really want to confirm this order?");
        if (confirm_message) {
            $.post("<?php echo site_url('quotes/quotes/complete_order'); ?>", {
                order_number: quote_number,
                order_id: quote_id

            },
                    function (data) {
                        alert(data);
                        location.reload();
                    });
        } else {

        }

    }
    function mark_fraud(quote_number)
    {
        var confirm_message = confirm("Do you really want to mark as fraud this order?");
        if (confirm_message) {
            $.post("<?php echo site_url('quotes/quotes/mark_fraud'); ?>", {
                order_number: quote_number

            },
                    function (data) {
                        alert(data);
                        location.reload();
                    });
        } else {

        }
    }

</script>