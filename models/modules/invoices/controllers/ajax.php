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

    public function erase_client_terminal() {
        if ($this->input->post("erase_data") == 1) {
            //ERASE THE REGISTERS FROM INVOICE ON CLIENT TERMINAL
            $this->db->select("*");
            $ip_invoice_client = $this->db->get("ip_invoice_client_on_turn")->result_array();
            foreach ($ip_invoice_client as $ip_invoice_client) {
                $this->db->where("id_client", $ip_invoice_client['id_client']);
                $this->db->delete("ip_invoice_client_on_turn");
            }
            //ERASE THE REGISTERS FROM AGREEMENTS ON CLIENT TERMINAL
            $this->db->select("*");
            $ip_agreement_client = $this->db->get("ip_agreement_client_on_turn")->result_array();
            foreach ($ip_agreement_client as $ip_agreement_client) {
                $this->db->where("client_id", $ip_agreement_client['client_id']);
                $this->db->delete("ip_agreement_client_on_turn");
            }
        }
        echo "Client terminal has been cleaned!";
    }

    public function save_itemcost() {
        $invoice_id = $this->input->post("invoice_id");
        
        
        $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                $this->db->set("item_cost", $item->item_cost);
                $this->db->set("cost_description", $item->cost_description);
                $this->db->where("invoice_id", $invoice_id);
                $this->db->update("ip_invoice_items");
                
                
            }
            
    }

    public function update_nrows() {
        if ($this->input->post("erase") == 1) {
            $this->session->unset_userdata("nrows");
        } else {
            $nrows = $this->session->userdata("nrows");
            if ($nrows == null) {
                $this->session->set_userdata('nrows', $this->input->post('qtt_rows'));
            } else {
                $nrows += 1;
                $this->session->set_userdata('nrows', $nrows);
            }
            echo $this->session->userdata("nrows");
        }
    }

    public function set_product() {
        $product_name = $this->input->post("product_name");
        $this->db->where("product_name", $product_name);
        $product_data = $this->db->get("ip_products")->result_object();

        echo json_encode($product_data[0]);
    }

    public function show_products() {
        $product_get = $this->input->get("term");
        $this->db->like("product_name", $product_get);
        $produt_data = $this->db->get("ip_products")->result_object();

        foreach ($produt_data as $produt_data) {
            $product_name[] = $produt_data->product_name;
        }
        echo json_encode($product_name);
    }

    public function customer_search() {


        $this->db->select("client_name");
        $this->db->like("client_name", $this->input->get('term'));
        $this->db->order_by("client_name", "asc");
        $this->db->limit("10");
        $client_autocomplete = $this->db->get("ip_clients")->result_array();

        foreach ($client_autocomplete as $client_autocomplete) {

            $array[] = $client_autocomplete['client_name'];
        }
        echo json_encode($array);
    }

    public function remove_walking() {
        $data = array(
            "user_id" => $this->session->userdata("user_id"),
            "walking" => 0
        );
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $this->db->update("ip_client_walking_aux", $data);
    }

    public function get_walking() {
        $data = array(
            "user_id" => $this->session->userdata("user_id"),
            "walking" => 1
        );
        $this->db->select("*");
        $this->db->where("walking", "1");
        $client_name = $this->db->get("ip_clients")->result_array();

        $this->db->select("*");
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $walking_customer_rows = $this->db->get("ip_client_walking_aux")->num_rows();

        if ($walking_customer_rows == 0) {
            $this->db->insert("ip_client_walking_aux", $data);
        } else {
            $this->db->where("user_id", $this->session->userdata("user_id"));
            $this->db->update("ip_client_walking_aux", $data);
        }
        echo $client_name[0]['client_name'];
    }

    public function save() {

        date_default_timezone_set('America/Los_Angeles');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('item_lookups/mdl_item_lookups');
        $i = 0;
        $invoice_id = $this->input->post('invoice_id');

        $this->mdl_invoices->set_id($invoice_id);

        $invoice_status = $this->input->post('invoice_status_id');

        if ($this->mdl_invoices->run_validation('validation_rules_save_invoice')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                // Check if an item has either a quantity + price or name or description
                //if (!empty($item->item_name)) {
                $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                $item->item_price = ($item->item_quantity ? standardize_amount($item->item_price) : floatval(0));
                $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);


                $amendment = $item->amendment;
                $item_id = ($item->item_id) ?: null;
                unset($item->item_id);

                $this->db->select("amendment");
                $this->db->where("product_id", $item->item_product_id);
                $product_data = $this->db->get("ip_products")->result_array();

                $this->db->select("invoice_id, id_status");
                $this->db->join("ip_invoices_status_history", "ip_invoices_status_history.id_invoice = ip_invoice_items.invoice_id");
                $this->db->where("invoice_id", $invoice_id);
                $this->db->order_by("id_status", "desc");
                @$invoice_items = $this->db->get("ip_invoice_items")->result_array();

                if (($amendment == 1) || ($product_data[0]['amendment'] == 1)) {
                    $i++;
                    if ($i == 1) {



                        $this->db->select("*");
                        $this->db->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
                        $this->db->where("invoice_id", $invoice_id);
                        $ip_invoices = $this->db->get("ip_invoices")->result_array();
                        if ($ip_invoices[0]['invoice_url_key'] == null) {
                            $this->load->helper('string');
                            $invoice_url_key = random_string('alnum', 15);

                            $this->db->set("invoice_url_key", $invoice_url_key);
                            $this->db->where("invoice_id", $invoice_id);
                            $this->db->update("ip_invoices");
                        }
                        $orders_array = array(
                            "oid_ref" => $ip_invoices[0]['ticket_id'],
                            "buytype" => 5,
                            "subtype" => "u",
                            "email" => $ip_invoices[0]['client_email'],
                            "order" => $item->item_name,
                            "endprice" => $item->item_price,
                            "time" => date("Y-m-d H:i:s"),
                            "staffcomments" => "<strong>(" . $this->session->userdata("user_name") . ")</strong>"
                        );

                        $this->db->select("*");
                        $this->db->where("invoice_id", $invoice_id);
                        $this->db->where("amendment", 1);
                        $ip_invoice_items = $this->db->get("ip_invoice_items")->num_rows();

                        if ($ip_invoice_items == 0) {
                            $this->db->insert("orders", $orders_array);

                            $this->db->select("oid");
                            $this->db->where($orders_array);
                            $orders = $this->db->get("orders")->result_array();

                            $this->db->set("amendment_id", $orders[0]['oid']);
                            $this->db->where("invoice_id", $invoice_id);
                            $this->db->update("ip_invoices");
                            $this->mdl_items->save($item_id, $item);
                        }
                    }
                } else if ($product_data[0]['amendment'] == 0) {

                    $this->mdl_items->save($item_id, $item);
                }
                /* } else {

                  // Throw an error message and use the form validation for that
                  $this->load->library('form_validation');
                  $this->form_validation->set_rules('item_name', lang('item'), 'required');
                  $this->form_validation->run();

                  $response = array(
                  'success' => 0,
                  'validation_errors' => array(
                  'item_name' => form_error('item_name', '', ''),
                  )
                  );

                  echo json_encode($response);
                  } */
            }



            if ($invoice_status == 4) {
                $this->db->select("amendment_id");
                $this->db->where("invoice_id", $invoice_id);
                $ip_invoices = $this->db->get("ip_invoices")->result_array();

                $this->db->set("complete", 1);
                $this->db->where("oid", $ip_invoices[0]['amendment_id']);
                $this->db->update("orders");
            }

            if ($this->input->post('invoice_discount_amount') === '') {
                $invoice_discount_amount = floatval(0);
            } else {
                $invoice_discount_amount = $this->input->post('invoice_discount_amount');
            }

            if ($this->input->post('invoice_discount_percent') === '') {
                $invoice_discount_percent = floatval(0);
            } else {
                $invoice_discount_percent = $this->input->post('invoice_discount_percent');
            }

            // Generate new invoice number if needed
            $invoice_number = $this->input->post('invoice_number');

            if (empty($invoice_number) && $invoice_status != 1) {
                $invoice_group_id = $this->mdl_invoices->get_invoice_group_id($invoice_id);
                $invoice_number = $this->mdl_invoices->get_invoice_number($invoice_group_id);
            }

            $db_array = array(
                'invoice_number' => $invoice_number,
                'invoice_terms' => $this->input->post('invoice_terms'),
                'invoice_date_created' => date_to_mysql($this->input->post('invoice_date_created')),
                'invoice_date_due' => date_to_mysql($this->input->post('invoice_date_due')),
                'invoice_password' => $this->input->post('invoice_password'),
                'invoice_status_id' => $invoice_status,
                'payment_method' => $this->input->post('payment_method'),
                'invoice_discount_amount' => $invoice_discount_amount,
                'invoice_discount_percent' => $invoice_discount_percent
            );

            // check if status changed to sent, the feature is enabled and settings is set to sent
            if ($this->config->item('disable_read_only') === false) {
                if ($invoice_status == $this->mdl_settings->setting('read_only_toggle')) {
                    $db_array['is_read_only'] = 1;
                }
            }

            $this->mdl_invoices->save($invoice_id, $db_array);

            // Recalculate for discounts
            $this->load->model('invoices/mdl_invoice_amounts');
            $this->mdl_invoice_amounts->calculate($invoice_id);

            $response = array(
                'success' => 1,
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        //GET CLIENT EMAIL
        $this->db->select("*");
        $this->db->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
        $this->db->where("ip_invoices.invoice_id", $invoice_id);
        $client_email = $this->db->get("ip_invoices")->result_array();

        //INSERT INTO IP_INVOICES_STATUS_HISTORY
        $data_invoice_status_history = array(
            "id_invoice" => $invoice_id,
            "id_status" => $invoice_status,
            "date_changed" => date("Y-m-d- H:i:s"),
            "staff_comments" => $this->input->post('staff_comments'),
            "notes_to_customer" => $this->input->post('notes_to_customer'),
            "user_id" => $this->session->userdata('user_id')
        );
        $this->db->insert("ip_invoices_status_history", $data_invoice_status_history);

        if ($invoice_status == 1) {
            $status = "Quote";
        } else if ($invoice_status == 2) {
            $status = "Sent";
        } else if ($invoice_status == 3) {
            $status = "Denied";
        } else if ($invoice_status == 4) {
            $status = "Paid";
        }
        //SEND THE EMAIL TO THE CUSTOMER JUST WHEN THE STATUS IS SENT
        if ($invoice_number == "") {
            $invoice_number_email = $invoice_id;
        } else {
            $invoice_number_email = $invoice_number;
        }
        //CHECK IF ALERT EXISTS
        $this->db->where("invoice_id", $invoice_id);
        $alerts_data = $this->db->get("ip_customeralerts")->result_object();
        if ($alerts_data == null) {
            //INSERT CUSTOMER ALERTS
            $this->db->set("message_qtt", 1);
            $this->db->set("invoice_id", $invoice_id);
            $this->db->insert("ip_customeralerts");
        } else {
            //UPDATE CUSTOMER ALERTS
            $this->db->set("message_qtt", 1);
            $this->db->where("invoice_id", $invoice_id);
            $this->db->update("ip_customeralerts");
        }
        $to = $client_email[0]['client_email'];
        $subject = "The Invoice $invoice_number_email status has been changed @ " . date("m-d-Y H:i:s");
        $message = "The Invoice $invoice_number_email status has been changed to <b>$status</b><br><br>" . $data_invoice_status_history['notes_to_customer'] . "<br>You can access the link below to check your orders/invoices status or make payments using you email and phone number as password,<br><a href='https://365laptoprepair.com/my'>Customer Dashboard</a><br>Thank you.";
        $headers = "Content-Type:text/html; charset=UTF-8\n";
        $headers .= "From:  365laptoprepair.com<support@365laptoprepair.com>\n";
        $headers .= "X-Sender:  <support@365laptoprepair.com>\n"; //email do servidor //que enviou
        $headers .= "X-Mailer: PHP  v" . phpversion() . "\n";
        $headers .= "X-IP:  " . $_SERVER['REMOTE_ADDR'] . "\n";
        $headers .= "Return-Path:  <support@365laptoprepair.com>\n"; //caso a msg //seja respondida vai para  este email.
        $headers .= "MIME-Version: 1.0\n";

        mail($to, $subject, $message, $headers);


        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = array();

            foreach ($this->input->post('custom') as $custom) {
                $db_array[str_replace(']', '', str_replace('custom[', '', $custom['name']))] = $custom['value'];
            }

            $this->load->model('custom_fields/mdl_invoice_custom');
            $this->mdl_invoice_custom->save_custom($invoice_id, $db_array);
        }

        echo json_encode($response);
    }

    public function save_invoice_tax_rate() {
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoice_tax_rates->run_validation()) {
            $this->mdl_invoice_tax_rates->save();

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_invoice_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    public function create() {
        $this->load->model('invoices/mdl_invoices');

        $store = $this->input->post("store");

        if ($this->mdl_invoices->run_validation()) {
            $invoice_id = $this->mdl_invoices->create($store);

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        //GET CLIENT ID

        $this->db->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
        $this->db->where("ip_invoices.invoice_id", $invoice_id);
        $customer_data = $this->db->get("ip_invoices")->result_object();
        $custom_alert = array(
            "client_id" => $customer_data[0]->client_id,
            "invoice_id" => $invoice_id,
            "message_qtt" => 1
        );
        $this->db->insert("ip_customeralerts", $custom_alert);





        echo json_encode($response);
    }

    public function create_recurring() {
        $this->load->model('invoices/mdl_invoices_recurring');

        if ($this->mdl_invoices_recurring->run_validation()) {
            $this->mdl_invoices_recurring->save();

            $response = array(
                'success' => 1,
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function get_item() {
        $this->load->model('invoices/mdl_items');

        $item = $this->mdl_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    public function modal_create_invoice() {
        $this->load->module('layout');

        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');
        $stores = $this->db->get("ip_stores")->result_object();
        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'client_name' => $this->input->post('client_name'),
            //'clients' => $this->mdl_clients->get()->result(),
            'stores' => $stores
        );

        $this->layout->load_view('invoices/modal_create_invoice', $data);
    }

    public function modal_create_recurring() {
        $this->load->module('layout');

        $this->load->model('mdl_invoices_recurring');

        $data = array(
            'invoice_id' => $this->input->post('invoice_id'),
            'recur_frequencies' => $this->mdl_invoices_recurring->recur_frequencies
        );

        $this->layout->load_view('invoices/modal_create_recurring', $data);
    }

    public function get_recur_start_date() {
        $invoice_date = $this->input->post('invoice_date');
        $recur_frequency = $this->input->post('recur_frequency');

        echo increment_user_date($invoice_date, $recur_frequency);
    }

    public function modal_change_client() {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'client_name' => $this->input->post('client_name'),
            'invoice_id' => $this->input->post('invoice_id'),
            'clients' => $this->mdl_clients->get()->result(),
        );

        $this->layout->load_view('invoices/modal_change_client', $data);
    }

    public function change_client() {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_name = $this->input->post('client_name');
        $client = $this->mdl_clients->where('client_name', $this->db->escape_str($client_name))
                        ->get()->row();

        if (!empty($client)) {
            $client_id = $client->client_id;
            $invoice_id = $this->input->post('invoice_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('ip_invoices', $db_array);

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function modal_copy_invoice() {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->mdl_invoices->where('ip_invoices.invoice_id', $this->input->post('invoice_id'))->get()->row()
        );

        $this->layout->load_view('invoices/modal_copy_invoice', $data);
    }

    public function copy_invoice() {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->input->post('invoice_id');

            $this->mdl_invoices->copy_invoice($source_id, $target_id);

            $response = array(
                'success' => 1,
                'invoice_id' => $target_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    public function modal_create_credit() {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->mdl_invoices->where('ip_invoices.invoice_id', $this->input->post('invoice_id'))->get()->row(),
        );

        $this->layout->load_view('invoices/modal_create_credit', $data);
    }

    public function create_credit() {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->input->post('invoice_id');

            $this->mdl_invoices->copy_credit_invoice($source_id, $target_id);

            // Set source invoice to read-only
            if ($this->config->item('disable_read_only') == false) {
                $this->mdl_invoices->where('invoice_id', $source_id);
                $this->mdl_invoices->update('ip_invoices', array('is_read_only' => '1'));
            }

            // Set target invoice to credit invoice
            $this->mdl_invoices->where('invoice_id', $target_id);
            $this->mdl_invoices->update('ip_invoices', array('creditinvoice_parent_id' => $source_id, 'store' => $this->input->post('store')));

            $this->mdl_invoices->where('invoice_id', $target_id);
            $this->mdl_invoices->update('ip_invoice_amounts', array('invoice_sign' => '-1'));

            $response = array(
                'success' => 1,
                'invoice_id' => $target_id,
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

}
