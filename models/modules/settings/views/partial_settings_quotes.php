<?php
$this->db->select("*");

$status = $this->db->get("status")->result_array();

$this->db->select("*");
$this->db->where("setting_key", "show_status_ticket");
$status_ipsettings = $this->db->get("ip_settings")->result_array();

$get_status = str_replace("show_status_ticket[]", "", $status_ipsettings[0]['setting_value']);
$get_status2 = str_replace("=", "", $get_status);
$get_status3 = str_replace("+", " ", $get_status2);
$status_array = explode("&", $get_status3);

$get_days = str_replace("show_status_text[]", "", $status_ipsettings[0]['days_urgent']);
$get_days2 = str_replace("=", "", $get_days);
$get_days3 = str_replace("+", " ", $get_days2);
$days_array = explode("&", $get_days3);
//print_r($days_array);

?>
<div class="tab-info">

    <div class="row">
        <div class="col-xs-12 col-md-6">

            <h4><?php echo lang('general_settings'); ?></h4>
            <br/>

            <div class="form-group">
                <label for="settings[quotes_expire_after]" class="control-label">
                    <?php echo lang('quotes_expire_after'); ?>
                </label>
                <input type="text" name="settings[quotes_expire_after]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('quotes_expire_after'); ?>">
            </div>

            <div class="form-group">
                <label for="settings[default_quote_group]" class="control-label">
                    <?php echo lang('default_quote_group'); ?>
                </label>
                <select name="settings[default_quote_group]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?php echo $invoice_group->invoice_group_id; ?>"
                        <?php
                        if ($this->mdl_settings->setting('default_quote_group') == $invoice_group->invoice_group_id) {
                            echo 'selected="selected"';
                        }
                        ?>>
                                    <?php echo $invoice_group->invoice_group_name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[mark_quotes_sent_pdf]" class="control-label">
                    <?php echo lang('mark_quotes_sent_pdf'); ?>
                </label>
                <select name="settings[mark_quotes_sent_pdf]" class="input-sm form-control">
                    <option value="0"
                    <?php
                    if (!$this->mdl_settings->setting('mark_quotes_sent_pdf')) {
                        echo 'selected="selected"';
                    }
                    ?>>
                                <?php echo lang('no'); ?>
                    </option>
                    <option value="1"
                    <?php
                    if ($this->mdl_settings->setting('mark_quotes_sent_pdf')) {
                        echo 'selected="selected"';
                    }
                    ?>>
                                <?php echo lang('yes'); ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[quote_pre_password]" class="control-label">
                    <?php echo lang('quote_pre_password'); ?>
                </label>
                <input type="text" name="settings[quote_pre_password]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('quote_pre_password'); ?>">
            </div>

            <div class="form-group">
                <label for="settings[default_quote_notes]">
                    <?php echo lang('default_notes'); ?>
                </label>
                <textarea name="settings[default_quote_notes]" rows="3" class="input-sm form-control"
                          ><?php echo $this->mdl_settings->setting('default_quote_notes'); ?></textarea>
            </div>

            <div class="form-group">
                <label for="settings[generate_quote_number_for_draft]" class="control-label">
                    <?php echo lang('generate_quote_number_for_draft'); ?>
                </label>
                <select name="settings[generate_quote_number_for_draft]" class="input-sm form-control">
                    <option value="0"
                    <?php
                    if (!$this->mdl_settings->setting('generate_quote_number_for_draft')) {
                        echo 'selected="selected"';
                    }
                    ?>>
                                <?php echo lang('no'); ?>
                    </option>
                    <option value="1"
                    <?php
                    if ($this->mdl_settings->setting('generate_quote_number_for_draft')) {
                        echo 'selected="selected"';
                    }
                    ?>>
                                <?php echo lang('yes'); ?>
                    </option>
                </select>
            </div>

        </div>
        <div class="col-xs-12 col-md-6">

            <h4><?php echo lang('quote_template'); ?></h4>
            <br/>

            <div class="form-group">
                <label for="settings[pdf_quote_template]" class="control-label">
                    <?php echo lang('default_pdf_template'); ?>
                </label>
                <select name="settings[pdf_quote_template]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($pdf_quote_templates as $quote_template) { ?>
                        <option value="<?php echo $quote_template; ?>"
                        <?php
                        if ($this->mdl_settings->setting('pdf_quote_template') == $quote_template) {
                            echo 'selected="selected"';
                        }
                        ?>><?php echo $quote_template; ?></option>
                            <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="settings[public_quote_template]" class="control-label">
                    <?php echo lang('default_public_template'); ?>
                </label>
                <select name="settings[public_quote_template]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($public_quote_templates as $quote_template) { ?>
                        <option value="<?php echo $quote_template; ?>"
                        <?php
                        if ($this->mdl_settings->setting('public_quote_template') == $quote_template) {
                            echo 'selected="selected"';
                        }
                        ?>><?php echo $quote_template; ?></option>
                            <?php } ?>
                </select>
                
                
                <br><label class="control-label">
                    <?php echo lang('days_ticket'); ?>
                </label>
                <input type="text" name="settings[days_ticket]" class="input-sm form-control"
                       value="<?php echo $this->mdl_settings->setting('days_ticket'); ?>">
                <br><label class="control-label">
                    <?php echo lang('show_status_ticket') . " / Days to turn urgent"; ?>
                </label>
                
                <table border="0">
                    <?php
                    $count = 0;
                    $count2 = 0;
                    foreach ($status as $status) {
                        ?>
                        <tr><td><input type="checkbox" name="show_status_ticket[]"  
                                       value="<?php echo $status['status']; ?>" class ="mycheckbox" onclick="mycheckbox()" <?php
                                       if ($status_array[$count] != "") {

                                           if ($status_array[$count] == $status['status']) {
                                               $count++;
                                               echo 'checked="checked"';
                                           }
                                       }
                                       ?>></td><td style="padding-left:10px; padding-right:10px;"><?php echo $status['status']; ?>
                            </td>
                            <td style="padding-bottom: 5px;"><input type = "text" name = show_status_text[] class ="mytextbox input-sm form-control"  onkeyup="postcheckbox()" value ="<?php echo $status['days_urgent'] ?>"/><?php } ?></td></tr></table>

                <p align="center"><a id = "mark" style="cursor:pointer;" >Mark/Unmark All</a></p><br><br>
            </div>

            <div class="form-group">
                <label for="settings[email_quote_template]" class="control-label">
                    <?php echo lang('default_email_template'); ?>
                </label>
                <select name="settings[email_quote_template]" class="input-sm form-control">
                    <option value=""></option>
                    <?php foreach ($email_templates_quote as $email_template) { ?>
                        <option value="<?php echo $email_template->email_template_id; ?>"
                        <?php
                        if ($this->mdl_settings->setting('email_quote_template') == $email_template->email_template_id) {
                            echo 'selected="selected"';
                        }
                        ?>><?php echo $email_template->email_template_title; ?></option>
                            <?php } ?>
                </select>
            </div>

        </div>
    </div>

</div>
<script>


    function postcheckbox()
    {
        var dataString = $('.mycheckbox').serialize();

        var dataString2 = $('.mytextbox').serialize();


        //var datacomplete = dataString + dataString2;
//alert(dataString); 
        $.post("<?php echo site_url('settings/ajax/get_cron_key'); ?>", {
            checkbox: dataString,
            textbox: dataString2


        }, function (data) {
            //alert(data);
        });
    }

    $(".mycheckbox").click(function () {
         
        postcheckbox();
    });
    $("#mark").click(function () {
        if ($(".mycheckbox").prop("checked")) {
            $(':checkbox').prop('checked', '');
            // $(this).text('Mark All');
            postcheckbox();
        } else {
            $(':checkbox').prop('checked', 'checked');
            //$(this).text('Unmark All');
            postcheckbox();

        }

    });


$("input.mytextbox").keyup(function() {
    var jThis=$(this);
    var notNumber=new RegExp("[^0-9]","g");
    var val=jThis.val();

    //Math before replacing to prevent losing keyboard selection 
    if(val.match(notNumber))
    { jThis.val(val.replace(notNumber,"")); }
}).keyup(); //Trigger on page load to sanitize values set by server

</script>
