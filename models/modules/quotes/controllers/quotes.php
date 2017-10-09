<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Quotes extends Admin_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_quotes');
    }

    public function index() {
        // Display all quotes by default
        redirect('quotes/status/all');
    }

    public function show_alert_complete($quote_number) {

        $this->db->select("complete_log");
        $this->db->where("quote_number", $quote_number);
        $ip_quotes = $this->db->get("ip_quotes")->result_array();
        $message = $ip_quotes[0]['complete_log'];

        echo "<script>alert('$message');</script>";
    }

    public function complete_order() {
        $this->load->model('mdl_quote_items');
        $order_number = $this->input->post("order_number");
        $order_id = $this->input->post("order_id");
        $complete_sentence = "Completed by Admin " . $this->session->userdata("user_name") . "@" . date("Y-m-d H:i:s");
        $this->db->where("quote_number", $order_number);
        $this->db->set("complete", 1);
        $this->db->set("complete_log", $complete_sentence);
        $this->db->update("ip_quotes");

        $this->db->where("oid", $order_number);
        $this->db->set("complete", 1);
        $this->db->set("complete_time", date("Y-m-d h:i:s"));
        $this->db->set("returnedresponse", 2);
        $this->db->set("sysdata", $complete_sentence);
        $this->db->update("orders");

        //GET QUOTE(ORDERS) DATA
        $this->db->where("quote_id", $order_id);
        $orders_data = $this->db->get("ip_quotes")->result_object();


        //GET ITEMS FROM QUOTE(ORDER)
        $order_items = $this->mdl_quote_items->where('quote_id', $order_id)->get()->result();
        //GET DATA AND CREATE ARRAY TO CREATE NEW INVOICE
        $invoice_data = array(
            "invoice_number" => $order_number . "/1",
            "client_id" => $orders_data[0]->client_id,
            "user_id" => $this->session->userdata("user_id"),
            "invoice_status_id" => 4,
            "is_read_only" => 1,
            "invoice_date_created" => date("Y-m-d"),
            "invoice_time_created" => date("H:i:s"),
            "invoice_date_modified" => date("Y-m-d"),
            "invoice_date_due" => date("Y-m-d"),
            "ticket_id" => $order_number,
            "store" => $orders_data[0]->store
        );
        //INSERT THE NEW INVOICE
        $this->db->insert("ip_invoices", $invoice_data);

        //GET INVOICE ID
        $this->db->where($invoice_data);
        $invoice_data = $this->db->get("ip_invoices")->result_object();
        //INSERT ITEMS INTO THE INVOICE
       
        if($invoice_data != null){
        foreach ($order_items as $order_items) {
            $invoice_items = array(
                "invoice_id" => $invoice_data[0]->invoice_id,
                "item_product_id" => $order_items->item_product_id,
                "item_date_added" => date("Y-m-d"),
                "item_name" => $order_items->item_name,
                "item_description" => $order_items->item_description,
                "item_quantity" => $order_items->item_quantity,
                "item_price" => $order_items->item_price,
                "item_discount_amount" => $order_items->item_discount_amount
            );
            $invoice_total += $order_items->item_price;
            $this->db->insert("ip_invoice_items", $invoice_items);
            
            
        }
     
        //INSERT INTO INVOICE_AMOUNTS
        $invoice_amounts = array(
            "invoice_id" => $invoice_data[0]->invoice_id,
            "invoice_item_subtotal" => $invoice_total,
            "invoice_total" => $invoice_total
            
        );
        $this->db->insert("ip_invoice_amounts", $invoice_amounts);
        
        //INSERT INTO IP_INVOICE_STATUS_HISTORY
        $status_array = array(
            "id_invoice" => $invoice_data[0]->invoice_id,
            "id_status" => 4,
            "date_changed" => date("Y-m-d"),
            "staff_comments" => "Created by manually order completed",
            "user_id" => $this->session->userdata("user_id")
        );
        $this->db->insert("ip_invoices_status_history", $status_array);
        
        
        //GET PAYMENT TYPE
        if($orders_data[0]->payment_status == 1)
        {
            $payment_method = "Authorize net";
        }
        else if($orders_data[0]->payment_status == 2)
        {
            $payment_method = "Paypal";
        }
        $this->db->where("payment_method_name", $payment_method);
        $payment_method_data = $this->db->get("ip_payment_methods")->result_object();
        
        //INSERT THE PAYMENT FOR THE NEW INVOICE
        $invoice_payment = array(
            "invoice_id" => $invoice_data[0]->invoice_id,
            "payment_method_id" =>$payment_method_data[0]->payment_method_id,
            "payment_date" => date("Y-m-d"),
            "payment_time" => date("H:i:s"),
            "payment_amount" => $invoice_total,
            "store" => $orders_data[0]->store
        );

        $this->db->insert("ip_payments", $invoice_payment);
        
        //GET MESSAGE QTT IF EXISTS
        $this->db->where("invoice_id", $invoice_data[0]->invoice_id);
        $message_data = $this->db->get("ip_customeralerts")->result_object();
        $message_qtt = 1;
        //INSERT THE MESSAGE_ALERT TO SHOW TO THE CUSTOMER IN THE CUSTOMER DASHBOARD
        $alert_array = array(
            "client_id" => $orders_data[0]->client_id,
            "invoice_id" => $invoice_data[0]->invoice_id,
            "message_qtt" => $message_qtt+=$message_data[0]->message_qtt
        );
        $this->db->insert("ip_customeralerts", $alert_array);
}
        echo "Order has been confirmed!";
    }

    public function mark_fraud() {
        $order_number = $this->input->post("order_number");
        $this->db->where("oid", $order_number);
        $this->db->set("fraud", 1);
        $this->db->update("orders");

        $this->db->where("quote_number", $order_number);
        $this->db->set("fraud", 1);
        $this->db->update("ip_quotes");

        echo "Order has been marked as fraud!";
    }

    public function show_log($quote_number) {
        $this->db->select("oid_ref");
        $this->db->where("oid", $quote_number);
        $orders_data = $this->db->get("orders")->result_array();

        $this->db->select("*");
        if ($orders_data[0]['oid_ref'] == 0) {
            $this->db->where("oid", $quote_number);
        } else {
            $this->db->where("oid", $orders_data[0]['oid_ref']);
        }
        $payment_status['payment_status'] = $this->db->get("orders")->result_array();

        $this->load->view("show_epayment_log", $payment_status);
    }

    public function send_agreements($ticket_id, $client_id) {

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
        $this->db->or_where("id_ticket", $ticket_id);
        $verify_insert_get = $this->db->get();
        $get_rows = $verify_insert_get->num_rows();
        if ($get_rows == null) {

            $this->db->insert('ip_agreement_terms_x_client', $insert_values_agreement);
        }
        $this->db->select("*");
        $this->db->from("ip_agreement_client_on_turn");
        $this->db->where("client_id", $client_agreement_on_turn['client_id']);
        $this->db->or_where("ticket_id", $client_agreement_on_turn['ticket_id']);
        $receive = $this->db->get();
        $get_rows_agreements = $receive->num_rows();

        if ($get_rows_agreements == null) {
            $this->db->insert('ip_agreement_client_on_turn', $client_agreement_on_turn);

            echo "<script>alert('Agreement Terms was Sent!'); </script>";

            echo "<script>window.close();</script>";
        } else {

            echo "<script>alert('Waiting for client signature...'); </script>";
            echo "<script>window.close();</script>";
        }
    }

    public function set_client_on_turn() {

        $id_client = $this->input->post('id_client');

        //$id_client = $_GET["id"];

        $passing_newclient_id = array(
            'id_client_on_turn' => $id_client,
        );

        $this->db->insert('ip_client_to_ticket', $passing_newclient_id);
    }

    public function status($status = 'all', $page = 0) {
        // Determine which group of quotes to load
        switch ($status) {
            /* case 'draft':
              $this->mdl_quotes->is_draft();
              break;
              case 'sent':
              $this->mdl_quotes->is_sent();
              break;
              case 'viewed':
              $this->mdl_quotes->is_viewed();
              break;
              case 'approved':
              $this->mdl_quotes->is_approved();
              break;
              case 'rejected':
              $this->mdl_quotes->is_rejected();
              break;
              case 'canceled':
              $this->mdl_quotes->is_canceled();
              break;
             */

            case 'diagnosing':
                $this->mdl_quotes->is_diagnosing();
                break;
            case 'waiting_on_approval':
                $this->mdl_quotes->is_waiting_on_approval();
                break;
            case 'ordered_parts':
                $this->mdl_quotes->is_ordered_parts();
                break;
            case 'repair_completed':
                $this->mdl_quotes->is_repair_completed();
                break;
            case 'accepted_by_client':
                $this->mdl_quotes->is_accepted_by_client();
                break;
            case 'returned_to_shop':
                $this->mdl_quotes->is_returned_to_shop();
                break;
            case 'repairing':
                $this->mdl_quotes->is_repairing();
                break;
            case 'repair_denied':
                $this->mdl_quotes->is_repair_denied();
                break;
            case 'new_order':
                $this->mdl_quotes->is_new_order();
                break;
            case 'waiting_for_package':
                $this->mdl_quotes->is_waiting_for_package();
                break;
            case 'update':
                $this->mdl_quotes->is_update();
                break;
            case 'received':
                $this->mdl_quotes->is_received();
                break;
        }

        $this->mdl_quotes->paginate(site_url('quotes/status/' . $status), $page);
        $quotes = $this->mdl_quotes->result();

        $this->layout->set(
                array(
                    'quotes' => $quotes,
                    'status' => $status,
                    'filter_display' => true,
                    'filter_placeholder' => lang('filter_quotes'),
                    'filter_method' => 'filter_quotes',
                    'quote_statuses' => $this->mdl_quotes->statuses()
                )
        );

        $this->layout->buffer('content', 'quotes/index');
        $this->layout->render();
    }

    public function view($quote_id) {

        $this->load->model('mdl_quote_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('mdl_quote_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_quote_custom');
        $this->load->library('encrypt');

        $quote_custom = $this->mdl_quote_custom->where('quote_id', $quote_id)->get();

        if ($quote_custom->num_rows()) {
            $quote_custom = $quote_custom->row();

            unset($quote_custom->quote_id, $quote_custom->quote_custom_id);

            foreach ($quote_custom as $key => $val) {
                $this->mdl_quotes->set_form_value('custom[' . $key . ']', $val);
            }
        }

        $quote = $this->mdl_quotes->get_by_id($quote_id);


        if (!$quote) {
            show_404();
        }

        $this->layout->set(
                array(
                    'quote' => $quote,
                    'items' => $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
                    'quote_id' => $quote_id,
                    'tax_rates' => $this->mdl_tax_rates->get()->result(),
                    'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
                    'custom_fields' => $this->mdl_custom_fields->by_table('ip_quote_custom')->get()->result(),
                    'custom_js_vars' => array(
                        'currency_symbol' => $this->mdl_settings->setting('currency_symbol'),
                        'currency_symbol_placement' => $this->mdl_settings->setting('currency_symbol_placement'),
                        'decimal_point' => $this->mdl_settings->setting('decimal_point')
                    ),
                    'quote_statuses' => $this->mdl_quotes->statuses()
                )
        );
        $this->layout->buffer(
                array(
                    array('modal_delete_quote', 'quotes/modal_delete_quote'),
                    array('modal_add_quote_tax', 'quotes/modal_add_quote_tax'),
                    array('content', 'quotes/view', $data)
                )
        );

        $this->layout->render();
    }

    public function delete($quote_id) {
        $this->db->where("id_ticket", $quote_id);
        $this->db->delete("ip_quotes_status_history");

        $this->db->where("quote_id", $quote_id);
        $quote_data = $this->db->get("ip_quotes")->result_object();

        $this->db->where("oid", $quote_data[0]->quote_number);
        $this->db->delete("orders");

        $this->db->where("id_ticket", $quote_data[0]->quote_number);
        $this->db->delete("ip_quotes_status_history");
        // Delete the quote
        $this->mdl_quotes->delete($quote_id);

        if ($this->uri->segment(4) == "uncomplete") {
            // Redirect to uncomplete
            redirect('quotes/status/uncomplete');
        } if ($this->uri->segment(4) == "fraud") {
            // Redirect to fraud
            redirect('quotes/status/fraud');
        } else {
            // Redirect to quote index
            redirect('quotes/index');
        }
    }

    public function delete_item($quote_id, $item_id) {




        //UPDATE INFO WAREHOUSE SYSTEM
        $this->db->select("*");
        $this->db->join("ip_quotes", "ip_quotes.quote_id = ip_quote_items.quote_id");
        $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
        $this->db->where("ip_quote_items.quote_id", $quote_id);
        $product_data = $this->db->get("ip_quote_items")->result_array();

        $this->db->where("invoice_number", $product_data[0]['quote_number'] . "/1");
        $invoice_data = $this->db->get("ip_invoices")->result_object();

        $this->db->where("item_id", $item_id);
        $quote_items = $this->db->get("ip_quote_items")->result_object();
        foreach ($quote_items as $quote_items) {

            $this->db->where("item_name", $quote_items->item_name);
            $this->db->where("item_description", $quote_items->item_description);
            $this->db->delete("ip_invoice_items");
        }

        // Delete quote item
        $this->load->model('mdl_quote_items');
        $this->mdl_quote_items->delete($item_id);






        foreach ($product_data as $product_data) {
            $post = array(
                'kp' => '/1=?6|[\zb+QQG&v>ZxS9n#r27 \p."UtpJr?!P-AOo%HW[}_m]T{\.}a?ZsVr~k]#wEgk6ry+R|9-!SDr*[R>I>ku23h9f[Pl?k)Rb+qx4O?ZOv-3O_(B&-e$o9b.jEk}xD_x:GU8T/hZvO0 `gLQaM/2aY%W#7MyHS`z2}6wH+j"gK-D$rA9KG3GhB;aBIW,lM@PQ$SL, rx:5t;3]{q;:8Ub>]w{&wX_a!H."(/zUeyY)6"{{**,j,',
                'un' => '365inpl_' . mktime(),
                'orderid' => $quote_id,
                'buyer' => $product_data['client_id'],
                'shipping' => $shipping,
                'bcn' => $product_data['item_product_id'],
                'product_qtd' => $product_data['item_quantity'],
                'warehouse' => serialize(array(array('warehouseid' => $product_data['wid'], 'pricesold' => $product_data['item_price']), array('warehouseid' => $product_data['wid'], 'pricesold' => $product_data['item_price']), array('warehouseid' => $product_data['wid'], 'pricesold' => $product_data['item_price'])))
            );
        }





        $ch = curl_init('http://www.dev.la-tronics.com/Mywarehouse/Comm');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


// execute!
//$response = curl_exec($ch);
// close the connection, release resources used
//curl_close($ch);
// do anything you want with your response
//var_dump($response);
        // Redirect to quote view
        redirect('quotes/view/' . $quote_id);
    }

    public function generate_pdf($quote_id, $stream = true, $quote_template = null) {
        $this->load->helper('pdf');

        if ($this->mdl_settings->setting('mark_quotes_sent_pdf') == 1) {
            $this->mdl_quotes->mark_sent($quote_id);
        }

        generate_quote_pdf($quote_id, $stream, $quote_template);
    }

    public function generate_receipt($quote_id) {
        $get_quote_id['quote_id'] = $quote_id;
        //load mPDF library
        $this->load->library('m_pdf');
        //load mPDF library





        $html = $this->load->view('receipt_client', $get_quote_id, true); //load the pdf_output.php by passing our data and get all data in $html varriable.
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();
        $pdf->SetJS('this.print();');
        //generate the PDF!

        $pdf->WriteHTML($html, 2);

        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output();
    }

    public function delete_quote_tax($quote_id, $quote_tax_rate_id) {
        $this->load->model('mdl_quote_tax_rates');
        $this->mdl_quote_tax_rates->delete($quote_tax_rate_id);

        $this->load->model('mdl_quote_amounts');
        $this->mdl_quote_amounts->calculate($quote_id);

        redirect('quotes/view/' . $quote_id);
    }

    public function recalculate_all_quotes() {
        $this->db->select('quote_id');
        $quote_ids = $this->db->get('ip_quotes')->result();

        $this->load->model('mdl_quote_amounts');

        foreach ($quote_ids as $quote_id) {
            $this->mdl_quote_amounts->calculate($quote_id->quote_id);
        }
    }

    function test() {
        $this->load->view('curl_test');
    }

}
