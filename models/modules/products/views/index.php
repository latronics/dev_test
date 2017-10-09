<h4 style='margin-top:10px;' align='center'>Category/Products</h4>
<hr style="width:100%; margin-top:10px;"/>
<table style="float:left;" id="datatree_search">
    <tr><td>
            <img src="../../../../assets/default/img/search.png" title="Type in the field to search" style="width:20px; height: 20px; float:left; margin-left:10px;">
        </td>
        <td>
            <input type="text" class="form-control" id="search_input" style="float:left; margin-left:10px; width:440px;" placeholder="Product Name/Category Name/Tag" onkeyup="search_bykey(this.value, 1);">
        </td>
        <td>
            <a href="#" onclick="clean_field(1);">
                <img src="../../../../assets/default/img/clean_fields.png" style="width:20px; height: 20px; float:left; margin-left:10px;" title="Clean Search">
            </a>
        </td>
    </tr>
</table>
<table style="float:left;" id="datatree_2search" hidden>
    <tr>
        <td>
            <img src="../../../../assets/default/img/search.png" title="Type in the field to search" style="width:20px; height: 20px; float:left; margin-left:10px;">
        </td>
        <td>
            <input type="text" class="form-control" id="search_input2" style="float:left; margin-left:10px; width:440px;" placeholder="Product Name/Category Name/Tag" onkeyup="search_bykey(this.value, 2);">
        </td>
        <td>
            <a href="#" onclick="clean_field(2);">
                <img src="../../../../assets/default/img/clean_fields.png" style="width:20px; height: 20px; float:left; margin-left:10px;" title="Clean Search">
            </a>
        </td>
    </tr>
