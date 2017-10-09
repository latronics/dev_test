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

class Mdl_Quotes extends Response_Model {

    public $table = 'ip_quotes';
    public $primary_key = 'ip_quotes.quote_id';
    public $date_modified_field = 'quote_date_modified';

    public function statuses() {
        return array(
            '1' => array(
                'label' => lang('diagnosing'),
                'class' => 'diagnosing',
                'href' => 'quotes/status/diagnosing'
            ),
            '2' => array(
                'label' => 'Waiting on approval',
                'class' => 'waiting_on_approval',
                'href' => 'quotes/status/waiting_on_approval'
            ),
            '3' => array(
                'label' => 'Ordered parts',
                'class' => 'ordered_parts',
                'href' => 'quotes/status/ordered_parts'
            ),
            '4' => array(
                'label' => lang('repairing'),
                'class' => 'repairing ',
                'href' => 'quotes/status/repairing'
            ),
            '5' => array(
                'label' => 'Repair completed',
                'class' => 'repair_completed ',
                'href' => 'quotes/status/repair_completed '
            ),
            '6' => array(
                'label' => "Accepted by client",
                'class' => 'accepted_by_client ',
                'href' => 'quotes/status/accepted_by_client '
            ),
            '7' => array(
                'label' => lang('new_order'),
                'class' => 'new_order',
                'href' => 'quotes/status/new_order'
            ),
            '8' => array(
                'label' => "Waiting for package",
                'class' => 'waiting_for_package',
                'href' => 'quotes/status/waiting_for_package'
            ),
            '9' => array(
                'label' => "Repair denied",
                'class' => 'repair_denied ',
                'href' => 'quotes/status/repair_denied'
            ),
            '10' => array(
                'label' => 'Returned to shop',
                'class' => 'returned_to_shop ',
                'href' => 'quotes/status/returned_to_shop '
            ),
            '27' => array(
                'label' => 'Update',
                'class' => 'update',
                'href' => 'quotes/status/update'
            ),
            '28' => array(
                'label' => 'Payment',
                'class' => 'payment',
                'href' => 'quotes/status/payment'
            ),
            '18' => array(
                'label' => 'Received',
                'class' => 'received',
                'href' => 'quotes/status/received'
            ),
            '17' => array(
                'label' => 'Order shipped',
                'class' => 'order_shipped',
                'href' => 'quotes/status/order_shipped'
            ),
            '21' => array(
                'label' => 'Performing diagnostic',
                'class' => 'performing_diagnostic',
                'href' => 'quotes/status/performing_diagnostic'
            ),
            '19' => array(
                'label' => 'Send Shipping item',
                'class' => 'send_shipping_item',
                'href' => 'quotes/status/send_shipping_item'
            )
            /* ,
                  '11' => array(
                  'label' => lang('draft'),
                  'class' => 'draft',
                  'href' => 'quotes/status/draft'
                  ),
                  '12' => array(
                  'label' => lang('sent'),
                  'class' => 'sent',
                  'href' => 'quotes/status/sent'
                  ),
                  '13' => array(
                  'label' => lang('viewed'),
                  'class' => 'viewed',
                  'href' => 'quotes/status/viewed'
                  ),
                  '14' => array(
                  'label' => lang('approved'),
                  'class' => 'approved',
                  'href' => 'quotes/status/approved'
                  ),
                  '15' => array(
                  'label' => lang('rejected'),
                  'class' => 'rejected',
                  'href' => 'quotes/status/rejected'
                  ),
                  '16' => array(
                  'label' => lang('canceled'),
                  'class' => 'canceled',
                  'href' => 'quotes/status/canceled'
                  ) */
        );
    }

    public function store_id() {
        if ($this->session->userdata('user_id') != "") {
            $CI = &get_instance();
            $CI->load->database();

            $connect = mysql_connect($CI->db->hostname, $CI->db->username, $CI->db->password);
            $db = mysql_select_db($CI->db->database);
            $select_user_store = mysql_query("SELECT user_store FROM ip_users where user_id = " . $this->session->userdata('user_id')) or die("Session Out");
            $user_store = mysql_result($select_user_store, 0, 0);
            return $user_store;
        }
    }
 
