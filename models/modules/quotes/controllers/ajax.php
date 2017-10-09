<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


date_default_timezone_set('America/Los_Angeles');

class Ajax extends Admin_Controller {

    public $ajax_controller = true;

    public function tickets_quantity() {
        $date_from = date("Y-m-d", strtotime($this->input->post("date_from")));
        $date_to = date("Y-m-d", strtotime($this->input->post("date_to")));

        $button_all = $this->input->post("all");
        if ($this->input->post("date_to") == null) {
            $date_to = date("Y-m-d");
        }
        //GET ALL STATUS
        $this->db->select("*");
        $this->db->where("status <>", "Repair complete");
        $this->db->where("status <>", "Sent");
        $this->db->where("status <>", "Waiting for response");
        $this->db->where("status <>", "Done");
        $this->db->where("status <>", "Performing diagnostic");
        $this->db->where("status <>", "Processing");
        $this->db->where("status <>", "Send Shipping item");
        $this->db->where("status <>", "Received");
        $this->db->where("status <>", "Order shipped");
        $this->db->where("status <>", "Uncategorized");
        $this->db->where("status <>", "Price quoted");
        $this->db->where("status <>", "Uncomplete");
        $status = $this->db->get("status")->result_array();


        $this->db->select("user_store");
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $ip_users = $this->db->get("ip_users")->result_array();
        $store = $ip_users[0]['user_store'];


        $this->db->select("*");
        $this->db->where("setting_key", "days_ticket");
        $ip_settings = $this->db->get("ip_settings")->result_array();

        if ($this->input->post("date_from") == null) {
            $date_from = date('Y-m-d', strtotime('-' . $ip_settings[0]['setting_value'] . ' days'));
        }

        $this->db->select("*");
        foreach ($status as $status) {

            $this->db->or_where("quote_status_id", $status['id']);
            if ($button_all != 1) {
                $this->db->where("quote_date_created between '$date_from' and '$date_to'");
            }
            if ($store != 1) {
                $this->db->where("store", $store);
            }
            $ip_quotes = $this->db->get("ip_quotes");
            $ip_quotes_data = $ip_quotes->result_array();
            $status_array[] = array(
                "css_label" => $status['css_label'],
                "status_id" => $ip_quotes_data[0]['quote_status_id'],
                "status_quantity" => $ip_quotes->num_rows(),
                "status" => $status['status']
            );
        }
        $data['data'] = $status_array;


        $this->load->view("calc_status", $data);
    }

    public function search_items() {
        $item_name_id = $this->input->post("item_name_id");
        if ($item_name_id != "") {
            $this->db->like("product_name", $item_name_id);
            $data['products'] = $this->db->get("ip_products")->result_object();
            $this->load->view("items_search", $data);
        } else {
            $data['products'] = 1;
            $this->load->view("items_search", $data);
        }
    }

    public function info_display() {
        $date_from = date("Y-m-d", strtotime($this->input->post("date_from")));

        $date_to = date("Y-m-d", strtotime($this->input->post("date_to")));
        $input_data = $this->input->post("all_data");
        if (is_numeric($input_data)) {
            $numeric = 1;
        } else {
            $numeric = 0;
        }
        $all = $this->input->post("all");
        if ($date_to == "1969-12-31") {

            $date_to = date("Y-m-d");
        }

        $display_data['display_data'] = array(
            "date_from" => $date_from,
            "date_to" => $date_to,
            "input_data" => $input_data,
            "all" => $all,
            "numeric" => $numeric
        );

        $this->load->view("info_display", $display_data);
    }

    public function recent_tickets() {
        $date_from = date("Y-m-d", strtotime($this->input->post("date_from")));
        $date_to = date("Y-m-d", strtotime($this->input->post("date_to")));
        $input_data = $this->input->post("all_data");
        $all = $this->input->post("all");

        if (is_numeric($input_data)) {
            $numeric = 1;
        } else {
            $numeric = 0;
        }
        $all = $this->input->post("all");
        if ($date_to == "1969-12-31") {

            $date_to = date("Y-m-d");
        }
        if ($date_to == "1969-12-31") {

            $date_to = date("Y-m-d");
        }

        $display_data['display_data'] = array(
            "date_from" => $date_from,
            "date_to" => $date_to,
            "input_data" => $input_data,
            "all" => $all,
            "numeric" => $numeric
        );
        $this->load->view("recent_tickets", $display_data);
    }

