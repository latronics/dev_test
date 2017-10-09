<script src="../../../../assets/default/jQuery-Mask-Plugin-master/src/jquery.mask.js"></script>
<style>
    .focoAtual {
        background: red;
    }
</style>

<script type="text/javascript">
    function hide_items()
    {
        if ($('#receive_items').is(":visible")) {
            $("#receive_items").hide();
        } else
        {
            $("#receive_items").show();
        }


    }
    function search_products(value)
    {
        $.post("<?php echo site_url('quotes/ajax/search_items'); ?>", {
            item_name_id: value


        },
                function (data) {
                    $("#receive_items").html(data);
                });
    }

    function Hide(el) {

        var display = document.getElementById(el).style.display;
        if (display === "none")
            document.getElementById(el).style.display = 'none';
        else
        {
            document.getElementById(el).style.display = 'none';
        }

    }
    function show_customer_info()
    {
        if ($('#address_1').is(':visible')) {
            $("#address_1").hide();
            $("#address_2").hide();
            $("#address_3").hide();
        } else
        {
            $("#address_1").show();
            $("#address_2").show();
            $("#address_3").show();
        }
    }
    function show_dealer()
    {
        if ($('#is_dealer').is(':checked'))
        {
            $("#company_info1").show();
            $("#company_info2").show();
        } else
        {
            $("#company_info1").hide();
            $("#company_info2").hide();
        }
    }
    function Show(el) {
        var display = document.getElementById(el).style.display;
        if (display === "none")
            document.getElementById(el).style.display = 'block';
        else
        {
            document.getElementById(el).style.display = 'block';
        }

    }
    function get_customer_info(client_name)
    {
       
               $.post("<?php echo site_url('quotes/ajax/load_clientdata'); ?>", {
                 client_name: client_name


                },
                        function (data) {
                            var result = JSON.parse(data);
                            $("#client_email").val(result.client_email);
                            $("#client_telephone").val(result.client_phone);
                            $("#client_address").val(result.client_address_1);
                            $("#client_city").val(result.client_city);
                            $("#client_state").val(result.client_state);
                            $("#client_zip").val(result.client_zip);
                            $("#client_country").val(result.client_country);
                        });
        
    }


    $(function () {
        var items_checked;
        var product_name;
        var product_description;
        var product_quantity;
        var product_price;
        var product_id;
        $('#create-quote').modal('show');
        $("#client_name_ac").focus();
        $("#serial_number").val("");
        $("#client_os_password").val("");

        $('#client_name_ac').autocomplete({
            source: '<?php echo site_url('quotes/ajax/customer_search'); ?>'
        });











        /*
         $("[name='client_name']").select2();*/


        // Creates the quote
        $('#quote_create_confirm').click(function () {
            store_valor = $(".store").serialize().replace('store=', '');

            console.log('clicked');
            if ($("#client_name_ac").val() == "")
            {
                $("#div_alert_client").show();
                setTimeout(function () {
                    $("#div_alert_client").hide();
                }, 3000);

            }
            // Posts the data to validate and create the quote;
            // will create the new client if necessary
            else if (store_valor == 1)
            {



                $(".store").focus();

                $("#div_alert_store").show();
                setTimeout(function () {
                    $("#div_alert_store").hide();
                }, 3000);


            } else
            {
                if ($("#validate").val() == 0) {
                    if ($("input[name='items_checked[]']:checked"))
                    {
                        product_id = $(".product_id").serializeArray();
                        items_checked = $(".items_checked").serializeArray();
                        product_name = $('.product_name').serializeArray();
                        product_description = $('.product_description').serializeArray();
                        product_quantity = $('.product_qtt').serializeArray();
                        product_price = $('.product_price').serializeArray();


                    }
                } else
                {
                    product_id = $(".product_id").serializeArray();
                    product_name = $('.product_name').serializeArray();
                    product_description = $('.product_description').serializeArray();
                    product_quantity = $('.product_qtt').serializeArray();
                    product_price = $('.product_price').serializeArray();
                }
                $.post("<?php echo site_url('quotes/ajax/create'); ?>", {
                    order_type: $("input[name='repair_type']:checked").val(),
                    client_name: $("[name='client_name']").val(),
                    quote_date_created: $('#quote_date_created').val(),
                    quote_password: $('#quote_password').val(),
                    user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                    invoice_group_id: $('#invoice_group_id').val(),
                    amount: $('#amount').val(),
                    brand: $('#brand').val(),
                    model: $('#model').val(),
                    serial_number: $('#serial_number').val(),
                    data_recovery: $('#data_recovery').val(),
                    client_os_password: '<?php echo $this->encrypt->encode('client_os_password'); ?>',
                    accessories_included: $('#accessories_included').val(),
                    problem_description_product: $('#problem_description_product').val(),
                    store: store_valor,
                    product_name: product_name,
                    product_description: product_description,
                    product_quantity: product_quantity,
                    product_price: product_price,
                    items_checked: items_checked,
                    product_id: product_id,
                    client_phone: $("#client_telephone").val(),
                    client_email: $("#client_email").val(),
                    company_name: $("#company_name").val(),
                    company_address: $("#company_address").val(),
                    company_phone: $("#company_phone").val(),
                    client_address: $("#client_address").val(),
                    client_city: $("#client_city").val(),
                    client_state: $("#client_state").val(),
                    client_zip: $("#client_zip").val(),
                    client_country: $("#client_country").val()






                },
                        function (data) {

<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                            var response = JSON.parse(data);
                            if (response.success == '1') {
                                // The validation was successful and quote was created
                                window.location = "<?php echo site_url('quotes/view'); ?>/" + response.quote_id;
                            } else {
                                // The validation was not successful
                                $('.control-group').removeClass('has-error');
                                for (var key in response.validation_errors) {
                                    $('#' + key).parent().parent().addClass('has-error');
                                }
                            }
                        });
            }
        });

    });
    $('#amount').mask("#.##0,00", {reverse: true});
    $("#client_telephone").mask('(000) 000-0000');
    $("#company_phone").mask('(000) 000-0000');
    $("#client_zip").mask('00000');