    public function default_select() {



        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_quote_custom.*,
            ip_client_custom.*,
            ip_user_custom.*,
            ip_users.user_name,
			ip_users.user_company,
			ip_users.user_address_1,
			ip_users.user_address_2,
			ip_users.user_city,
			ip_users.user_state,
			ip_users.user_zip,
			ip_users.user_country,
			ip_users.user_phone,
			ip_users.user_fax,
			ip_users.user_mobile,
			ip_users.user_email,
			ip_users.user_web,
			ip_users.user_vat_id,
			ip_users.user_tax_code,
			ip_clients.*,
                        ip_quotes.*,
			ip_quote_amounts.quote_amount_id,
			IFnull(ip_quote_amounts.quote_item_subtotal, '0.00') AS quote_item_subtotal,
			IFnull(ip_quote_amounts.quote_item_tax_total, '0.00') AS quote_item_tax_total,
			IFnull(ip_quote_amounts.quote_tax_total, '0.00') AS quote_tax_total,
			IFnull(ip_quote_amounts.quote_total, '0.00') AS quote_total,
            ip_invoices.invoice_number,
			ip_quotes.*", false);
        $this->db->where("ip_quotes.fraud", 0);
     
        if ($this->store_id() != 1) {
            $this->db->where("ip_quotes.store", $this->store_id());
        }
      
        
          
       
      
       
    }
    
    public function website_saleid()
    {
        $this->db->where("store_name", "Website Sale");
        $website_sale = $this->db->get("ip_stores")->result_object();
        return $website_sale[0]->id;
    }
     public function website_repairid()
    {
        $this->db->where("store_name", "Website Repair");
        $website_repair = $this->db->get("ip_stores")->result_object();
        return $website_repair[0]->id;
    }
    
    public function store_name()
    {
        $this->db->where("id", $this->store_id());
        $store_data = $this->db->get("ip_stores")->result_object();
        return $store_data[0]->store_name;
    }

    public function default_order_by() {
        $this->db->order_by('ip_quotes.quote_id DESC');
    }

    public function default_join() {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_quotes.client_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_quotes.user_id');
        $this->db->join('ip_quote_amounts', 'ip_quote_amounts.quote_id = ip_quotes.quote_id', 'left');
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_quotes.invoice_id', 'left');
        $this->db->join('ip_client_custom', 'ip_client_custom.client_id = ip_clients.client_id', 'left');
        $this->db->join('ip_user_custom', 'ip_user_custom.user_id = ip_users.user_id', 'left');
        $this->db->join('ip_quote_custom', 'ip_quote_custom.quote_id = ip_quotes.quote_id', 'left');
    }

    public function validation_rules() {


        return array(
            'client_name' => array(
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ),
            'quote_date_created' => array(
                'field' => 'quote_date_created',
                'label' => lang('quote_date'),
                'rules' => 'required'
            ),
            'invoice_group_id' => array(
                'field' => 'invoice_group_id',
                'label' => lang('quote_group'),
                'rules' => 'required'
            ),
            'order_type' => array(
                'field' => 'order_type',
                'label' => lang('order_type')
            ),
            'store' => array(
                'field' => 'store',
                'label' => lang('store')
            ),
            'amount' => array(
                'field' => 'amount',
                'label' => lang('amount')
            )
            ,
            'brand' => array(
                'field' => 'brand',
                'label' => lang('brand')
            ),
            'model' => array(
                'field' => 'model',
                'label' => lang('model')
            ),
            'serial_number' => array(
                'field' => 'serial_number',
                'label' => lang('serial_number')
            ),
            'data_recovery' => array(
                'field' => 'data_recovery',
                'label' => lang('data_recovery')
            ),
            'client_os_password' => array(
                'field' => 'client_os_password',
                'label' => lang('client_os_password')
            ),
            'accessories_included' => array(
                'field' => 'accessories_included',
                'label' => lang('accessories_included')
            ),
            'data_recovery_yes' => array(
                'field' => 'data_recovery_yes',
                'label' => lang('data_recovery_yes')
            )
            ,
            'problem_description_product' => array(
                'field' => 'problem_description_product',
                'label' => lang('problem_description_product')
            ),
            'quote_password' => array(
                'field' => 'quote_password',
                'label' => lang('quote_password')
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => lang('user'),
                'rule' => 'required'
            )
        );
    }

