<script type="text/javascript">
    function set_walking()
    {
        var walking;
        if ($('#walking_customer').is(":checked"))
        {
            walking = 1;
        } else
        {
            walking = 0;
        }

        $.post("<?php echo site_url('clients/ajax/send_walking'); ?>", {
            walking: walking,
            client_id: <?php echo $this->mdl_clients->form_value('client_id'); ?>
        },
                function (data) {
                    if (data !== "")
                    {
                        $('#div_error_walking').show();
                        setTimeout(function () {
                            $('#div_error_walking').hide();
                        }, 4000);

                        $('#walking_customer').prop('checked', false);
                    }

                });
    }
    $(function () {
        $('#client_name').focus();
        $("#client_country").select2({
            placeholder: "<?php echo lang('country'); ?>",
            allowClear: true
        });
    });

    function ShowHide(el) {
        var display = document.getElementById(el).style.display;
        if (display === "block")
            document.getElementById(el).style.display = 'none';
        else
            document.getElementById(el).style.display = 'block';
    }
</script>
<?php
$this->db->select("*");
$this->db->where("client_id", $this->mdl_clients->form_value('client_id'));
$check_walking = $this->db->get("ip_clients")->result_array();


$this->db->select("*");
$this->db->where("walking","1");
$client_walker_customer = $this->db->get("ip_clients")->result_array();
?>
<form method="post">

    <div id="headerbar">
        <h1><?php echo lang('client_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
        <?php
        if ($this->mdl_clients->form_value('is_update')) {
            echo 'value="1"';
        } else {
            echo 'value="0"';
        }
        ?>
               >

        <fieldset>
            <legend><?php echo lang('personal_information'); ?></legend>
            <div><label><?php echo lang('client_name'); ?>: </label>
                <div class="input-group col-xs-6">

                    <span class="input-group-addon">
                        <?php echo lang('active_client'); ?>: 
                        <input id="client_active" name="client_active" type="checkbox" value="1"
                        <?php
                        if ($this->mdl_clients->form_value('client_active') == 1
                                or ! is_numeric($this->mdl_clients->form_value('client_active'))
                        ) {
                            echo 'checked="checked"';
                        }
                        ?>
                               >
                    </span>
                    

                    <input id="client_name" name="client_name" type="text" class="form-control"
                           placeholder="<?php echo lang('client_name'); ?>"
                           value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_name')); ?>">
                </div>
                <br>
                <div><label><?php echo 'Company Name'; ?>: </label></div>
                <div class="input-group col-xs-6">
                    <input id="client_company" name="client_company" type="text" class="form-control" style="border-radius:4px;"
                           placeholder="Client Company"
                           value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_company')); ?>">
                </div>
                <div><label><?php echo 'Company Address'; ?>: </label></div>
                <div class="input-group col-xs-6">
                    <input id="company_address" name="company_address" type="text" class="form-control" style="border-radius:4px;"
                           placeholder="Company Address"
                           value="<?php echo htmlspecialchars($this->mdl_clients->form_value('company_address')); ?>">
                </div>
                 <div><label><?php echo 'Company Phone'; ?>: </label></div>
                <div class="input-group col-xs-6">
                    <input id="company_phone" name="company_phone" type="text" class="form-control" style="border-radius:4px;"
                           placeholder="Company Phone"
                           value="<?php echo htmlspecialchars($this->mdl_clients->form_value('company_phone')); ?>">
                </div>
                <div id="div_error_walking" hidden style="padding-top:10px;"><label class="alert alert-danger" style="width:880px;">You can't have two walking customers!<br><b>Walking Customer Name</b>: <?php echo $client_walker_customer[0]['client_name']; ?></label></div>
                <div class="input-group col-xs-6" style="padding-top:10px;" ><table><tr><td>Walking Customer</td> 
                            <td style="padding-left:10px;"><input class="" type ="checkbox" id="walking_customer" <?php if ($check_walking[0]['walking'] == 1) {
                            echo "checked";
                            } ?> onclick="set_walking()" align="left"></td></tr></table></div>
                
                    <div class="input-group col-xs-6" style="padding-top:10px; padding-left:10px;">
                        </div>
                    
        </fieldset>

        <div class="row">
            <div class="col-xs-12 col-sm"><button class="btn_add_row btn btn-sm btn-default" type="button" onclick="ShowHide('address')">Show/Hide Address</button>
                <button class="btn_add_row btn btn-sm btn-default" type="button" onclick="ShowHide('tax_information')">Show/Hide Tax</button>
            </div>
            <div id = "address" class="col-xs-12 col-sm-6" hidden>

                <fieldset>
                    <legend><?php echo 'Billing Address'; ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('street_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_address_1" id="client_address_1" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_address_1')); ?>" placeholder="<?php echo lang('street_address'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('street_address_2'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_address_2" id="client_address_2" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_address_2')); ?>" placeholder="<?php echo lang('street_address_2'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('city'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_city" id="client_city" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_city')); ?>" placeholder="<?php echo lang('city'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('state'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_state" id="client_state" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_state')); ?>" placeholder="<?php echo lang('state'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('zip_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_zip" id="client_zip" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_zip')); ?>" placeholder="<?php echo lang('zip_code'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('country'); ?>: </label>

                        <div class="controls">
                            <select name="client_country" id="client_country" class="form-control">
                                <option></option>
                                        <?php foreach ($countries as $cldr => $country) { ?>
                                    <option value="<?php echo $cldr; ?>"
    <?php
    if ($selected_country == $cldr) {
        echo 'selected="selected"';
    }
    ?>
                                            ><?php echo $country ?></option>
<?php } ?>
                            </select>
                        </div>
                    </div>
              
                    <legend><?php echo 'Shipping Address'; ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('street_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="shipping_address" id="shipping_address" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('shipping_address')); ?>" placeholder="<?php echo lang('street_address'); ?>">
                        </div>
                    </div>

                    

                    <div class="form-group">
                        <label><?php echo lang('city'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="shipping_city" id="shipping_city" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('shipping_city')); ?>" placeholder="<?php echo lang('city'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('state'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="shipping_state" id="shipping_state" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('shipping_state')); ?>" placeholder="<?php echo lang('state'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('zip_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="shipping_zip" id="shipping_zip" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('shipping_zip')); ?>" placeholder="<?php echo lang('zip_code'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('country'); ?>: </label>

                        <div class="controls">
                            <select name="shipping_country" id="shipping_country" class="form-control">
                                <option></option>
                                        <?php foreach ($countries as $cldr => $country) { ?>
                                    <option value="<?php echo $cldr; ?>"
    <?php
    if ($selected_country == $cldr) {
        echo 'selected="selected"';
    }
    ?>
                                            ><?php echo $country ?></option>
<?php } ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="col-xs-12 col-sm-6">
                <fieldset>

                    <legend><?php echo lang('contact_information'); ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('phone_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_phone" id="client_phone" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_phone')); ?>" placeholder="<?php echo lang('phone_number'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('fax_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_fax" id="client_fax" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_fax')); ?>" placeholder="<?php echo lang('fax_number'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('mobile_number'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_mobile" id="client_mobile" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_mobile')); ?>" placeholder="<?php echo lang('mobile_number'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('email_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_email" id="client_email" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_email')); ?>" placeholder="<?php echo lang('email_address'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('web_address'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_web" id="client_web" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_web')); ?>" placeholder="<?php echo lang('web_address'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

            <div id ="tax_information" class="col-xs-12 col-sm-6" hidden>
                <fieldset>

                    <legend><?php echo lang('tax_information'); ?></legend>

                    <div class="form-group">
                        <label><?php echo lang('vat_id'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_vat_id" id="client_vat_id" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_vat_id')); ?>" placeholder="<?php echo lang('vat_id'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo lang('tax_code'); ?>: </label>

                        <div class="controls">
                            <input type="text" name="client_tax_code" id="client_tax_code" class="form-control"
                                   value="<?php echo htmlspecialchars($this->mdl_clients->form_value('client_tax_code')); ?>" placeholder="<?php echo lang('tax_code'); ?>">
                        </div>
                    </div>

                </fieldset>
            </div>

        </div>

<?php if ($custom_fields) { ?>
            <div class="row">
                <div class="col-xs-12">
                    <fieldset>
                        <legend><?php echo lang('custom_fields'); ?></legend>
    <?php foreach ($custom_fields as $custom_field) { ?>
                            <div class="form-group">
                                <label><?php echo $custom_field->custom_field_label; ?>: </label>

                                <div class="controls">
                                    <input type="text" class="form-control"
                                           name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                           id="<?php echo $custom_field->custom_field_column; ?>"
                                           value="<?php echo form_prep($this->mdl_clients->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                                </div>
                            </div>
    <?php } ?>
                    </fieldset>
                </div>
            </div>
<?php } ?>
    </div>
</form>
