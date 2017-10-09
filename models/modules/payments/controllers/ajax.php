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

class Ajax extends Admin_Controller {

    public $ajax_controller = true;

    public function add() {
        $invoice_id = $this->input->post("invoice_id");
        $this->load->model('payments/mdl_payments');

        if ($this->mdl_payments->validation_rules()) {
            $this->mdl_payments->save();


            $response = array(
                'success' => 1
            );
            $invoice_history = array(
                "id_invoice" => $invoice_id,
                "id_status" => 4,
                "date_changed" => date("Y-m-d h:i:s"),
                "staff_comments" => "Paid by Payments",
                "user_id" => $this->session->userdata("user_id")
            );
            $this->db->insert("ip_invoices_status_history", $invoice_history);

            $this->db->set("is_read_only", 1);
            $this->db->where("invoice_id", $invoice_id);
            $this->db->update("ip_invoices");
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }


        echo json_encode($response);
    }

    public function modal_add_payment() {

        $this->load->module('layout');
        $this->load->model('payments/mdl_payments');
        $this->load->model('payment_methods/mdl_payment_methods');

        $data = array(
            'payment_methods' => $this->mdl_payment_methods->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice_balance' => $this->input->post('invoice_balance'),
            'invoice_payment_method' => $this->input->post('invoice_payment_method')
        );

        $this->layout->load_view('payments/modal_add_payment', $data);
    }

}