    public function validation_rules_save_quote() {
        return array(
            'quote_number' => array(
                'field' => 'quote_number',
                'label' => lang('quote') . ' #',
                'rules' => 'is_unique[ip_quotes.quote_number' . (($this->id) ? '.quote_id.' . $this->id : '') . ']'
            ),
            'quote_date_created' => array(
                'field' => 'quote_date_created',
                'label' => lang('date'),
                'rules' => 'required'
            ),
            'quote_date_expires' => array(
                'field' => 'quote_date_expires',
                'label' => lang('due_date'),
                'rules' => 'required'
            ),
            'order_type' => array(
                'field' => 'order_type',
                'label' => lang('order_type')
            ),
            'amount' => array(
                'field' => 'amount',
                'label' => lang('amount')
            ),
            'store' => array(
                'field' => 'store',
                'label' => lang('store')
            ),
            'brand' => array(
                'field' => 'brand',
                'label' => lang('brand')
            ),
            'model' => array(
                'field' => 'model',
                'label' => lang('model')
            ),
            'serial_number' => array(
                'field' => 'serial_number',
                'label' => lang('serial_number')
            ),
            'data_recovery' => array(
                'field' => 'data_recovery',
                'label' => lang('data_recovery')
            ),
            'client_os_password' => array(
                'field' => 'client_os_password',
                'label' => lang('client_os_password')
            ),
            'accessories_included' => array(
                'field' => 'accessories_included',
                'label' => lang('accessories_included')
            ),
            'problem_description_product' => array(
                'field' => 'problem_description_product',
                'label' => lang('problem_description_product')
            ),
            'quote_password' => array(
                'field' => 'quote_password',
                'label' => lang('quote_password')
            )
        );
    }

    public function create($db_array = null) {
        $quote_id = parent::save(null, $db_array);

        // Create an quote amount record
        $db_array = array(
            'quote_id' => $quote_id
        );
       
        $this->db->insert('ip_quote_amounts', $db_array);

        // Create the default invoice tax record if applicable
        if ($this->mdl_settings->setting('default_invoice_tax_rate')) {
            $db_array = array(
                'quote_id' => $quote_id,
                'tax_rate_id' => $this->mdl_settings->setting('default_invoice_tax_rate'),
                'include_item_tax' => $this->mdl_settings->setting('default_include_item_tax'),
                'quote_tax_rate_amount' => 0
            );

            $this->db->insert('ip_quote_tax_rates', $db_array);
        }

        return $quote_id;
    }

