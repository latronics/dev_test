<!--<button class=" btn btn-sm btn-default" type="button" onclick="ShowHide('item_information')">Show/Hide Items</button>-->
<div id="panel-quick-actions" class="panel panel-default" style="width:1095px; margin-left:0px;">
                    <div class="panel-heading" align="left">
                        <label for="ticket_content" ><?php echo "<b>Order Items</b>"; ?></label>
                    </div>
                    <br>
                    <?php if ($this->session->userdata("is_tech") == 0) { ?>
<div id = "item_information">
    
    <table id="item_table" class="items table table-condensed table-bordered" style="width:1087px; margin-top:-15px; margin-left:3px; margin-right:3px;">
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
                <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                <input type="hidden" name="item_id" value="">
                <input type="hidden" name="item_product_id" value="">

                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('item'); ?></span>
                    <input type="text" name="item_name" class="input-sm form-control" value="">
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                    <input type="text" name="item_quantity" class="input-sm form-control amount" value="">
                </div>
            </td>
            <td class="td-amount">
                <div class="input-group">
                    <span class="input-group-addon"><?php echo lang('price'); ?></span>
                    <input type="text" name="item_price" class="input-sm form-control amount" value="">
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
                    <textarea name="item_description" class="input-sm form-control"></textarea>
                </div>
            </td>
            
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
                    <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>">
                    <input type="hidden" name="item_product_id" value="<?php echo $item->item_product_id; ?>">

                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item'); ?></span>
                        <input type="text" name="item_name" class="input-sm form-control"
                               value="<?php echo html_escape($item->item_name); ?>">
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('quantity'); ?></span>
                        <input type="text" name="item_quantity" class="input-sm form-control amount"
                               value="<?php echo format_amount($item->item_quantity); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('price'); ?></span>
                        <input type="text" name="item_price" class="input-sm form-control amount"
                               value="<?php echo format_amount($item->item_price); ?>">
                    </div>
                </td>
                <td class="td-amount ">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('item_discount'); ?></span>
                        <input type="text" name="item_discount_amount" class="input-sm form-control amount"
                               value="<?php echo format_amount($item->item_discount_amount); ?>"
                               data-toggle="tooltip" data-placement="bottom"
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
                                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                                        <?php if ($item->item_tax_rate_id == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>>
                                    <?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle">
                    <a href="<?php echo site_url('quotes/delete_item/' . $quote->quote_id . '/' . $item->item_id); ?>"
                       title="<?php echo lang('delete'); ?>">
                        <i class="fa fa-trash-o text-danger"></i>
                    </a>
                </td>
            </tr>
            <tr>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo lang('description'); ?></span>
                        <textarea name="item_description"
                                  class="input-sm form-control"><?php echo $item->item_description; ?></textarea>
                    </div>
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
</div>

<div class="row" style="margin-left:5px;">
    <div class="col-xs-12 col-md-4">
        <div class="btn-group">
            <a href="#" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i>
                <?php echo lang('add_new_row'); ?>
            </a>
            <a href="#" class="btn_add_product btn btn-sm btn-default">
                <i class="fa fa-database"></i>
                <?php echo lang('add_product'); ?>
            </a>
        </div>
        <br/><br/>
    </div>
    <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
        <table class="table table-condensed text-right">
            <tr>
                <td style="width: 40%;"><?php echo lang('subtotal'); ?></td>
                <td style="width: 60%;" class="amount"><?php echo format_currency($quote->quote_item_subtotal); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('item_tax'); ?></td>
                <td class="amount"><?php echo format_currency($quote->quote_item_tax_total); ?></td>
            </tr>
            <tr>
                <td><?php echo lang('quote_tax'); ?></td>
                <td>
                    <?php if ($quote_tax_rates) {
                        foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                            <span class="text-muted">
                            <?php echo anchor('quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quote_tax_rate->quote_tax_rate_id, '<i class="fa fa-trash-o"></i>');
                            echo ' ' . $quote_tax_rate->quote_tax_rate_name . ' ' . $quote_tax_rate->quote_tax_rate_percent; ?>
                                %</span>&nbsp;
                            <span class="amount">
                                <?php echo format_currency($quote_tax_rate->quote_tax_rate_amount); ?>
                            </span>
                        <?php }
                    } else {
                        echo format_currency('0');
                    } ?>
                </td>
            </tr>
            <tr>
                <td class="td-vert-middle"><?php echo lang('discount'); ?></td>
                <td class="clearfix">
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_amount" name="quote_discount_amount"
                                   class="discount-option form-control input-sm amount"
                                   value="<?php echo($quote->quote_discount_amount != 0 ? $quote->quote_discount_amount : ''); ?>">

                            <div
                                class="input-group-addon"><?php echo $this->mdl_settings->setting('currency_symbol'); ?></div>
                        </div>
                    </div>
                    <div class="discount-field">
                        <div class="input-group input-group-sm">
                            <input id="quote_discount_percent" name="quote_discount_percent"
                                   value="<?php echo($quote->quote_discount_percent != 0 ? $quote->quote_discount_percent : ''); ?>"
                                   class="discount-option form-control input-sm amount">

                            <div class="input-group-addon">&percnt;</div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><b><?php echo lang('total'); ?></b></td>
                <?php  //GET TICKET PRICE
             
                $this->db->select("amount");
                $this->db->from("ip_quotes");
                $this->db->where("quote_id", $quote->quote_id);
                $ticket_amount_get = $this->db->get();
                $ticket_amount = $ticket_amount_get->result_array();
                
                
                ?>
                <td class="amount"><b><?php echo format_currency($quote->quote_total); ?></b></td>
            </tr>
        </table>
    </div>
</div>
                    <?php } ?>
                </div>

