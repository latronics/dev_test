<?php
//SELECT TO GET SIGNATURE FROM THE CLIENT
$this->db->select("*");
$this->db->from("ip_agreement_terms_x_client");
$this->db->where("id_client", $quote->client_id);
$this->db->where("id_ticket", $quote->quote_id);
$get_signature = $this->db->get();
$signature = $get_signature->result_array();

//CLIENT SIGNATURE READY
$signature_ready = str_replace("[removed]", "", $signature[0]['signature']);

//GET INVOICE STORE
$this->db->where("id", $quote->store);
$store = $this->db->get("ip_stores")->result_object();
$store = $store[0]->store_name;

//SELECT TO GET STATUS CHANGE HISTORY AND MESSAGES
$this->db->select("*");
$this->db->from("ip_quotes_status_history");
$this->db->join("status", "status.id = ip_quotes_status_history.id_status", "left");
$this->db->join("ip_users", "ip_users.user_id = ip_quotes_status_history.id_user", "left");
$this->db->where("id_ticket", $quote->quote_id);
$this->db->or_where("id_ticket", $quote->quote_number);
$this->db->order_by("ip_quotes_status_history.id", "DESC");
$get_ticket_history = $this->db->get();
$ticket_history = $get_ticket_history->result_array();



//SELECT TO GET INVOICES HISTORY DATA
$this->db->select("*");
$this->db->join("ip_invoices_status_history", "ip_invoices_status_history.id_invoice = ip_invoices.invoice_id");
$this->db->join("ip_users", "ip_users.user_id = ip_invoices.user_id");
$this->db->where("ticket_id", $quote->quote_number);
$this->db->order_by("ip_invoices_status_history.date_changed", "DESC");
$invoice_data = $this->db->get("ip_invoices")->result_array();

//SELECT TO GET PAYMENT HISTORY DATA
$this->db->select("*");
$this->db->join("ip_payment_methods", "ip_payment_methods.payment_method_id = ip_payments.payment_method_id");
$this->db->join("ip_invoices", "ip_invoices.invoice_id = ip_payments.invoice_id");
$this->db->where("ip_invoices.ticket_id", $quote->quote_number);
$this->db->order_by("ip_payments.payment_id", "desc");
$ip_payments = $this->db->get("ip_payments")->result_array();

//CHECK IF IS TO SHOW SIGNATURE BUTTON AND MESSAGE
$this->db->select("*");
$this->db->join("ip_stores", "ip_stores.id = ip_quotes.store");
$this->db->where("quote_id", $quote->quote_id);
$store_info = $this->db->get("ip_quotes")->result_array();



echo $modal_delete_quote;
echo $modal_add_quote_tax;
?>

