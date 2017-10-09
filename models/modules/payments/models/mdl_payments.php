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

class Mdl_Payments extends Response_Model {

    public $table = 'ip_payments';
    public $primary_key = 'ip_payments.payment_id';
    public $validation_rules = 'validation_rules';

    public function default_select() {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_payment_custom.*,
            ip_payment_methods.*,
            ip_products.product_name,
            ip_products.product_price,
            ip_products.purchase_price,
            ip_categories.*,
            ip_invoice_amounts.*,
            ip_clients.client_name,
        	ip_clients.client_id,
            ip_invoices.invoice_number,
            ip_invoices.invoice_date_created,
            ip_payments.*, ip_stores.*", false);
        $this->db->distinct("invoice_number");
        if ($this->store_id() != 1) {
            $this->db->where("ip_payments.store", $this->store_id());
        }
    }

    public function default_order_by() {
        $this->db->order_by('ip_payments.payment_date DESC');
    }

    public function default_join() {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id');
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id', 'left');
        $this->db->join('ip_payment_custom', 'ip_payment_custom.payment_id = ip_payments.payment_id', 'left');
        $this->db->join("ip_stores", "ip_stores.id = ip_payments.store");
        $this->db->join("ip_invoice_items", "ip_invoice_items.invoice_id = ip_invoices.invoice_id", "left");
        $this->db->join("ip_products", "ip_products.product_id = ip_invoice_items.item_product_id", "left");
        $this->db->join("ip_categories","ip_categories.category_id = ip_products.pcategory_id", "left");
    }

    public function validation_rules() {


        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ),
            'payment_date' => array(
                'field' => 'payment_date',
                'label' => lang('date'),
                'rules' => 'required'
            ),
            'payment_time' => array(
                'field' => 'payment_time',
                'label' => 'Payment time',
                'rules' => 'required'
            ),
            'payment_amount' => array(
                'field' => 'payment_amount',
                'label' => lang('payment'),
                'rules' => 'required|callback_validate_payment_amount'
            ),
            'payment_method_id' => array(
                'field' => 'payment_method_id',
                'label' => lang('payment_method')
            ),
            'payment_note' => array(
                'field' => 'payment_note',
                'label' => lang('note')
            )
        );
    }

    public function store_id() {
        $CI = &get_instance();
        $CI->load->database();
        if ($this->session->userdata('user_id') != "") {
            $connect = mysql_connect($CI->db->hostname, $CI->db->username, $CI->db->password);
            $db = mysql_select_db($CI->db->database);
            $select_user_store = mysql_query("SELECT user_store FROM ip_users where user_id = " . $this->session->userdata('user_id')) or die("Session out");
            $user_store = mysql_result($select_user_store, 0, 0);
            return $user_store;
        }
    }

    public function validate_payment_amount($amount) {

        date_default_timezone_set('America/Los_Angeles');
        $invoice_id = $this->input->post('invoice_id');
        $payment_id = $this->input->post('payment_id');


        $invoice_balance = $this->db->where('invoice_id', $invoice_id)->get('ip_invoice_amounts')->row()->invoice_balance;




        if ($payment_id) {
            $payment = $this->db->where('payment_id', $payment_id)->get('ip_payments')->row();

            $invoice_balance = $invoice_balance + $payment->payment_amount;
        }

        if ($amount > $invoice_balance) {
            $this->form_validation->set_message('validate_payment_amount', lang('payment_cannot_exceed_balance'));
            return false;
        }


        //INSERT INTO IP_INVOICES_STATUS_HISTORY
        $data_invoice_status_history = array(
            "id_invoice" => $invoice_id,
            "id_status" => 4,
            "date_changed" => date("Y-m-d H:i:s"),
            "staff_comments" => 'Paid by Payments',
            "user_id" => $this->session->userdata('user_id')
        );
        $this->db->insert("ip_invoices_status_history", $data_invoice_status_history);


        return true;
    }

    public function save($id = null, $db_array = null) {
        $db_array = ($db_array) ? $db_array : $this->db_array();
        
        // Save the payment
        $id = parent::save($id, $db_array);
        $this->db->where("invoice_id", $db_array['invoice_id']);
        $invoices_data =$this->db->get("ip_invoices")->result_object();
        $store = $invoices_data[0]->store;
        $store_id_array = array(
            "store" => $store
        );
     
        $this->db->where("payment_id", $id);
        $this->db->update("ip_payments", $store_id_array);


        // Set proper status for the invoice
        $this->db->where('invoice_id', $db_array['invoice_id']);
        $this->db->set('invoice_status_id', 4);
        $this->db->update('ip_invoices');

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id']);

        return $id;
    }

    public function delete($id = null) {
        // Get the invoice id before deleting payment
        $this->db->select('invoice_id');
        $this->db->where('payment_id', $id);
        $invoice_id = $this->db->get('ip_payments')->row()->invoice_id;

        // Delete the payment
        parent::delete($id);

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        // Change invoice status back to sent
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);
        $invoice = $this->db->get('ip_invoices')->row();

        if ($invoice->invoice_status_id == 4) {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_status_id', 2);
            $this->db->update('ip_invoices');
        }

        $this->load->helper('orphan');
        delete_orphans();
    }

    public function db_array() {
        $db_array = parent::db_array();

        $db_array['payment_date'] = date_to_mysql($db_array['payment_date']);
        $db_array['payment_amount'] = standardize_amount($db_array['payment_amount']);

        return $db_array;
    }

    public function prep_form($id = null) {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('payment_date', date('Y-m-d'));
            parent::set_form_value('payment_time', date('H:i:s'));
        }

        return true;
    }

    public function by_client($client_id) {
        $this->filter_where('ip_clients.client_id', $client_id);
        return $this;
    }

}