    public function load_clientdata() {
        $client_name = $this->input->post("client_name");

        $this->db->where("client_name", $client_name);
        $client_array = $this->db->get("ip_clients")->result_array();


        echo json_encode($client_array[0]);
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

    public function customer_ticket_search() {


        //GET STORE FROM USER
        $this->db->select("user_store");
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $store = $this->db->get("ip_users")->result_array();

        //CHECK IF THE INPUT GET A NUMBER OR NAME
        $this->db->select("quote_number");
        $this->db->like("quote_number", $this->input->get('term'));
        $row_quote_number = $this->db->get("ip_quotes")->num_rows();

        //START THE QUERY TO GET THE RESULTS
        $this->db->select("client_name,quote_number");
        $this->db->join("ip_quotes", "ip_quotes.client_id = ip_clients.client_id");
        if ($this->input->get('client_id') != -1) {
            $this->db->where("ip_clients.client_id", $this->input->get('client_id'));
        }

        if ($store[0]['user_store'] != 1) {
            $this->db->where("ip_quotes.store", $store[0]['user_store']);
        }
        if ($row_quote_number == 0) {
            $this->db->or_like("ip_clients.client_name", $this->input->get('term'));
        } else {
            $this->db->or_like("ip_quotes.quote_number", $this->input->get('term'));
        }

        $this->db->group_by("ip_clients.client_name", "asc");
        $this->db->limit("10");
        $client_autocomplete = $this->db->get("ip_clients")->result_array();

        //FOREACH TO CREATE AN ARRAY TO AUTOCOMPLETE
        foreach ($client_autocomplete as $client_autocomplete) {
            if ($row_quote_number == 0) {
                $array[] = $client_autocomplete['client_name'];
            } else {
                $array[] = $client_autocomplete['quote_number'];
            }
        }
        echo json_encode($array);
    }

    public function update_date() {

        if ($this->input->post("update_date") == null) {
            $date = "0000-00-00";
        } else {
            $date = $this->input->post("update_date");
        }
        if ($this->input->post("date_to") == null) {
            $date_to = date("Y-m-d");
        } else {
            $date_to = $this->input->post("date_to");
        }
        $this->db->select("*");
        $this->db->where("user_id", $this->session->userdata('user_id'));
        $get_store = $this->db->get("ip_users")->result_array();




        $this->db->select("*");
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $rows_ip_ticket_rows = $this->db->get("ip_ticket_num_rows")->num_rows();
        if (is_numeric($this->input->post("filter_data"))) {
            $ticket_id = $this->input->post("filter_data");
            $client_name = "";
        } else {
            $client_name = $this->input->post("filter_data");
            $ticket_id = 0;
        }

        if ($rows_ip_ticket_rows == 0) {
            $update_limit = array(
                "num_rows" => 15,
                "user_id" => $this->session->userdata("user_id"),
                "store_id" => $get_store[0]['user_store'],
                "ticket_id" => $ticket_id,
                "client_name" => $client_name,
                "date" => date("Y-m-d", strtotime($date)),
                "date_to" => date("Y-m-d", strtotime($date_to))
            );
            $this->db->insert("ip_ticket_num_rows", $update_limit);
        } else {
            $update_limit = array(
                "user_id" => $this->session->userdata("user_id"),
                "store_id" => $get_store[0]['user_store'],
                "ticket_id" => $ticket_id,
                "client_name" => $client_name,
                "date" => date("Y-m-d", strtotime($date)),
                "date_to" => date("Y-m-d", strtotime($date_to))
            );

            $this->db->where("user_id", $this->session->userdata("user_id"));
            $this->db->update("ip_ticket_num_rows", $update_limit);
        }
    }

    public function change_limit() {

        if ($this->input->post("date") == null) {
            $date = "0000-00-00";
        } else {
            $date = $this->input->post("date");
        }
        if ($this->input->post("date_to") == null) {
            $date_to = date("Y-m-d");
        } else {
            $date_to = $this->input->post("date_to");
        }
        $this->db->select("*");
        $this->db->where("user_id", $this->session->userdata('user_id'));
        $get_store = $this->db->get("ip_users")->result_array();




        $this->db->select("*");
        $this->db->where("user_id", $this->session->userdata("user_id"));
        $rows_ip_ticket_rows = $this->db->get("ip_ticket_num_rows")->num_rows();
        if ($rows_ip_ticket_rows == 0) {
            $update_limit = array(
                "num_rows" => $this->input->post("setting_limit"),
                "user_id" => $this->session->userdata("user_id"),
                "store_id" => $get_store[0]['user_store'],
                "date" => date("Y-m-d", strtotime($date)),
                "date_to" => date("Y-m-d", strtotime($date_to))
            );
            $this->db->insert("ip_ticket_num_rows", $update_limit);
        } else {
            $update_limit = array(
                "num_rows" => $this->input->post("setting_limit"),
                "user_id" => $this->session->userdata("user_id"),
                "store_id" => $get_store[0]['user_store'],
                "date" => date("Y-m-d", strtotime($date)),
                "date_to" => date("Y-m-d", strtotime($date_to))
            );
            $this->db->where("user_id", $this->session->userdata("user_id"));
            $this->db->update("ip_ticket_num_rows", $update_limit);
        }
    }

    public function save() {

        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('item_lookups/mdl_item_lookups');
        $this->load->library('encrypt');
        $item_price = 0;
        $quote_id = $this->input->post('quote_id');

        $this->mdl_quotes->set_id($quote_id);

        if ($this->mdl_quotes->run_validation('validation_rules_save_quote')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                if ($item->item_name) {
                    $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                    $item->item_price = ($item->item_quantity ? standardize_amount($item->item_price) : floatval(0));
                    $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);

                    $item_id = ($item->item_id) ?: null;
                    unset($item->item_id);

                    $this->mdl_quote_items->save($item_id, $item);
                }
                $item_price += $item->item_price * $item->item_quantity;
            }

            if ($this->input->post('quote_discount_amount') === '') {
                $quote_discount_amount = floatval(0);
            } else {
                $quote_discount_amount = $this->input->post('quote_discount_amount');
            }

            if ($this->input->post('quote_discount_percent') === '') {
                $quote_discount_percent = floatval(0);
            } else {
                $quote_discount_percent = $this->input->post('quote_discount_percent');
            }

            // Generate new quote number if needed
            $quote_number = $this->input->post('quote_number');
            $quote_status_id = $this->input->post('quote_status_id');




            if (empty($quote_number) && $quote_status_id != 1) {
                $quote_group_id = $this->mdl_quotes->get_invoice_group_id($quote_id);

                $quote_number = $this->mdl_quotes->get_quote_number($quote_group_id);
            }



            $password = $this->input->post('client_os_password');
            $encrypted_string = $this->encrypt->encode($password);

            if ($item_price == 0) {
                $item_price = $this->input->post('amount');
            }



            $db_array = array(
                'quote_number' => $quote_number,
                'quote_date_created' => date_to_mysql($this->input->post('quote_date_created')),
                'quote_date_expires' => date_to_mysql($this->input->post('quote_date_expires')),
                'quote_status_id' => $quote_status_id,
                'amount' => $item_price,
                'brand' => $this->input->post('brand'),
                'model' => $this->input->post('model'),
                'serial_number' => $this->input->post('serial_number'),
                'data_recovery' => $this->input->post('data_recovery'),
                'client_os_password' => $encrypted_string,
                'accessories_included' => $this->input->post('accessories_included'),
                'problem_description_product' => $this->input->post('problem_description_product'),
                'quote_password' => $this->input->post('quote_password'),
                'notes' => $this->input->post('notes'),
                'quote_discount_amount' => $quote_discount_amount,
                'quote_discount_percent' => $quote_discount_percent,
                'store' => $this->input->post('store'),
            );



            //UPDATE ESTIMATES AUTOMATIC INVOICE
            $this->db->where("invoice_number", "$quote_number/1");
            $invoice_data = $this->db->get("ip_invoices")->result_object();

            if ($invoice_data[0]->invoice_status_id == 1) {

                $quote_items3 = $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result();
                if ($quote_items3 != null) {
                    foreach ($quote_items3 as $quote_items3) {
                        $db_array_2 = array(
                            'invoice_id' => $invoice_data[0]->invoice_id,
                            'item_tax_rate_id' => $quote_items3->item_tax_rate_id,
                            'item_product_id' => $quote_items3->item_product_id,
                            'item_name' => $quote_items3->item_name,
                            'item_description' => $quote_items3->item_description,
                            'item_quantity' => $quote_items3->item_quantity,
                            'item_price' => $quote_items3->item_price,
                            'item_discount_amount' => $quote_items3->item_discount_amount,
                            'item_order' => $quote_items3->item_order
                        );
                    }

                    $this->db->where("item_name", $quote_items3->item_name);
                    $this->db->where("item_description", $quote_items3->item_description);
                    $item_row_invoice = $this->db->get("ip_invoice_items")->num_rows();
                    if ($item_row_invoice == 1) {
                        $this->db->update("ip_invoice_items", $db_array_2);
                    } else {
                        $this->db->insert("ip_invoice_items", $db_array_2);
                    }
                }
                $this->db->where("invoice_id", $invoice_data[0]->invoice_id);
                $this->db->set("invoice_total", $item_price);
                $this->db->set("invoice_balance", $item_price);
                $this->db->set("invoice_item_subtotal", $item_price);
                $this->db->update("ip_invoice_amounts");
            }



            // Recalculate for discounts
            $this->load->model('quotes/mdl_quote_amounts');
            $this->mdl_quote_amounts->calculate($quote_id);

            //START THE QUOTE_STATUS TRACK HISTORY
            $date = date('Y-m-d H:i:s');
            $ticket_history = array(
                'id_ticket' => $quote_number,
                'id_status' => $quote_status_id,
                'date_changed' => $date,
                'notes_to_customer' => $this->input->post('notes_to_customer'),
                'staff_comments' => $this->input->post('staff_comments'),
                'client_notified' => $this->input->post('send_email'),
                'id_user' => $this->session->userdata('user_id')
            );

            $send_email = $this->input->post('send_email');

            $this->db->insert("ip_quotes_status_history", $ticket_history);


            $this->db->select("*");
            $this->db->from("ip_clients");
            $this->db->join("ip_quotes", "ip_quotes.client_id = ip_clients.client_id");
            $this->db->where("ip_quotes.quote_id", $quote_id);
            $get_client_data = $this->db->get();
            $client_data = $get_client_data->result_array();

            //UPDATE NEW MESSAGES
            $this->db->where("order_id", $quote_id);
            $message_qttdata = $this->db->get("ip_customeralerts")->num_rows();

            if ($message_qttdata == 0) {
                $message_array = array(
                    "client_id" => $client_data[0]['client_id'],
                    "message_qtt" => 1,
                    "order_id" => $quote_id
                );
                $this->db->insert("ip_customeralerts", $message_array);
            } else {
                $this->db->set("message_qtt", 1);
                $this->db->set("order_id", $quote_id);
                $this->db->where("order_id", $quote_id);
                $this->db->update("ip_customeralerts");
            }
            //GET THE ORDER STATUS CHANGED

            $this->db->join("status", "status.id = ip_quotes.quote_status_id");
            $this->db->where("ip_quotes.quote_id", $quote_id);
            $status_changed = $this->db->get("ip_quotes")->result_array();


            $to = $client_data[0]['client_email'];
            $subject = "The order $quote_number status has been changed @ " . date("m-d-Y H:i:s", strtotime($date));
            $message = "The order $quote_number status has been changed to <b>" . $status_changed[0]['status'] . "</b><br>"
                    . "<br>" . $this->input->post('notes_to_customer') . "<br>Thank you.";
            $headers = "Content-Type:text/html; charset=UTF-8\n";
            $headers .= "From:  365laptoprepair.com<support@365laptoprepair.com>\n";
            $headers .= "X-Sender:  <support@365laptoprepair.com>\n"; //email do servidor //que enviou
            $headers .= "X-Mailer: PHP  v" . phpversion() . "\n";
            $headers .= "X-IP:  " . $_SERVER['REMOTE_ADDR'] . "\n";
            $headers .= "Return-Path:  <support@365laptoprepair.com>\n"; //caso a msg //seja respondida vai para  este email.
            $headers .= "MIME-Version: 1.0\n";

            if ($send_email == 'Yes') {

                mail($to, $subject, $message, $headers);
            }

            $this->mdl_quotes->save($quote_id, $db_array);
            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        if ($this->input->post('custom')) {
            $db_array = array();

            foreach ($this->input->post('custom') as $custom) {
                // I hate myself for this...
                $db_array[str_replace(']', '', str_replace('custom[', '', $custom['name']))] = $custom['value'];
            }

            $this->db->select("*");
            $this->db->join("ip_quotes", "ip_quotes.quote_id = ip_quote_items.quote_id");
            $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
            $this->db->where("ip_quote_items.quote_id", $quote_id);
            $product_data = $this->db->get("ip_quote_items")->result_array();



            foreach ($product_data as $product_data) {
                $post = array(
                    'kp' => '/1=?6|[\zb+QQG&v>ZxS9n#r27 \p."UtpJr?!P-AOo%HW[}_m]T{\.}a?ZsVr~k]#wEgk6ry+R|9-!SDr*[R>I>ku23h9f[Pl?k)Rb+qx4O?ZOv-3O_(B&-e$o9b.jEk}xD_x:GU8T/hZvO0 `gLQaM/2aY%W#7MyHS`z2}6wH+j"gK-D$rA9KG3GhB;aBIW,lM@PQ$SL, rx:5t;3]{q;:8Ub>]w{&wX_a!H."(/zUeyY)6"{{**,j,',
                    'un' => '365inpl_' . mktime(),
                    'buytype' => $this->input->post('store'),
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
            $response = curl_exec($ch);

// close the connection, release resources used
            curl_close($ch);

// do anything you want with your response
            var_dump($response);

            $this->load->model('custom_fields/mdl_quote_custom');
            $this->mdl_quote_custom->save_custom($quote_id, $db_array);
        }

        $this->db->select("*");
        $this->db->from("ip_clients");
        $this->db->join("ip_quotes", "ip_quotes.client_id = ip_clients.client_id");
        $this->db->where("ip_quotes.quote_id", $quote_id);
        $get_client_data = $this->db->get();
        $client_data = $get_client_data->result_array();



        //SEND THE TICKET INFORMATION TO 365LAPTOPREPAIR
        //VERIFY IF THE TICKET EXISTS IN 365LAPTOPREPAIR
        $this->db->select("*");
        $this->db->where("id_ticket", $quote_id);
        $data_history_ticket = $this->db->get("ip_quotes_status_history")->result_array();



        $this->db->select("*");
        $this->db->where("oid", $quote_number);
        $order_rows = $this->db->get("orders")->num_rows();


        //VALIDATE BUYTYPE
        $this->db->select("*");
        $this->db->where("id", $this->input->post('store'));
        $store_validate = $this->db->get("ip_stores")->result_array();
        $store = 0;
        if ($store_validate[0]['store_name'] == "Hawthorne") {
            $store = 7;
        } else
        if ($store_validate[0]['store_name'] == "Venice") {
            $store = 8;
        } else if (($store_validate[0]['store_name'] == "Website Sale") || ($store_validate[0]['store_name'] == "Website Repair")) {
            $store = 1;
        }

        $this->db->select("status");
        $this->db->where("oid", $quote_number);
        $order_status = $this->db->get("orders")->result_array();

        $test2 = unserialize($order_status[0]['status']);
        $count = count($test2);
        if ($store == 1) {
            if ($client_data[0]['quote_status_id'] == 2) {
                $status_id = 12;
            } else if ($client_data[0]['quote_status_id'] == 1) {
                $status_id = 1;
            } else if ($client_data[0]['quote_status_id'] == 3) {
                $status_id = 13;
            } else if ($client_data[0]['quote_status_id'] == 10) {
                $status_id = 13;
            } else if ($client_data[0]['quote_status_id'] == 4) {
                $status_id = 17;
            } else if ($client_data[0]['quote_status_id'] == 5) {
                $status_id = 14;
            } else if ($client_data[0]['quote_status_id'] == 9) {
                $status_id = 18;
            } else if ($client_data[0]['quote_status_id'] == 7) {
                $status_id = 1;
            } else if ($client_data[0]['quote_status_id'] == 8) {
                $status_id = 19;
            } else if ($client_data[0]['quote_status_id'] == 18) {
                $status_id = 10;
            } else if ($client_data[0]['quote_status_id'] == 17) {
                $status_id = 15;
            } else if ($client_data[0]['quote_status_id'] == 21) {
                $status_id = 11;
            } else if ($client_data[0]['quote_status_id'] == 19) {
                $status_id = 16;
            }
        } else {
            if ($client_data[0]['quote_status_id'] == 1) {
                $status_id = 8;
            } else if ($client_data[0]['quote_status_id'] == 2) {
                $status_id = 9;
            } else if ($client_data[0]['quote_status_id'] == 3) {
                $status_id = 10;
            } else if ($client_data[0]['quote_status_id'] == 4) {
                $status_id = 14;
            } else if ($client_data[0]['quote_status_id'] == 5) {
                $status_id = 11;
            } else if ($client_data[0]['quote_status_id'] == 6) {
                $status_id = 12;
            } else if ($client_data[0]['quote_status_id'] == 9) {
                $status_id = 18;
            } else if ($client_data[0]['quote_status_id'] == 10) {
                $status_id = 13;
            } else if ($client_data[0]['quote_status_id'] == 28) {
                $status_id = 30;
            }
        }



        foreach ($data_history_ticket as $data_history_ticket) {
            if ($this->input->post('send_email') == "Yes") {
                $notify = 1;
            } else {
                $notify = 0;
            }

            $data_array = array(
                'status' => $status_id,
                'comment' => "<strong>(" . $this->session->userdata('user_name') . "</strong>)" . $this->input->post('staff_comments'),
                'notified' => $notify,
                'msgclient' => $this->input->post('notes_to_customer'),
                'time' => $date
            );
        }

        $test2[$count + 1] = $data_array;








        $order_data = array(
            'buytype' => $store,
            'fname' => $client_data[0]['client_name'],
            'address' => $client_data[0]['client_address1'],
            'city' => $client_data[0]['client_city'],
            'state' => $client_data[0]['client_state'],
            'postcode' => $client_data[0]['client_zip'],
            'country' => $client_data[0]['client_country'],
            'tel' => $client_data[0]['client_phone'],
            'email' => $client_data[0]['client_email'],
            'status' => serialize($test2),
            'endprice' => $client_data[0]['amount']
        );

        if (($order_rows > 0) && ($store != 0)) {
            $this->db->where("oid", $quote_number);
            $this->db->update("orders", $order_data);

            if (($status_id == 6) || ($status_id == 5)) {
                $this->db->where("oid", $quote_number);
                $this->db->set("endprice", $this->input->post('amount'));
                $this->db->update("orders");
            }
        }
        $this->db->set("quote_total", $item_price);
        $this->db->where("quote_id", $quote_id);
        $this->db->update("ip_quote_amounts");

        echo json_encode($response);
    }

    public function save_quote_tax_rate() {
        $this->load->model('quotes/mdl_quote_tax_rates');

        if ($this->mdl_quote_tax_rates->run_validation()) {
            $this->mdl_quote_tax_rates->save();

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_quote_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    public function create() {
        $date = date('Y-m-d H:i:s');


        $this->load->model(
                array(
                    'invoices/mdl_invoices',
                    'invoices/mdl_items',
                    'quotes/mdl_quotes',
                    'quotes/mdl_quote_items',
                    'invoices/mdl_invoice_tax_rates',
                    'quotes/mdl_quote_tax_rates'
                )
        );
        if ($this->mdl_quotes->run_validation()) {
            $quote_id = $this->mdl_quotes->create();

            $response = array(
                'success' => 1,
                'quote_id' => $quote_id
            );


            /* $client_info = array(
              "client_name" => $client_name = $this->input->post("client_name"),
              "client_email" => $this->input->post("client_email"),
              "client_phone" => $this->input->post("client_phone"),
              "client_company" => $this->input->post("company_name"),
              "company_address" => $this->input->post("company_address"),
              "company_phone" => $this->input->post("company_phone"),
              "client_address_1" => $this->input->post("client_address"),
              "client_city" => $this->input->post("client_city"),
              "client_state" => $this->input->post("client_state"),
              "client_zip" => $this->input->post("client_zip"),
              "client_country" => $this->input->post("client_country")
              ); */





            $i = 0;
            $j = 0;
            $items_sum = 0;
            $product_id = $this->input->post("product_id");
            $product_name = $this->input->post("product_name");
            $description = $this->input->post("product_description");
            $product_quantity = $this->input->post("product_quantity");
            $product_price = $this->input->post("product_price");
            $items_checked = $this->input->post("items_checked");


            if ($items_checked == null) {
                foreach ($product_name as $product_data) {
                    if ($product_data["value"] != null) {
                        $product_info[] = array(
                            "quote_id" => $quote_id,
                            "item_product_id" => $product_id[$i]["value"],
                            "item_name" => $product_data['value'],
                            "item_description" => $description[$i]["value"],
                            "item_quantity" => $product_quantity[$i]["value"],
                            "item_date_added" => date("Y-m-d"),
                            "item_price" => $product_price[$i]['value']
                        );
                    }
                    $i++;
                }
            } else {
                foreach ($product_name as $product_data2) {
                    if ($product_data2["value"] != null) {
                        $product_info[] = array(
                            "quote_id" => $quote_id,
                            "item_product_id" => $product_id[$j]["value"],
                            "item_name" => $product_data2['value'],
                            "item_description" => $description[$j]["value"],
                            "item_quantity" => $product_quantity[$j]["value"],
                            "item_date_added" => date("Y-m-d"),
                            "item_price" => $product_price[$j]['value']
                        );
                    }
                    $j++;
                }
            }

            foreach ($product_info as $product_info) {
                $items_sum += (float) $product_info['item_price'];
                $this->db->insert("ip_quote_items", $product_info);
            }
            $this->db->set("amount", $items_sum);
            $this->db->where("quote_id", $quote_id);
            $this->db->update("ip_quotes");

            $this->db->where("quote_id", $quote_id);
            $this->db->set("complete", 1);
            $this->db->update("ip_quotes");

            $this->db->select("client_id, store, quote_number, amount");
            $this->db->from("ip_quotes");
            $this->db->where("quote_id", $quote_id);
            $result_object = $this->db->get();
            $result = $result_object->result_array();
            $rows = $result_object->num_rows();



            $setzero = array(
                'new_client' => '0'
            );
            if ($rows >= 0) {


                $this->db->where('client_id', $result[0]['client_id']);
                $this->db->update('ip_clients', $setzero);












                if ($this->input->post("client_name") != "") {
                    $this->db->set("client_name", $this->input->post("client_name"));
                }
                if ($this->input->post("client_email") != "") {
                    $this->db->set("client_email", $this->input->post("client_email"));
                }
                if ($this->input->post("client_phone") != "") {
                    $this->db->set("client_phone", $this->input->post("client_phone"));
                }
                if ($this->input->post("company_name") != "") {
                    $this->db->set("client_company", $this->input->post("company_name"));
                }
                if ($this->input->post("company_address") != "") {
                    $this->db->set("company_address", $this->input->post("company_address"));
                }
                if ($this->input->post("company_phone") != "") {
                    $this->db->set("company_phone", $this->input->post("company_phone"));
                }
                if ($this->input->post("client_address") != "") {
                    $this->db->set("client_address_1", $this->input->post("client_address"));
                }
                if ($this->input->post("client_city") != "") {
                    $this->db->set("client_city", $this->input->post("client_city"));
                }
                if ($this->input->post("client_state") != "") {
                    $this->db->set("client_state", $this->input->post("client_state"));
                }
                if ($this->input->post("client_zip") != "") {
                    $this->db->set("client_zip", $this->input->post("client_zip"));
                }
                if ($this->input->post("client_country") != "") {
                    $this->db->set("client_country", $this->input->post("client_country"));
                }
                $this->db->where('client_id', $result[0]['client_id']);
                $this->db->update("ip_clients");
            }




            //CHECK THE STORE
            $this->db->select("store_name");
            $this->db->where("id", $result[0]['store']);
            $store_data = $this->db->get("ip_stores")->result_array();
            if (($store_data[0]['store_name'] != "Website Sale") && ($store_data[0]['store_name'] != "Website Repair")) {
                //INSERT THE INVOICE AS DRAFT FROM ORDER
                $array_invoice = array(
                    "user_id" => $this->session->userdata('user_id'),
                    "client_id" => $result[0]['client_id'],
                    "invoice_status_id" => 1,
                    "invoice_date_created" => date("Y-m-d"),
                    "invoice_time_created" => date("H:i:s"),
                    "invoice_number" => $result[0]['quote_number'] . "/1",
                    "ticket_id" => $result[0]['quote_number'],
                    "store" => $result[0]['store']
                );
                $this->db->insert("ip_invoices", $array_invoice);




                //GET INVOICE_ID
                $this->db->select("invoice_id");
                $this->db->where($array_invoice);
                $invoice_data = $this->db->get("ip_invoices")->result_array();

                $array_invoice_status_history = array(
                    "id_invoice" => $invoice_data[0]['invoice_id'],
                    "id_status" => 1,
                    "date_changed" => date("Y-m-d H:i:s"),
                    "user_id" => $this->session->userdata('user_id')
                );
                $this->db->insert("ip_invoices_status_history", $array_invoice_status_history);

                $invoice_amounts = array(
                    "invoice_id" => $invoice_data[0]['invoice_id'],
                    "invoice_item_subtotal" => $result[0]['amount'],
                    "invoice_total" => $result[0]['amount'],
                    "invoice_balance" => $result[0]['amount']
                );
                $this->db->insert("ip_invoice_amounts", $invoice_amounts);

                $quote_items2 = $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result();


                foreach ($quote_items2 as $quote_items2) {
                    $db_array_2 = array(
                        'invoice_id' => $invoice_data[0]['invoice_id'],
                        'item_tax_rate_id' => $quote_items2->item_tax_rate_id,
                        'item_product_id' => $quote_items2->item_product_id,
                        'item_name' => $quote_items2->item_name,
                        'item_description' => $quote_items2->item_description,
                        'item_quantity' => $quote_items2->item_quantity,
                        'item_price' => $quote_items2->item_price,
                        'item_discount_amount' => $quote_items2->item_discount_amount,
                        'item_order' => $quote_items2->item_order
                    );

                    $this->mdl_items->save(null, $db_array_2);
                }
            }




            //START THE QUOTE_STATUS TRACK HISTORY

            $ticket_history = array(
                'id_ticket' => $quote_id,
                'id_status' => 1,
                'date_changed' => $date,
                'id_user' => $this->session->userdata('user_id')
            );



            $this->db->insert("ip_quotes_status_history", $ticket_history);

            $this->db->select("*");
            $this->db->from("ip_clients");
            $this->db->join("ip_quotes", "ip_quotes.client_id = ip_clients.client_id");
            $this->db->where("ip_quotes.quote_id", $quote_id);
            $get_client_data = $this->db->get();
            $client_data = $get_client_data->result_array();


            //GET THE ORDER STATUS CHANGED
            $this->db->select("*");
            $this->db->join("ip_stores", "ip_stores.id = ip_quotes.store");
            $this->db->where("ip_quotes.quote_id", $quote_id);
            $status_changed = $this->db->get("ip_quotes")->result_array();

            if ($status_changed[0]['store_name'] == "Hawthorne") {
                $buytype = 7;
            } else if ($status_changed[0]['store_name'] == "Venice") {
                $buytype = 8;
            } else if ($status_changed[0]['store_name'] == "Usc Repair") {
                $buytype = 6;
            } else {
                $buytype = 1;
            }
            $quote_number = $client_data[0]['quote_number'];
            $to = $client_data[0]['client_email'];
            $subject = "The order $quote_number has been created @ " . date("m-d-Y H:i:s", strtotime($date));
            $message = "The order <b>$quote_number</b> has been created to <b>" . $client_data[0]['client_name'] . "</b><br>"
                    . "<br>" . $this->input->post('notes_to_customer') . "<br><b>Total: </b>" . $client_data[0]['amount'] .
                    "<br><b>Brand: </b>" . $client_data[0]['brand'] .
                    "<br><b>Model: </b>" . $client_data[0]['model'] .
                    "<br><b>Serial Number: </b>" . $client_data[0]['serial_number'] .
                    "<br><b>Data Recovery: </b>" . $client_data[0]['data_recovery'] .
                    "<br><b>Accessories Included: </b>" . $client_data[0]['accessories_included'] .
                    "<br><b>Problem Description/Product: </b>" . $client_data[0]['problem_description_product'] .
                    "<br>Thank you.";
            $headers = "Content-Type:text/html; charset=UTF-8\n";
            $headers .= "From:  365laptoprepair.com<support@365laptoprepair.com>\n";
            $headers .= "X-Sender:  <support@365laptoprepair.com>\n"; //email do servidor //que enviou
            $headers .= "X-Mailer: PHP  v" . phpversion() . "\n";
            $headers .= "X-IP:  " . $_SERVER['REMOTE_ADDR'] . "\n";
            $headers .= "Return-Path:  <support@365laptoprepair.com>\n"; //caso a msg //seja respondida vai para  este email.
            $headers .= "MIME-Version: 1.0\n";



            mail($to, $subject, $message, $headers);




            //SEND THE TICKET INFORMATION TO 365LAPTOPREPAIR
            //VERIFY IF THE TICKET EXISTS IN 365LAPTOPREPAIR
            $this->db->select("*");
            $this->db->where("id_ticket", $quote_id);
            $data_history_ticket = $this->db->get("ip_quotes_status_history")->result_array();

            foreach ($data_history_ticket as $data_history_ticket) {
                $data_history_ticket[0]['staff_comments'] += $data_history_ticket['staff_comments'];
            }

            $this->db->select("*");
            $this->db->where("oid", $quote_number);
            $order_rows = $this->db->get("orders")->num_rows();
            if ($data_history_ticket[0]['staff_comments'] == 0) {
                $comment = "";
            } else {
                $comment = $data_history_ticket[0]['staff_comments'];
            }
            $array_365 = array(
                array(
                    "status" => 1,
                    "comment" => $comment,
                    "notified" => 1,
                    "time" => date('Y-m-d H:i:s')
                )
            );
            $order_data = array(
                'oid' => $status_changed[0]['quote_number'],
                'buytype' => $buytype,
                'fname' => $client_data[0]['client_name'],
                'address' => $client_data[0]['client_address1'],
                'city' => $client_data[0]['client_city'],
                'state' => $client_data[0]['client_state'],
                'postcode' => $client_data[0]['client_zip'],
                'country' => $client_data[0]['client_country'],
                'tel' => $client_data[0]['client_phone'],
                'email' => $client_data[0]['client_email'],
                'status' => serialize($array_365),
                'comments' => $data_history_ticket[0]['staff_comments'],
                'endprice' => $client_data[0]['amount'],
                'time' => date('Y-m-d H:i:s')
            );

            if ($order_rows == 0) {
                $this->db->insert("orders", $order_data);
            }
            /* else {
              $this->db->where("oid", $quote_id);
              $this->db->update("orders", $order_data);
              } */
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }
        echo json_encode($response);
    }

    public function modal_change_client() {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'client_name' => $this->input->post('client_name'),
            'quote_id' => $this->input->post('quote_id')
        );

        $this->layout->load_view('quotes/modal_change_client', $data);
    }

    public function change_client() {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_name = $this->input->post('client_name');
        $client = $this->mdl_clients->where('client_name', $this->db->escape_str($client_name))
                        ->get()->row();

        if (!empty($client)) {
            $client_id = $client->client_id;
            $quote_id = $this->input->post('quote_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quotes', $db_array);

            $response = array(
                'success' => 1,
                'quote_id' => $quote_id
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
        $this->load->model('quotes/mdl_quote_items');

        $item = $this->mdl_quote_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    public function modal_create_quote() {

        $this->load->module('layout');

        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->helper('country');






        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'client_name' => $this->input->post('client_name'),
            "countries" => get_country_list(lang('cldr'))
                //'clients' => $this->mdl_clients->get()->result()
        );


        $this->layout->load_view('quotes/modal_create_quote', $data);
    }

    public function modal_copy_quote() {
        $this->load->module('layout');

        $this->load->model('quotes/mdl_quotes');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'quote_id' => $this->input->post('quote_id'),
            'quote' => $this->mdl_quotes->where('ip_quotes.quote_id', $this->input->post('quote_id'))->get()->row()
        );

        $this->layout->load_view('quotes/modal_copy_quote', $data);
    }

    public function copy_quote() {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');

        if ($this->mdl_quotes->run_validation()) {
            $target_id = $this->mdl_quotes->save();
            $source_id = $this->input->post('quote_id');

            $this->mdl_quotes->copy_quote($source_id, $target_id);

            $response = array(
                'success' => 1,
                'quote_id' => $target_id
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

    public function modal_quote_to_invoice($quote_id) {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('quotes/mdl_quotes');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'quote_id' => $quote_id,
            'quote' => $this->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row()
        );

        $this->load->view('quotes/modal_quote_to_invoice', $data);
    }

    public function quote_to_invoice() {
        $this->load->model(
                array(
                    'invoices/mdl_invoices',
                    'invoices/mdl_items',
                    'quotes/mdl_quotes',
                    'quotes/mdl_quote_items',
                    'invoices/mdl_invoice_tax_rates',
                    'quotes/mdl_quote_tax_rates'
                )
        );
        $this->db->select("*");
        $this->db->where("quote_id", $this->input->post('quote_id'));
        $quote_number = $this->db->get("ip_quotes")->result_array();

        if ($this->mdl_invoices->run_validation()) {
            // Get the quote
            $quote = $this->mdl_quotes->get_by_id($this->input->post('quote_id'));

            $invoice_id = $this->mdl_invoices->create(null, false);

            // Update the discounts
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_discount_amount', $quote->quote_discount_amount);
            $this->db->set('invoice_discount_percent', $quote->quote_discount_percent);
            $this->db->update('ip_invoices');

            // Save the invoice id to the quote
            $this->db->where('quote_id', $this->input->post('quote_id'));
            $this->db->set('invoice_id', $invoice_id);
            $this->db->update('ip_quotes');


            $update_ticket_id = array(
                'ticket_id' => $quote_number[0]['quote_number']
            );

            $this->db->where("invoice_id", $invoice_id);
            $this->db->update("ip_invoices", $update_ticket_id);

            //INSERT INVOICE VALOR FROM TICKET(QUOTE)
            $array_invoice_values = array(
                "invoice_item_subtotal" => $quote_number[0]['amount'],
                "invoice_total" => $quote_number[0]['amount'],
                "invoice_balance" => $quote_number[0]['amount']
            );
            $this->db->where("invoice_id", $invoice_id);
            $this->db->update("ip_invoice_amounts", $array_invoice_values);

            $quote_items = $this->mdl_quote_items->where('quote_id', $this->input->post('quote_id'))->get()->result();
           

            foreach ($quote_items as $quote_item) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'item_tax_rate_id' => $quote_item->item_tax_rate_id,
                    'item_product_id' => $quote_item->item_product_id,
                    'item_name' => $quote_item->item_name,
                    'item_description' => $quote_item->item_description,
                    'item_quantity' => $quote_item->item_quantity,
                    'item_price' => $quote_item->item_price,
                    'item_discount_amount' => $quote_item->item_discount_amount,
                    'item_order' => $quote_item->item_order
                );

                $this->mdl_items->save(null, $db_array);
            }
        
            

            $invoice_history = array(
                "id_invoice" => $invoice_id,
                "id_status" => 1,
                "date_changed" => date("Y-m-d h:i:s"),
                "staff_comments" => "Created from order automatically.",
                "user_id" => $this->session->userdata("user_id")
            );
            $this->db->insert("ip_invoices_status_history", $invoice_history);
            $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $this->input->post('quote_id'))->get()->result();

            foreach ($quote_tax_rates as $quote_tax_rate) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => $quote_tax_rate->tax_rate_id,
                    'include_item_tax' => $quote_tax_rate->include_item_tax,
                    'invoice_tax_rate_amount' => $quote_tax_rate->quote_tax_rate_amount
                );

                $this->mdl_invoice_tax_rates->save(null, $db_array);
            }

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );


            $this->db->select("*");
            $this->db->where("ticket_id", $quote_number[0]['quote_number']);
            $data_invoice = $this->db->get("ip_invoices")->result_array();

            $count = 0;
            foreach ($data_invoice as $data_invoice) {
                $count++;
            }


            $data_update_invoice = array(
                "invoice_number" => $quote_number[0]['quote_number'] . "/" . $count,
                "store" => $quote_number[0]['store']
            );

            $this->db->where("invoice_id", $invoice_id);
            $this->db->update("ip_invoices", $data_update_invoice);
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
        $client_data = $this->db->get("ip_invoices")->result_object();

        //INSERT OR UPDATE INVOICE MESSAGES TO CUSTOMER DASHBOARD

        $this->db->where("invoice_id", $invoice_id);
        $message_qttdata = $this->db->get("ip_customeralerts")->num_rows();

        if ($message_qttdata == 0) {
            $message_array = array(
                "client_id" => $client_data[0]->client_id,
                "message_qtt" => 1,
                "invoice_id" => $invoice_id
            );
            $this->db->insert("ip_customeralerts", $message_array);
        } else {
            $this->db->set("message_qtt", 1);
            $this->db->set("invoice_id", $invoice_id);
            $this->db->where("invoice_id", $invoice_id);
            $this->db->update("ip_customeralerts");
        }

        echo json_encode($response);
    }

}