</table>
<br><br>
<a href='#' style='float:left; margin-left:20px; font-weight: bold;' onclick='new_category();'>New Category</a>
<a href='#' style='float:left; margin-left:20px; font-weight: bold;' onclick='new_product();'>New Product</a>
<a  href='#' style="margin-left:20px; font-weight: bold;" onclick='hide_showdatatree();'>Second Datatree</a>
<a  href='#' style="margin-left:20px; font-weight: bold;" onclick='hide_showlog();'>Show/Hide Log</a>
<img src='../../../../assets/default/img/page-loader.gif' id='show_loader' style='width:20px; height: 20px; margin-left:10px;' hidden/><br>
<div id="datatree"  style="height: 700px; width:500px; overflow: auto; float:left; margin-left:10px;"></div>
<div id="datatree_2"  style="height: 700px; width:500px; overflow: auto; float:left; margin-left:10px;"></div>
<div id="category_div" class="webix_view webix_tree" style="float:left; border:1px #ddd solid; border-radius:5px; margin-left:10px; margin-top:10px; padding:10px;" hidden>
    <form method='post' action='#' id='category_form'>
        <table class="table-content" border="0">
            <tr>
                <td colspan='2'><label style="font-weight: bold;">Category Name</label></td>
            </tr>
            <tr>
                <td colspan='2'><input type="text" name="category_name" id="category_name" placeholder="Category Name" class="form-control" required></td>
            </tr>
            <tr>
                <td colspan='2'><label  style="font-weight: bold;">This belong to</label></td>
            </tr>
            <tr>
                <td colspan='2'><select id='category_select_ex' name='category_select_ex' class="form-control">

                        <option value='0'></option>
                        <?php
                        foreach ($categories_list as $categories_list1) {
                            ?>

                            <option value='<?php echo $categories_list1->category_id; ?>' id='cat_id'><?php echo $categories_list1->category_name; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>
            <tr><td><label  style="font-weight: bold;">Tag</label></td></tr>
            <tr><td><input type="text" name="category_tag" id="category_tag" placeholder="Category Tag" class="form-control"></td></tr>
            <input type='text' id='cat_id' name='cat_id' value='null' hidden>
            <tr>
                <td style='padding-top:10px;' align='center'><input class='btn btn-sm btn-success' type='submit' value='Save'>
                    <a class='btn btn-danger btn-sm delete'  onclick='delete_category($("#cat_id").val());'>Delete</a></td>
            </tr>
            <input type="text" value="0" id="cat_prod" hidden>
        </table>
    </form>
</div>
<div id="product_div" class="webix_view webix_tree" style="float:left; border:1px #ddd solid; border-radius:5px; margin-left:10px; margin-top:10px; padding:10px;" hidden>
    <form method='post' action='#' id='products_form'>
        <table class="table-content" border="0">
            <tr>
                <td colspan='2'><label style="font-weight: bold;">Product Name</label></td>
            </tr>
            <tr>
                <td colspan='2'><input type="text" name="product_name" id="product_name" placeholder="Product Name" class="form-control" required></td>
            </tr>
            <tr>
                <td colspan='2'><label  style="font-weight: bold;">Product Category</label></td>
            </tr>
            <tr>
                <td colspan='2'><select id='category_prod_select' name='category_prod_select' class="form-control">

                        <option value='0'></option>
                        <?php
                        foreach ($categories_list as $categories_list2) {
                            ?>

                            <option value='<?php echo $categories_list2->category_id; ?>' id='cat_id'><?php echo $categories_list2->category_name; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>
            <tr>
                <td colspan='2'><label  style="font-weight: bold;">Product Price</label></td>
            </tr>

            <tr>
                <td colspan='2'><input type="text" name="product_price" id="product_price" placeholder="Product Price" class="form-control" value="" required></td>
            </tr>
            <tr>
                <td colspan='2'><label  style="font-weight: bold;">Product Cost</label></td>
            </tr>

            <tr>
                <td colspan='2'><input type="text" name="product_cost" id="product_cost" placeholder="Product Cost" class="form-control" value="" required></td>
            </tr>
            <tr>
                <td style='padding-top:10px;' align='center'><input class='btn btn-sm btn-success' type='submit' value='Save'>
                    <a class='btn btn-danger btn-sm delete'  onclick='delete_products($("#prod_id").val());'>Delete</a></td>
            </tr>
            <input type='text' id='prod_id' name='prod_id' value='' hidden>
        </table>
    </form>
</div>
<input type='text' id='aux' value='0' hidden>
<div id="new_category" class="webix_view webix_tree" style="float:left; border:1px #ddd solid; border-radius:5px; margin-left:10px;  padding:10px;" hidden>
    <form method='post' action='#' id='new_category_form'>
        <table class="table-content" border="0">
            <tr>
                <td colspan='2' align='center'><label style="font-weight: bold;">New Category</label>
                    <hr style="width:100%; margin-top:0px;"/>
                </td>
            </tr>
            <tr>
                <td colspan='2'><label style="font-weight: bold;">Category Name</label></td>
            </tr>
            <tr>
                <td colspan='2'><input type="text" name="category_name_new" id="category_name_new" placeholder="Category Name" class="form-control" required></td>
            </tr>
            <tr>
                <td colspan='2'><label  style="font-weight: bold;">This belong to</label></td>
            </tr>
            <tr>
                <td colspan='2'><select id='category_select_new' name='category_select_new' class="form-control">

                        <option value='0'></option>
                        <?php
                        foreach ($categories_list as $categories_list3) {
                            ?>

                            <option value='<?php echo $categories_list3->category_id; ?>' id='cat_id'><?php echo $categories_list3->category_name; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>
            <tr><td><label  style="font-weight: bold;">Tag</label></td></tr>
            <tr><td><input type="text" name="category_tag_new" id="category_tag_new" placeholder="Category Tag" class="form-control"></td></tr>
            <tr>
                <td style='padding-top:10px;' align='center'>
                    <input class='btn btn-sm btn-success' type='submit' value='Save'>
                </td>
            </tr>
            <input type='text' id='cat_id_new' name='cat_id_new' value='null' hidden>
        </table>
    </form>
</div>
<div id="new_product" class="webix_view webix_tree" style="float:left; border:1px #ddd solid; border-radius:5px; margin-left:10px;  padding:10px;" hidden>
    <form method='post' action='#' id='new_product_form'>
        <table class="table-content" border="0">
            <tr>
                <td colspan='2' align='center'><label style="font-weight: bold;">New Product</label>
                    <hr style="width:100%; margin-top:0px;"/>
                </td>
            </tr> 
            <tr>
                <td colspan='2'><label style="font-weight: bold;">Product Name</label></td>
            </tr>
            <tr>
                <td colspan='2'><input type="text" name="product_name_new" id="product_name_new" placeholder="Product Name" class="form-control" required></td>
            </tr>
            <tr>
                <td colspan='2'><label  style="font-weight: bold;">Product Category</label></td>
            </tr>
            <tr>
                <td colspan='2'><select id='product_select_new' name='product_select_new' class="form-control">

                        <option value='0'></option>
                        <?php
                        foreach ($categories_list as $categories_list4) {
                            ?>

                            <option value='<?php echo $categories_list4->category_id; ?>' id='cat_id'><?php echo $categories_list4->category_name; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>
            <tr>
                <td colspan='2'><label style="font-weight: bold;">Product Price</label></td>
            </tr>
            <tr>
                <td colspan='2'><input type="text" name="product_price_new" id="product_price_new" placeholder="Product Price" class="form-control" required></td>
            </tr>
            <tr>
                <td colspan='2'><label style="font-weight: bold;">Product Cost</label></td>
            </tr>
            <tr>
                <td colspan='2'><input type="text" name="product_cost_new" id="product_cost_new" placeholder="Product Cost" class="form-control" required></td>
            </tr>
            <tr>
            <tr>
                <td style='padding-top:10px;' align='center'>
                    <input class='btn btn-sm btn-success' type='submit' value='Save'>
                </td>
            </tr>
            <input type='text' id='cat_id_new' name='cat_id_new' value='null' hidden>
        </table>
    </form>
    <input type='text' id='aux2' value='0' hidden>
</div>

<div id="log_main" class="panel panel-default" style="width:943px; float:left; border-bottom:0px;  margin-left:10px;" hidden>

    <div class="panel-heading" style="">


        <b><i class="fa fa-history fa-margin" ></i>Last Changes Log</b>
        <span class="pull-right text-muted"><?php //echo lang($quote_status_period);                                                                                                                                                      ?></span>
    </div>
    <table style='margin-top:5px; margin-bottom:5px;'>
        <tr><td>

                <div id ="date_content" style="padding-left:15px;">
                    <div class="input-group" >
                        <input name="date" id="date_fromlog"
                               class="form-control input-sm datepicker"
                               value="" placeholder = "Date From" style="width:150px;" onchange='release_button();'>
                        <i class="fa fa-calendar fa-fw input-group-addon" style="width:30px; height: 30px; padding-top:8px; margin-left:-8px;"></i>
                    </div></div>
            </td><td>
                <div id ="date_content" style="padding-left:15px;" hidden>
                    <div class="input-group" >
                        <input name="date" id="date_tolog"
                               class="form-control input-sm datepicker"
                               value="" placeholder = "Date From" style="width:150px;" onchange='release_button();' >
                        <i class="fa fa-calendar fa-fw input-group-addon" style="width:30px; height: 30px; padding-top:8px;"></i>
                    </div></div>
            </td>
            <td id='restore_logtd' hidden>
                <input type="button" class='btn btn-default' data-toggle="modal" data-target="#myModal2" id='restore_log' value="Restore from log" style='margin-left:10px;' >
            </td>
        </tr>
    </table>

    <div  id="log_content" class="webix_view webix_tree" style="overflow:auto; float:right; border:1px #ddd solid;  border-radius:5px; border-top-left-radius:0px; border-top-right-radius: 0px;  padding:10px; height: 634px; width:920px;">

        <table  border="0" style='border:1px #ddd solid;  width:100%; border-radius:3px;'>
            <thead>

                <tr style='font-weight: bold; background-color:#D6D7C8;'  align='center'>
                    <th style='padding:5px; border-top:1px #ddd solid;'>
                        Undo
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:80px;'>
                        Username
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:70px;'>
                        Last Parent
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:84px;'>
                        New Parent
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:78px;'>
                        Last Name
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:78px;'>
                        New Name
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:58px;'>
                        Last Price
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:56px;'>
                        New Price
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:56px;'>
                        Last Cost
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:58px;'>
                        New Cost
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:78px;'>
                        Type
                    </th>
                    <th style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:110px;'>
                        Date
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($log_data as $log_data_ready) { ?>
                <input type='text' id='log_id' hidden>
                <tr style='font-weight: bold; color:#666; '  align='center'>
                    <td style='padding:5px; border-top:1px #ddd solid; width:42px; width:54px;'>
                        <label><a href='#' class="" data-toggle="modal" data-target="#myModal" onclick='$("#log_id").val(<?php echo $log_data_ready->id; ?>);'>
                                <img src='../../../../assets/default/img/undo.ico' style='width:15px; height: 15px;' title='Back to this event'></a></label>
                    </td>

                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:92px;'>
                        <label><?php echo $log_data_ready->user_name; ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <?php
                        //GET CATEGORY NAMES
                        $this->db->where("category_id", $log_data_ready->last_parent);
                        $last_parent = $this->db->get("ip_categories")->result_object();
                        if ($last_parent != null) {
                            ?>
                            <label><?php echo $last_parent[0]->category_name; ?></label>
                            <?php
                        } else {
                            echo "<label style='color:red;'>No Parent</label>";
                        }
                        ?>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid; width:90px;'>
                        <?php
                        //GET CATEGORY NAMES
                        $this->db->where("category_id", $log_data_ready->new_parent);
                        $new_parent = $this->db->get("ip_categories")->result_object();
                        if ($new_parent != null) {
                            ?>
                            <label><?php echo $new_parent[0]->category_name; ?></label>
                            <?php
                        } else {
                            echo "<label style='color:red;'>No Parent</label>";
                        }
                        ?>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php echo $log_data_ready->last_name; ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php echo $log_data_ready->new_name; ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php
                            if ($log_data_ready->last_price == 0) {
                                echo "0.00";
                            } else {
                                echo format_amount($log_data_ready->last_price);
                            }
                            ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php
                            if ($log_data_ready->new_price == 0) {
                                echo "0.00";
                            } else {
                                echo format_amount($log_data_ready->new_price);
                            }
                            ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php
                            if ($log_data_ready->last_cost == 0) {
                                echo "0.00";
                            } else {
                                echo format_amount($log_data_ready->last_cost);
                            }
                            ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php
                            if ($log_data_ready->new_cost == 0) {
                                echo "0.00";
                            } else {
                                echo format_amount($log_data_ready->new_cost);
                            }
                            ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php
                            if ($log_data_ready->category_id != 0) {
                                echo "Category";
                            } else {
                                echo "Product";
                            }
                            ?></label>
                    </td>
                    <td style='padding:5px; border-top:1px #ddd solid; border-left:1px #ddd solid;'>
                        <label><?php echo date("m/d/Y", strtotime($log_data_ready->date)) . " " . date("H:i:s", strtotime($log_data_ready->time)); ?></label>
                    </td>
                </tr>

            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog" style='margin-top:200px;'>
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Do you really want to undo?</h4>
            </div>
            <div class="modal-body">
                <label id='show_passerror' class='alert alert-danger' style='width:540px;' hidden>Answer Required</label>
                <label id='show_invalidanswer' class='alert alert-danger' style='width:540px;' hidden>Invalid Answer</label>
                <br>
                <label>Type 'Yes' to confirm</label><br><input type='text' id='user_answer' class='form-control' autofocus required><br>
                <input type='button' class='btn btn-sm btn-success' onclick='undo_event($("#log_id").val());' value='Submit'>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="myModal2" class="modal fade" role="dialog" style='margin-top:200px;'>
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Do you really want to restore from <label id='restore_date2'></label> ?</h4>
            </div>
            <div class="modal-body">
                <label id='backup_error' class='alert alert-danger' style='margin-top:5px; width:96.7%;' hidden>No data found.</label>
                <label id='show_passerror2' class='alert alert-danger' style='width:540px;' hidden>Answer Required</label>
                <label id='show_invalidanswer2' class='alert alert-danger' style='width:540px;' hidden>Invalid Answer</label>
                <br>
                <label>Type 'Yes' to confirm</label><br><input type='text' id='user_answer2' class='form-control' autofocus><br>
                <input type='button' class='btn btn-sm btn-success' onclick='restore_fromlog();' value='Submit'>
            </div>
            <div class="modal-footer">
                <button type="button" id='close_modal2' class="btn btn-default" data-dismiss="modal" onclick='clean_fields2();'>Close</button>
            </div>
        </div>
    </div>
</div>

<script src="../../../../assets/default/js/jquery.sortable.js"></script>
<link rel="stylesheet" href="../../../../assets/default/js/webix/webix/codebase/webix.css" type="text/css" charset="utf-8">
<script src="../../../../assets/default/js/webix/webix/codebase/webix.js" type="text/javascript" charset="utf-8"></script>
<script src="../../../../assets/default/js/webix/webix/samples/common/treedata.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="../../../../assets/default/js/webix/webix/samples/common/samples.css"> 

<script type="text/javascript">
                    $("input#date_fromlog").on({
                        keydown: function (e) {
                            if (e.which === 32)
                                return false;
                        },
                        change: function () {
                            this.value = this.value.replace(/\s/g, "");
                        }
                    });
                    $("input#date_tolog").on({
                        keydown: function (e) {
                            if (e.which === 32)
                                return false;
                        },
                        change: function () {
                            this.value = this.value.replace(/\s/g, "");
                        }
                    });
                    function clean_fields2()
                    {
                        $("#backup_error").hide();
                        $("#show_invalidanswer2").hide();
                        $("#show_passerror2").hide();
                        $("#user_answer2").val('');
                    }
                    function restore_fromlog()
                    {

                        if ($("#user_answer2").val() == "Yes") {
                            $("#show_passerror2").hide();
                            $.post("<?php echo site_url('products/ajax/back_logdata'); ?>", {
                                date_from: $("#date_fromlog").val(),
                                date_to: $("#date_tolog").val()
                            },
                                    function (data) {
                                        if (data == 'false')
                                        {
                                            $("#backup_error").show();
                                            $("#show_invalidanswer2").hide();
                                            $("#show_passerror2").hide();
                                            $("#user_answer2").focus();
                                        } else
                                        {
                                            $("#backup_error").hide();
                                            location.reload();
                                        }
                                    });
                        } else if ($("#user_answer2").val() == '')
                        {
                            $("#show_invalidanswer2").hide();
                            $("#backup_error").hide();
                            $("#show_passerror2").show();
                            $("#user_answer2").focus();

                        } else
                        {
                            $("#show_passerror2").hide();
                            $("#backup_error").hide();
                            $("#show_invalidanswer2").show();
                            $("#user_answer2").focus();
                        }

                    }
                    function release_button()
                    {

                        if ($("#date_fromlog").val() != '')
                        {
                            $("#restore_logtd").show();

                            $("#restore_date2").html($("#date_fromlog").val());
                        } else
                        {
                            $("#restore_logtd").hide();
                        }
                    }
                    function clean_field(datatree)
                    {
                        if (datatree == 1)
                        {
                            $("#search_input").val('');
                            search_bykey('', 1);
                            $("#search_input").focus();
                        } else if (datatree == 2)
                        {
                            search_bykey('', 2);
                            $("#search_input2").val('');
                            $("#search_input2").focus();
                        }
                    }
                    function search_bykey(key, view)
                    {
                        $.post("<?php echo site_url('products/ajax/search_catprod'); ?>", {
                            key: key

                        }, function (data) {
                            if (view == 1) {
                                $("#datatree").html('');
                                webix.ready(function () {
                                    var json_cat = data;
                                    webix.DataDriver.plainjs = webix.extend({
                                        arr2hash: function (data) {
                                            var hash = {};
                                            for (var i = 0; i < data.length; i++) {
                                                var pid = data[i].parent_id;
                                                if (!hash[pid])
                                                    hash[pid] = [];
                                                hash[pid].push(data[i]);
                                            }
                                            return hash;
                                        },
                                        hash2tree: function (hash, level) {
                                            var top = hash[level];
                                            for (var i = 0; i < top.length; i++) {
                                                var branch = top[i].id;
                                                if (hash[branch])
                                                    top[i].data = this.hash2tree(hash, branch);
                                            }
                                            return top;
                                        },
                                        getRecords: function (data, id) {
                                            var hash = this.arr2hash(data);
                                            return this.hash2tree(hash, 0);
                                        }
                                    }, webix.DataDriver.json);
                                    //treedata = [{"id":"249","value":"Accessories"},{"id":"286","value":"acer m5"},{"id":"258","value":"Apple Product Sales"},{"id":"135","value":"Computer Repair Services"},{"id":"250","value":"Computer Sales"},{"id":"248","value":"Dell Venue 11"},{"id":"295","value":"Enjoy the structure"},{"id":"47","value":"Items"},{"id":"244","value":"Los Angeles Repair Services"},{"id":"131","value":"Mac Repairs"},{"id":"136","value":"Mobile Device Repairs"},{"id":"245","value":"Parts"},{"id":"196","value":"PC Repairs"},{"id":"249","value":"Accessories","data":{"id":"249.1776","value":"NXG Technology 4 Port USB Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1777","value":"NXG Technology 2port Compact Car Usb Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1690","value":"Monster Digital USB Flash Drive - 16GB - USB 2.0"}},{"id":"249","value":"Accessories","data":{"id":"249.1775","value":"NXG Technology High Fidelity Stereo Earbuds"}},{"id":"249","value":"Accessories","data":{"id":"249.1715","value":"Mophie - Juice Pack plus External Battery Case for Apple iPhone 5 and 5s"}},{"id":"249","value":"Accessories","data":{"id":"249.1716","value":"Griffin Technology - PowerJolt SE Lightning Vehicle Charger"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.435","value":"Laptop Repair Form"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1067","value":"Hard Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1068","value":"Optical Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1069","value":"Casing"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1070","value":"Power"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1071","value":"Memory"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1072","value":"Logic Boards"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1082","value":"Screens"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1691","value":"Microsoft Surface Pro 3"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1692","value":"Sony Vaio Tap 11 - SVT11213CXB"}},{"id":"248","value":"Dell Venue 11","data":{"id":"248.1726","value":"Appleizer test"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1683","value":"iPhone and iPad Repair in Hawthorne"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1684","value":"iPhone and iPad Repair in Venice"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1649","value":"Mac Repair Los Angeles"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1687","value":"Test product"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1060","value":"Mac Mini"}},{"id":"245","value":"Parts","data":{"id":"245.1696","value":"Samsung NP900X3D Screen Assembly"}},{"id":"245","value":"Parts","data":{"id":"245.1784","value":"Lenovo Yoga 2 UK keyboard"}},{"data":{"id":"258.259","value":"iPod Touch 5th Gen 2014"}},{"data":{"id":"135.214","value":"Logic Board Repair"}},{"data":{"id":"295.310","value":"test"}},{"data":{"id":"295.311","value":"testchild"}},{"data":{"id":"295.318","value":"Enjoy the structure Child 1"}},{"data":{"id":"47.27","value":"LCD - Displays"}},{"data":{"id":"47.34","value":"Specials"}},{"data":{"id":"47.247","value":"iPad Lots"}},{"data":{"id":"131.132","value":"MacBook Pro"}},{"data":{"id":"131.133","value":"Macbook"}},{"data":{"id":"131.134","value":"Macbook Air"}},{"data":{"id":"131.235","value":"Mac Mini"}},{"data":{"id":"131.243","value":"iMac"}},{"data":{"id":"131.271","value":"Power Adapters"}},{"data":{"id":"136.137","value":"HTC"}},{"data":{"id":"136.179","value":"Samsung"}},{"data":{"id":"136.139","value":"Apple"}},{"data":{"id":"245.239","value":"Lenovo Screens"}},{"data":{"id":"245.240","value":"Sony Vaio Displays"}},{"data":{"id":"245.241","value":"Lenovo U310 Touch"}},{"data":{"id":"245.242","value":"iMac Parts"}},{"data":{"id":"245.262","value":"Macbook Parts"}},{"data":{"id":"245.284","value":"Mac Parts"}},{"data":{"id":"196.197","value":"Samsung"}},{"data":{"id":"196.198","value":"Toshiba"}},{"data":{"id":"196.199","value":"Dell"}},{"data":{"id":"196.200","value":"Asus"}},{"data":{"id":"196.201","value":"Hewlett-Packard HP"}},{"data":{"id":"196.202","value":"Lenovo"}},{"data":{"id":"196.203","value":"Sony"}},{"data":{"id":"196.204","value":"Acer"}},{"data":{"id":"196.234","value":"Panasonic"}},{"data":{"id":"196.238","value":"MSI"}}];
                                    tree = webix.ui({

                                        container: "datatree",
                                        view: "tree",
                                        datatype: "plainjs",
                                        select: "multiselect",
                                        drag: true,
                                        on: {
                                            "onAfterDrop": function (id, e, trg) {
                                                var start = id.source;
                                                var parent = id.parent;
                                                var cat_prod = $("#cat_prod").val();
                                                //console.log(id);

                                                $.post("<?php echo site_url('products/ajax/update_parent'); ?>", {
                                                    start: start,
                                                    parent: parent
                                                },
                                                        function (data) {
                                                            //console.log(data);
                                                        });
                                            },
                                            //default click behavior that is true for any datatable cell
                                            "onItemClick": function (id, e, trg) {


                                                $.post("<?php echo site_url('products/ajax/validate_cat'); ?>", {
                                                    cat_prod_id: id
                                                },
                                                        function (data) {
                                                            var response = JSON.parse(data);
                                                            if (response.category_id == null) {
                                                                $("#prod_id").val(id);
                                                                $("#product_name").val(response.product_name);
                                                                $("#product_price").val(response.product_price);
                                                                $("#product_cost").val(response.purchase_price);
                                                                $("#category_prod_select").val(response.pcategory_id);
                                                                $("#category_div").hide();
                                                                $("#new_category").hide();
                                                                $("#new_product").hide();
                                                                $("#aux").val(0);
                                                                $("#aux2").val(0);
                                                                $("#product_div").show();
                                                            } else
                                                            {
                                                                $("#cat_id").val(id);
                                                                $("#category_name").val(response.category_name);
                                                                $("#category_tag").val(response.category_tag);
                                                                $("#cat_prod_name").html("Category Name");
                                                                $("#category_select_ex").val(response.category_parent);
                                                                $("#product_div").hide();
                                                                $("#new_category").hide();
                                                                $("#new_product").hide();
                                                                $("#aux").val(0);
                                                                $("#aux2").val(0);
                                                                $("#category_div").show();
                                                            }

                                                        });
                                                //webix.message("Click on row: " + id);
                                            }
                                        },
                                        ready: function () {

                                        },
                                        data: json_cat

                                    });

                                });
                            } else if (view == 2)
                            {
                                $("#datatree_2").html('');
                                webix.ready(function () {
                                    var json_cat = data;
                                    webix.DataDriver.plainjs = webix.extend({
                                        arr2hash: function (data) {
                                            var hash = {};
                                            for (var i = 0; i < data.length; i++) {
                                                var pid = data[i].parent_id;
                                                if (!hash[pid])
                                                    hash[pid] = [];
                                                hash[pid].push(data[i]);
                                            }
                                            return hash;
                                        },
                                        hash2tree: function (hash, level) {
                                            var top = hash[level];
                                            for (var i = 0; i < top.length; i++) {
                                                var branch = top[i].id;
                                                if (hash[branch])
                                                    top[i].data = this.hash2tree(hash, branch);
                                            }
                                            return top;
                                        },
                                        getRecords: function (data, id) {
                                            var hash = this.arr2hash(data);
                                            return this.hash2tree(hash, 0);
                                        }
                                    }, webix.DataDriver.json);
                                    //treedata = [{"id":"249","value":"Accessories"},{"id":"286","value":"acer m5"},{"id":"258","value":"Apple Product Sales"},{"id":"135","value":"Computer Repair Services"},{"id":"250","value":"Computer Sales"},{"id":"248","value":"Dell Venue 11"},{"id":"295","value":"Enjoy the structure"},{"id":"47","value":"Items"},{"id":"244","value":"Los Angeles Repair Services"},{"id":"131","value":"Mac Repairs"},{"id":"136","value":"Mobile Device Repairs"},{"id":"245","value":"Parts"},{"id":"196","value":"PC Repairs"},{"id":"249","value":"Accessories","data":{"id":"249.1776","value":"NXG Technology 4 Port USB Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1777","value":"NXG Technology 2port Compact Car Usb Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1690","value":"Monster Digital USB Flash Drive - 16GB - USB 2.0"}},{"id":"249","value":"Accessories","data":{"id":"249.1775","value":"NXG Technology High Fidelity Stereo Earbuds"}},{"id":"249","value":"Accessories","data":{"id":"249.1715","value":"Mophie - Juice Pack plus External Battery Case for Apple iPhone 5 and 5s"}},{"id":"249","value":"Accessories","data":{"id":"249.1716","value":"Griffin Technology - PowerJolt SE Lightning Vehicle Charger"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.435","value":"Laptop Repair Form"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1067","value":"Hard Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1068","value":"Optical Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1069","value":"Casing"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1070","value":"Power"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1071","value":"Memory"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1072","value":"Logic Boards"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1082","value":"Screens"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1691","value":"Microsoft Surface Pro 3"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1692","value":"Sony Vaio Tap 11 - SVT11213CXB"}},{"id":"248","value":"Dell Venue 11","data":{"id":"248.1726","value":"Appleizer test"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1683","value":"iPhone and iPad Repair in Hawthorne"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1684","value":"iPhone and iPad Repair in Venice"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1649","value":"Mac Repair Los Angeles"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1687","value":"Test product"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1060","value":"Mac Mini"}},{"id":"245","value":"Parts","data":{"id":"245.1696","value":"Samsung NP900X3D Screen Assembly"}},{"id":"245","value":"Parts","data":{"id":"245.1784","value":"Lenovo Yoga 2 UK keyboard"}},{"data":{"id":"258.259","value":"iPod Touch 5th Gen 2014"}},{"data":{"id":"135.214","value":"Logic Board Repair"}},{"data":{"id":"295.310","value":"test"}},{"data":{"id":"295.311","value":"testchild"}},{"data":{"id":"295.318","value":"Enjoy the structure Child 1"}},{"data":{"id":"47.27","value":"LCD - Displays"}},{"data":{"id":"47.34","value":"Specials"}},{"data":{"id":"47.247","value":"iPad Lots"}},{"data":{"id":"131.132","value":"MacBook Pro"}},{"data":{"id":"131.133","value":"Macbook"}},{"data":{"id":"131.134","value":"Macbook Air"}},{"data":{"id":"131.235","value":"Mac Mini"}},{"data":{"id":"131.243","value":"iMac"}},{"data":{"id":"131.271","value":"Power Adapters"}},{"data":{"id":"136.137","value":"HTC"}},{"data":{"id":"136.179","value":"Samsung"}},{"data":{"id":"136.139","value":"Apple"}},{"data":{"id":"245.239","value":"Lenovo Screens"}},{"data":{"id":"245.240","value":"Sony Vaio Displays"}},{"data":{"id":"245.241","value":"Lenovo U310 Touch"}},{"data":{"id":"245.242","value":"iMac Parts"}},{"data":{"id":"245.262","value":"Macbook Parts"}},{"data":{"id":"245.284","value":"Mac Parts"}},{"data":{"id":"196.197","value":"Samsung"}},{"data":{"id":"196.198","value":"Toshiba"}},{"data":{"id":"196.199","value":"Dell"}},{"data":{"id":"196.200","value":"Asus"}},{"data":{"id":"196.201","value":"Hewlett-Packard HP"}},{"data":{"id":"196.202","value":"Lenovo"}},{"data":{"id":"196.203","value":"Sony"}},{"data":{"id":"196.204","value":"Acer"}},{"data":{"id":"196.234","value":"Panasonic"}},{"data":{"id":"196.238","value":"MSI"}}];
                                    tree = webix.ui({

                                        container: "datatree_2",
                                        view: "tree",
                                        datatype: "plainjs",
                                        select: "multiselect",
                                        drag: true,
                                        on: {
                                            "onAfterDrop": function (id, e, trg) {
                                                var start = id.source;
                                                var parent = id.parent;
                                                var cat_prod = $("#cat_prod").val();
                                                //console.log(id);

                                                $.post("<?php echo site_url('products/ajax/update_parent'); ?>", {
                                                    start: start,
                                                    parent: parent
                                                },
                                                        function (data) {
                                                            //console.log(data);
                                                        });
                                            },
                                            //default click behavior that is true for any datatable cell
                                            "onItemClick": function (id, e, trg) {


                                                $.post("<?php echo site_url('products/ajax/validate_cat'); ?>", {
                                                    cat_prod_id: id
                                                },
                                                        function (data) {
                                                            var response = JSON.parse(data);
                                                            if (response.category_id == null) {
                                                                $("#prod_id").val(id);
                                                                $("#product_name").val(response.product_name);
                                                                $("#product_price").val(response.product_price);
                                                                $("#product_cost").val(response.purchase_price);
                                                                $("#category_prod_select").val(response.pcategory_id);
                                                                $("#category_div").hide();
                                                                $("#new_category").hide();
                                                                $("#new_product").hide();
                                                                $("#aux").val(0);
                                                                $("#aux2").val(0);
                                                                $("#product_div").show();
                                                            } else
                                                            {
                                                                $("#cat_id").val(id);
                                                                $("#category_name").val(response.category_name);
                                                                $("#category_tag").val(response.category_tag);
                                                                $("#cat_prod_name").html("Category Name");
                                                                $("#category_select_ex").val(response.category_parent);
                                                                $("#product_div").hide();
                                                                $("#new_category").hide();
                                                                $("#new_product").hide();
                                                                $("#aux").val(0);
                                                                $("#aux2").val(0);
                                                                $("#category_div").show();
                                                            }

                                                        });
                                                //webix.message("Click on row: " + id);
                                            }
                                        },
                                        ready: function () {

                                        },
                                        data: json_cat

                                    });

                                });
                            }
                        });
                    }
                    function hide_showdatatree()
                    {
                        if ($("#datatree_2").is(":visible")) {
                            if ($("#log_main").is(":visible"))
                            {
                                $("#log_main").css("margin-top", "10px");
                            }
                            $("#datatree_2search").hide();
                            $("#datatree_2").hide();
                            $("#log_main").css("margin-top", "0px");
                        } else
                        {
                            $("#datatree_2search").show();
                            $("#datatree_2").show();
                            $("#log_main").css("margin-top", "10px");
                        }

                    }
                    function hide_showlog()
                    {
                        if ($("#log_main").is(":visible")) {
                            $("#log_main").hide();
                            $("#date_fromlog").hide();
                            $("#date_tolog").hide();
                        } else
                        {
                            $("#date_fromlog").show();
                            $("#date_tolog").show();
                            $("#log_main").show();

                        }
                        if ($("#datatree_2").is(":visible")) {
                            $("#log_main").css("margin-top", "10px");
                        } else
                        {
                            $("#log_main").css("margin-top", "0px");
                        }
                    }

                    function undo_event(log_id)
                    {

                        var valid_pass;
                        $("#show_passerror").hide();
                        $.post("<?php echo site_url('products/ajax/valid_pass'); ?>", {
                            user_answer: $("#user_answer").val()

                        }, function (data) {
                            valid_pass = data;
                            if ($("#user_answer").val() != '') {
                                if (valid_pass == 1) {
                                    $.post("<?php echo site_url('products/ajax/undo_event'); ?>", {
                                        log_id: log_id

                                    }, function (data) {
                                        location.reload();
                                    });
                                } else
                                {
                                    $("#show_invalidanswer").show();
                                    $("#user_answer").focus();
                                }
                            } else
                            {
                                $("#show_invalidanswer").hide();
                                $("#show_passerror").show();
                                $("#user_answer").focus();
                            }
                        });
                    }
                    $(document).ajaxStart(function () {
                        $("#show_loader").show();
                    });
                    $(document).ajaxStop(function () {
                        $("#show_loader").hide();
                    });
                    function new_product()
                    {

                        if ($("#aux2").val() == 0) {
                            $("#new_product").show();
                            $("#product_div").hide();
                            $("#category_div").hide();
                            $("#new_category").hide();
                            $("#aux").val(0);
                            $("#aux2").val(1);
                        } else
                        {
                            $("#product_div").hide();
                            $("#category_div").hide();
                            $("#new_product").hide();
                            $("#aux").val(0);
                            $("#aux2").val(0);
                        }
                    }
                    function delete_products(prod_id)
                    {
                        var retVal = confirm("Do you really want to delete this Product ?");
                        if (retVal == true) {
                            $.post("<?php echo site_url('products/ajax/delete_prod'); ?>", {
                                prod_id: prod_id

                            }, function (data) {
                                location.reload();
                            });
                        } else {

                        }

                    }


                    $("#new_product_form").submit(function (event) {
                        event.preventDefault();
                        $.post("<?php echo site_url('products/ajax/new_product'); ?>", {
                            product_name: $("#product_name_new").val(),
                            product_category: $("#product_select_new").val(),
                            product_price: $("#product_price_new").val(),
                            product_cost_new: $("#product_cost_new").val()

                        }, function (data) {
                            //console.log(data);
                            location.reload();
                        });
                    });
                    $("#category_form").submit(function (event) {
                        event.preventDefault();
                        $.post("<?php echo site_url('products/ajax/save_log'); ?>", {
                            cat_id: $("#cat_id").val(),
                            category_name: $("#category_name").val(),
                            category_select: $("#category_select_ex").val(),
                            category_tag: $("#category_tag").val()

                        }, function (data) {



                        });
                        $.post("<?php echo site_url('products/products/categories'); ?>", {
                            cat_id: $("#cat_id").val(),
                            category_name: $("#category_name").val(),
                            category_select: $("#category_select_ex").val(),
                            category_tag: $("#category_tag").val()
                        }, function (data) {

                            location.reload();
                        });
                    });
                    $("#products_form").submit(function (event) {
                        event.preventDefault();
                        $.post("<?php echo site_url('products/ajax/save_log'); ?>", {
                            prod_id: $("#prod_id").val(),
                            product_name: $("#product_name").val(),
                            category_prod_select: $("#category_prod_select").val(),
                            product_price: $("#product_price").val(),
                            product_cost: $("#product_cost").val()

                        }, function (data) {


                        });
                        $.post("<?php echo site_url('products/products/products_update'); ?>", {
                            prod_id: $("#prod_id").val(),
                            product_name: $("#product_name").val(),
                            category_prod_select: $("#category_prod_select").val(),
                            product_price: $("#product_price").val(),
                            product_cost: $("#product_cost").val()
                            
                        }, function (data) {

                            location.reload();
                        });
                    });
                    $("#new_category_form").submit(function (event) {

                        //event.preventDefault();
                        $.post("<?php echo site_url('products/products/categories'); ?>", {
                            cat_id: $("#cat_id_new").val(),
                            category_name: $("#category_name_new").val(),
                            category_select: $("#category_select_new").val(),
                            category_tag: $("#category_tag_new").val()
                        }, function (data) {
                            location.reload();
                        });
                    });
                    function new_category()
                    {
                        if ($("#aux").val() == 0) {
                            $("#new_category").show();
                            $("#product_div").hide();
                            $("#category_div").hide();
                            $("#new_product").hide();
                            $("#aux2").val(0);
                            $("#aux").val(1);
                        } else
                        {
                            $("#product_div").hide();
                            $("#category_div").hide();
                            $("#new_category").hide();
                            $("#new_product").hide();
                            $("#aux2").val(0);
                            $("#aux").val(0);
                        }
                    }
                    function delete_category(cat_id)
                    {
                        var retVal = confirm("Do you really want to delete this Category ?");
                        if (retVal == true) {
                            $.post("<?php echo site_url('products/ajax/delete_cat'); ?>", {
                                cat_id: cat_id
                            },
                                    function (data) {

                                        location.reload();
                                    });
                        } else {

                        }

                    }
                    webix.ready(function () {
                        var json_cat =<?php echo $categories ?>;
                        webix.DataDriver.plainjs = webix.extend({
                            arr2hash: function (data) {
                                var hash = {};
                                for (var i = 0; i < data.length; i++) {
                                    var pid = data[i].parent_id;
                                    if (!hash[pid])
                                        hash[pid] = [];
                                    hash[pid].push(data[i]);
                                }
                                return hash;
                            },
                            hash2tree: function (hash, level) {
                                var top = hash[level];
                                for (var i = 0; i < top.length; i++) {
                                    var branch = top[i].id;
                                    if (hash[branch])
                                        top[i].data = this.hash2tree(hash, branch);
                                }
                                return top;
                            },
                            getRecords: function (data, id) {
                                var hash = this.arr2hash(data);
                                return this.hash2tree(hash, 0);
                            }
                        }, webix.DataDriver.json);
                        //treedata = [{"id":"249","value":"Accessories"},{"id":"286","value":"acer m5"},{"id":"258","value":"Apple Product Sales"},{"id":"135","value":"Computer Repair Services"},{"id":"250","value":"Computer Sales"},{"id":"248","value":"Dell Venue 11"},{"id":"295","value":"Enjoy the structure"},{"id":"47","value":"Items"},{"id":"244","value":"Los Angeles Repair Services"},{"id":"131","value":"Mac Repairs"},{"id":"136","value":"Mobile Device Repairs"},{"id":"245","value":"Parts"},{"id":"196","value":"PC Repairs"},{"id":"249","value":"Accessories","data":{"id":"249.1776","value":"NXG Technology 4 Port USB Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1777","value":"NXG Technology 2port Compact Car Usb Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1690","value":"Monster Digital USB Flash Drive - 16GB - USB 2.0"}},{"id":"249","value":"Accessories","data":{"id":"249.1775","value":"NXG Technology High Fidelity Stereo Earbuds"}},{"id":"249","value":"Accessories","data":{"id":"249.1715","value":"Mophie - Juice Pack plus External Battery Case for Apple iPhone 5 and 5s"}},{"id":"249","value":"Accessories","data":{"id":"249.1716","value":"Griffin Technology - PowerJolt SE Lightning Vehicle Charger"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.435","value":"Laptop Repair Form"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1067","value":"Hard Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1068","value":"Optical Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1069","value":"Casing"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1070","value":"Power"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1071","value":"Memory"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1072","value":"Logic Boards"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1082","value":"Screens"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1691","value":"Microsoft Surface Pro 3"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1692","value":"Sony Vaio Tap 11 - SVT11213CXB"}},{"id":"248","value":"Dell Venue 11","data":{"id":"248.1726","value":"Appleizer test"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1683","value":"iPhone and iPad Repair in Hawthorne"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1684","value":"iPhone and iPad Repair in Venice"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1649","value":"Mac Repair Los Angeles"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1687","value":"Test product"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1060","value":"Mac Mini"}},{"id":"245","value":"Parts","data":{"id":"245.1696","value":"Samsung NP900X3D Screen Assembly"}},{"id":"245","value":"Parts","data":{"id":"245.1784","value":"Lenovo Yoga 2 UK keyboard"}},{"data":{"id":"258.259","value":"iPod Touch 5th Gen 2014"}},{"data":{"id":"135.214","value":"Logic Board Repair"}},{"data":{"id":"295.310","value":"test"}},{"data":{"id":"295.311","value":"testchild"}},{"data":{"id":"295.318","value":"Enjoy the structure Child 1"}},{"data":{"id":"47.27","value":"LCD - Displays"}},{"data":{"id":"47.34","value":"Specials"}},{"data":{"id":"47.247","value":"iPad Lots"}},{"data":{"id":"131.132","value":"MacBook Pro"}},{"data":{"id":"131.133","value":"Macbook"}},{"data":{"id":"131.134","value":"Macbook Air"}},{"data":{"id":"131.235","value":"Mac Mini"}},{"data":{"id":"131.243","value":"iMac"}},{"data":{"id":"131.271","value":"Power Adapters"}},{"data":{"id":"136.137","value":"HTC"}},{"data":{"id":"136.179","value":"Samsung"}},{"data":{"id":"136.139","value":"Apple"}},{"data":{"id":"245.239","value":"Lenovo Screens"}},{"data":{"id":"245.240","value":"Sony Vaio Displays"}},{"data":{"id":"245.241","value":"Lenovo U310 Touch"}},{"data":{"id":"245.242","value":"iMac Parts"}},{"data":{"id":"245.262","value":"Macbook Parts"}},{"data":{"id":"245.284","value":"Mac Parts"}},{"data":{"id":"196.197","value":"Samsung"}},{"data":{"id":"196.198","value":"Toshiba"}},{"data":{"id":"196.199","value":"Dell"}},{"data":{"id":"196.200","value":"Asus"}},{"data":{"id":"196.201","value":"Hewlett-Packard HP"}},{"data":{"id":"196.202","value":"Lenovo"}},{"data":{"id":"196.203","value":"Sony"}},{"data":{"id":"196.204","value":"Acer"}},{"data":{"id":"196.234","value":"Panasonic"}},{"data":{"id":"196.238","value":"MSI"}}];
                        tree = webix.ui({

                            container: "datatree_2",
                            view: "tree",
                            datatype: "plainjs",
                            select: "multiselect",
                            drag: true,
                            on: {
                                "onAfterDrop": function (id, e, trg) {
                                    var start = id.source;
                                    var parent = id.parent;
                                    var cat_prod = $("#cat_prod").val();
                                    //console.log(id);

                                    $.post("<?php echo site_url('products/ajax/update_parent'); ?>", {
                                        start: start,
                                        parent: parent
                                    },
                                            function (data) {
                                                console.log(data);
                                            });
                                },
                                //default click behavior that is true for any datatable cell
                                "onItemClick": function (id, e, trg) {


                                    $.post("<?php echo site_url('products/ajax/validate_cat'); ?>", {
                                        cat_prod_id: id
                                    },
                                            function (data) {
                                                var response = JSON.parse(data);
                                                if (response.category_id == null) {
                                                    $("#prod_id").val(id);
                                                    $("#product_name").val(response.product_name);
                                                    $("#product_price").val(response.product_price);
                                                    $("#product_cost").val(response.purchase_price);
                                                    $("#category_prod_select").val(response.pcategory_id);
                                                    $("#category_div").hide();
                                                    $("#new_category").hide();
                                                    $("#new_product").hide();
                                                    $("#aux").val(0);
                                                    $("#aux2").val(0);
                                                    $("#product_div").show();
                                                } else
                                                {
                                                    $("#cat_id").val(id);
                                                    $("#category_name").val(response.category_name);
                                                    $("#category_tag").val(response.category_tag);
                                                    $("#cat_prod_name").html("Category Name");
                                                    $("#category_select_ex").val(response.category_parent);
                                                    $("#product_div").hide();
                                                    $("#new_category").hide();
                                                    $("#new_product").hide();
                                                    $("#aux").val(0);
                                                    $("#aux2").val(0);
                                                    $("#category_div").show();
                                                }

                                            });
                                    //webix.message("Click on row: " + id);
                                }
                            },
                            ready: function () {
                                $("#datatree_2").hide();
                            },
                            data: json_cat

                        });
                    });
                    webix.ready(function () {
                        var json_cat =<?php echo $categories ?>;
                        webix.DataDriver.plainjs = webix.extend({
                            arr2hash: function (data) {
                                var hash = {};
                                for (var i = 0; i < data.length; i++) {
                                    var pid = data[i].parent_id;
                                    if (!hash[pid])
                                        hash[pid] = [];
                                    hash[pid].push(data[i]);
                                }
                                return hash;
                            },
                            hash2tree: function (hash, level) {
                                var top = hash[level];
                                for (var i = 0; i < top.length; i++) {
                                    var branch = top[i].id;
                                    if (hash[branch])
                                        top[i].data = this.hash2tree(hash, branch);
                                }
                                return top;
                            },
                            getRecords: function (data, id) {
                                var hash = this.arr2hash(data);
                                return this.hash2tree(hash, 0);
                            }
                        }, webix.DataDriver.json);
                        //treedata = [{"id":"249","value":"Accessories"},{"id":"286","value":"acer m5"},{"id":"258","value":"Apple Product Sales"},{"id":"135","value":"Computer Repair Services"},{"id":"250","value":"Computer Sales"},{"id":"248","value":"Dell Venue 11"},{"id":"295","value":"Enjoy the structure"},{"id":"47","value":"Items"},{"id":"244","value":"Los Angeles Repair Services"},{"id":"131","value":"Mac Repairs"},{"id":"136","value":"Mobile Device Repairs"},{"id":"245","value":"Parts"},{"id":"196","value":"PC Repairs"},{"id":"249","value":"Accessories","data":{"id":"249.1776","value":"NXG Technology 4 Port USB Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1777","value":"NXG Technology 2port Compact Car Usb Charger"}},{"id":"249","value":"Accessories","data":{"id":"249.1690","value":"Monster Digital USB Flash Drive - 16GB - USB 2.0"}},{"id":"249","value":"Accessories","data":{"id":"249.1775","value":"NXG Technology High Fidelity Stereo Earbuds"}},{"id":"249","value":"Accessories","data":{"id":"249.1715","value":"Mophie - Juice Pack plus External Battery Case for Apple iPhone 5 and 5s"}},{"id":"249","value":"Accessories","data":{"id":"249.1716","value":"Griffin Technology - PowerJolt SE Lightning Vehicle Charger"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.435","value":"Laptop Repair Form"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1067","value":"Hard Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1068","value":"Optical Drives"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1069","value":"Casing"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1070","value":"Power"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1071","value":"Memory"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1072","value":"Logic Boards"}},{"id":"135","value":"Computer Repair Services","data":{"id":"135.1082","value":"Screens"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1691","value":"Microsoft Surface Pro 3"}},{"id":"250","value":"Computer Sales","data":{"id":"250.1692","value":"Sony Vaio Tap 11 - SVT11213CXB"}},{"id":"248","value":"Dell Venue 11","data":{"id":"248.1726","value":"Appleizer test"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1683","value":"iPhone and iPad Repair in Hawthorne"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1684","value":"iPhone and iPad Repair in Venice"}},{"id":"244","value":"Los Angeles Repair Services","data":{"id":"244.1649","value":"Mac Repair Los Angeles"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1687","value":"Test product"}},{"id":"131","value":"Mac Repairs","data":{"id":"131.1060","value":"Mac Mini"}},{"id":"245","value":"Parts","data":{"id":"245.1696","value":"Samsung NP900X3D Screen Assembly"}},{"id":"245","value":"Parts","data":{"id":"245.1784","value":"Lenovo Yoga 2 UK keyboard"}},{"data":{"id":"258.259","value":"iPod Touch 5th Gen 2014"}},{"data":{"id":"135.214","value":"Logic Board Repair"}},{"data":{"id":"295.310","value":"test"}},{"data":{"id":"295.311","value":"testchild"}},{"data":{"id":"295.318","value":"Enjoy the structure Child 1"}},{"data":{"id":"47.27","value":"LCD - Displays"}},{"data":{"id":"47.34","value":"Specials"}},{"data":{"id":"47.247","value":"iPad Lots"}},{"data":{"id":"131.132","value":"MacBook Pro"}},{"data":{"id":"131.133","value":"Macbook"}},{"data":{"id":"131.134","value":"Macbook Air"}},{"data":{"id":"131.235","value":"Mac Mini"}},{"data":{"id":"131.243","value":"iMac"}},{"data":{"id":"131.271","value":"Power Adapters"}},{"data":{"id":"136.137","value":"HTC"}},{"data":{"id":"136.179","value":"Samsung"}},{"data":{"id":"136.139","value":"Apple"}},{"data":{"id":"245.239","value":"Lenovo Screens"}},{"data":{"id":"245.240","value":"Sony Vaio Displays"}},{"data":{"id":"245.241","value":"Lenovo U310 Touch"}},{"data":{"id":"245.242","value":"iMac Parts"}},{"data":{"id":"245.262","value":"Macbook Parts"}},{"data":{"id":"245.284","value":"Mac Parts"}},{"data":{"id":"196.197","value":"Samsung"}},{"data":{"id":"196.198","value":"Toshiba"}},{"data":{"id":"196.199","value":"Dell"}},{"data":{"id":"196.200","value":"Asus"}},{"data":{"id":"196.201","value":"Hewlett-Packard HP"}},{"data":{"id":"196.202","value":"Lenovo"}},{"data":{"id":"196.203","value":"Sony"}},{"data":{"id":"196.204","value":"Acer"}},{"data":{"id":"196.234","value":"Panasonic"}},{"data":{"id":"196.238","value":"MSI"}}];
                        tree = webix.ui({

                            container: "datatree",
                            view: "tree",
                            datatype: "plainjs",
                            select: "multiselect",
                            drag: true,
                            on: {
                                "onAfterDrop": function (id, e, trg) {
                                    var start = id.source;
                                    var parent = id.parent;
                                    var cat_prod = $("#cat_prod").val();
                                    //console.log(id);

                                    $.post("<?php echo site_url('products/ajax/update_parent'); ?>", {
                                        start: start,
                                        parent: parent
                                    },
                                            function (data) {
                                                console.log(data);
                                            });
                                },
                                //default click behavior that is true for any datatable cell
                                "onItemClick": function (id, e, trg) {


                                    $.post("<?php echo site_url('products/ajax/validate_cat'); ?>", {
                                        cat_prod_id: id
                                    },
                                            function (data) {
                                                var response = JSON.parse(data);
                                                if (response.category_id == null) {
                                                    $("#prod_id").val(id);
                                                    $("#product_name").val(response.product_name);
                                                    $("#product_price").val(response.product_price);
                                                    $("#product_cost").val(response.purchase_price);
                                                    $("#category_prod_select").val(response.pcategory_id);
                                                    $("#category_div").hide();
                                                    $("#new_category").hide();
                                                    $("#new_product").hide();
                                                    $("#aux").val(0);
                                                    $("#aux2").val(0);
                                                    $("#product_div").show();
                                                } else
                                                {
                                                    $("#cat_id").val(id);
                                                    $("#category_name").val(response.category_name);
                                                    $("#category_tag").val(response.category_tag);
                                                    $("#cat_prod_name").html("Category Name");
                                                    $("#category_select_ex").val(response.category_parent);
                                                    $("#product_div").hide();
                                                    $("#new_category").hide();
                                                    $("#new_product").hide();
                                                    $("#aux").val(0);
                                                    $("#aux2").val(0);
                                                    $("#category_div").show();
                                                }

                                            });
                                    //webix.message("Click on row: " + id);
                                }
                            },
                            ready: function () {
                            },
                            data: json_cat

                        });
                    });
                    var aux = 1;
                    var id_less;
                    function test(id)
                    {
                        id_less = id - 1;
                        if (aux === 1) {
                            if ($("#" + id).hide())
                            {

                                $("#minus_father" + id_less).hide();
                                $("#plus_father" + id_less).show();
                                aux = 0;
                            }
                        } else
                        {
                            $("#minus_father" + id_less).show();
                            $("#plus_father" + id_less).hide();
                            $("#" + id).show();
                            aux = 1;
                        }


                    }
                    $('.sortable').sortable();
</script>
<style>

    .webix_tree_folder{
        background-image: url('../../../../assets/default/img/Folder_3.png');
        background-position: 0px 4px;
        background-size: 20px 20px;
    }
    .webix_tree_folder_open{
        background-image: url('../../../../assets/default/img/Folder_3.png');
        background-position: 0px 4px;
        background-size: 20px 20px;
    }

</style>