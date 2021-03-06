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

class mdl_orders extends Model {

    public $admin365_db;
    public $db;
    public $table = 'ip_invoices';
    public $primary_key = 'ip_invoices.invoice_id';
    public $date_modified_field = 'invoice_date_modified';

    function __construct() {
        parent::__construct();

        $this->admin365_db = $this->load->database("365admin", true);
    }

    public function statuses() {
        return array(
            '1' => array(
                'label' => lang('draft'),
                'class' => 'draft',
                'href' => 'invoices/status/draft'
            ),
            '2' => array(
                'label' => lang('sent'),
                'class' => 'sent',
                'href' => 'invoices/status/sent'
            ),
            '3' => array(
                'label' => lang('viewed'),
                'class' => 'viewed',
                'href' => 'invoices/status/viewed'
            ),
            '4' => array(
                'label' => lang('paid'),
                'class' => 'paid',
                'href' => 'invoices/status/paid'
            )
        );
    }

    public function default_select() {
        $this->admin365_db->select("
            SQL_CALC_FOUND_ROWS ip_invoice_custom.*,
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
			ip_invoice_amounts.invoice_amount_id,
			IFnull(ip_invoice_amounts.invoice_item_subtotal, '0.00') AS invoice_item_subtotal,
			IFnull(ip_invoice_amounts.invoice_item_tax_total, '0.00') AS invoice_item_tax_total,
			IFnull(ip_invoice_amounts.invoice_tax_total, '0.00') AS invoice_tax_total,
			IFnull(ip_invoice_amounts.invoice_total, '0.00') AS invoice_total,
			IFnull(ip_invoice_amounts.invoice_paid, '0.00') AS invoice_paid,
			IFnull(ip_invoice_amounts.invoice_balance, '0.00') AS invoice_balance,
			ip_invoice_amounts.invoice_sign AS invoice_sign,
            (CASE WHEN ip_invoices.invoice_status_id NOT IN (1,4) AND DATEDIFF(NOW(), invoice_date_due) > 0 THEN 1 ELSE 0 END) is_overdue,
			DATEDIFF(NOW(), invoice_date_due) AS days_overdue,
            (CASE (SELECT COUNT(*) FROM ip_invoices_recurring WHERE ip_invoices_recurring.invoice_id = ip_invoices.invoice_id and ip_invoices_recurring.recur_next_date <> '0000-00-00') WHEN 0 THEN 0 ELSE 1 END) AS invoice_is_recurring,
			ip_invoices.*", false);
        if ($this->store_id() != 1) {
            $this->admin365_db->where("ip_invoices.store", $this->store_id());
        }
    }

    public function default_order_by() {
        $this->admin365_db->order_by('ip_invoices.invoice_id DESC');
    }

    public function default_join() {
        $this->admin365_db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->admin365_db->join('ip_users', 'ip_users.user_id = ip_invoices.user_id');
        $this->admin365_db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id', 'left');
        $this->admin365_db->join('ip_client_custom', 'ip_client_custom.client_id = ip_clients.client_id', 'left');
        $this->admin365_db->join('ip_user_custom', 'ip_user_custom.user_id = ip_users.user_id', 'left');
        $this->admin365_db->join('ip_invoice_custom', 'ip_invoice_custom.invoice_id = ip_invoices.invoice_id', 'left');
    }

    public function validation_rules() {
        return array(
            'client_name' => array(
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ),
            'invoice_date_created' => array(
                'field' => 'invoice_date_created',
                'label' => lang('invoice_date'),
                'rules' => 'required'
            ),
            'invoice_time_created' => array(
                'rules' => 'required'
            ),
            'invoice_group_id' => array(
                'field' => 'invoice_group_id',
                'label' => lang('invoice_group'),
                'rules' => 'required'
            ),
            'invoice_password' => array(
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => lang('user'),
                'rule' => 'required'
            ),
            'payment_method' => array(
                'field' => 'payment_method',
                'label' => lang('payment_method')
            ),
        );
    }

    public function validation_rules_save_invoice() {

        return array(
            'invoice_date_created' => array(
                'field' => 'invoice_date_created',
                'label' => lang('date'),
                'rules' => 'required'
            ),
            'invoice_date_due' => array(
                'field' => 'invoice_date_due',
                'label' => lang('due_date'),
                'rules' => 'required'
            ),
            'invoice_time_created' => array(
                'rules' => 'required'
            ),
            'invoice_password' => array(
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            )
        );
    }

    function store_id() {
        if ($this->session->userdata('user_id') != "") {
            $CI = &get_instance();
            $CI->load->database();

            @$connect = @mysql_connect($CI->db->hostname, $CI->db->username, $CI->db->password);
            @$db = @mysql_select_db($CI->db->database);
            @$select_user_store = @mysql_query("SELECT user_store FROM ip_users where user_id = " . $this->session->userdata('user_id')) or die("Session out");
            @$user_store = @mysql_result($select_user_store, 0, 0);
            return $user_store;
        } else {
            return 1;
        }
    }

    public function create($store = null, $client_id = null, $db_array = null, $include_invoice_tax_rates = true) {

        //get client data
        $this->admin365_db->where("client_id", $client_id);
        $client_data = $this->admin365_db->get("ip_clients")->result_object();
        $timeparts = explode(" ", microtime());
        $microtime = bcadd(($timeparts[0] * 1000), bcmul($timeparts[1], 1000));
        
        //Get store data
        $this->admin365_db->where("id", $store);
        $store_data = $this->admin365_db->get("ip_stores")->result_object();
        
        if($store_data[0]->store_name != "Warehouse")
        {
        $invoice_array = array(
            "user_id" => 9999,
            "invoice_status_id" => 1,
            "invoice_date_created" => date("Y-m-d"),
            "invoice_time_created" => date("g:i:s"),
            "invoice_date_modified" => date("Y-m-d"),
            "client_id" => $client_id,
            "store" => $store
        );

        $this->admin365_db->insert("ip_invoices", $invoice_array);
        $invoice_id = $this->admin365_db->insert_id();
       
        $this->db = $this->load->database("default", true);

        $order_array = array(
            "subchannel" => $store,
            "buyer" => $client_data[0]->client_name,
            "time" => date("Y-m-d g:i:s"),
            "timemk" => mktime(),
            "admin" => $this->session->userdata("name"),
            "invoice_id" => $invoice_id
        );
        $this->db->insert("warehouse_orders", $order_array);
        $order_id = $this->db->insert_id();

        $this->load->database("365admin", true);

        //UPDATE INVOICE NUMBER USING ORDER_ID
        $this->admin365_db->set("invoice_number", "W" . $order_id);
        $this->admin365_db->where("invoice_id", $invoice_id);
        $this->admin365_db->update("ip_invoices");


        // Create an invoice amount record
        $db_array = array(
            'invoice_id' => $invoice_id,
        );

        $store_array = array(
            'store' => $store,
        );
        if ($store != 1) {
            $this->admin365_db->where("invoice_id", $invoice_id);
            $this->admin365_db->update("ip_invoices", $store_array);
            $this->admin365_db->insert('ip_invoice_amounts', $db_array);
            


            return $order_id;
        } else {
            echo "Store required(can't be General)";
        }
        }
        else
        {
            $this->db = $this->load->database("default", true);

        $order_array = array(
            "subchannel" => $store,
            "buyer" => $client_data[0]->client_name,
            "time" => date("Y-m-d g:i:s"),
            "timemk" => mktime(),
            "admin" => $this->session->userdata("name"),
            "client_id" => $client_data[0]->client_id
        );
        $this->db->insert("warehouse_orders", $order_array);
        $order_id = $this->db->insert_id();
        return $order_id;
        }
    }

    public function get_url_key() {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_invoice($source_id, $target_id) {
        $this->load->model('invoices/mdl_items');

        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = array(
                'invoice_id' => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name' => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity' => $invoice_item->item_quantity,
                'item_price' => $invoice_item->item_price,
                'item_order' => $invoice_item->item_order
            );

            $this->mdl_items->save(null, $db_array);
        }

        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_item_tax' => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => $invoice_tax_rate->invoice_tax_rate_amount
            );

            $this->mdl_invoice_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_invoice_custom');
        $db_array = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['invoice_custom_id']);
            $db_array['invoice_id'] = $target_id;
            $this->mdl_invoice_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_credit_invoice($source_id, $target_id) {
        $this->load->model('invoices/mdl_items');

        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = array(
                'invoice_id' => $target_id,
                'item_tax_rate_id' => $invoice_item->item_tax_rate_id,
                'item_name' => $invoice_item->item_name,
                'item_description' => $invoice_item->item_description,
                'item_quantity' => -$invoice_item->item_quantity,
                'item_price' => $invoice_item->item_price,
                'item_order' => $invoice_item->item_order
            );

            $this->mdl_items->save(null, $db_array);
        }

        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_item_tax' => $invoice_tax_rate->include_item_tax,
                'invoice_tax_rate_amount' => -$invoice_tax_rate->invoice_tax_rate_amount
            );

            $this->mdl_invoice_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_invoice_custom');
        $db_array = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['invoice_custom_id']);
            $db_array['invoice_id'] = $target_id;
            $this->mdl_invoice_custom->save_custom($target_id, $db_array);
        }
    }

    public function db_array() {
        $db_array = parent::db_array();

        // Get the client id for the submitted invoice
        $this->load->model('clients/mdl_clients');
        $db_array['client_id'] = $this->mdl_clients->client_lookup($db_array['client_name']);
        unset($db_array['client_name']);

        $db_array['invoice_date_created'] = date_to_mysql($db_array['invoice_date_created']);
        $db_array['invoice_date_due'] = $this->get_date_due($db_array['invoice_date_created']);
        $db_array['invoice_terms'] = $this->mdl_settings->setting('default_invoice_terms');

        if (!isset($db_array['invoice_status_id'])) {
            $db_array['invoice_status_id'] = 1;
        }

        $generate_invoice_number = $this->mdl_settings->setting('generate_invoice_number_for_draft');

        if ($db_array['invoice_status_id'] === 1 && $generate_invoice_number === 1) {
            $db_array['invoice_number'] = $this->get_invoice_number($db_array['invoice_group_id']);
        } elseif ($db_array['invoice_status_id'] != 1) {
            $db_array['invoice_number'] = $this->get_invoice_number($db_array['invoice_group_id']);
        } else {
            $db_array['invoice_number'] = '';
        }

        // Set default values
        $db_array['payment_method'] = (empty($db_array['payment_method']) ? 0 : $db_array['payment_method']);

        // Generate the unique url key
        $db_array['invoice_url_key'] = $this->get_url_key();

        return $db_array;
    }

    public function get_invoice_group_id($invoice_id) {
        $invoice = $this->get_by_id($invoice_id);
        return $invoice->invoice_group_id;
    }

    public function get_invoice_number($invoice_group_id) {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }

    public function get_date_due($invoice_date_created) {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . $this->mdl_settings->setting('invoices_due_after') . 'D'));
        return $invoice_date_due->format('Y-m-d');
    }

    public function delete($invoice_id) {
        parent::delete($invoice_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    // Used from the guest module, excludes draft and paid
    public function is_open() {
        $this->filter_where_in('invoice_status_id', array(2, 3));
        return $this;
    }

    public function guest_visible() {
        $this->filter_where_in('invoice_status_id', array(2, 3, 4));
        return $this;
    }

    public function is_draft() {
        $this->filter_where('invoice_status_id', 1);
        return $this;
    }

    public function is_sent() {
        $this->filter_where('invoice_status_id', 2);
        return $this;
    }

    public function is_viewed() {
        $this->filter_where('invoice_status_id', 3);
        return $this;
    }

    public function is_paid() {
        $this->filter_where('invoice_status_id', 4);
        return $this;
    }

    public function is_overdue() {
        $this->filter_having('is_overdue', 1);
        return $this;
    }

    public function by_client($client_id) {
        $this->filter_where('ip_invoices.client_id', $client_id);
        return $this;
    }

    public function mark_viewed($invoice_id) {
        $this->admin365_db->select('invoice_status_id');
        $this->admin365_db->where('invoice_id', $invoice_id);

        $invoice = $this->admin365_db->get('ip_invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == 2) {
                $this->admin365_db->where('invoice_id', $invoice_id);
                $this->admin365_db->set('invoice_status_id', 3);
                $this->admin365_db->update('ip_invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is view
            if ($this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 3) {
                $this->admin365_db->where('invoice_id', $invoice_id);
                $this->admin365_db->set('is_read_only', 1);
                $this->admin365_db->update('ip_invoices');
            }
        }
    }

    public function mark_sent($invoice_id) {
        $this->admin365_db->select('invoice_status_id');
        $this->admin365_db->where('invoice_id', $invoice_id);

        $invoice = $this->admin365_db->get('ip_invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->invoice_status_id == 1) {
                $this->admin365_db->where('invoice_id', $invoice_id);
                $this->admin365_db->set('invoice_status_id', 2);
                $this->admin365_db->update('ip_invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is sent
            if ($this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 2) {
                $this->admin365_db->where('invoice_id', $invoice_id);
                $this->admin365_db->set('is_read_only', 1);
                $this->admin365_db->update('ip_invoices');
            }
        }
    }

}
