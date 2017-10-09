<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Mdl_Item_Amounts extends Model
{
    /**
     * item_amount_id
     * item_id
     * item_subtotal (item_quantity * item_price)
     * item_tax_total
     * item_total ((item_quantity * item_price) + item_tax_total)
     */
    public function calculate($item_id)
    {
 
        $admin365 = $this->load->database("365admin", true);
        $this->load->model('invoices/mdl_items');
        $item = $this->mdl_items->get_by_id($item_id);

        $tax_rate_percent = 0;

        $item_subtotal = $item->item_quantity * $item->item_price;
        $item_tax_total = $item_subtotal * ($item->item_tax_rate_percent / 100);
        $item_discount_total = $item->item_discount_amount * $item->item_quantity;
        $item_total = $item_subtotal + $item_tax_total - $item_discount_total;

        $db_array = array(
            'item_id' => $item_id,
            'item_subtotal' => $item_subtotal,
            'item_tax_total' => $item_tax_total,
            'item_discount' => $item_discount_total,
            'item_total' => $item_total
        );

        $admin365->where('item_id', $item_id);
        if ($admin365->get('ip_invoice_item_amounts')->num_rows()) {
            $admin365->where('item_id', $item_id);
            $admin365->update('ip_invoice_item_amounts', $db_array);
        } else {
            $admin365->insert('ip_invoice_item_amounts', $db_array);
        }
    }
    

}
