<?php
//SELECT TO GET INVOICES HISTORY DATA
$this->db->select("*");
$this->db->join("ip_invoices_status_history", "ip_invoices_status_history.id_invoice = ip_invoices.invoice_id");
$this->db->join("ip_users", "ip_users.user_id = ip_invoices_status_history.user_id");
$this->db->join("ip_payments", "ip_payments.invoice_id = ip_invoices.invoice_id", 'left');
$this->db->join("ip_payment_methods", "ip_payment_methods.payment_method_id = ip_payments.payment_method_id", 'left');
$this->db->where("ip_invoices.invoice_id", $invoice_id);
$this->db->order_by("ip_invoices_status_history.date_changed", "DESC");
$invoice_data = $this->db->get("ip_invoices")->result_array();

//GET INVOICE STORE
$this->db->where("id", $invoice->store);
$store = $this->db->get("ip_stores")->result_object();
$store = $store[0]->store_name;

//SELECT TO GET PAYMENT HISTORY DATA
$this->db->select("*");
$this->db->join("ip_payment_methods", "ip_payment_methods.payment_method_id = ip_payments.payment_method_id");
$this->db->where("invoice_id", $invoice_id);
$this->db->order_by("ip_payments.payment_id", "desc");
$ip_payments = $this->db->get("ip_payments")->result_array();

//GET QUOTE ID
$this->db->select("quote_id");
$this->db->where("quote_number", $invoice->ticket_id);
$ip_quotes = $this->db->get("ip_quotes")->result_array();

//INSERT OR UPDATE GUEST_URL

$guest_url = site_url('guest/view/invoice/' . $invoice->invoice_url_key);
$this->db->select("*");
$this->db->where("invoice_id", $invoice->invoice_id);
$rows_guest_url = $this->db->get("ip_guest_url")->num_rows();

$insert_update_guest_url = array(
    "invoice_id" => $invoice->invoice_id,
    "guest_url" => $guest_url
);

if ($rows_guest_url == 0) {
    $this->db->insert("ip_guest_url", $insert_update_guest_url);
} else {
    $this->db->where("invoice_id", $invoice->invoice_id);
    $this->db->update("ip_guest_url", $insert_update_guest_url);
}
?>

<script type="text/javascript">


    function enable_autocomplete(InputClass, qtt, price, description, cost) {

        $(InputClass).autocomplete({
            source: '<?php echo site_url('invoices/ajax/show_products'); ?>',
            select: function (event, ui) {
                $.post("<?php echo site_url('invoices/ajax/set_product'); ?>", {
                    product_name: ui.item.value

                },
                        function (data) {
                            var response = JSON.parse(data);
                            $(qtt).val('1.00');
                            $(price).val(response.product_price);
                            $(description).val(response.product_description);
                            $(cost).val(response.purchase_price);
                        });


                //return false;
            }
        });
    }


    $(function () {

        $("#item_name").autocomplete({
            source: '<?php echo site_url('invoices/ajax/show_products'); ?>',
            select: function (event, ui) {
                $.post("<?php echo site_url('invoices/ajax/set_product'); ?>", {
                    product_name: ui.item.value

                },
                        function (data) {
                            var response = JSON.parse(data);
                            $("#item_quantity").val('1.00');
                            $("#item_price").val(response.product_price);
                            $("#item_description").val(response.product_description);
                        });


                //return false;
            }
        });
        var rows_qtt;
        var rows_qtt_aux;
        var x;

        $.post("<?php echo site_url('invoices/ajax/update_nrows'); ?>", {
            erase: 1

        },
                function (data) {

                });

        $('.btn_add_product').click(function () {

            $.post("<?php echo site_url('parts/insert_invoice_id_aux'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>

            },
                    function (data) {
                        //alert(data);
                    });
            $('#modal-placeholder').load("<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" + Math.floor(Math.random() * 1000));
        });

        $('.btn_add_row').click(function () {
            $.post("<?php echo site_url('invoices/ajax/update_nrows'); ?>", {
                qtt_rows: 1


            },
                    function (data) {
                        rows_qtt = data;
                        rows_qtt_aux = parseInt(rows_qtt) + 1;

                    });
            if (rows_qtt == null)
            {
                rows_qtt = 0;
            }

            x = $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
            x.find('#item_name').attr({class: 'new_item_name' + rows_qtt + " input-sm form-control"});
            x.find('#item_price').attr({id: 'new_item_price' + rows_qtt});
            x.find('#item_description').attr({id: 'new_item_description' + rows_qtt});
            x.find('#item_quantity').attr({id: 'new_item_quantity' + rows_qtt});
            x.find('#cost').attr({id: 'new_item_cost' + rows_qtt});
            $('html, body').animate({scrollTop: $("#new_item_price" + rows_qtt).offset().top - 50}, 'slow');
            $(".new_item_name" + rows_qtt).focus();
            enable_autocomplete('.new_item_name' + rows_qtt, '#new_item_quantity' + rows_qtt, "#new_item_price" + rows_qtt, "#new_item_description" + rows_qtt, "#new_item_cost" + rows_qtt);

        });

<?php if (!$items) { ?>
            x = $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
            x.find('#item_name').attr({class: 'new_item_name' + rows_qtt + " input-sm form-control"});
            x.find('#item_price').attr({id: 'new_item_price' + rows_qtt});
            x.find('#item_description').attr({id: 'new_item_description' + rows_qtt});
            x.find('#item_quantity').attr({id: 'new_item_quantity' + rows_qtt});
            x.find('#cost').attr({id: 'new_item_cost' + rows_qtt});
            enable_autocomplete('.new_item_name' + rows_qtt, '#new_item_quantity' + rows_qtt, "#new_item_price" + rows_qtt, "#new_item_description" + rows_qtt, "#new_item_cost" + rows_qtt);
<?php } ?>

        $('#btn_create_recurring').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_recurring'); ?>", {invoice_id: <?php echo $invoice_id; ?>});
        });

        $('#invoice_change_client').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_change_client'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                client_name: "<?php echo $this->db->escape_str($invoice->client_name); ?>"
            });
        });
        
        //GET BUTTON CLICK TO SAVE ITEM COSTS
