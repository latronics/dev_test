
<script type="text/javascript">
    function set_amount()
    {
        $("#amount").val($('#payment_amount').val());


    }

    $(function () {
        $('#payment_method_id').change(function () {

            $.post("<?php echo site_url('payments/validate_method_type'); ?>", {
                method_payment_id: $('#payment_method_id').val()

            },
                    function (data) {

                        //alert(data);
                       if (data === "card")  {
                            //alert(data);
                            $("#btn-submit").hide();
                            $("#div_process_payment").show();
                        } else if (data === "cash") {
                            
                            $("#btn-submit").show();
                            $("#div_process_payment").hide();
                        }
                        else if (data === "check")
                        {
                            $("#btn-submit").hide();
                            $("#div_process_payment").show();
                        }
                        else if (data === "paypal")
                        {
                            $("#btn-submit").hide();
                            $("#div_process_payment").show();
                        }

                    });

        });



        $.post("<?php echo site_url('payments/validate_method_type'); ?>", {
            method_payment_id: $('#payment_method_id').val()

        },
                function (data) {

                    //alert(data);
                    if (data === "card")  {
                            //alert(data);
                            $("#btn-submit").hide();
                            $("#div_process_payment").show();
                        } else if (data === "cash") {
                            
                            $("#btn-submit").show();
                            $("#div_process_payment").hide();
                        }
                        else if (data === "check")
                        {
                            $("#btn-submit").hide();
                            $("#div_process_payment").show();
                        }
                        else if (data === "paypal")
                        {
                            $("#btn-submit").hide();
                            $("#div_process_payment").show();
                        }

                });

        $("#process_payment_form").submit(function (event) {

            $("#payment_type").val($('#payment_method_id').val());
            $("#note").val($("#payment_note").val());

            if ($('#invoice_id').val() === "") {
                $("#invoice_warning").show();
                event.preventDefault();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if ($("#amount").val() === "") {
                $("#amount_warning").show();
                event.preventDefault();
                setTimeout(function () {
                    location.reload();
                }, 2000);


            }





        });



        $('#invoice_id').focus();

        amounts = JSON.parse('<?php echo $amounts; ?>');
        invoice_payment_methods = JSON.parse('<?php echo $invoice_payment_methods; ?>');
        $('#invoice_id').change(function () {

            
            var invoice_identifier = "invoice" + $('#invoice_id').val();
            $('#payment_amount').val(amounts[invoice_identifier].replace("&nbsp;", " "));
            $("#amount").val(amounts[invoice_identifier].replace("&nbsp;", " "));
            $('#payment_method_id option[value="' + invoice_payment_methods[invoice_identifier] + '"]').prop('selected', true);
            $("#invoice_id_bluepay").val($('#invoice_id').val());


            if (invoice_payment_methods[invoice_identifier] != 0) {
                $('.payment-method-wrapper').append("<input type='hidden' name='payment_method_id' id='payment-method-id-hidden' class='hidden' value='" + invoice_payment_methods[invoice_identifier] + "'>");
                $('#payment_method_id').prop('disabled', true);
            } else {
                $('#payment-method-id-hidden').remove();
                $('#payment_method_id').prop('disabled', false);
            }
        });

    });
</script>
<form method="post" id ="process_payment_form" class="form-horizontal" action="bluepay">

    <input type="hidden" name="SHPF_FORM_ID" value="mobileform02D">
    <input type="hidden" name="SHPF_ACCOUNT_ID" value="100335725599">
    <input type="hidden" name="SHPF_TPS_DEF" value="SHPF_FORM_ID SHPF_ACCOUNT_ID DBA TAMPER_PROOF_SEAL AMEX_IMAGE DISCOVER_IMAGE TPS_DEF SHPF_TPS_DEF CUSTOM_HTML REBILLING REB_CYCLES REB_AMOUNT REB_EXPR REB_FIRST_DATE">
    <input type="hidden" name="SHPF_TPS" value="e731462c280f1689da736c7282f0175f">
    <input type="hidden" name="MODE" value="TEST">
    <input type="hidden" name="TRANSACTION_TYPE" value="SALE">

    <input type="hidden" name="DBA" value="DEMO-365laptoprepair">
    <input type="hidden" name="TAMPER_PROOF_SEAL" value="4f2bf41ee765a1f0a633621bed9077c4">
    <input type="hidden" name="REBILLING" value="0">
    <input type="hidden" name="REB_CYCLES" value="">
    <input type="hidden" name="REB_AMOUNT" value="">
    <input type="hidden" name="REB_EXPR" value="">
    <input type="hidden" name="REB_FIRST_DATE" value="">
    <input type="hidden" name="AMEX_IMAGE" value="amex.gif">
    <input type="hidden" name="DISCOVER_IMAGE" value="discvr.gif">
    <input type="hidden" name="REDIRECT_URL" value="https://secure.bluepay.com/interfaces/shpf?SHPF_FORM_ID=defaultres2&amp;SHPF_ACCOUNT_ID=100335725599&amp;SHPF_TPS_DEF=SHPF%5FACCOUNT%5FID%20SHPF%5FFORM%5FID%20RETURN%5FURL%20DBA%20AMEX%5FIMAGE%20DISCOVER%5FIMAGE%20SHPF%5FTPS%5FDEF&amp;SHPF_TPS=1c9269d23e744ee64d427f45629f7d6b&amp;RETURN_URL=&amp;DBA=DEMO%2D365laptoprepair&amp;AMEX_IMAGE=amex%2Egif&amp;DISCOVER_IMAGE=discvr%2Egif">
    <input type="hidden" name="TPS_DEF" value="MERCHANT APPROVED_URL DECLINED_URL MISSING_URL MODE TRANSACTION_TYPE TPS_DEF REBILLING REB_CYCLES REB_AMOUNT REB_EXPR REB_FIRST_DATE">
    <input type="hidden" name="CUSTOM_HTML" value="">
    <input type="hidden" name="CUSTOM_ID" value="">
    <input type="hidden" name="CUSTOM_ID2" value="">
    <input type="hidden" name="CARD_TYPES" value="vi-mc-di-am">
    <input type="text" id = "amount" name="AMOUNT" value="" hidden>
    <input type="text" id = "payment_type" name="payment_type" value="" hidden>
    <input type="text" id = "invoice_id_bluepay" name="invoice_id_bluepay" value="" hidden>
    <input type="text" id = "note" name="note" value="" hidden>



    <div id = "div_process_payment" hidden><input type="submit" class="btn-process btn  btn-sm" id ="process_payment"  value="Process Payment" style="  float:right; margin-left: 5px; margin-right: 20px; margin-top:8px; font-weight: bold; color:white;" /></div>
</form>

<form method="post" class="form-horizontal">


    <?php if ($payment_id) { ?>
        <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>">
    <?php } ?>

    <div id="headerbar">
        <h1><?php echo lang('payment_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>
        <div id = "amount_warning" class="alert alert-danger" hidden>Cashiered amount is required.</div>
        <div id = "invoice_warning" class="alert alert-danger" hidden>Invoice is required.</div>
        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="invoice_id" class="control-label"><?php echo lang('invoice'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <select name="invoice_id" id="invoice_id" class="form-control">
                    <?php if (!$payment_id) { ?>
                        <option value=""></option>
                        <?php foreach ($open_invoices as $invoice) { ?>
                            <option value="<?php echo $invoice->invoice_id; ?>"
                                    <?php if ($this->mdl_payments->form_value('invoice_id') == $invoice->invoice_id) { ?>selected="selected"<?php } ?>><?php echo $invoice->invoice_number . ' - ' . $invoice->client_name . ' - ' . format_currency($invoice->invoice_balance); ?></option>
                                <?php } ?>
                            <?php } else { ?>
                        <option
                            value="<?php echo $payment->invoice_id; ?>"><?php echo $payment->invoice_number . ' - ' . $payment->client_name . ' - ' . format_currency($payment->invoice_balance); ?></option>
                        <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_date" class="control-label"><?php echo lang('date'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="input-group">
                    <input name="payment_date" id="payment_date"
                           class="form-control datepicker"
                           value="<?php echo date_from_mysql($this->mdl_payments->form_value('payment_date')); ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group has-feedback">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_time" class="control-label"><?php echo "Payment time"; ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                
                    <input name="payment_time" id="payment_time"
                           class="form-control"
                           value="<?php echo $this->mdl_payments->form_value('payment_time'); ?>">
              
              
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_amount" class="control-label"><?php echo lang('amount'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <input type="text" name="payment_amount" id="payment_amount" class="form-control"
                       value="<?php echo format_amount($this->mdl_payments->form_value('payment_amount')); ?>" onkeyup="set_amount()">
            </div>

        </div>



        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_method_id" class="control-label">
                    <?php echo lang('payment_method'); ?>
                </label>
            </div>
            <div class="col-xs-12 col-sm-6 payment-method-wrapper">

                <?php
                // Add a hidden input field if a payment method was set to pass the disabled attribute
                if ($this->mdl_payments->form_value('payment_method_id')) {
                    ?>
                    <input type="hidden" name="payment_method_id" class="hidden"
                           value="<?php echo $this->mdl_payments->form_value('payment_method_id'); ?>">
                       <?php } ?>

                <select id="payment_method_id" name="payment_method_id" class="form-control"
                        <?php echo($this->mdl_payments->form_value('payment_method_id') ? 'disabled="disabled"' : ''); ?>>

                    <?php foreach ($payment_methods as $payment_method) { ?>
                        <option  value="<?php echo $payment_method->payment_method_id; ?>"
                                 <?php if ($this->mdl_payments->form_value('payment_method_id') == $payment_method->payment_method_id) { ?>selected="selected"<?php } ?>>
                                 <?php echo $payment_method->payment_method_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                <label for="payment_note" class="control-label"><?php echo lang('note'); ?></label>
            </div>
            <div class="col-xs-12 col-sm-6">
                <textarea name="payment_note" id ="payment_note"
                          class="form-control"><?php echo $this->mdl_payments->form_value('payment_note'); ?></textarea>
            </div>

        </div>

        <?php foreach ($custom_fields as $custom_field) { ?>
            <div class="form-group">
                <div class="col-xs-12 col-sm-2 text-right text-left-xs">
                    <label><?php echo $custom_field->custom_field_label; ?>: </label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input type="text" name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                           id="<?php echo $custom_field->custom_field_column; ?>"
                           class="form-control"
                           value="<?php echo form_prep($this->mdl_payments->form_value('custom[' . $custom_field->custom_field_column . ']')); ?>">
                </div>
            </div>
        <?php } ?>

    </div>

</form>