</script>
<?php
$this->db->select("*");
$this->db->join("ip_stores", "ip_stores.id = ip_users.user_store");
$this->db->where("ip_users.user_id", $this->session->userdata("user_id"));
$user_store_data = $this->db->get("ip_users")->result_array();
?>
</head>
<body>

    <div id="create-quote" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
         role="dialog" aria-labelledby="modal_create_quote" aria-hidden="true">

        <form class="modal-content">

            <div class="modal-header">

                <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

                <h3><?php echo lang('create_quote'); ?></h3>
            </div>
            <div class="modal-body">

                <div id="panel-quick-actions" class="panel panel-default" style="float:left; width:396px;">
                    <div class="panel-heading" align="left">
                        <b><?php echo "Customer Info"; ?></b><label style="float:right;"><a href="#" class="form-control" style="margin-top:-7px; margin-left:20px;" onclick="show_customer_info();"><img src="../../../../assets/default/img/list_info.png" style="width:25px; height: 25px; margin-top:-3px;"/> Full customer info</label></a>
                    </div><br>
                    <table align="center">
                        <tr><td colspan="2">
                                <div class="form-group">
                                    <div class="alert alert-danger" id="div_alert_client" hidden>Client name is required</div>
                                    <label for="client_name"><?php echo "Client Name"; ?></label><label style="float:right"><input type="checkbox" id="is_dealer" onclick="show_dealer();">Is Dealer</label>
                                    <?php
                                    $this->db->select("id_client_on_turn");
                                    $this->db->from("ip_client_to_ticket");
                                    $id_client = $this->db->get();
                                    $result_id_client = $id_client->result_array();

                                    if ($result_id_client[0]['id_client_on_turn'] != '') {

                                        $this->db->select("ip_clients.client_name, ip_clients.client_id");
                                        $this->db->from("ip_clients");
                                        $this->db->join("ip_client_to_ticket", 'ip_client_to_ticket.id_client_on_turn = ip_clients.client_id', 'inner');
                                        $this->db->where("ip_client_to_ticket.id_client_on_turn", $result_id_client[0]['id_client_on_turn']);
                                        $name_client = $this->db->get();
                                        $result_name_client = $name_client->result_array();
                                    }
                                    ?>

                                    <input name="client_name" id="client_name_ac"  class="pac-container form-control" value="<?php
                                    if ($result_name_client != null) {
                                        echo $result_name_client[0]['client_name'];
                                    }
                                    ?>" onblur="get_customer_info(this.value)">
                                           <?php
                                           $this->db->where("id_client_on_turn <> ''");
                                           $this->db->delete("ip_client_to_ticket");
                                           ?>

                                </div>
                            </td></tr>
                        <tr><td>
                                <div class="form-group" >
                                    <label for="client_email">Email</label>
                                    <input type="text" name="client_email" style="width:175px;" id="client_email" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">

                                </div>
                            </td><td style="padding-left:5px;">
                                <div class="form-group">
                                    <label for="client_telephone">Telephone</label>
                                    <input type="text" name="client_telephone" style="width:175px;" id="client_telephone" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td></tr>
                        <tr id="company_info1" hidden><td>
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" name="company_name" style="width:175px;" id="company_name" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td><td style="padding-left:5px;">
                                <div class="form-group">
                                    <label for="company_address">Company Address</label>
                                    <input type="text" name="company_address" style="width:175px;" id="company_address" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td></tr>
                        <tr id="company_info2" hidden><td colspan="2">
                                <div class="form-group">
                                    <label for="company_phone">Company Phone</label>
                                    <input type="text" name="company_phone"  id="company_phone" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td></tr>

                        <tr id="address_1" hidden><td>
                                <div class="form-group">
                                    <label for="client_address">Address</label>
                                    <input type="text" name="client_address" style="width:175px;" id="client_address" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td>
                            <td>
                                <div class="form-group" style="padding-left:5px;">
                                    <label for="client_city">City</label>
                                    <input type="text" name="client_city" style="width:175px; " id="client_city" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td>
                        </tr>
                        <tr id="address_2" hidden><td>
                                <div class="form-group">
                                    <label for="client_state">State</label>
                                    <input type="text" name="client_state" style="width:175px; padding-left:5px;" id="client_state" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td>
                            <td>
                                <div class="form-group" style="padding-left:5px;">
                                    <label for="client_zip">Zipcode</label>
                                    <input type="text" name="client_zip" style="width:175px; padding-left:5px;" id="client_zip" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td>
                        </tr>
                        <tr id="address_3" hidden><td colspan="2">
                                <div class="form-group">
                                    <label for="client_country">Country</label>
                                    <select name="client_country" id="client_country" class="form-control">
                                        <option></option>
                                        <?php foreach ($countries as $cldr => $country) { ?>
                                            <option value="<?php echo $cldr; ?>"
                                            <?php
                                            if ($country == 'United States') {
                                                echo 'selected="selected"';
                                            }
                                            ?>
                                                    ><?php echo $country ?></option>
                                                <?php } ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="panel-quick-actions" class="panel panel-default" style="width:396px; float:left; margin-left:10px;">
                    <div class="panel-heading" align="left">
                        <b><?php echo "Order Info"; ?></b>
                    </div>
                    <br>
                    <table align="center">
                        <tr>
                            <td colspan="2">
                                <div class="form-group">
                                    <label for="brand" ><?php echo lang('brand'); ?></label>
                                    <input type="text" name="brand" id="brand" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div>
                            </td><tr><td>
                                <div class="form-group">
                                    <label for="model"><?php echo lang('model'); ?></label>
                                    <input type="text" name="model" id="model" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div></td><td>
                                <div class="form-group" style="padding-left:5px;">
                                    <label for="serial_number"><?php echo lang('serial_number'); ?></label>
                                    <input type="text" name="serial_number" id="serial_number" class="form-control"
                                           value=""  style="margin: 0 auto;" autocomplete="off">
                                </div></td></tr><tr><td>
                                <div class="form-group">
                                    <label for="data_recovery"><?php echo lang('data_recovery'); ?></label>
                                    <select name="data_recovery" id="data_recovery" class="form-control"
                                            value="" style="margin: 0 auto;" autocomplete="off"><option id = "yes" name = "yes">Yes</option><option id = "no" name = "no" selected="selected">No</option></select>
                                </div></td><td style="padding-left:5px;">
                                <div class="form-group">
                                    <label for="client_os_password"><?php echo lang('client_os_password'); ?></label>
                                    <input type="password" name="client_os_password" id="client_os_password" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off">
                                </div></td></tr><tr><td colspan="2">
                                <div class="form-group">
                                    <label for="accessories_included"><?php echo lang('accessories_included'); ?></label>
                                    <textarea name="accessories_included" id="accessories_included" class="form-control"
                                              value="" style="margin: 0 auto;" autocomplete="off"></textarea>
                                </div></td></tr><tr><td colspan="2">
                                <div class="form-group">
                                    <label for="problem_description_product"><?php echo lang('problem_description_product'); ?></label>
                                    <textarea name="problem_description_product" id="problem_description_product" class="form-control"
                                              value="" style="margin: 0 auto;" autocomplete="off"></textarea>
                                </div>
                            </td></tr>
                        <tr><td colspan="2">
                                <div class="form-group has-feedback">
                                    <label for="quote_date_created">
                                        <?php echo lang('quote_date'); ?>
                                    </label>

                                    <div class="input-group">
                                        <input name="quote_date_created" id="quote_date_created" 
                                               class="form-control datepicker"
                                               value="<?php echo date(date_format_setting()); ?>">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                            </td></tr>

                        <tr><td colspan="2">
                                <div class="form-group" style="float:left;">
                                    <div class="alert alert-danger" id="div_alert_store" hidden>Please select a store(General is not a store)</div>
                                    <label for="store" ><?php echo lang('store'); ?></label>
                                    <select name="store" id="store" class="store form-control" value="" style="margin: 0 auto; width:300px; " autocomplete="off">
                                        <?php
                                        $this->db->select("*");
                                        $this->db->order_by("default", "DESC");
                                        $data_stores = $this->db->get("ip_stores")->result_array();




                                        foreach ($data_stores as $data_stores) {
                                            $this->db->select("*");
                                            $this->db->where("user_id", $this->session->userdata('user_id'));
                                            $store = $this->db->get("ip_users")->result_array();
                                            ?>

                                            <?php if ($store[0]['user_store'] == 1) { ?> <option id = "<?php echo $data_stores['id'] ?>" name = "store[]" value = "<?php echo $data_stores['id'] ?>" <?php
                                                if ($store == 'Hawthorne') {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $data_stores['store_name']; ?></option><?php ?>
                                            <?php } else if ($store[0]['user_store'] == $data_stores['id']) { ?> <option id = "<?php echo $data_stores['id'] ?>" name = "store[]" value = "<?php echo $data_stores['id'] ?>" <?php
                                                if ($store[0]['user_store'] == $data_stores['id']) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $data_stores['store_name']; ?></option><?php } ?>
                                                <?php } ?>
                                    </select>
                                </div>
                            </td></tr>

                    </table>

                </div>
                <div id="panel-quick-actions" class="panel panel-default" style="width:397px; float:left; margin-left:10px;">
                    <div class="panel-heading" align="left">
                        <b><?php echo "Items Info"; ?></b>
                    </div>
                    <br>
                    <table align="center">
                        <tr><td>
                                <div class="form-group">
                                    <label for="amount" ><?php echo lang('amount'); ?></label>
                                    <input type="text" name="amount" id="amount" class="form-control"
                                           value="" style="margin: 0 auto;" autocomplete="off" >
                                </div>
                            </td></tr>
                        <tr><td>
                                <div class="form-group">
                                    <label for="items"><?php echo "Items Filter"; ?></label>
                                    <input type="text" id="items_text" class="form-control" placeholder="Type the item name here" onkeyup="search_products(this.value)" style="width:380px;">
                                    <div id="receive_items" style="width:380px; height: 360px; margin-top:10px; overflow: auto;" hidden>
                                        <table>
            <?php while ($i <= 10) { ?>
                <tr>
                    <td style="font-weight: bold;">
                        Product Name
                    </td>
                    <!--<td style="padding-left:10px; font-weight: bold;">
                        Product Description
                    </td>-->
                    <td style="padding-left:10px; font-weight: bold;">
                        Quantity
                    </td>
                    <td style="padding-left:10px; font-weight: bold;">
                        Product Price
                    </td>
                </tr>
                <tr><input type ="text" class="product_id" name="product_id[]" value="<?php echo $products->product_id; ?>" hidden>
                <td>
                    <input type="text" class="product_name form-control" name="product_name[]" value="" style="width:180px;">
                </td>
                <!--<td style="padding-left:10px;">
                    <input type="text" class="product_description form-control" name="product_description[]" value="" style="width:300px;">
                </td>-->
                <td style="padding-left:10px;">
                    <input type="number" class="product_qtt form-control" name="product_qtt[]" value="1" style="width:60px;">
                </td>
                <td style="padding-left:10px;">
                    <input type="text" class="product_price form-control" name="product_price[]" value="" >
                </td>
                <input type="text" name="validate" id="validate" value="1" hidden>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
                                        
                                    </div>
                                </div>
                                <div class="btn-group">

                                    <input type="button" id="show_hide_items" class="btn" onclick="hide_items();" value="Show/Hide Products" style="margin-bottom:10px; float:left;">

                                </div>
                                <div class="form-group" hidden>
                                    <label for="invoice_group_id"><?php echo lang('invoice_group'); ?>: </label>

                                    <div class="controls"> 
                                        <select name="invoice_group_id" id="invoice_group_id"
                                                class="form-control">
                                            <option value=""></option>
                                            <?php foreach ($invoice_groups as $invoice_group) { ?>
                                                <option value="<?php
                                                if ($user_store_data['id'] == 1) {
                                                    $this->db->select("invoice_group_id");
                                                    $this->db->where("invoice_group_name", "Default");
                                                    $invoice_group_name = $this->db->get("ip_invoice_groups")->result_array();
                                                    echo $invoice_group_name[0]['invoice_group_id'];
                                                } else {
                                                    echo $invoice_group->invoice_group_id;
                                                }
                                                ?>"
                                                        <?php if ($this->mdl_settings->setting('default_quote_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php
                                                        } else if ($user_store_data[0]['store_name'] == $invoice_group->invoice_group_name) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $invoice_group->invoice_group_name; ?></option>
                                                    <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                    </table>

                </div>
                <div class="modal-footer" style="border-top:0px; margin-top:650px;">
                    <div class="btn-group">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">
                            <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                        </button>
                        <button class="btn btn-success ajax-loader" id="quote_create_confirm" type="button">
                            <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                        </button>
                    </div>
                </div>

        </form>

    </div>
