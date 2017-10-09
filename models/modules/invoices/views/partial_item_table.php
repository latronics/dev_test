<?php
//GET STORE
$this->db->where("id", $this->session->userdata("user_store"));
$store = $this->db->get("ip_stores")->result_object();
$store = $store[0]->store_name;
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div id="panel-quick-actions" class="panel panel-default" style="width:1370px; margin-top:-15px; float:left; overflow: auto;">
                                    <div class="panel-heading" align="left">
                                        <label for="ticket_content" ><?php echo "<b>Invoice Items</b>"; ?></label>
                                        <img src="../../../../assets/default/img/page-loader.gif" id="cost_loader" style="height:35px; height:35px; float:right;" title="Saving..." hidden/>
                                    </div>
                                    <div >
                                    <table id="item_table" class="items table-bordered" >
        <thead style="display: none">
            <tr>
                <th></th>
                <th><?php echo lang('item'); ?></th>
                <th><?php echo lang('description'); ?></th>
                <th><?php echo lang('quantity'); ?></th>
                <th><?php echo lang('price'); ?></th>
                <th><?php echo lang('tax_rate'); ?></th>
                <th><?php echo lang('subtotal'); ?></th>
                <th><?php echo lang('tax'); ?></th>
                <th><?php echo lang('total'); ?></th>
                <th></th>
            </tr>
        </thead>

        <tbody id="new_row" style="display: none;">
            <tr>
                <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
                <td class="td-text">
                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                    <input type="hidden" name="item_id" value="">
                    <input type="hidden" name="item_product_id" value="">

                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item'); ?></span>
                        <input type="text" name="item_name" id="item_name"  class="item_name input-sm form-control" value="" >
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                        <input type="text" name="item_quantity" id="item_quantity"  class="item_quantity input-sm form-control amount" value="">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('price'); ?></span>
                        <input type="text" name="item_price" id="item_price" class="item_price input-sm form-control amount" value="">
                    </div>
                </td>
                <td class="td-amount ">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                        <input type="text" name="item_discount_amount" class="input-sm form-control amount"
                               value="" data-toggle="tooltip" data-placement="bottom"
                               title="<?php echo $this->mdl_settings->setting('currency_symbol') . ' ' . lang('per_item'); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('tax_rate'); ?></span>
                        <select name="item_tax_rate_id" name="item_tax_rate_id"
                                class="form-control input-sm">
                            <option value="0"><?php echo lang('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>">
                                    <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle"></td>
            </tr>
            <tr>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('description'); ?></span>
                        <textarea name="item_description" id="item_description" class="item_description input-sm form-control"></textarea>
                    </div>
                </td>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo "Cost"; ?></span>

                        <input type="text" name="item_cost" id='cost' class='form-control'>
                    </div>
                </td>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo "Cost Description"; ?></span>
                        <input type="text" name="cost_description" id='cost_description' class='form-control' value='<?php echo $item->cost_description; ?>'>
                    </div>
                </td>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo "Cost Category"; ?></span>
                        <input type="text" name="cost_category" id='cost_category' class='form-control' value='<?php echo $item->cost_category; ?>'>
                    </div>
                </td>
                <?php if (($store == 'Website Repair') || ($store == 'Website Sale')) { ?>
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo "Amendment "; ?></span>

                            <input type="checkbox" id ="amendment" name="amendment"/>
                        </div>
                    </td>
                <?php } ?>
                <td colspan="2" class="td-amount td-vert-middle">
                    <span><?php echo lang('subtotal'); ?></span><br/>
                    <span name="subtotal" class="amount"></span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('discount'); ?></span><br/>
                    <span name="item_discount_total" class="amount"></span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('tax'); ?></span><br/>
                    <span name="item_tax_total" class="amount"></span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?php echo lang('total'); ?></span><br/>
                    <span name="item_total" class="amount"></span>
                </td>
            </tr>
        </tbody>

        <?php foreach ($items as $item) { ?>
            <tbody class="item">
                <tr>
                    <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
                    <td class="td-text">
                        <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                        <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>"
                        <?php
                        if ($invoice->is_read_only == 1) {
                            echo 'disabled="disabled"';
                        }
                        ?>>
                        <input type="hidden" name="item_product_id" value="<?php echo $item->item_product_id; ?>">

                        <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('item'); ?></span>
                            <input type="text" name="item_name" id="item_name" class="item_name input-sm form-control"
                                   value="<?php echo html_escape($item->item_name); ?>"
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>
                        </div>
                    </td>
                    <td class="td-amount td-quantity">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                            <input type="text" name="item_quantity" id="item_quantity" class="item_quantity input-sm form-control amount"
                                   value="<?php echo format_amount($item->item_quantity); ?>"
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>
                        </div>
                    </td>
                    <td class="td-amount">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('price'); ?></span>
                            <input type="text" name="item_price" id="item_price" class="item_price input-sm form-control amount"
                                   value="<?php echo format_amount($item->item_price); ?>"
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>
                        </div>
                    </td>
                    <td class="td-amount ">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                            <input type="text" name="item_discount_amount" class="input-sm form-control amount"
                                   value="<?php echo format_amount($item->item_discount_amount); ?>"
                                   data-toggle="tooltip" data-placement="bottom"
                                   title="<?php echo $this->mdl_settings->setting('currency_symbol') . ' ' . lang('per_item'); ?>"
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>
                        </div>
                    </td>
                    <td class="td-amount">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('tax_rate'); ?></span>
                            <select name="item_tax_rate_id" name="item_tax_rate_id"
                                    class="form-control input-sm"
                                    <?php
                                    if ($invoice->is_read_only == 1) {
                                        echo 'disabled="disabled"';
                                    }
                                    ?>>
                                <option value="0"><?php echo lang('none'); ?></option>
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                            <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>>
                                                <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td class="td-icon text-right td-vert-middle">
                        
                        <?php if ($invoice->is_read_only != 1): ?>
                            <a href="<?php echo site_url('invoices/delete_item/' . $invoice->invoice_id . '/' . $item->item_id); ?>"
                               title="<?php echo lang('delete'); ?>">
                                <i class="fa fa-trash-o text-danger"></i>
                            </a>
                        <?php endif; ?>
                        
                    </td>
                    <td colspan="2" align="center">
                        <label href="#" class="btn-process btn btn-sm" id="save_costs">Save Costs</label>
                    </td>
                    
                </tr>
                <tr>
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('description'); ?></span>
                            <textarea name="item_description" id="item_description"
                                      class="item_description input-sm form-control"
                                      <?php
                                      if ($invoice->is_read_only == 1) {
                                          echo 'disabled="disabled"';
                                      }
                                      ?>><?php echo $item->item_description; ?></textarea>
                        </div>
                    </td>
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo "Cost"; ?></span>
                            <input type="text" name="item_cost" id='cost' class='form-control' value='<?php echo format_amount($item->item_cost); ?>'>
                        </div>
                    </td>
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo "Cost Description"; ?></span>
                            <input type="text" name="cost_description" id='cost_description' class='form-control' value='<?php echo $item->cost_description; ?>'>
                        </div>
                    </td>
                    <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo "Cost Category"; ?></span>
                        <input type="text" name="cost_category" id='cost_category' class='form-control' value='<?php echo $item->cost_category; ?>'>
                    </div>
                </td>
                    <?php if (($store == 'Website Repair') || ($store == 'Website Sale')) { ?>
                        <td class="td-textarea">
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo "Amendment "; ?></span>

                                <input type="checkbox" id ="amendment" name="amendment"/>
                            </div>
                        <?php } ?>
                    </td>
                    <td colspan="2" class="td-amount td-vert-middle">
                        <span><?php echo lang('subtotal'); ?></span><br/>
                        <span name="subtotal" class="amount">
                            <?php echo format_currency($item->item_subtotal); ?>
                        </span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?php echo lang('discount'); ?></span><br/>
                        <span name="item_discount_total" class="amount">
                            <?php echo format_currency($item->item_discount); ?>
                        </span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?php echo lang('tax'); ?></span><br/>
                        <span name="item_tax_total" class="amount">
                            <?php echo format_currency($item->item_tax_total); ?>
                        </span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?php echo lang('total'); ?></span><br/>
                        <span name="item_total" class="amount">
                            <?php echo format_currency($item->item_total); ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        <?php } ?>

    </table>
    <div class="col-xs-12 col-md-4" style="margin-top:10px;">
        <div class="btn-group">
            <?php if ($invoice->is_read_only != 1) { ?>
                <a href="#" class="btn_add_row btn btn-sm btn-default">
                    <i class="fa fa-plus"></i> <?php echo lang('add_new_row'); ?>
                </a>
                <a href="#" class="btn_add_product btn btn-sm btn-default">
                    <i class="fa fa-database"></i>
                    <?php echo lang('add_product'); ?>
                </a>
            <?php } ?>
        </div>
        <br/><br/>
    </div>

    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-condensed text-right">
            <tr>
                <td style="width: 40%;"><?php echo lang('subtotal'); ?></td>
                <td style="width: 60%;"
                    class="amount"><?php echo format_currency($invoice->invoice_item_subtotal); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('item_tax'); ?></td>
                <td class="amount"><?php echo format_currency($invoice->invoice_item_tax_total); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('invoice_tax'); ?></td>
                <td>
                    <?php
                    if ($invoice_tax_rates) {
                        foreach ($invoice_tax_rates as $invoice_tax_rate) {
                            ?>
                            <span class="text-muted">
                                <?php
                                echo anchor('invoices/delete_invoice_tax/' . $invoice->invoice_id . '/' . $invoice_tax_rate->invoice_tax_rate_id, '<i class="fa fa-trash-o"></i>');
                                echo ' ' . $invoice_tax_rate->invoice_tax_rate_name . ' ' . $invoice_tax_rate->invoice_tax_rate_percent;
                                ?>
                                %</span>&nbsp;
                            <span class="amount">
                                <?php echo format_currency($invoice_tax_rate->invoice_tax_rate_amount); ?>
                            </span>
                            <?php
                        }
                    } else {
                        echo format_currency('0');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="td-vert-middle"><?php echo lang('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_amount" name="invoice_discount_amount"
                                   class="discount-option form-control input-sm amount"
                                   value="<?php echo($invoice->invoice_discount_amount != 0 ? $invoice->invoice_discount_amount : ''); ?>"
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>

                            <div
                                class="input-group-addon"><?php echo $this->mdl_settings->setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="invoice_discount_percent" name="invoice_discount_percent"
                                   value="<?php echo($invoice->invoice_discount_percent != 0 ? $invoice->invoice_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount"
                                   <?php
                                   if ($invoice->is_read_only == 1) {
                                       echo 'disabled="disabled"';
                                   }
                                   ?>>
                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo lang('total'); ?></td>
                <td class="amount"><b><?php echo format_currency($invoice->invoice_total); ?></b></td>
            </tr>
            <tr>
                <td><?php echo lang('paid'); ?></td>
                <td class="amount"><b><?php echo format_currency($invoice->invoice_paid); ?></b></td>
            </tr>
            <tr>
                <td><b><?php echo lang('balance'); ?></b></td>
                <td class="amount"><b><?php echo format_currency($invoice->invoice_balance); ?></b></td>
            </tr>
        </table>
    </div>

                                    </div>
                                </div>




