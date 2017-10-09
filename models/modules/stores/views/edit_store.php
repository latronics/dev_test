<?php
$this->db->select("*");
$this->db->where("id", $id_store);
$stores_data = $this->db->get("ip_stores")->result_array();
?>
<input type="text" value ="<?php echo $stores_data[0]['id']; ?>" hidden id ='id_store'/>
<div class="tab-info" style="float: right; padding-left: 30px; width:800px; text-align: left; margin-right: 30px;">
    <center><labe><b>Edit Store</b></label></center>
    <div id = "store_edit_alert" class="alert alert-success" hidden>Store has been Edited!</div>
    <div id = "store_created_exist_alert_edit" class="alert alert-danger" hidden>Store Already Exists!</div>
    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_name') . "<font color = 'red'>*</font>"; ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id ="store_name_edit" value = "<?php echo $stores_data[0]['store_name']; ?>"/>
        <div class="form-group" id ="obrigatory_name_edit" hidden><font color = 'red'>The Store Name is Required.</font></div>
    </div>
    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_address') . "<font color = 'red'>*</font>"; ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id = "store_address_edit" value = "<?php echo $stores_data[0]['store_address']; ?>"/>
        <div class="form-group" id ="obrigatory_address_edit" hidden><font color = 'red'>The Store Address is Required.</font></div>
    </div>
    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_phone') . "<font color = 'red'>*</font>"; ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id ="store_phone_edit" value = "<?php echo $stores_data[0]['store_phone']; ?>"/>
        <div class="form-group" id ="obrigatory_phone_edit" hidden><font color = 'red'>The Store Phone Number is Required or Can't be to Short.</font></div>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_tax_active'); ?>
        </label>
        <br><select name="store_tax_active_edit[]" class = "store_tax_active_edit">
            <option value="1" <?php
            if ($stores_data[0]['store_tax'] == 1) {
                echo "selected";
            }
            ?>>Yes</option>
            <option value="0" <?php
                    if ($stores_data[0]['store_tax'] == 0) {
                        echo "selected";
                    }
                    ?>>No</option>

        </select>

    </div>
    <div class="form-group" id = "store_tax_percent_div_edit" <?php if ($stores_data[0]['store_tax'] == 0) {
                        echo "hidden";
                    } ?>>
        <label class="control-label">
<?php echo lang('store_tax_percent'); ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id = "store_tax_percent_edit" value = "<?php echo $stores_data[0]['tax_percent']; ?>" />

    </div>
    <div class="form-group" id = "store_default_edit">
        <label class="control-label">
<?php echo lang('store_default'); ?>

        </label>
        <input type="checkbox"   class = "store_default_edit_input" name = "store_default_edit[]" value = "1" <?php if($stores_data[0]['default'] == 1) {echo "checked";} ?>/>
    </div>
    <div class="form-group" align='center'>
        <button id = "save_changes" class="btn btn-success">Save Changes</button>

    </div>

</div>
<script>
    $(".store_tax_active_edit").change(function () {

        if ($(".store_tax_active_edit").val() == 0) {
            $("#store_tax_percent_div_edit").hide();

        } else
        {
            $("#store_tax_percent_div_edit").show();
        }
    });
    $("#save_changes").click(function () {
        store_default_edit = $(".store_default_edit_input").serialize();
        values2 = $('.store_tax_active_edit').serialize();

        if ($("#store_name_edit").val() === "") {
            $("#obrigatory_name_edit").show();
            setTimeout(function () {
                $("#obrigatory_name_edit").hide();
            }, 2000);

        } else if ($("#store_address_edit").val() === "") {
            $("#obrigatory_address_edit").show();
            setTimeout(function () {
                $("#obrigatory_address_edit").hide();
            }, 2000);
        } else if (($("#store_phone_edit").val() === "") || ($('#store_phone_edit').val().length <= 8)) {
            $("#obrigatory_phone_edit").show();
            setTimeout(function () {
                $("#obrigatory_phone_edit").hide();
            }, 2000);
        } else
        {





            $.post("<?php echo site_url('stores/validate_edit_store'); ?>", {
                store_id: $("#id_store").val(),
                store_name: $("#store_name_edit").val(),
                store_address: $("#store_address_edit").val(),
                store_phone: $("#store_phone_edit").val(),
                store_tax_active: values2,
                store_tax_percent: $("#store_tax_percent_edit").val(),
                store_default_edit: store_default_edit
            },
                    function (data) {
                        //alert(data);
                        if (data == -13) {
                            $("#store_created_exist_alert_edit").show();
                        } else
                        {

                            $("#store_edit_alert").show();

                            //$("#show_stores").html(data);
                        }
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    });

        }
    });
    $(function () {
        $('#store_phone_edit').bind('keydown', onlyNumbs); // o "#input" é o input que vc quer aplicar a funcionalidade
    });

    function onlyNumbs(e) {

        //teclas adicionais permitidas (tab,delete,backspace,setas direita e esquerda)
        keyCodesPermits = new Array(8, 9, 37, 39, 46);

        //numeros e 0 a 9 do teclado alfanumerico
        for (x = 48; x <= 57; x++) {
            keyCodesPermits.push(x);
        }

        //numeros e 0 a 9 do teclado numerico
        for (x = 96; x <= 105; x++) {
            keyCodesPermits.push(x);
        }

        //Pega a tecla digitada
        keyCode = e.which;

        //Verifica se a tecla digitada é permitida
        if ($.inArray(keyCode, keyCodesPermits) !== -1) {
            return true;
        }
        return false;
    }
</script>