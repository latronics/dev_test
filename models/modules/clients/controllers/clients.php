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

class Clients extends Admin_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_clients');
    }

    public function index() {
        // Display active clients by default
        redirect('clients/status/active');
    }

    function update_client_terminal() {
        if ($this->db->get("ip_agreement_client_on_turn")->num_rows() > 0) {
            echo "true";
        } else {
            return "false";
        }
    }
    function update_client_password()
    {
        $client_data = $this->db->get("ip_clients")->result_object();
        foreach($client_data as $client_data){
            $this->db->set("client_password", md5($client_data->client_phone));
            $this->db->where("client_id", $client_data->client_id);
            $this->db->where("client_phone <>", "");
            $this->db->update("ip_clients");
        }
    }

    function update_agreements_terminal() {
        if ($this->db->get("ip_agreement_client_on_turn")->num_rows() == 0) {
            echo "true";
        } else {
            return "false";
        }
    }

    function check_signature() {
        $order_id = $this->input->post("ticket_id");
        $this->db->where("id_ticket", $order_id);
        $result = $this->db->get("ip_agreement_terms_x_client")->result_array();
        if ($result[0]['signature'] != null) {
            echo "true";
        }
    }

    public function print_label($quote_id) {
        $this->db->select("*");
        $this->db->where("quote_id", $quote_id);
        $quote_data['quote_data'] = $this->db->get("ip_quotes")->result_array();

        $this->db->select("client_name");
        $this->db->where("client_id", $quote_data['quote_data'][0]['client_id']);
        $quote_data['client_data'] = $this->db->get("ip_clients")->result_array();
        $this->load->view("quotes/print_label", $quote_data);
    }

    public function send_invoice_terminal($id_invoice, $id_client) {

        $data_invoice = array(
            'id_client' => $id_client,
            'id_invoice' => $id_invoice
        );

        $this->db->select("*");
        $get_rows_invoice = $this->db->get("ip_invoice_client_on_turn")->num_rows();

        if ($get_rows_invoice == null) {
            $this->db->insert("ip_invoice_client_on_turn", $data_invoice);

            echo "<script>alert('Invoice was Sent!'); </script>";
        } else {

            echo "<script>alert('Waiting for Invoice Payment...');</script>";
        }
        echo "<script>window.open('" . site_url('invoices/view/' . $id_invoice) . "','_self');</script>";
    }

    public function sendagreements($ticket_id, $client_id) {
        $insert_values_agreement = array
            (
            'id_client' => $client_id,
            'id_ticket' => $ticket_id
        );
        $client_agreement_on_turn = array
            (
            'client_id' => $client_id,
            'ticket_id' => $ticket_id
        );



        $this->db->select("*");
        $this->db->from("ip_agreement_terms_x_client");
        //$this->db->where("id_client", $client_id);
        $this->db->where("id_ticket", $ticket_id);
        $verify_insert_get = $this->db->get();
        $get_rows = $verify_insert_get->num_rows();
        if ($get_rows == null) {

            $this->db->insert('ip_agreement_terms_x_client', $insert_values_agreement);
        }
        $this->db->select("*");
        $this->db->from("ip_agreement_client_on_turn");
        $this->db->where("client_id", $client_agreement_on_turn['client_id']);
        $this->db->where("ticket_id", $client_agreement_on_turn['ticket_id']);
        $receive = $this->db->get();
        $get_rows_agreements = $receive->num_rows();

        if ($get_rows_agreements == null) {
            $this->db->insert('ip_agreement_client_on_turn', $client_agreement_on_turn);

            echo "<script>alert('Agreement Terms was Sent!'); </script>";
        } else {

            echo "<script>alert('Waiting for client signature...');</script>";
        }
        echo "<script>window.open('" . site_url('quotes/view/' . $ticket_id) . "','_self');</script>";
    }

    public function receive_signature($id_client, $id_ticket) {
        if ($_POST['sig']) {
            file_put_contents("myfile.png", file_get_contents($_POST['sig']));

            $data = array(
                'id_client' => $id_client,
                'id_ticket' => $id_ticket
            );
            $signature = array(
                'signature' => $this->input->post('sig')
            );


            $this->db->where('id_client', $id_client);
            $this->db->where('id_ticket', $id_ticket);
            $this->db->update('ip_agreement_terms_x_client', $signature);
        }
//GETTING CLIENT INFORMATION TO CREATE AN EMAIL
        $this->db->select("*");
        $this->db->from("ip_clients");
        $this->db->join("ip_quotes", "ip_quotes.client_id = ip_clients.client_id");
        $this->db->where("ip_clients.client_id", $id_client);
        $this->db->where("ip_quotes.quote_id", $id_ticket);
        $get_client_data = $this->db->get();
        $client_data = $get_client_data->result_array();

        $agreements_data['agreements_data'] = $client_data;



//CLEAN THE AUX TABLE IP_AGREEMENT_CLIENT_ON_TURN
        $this->db->where("client_id !=", 'NULL');
        $this->db->delete("ip_agreement_client_on_turn");


//SEND THE EMAIL TO THE CLIENT
        $to = $client_data[0]['client_email'];

        $subject = "Order " . $client_data[0]['quote_number'] . " created";


        ob_start();
        $this->load->view("client_agreements_confirm", $agreements_data);


        $content = ob_get_contents();


        $headers = "Content-Type:text/html; charset=UTF-8\n";
        $headers .= "From:  365laptoprepair.com<thiago@365laptoprepair.com>\n";
        $headers .= "X-Sender:  <thiago@365laptoprepair.com>\n";
        $headers .= "X-Mailer: PHP  v" . phpversion() . "\n";
        $headers .= "X-IP:  " . $_SERVER['REMOTE_ADDR'] . "\n";
        $headers .= "Return-Path:  <thiago@365laptoprepair.com>\n";
        $headers .= "MIME-Version: 1.0\n";

        if (mail($to, $subject, $content, $headers)) {
            ob_end_clean();

            $this->load->view('received_signature');
        }
    }

    public function clientTerminal() {

        //GET CLIENT AND TICKET DATA FROM IP_AGREEMENT_CLIENT_ON_TURN
        $this->db->select("*");
        $this->db->from("ip_agreement_client_on_turn");
        $open_terms_get = $this->db->get();
        $open_terms = $open_terms_get->result_array();
        $data['data'] = $open_terms[0]['client_id'];


        //GET CLIENT AND INVOICE DATA FROM IP_INVOICE_CLIENT_ON_TURN
        $this->db->select("*");
        $invoice_data_array = $this->db->get("ip_invoice_client_on_turn")->result_array();
        $invoice_data['invoice_data'] = $invoice_data_array;



        if (($open_terms[0]['client_id'] == "") && ($invoice_data_array[0]['id_client'] == "")) {


            $this->load->view('client_terminal', true);
        } else if (($open_terms[0]['client_id'] != "") && ($invoice_data_array[0]['id_client'] == "")) {


            $this->load->view('agreements', $data);
        } else if ($invoice_data_array[0]['id_client'] != "") {
            $this->load->view('invoice_client', $invoice_data);
        }
    }

    public function register_client_terminal() {


        $get_fname_lanem = array(
            'firstname' => $this->input->post('FirstName'),
            'lastname' => $this->input->post('LastName'),
        );

        $data_new_client_array = array(
            'client_email' => $this->input->post('Email'),
            'client_name' => $this->input->post('FirstName') . " " . $this->input->post('LastName'),
            'client_phone' => $this->input->post('PhoneNumber'),
            'new_client' => '1',
        );

        $this->db->select("*");
        $this->db->from("ip_clients");
        $this->db->where("client_email", $data_new_client_array['client_email']);
        $this->db->or_where("client_name", $data_new_client_array['client_name']);
        $check_client_exist_get = $this->db->get();
        $check_client_exist = $check_client_exist_get->num_rows();
        $client_data = $check_client_exist_get->result_object();

        if ($data_new_client_array['client_email'] == "") {
            $warnings['warnings'] = "The field 'E-mail' is required.";
        } else if ($get_fname_lanem['firstname'] == "") {
            $warnings['warnings'] = "The field 'First Name' is required.";
        } else if ($get_fname_lanem['lastname'] == "") {
            $warnings['warnings'] = "The field 'Last Name' is required.";
        } else if ($data_new_client_array['client_phone'] == "") {
            $warnings['warnings'] = "The field 'Phone Number' is required.";
        } else if ($check_client_exist > 0) {
            $this->db->set("new_client", 1);
            $this->db->where("client_id", $client_data[0]->client_id);
            $this->db->update("ip_clients");
            $warnings['warnings'] = "This client already exists.";
        } else {

            $this->db->insert("ip_clients", $data_new_client_array);
            $warnings['congrats'] = "Registered with success.";
        }
        $this->load->view('../views/client_terminal', $warnings);
    }

    public function status($status = 'active', $page = 0) {
        if (is_numeric(array_search($status, array('active', 'inactive')))) {
            $function = 'is_' . $status;
            $this->mdl_clients->$function();
        }

        $this->mdl_clients->with_total_balance()->paginate(site_url('clients/status/' . $status), $page);
        $clients = $this->mdl_clients->result();

        $this->layout->set(
                array(
                    'records' => $clients,
                    'filter_display' => true,
                    'filter_placeholder' => lang('filter_clients'),
                    'filter_method' => 'filter_clients'
                )
        );

        $this->layout->buffer('content', 'clients/index');
        $this->layout->render();
    }

    public function form($id = null) {
        if ($this->input->post('btn_cancel')) {
            redirect('clients');
        }

        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('client_name') != '') {



            $check = $this->db->get_where('ip_clients', array('client_name' => $this->input->post('client_name')))->result();



            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('client_already_exists'));
                redirect('clients/form');
            }
        }

        if ($this->mdl_clients->run_validation()) {
            $id = $this->mdl_clients->save($id);

            $this->load->model('custom_fields/mdl_client_custom');

            $this->mdl_client_custom->save_custom($id, $this->input->post('custom'));


            redirect('clients/view/' . $id);
        }

        if ($id and ! $this->input->post('btn_submit')) {

            if (!$this->mdl_clients->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_client_custom');
            $this->mdl_clients->set_form_value('is_update', true);

            $client_custom = $this->mdl_client_custom->where('client_id', $id)->get();

            if ($client_custom->num_rows()) {
                $client_custom = $client_custom->row();

                unset($client_custom->client_id, $client_custom->client_custom_id);

                foreach ($client_custom as $key => $val) {
                    $this->mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {

            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->helper('country');

        $this->layout->set('custom_fields', $this->mdl_custom_fields->by_table('ip_client_custom')->get()->result());
        $this->layout->set('countries', get_country_list(lang('cldr')));
        $this->layout->set('selected_country', $this->mdl_clients->form_value('client_country') ?:
                        $this->mdl_settings->setting('default_country'));

        $this->layout->buffer('content', 'clients/form');
        $this->layout->render();
    }

    public function view($client_id) {
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $user_data = $this->db->get("ip_users")->result_object();
        $user_type = $user_data[0]->user_type;

        $this->load->model('clients/mdl_client_notes');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('payments/mdl_payments');
        $this->load->model('custom_fields/mdl_custom_fields');

        $client = $this->mdl_clients->with_total()->with_total_balance()->with_total_paid()->where('ip_clients.client_id', $client_id)->get()->row();

        if (!$client) {
            show_404();
        }

        $this->layout->set(
                array(
                    'client' => $client,
                    'client_notes' => $this->mdl_client_notes->where('client_id', $client_id)->get()->result(),
                    'invoices' => $this->mdl_invoices->by_client($client_id)->limit(20)->get()->result(),
                    'quotes' => $this->mdl_quotes->by_client($client_id)->limit(20)->get()->result(),
                    'payments' => $this->mdl_payments->by_client($client_id)->limit(20)->get()->result(),
                    'custom_fields' => $this->mdl_custom_fields->by_table('ip_client_custom')->get()->result(),
                    'quote_statuses' => $this->mdl_quotes->statuses(),
                    'invoice_statuses' => $this->mdl_invoices->statuses(),
                )
        );

        $this->layout->buffer(
                array(
                    array(
                        'invoice_table',
                        'invoices/partial_invoice_table'
                    ),
                    array(
                        'quote_table',
                        'quotes/partial_quote_table'
                    ),
                    array(
                        'payment_table',
                        'payments/partial_payment_table'
                    ),
                    array(
                        'partial_notes',
                        'clients/partial_notes'
                    ),
                    array(
                        'content',
                        'clients/view'
                    ),
                    array(
                        'signed_tickets',
                        'clients/show_signed_tickets'
                    )
                )
        );
        if ($user_type == 1) {
            $this->layout->render();
        } else {
            $send_type['user_type'] = $user_type;
            $this->load->view("clients/view", $send_type);
        }
    }

    public function delete($client_id) {
        $this->mdl_clients->delete($client_id);
        redirect('clients');
    }

}
