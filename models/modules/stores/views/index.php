<div class="tab-info" style="text-align: center; padding-top: 15px;">

    <div class="form-group" >
        <label class="control-label">
            <b><?php echo lang('stores'); ?></b>
        </label>

    </div>


</div>

  <div class="tab-info" style="float: left; padding-left: 30px; width:500px; align-content: center;" id = "show_form">
       
    <div id = "store_created_alert" class="alert alert-success" hidden>Store Created!</div>
    <div id = "store_created_exist_alert" class="alert alert-danger" hidden>Store Already Exists!</div>
    <div id = "store_default_exist_alert" class="alert alert-danger" hidden>Store Default Already Exists!</div>
    <label class="control-label" style="padding-left:180px;">
        <?php echo "<b>Create New Store</b>"; ?>
    </label>
    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_name') . "<font color = 'red'>*</font>"; ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id ="store_name"/>
        <div class="form-group" id ="obrigatory_name" hidden><font color = 'red'>The Store Name is Required.</font></div>
    </div>
    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_address') . "<font color = 'red'>*</font>"; ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id = "store_address"/>
        <div class="form-group" id ="obrigatory_address" hidden><font color = 'red'>The Store Address is Required.</font></div>
    </div>
    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_phone') . "<font color = 'red'>*</font>"; ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id ="store_phone"/>
        <div class="form-group" id ="obrigatory_phone" hidden><font color = 'red'>The Store Phone Number is Required or Can't be to Short.</font></div>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo lang('store_tax_active'); ?>
        </label>
        <br><select name="store_tax_active[]" class = "store_tax_active" >
            <option value="1" >Yes</option>
            <option value="0" selected>No</option>

        </select>

    </div>
    <div class="form-group" id = "store_tax_percent_div" hidden>
        <label class="control-label">
            <?php echo lang('store_tax_percent'); ?>
        </label>
        <br><input type="text" class = "input-sm form-control" id = "store_tax_percent" value = "0" />

    </div>
    <div class="form-group" id = "store_default">
        <label class="control-label">
            <?php echo lang('store_default'); ?>

        </label>
        <input type="checkbox"  class = "store_default" name = "store_default[]" value = "1" />
    </div>
    <div class="form-group" align='center'>
        <button id = "submit" class="btn btn-success">Create Store</button>

    </div>

  </div>
<div id = "delete_stores" style="text-align: center;" hidden></div>

<div class="tab-info" style="text-align: center;" id = "show_stores">

  <div class="column form-group">
        <label class="control-label">
            <b><?php echo lang('show_stores'); ?></b>
        </label>
            </div>


  
    <table border = "1" align="center" class="table table-striped" style="width:1100px; float: right; margin-right: 20px; border-left:0px; border-top: 0px; border-right: 0px; border-bottom: 0px; margin-right: 30px;">



        <tr style="font-weight: bold; text-align: left; ">
            <td style="padding-left: 10px;"><?php echo lang('store_name'); ?></td>
            <td style="padding-left: 10px;"><?php echo lang('store_address'); ?></td>
            <td style="padding-left: 10px;"><?php echo lang('store_phone'); ?></td>   
            <td style="padding-left: 10px;"><?php echo lang('store_tax_active'); ?></td>
            <td style="padding-left: 10px;"><?php echo lang('store_tax_percent'); ?></td>
            <td style="padding-left: 10px;"><?php echo lang('store_default'); ?></td>
        </tr>
        <?php $this->load->view('stores/show_stores'); ?>
    </table>
    
</div>





<script>

//oficial code


    $(".store_tax_active").change(function () {

        if ($(".store_tax_active").val() == 1) {
            $("#store_tax_percent_div").show();

        } else
        {
            $("#store_tax_percent_div").hide();
        }
    });
    function edit_function(id_store)
    {
        $.post("<?php echo site_url('stores/edit_store'); ?>", {
            id_store: id_store

        },
                function (delete_stores) {

                    $("#delete_stores").html(delete_stores);
                    $("#show_stores").hide();
                    $("#delete_stores").show();
                });
    }


    function delete_function(id_store)
    {

        $.post("<?php echo site_url('stores/delete_store'); ?>", {
            id_store: id_store

        },
                function (delete_stores) {

                    $("#delete_stores").html(delete_stores);
                    $("#show_stores").hide();
                    $("#delete_stores").show();
                });
    }




    $("#submit").click(function () {
        values = $('.store_tax_active').serialize();
        store_default = $(".store_default").serialize();

        if ($("#store_name").val() === "") {
            $("#obrigatory_name").show();
            setTimeout(function () {
                $("#obrigatory_name").hide();
            }, 2000);

        } else if ($("#store_address").val() === "") {
            $("#obrigatory_address").show();
            setTimeout(function () {
                $("#obrigatory_address").hide();
            }, 2000);
        } else if (($("#store_phone").val() === "") || ($('#store_phone').val().length <= 8)) {
            $("#obrigatory_phone").show();
            setTimeout(function () {
                $("#obrigatory_phone").hide();
            }, 2000);
        } else
        {





            $.post("<?php echo site_url('stores/process_store_info'); ?>", {
                store_name: $("#store_name").val(),
                store_address: $("#store_address").val(),
                store_phone: $("#store_phone").val(),
                store_default: store_default,
                store_tax_active: values,
                store_tax_percent: $("#store_tax_percent").val()
            },
                    function (data) {
                        //alert(data);
                         if (data == -11) {
                             $("#store_default_exist_alert").show();
                         }
                         else
                        if (data == -13) {
                            $("#store_created_exist_alert").show();
                        } else
                        {


                            $("#store_created_alert").show();
                        }
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                        //$("#show_stores").html(data);

                    });

        }

    });

    $(function () {
        $('#store_phone').bind('keydown', onlyNumbs); // o "#input" é o input que vc quer aplicar a funcionalidade
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