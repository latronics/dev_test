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

class Payments extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('America/Los_Angeles');
        $this->load->model('mdl_payments');
    }

    public function index($page = 0) {
        $this->mdl_payments->paginate(site_url('payments/index'), $page);
        $payments = $this->mdl_payments->result();

        $this->layout->set(
                array(
                    'payments' => $payments,
                    'filter_display' => true,
                    'filter_placeholder' => lang('filter_payments'),
                    'filter_method' => 'filter_payments'
                )
        );

        $this->layout->buffer('content', 'payments/index');
        $this->layout->render();
    }

    public function form($id = null) {
        if ($this->input->post('btn_cancel')) {
            redirect('payments');
        }

        if ($this->mdl_payments->run_validation()) {
            $id = $this->mdl_payments->save($id);

            $this->load->model('custom_fields/mdl_payment_custom');

            $this->mdl_payment_custom->save_custom($id, $this->input->post('custom'));


            redirect('payments');
        }

        if (!$this->input->post('btn_submit')) {





            $prep_form = $this->mdl_payments->prep_form($id);






            if ($id and ! $prep_form) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_payment_custom');

            $payment_custom = $this->mdl_payment_custom->where('payment_id', $id)->get();

            if ($payment_custom->num_rows()) {
                $payment_custom = $payment_custom->row();

                unset($payment_custom->payment_id, $payment_custom->payment_custom_id);





                foreach ($payment_custom as $key => $val) {
                    $this->mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } else {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_custom_fields');

        $open_invoices = $this->mdl_invoices->where('ip_invoice_amounts.invoice_balance >', 0)->get()->result();

        $amounts = array();
        $invoice_payment_methods = array();
        foreach ($open_invoices as $open_invoice) {
            $amounts['invoice' . $open_invoice->invoice_id] = format_amount($open_invoice->invoice_balance);
            $invoice_payment_methods['invoice' . $open_invoice->invoice_id] = $open_invoice->payment_method;
        }

        $this->layout->set(
                array(
                    'payment_id' => $id,
                    'payment_methods' => $this->mdl_payment_methods->get()->result(),
                    'open_invoices' => $open_invoices,
                    'custom_fields' => $this->mdl_custom_fields->by_table('ip_payment_custom')->get()->result(),
                    'amounts' => json_encode($amounts),
                    'invoice_payment_methods' => json_encode($invoice_payment_methods)
                )
        );

        if ($id) {
            $this->layout->set('payment', $this->mdl_payments->where('ip_payments.payment_id', $id)->get()->row());
        }




        $this->layout->buffer('content', 'payments/form');
        $this->layout->render();
    }

    public function bluepay() {


        $this->layout->buffer('content', 'payments/bluepay');
        $this->layout->render();
    }

    public function process_bluepay($invoice_id, $client_id) {

        $this->db->select("*");
        $this->db->where("payment_method_id", $this->input->post('payment_method_id'));
        $payment_type = $this->db->get("ip_payment_methods")->result_array();


        $insert_payment = array(
            "invoice_id" => $this->input->post('invoice_id'),
            "payment_method_id" => $this->input->post('payment_method_id'),
            "payment_date" => date('Y-m-d'),
            "payment_amount" => $this->input->post('amount'),
            "payment_note" => $this->input->post('note')
        );

        $update_invoice = array(
            "invoice_status_id" => 4,
            "is_read_only" => 1
        );

        $this->db->select("*");
        $this->db->where("invoice_id", $this->input->post('invoice_id'));
        $invoice_total_valor = $this->db->get("ip_invoice_amounts")->result_array();
        $invoice_paid = $invoice_total_valor[0]['invoice_paid'] + $this->input->post('amount');
        $invoice_balance = $invoice_total_valor[0]['invoice_total'] - $invoice_paid;

        $update_invoice_amount = array(
            "invoice_paid" => $invoice_paid,
            "invoice_balance" => $invoice_balance
        );

        $this->load->library('BluePay');

        //GET BLUEPAY ACCOUNT INFO
        $this->db->select("*");
        $this->db->where("setting_key", "bluepay_account_id");
        $blue_pay_account_id = $this->db->get("ip_settings")->result_array();

        $this->db->select("*");
        $this->db->where("setting_key", "secret_key");
        $secret_key = $this->db->get("ip_settings")->result_array();

        $this->db->select("*");
        $this->db->where("setting_key", "mode");
        $bluepay_mode = $this->db->get("ip_settings")->result_array();

        $accountID = $blue_pay_account_id[0]['setting_value'];
        $secretKey = $secret_key[0]['setting_value'];
        $mode = $bluepay_mode[0]['mode'];

        $payment = new BluePay(
                $accountID, $secretKey, $mode
        );




        if (($client_id == "") && ($invoice_id == "")) {
            $payment->setCustomerInformation(array(
                'firstName' => $this->input->post('fname'),
                'lastName' => $this->input->post('lname'),
                'addr1' => $this->input->post('faddress'),
                'addr2' => $this->input->post('saddress'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'zip' => $this->input->post('zip'),
                'country' => $this->input->post('country'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email')
            ));
            $payment->setCustomID1($this->input->post('ticket_id'));
            if ($payment_type[0]['payment_method_type'] == "card") {
                $payment->setCCInformation(array(
                    'cardNumber' => $this->input->post('card_number'), // Card Number test: 4111111111111111
                    'cardExpire' => $this->input->post('expire_date'),
                    'cvv2' => $this->input->post('ccv_code'),
                    'payment_type' => $this->input->post('credit_debit')
                ));
            } else if ($payment_type[0]['payment_method_type'] == "check") {
                $payment->setACHInformation(array(
                    'routingNumber' => $this->input->post('routing_number'), // Routing Number test: 123123123
                    'accountNumber' => $this->input->post('account_number'), // Account Number test: 844-236-1465
                    'accountType' => 'C', // Account Type: Checking
                    'documentType' => 'WEB' // ACH Document Type: WEB
                ));
            }
            $payment->sale($this->input->post('amount')); // Sale Amount: $3.00
            // Makes the API request with BluePAy
            $payment->process();

            // Reads the response from BluePay
            if ($payment->isSuccessfulResponse()) {
                $data['payment'] = array(
                    'transaction_status' => $payment->getStatus(),
                    'transaction_message' => $payment->getMessage(),
                    'transaction_id' => $payment->getTransID(),
                    'avs_response' => $payment->getAVSResponse(),
                    'cvs_response' => $payment->getCVV2Response(),
                    'masked_account' => $payment->getMaskedAccount(),
                    'card_type' => $payment->getCardType(),
                    'authorization_code' => $payment->getAuthCode(),
                    'amount' => $this->input->post('amount')
                );
                $insert_receipt_data = array(
                    'status' => $payment->getStatus(),
                    'trans_id' => $payment->getTransID(),
                    'message' => $payment->getMessage(),
                    'card_digits' => $payment->getMaskedAccount(),
                    'card_type' => $payment->getCardType(),
                    'amount' => $this->input->post('amount'),
                    'date' => date('Y-m-d h:i:s'),
                    'invoice_id' => $invoice_id
                );
            }

            if (($payment->getMessage() != "DUPLICATE\r") && ($payment->getStatus() == "APPROVED")) {

                $this->db->insert("ip_approved_transactions", $insert_receipt_data);

                $this->db->insert("ip_payments", $insert_payment);




                $this->db->where("invoice_id", $this->input->post('invoice_id'));
                $this->db->update("ip_invoice_amounts", $update_invoice_amount);


                if ($invoice_balance <= 0) {
                    $this->db->where("invoice_id", $this->input->post('invoice_id'));
                    $this->db->update("ip_invoices", $update_invoice);
                }
                $this->load->view("return_payment_status", $data);
            } else {
                $data['declined'] = $payment->getMessage();

                $this->load->view("return_payment_status", $data);
            }
        } else if (($client_id != "") && ($invoice_id != "")) {
            $this->db->select("*");
            $this->db->join("ip_invoices", "ip_invoices.client_id = ip_clients.client_id");
            $this->db->where("ip_invoices.invoice_id", $invoice_id);
            $client_info = $this->db->get("ip_clients")->result_array();


            

            $payment->setCustomerInformation(array(
                'firstName' => $client_info[0]['client_name'],
                'addr1' => $client_info[0]['client_address_1'],
                'addr2' => $client_info[0]['client_address_2'],
                'city' => $client_info[0]['client_city'],
                'state' => $client_info[0]['client_state'],
                'zip' => $client_info[0]['client_zip'],
                'country' => $client_info[0]['client_country'],
                'phone' => $client_info[0]['client_phone'],
                'email' => $client_info[0]['client_email']
            ));
            $payment->setCustomID1($invoice_id);



            $payment->setCCInformation(array(
                'cardNumber' => $this->input->post("card_number"), // Card Number test: 4111111111111111
                'cardExpire' => $this->input->post("expire_date"),
                'cvv2' => $this->input->post("ccv_code")
            ));

            $payment->sale($this->input->post("amount")); // Sale Amount: $3.00
            // Makes the API request with BluePAy
            $payment->process();

            // Reads the response from BluePay
            if ($payment->isSuccessfulResponse()) {
                $data['payment'] = array(
                    'transaction_status' => $payment->getStatus(),
                    'transaction_message' => $payment->getMessage(),
                    'transaction_id' => $payment->getTransID(),
                    'avs_response' => $payment->getAVSResponse(),
                    'cvs_response' => $payment->getCVV2Response(),
                    'masked_account' => $payment->getMaskedAccount(),
                    'card_type' => $payment->getCardType(),
                    'authorization_code' => $payment->getAuthCode(),
                    'amount' => $this->input->post("amount")
                );
                $insert_receipt_data = array(
                    'status' => $payment->getStatus(),
                    'trans_id' => $payment->getTransID(),
                    'message' => $payment->getMessage(),
                    'card_digits' => $payment->getMaskedAccount(),
                    'card_type' => $payment->getCardType(),
                    'amount' => $this->input->post('amount'),
                    'date' => date('Y-m-d h:i:s'),
                    'invoice_id' => $invoice_id
                );
            }

            if (($payment->getMessage() != "DUPLICATE\r") && ($payment->getStatus() == "APPROVED")) {

                $this->db->insert("ip_approved_transactions", $insert_receipt_data);

                $this->db->insert("ip_payments", $insert_payment);




                $this->db->where("invoice_id", $this->input->post('invoice_id'));
                $this->db->update("ip_invoice_amounts", $update_invoice_amount);


                if ($invoice_balance <= 0) {
                    $this->db->where("invoice_id", $this->input->post('invoice_id'));
                    $this->db->update("ip_invoices", $update_invoice);
                }
                $this->load->view("return_payment_status", $data);
            } else {
                $data['declined'] = $payment->getMessage();

                $this->load->view("return_payment_status", $data);
            }


            $this->db->where("id_invoice", $invoice_id);
            $this->db->delete("ip_invoice_client_on_turn");
        }
    }

    public function card_receipt($trans_id) {

        $this->db->select("*");

        $this->db->where("trans_id", $trans_id);
        $data_receipt_print['data_receipt_print'] = $this->db->get("ip_approved_transactions")->result_array();

        $this->load->view("card_receipt", $data_receipt_print);
    }

    public function delete($id) {
        $this->mdl_payments->delete($id);
        redirect('payments');
    }

    public function approved_payments() {
        $this->db->select("*");
        $this->db->order_by("id", "DESC");
        $data_approved_payments['data_approved_payments'] = $this->db->get("ip_approved_transactions")->result_array();
        //$this->load->view("show_approved_payments", $data_approved_payments);
        $this->layout->buffer('content', 'payments/show_approved_payments', $data_approved_payments);
        $this->layout->render();
    }

    public function validate_method_type() {

        $this->db->select("*");
        $this->db->where("payment_method_id", $this->input->post('method_payment_id'));
        $data_method_payment = $this->db->get("ip_payment_methods")->result_array();
        echo $data_method_payment[0]['payment_method_type'];
    }

    public function test() {
        $this->load->view("test");
    }

}
