<script type="text/javascript">
    var client_name;
    var validate = 0;
    function validate_store()
    {
        if ($("#store").val() == 1)
        {
            $("#store_alert").show();
        } else
        {
            $("#store_alert").hide();
        }
    }
    function set_walking_customer()
    {
        if ($("#walking").is(":checked"))
        {
            $.post("<?php echo site_url('invoices/ajax/get_walking'); ?>", {
            },
                    function (data) {

                        client_name = data;
                        validate = 1;
                        $("#show_client").hide();
                    });
        } else
        {
            $.post("<?php echo site_url('invoices/ajax/remove_walking'); ?>", {
            },
                    function (data) {

                        client_name = $('#client_name').val();
                        validate = 0;
                        $("#show_client").show();
                    });

        }
    }

    function hide_password()
    {
        $("#hide_password").hide();
        $("#pdf_password").hide();
        $("#show_password").show();
    }
    function show_password()
    {
        $("#pdf_password").show();
        $("#show_password").hide();
        $("#hide_password").show();
    }
    $(function () {
        $('#client_name_ac').autocomplete({
            source: '<?php echo site_url('invoices/ajax/customer_search'); ?>'
        });
        // Display the create invoice modal
        $('#create-invoice').modal('show');

        $("#client_name_ac").focus();


        // Creates the invoice
        $('#invoice_create_confirm').click(function () {


            if (validate === 0)
            {
                client_name = $('#client_name_ac').val();

            }



            // Posts the data to validate and create the invoice;
            // will create the new client if necessar
            $.post("<?php echo site_url('invoices/ajax/create'); ?>", {
                client_name: client_name,
                invoice_date_created: $('#invoice_date_created').val(),
                invoice_group_id: $('#invoice_group_id').val(),
                invoice_time_created: '<?php echo date('H:i:s') ?>',
                invoice_password: $('#invoice_password').val(),
                user_id: '<?php echo $this->session->userdata('user_id'); ?>',
                payment_method: $('#payment_method_id').val(),
                store: $("#store").val()
            },
                    function (data) {
<?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                        var response = JSON.parse(data);
                        if (response.success == '1') {
                            // The validation was successful and invoice was created
                            window.location = "<?php echo site_url('invoices/view'); ?>/" + response.invoice_id;
                        } else {
                            // The validation was not successful
                            $('.control-group').removeClass('has-error');
                            for (var key in response.validation_errors) {
                                $('#' + key).parent().parent().addClass('has-error');
                            }
                        }
                    });
        });
    });

</script>

<?php
$this->db->select("*");
$this->db->join("ip_stores", "ip_stores.id = ip_users.user_store");
$this->db->where("ip_users.user_id", $this->session->userdata("user_id"));
$store_users_data = $this->db->get("ip_users")->result_array();
//GET CLIENT WALKING
$this->db->select("*");
$this->db->where("user_id", $this->session->userdata("user_id"));
$walking_data = $this->db->get("ip_client_walking_aux")->result_array();

if ($walking_data[0]['walking'] == 1) {
    $this->db->select("*");
    $this->db->where("walking", "1");
    $client_name_walking = $this->db->get("ip_clients")->result_array();
}
?>
<div id="create-invoice" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo lang('create_invoice'); ?></h3>
        </div>
        <div class="modal-body">

            <input class="hidden" id="payment_method_id"
                   value="<?php echo $this->mdl_settings->setting('invoice_default_payment_method'); ?>">
            <div class="form-group">
                <label for="walking_customer"><?php echo "Walking Customer: "; ?></label>
                <input type="checkbox" id ="walking" name="walking" onclick="set_walking_customer()"/>

            </div>

            <div class="form-group" id="show_client">
                <label for="client_name"><?php echo lang('client'); ?></label>
                <input name="client_name" id="client_name_ac" class="form-control">


            </div>

            <div class="form-group has-feedback">
                <label><?php echo lang('invoice_date'); ?></label>

                <div class="input-group">
                    <input name="invoice_date_created" id="invoice_date_created"
                           class="form-control datepicker"
                           value="<?php echo date(date_format_setting()); ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>
            <div id="store_alert" class="alert alert-danger" hidden>Store required</div>
            <div class="form-group has-feedback">
                <label><?php echo 'Store'; ?></label>
                <div>
                    <select class="form-control" name="store" id="store">
                        <?php foreach ($stores as $stores) { ?>
                            <option value="<?php echo $stores->id; ?>"  <?php if($stores->store_name == "Hawthorne"){ echo "selected"; } if ($this->session->userdata("user_store") == $stores->store_id) {
                            echo "selected";
                        } ?>><?php echo $stores->store_name; ?></option>
<?php } ?>
                    </select>
                </div>
            </div>
            <div id = "show_password" align="center"><a href ="#" id="set_password" onclick="show_password()">Show Password</a></div>
            <div id ="hide_password" align="center" hidden><a href ="#" id="unset_password" onclick="hide_password()">Hide Password</a></div>
            <div class="form-group" id="pdf_password" hidden>
                <label for="invoice_password"><?php echo lang('invoice_password'); ?></label>
                <input type="text" name="invoice_password" id="invoice_password" class="form-control"
                       value="<?php
                       if ($this->mdl_settings->setting('invoice_pre_password') == '') {
                           echo '';
                       } else {
                           echo $this->mdl_settings->setting('invoice_pre_password');
                       }
                       ?>" style="margin: 0 auto;" autocomplete="off">
            </div>

            <div class="form-group" hidden>
                <label><?php echo lang('invoice_group'); ?></label>

                <div class="controls">
                    <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                        <option value=""></option>
                                <?php foreach ($invoice_groups as $invoice_group) { ?>
                            <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                                    <?php if ($this->mdl_settings->setting('default_invoice_group') == $invoice_group->invoice_group_id) { ?>selected="selected"<?php
                                    } else {
                                        if ($store_users_data[0]['store_name'] == $invoice_group->invoice_group_name) {
                                            echo "selected";
                                        }
                                    }
                                    ?>><?php echo $invoice_group->invoice_group_name; ?></option>
<?php } ?>
                    </select>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php echo lang('cancel'); ?>
                </button>
                <button class="btn btn-success ajax-loader" id="invoice_create_confirm" type="button" onclick="validate_store();">
                    <i class="fa fa-check"></i> <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