$("#save_costs").click(function (){
        $(document).ajaxStart(function(){
    $("#cost_loader").show();
});
        var items_cost = [];
        var item_order = 1;
          $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea, checkbox').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });

                row['item_order'] = item_order;
                item_order++;
                items_cost.push(row);

            });
            
             $.post("<?php echo site_url('invoices/ajax/save_itemcost'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                items: JSON.stringify(items_cost),
               



            },
                    function (data) {
                        $("#cost_loader").hide();

                    });
        
        });
        $('#btn_save_invoice').click(function () {


            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea, checkbox').each(function () {
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
            $.post("<?php echo site_url('invoices/ajax/save'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                invoice_number: $('#invoice_number').val(),
                invoice_date_created: $('#invoice_date_created').val(),
                invoice_date_due: $('#invoice_date_due').val(),
                invoice_status_id: $('#invoice_status_id').val(),
                invoice_password: $('#invoice_password').val(),
                items: JSON.stringify(items),
                invoice_discount_amount: $('#invoice_discount_amount').val(),
                invoice_discount_percent: $('#invoice_discount_percent').val(),
                invoice_terms: $('#invoice_terms').val(),
                custom: $('input[name^=custom]').serializeArray(),
                payment_method: $('#payment_method').val(),
                staff_comments: $("#staff_comments").val(),
                notes_to_customer: $('#notes_to_customer').val()



            },
                    function (data) {
<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>

                        var response = JSON.parse(data);
                        if (response.success == '1') {
                            window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                        }


                        /*else
                         {
                         $('#fullpage-loader').hide();
                         $('.control-group').removeClass('has-error');
                         $('div.alert[class*="alert-"]').remove();
                         $('#invoice_form').prepend('<div class="alert alert-danger">Item is required</div>');
                         }*/


                    });
        });

        $('#btn_save_invoice_2').click(function () {
            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea, checkbox').each(function () {
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
            $.post("<?php echo site_url('invoices/ajax/save'); ?>", {
                invoice_id: <?php echo $invoice_id; ?>,
                invoice_number: $('#invoice_number').val(),
                invoice_date_created: $('#invoice_date_created').val(),
                invoice_date_due: $('#invoice_date_due').val(),
                invoice_status_id: $('#invoice_status_id').val(),
                invoice_password: $('#invoice_password').val(),
                items: JSON.stringify(items),
                invoice_discount_amount: $('#invoice_discount_amount').val(),
                invoice_discount_percent: $('#invoice_discount_percent').val(),
                invoice_terms: $('#invoice_terms').val(),
                custom: $('input[name^=custom]').serializeArray(),
                payment_method: $('#payment_method').val(),
                staff_comments: $("#staff_comments").val(),
                notes_to_customer: $('#notes_to_customer').val()



            },
                    function (data) {
<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>

                        var response = JSON.parse(data);
                        if (response.success == '1') {
                            window.location = "<?php echo site_url('invoices/view'); ?>/" + <?php echo $invoice_id; ?>;
                        }


                        /*else
                         {
                         $('#fullpage-loader').hide();
                         $('.control-group').removeClass('has-error');
                         $('div.alert[class*="alert-"]').remove();
                         $('#invoice_form').prepend('<div class="alert alert-danger">Item is required</div>');
                         }*/


                    });
        });

        $('#btn_generate_pdf').click(function () {
            window.open('<?php echo site_url('invoices/generate_pdf/' . $invoice_id); ?>', '_blank');
        });