    public function get_url_key() {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    /**
     * Copies quote items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_quote($source_id, $target_id) {
        $this->load->model('quotes/mdl_quote_items');

        $quote_items = $this->mdl_quote_items->where('quote_id', $source_id)->get()->result();

        foreach ($quote_items as $quote_item) {
            $db_array = array(
                'quote_id' => $target_id,
                'item_tax_rate_id' => $quote_item->item_tax_rate_id,
                'item_name' => $quote_item->item_name,
                'item_description' => $quote_item->item_description,
                'item_quantity' => $quote_item->item_quantity,
                'item_price' => $quote_item->item_price,
                'item_order' => $quote_item->item_order
            );

            $this->mdl_quote_items->save(null, $db_array);
        }

        $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $source_id)->get()->result();

        foreach ($quote_tax_rates as $quote_tax_rate) {
            $db_array = array(
                'quote_id' => $target_id,
                'tax_rate_id' => $quote_tax_rate->tax_rate_id,
                'include_item_tax' => $quote_tax_rate->include_item_tax,
                'quote_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount
            );

            $this->mdl_quote_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_quote_custom');
        $db_array = $this->mdl_quote_custom->where('quote_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['quote_custom_id']);
            $db_array['quote_id'] = $target_id;
            $this->mdl_quote_custom->save_custom($target_id, $db_array);
        }
    }

    public function db_array() {




        $db_array = parent::db_array();

        // Get the client id for the submitted quote
        $this->load->model('clients/mdl_clients');
        $db_array['client_id'] = $this->mdl_clients->client_lookup($db_array['client_name']);
        unset($db_array['client_name']);

        $db_array['quote_date_created'] = date_to_mysql($db_array['quote_date_created']);
        $db_array['quote_date_expires'] = $this->get_date_due($db_array['quote_date_created']);

        $db_array['notes'] = $this->mdl_settings->setting('default_quote_notes');

        //SELECT TO GET THE LAST ID FROM THE TICKES(QUOTES)
        $this->db->select('quote_number');
        $this->db->order_by('quote_number', 'desc');
        $this->db->limit(1);
        $db_table = $this->db->get('ip_quotes');
        $data = $db_table->result_array();
        //END HERE//



        if (!isset($db_array['quote_status_id'])) {
            $db_array['quote_status_id'] = 1;
        }

        $generate_quote_number = $this->mdl_settings->setting('generate_quote_number_for_draft');

        if ($db_array['quote_status_id'] === 1 && $generate_quote_number === 1) {
            $db_array['quote_number'] = $this->get_quote_number($db_array['invoice_group_id']);
        } elseif ($db_array['quote_status_id'] != 1) {
            $db_array['quote_number'] = $this->get_quote_number($db_array['invoice_group_id']);
        } else {
            $db_array['quote_number'] = $data[0]['quote_number'] + 1;
        }

        // Generate the unique url key
        $db_array['quote_url_key'] = $this->get_url_key();

        return $db_array;
    }

    public function get_invoice_group_id($invoice_id) {
        $invoice = $this->get_by_id($invoice_id);
        return $invoice->invoice_group_id;
    }

    public function get_quote_number($invoice_group_id) {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }

    public function get_date_due($quote_date_created) {
        $quote_date_expires = new DateTime($quote_date_created);
        $quote_date_expires->add(new DateInterval('P' . $this->mdl_settings->setting('quotes_expire_after') . 'D'));
        return $quote_date_expires->format('Y-m-d');
    }

    public function delete($quote_id) {
        parent::delete($quote_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    public function is_diagnosing() {
        $this->filter_where('quote_status_id', 1);
        return $this;
    }



    public function is_waiting_on_approval() {
        $this->filter_where('quote_status_id', 2);
        return $this;
    }

    public function is_ordered_parts() {
        $this->filter_where('quote_status_id', 3);
        return $this;
    }

    public function is_repairing() {
        $this->filter_where('quote_status_id', 4);
        return $this;
    }

    public function is_repair_completed() {
        $this->filter_where('quote_status_id', 5);
        return $this;
    }

    public function is_accepted_by_client() {
        $this->filter_where('quote_status_id', 6);
        return $this;
    }

    public function is_new_order() {
        $this->filter_where('quote_status_id', 7);
        return $this;
    }

    public function is_waiting_for_package() {
        $this->filter_where('quote_status_id', 8);
        return $this;
    }

    public function is_repair_denied() {
        $this->filter_where('quote_status_id', 9);
        return $this;
    }

    public function is_returned_to_shop() {
        $this->filter_where('quote_status_id', 10);
        return $this;
    }
      public function is_update() {
        $this->filter_where('quote_status_id', 27);
        return $this;
    }
       public function is_payment() {
        $this->filter_where('quote_status_id', 28);
        return $this;
    }
     public function is_received() {
        $this->filter_where('quote_status_id', 18);
        return $this;
    }
    public function is_order_shipped() {
        $this->filter_where('quote_status_id', 17);
        return $this;
    }
    /*
      public function is_draft()
      {
      $this->filter_where('quote_status_id', 11);
      return $this;
      }

      public function is_sent()
      {
      $this->filter_where('quote_status_id', 12);
      return $this;
      }

      public function is_viewed()
      {
      $this->filter_where('quote_status_id', 13);
      return $this;
      }

      public function is_approved()
      {
      $this->filter_where('quote_status_id', 14);
      return $this;
      }

      public function is_rejected()
      {
      $this->filter_where('quote_status_id', 15);
      return $this;
      }

      public function is_canceled()
      {
      $this->filter_where('quote_status_id', 16);
      return $this;
      }
     */

    // Used by guest module; includes only sent and viewed
    public function is_open() {
        $this->filter_where_in('quote_status_id', array(2, 3));
        return $this;
    }

    public function guest_visible() {
        $this->filter_where_in('quote_status_id', array(2, 3, 4, 5));
        return $this;
    }

    public function by_client($client_id) {
        $this->filter_where('ip_quotes.client_id', $client_id);
        return $this;
    }

    public function approve_quote_by_key($quote_url_key) {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_url_key', $quote_url_key);
        $this->db->set('quote_status_id', 4);
        $this->db->update('ip_quotes');
    }

    public function reject_quote_by_key($quote_url_key) {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_url_key', $quote_url_key);
        $this->db->set('quote_status_id', 5);
        $this->db->update('ip_quotes');
    }

    public function approve_quote_by_id($quote_id) {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_id', $quote_id);
        $this->db->set('quote_status_id', 4);
        $this->db->update('ip_quotes');
    }

    public function reject_quote_by_id($quote_id) {
        $this->db->where_in('quote_status_id', array(2, 3));
        $this->db->where('quote_id', $quote_id);
        $this->db->set('quote_status_id', 5);
        $this->db->update('ip_quotes');
    }

    public function mark_viewed($quote_id) {
        $this->db->select('quote_status_id');
        $this->db->where('quote_id', $quote_id);

        $quote = $this->db->get('ip_quotes');

        if ($quote->num_rows()) {
            if ($quote->row()->quote_status_id == 2) {
                $this->db->where('quote_id', $quote_id);
                $this->db->set('quote_status_id', 3);
                $this->db->update('ip_quotes');
            }
        }
    }

    public function mark_sent($quote_id) {
        $this->db->select('quote_status_id');
        $this->db->where('quote_id', $quote_id);

        $quote = $this->db->get('ip_quotes');

        if ($quote->num_rows()) {
            if ($quote->row()->quote_status_id == 1) {
                $this->db->where('quote_id', $quote_id);
                $this->db->set('quote_status_id', 2);
                $this->db->update('ip_quotes');
            }
        }
    }

}