<div id="headerbar">

    <h1>
        <?php
        echo lang('quote') . ' ';
        echo($quote->quote_number ? '#' . $quote->quote_number : $quote->quote_id);
        if ($this->session->userdata("is_tech") == 0) {
            if ($store_info[0]['show_signature'] == 1) {
                if ($signature_ready == "") {

                    echo "<label id='signature_status'> - <font color = 'red' class = 'alert-agreement-fail'>";
                    echo lang('necessery_terms');
                    echo "</font></label>";
                }
            }
        }
        ?>
    </h1>

    <div class="pull-right btn-group">
        <?php if ($this->session->userdata("is_tech") == 0) { ?>
            <div class="options btn-group pull-left">
                <a class="btn btn-sm btn-default" href="<?php echo site_url('clients/clients/print_label/' . $quote->quote_id); ?>" target="_blank">
                    Print Label
                </a>
                <a class="btn btn-sm btn-default" href="<?php echo site_url('quotes/generate_receipt/' . $quote->quote_id); ?>" target="_blank" 
                   >
                    <i class="fa fa-print fa-margin"></i>
                    <?php echo lang('print_receipt'); ?>
                </a>
                <?php
                if ($store_info[0]['show_signature'] == 1) {
                    if ($signature_ready == "") {
                        ?>

                        <a class="btn btn-sm btn-default" href=" <?php echo site_url('clients/clients/sendagreements/' . $quote->quote_id . '/' . $quote->client_id); ?>" target="_self" onclick="set_agreements()">
                            <?php
                            echo lang('agreement_terms');
                        }
                    }
                    ?>  
                </a>
                <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php echo lang('options'); ?> <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#add-quote-tax" data-toggle="modal">
                            <i class="fa fa-plus fa-margin"></i>
                            <?php echo lang('add_quote_tax'); ?>
                        </a>
                    </li>
                    <!-- <li>
                         <a href="<?php echo site_url('quotes/generate_receipt/' . $quote->quote_id); ?>" target="_blank" 
                            >
                             <i class="fa fa-print fa-margin"></i>
                    <?php echo lang('print_receipt'); ?>
                         </a>
                     </li>-->
                    <li>
                        <a href="<?php echo site_url('mailer/quote/' . $quote->quote_id); ?>">
                            <i class="fa fa-send fa-margin"></i>
                            <?php echo lang('send_email'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="btn_quote_to_invoice"
                           data-quote-id="<?php echo $quote_id; ?>">
                            <i class="fa fa-refresh fa-margin"></i>
                            <?php echo lang('quote_to_invoice'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="btn_copy_quote"
                           data-quote-id="<?php echo $quote_id; ?>">
                            <i class="fa fa-copy fa-margin"></i>
                            <?php echo lang('copy_quote'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#delete-quote" data-toggle="modal">
                            <i class="fa fa-trash-o fa-margin"></i> <?php echo lang('delete'); ?>
                        </a>
                    </li>
                </ul>
            </div>


            <a href="#" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i>
                <?php echo lang('add_new_row'); ?>
            </a>
            <a href="#" class="btn_add_product btn btn-sm btn-default">
                <i class="fa fa-database"></i>
                <?php echo lang('add_product'); ?>
            </a>
        <?php } ?>
        <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_quote">
            <i class="fa fa-check"></i>
            <?php echo lang('save'); ?>
        </a>
    </div>

</div>

<div id="content">
    <div id ="amount_zero" class ="alert alert-danger" hidden>The amount is required !</div>
    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="quote_form">

        <div class="quote">

            <div class="cf row">
                <table style="float:left">
                    <tr><td>
                            <div id="panel-quick-actions" class="panel panel-default" style="width:290px; margin-top:-8px; float:left;">

                                <div class="panel-heading" align="left">
                                    <label for="ticket_content" ><?php echo "<b>Customer Details</b>"; ?></label>
                                </div>
                                <br>


                                <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                    <div class="pull-left"  style="margin-left:10px; margin-right:10px;">
                                        <h2>
                                            <a href="<?php echo site_url('clients/view/' . $quote->client_id); ?>"><?php echo $quote->client_name; ?></a>
                                            <?php
                                            if ($quote->client_company != "") {
                                                echo "(Dealer)";
                                            }
                                            ?>
                                            <?php if ($quote->quote_status_id == 1) { ?>
                                                <span id="quote_change_client" class="fa fa-edit cursor-pointer small"
                                                      data-toggle="tooltip" data-placement="bottom"
                                                      title="<?php echo lang('change_client'); ?>"></span>
                                                  <?php } ?>
                                        </h2><br>
                                        <span>
                                            <label style="font-weight:bold;">Billing Address</label><br>
                                            <?php echo ($quote->client_address_1) ? $quote->client_address_1 . '<br>' : ''; ?>
                                            <?php echo ($quote->client_address_2) ? $quote->client_address_2 . '<br>' : ''; ?>
                                            <?php echo ($quote->client_city) ? $quote->client_city : ''; ?>
                                            <?php echo ($quote->client_state) ? $quote->client_state : ''; ?>
                                            <?php echo ($quote->client_zip) ? $quote->client_zip : ''; ?>
                                            <?php echo ($quote->client_country) ? '<br>' . $quote->client_country : ''; ?>
                                        </span><br>

                                        <?php if (($store == 'Website Sale') || ($store == 'Website Repair') || ($store == 'General')) { ?>
                                            <span>
                                                <label style="font-weight:bold;">Shipping Address</label><br>
                                                <?php echo ($quote->shipping_address) ? $quote->shipping_address . '<br>' : ''; ?>
                                                <?php echo ($quote->shipping_city) ? $quote->shipping_city : ''; ?>
                                                <?php echo ($quote->shipping_state) ? $quote->shipping_state : ''; ?>
                                                <?php echo ($quote->shipping_zip) ? $quote->shipping_zip : ''; ?>
                                                <?php echo ($quote->shipping_country) ? '<br>' . $quote->shipping_country : ''; ?>
                                            </span>
                                            <br><br>
                                        <?php } ?>
                                        <br><br>
                                        <?php if ($quote->client_phone) { ?>
                                            <span><strong><?php echo lang('phone'); ?>
                                                    :</strong> <?php echo $quote->client_phone; ?></span><br>
                                            <?php
                                        }
                                        if ($quote->client_mobile) {
                                            ?>
                                            <span><strong><?php echo "Mobile"; ?>
                                                    :</strong> <?php echo $quote->client_mobile; ?></span><br>
                                        <?php }
                                        ?>
                                        <?php if ($quote->client_email) { ?>
                                            <span><strong><?php echo lang('email'); ?>
                                                    :</strong> <?php echo $quote->client_email; ?></span>
                                        <?php }
                                        ?>
                                        <br><br>
                                    </div>
                                <?php } else { ?>
                                    <a href="#"><?php echo $quote->client_name; ?></a>

                                <?php } ?>


                            </div></td></tr>
                    <tr><td>
                            <div id="panel-quick-actions" class="panel panel-default" style="width:290px; margin-top:-15px; height: auto; float:left;">
                                <div class="panel-heading" align="left">
                                    <label for="ticket_content" ><?php echo "<b>Shipping/Products Details</b>"; ?></label>
                                </div>

                                <table border='0' style='width:100%;'>
                                    <?php if (($store == "Website Sale") || ($store == "Website Repair")) { ?>
                                        <tr><td colspan='2' align='center' style='border-bottom:1px #ddd solid;'>
                                                <label style="font-weight:bold; margin-left:10px;">Shipping Info</label>
                                            </td></tr>
                                        <?php
                                        $icount = 0;

                                        foreach ($items as $shipping) {
                                            if ((strpos($shipping->item_name, 'Return to client:') !== false) || (strpos($shipping->item_name, 'Shipping Box:') !== false) || (strpos($shipping->item_name, 'Shipping Label:') !== false)) {
                                                $return_client = explode($shipping->item_name, "Return to client:");
                                                $shipping_box = explode($shipping->item_name, "Shipping Box:");
                                                $shipping_label = explode($shipping->item_name, "Shipping Label:");
                                                $return_client_name = substr($shipping->item_name, strpos($shipping->item_name, ":") + 1);
                                                $box_client_name = substr($shipping->item_name, strpos($shipping->item_name, ":") + 1);
                                                $label_client_name = substr($shipping->item_name, strpos($shipping->item_name, ":") + 1);
                                                ?>
                                                <tr><td style='padding-left:5px; border-bottom:1px #ddd solid;'>
                                                        <?php
                                                        if ($icount == 0) {
                                                            print_r("<b><u>" . $return_client[0] . "</b>" . " - " . $return_client_name . "</td><td style='border-bottom:1px #ddd solid; padding-left:5px; padding-right:2px;'>$$shipping->item_price</u><br>");
                                                            $icount++;
                                                        } else if ($icount == 1) {
                                                            print_r("<b><u>" . $shipping_box[0] . "</b>" . " - " . $box_client_name . "</td><td style='border-bottom:1px #ddd solid; padding-left:5px;'>$$shipping->item_price<br></u>");
                                                            $icount++;
                                                        } else if ($icount == 2) {
                                                            print_r("<b><u>" . $shipping_label[0] . "</b>" . " - " . $label_client_name . "</td><td style='border-bottom:1px #ddd solid; padding-left:5px;'>$$shipping->item_price<br></u>");
                                                            $icount++;
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                </td></tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <tr><td style='padding-top:5px; border-bottom:1px #ddd solid;' colspan='2' align='center'>
                                            <label style="font-weight:bold; margin-left:10px;">Products Info</label>
                                        </td></tr>
                                    <?php
                                    foreach ($items as $products) {
                                        ?>

                                        <?php
                                        if ((strpos($products->item_name, 'Return to client:') !== false) || (strpos($products->item_name, 'Shipping Box:') !== false) || (strpos($products->item_name, 'Shipping Label:') !== false)) {
                                            
                                        } else {
                                            print_r("<tr><td style='padding-left:5px; border-bottom:1px #ddd solid;'><u>" . $products->item_name . "</u></td><td style='border-bottom:1px #ddd solid; '>$" . $products->item_price . "<br>");
                                        }
                                        ?></td></tr>
                                        <?php
                                    }
                                    ?>
                                    <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                        <tr><td><label style='margin-left:10px; font-size:18px; font-weight: bold;'>Order total:

                                                </label></td><td><?php echo "$" . $quote->amount; ?></td></tr>
                                    <?php } ?>
                                </table>

                            </div>
                        </td></tr>
                </table>


                <table border='0' style='float:left;'>
                    <tr>
                        <td>
                            <div id="panel-quick-actions" class="panel panel-default" style="width:290px; margin-top:-8px; float:left; margin-left:5px;" >
                                <div class="panel-heading" align="left">
                                    <label for="ticket_content" ><?php echo "<b>Order Details</b>"; ?></label>
                                </div>
                                <br>
                                <table border="0" style="margin-left:10px; margin-right:10px;" >
                                    <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                        <tr>
                                            <td colspan="2"> 
                                                <div class="form-group">
                                                    <label for="amount"><?php echo lang('amount'); ?></label>
                                                    <input type="text" name="amount" id="amount" class="form-control"
                                                           value="<?php echo $quote->amount; ?>" style="margin: 0 auto;" autocomplete="off">
                                                </div></td></tr>
                                        <tr>
                                        <?php } ?>
                                        <td>
                                            <div class="form-group">
                                                <label for="brand" ><?php echo lang('brand'); ?></label>
                                                <input type="text" name="brand" id="brand" class="form-control"
                                                       value="<?php echo $quote->brand; ?>" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>>
                                            </div>
                                        </td>
                                        <td style="padding-left:5px;">  
                                            <div class="form-group">          
                                                <label for="model"><?php echo lang('model'); ?></label>
                                                <input type="text" name="model" id="model" class="form-control"
                                                       value="<?php echo $quote->model; ?>" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>>
                                            </div>
                                        </td></tr><tr>
                                        <td>
                                            <div class="form-group">
                                                <label for="serial_number"><?php echo lang('serial_number'); ?></label>
                                                <input type="text" name="serial_number" id="serial_number" class="form-control"
                                                       value="<?php echo $quote->serial_number; ?>" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>>

                                            </div>
                                        </td>
                                        <td style="padding-left:5px;">
                                            <div class="form-group">

                                                <label for="data_recovery"><?php echo lang('data_recovery'); ?></label>
                                                <select name="data_recovery" id="data_recovery" class="form-control"
                                                        value="" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>>
                                                    <option id = "yes" name = "yes" <?php
                                                            if ($quote->data_recovery == "Yes") {
                                                                echo "selected";
                                                            }
                                                            ?>>Yes</option><option id = "no" name = "no" <?php
                                                                                                            if ($quote->data_recovery == "No") {
                                                                                                                echo "selected";
                                                                                                            }
                                                                                                            ?>>No</option></select>
                                            </div>
                                        </td></tr><tr><td colspan="2">
                                            <div class="form-group">

                                                <label for="client_os_password"><?php echo lang('client_os_password'); ?></label>
                                                <input type="password" name="client_os_password" id="client_os_password" class="form-control"
                                                       value="<?php echo $quote->client_os_password; ?>" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>>
                                            </div>
                                        </td></tr>
                                    <tr><td colspan="2">
                                            <div class="form-group">


                                                <label for="accessories_included"><?php echo lang('accessories_included'); ?></label>
                                                <textarea name="accessories_included" id="accessories_included" class="form-control"
                                                          value="" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>><?php echo $quote->accessories_included; ?></textarea>
                                            </div>
                                        </td></tr>
                                    <tr><td colspan="2">
                                            <div class="form-group">

                                                <label for="problem_description_product"><?php echo lang('problem_description_product'); ?></label>
                                                <textarea name="problem_description_product" id="problem_description_product" class="form-control"
                                                          value="" style="margin: 0 auto;" autocomplete="off" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>><?php echo $quote->problem_description_product; ?></textarea>
                                            </div>
                                        </td></tr>
                                    <tr><td colspan="2">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo lang('notes'); ?></label>
                                                <textarea name="notes" id="notes" rows="3"
                                                          class="input-sm form-control" <?php if ($this->session->userdata("is_tech") == 1) { echo "disabled"; } ?>><?php echo $quote->notes; ?></textarea>
                                            </div>
                                        </td></tr>
                                </table>

                            </div>
                        </td></tr>
                    <tr><td><?php if ($this->session->userdata("is_tech") == 0) { ?>
                                <div id="panel-quick-actions" class="panel panel-default" style="width:290px; margin-left:5px; margin-top:-15px; overflow: auto;">
                                    <div class="panel-heading" align="left">
                                        <label for="ticket_content" ><?php echo "<b>Payment History</b>"; ?></label>
                                    </div>
                                    <div style="max-height: 120px; overflow: auto;">
                                        <table class = "table table-striped">
                                            <tr >
                                                <td><label class="control-label"><?php echo "Payment method"; ?></label></td>
                                                <td><label class="control-label"><?php echo "Payment date"; ?></label></td>
                                                <td style="width:130px;"><label class="control-label"><?php echo "Cashiered amount"; ?></label></td>

                                            </tr>


                                            <?php
                                            foreach ($ip_payments as $history_payment) {
                                                ?>
                                                <tr><td>
                                                        <?php
                                                        echo $history_payment['payment_method_name'];
                                                        ?></td>
                                                    <td style="border-left:1px #ddd solid;"><?php echo date("m-d-Y", strtotime($history_payment['payment_date'])) . " " . $history_payment['payment_time']; ?></td>

                                                    <td style="border-left:1px #ddd solid;"><?php echo $quote->quote_total; ?></td>


                                                </tr>

                                                <?php
                                            }
                                            ?>

                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </td></tr>
                </table>
                <div id="history_all">
                    <table id="table_general" border="0">
                        <tr><td>
                                <div id="panel-quick-actions" class="panel panel-default" style="width:800px; min-height: 520px;  float:left; margin-top:-8px;  margin-left:5px;">
                                    <div class="panel-heading" align="left">
                                        <label for="ticket_content" ><?php echo "<b>Order History</b>"; ?></label>
                                    </div>
                                    <div style="max-height: 400px; overflow: auto;">
                                        <table class = "table table-striped" >
                                            <tr><td><label class="control-label"><?php echo lang('status'); ?></label></td>
                                                <td style="width:130px;"><label class="control-label"><?php echo lang('date_changed'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('staff_comments'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('notes_to_customer'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('client_notified'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('user'); ?></label></td>
                                            </tr>


                                            <?php
                                            $count = 0;

                                            foreach ($ticket_history as $history) {
                                                $this->db->select("store_name");
                                                $this->db->where("id", $quote->store);
                                                $store_data = $this->db->get("ip_stores")->result_array();
                                                $store_name = $store_data[0]['store_name'];
                                                ?>
                                                <tr><td class = "<?php echo $ticket_history[$count]['css_label']; ?>">

                                                        <?php
                                                        if ($ticket_history[$count]['status'] == "New Order") {

                                                            /*  echo "Draft";
                                                              } else { */
                                                            echo $ticket_history[$count]['status'];
                                                        } else if ($ticket_history[$count]['status'] == "Accepted by client") {
                                                            if (($store_name == "Hawthorne") || ($store_name == "Venice") || ($store_name == "Usc Repair")) {
                                                                //echo "Paid";
                                                                echo $ticket_history[$count]['status'];
                                                            } else {
                                                                echo $ticket_history[$count]['status'];
                                                            }
                                                        } else if ($ticket_history[$count]['status'] == "Repair Completed") {
                                                            if (($store_name == "Hawthorne") || ($store_name == "Venice") || ($store_name == "Usc Repair")) {
                                                                //echo "Sent";
                                                                echo $ticket_history[$count]['status'];
                                                            } else {
                                                                echo $ticket_history[$count]['status'];
                                                            }
                                                        } else {
                                                            echo $ticket_history[$count]['status'];
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="border-left:1px #ddd solid;"><?php echo date("m-d-Y h:i:s", strtotime($ticket_history[$count]['date_changed'])); ?></td>
                                                    <td style="border-left:1px #ddd solid;"><?php echo $ticket_history[$count]['staff_comments']; ?></td>
                                                    <td style="border-left:1px #ddd solid;"><?php echo $ticket_history[$count]['notes_to_customer']; ?></td>
                                                    <td style="border-left:1px #ddd solid;"><?php echo $ticket_history[$count]['client_notified'];
                                                        ?></td>
                                                    <td style="border-left:1px #ddd solid;"><?php echo $ticket_history[$count]['user_name']; ?></td>

                                                </tr>

                                                <?php
                                                $count++;
                                            }
                                            ?>

                                        </table></div>

                            </td>
                            <td rowspan="2">
                                <div id="panel-quick-actions" class="panel panel-default" style="margin-left:-38px; margin-top:-8px; height: 690px; width:290px; ">
                                    <div class="panel-heading" align="left">
                                        <label for="ticket_content" ><?php echo "<b>Order Actions</b>"; ?></label>
                                    </div>


                                    <div class="" style="border:0px; height: 565px; overflow: auto;">
                                        <table border="0"><tr><td>
                                                    <button class="btn btn-sm btn-default" type="button" onclick="ShowHide('ticket_info')" style="margin-left:5px; margin-top:5px; margin-bottom:5px;">Order Info</button>
                                                    <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                                    </td><td><button class="btn btn-sm btn-default" type="button" onclick="ShowHide('signature')" style="margin-left:-190px; margin-top:5px; margin-bottom:5px;">Signature Info</button>
                                                    <?php } ?></td></tr>
                                            <tr><td colspan="2">
                                                    <div class="row">

                                                        <div class="">

                                                            <div class="" id = "ticket_info" hidden style="width:200px; margin-left:20px;">
                                                                <label>Order Type</label>
                                                                <div class="controls">
                                                                    <input type="text" id="order_type" class="form-control input-sm"
                                                                    <?php if ($quote->order_type) : ?>
                                                                               value="<?php
                                                                               $this->db->select("otype_title");
                                                                               $this->db->from("ip_order_types");
                                                                               $this->db->where("id_otype", $quote->order_type);
                                                                               $get_order_type = $this->db->get();
                                                                               $result_order_type = $get_order_type->result_array();
                                                                               echo $result_order_type[0]['otype_title'];
                                                                               ?>"
                                                                           <?php else : ?>
                                                                               placeholder="<?php echo lang('not_set'); ?>"
                                                                           <?php endif; ?> disabled>
                                                                </div>
                                                                <label>Store</label>
                                                                <div class="controls">
                                                                    <select name="store" id="store" class="store form-control input-sm <?php
                                                                    if ($this->session->userdata("is_tech") == 1) {
                                                                        echo "disabled";
                                                                    }
                                                                    ?>" value="" style="">
                                                                        <option value = ""></option>
                                                                        <?php
                                                                        $this->db->select("*");


                                                                        $data_stores = $this->db->get("ip_stores")->result_array();

                                                                        foreach ($data_stores as $data_stores) {
                                                                            ?>

                                                                            <option id = "<?php echo $data_stores['id'] ?>" name = "store[]" value = "<?php echo $data_stores['id'] ?>" <?php
                                                                            if ($quote->store == $data_stores['id']) {
                                                                                echo "selected";
                                                                            }
                                                                            ?>><?php echo $data_stores['store_name']; ?></option>
                                                                                <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <label for="quote_number">
                                                                    <?php echo lang('quote'); ?> #
                                                                </label>

                                                                <div class="controls">
                                                                    <input type="text" id="quote_number" class="form-control input-sm"
                                                                    <?php if ($quote->quote_number) : ?>
                                                                               value="<?php echo $quote->quote_number; ?>"
                                                                           <?php else : ?>
                                                                               placeholder="<?php echo lang('not_set'); ?>"
                                                                           <?php endif; ?> disabled>
                                                                </div>


                                                                <div class="quote-properties has-feedback">
                                                                    <label for="quote_date_created">
                                                                        <?php echo lang('date'); ?>
                                                                    </label>

                                                                    <div class="input-group">
                                                                        <input name="quote_date_created" id="quote_date_created"
                                                                               class="form-control input-sm datepicker"
                                                                               value="<?php echo date_from_mysql($quote->quote_date_created); ?>" <?php
                                                                               if ($this->session->userdata("is_tech") == 1) {
                                                                                   echo "disabled";
                                                                               }
                                                                               ?>>
                                                                        <span class="input-group-addon">
                                                                            <i class="fa fa-calendar fa-fw"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="quote-properties has-feedback">
                                                                    <label for="quote_date_expires">
                                                                        <?php echo lang('expires'); ?>
                                                                    </label>

                                                                    <div class="input-group">
                                                                        <input name="quote_date_expires" id="quote_date_expires"
                                                                               class="form-control input-sm datepicker"
                                                                               value="<?php echo date_from_mysql($quote->quote_date_expires); ?>" <?php
                                                                               if ($this->session->userdata("is_tech") == 1) {
                                                                                   echo "disabled";
                                                                               }
                                                                               ?>>
                                                                        <span class="input-group-addon">
                                                                            <i class="fa fa-calendar fa-fw"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <table class ="table table-bordered" id = "signature" hidden style="border-top:0px; border-right: 0px; border-bottom:0px; border-left:0px; width:200px; margin-left:20px; margin-top:5px;">
                                                                    <tr><td style="padding-left:16px;">Client Signature<br></td></tr>
                                                                    <tr><td><?php if ($signature_ready != "") { ?><img src = "data:image/png;base64,<?php echo $signature_ready; ?>" width=170 height=70 id="signature_ready"><?php
                                                                            } else {
                                                                                echo "<font color = 'red'>No signature</font>";
                                                                            }
                                                                            ?> </td> </tr></table></div>
                                                        </div>

                                                        <div style="margin-left:20px;  width: 280px;" >

                                                            <div class="quote-properties" >

                                                                <label for="quote_status_id">
                                                                    <?php echo lang('status'); ?>
                                                                </label>

                                                                <select name="quote_status_id" id="quote_status_id"
                                                                        class="form-control input-sm">
                                                                            <?php
                                                                            if ($this->session->userdata("is_tech") == 0) {
                                                                                foreach ($quote_statuses as $key => $status) {
                                                                                    if (($store_info[0]['store_name'] == "Website Repair") || ($store_info[0]['store_name'] == "Website Sale")) {

                                                                                        if (($status['label'] == 'Received') || ($status['label'] == 'Performing diagnostic') || ($status['label'] == 'Waiting on approval') || ($status['label'] == 'Ordered parts') || ($status['label'] == 'Repair completed') || ($status['label'] == 'Order shipped') || ($status['label'] == 'Send Shipping item') || ($status['label'] == 'Repairing') || ($status['label'] == 'Repair denied') || ($status['label'] == 'Waiting for package')) {
                                                                                            ?>

                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php echo $status['label']; ?>
                                                                                    </option>
                                                                                    <?php
                                                                                }
                                                                            } else {
                                                                                if (($status['label'] == 'Diagnosing') || ($status['label'] == 'Waiting on approval') || ($status['label'] == 'Ordered parts') || ($status['label'] == 'Repair completed') || ($status['label'] == 'Accepted by client') || ($status['label'] == 'Returned to shop') || ($status['label'] == 'Repairing') || ($status['label'] == 'Repair denied') || ($status['label'] == 'Payment')) {
                                                                                    ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php echo $status['label']; ?>
                                                                                    </option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        }
                                                                    } else {
                                                                        foreach ($quote_statuses as $key => $status2) {
                                                                            if (($store_info[0]['store_name'] == "Website Repair") || ($store_info[0]['store_name'] == "Website Sale")) {
                                                                                if ($status2['label'] == "Repairing") {
                                                                                    ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php
                                                                                                if ($status2['label'] == "Repairing") {
                                                                                                    echo $status2['label'];
                                                                                                }
                                                                                                ?>
                                                                                    </option>

                                                                                    <?php
                                                                                } else if ($status2['label'] == "Repair Completed") {
                                                                                    ?>                                                      <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php
                                                                                                if ($status2['label'] == "Repair Completed") {
                                                                                                    echo $status2['label'];
                                                                                                }
                                                                                                ?>
                                                                                    </option>
                                                                                    <?php
                                                                                    echo $status2['label'];
                                                                                } else if ($status2['label'] == "Update") {
                                                                                    ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php
                                                                                                echo $status2['label'];
                                                                                                ?>
                                                                                    </option>
                                                                                    <?php
                                                                                } else if ($status2['label'] == "Payment") {
                                                                                    ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php
                                                                                                echo $status2['label'];
                                                                                                ?>
                                                                                    </option>
                                                                                    <?php
                                                                                }
                                                                            } else {

                                                                                if ($status2['label'] == "Repair Completed") {
                                                                                    ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php
                                                                                                if (($store_info[0]['store_name'] == "Hawthorne") || ($store_info[0]['store_name'] == "Venice") || ($store_info[0]['store_name'] == "Usc Repair")) {
                                                                                                    //echo "Sent";
                                                                                                    echo $status2['label'];
                                                                                                }
                                                                                                ?>
                                                                                    </option>
                                                                                <?php } else if ($status2['label'] == "Repairing") { ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php echo "Repairing"; ?>
                                                                                    </option>
                                                                                <?php } else if ($status2['label'] == "Update") {
                                                                                    ?>
                                                                                    <option value="<?php echo $key; ?>"
                                                                                            <?php if ($key == $quote->quote_status_id) { ?>selected="selected"<?php } ?>>
                                                                                                <?php
                                                                                                echo $status2['label'];
                                                                                                ?>
                                                                                    </option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                                                <div class="quote-properties">

                                                                    <label for="send_email">
                                                                        <?php echo lang('send_email'); ?>
                                                                    </label>
                                                                    <select name="send_email" id="send_email"
                                                                            class="form-control input-sm">

                                                                        <option value = "Yes">Yes</option>
                                                                        <option value = "No" selected="No">No</option>



                                                                    </select>
                                                                </div>
                                                            <?php } ?>
                                                            <div class="form-group">
                                                                <label class="control-label"><?php echo lang('staff_comments'); ?></label>
                                                                <textarea name="staff_comments" id="staff_comments" 
                                                                          class="input-sm form-control" rows="4"></textarea>
                                                            </div>
                                                            <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                                                <div class="form-group">
                                                                    <label class="control-label"><?php echo lang('notes_to_customer'); ?></label>
                                                                    <textarea name="notes_to_customer" id="notes_to_customer" 
                                                                              class="input-sm form-control" rows="4"></textarea>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                                                <button class=" btn btn-sm btn-default" type="button" onclick="ShowHide('password')">Show/Hide Password</button>
                                                            <?php } ?>
                                                            <div class="quote-properties" id = "password" hidden>
                                                                <label for="quote_password">
                                                                    <?php echo lang('quote_password'); ?>
                                                                </label>

                                                                <div class="controls">
                                                                    <input type="text" id="quote_password" class="form-control input-sm"
                                                                           value="<?php echo $quote->quote_password; ?>">
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div></td></tr>
                                        </table>
                                    </div>
                                </div>


                            </td>
                        </tr><tr><td>

                                <div id="panel-quick-actions" class="panel panel-default" style="width:800px; height: 164px; margin-left:5px; margin-top:-15px; overflow: auto;">
                                    <div class="panel-heading" align="left">
                                        <label for="ticket_content" ><?php echo "<b>Invoices History</b>"; ?></label>
                                    </div>
                                    <div style="max-height: 130px; overflow: auto;">
                                        <table class = "table table-striped" >
                                            <tr >
                                                <td><label class="control-label"><?php echo lang('status'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('invoice'); ?></label></td>
                                                <td style="width:130px;"><label class="control-label"><?php echo lang('date_changed'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('staff_comments'); ?></label></td>
                                                <td><label class="control-label"><?php echo lang('notes_to_customer'); ?></label></td>                            
                                                <td><label class="control-label"><?php echo lang('user'); ?></label></td>
                                            </tr>


                                            <?php
                                            if ($this->session->userdata("is_tech") == 0) {
                                                foreach ($invoice_data as $history_invoice) {
                                                    ?>
                                                    <tr>
                                                        <td class = "<?php
                                                        if ($history_invoice['id_status'] == 1) {
                                                            echo "estimates";
                                                        } else if ($history_invoice['id_status'] == 2) {
                                                            echo "sent";
                                                        } else if ($history_invoice['id_status'] == 3) {
                                                            echo "viewed";
                                                        } else if ($history_invoice['id_status'] == 4) {
                                                            echo "paid";
                                                        }
                                                        ?>"><?php
                                                                if ($history_invoice['id_status'] == 1) {
                                                                    echo '<font color="blue">Estimates</font>';
                                                                } else if ($history_invoice['id_status'] == 2) {
                                                                    echo lang('sent');
                                                                } else if ($history_invoice['id_status'] == 3) {
                                                                    echo '<font color="red">Denied</font>';
                                                                } else if ($history_invoice['id_status'] == 4) {
                                                                    echo lang('paid');
                                                                }
                                                                ?></td>
                                                        <td style="border-left:1px #ddd solid;"><a href="<?php echo site_url("/invoices/view/" . $history_invoice['id_invoice']); ?>" target="_BLANK"><?php
                                                                if ($history_invoice['invoice_number'] == "") {
                                                                    echo $history_invoice['id_invoice'];
                                                                } else {
                                                                    echo $history_invoice['invoice_number'];
                                                                }
                                                                ?></a></td>
                                                        <td style="border-left:1px #ddd solid;"><?php echo date("m-d-Y h:i:s", strtotime($history_invoice['date_changed'])); ?></td>
                                                        <td style="border-left:1px #ddd solid;"><?php echo $history_invoice['staff_comments']; ?></td>
                                                        <td style="border-left:1px #ddd solid;"><?php
                                                            if ($history_invoice['notes_to_customer'] == 0) {
                                                                echo "";
                                                            } else {
                                                                echo $history_invoice['notes_to_customer'];
                                                            }
                                                            ?></td>
                                                        <td style="border-left:1px #ddd solid;"><?php echo $history_invoice['user_name']; ?></td>

                                                    </tr>
                                                <?php
                                                }
                                            }
                                            ?>


                                        </table>
                                    </div>
                                </div>

                            </td></tr>

                        <tr>
                            <td colspan="2">

                                <div class="col-xs-12 col-sm-12" style="margin-left:-10px; margin-top:-15px;">

                                    <div class="form-group">
<?php $this->layout->load_view('quotes/partial_item_table'); ?>

                                    </div>

                                </div>

                            </td>
                        </tr>

                    </table>
                </div>












<?php if ($this->session->userdata("is_tech") == 0) { ?>


                </div>
            </div>



            <hr/>







            <div class="row" >



                <div class="col-xs-12 col-sm-8" >

                    <div class="form-group">
                        <label class="control-label"><?php echo lang('attachments'); ?></label>
                        <br/>
                        <!-- The fileinput-button span is used to style the file input field as button -->
                        <span class="btn btn-default fileinput-button">
                            <i class="fa fa-plus"></i>
                            <span><?php echo lang('add_files'); ?></span>
                        </span>
                    </div>

                </div>






                <!-- dropzone -->
                <div id="actions" class="col-xs-12 col-sm-12 row">
                    <div class="col-lg-7">
                    </div>
                    <div class="col-lg-5">
                        <!-- The global file processing state -->
                        <span class="fileupload-process">
                            <div id="total-progress" class="progress progress-striped active" role="progressbar"
                                 aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"
                                     data-dz-uploadprogress></div>
                            </div>
                        </span>
                    </div>

                    <div class="table table-striped" class="files" id="previews">

                        <div id="template" class="file-row">
                            <!-- This is used as the file preview template -->
                            <div>
                                <span class="preview"><img data-dz-thumbnail/></span>
                            </div>
                            <div>
                                <p class="name" data-dz-name></p>
                                <strong class="error text-danger" data-dz-errormessage></strong>
                            </div>
                            <div>
                                <p class="size" data-dz-size></p>

                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0"
                                     aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success" style="..."
                                         data-dz-uploadprogress></div>
                                </div>
                            </div>
                            <div>
                                <button data-dz-remove class="btn btn-danger btn-sm delete">
                                    <i class="fa fa-trash-o"></i>
                                    <span><?php echo lang('delete'); ?></span>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- stop dropzone -->
                <div class="pull-right btn-group">
                    <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_quote_2" style="float:right; width:200px; font-size:14px; margin-right:30px;">
                        <i class="fa fa-check"></i>
    <?php echo lang('save'); ?>
                    </a>
                </div>
            </div>
            <!-- right div side -->



    </div>


    <?php if ($custom_fields): ?>
        <h4 class="no-margin"><?php echo lang('custom_fields'); ?></h4>
    <?php endif; ?>
        <?php foreach ($custom_fields as $custom_field) { ?>
        <label class="control-label">
        <?php echo $custom_field->custom_field_label; ?>
        </label>
        <input type="text" class="form-control"
               name="custom[<?php echo $custom_field->custom_field_column; ?>]"
               id="<?php echo $custom_field->custom_field_column; ?>"
               value="<?php echo form_prep($this->mdl_quotes->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
    <?php } ?>

        <?php //if ($quote->quote_status_id != 1) {                  ?>
    <p class="padded">
        <?php //echo lang('guest_url').":";                 ?>
    <?php //echo auto_link(site_url('guest/view/fquote/' . $quote->quote_url_key));                          ?>
    </p>

    </div>
<?php } ?>
<style>
    .disabled {
        pointer-events: none;
        cursor: not-allowed;
        background-color: #eee;
    }
</style>
<script>
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var myDropzone = new Dropzone(document.body, {// Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/' . $quote->client_id . '/' . $quote->quote_url_key) ?>", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON("<?php echo site_url('upload/upload_file/' . $quote->client_id . '/' . $quote->quote_url_key) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    if (val.fullname.match(/\.(jpg|jpeg|png|gif)$/)) {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                                '<?php echo base_url(); ?>uploads/customer_files/' + val.fullname);
                    } else {
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                                '<?php echo base_url(); ?>assets/default/img/favicon.png');
                    }
                    thisDropzone.emit("complete", mockFile);
                    thisDropzone.emit("success", mockFile);
                });
            });
        }
    });

    myDropzone.on("addedfile", function (file) {
        myDropzone.emit("thumbnail", file, '<?php echo base_url(); ?>assets/default/img/favicon.png');
    });

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    });

    myDropzone.on("sending", function (file) {
        // Show the total progress bar when upload starts
        document.querySelector("#total-progress").style.opacity = "1";
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) {
        document.querySelector("#total-progress").style.opacity = "0";
    });

    myDropzone.on("removedfile", function (file) {
        $.ajax({
            url: "<?php echo site_url('upload/delete_file/' . $quote->quote_url_key) ?>",
            type: "POST",
            data: {'name': file.name}
        });
    });
</script>

<script type="text/javascript">

    function ShowHide(el) {
        var display = document.getElementById(el).style.display;
        if (display === "block")
            document.getElementById(el).style.display = 'none';
        else
        {
            document.getElementById(el).style.display = 'block';
        }

    }



    $(function () {
        setInterval(function () {
            $.post("<?php echo site_url('clients/clients/check_signature'); ?>", {
                ticket_id: <?php echo $quote_id; ?>

            },
                    function (data) {
                        if (data == "true")
                        {
                            $("#signature_status").html("<label style='background-color:white; color:green;'>&nbsp; - Signed</font>");


                        }
                    });
        }, 1000);


        $('.btn_add_product').click(function () {
            $.post("<?php echo site_url('parts/insert_quote_id_aux'); ?>", {
                ticket_id: <?php echo $quote_id; ?>

            },
                    function (data) {
                        //alert(data);
                    });

            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups/'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {

            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();

        });

        $('#quote_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_change_client'); ?>", {
                quote_id: <?php echo $quote_id; ?>,
                client_name: "<?php echo $this->db->escape_str($quote->client_name); ?>"
            });
        });

<?php if (!$items) { ?>
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
<?php } ?>
        $('#btn_save_quote').click(function () {


            if (($("#quote_status_id").val() == 5) || ($("#quote_status_id").val() == 6))
            {
                /*
                 if ($('#amount').val() == 0)
                 {
                 $("#amount_zero").show();
                 callback();
                 
                 }*/
            }
            var items = [];
            var item_order = 1;
            store_valor = $(".store").serialize().replace('store=', '');

            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('quotes/ajax/save'); ?>", {
                quote_id: <?php echo $quote_id; ?>,
                quote_number: $('#quote_number').val(),
                quote_date_created: $('#quote_date_created').val(),
                quote_date_expires: $('#quote_date_expires').val(),
                quote_status_id: $('#quote_status_id').val(),
                send_email: $('#send_email').val(),
                quote_password: $('#quote_password').val(),
                items: JSON.stringify(items),
                quote_discount_amount: $('#quote_discount_amount').val(),
                quote_discount_percent: $('#quote_discount_percent').val(),
                notes: $('#notes').val(),
                amount: $('#amount').val(),
                brand: $('#brand').val(),
                model: $('#model').val(),
                serial_number: $('#serial_number').val(),
                data_recovery: $('#data_recovery').val(),
                client_os_password: '<?php echo $this->encrypt->encode('client_os_password'); ?>',
                accessories_included: $('#accessories_included').val(),
                problem_description_product: $('#problem_description_product').val(),
                custom: $('input[name^=custom]').serializeArray(),
                staff_comments: $('#staff_comments').val(),
                notes_to_customer: $('#notes_to_customer').val(),
                store: store_valor
            },
                    function (data) {

<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        var response = JSON.parse(data);
                        if (response.success == '1') {
                            window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
                        } else {
                            $('.control-group').removeClass('error');
                            for (var key in response.validation_errors) {
                                $('#' + key).parent().parent().addClass('error');
                            }
                        }
                    });
        });
        $('#btn_save_quote_2').click(function () {


            if (($("#quote_status_id").val() == 5) || ($("#quote_status_id").val() == 6))
            {
                /*
                 if ($('#amount').val() == 0)
                 {
                 $("#amount_zero").show();
                 callback();
                 
                 }*/
            }
            var items = [];
            var item_order = 1;
            store_valor = $(".store").serialize().replace('store=', '');

            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('quotes/ajax/save'); ?>", {
                quote_id: <?php echo $quote_id; ?>,
                quote_number: $('#quote_number').val(),
                quote_date_created: $('#quote_date_created').val(),
                quote_date_expires: $('#quote_date_expires').val(),
                quote_status_id: $('#quote_status_id').val(),
                send_email: $('#send_email').val(),
                quote_password: $('#quote_password').val(),
                items: JSON.stringify(items),
                quote_discount_amount: $('#quote_discount_amount').val(),
                quote_discount_percent: $('#quote_discount_percent').val(),
                notes: $('#notes').val(),
                amount: $('#amount').val(),
                brand: $('#brand').val(),
                model: $('#model').val(),
                serial_number: $('#serial_number').val(),
                data_recovery: $('#data_recovery').val(),
                client_os_password: '<?php echo $this->encrypt->encode('client_os_password'); ?>',
                accessories_included: $('#accessories_included').val(),
                problem_description_product: $('#problem_description_product').val(),
                custom: $('input[name^=custom]').serializeArray(),
                staff_comments: $('#staff_comments').val(),
                notes_to_customer: $('#notes_to_customer').val(),
                store: store_valor
            },
                    function (data) {

<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        var response = JSON.parse(data);
                        if (response.success == '1') {
                            window.location = "<?php echo site_url('quotes/view'); ?>/" + <?php echo $quote_id; ?>;
                        } else {
                            $('.control-group').removeClass('error');
                            for (var key in response.validation_errors) {
                                $('#' + key).parent().parent().addClass('error');
                            }
                        }
                    });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url("quotes/generate_pdf/$quote_id"); ?>', '_blank');
        });

        $(document).ready(function () {
            if ($('#quote_discount_percent').val().length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            }
            if ($('#quote_discount_amount').val().length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            }
        });
        $('#quote_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_percent').prop('disabled', true);
            } else {
                $('#quote_discount_percent').prop('disabled', false);
            }
        });
        $('#quote_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#quote_discount_amount').prop('disabled', true);
            } else {
                $('#quote_discount_amount').prop('disabled', false);
            }
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $("#item_table").sortable({
            helper: fixHelper,
            items: 'tbody'
        });

    });



</script>