<?php if ($invoice->is_read_only != 1): ?>
            var fixHelper = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            };

            $("#item_table").sortable({
                items: 'tbody',
                helper: fixHelper
            });

            $(document).ready(function () {
                if ($('#invoice_discount_percent').val().length > 0) {
                    $('#invoice_discount_amount').prop('disabled', true);
                }
                if ($('#invoice_discount_amount').val().length > 0) {
                    $('#invoice_discount_percent').prop('disabled', true);
                }
            });
            $('#invoice_discount_amount').keyup(function () {
                if (this.value.length > 0) {
                    $('#invoice_discount_percent').prop('disabled', true);
                } else {
                    $('#invoice_discount_percent').prop('disabled', false);
                }
            });
            $('#invoice_discount_percent').keyup(function () {
                if (this.value.length > 0) {
                    $('#invoice_discount_amount').prop('disabled', true);
                } else {
                    $('#invoice_discount_amount').prop('disabled', false);
                }
            });
<?php endif; ?>
    });

</script>

<?php
echo $modal_delete_invoice;
echo $modal_add_invoice_tax;
if ($this->config->item('disable_read_only') == true) {
    $invoice->is_read_only = 0;
}
?>

<div id="headerbar">
    <h1>
        <?php
        echo lang('invoice') . ' ';
        ?><a href ="<?php echo site_url('/quotes/view/' . $ip_quotes[0]['quote_id']); ?>" title="Click here to open the ticket">
            <?php
            echo($invoice->invoice_number ? '#' . $invoice->invoice_number : $invoice->invoice_id);
            ?></a>
    </h1>

    <div
        class="pull-right <?php if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) { ?>btn-group<?php } ?>">

        <a href="#" class="invoice-add-payment btn btn-sm btn-default"
           data-invoice-id="<?php echo $invoice_id; ?>"
           data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
           data-invoice-payment-method="<?php echo $invoice->payment_method; ?>" >
            <i class="fa fa-credit-card fa-margin"></i>
            <?php echo lang('enter_payment'); ?>
        </a>

        <?php if (($invoice->invoice_status_id == 2) && ($invoice->invoice_url_key != '')) { ?>
            <a href="#" class="btn btn-sm btn-default" onclick="send_email(<?php echo $invoice_id; ?>);">
                <i class="fa fa-send fa-margin"></i>
                <?php echo lang('send_email'); ?>
            </a>

        <?php } if (($invoice->invoice_status_id != 1) && ($invoice->invoice_url_key != '')) { ?> 
            <a class="btn btn-sm btn-default" href="<?php echo site_url('guest/view/invoice/' . $invoice->invoice_url_key); ?>" target="_blank" 
               >
                <i class="fa fa-print fa-margin"></i>
                <?php echo lang('print_receipt'); ?>
            </a>
        <?php } if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) { ?>
            <a href="<?php echo site_url('clients/send_invoice_terminal/' . $invoice->invoice_id . "/" . $invoice->client_id); ?>" class="send_invoice_terminal btn btn-sm btn-default">
                <?php echo lang('send_invoice_to_terminal'); ?>
            </a>
        <?php } ?>
        <div class="options btn-group pull-left">
            <a class="btn btn-sm btn-default dropdown-toggle"
               data-toggle="dropdown" href="#">
                <i class="fa fa-caret-down no-margin"></i> <?php echo lang('options'); ?>
            </a>
            <ul class="dropdown-menu">
                <?php if ($invoice->is_read_only != 1) { ?>
                    <li>
                        <a href="#add-invoice-tax" data-toggle="modal">
                            <i class="fa fa-plus fa-margin"></i> <?php echo lang('add_invoice_tax'); ?>
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a href="#" id="btn_create_credit" data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-minus fa-margin"></i> <?php echo lang('create_credit_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" class="invoice-add-payment"
                       data-invoice-id="<?php echo $invoice_id; ?>"
                       data-invoice-balance="<?php echo $invoice->invoice_balance; ?>"
                       data-invoice-payment-method="<?php echo $invoice->payment_method; ?>">
                        <i class="fa fa-credit-card fa-margin"></i>
                        <?php echo lang('enter_payment'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php echo lang('download_pdf'); ?>
                    </a>
                </li>
                <?php if (($invoice->invoice_status_id == 2) && ($invoice->invoice_url_key != '')) { ?>
                    <li>
                        <a href="#" onclick="send_email(<?php echo $invoice_id; ?>);">
                            <i class="fa fa-send fa-margin"></i>
                            <?php echo lang('send_email'); ?>
                        </a>
                    </li>
                <?php } ?>
                <li class="divider"></li>
                <li>
                    <a href="#" id="btn_create_recurring"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-repeat fa-margin"></i>
                        <?php echo lang('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_invoice"
                       data-invoice-id="<?php echo $invoice_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php echo lang('copy_invoice'); ?>
                    </a>
                </li>
                <?php if ($invoice->invoice_status_id == 1 || ($this->config->item('enable_invoice_deletion') === true && $invoice->is_read_only != 1)) { ?>
                    <li>
                        <a href="#delete-invoice" data-toggle="modal">
                            <i class="fa fa-trash-o fa-margin"></i>
                            <?php echo lang('delete'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?php if ($invoice->is_read_only != 1) { ?>
            <a href="#" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i> <?php echo lang('add_new_row'); ?>
            </a>
            <a href="#" class="btn_add_product btn btn-sm btn-default">
                <i class="fa fa-database"></i>
                <?php echo lang('add_product'); ?>
            </a>
            <?php
        }
        if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
            ?>
            <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_invoice">
                <i class="fa fa-check"></i> <?php echo lang('save'); ?>
            </a>
        <?php } ?>
    </div>

    <div class="invoice-labels pull-right">
        <?php if ($invoice->invoice_is_recurring) { ?>
            <span class="label label-info"><?php echo lang('recurring'); ?></span>
        <?php } ?>
        <?php if ($invoice->is_read_only == 1) { ?>
            <span class="label label-danger">
                <i class="fa fa-read-only"></i> <?php echo lang('read_only'); ?>
            </span>
        <?php } ?>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="invoice_form" class="form-horizontal">
        <label id="successemail" class="alert alert-success" style="width:100%;" hidden>Email has been sent</label>
        <label id="erroremail" class="alert alert-danger" style="width:100%;" hidden>Problem to send email, please check client info</label>
        <div class="invoice">

            <div class="cf row">
                <table style="float:left;"><tr><td>
                            <div id="panel-quick-actions" class="panel panel-default" style="width:300px; float:left;">

                                <div class="panel-heading" align="left">
                                    <label for="ticket_content" ><?php echo "<b>Customer Details</b>"; ?></label>
                                </div>
                                <br>


                                <div class="pull-left" style="padding-left:10px;">

                                    <h2>
                                        <a href="<?php echo site_url('clients/view/' . $invoice->client_id); ?>" title="Open Customer Dashboard"><?php echo $invoice->client_name; ?></a>
                                        <?php if($invoice->client_company != ""){ echo "(Dealer)"; } ?>
                                        <a href="<?php echo site_url('clients/form/' . $invoice->client_id); ?>" title="Edit Customer">
                                            <img src="../../../../assets/default/img/edit_icon.png" style="width:25px; height:25px;"></a>
                                        <?php if ($invoice->invoice_status_id == 1) { ?>
                                            <span id="invoice_change_client" class="fa fa-edit cursor-pointer small"
                                                  data-toggle="tooltip" data-placement="bottom"
                                                  title="<?php echo lang('change_client'); ?>"></span>
                                              <?php } ?>
                                    </h2><br>
                                    <span>
                                        <label style="font-weight:bold;">Billing Address</label><br>
                                        <?php echo ($invoice->client_address_1) ? $invoice->client_address_1 . '<br>' : ''; ?>
                                        <?php echo ($invoice->client_address_2) ? $invoice->client_address_2 . '<br>' : ''; ?>
                                        <?php echo ($invoice->client_city) ? $invoice->client_city : ''; ?>
                                        <?php echo ($invoice->client_state) ? $invoice->client_state : ''; ?>
                                        <?php echo ($invoice->client_zip) ? $invoice->client_zip : ''; ?>
                                        <?php echo ($invoice->client_country) ? '<br>' . $invoice->client_country : ''; ?>
                                    </span><br>
                                    <?php if (($store == 'Website Sale') || ($store == 'Website Repair') || ($store == 'General')) { ?>
                                        <span>
                                            <label style="font-weight:bold;">Shipping Address</label><br>
                                            <?php echo ($invoice->shipping_address) ? $invoice->shipping_address . '<br>' : ''; ?>
                                            <?php echo ($invoice->shipping_city) ? $invoice->shipping_city : ''; ?>
                                            <?php echo ($invoice->shipping_state) ? $invoice->shipping_state : ''; ?>
                                            <?php echo ($invoice->shipping_zip) ? $invoice->shipping_zip : ''; ?>
                                            <?php echo ($invoice->shipping_country) ? '<br>' . $invoice->shipping_country : ''; ?>
                                        </span>
                                        <br><br>
                                    <?php } if ($invoice->client_phone) { ?>
                                        <span><strong><?php echo lang('phone'); ?>
                                                :</strong> <?php echo $invoice->client_phone; ?></span><br>
                                    <?php } ?>
                                    <?php if ($invoice->client_email) { ?>
                                        <span><strong><?php echo lang('email'); ?>
                                                :</strong> <?php echo $invoice->client_email; ?></span>
                                    <?php } ?>


                                </div>

                            </div></td></tr>
                    <tr><td>
                            <div id="panel-quick-actions" class="panel panel-default" style="width:300px; margin-top:-15px; height: auto; float:left;">
                                <div class="panel-heading" align="left">
                                    <label for="ticket_content" ><?php echo "<b>Shipping/Products Details</b>"; ?></label>
                                </div>
                                <br>

                                <?php if (($store == "Website Sale") || ($store == "Website Repair")) { ?>
                                    <label style="font-weight:bold; margin-left:10px;">Shipping Info</label><br>
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
                                            if ($icount == 0) {
                                                print_r("&nbsp;&nbsp;&nbsp;<b><u>" . $return_client[0] . "</u></b>" . " - " . $return_client_name . "<br>");
                                                $icount++;
                                            } else if ($icount == 1) {
                                                print_r("&nbsp;&nbsp;&nbsp;<b><u>" . $shipping_box[0] . "</u></b>" . " - " . $box_client_name . "<br>");
                                                $icount++;
                                            } else if ($icount == 2) {
                                                print_r("&nbsp;&nbsp;&nbsp;<b><u>" . $shipping_label[0] . "</u></b>" . " - " . $label_client_name . "<br>");
                                                $icount++;
                                            } else {
                                                break;
                                            }
                                        }
                                    }
                                }
                                ?>
                                <label style="font-weight:bold; margin-left:10px;">Products Info</label><br>
                                <?php
                                foreach ($items as $products) {
                                    if ((strpos($products->item_name, 'Return to client:') !== false) || (strpos($products->item_name, 'Shipping Box:') !== false) || (strpos($products->item_name, 'Shipping Label:') !== false)) {
                                        
                                    } else {
                                        print_r("&nbsp;&nbsp;&nbsp;" . $products->item_name . " - " . $products->item_price . "<br>");
                                    }
                                }
                                ?>


                            </div>
                        </td></tr>
                    <tr><td>
                        <div id="panel-quick-actions" class="panel panel-default" style="width:300px;  margin-top:-15px;  float:left;">
                                <div class="panel-heading" align="left">
                                    <label for="payment_log" ><?php echo "<b>Payment Log</b>"; ?></label>
                                </div>
                            <div style="overflow:auto; height: 120px;">
                            <table class="table table-bordered">
                                <tr><td>
                                        <b>Aturhorize.net Return</b>
                                    </td>
                                <td>
                                        <b>Date</b>
                                    </td></tr>
                                
                            <?php foreach($payment_log as $payment_log){ ?>
                                <tr>
                                    <td><?php echo $payment_log->message; ?></td>
                                    <td><?php echo $payment_log->date_time; ?></td>
                             </tr>
                            <?php } ?>
                               
                            </table>
                            </div>
                        </div>
                        </td></tr>
                </table>


                <div id="panel-quick-actions" class="panel panel-default" style="width:1400px; margin-left:10px; height: auto; float:left;">
                    <div class="panel-heading" align="left">
                        <label for="ticket_content" ><?php echo "<b>Invoice Details</b>"; ?></label>
                    </div>
                    <br>
                    <div class=" row">

                        <?php if ($invoice->invoice_sign == -1) { ?>
                            <div class="col-xs-12">
                                <span class="label label-warning">
                                    <i class="fa fa-credit-invoice"></i>&nbsp;
                                    <?php
                                    echo lang('credit_invoice_for_invoice') . ' ';
                                    echo anchor('/invoices/view/' . $invoice->creditinvoice_parent_id, $invoice->creditinvoice_parent_id)
                                    ?>
                                </span>
                            </div>
                        <?php } ?>





                    </div>
                    <div class="col-xs-12 col-sm-6">

                        <div class="invoice-properties">
                            <label><?php echo lang('invoice'); ?> #</label>
                            <input type="text" id="invoice_number"
                                   class="input-sm form-control"
                                   <?php if ($invoice->invoice_number) : ?>
                                       value="<?php echo $invoice->invoice_number; ?>"
                                   <?php else : ?>
                                       value = "<?php echo $invoice->invoice_id; ?>" placeholder="<?php echo lang('not_set'); ?>"
                                   <?php endif; ?>
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>
                        </div>

                        <div class="invoice-properties has-feedback">
                            <label><?php echo lang('date'); ?></label>

                            <div class="input-group">
                                <input name="invoice_date_created" id="invoice_date_created"
                                       class="form-control datepicker"
                                       value="<?php echo date_from_mysql($invoice->invoice_date_created); ?>"
                                       <?php
                                       if ($invoice->is_read_only == 1) {
                                           echo 'disabled="disabled"';
                                       }
                                       ?>>
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>

                        <div class="invoice-properties has-feedback">
                            <label><?php echo lang('due_date'); ?></label>

                            <div class="input-group">
                                <input name="invoice_date_due" id="invoice_date_due"
                                       class="form-control datepicker"
                                       value="<?php echo date("m/d/Y"); ?>"
                                       <?php
                                       if ($invoice->is_read_only == 1) {
                                           echo 'disabled="disabled"';
                                       }
                                       ?>>
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>
                        <div id="panel-quick-actions" class="panel panel-default" style="width:670px;  height: auto; float:left;">
                            <div class="panel-heading" align="left">
                                <label for="ticket_content" ><?php echo "<b>Invoices History</b>"; ?></label>
                            </div>





                            <div style="height:100px; overflow: auto;">
                                <table class = "table table-bordered table-striped" >
                                    <tr >
                                        <td><label class="control-label" ><?php echo lang('status'); ?></label></td>

                                        <td style="width:130px;"><label class="control-label"><?php echo lang('date_changed'); ?></label></td>
                                        <td><label class="control-label"><?php echo lang('staff_comments'); ?></label></td>
                                        <td><label class="control-label"><?php echo lang('notes_to_customer'); ?></label></td>                            
                                        <td><label class="control-label"><?php echo lang('user'); ?></label></td>
                                    </tr>


                                    <?php
                                    foreach ($invoice_data as $history_invoice) {
                                        ?>
                                        <tr>
                                            <td class = "<?php
                                            if ($history_invoice['id_status'] == 1) {
                                                echo "estimates";
                                            } else if ($history_invoice['id_status'] == 2) {
                                                echo "sent";
                                            } else if ($history_invoice['id_status'] == 3) {
                                                echo "denied";
                                            } else if ($history_invoice['id_status'] == 4) {
                                                echo "paid";
                                            }
                                            ?>" style="text-align: center;">
                                                <?php
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

                                            <td><?php echo date("m/d/Y h:i:s", strtotime($history_invoice['date_changed'])); ?></td>
                                            <td><?php echo $history_invoice['staff_comments']; ?></td>
                                            <td><?php echo $history_invoice['notes_to_customer']; ?></td>
                                            <td><?php echo $history_invoice['user_name']; ?></td>

                                        </tr>

                                        <?php
                                    }
                                    ?>

                                </table>
                            </div>
                        </div>
                        
                        <div id="panel-quick-actions" class="panel panel-default" style="width:670px; margin-top:-15px;  height: auto; float:left;">
                            <div class="panel-heading" align="left">
                                <label for="ticket_content" ><?php echo "<b>Payment History</b>"; ?></label>
                            </div>

                            <div style="height:100px; overflow: auto;">


                                <table class = "table table-bordered table-striped" style="">
                                    <tr >
                                        <td><label class="control-label" ><?php echo "Payment method"; ?></label></td>

                                        <td style="width:130px;"><label class="control-label"><?php echo "Payment date"; ?></label></td>
                                        <td><label class="control-label"><?php echo "Cashiered amount"; ?></label></td>                           

                                    </tr>


                                    <?php
                                    foreach ($ip_payments as $history_payment) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $history_payment['payment_method_name'];
                                                ?></td>

                                            <td><?php echo date("m/d/Y", strtotime($history_payment['payment_date'])) . " " . $history_payment['payment_time']; ?></td>
                                            <td><?php echo "$" . $history_payment['payment_amount']; ?></td>



                                        </tr>

                                        <?php
                                    }
                                    ?>

                                </table>

                            </div>
                            
                        </div>
                        <?php $this->layout->load_view('invoices/partial_item_table'); ?>
                    </div>
                    
                    <div class="col-xs-12 col-sm-6">

                        <div class="invoice-properties">
                            <label><?php
                                echo lang('status');
                                ?>
                            </label>
                            <select name="invoice_status_id" id="invoice_status_id"
                                    class="form-control"
                                    <?php
                                    if ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) {
                                        echo 'disabled="disabled"';
                                    }
                                    ?>>
                                        <?php foreach ($invoice_statuses as $key => $status) { ?>
                                    <option value="<?php echo $key; ?>"
                                            <?php if ($key == $invoice->invoice_status_id) { ?>selected="selected"<?php } ?>>
                                                <?php
                                                if ($status['label'] == "Draft") {
                                                    echo "Estimates";
                                                } else if ($status['label'] == "Viewed") {
                                                    echo "Denied";
                                                } else {
                                                    echo $status['label'];
                                                }
                                                ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="invoice-properties">
                            <label><?php echo lang('payment_method'); ?></label>
                            <select name="payment_method" id="payment_method" class="form-control"
                            <?php
                            if ($invoice->is_read_only == 1 && $invoice->invoice_status_id == 4) {
                                echo 'disabled="disabled"';
                            }
                            ?>>
                                <option value="0"><?php echo lang('select_payment_method'); ?></option>
                                <?php foreach ($payment_methods as $payment_method) { ?>
                                    <option <?php if ($invoice->payment_method == $payment_method->payment_method_id) echo "selected" ?>
                                        value="<?php echo $payment_method->payment_method_id; ?>">
                                            <?php echo $payment_method->payment_method_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="invoice-properties">
                            <label class="control-label"><?php echo lang('staff_comments'); ?></label>
                            <textarea name="staff_comments" id="staff_comments" 
                                      class="input-sm form-control" rows="4"></textarea>
                        </div>

                        <div class="invoice-properties">
                            <label class="control-label"><?php echo lang('notes_to_customer'); ?></label>
                            <textarea name="notes_to_customer" id="notes_to_customer" 
                                      class="input-sm form-control" rows="4"></textarea>
                        </div>
                        

                    </div>
                    
                </div>
                
</div>



            <?php if ($invoice->is_read_only != 1 || $invoice->invoice_status_id != 4) {
                ?>
                <a href="#" class="btn btn-sm btn-success ajax-loader" id="btn_save_invoice_2" style="float:right; width:200px;">
                    <i class="fa fa-check"></i> <?php echo lang('save'); ?>
                </a>
            <?php } ?>
            <br><br>

            

            <hr/>

            <div class="row">
                <div class="col-xs-12 col-sm-4">

                    <label><?php echo lang('invoice_terms'); ?></label>
                    <textarea id="invoice_terms" name="invoice_terms" class="form-control" rows="3"
                    <?php
                    if ($invoice->is_read_only == 1) {
                        echo 'disabled="disabled"';
                    }
                    ?>
                              ><?php echo $invoice->invoice_terms; ?></textarea>

                </div>

                <div class="col-xs-12 col-sm-8">

                    <label class="control-label"><?php echo lang('attachments'); ?></label>
                    <br/>
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-default fileinput-button">
                        <i class="fa fa-plus"></i>
                        <span><?php echo lang('add_files'); ?></span>
                    </span>

                    <!-- dropzone -->
                    <div class="row">
                        <div id="actions" class="col-xs-12 col-sm-12">
                            <div class="col-lg-7"></div>
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

                            <div id="previews" class="table table-condensed table-striped files">
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

                                        <div class="progress progress-striped active" role="progressbar"
                                             aria-valuemin="0"
                                             aria-valuemax="100" aria-valuenow="0">
                                            <div class="progress-bar progress-bar-success" style="..."
                                                 data-dz-uploadprogress></div>
                                        </div>
                                    </div>
                                    <div class="pull-left btn-group">
                                        <button data-dz-download class="btn btn-sm btn-primary">
                                            <i class="fa fa-download"></i>
                                            <span><?php echo lang('download'); ?></span>
                                        </button>
                                        <?php if ($invoice->is_read_only != 1) { ?>
                                            <button data-dz-remove class="btn btn-danger btn-sm delete">
                                                <i class="fa fa-trash-o"></i>
                                                <span><?php echo lang('delete'); ?></span>
                                            </button>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- stop dropzone -->
                    </div>
                </div>
            </div>

            <?php if ($custom_fields): ?>
                <h4 class="no-margin"><?php echo lang('custom_fields'); ?></h4>
            <?php endif; ?>
            <?php foreach ($custom_fields as $custom_field) { ?>
                <label><?php echo $custom_field->custom_field_label; ?></label>
                <input type="text" class="form-control"
                       name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                       id="<?php echo $custom_field->custom_field_column; ?>"
                       value="<?php echo form_prep($this->mdl_invoices->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>"
                       <?php
                       if ($invoice->is_read_only == 1) {
                           echo 'disabled="disabled"';
                       }
                       ?>>
                   <?php } ?>


            <?php if ($invoice->invoice_status_id != 1) { ?>
                <p class="padded">
                    <?php echo lang('guest_url'); ?>:
                    <?php echo auto_link(site_url('guest/view/invoice/' . $invoice->invoice_url_key)); ?>
                </p>
            <?php } ?>

        </div>

    </form>

</div>
<script>
    function send_email(invoice_id)
    {
        $.post("<?php echo site_url('mailer/mailer/send_invoice_topay'); ?>", {
            invoice_id: invoice_id

        },
                function (data) {
                    if (data == 'success')
                    {
                        $("#erroremail").hide();
                        $("#successemail").show();
                    } else
                    {
                        $("#successemail").hide();
                        $("#erroremail").show();
                    }
                });
    }
    // Get the template HTML and remove it from the document
    var previewNode = document.querySelector("#template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var myDropzone = new Dropzone(document.body, {// Make the whole body a dropzone
        url: "<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>", // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        uploadMultiple: false,
        dictRemoveFileConfirmation: '<?php echo lang('delete_attachment_warning'); ?>',
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        init: function () {
            thisDropzone = this;
            $.getJSON("<?php echo site_url('upload/upload_file/' . $invoice->client_id . '/' . $invoice->invoice_url_key) ?>", function (data) {
                $.each(data, function (index, val) {
                    var mockFile = {fullname: val.fullname, size: val.size, name: val.name};
                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                    createDownloadButton(mockFile, '<?php echo base_url(); ?>uploads/customer_files/' + val.fullname);
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
        createDownloadButton(file, '<?php echo base_url() . 'uploads/customer_files/' . $invoice->invoice_url_key . '_' ?>' + file.name.replace(/\s+/g, '_'));
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
            url: "<?php echo site_url('upload/delete_file/' . $invoice->invoice_url_key) ?>",
            type: "POST",
            data: {'name': file.name.replace(/\s+/g, '_')}
        });
    });

    function createDownloadButton(file, fileUrl) {
        var downloadButtonList = file.previewElement.querySelectorAll("[data-dz-download]");
        for (_i = 0; _i < downloadButtonList.length; _i++) {
            downloadButtonList[_i].addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.open(fileUrl);
                return false;
            });
        }
    }
</script>
