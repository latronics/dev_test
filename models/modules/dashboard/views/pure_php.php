
<?php
$this->db->select("*");
$data_session_start = $this->db->get("365admin_valid_session")->result_array();
$this->db->where("session_start", $data_session_start[0]['session_start']);
$this->db->delete("365admin_valid_session");
//GET STORE
$this->db->select("user_store");
$this->db->where("user_id", $this->session->userdata('user_id'));
$users_data = $this->db->get("ip_users")->result_array();
$store_id = $users_data[0]['user_store'];

$this->db->where("session_start", $data_session_start[0]['session_start']);
$this->db->delete("365admin_valid_session");

//CHECK IF THE PAYMENT FROM ONLINE ORDERS IS READY
$this->db->select("*");
$this->db->join("ip_stores", "ip_stores.id = ip_quotes.store");
$this->db->join("orders", "orders.oid = ip_quotes.quote_number");
$this->db->where("ip_quotes.active <>", 1);
$this->db->where("ip_quotes.complete <>", 1);
$this->db->where("ip_quotes.payment_status <>", 0);
$this->db->where("orders.oid_ref", 0);
$this->db->where("ip_quotes.fraud", 0);
$ip_quotes = $this->db->get("ip_quotes")->result_array();

$this->db->select("oid, fraud");
$this->db->where("fraud", 1);
$fraud = $this->db->get("orders")->result_array();
foreach ($fraud as $fraud) {
    $this->db->where("quote_number", $fraud['oid']);
    $this->db->set("fraud", 1);
    $this->db->update("ip_quotes");
}

foreach ($ip_quotes as $ip_quotes) {


    $this->db->select("*");
    $this->db->where("oid", $ip_quotes['quote_number']);
    $orders = $this->db->get("orders")->result_array();
    if ($orders[0]['complete'] == 1) {
        //UPDATE COMPLETE ORDERS IN 365ADMIN
        $this->db->where("quote_number", $ip_quotes['quote_number']);
        $this->db->set("complete", 1);
        $this->db->set("complete_log", $ip_quotes['sysdata']);
        $this->db->update("ip_quotes");

        //INSERT INVOICES FROM COMPLETE ORDER
        $invoice_array = array(
            "user_id" => $this->session->userdata("user_id"),
            "client_id" => $ip_quotes['client_id'],
            "invoice_status_id" => 4,
            "is_read_only" => 1,
            "invoice_date_created" => date("Y-m-d"),
            "invoice_time_created" => date("H:i:s"),
            "invoice_number" => $ip_quotes['quote_number'] . "/1",
            "ticket_id" => $ip_quotes['quote_number'],
            "store" => $store_id
        );
        $this->db->insert("ip_invoices", $invoice_array);



        //INSERT IP_AMOUNTS FROM COMPLETE ORDER
        $this->db->select("invoice_id");
        $this->db->where($invoice_array);
        $invoice_data = $this->db->get("ip_invoices")->result_array();
        $invoice_id = $invoice_data[0]['invoice_id'];

        $ip_amounts_array = array(
            "invoice_id" => $invoice_id,
            "invoice_item_subtotal" => $orders[0]['endprice'],
            "invoice_total" => $orders[0]['endprice'] + $orders[0]['endprice_delivery'],
            "invoice_paid" => $orders[0]['endprice'] + $orders[0]['endprice_delivery'],
            "invoice_balance" => 0
        );
        $this->db->insert("ip_invoice_amounts", $ip_amounts_array);

        //INSERT INVOICES FROM COMPLETE ORDER
        $invoice_history_array = array(
            "id_invoice" => $invoice_id,
            "id_status" => 4,
            "date_changed" => date("Y-m-d H:i:s"),
            "user_id" => $this->session->userdata("user_id")
        );
        $this->db->insert("ip_invoices_status_history", $invoice_history_array);

        //INSERT IP_INVOICE_ITEMS
        //INSERT INVOICE_ITEMS
        $this->db->select("*");
        $this->db->where("invoice_id", $invoice_id);
        $rows_invoice_items = $this->db->get("ip_invoice_items")->num_rows();
        @$item_name = unserialize($orders[0]['order']);
        if ($item_name != "") {



            foreach ($item_name as $item_name) {
                if (is_array(@$item_name['attributes'])) {

                    foreach ($item_name['attributes'] as $attributes) {



                        $array_invoice_items = array(
                            "invoice_id" => $invoice_id,
                            "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                            "item_name" => $item_name['p_title'],
                            "item_description" => $attributes['a_title'],
                            "item_price" => $attributes['a_price']
                        );
                        $delivery = array(
                            "invoice_id" => $invoice_id,
                            "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                            "item_name" => "Shipping",
                            "item_description" => "Shipping",
                            "item_price" => $orders[0]['endprice_delivery']
                        );
                        if ($rows_invoice_items == 0) {


                            $this->db->insert("ip_invoice_items", $array_invoice_items);
                        }
                    }
                } else {
                    $array_invoice_items = array(
                        "invoice_id" => $invoice_id,
                        "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                        "item_name" => $item_name['p_title'],
                        "item_description" => $item_name['p_title'],
                        "item_price" => $item_name['p_price']
                    );
                    $delivery = array(
                        "invoice_id" => $invoice_id,
                        "item_date_added" => date("Y-m-d", strtotime($orders[0]['time'])),
                        "item_name" => "Shipping",
                        "item_description" => "Shipping",
                        "item_price" => $orders[0]['endprice_delivery']
                    );
                    if ($rows_invoice_items == 0) {


                        $this->db->insert("ip_invoice_items", $array_invoice_items);
                    }
                }
            }
            if ($orders[0]['endprice_delivery'] != 0) {

                $this->db->insert("ip_invoice_items", $delivery);
            }
        }
        //INSERT IP_PAYMENTS FROM COMPLETE ORDER
        if ($orders[0]['payproc'] == 1) {
            $payproc = "Paypal";
        } else {
            $payproc = "Authorize net";
        }
        $this->db->select("ip_payment_methods.payment_method_id");
        $this->db->join("ip_payment_methods", "ip_payment_methods.payment_method_id = ip_payments.payment_method_id");
        $this->db->where("ip_payment_methods.payment_method_name", $payproc);
        $ip_payments_data = $this->db->get("ip_payments")->result_array();
        $ip_payments_array = array(
            "invoice_id" => $invoice_id,
            "payment_method_id" => $ip_payments_data[0]['payment_method_id'],
            "payment_date" => date("Y-m-d"),
            "payment_time" => date("H:i:s"),
            "payment_amount" => $orders[0]['endprice'] + $orders[0]['endprice_delivery'],
            "store" => $store_id
        );
        $this->db->insert("ip_payments", $ip_payments_array);
    }
}
//VARIABLE DECLARATIONS
$count = 0;
$x = 0;
$y = 0;

$array_aux = array(
    '0' => 'Diagnosing',
    '1' => 'Waiting on approval',
    '2' => 'Ordered Parts',
    '3' => 'Repairing',
    '4' => 'Repair Completed',
    '5' => 'Accepted by client',
    '6' => 'New Order',
    '7' => 'Waiting for package',
    '8' => 'Repair denied',
    '9' => 'Returned to shop'
);

//GET USER STORE
$this->db->select("*");
$this->db->where("user_id", $this->session->userdata('user_id'));
$user_store = $this->db->get("ip_users")->result_array();


//CLEANING 365_X_INVOICEPLANE TRASH
$this->db->select("*");
$clean_trash = $this->db->get("365admin_valid_session")->result_array();

if ($clean_trash[0]['session_start'] != "") {
    $this->db->where("session_start", $clean_trash[0]['session_start']);
    $this->db->delete("365admin_valid_session");
}

//GET NUMBER OF STATUS
$this->db->select("*");
$rows_status = $this->db->get("status")->num_rows();


//GET THE YEARS QUANTITY TO SHOW THE TICKETS
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'years_ticket'");
$get_years_ticket = $this->db->get();
$years_ticket = $get_years_ticket->result_array();

//GET THE DAYS QUANTITY TO SHOW THE TICKETS
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'days_ticket'");
$get_days_ticket = $this->db->get();
$days_ticket = $get_days_ticket->result_array();

//GET THE DAYS QUANTITY TO TURN TICKETS URGENT(GENERAL)
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'days_urgent'");
$get_days_urgent = $this->db->get();
$days_urgent = $get_days_urgent->result_array();


//CREATE THE URGENT ARRAY TO UPDATE THE TABLE IP_QUOTES SETTING
$urgent = array(
    'urgent' => 2
);
//CREATE THE RETURN URGENT ARRAY TO UPDATE THE TABLE IP_QUOTES SETTING
$return_urgent = array(
    'urgent' => 0
);
$middle_urgent = array(
    'urgent' => 1
);

//CHECK WHAT STATUS PARAMETERS ARE SETTED ON SETTINGS AND ADJUST THE DATA
$this->db->select("*");
$this->db->where("setting_key", "show_status_ticket");
$status_ipsettings = $this->db->get("ip_settings")->result_array();

$get_status = str_replace("show_status_ticket[]", "", $status_ipsettings[0]['setting_value']);
$get_status2 = str_replace("=", "", $get_status);
$get_status3 = str_replace("+", " ", $get_status2);
$status_array = explode("&", $get_status3);
$status_array2 = explode("&", $get_status3);











//GET THE TICKETS INFORMATION USING THE SETTINGS AUTOMATIC   

$date_full = date('Y-m-d');
$date_month = $days_ticket[0]['setting_value'];
$date_year -= $years_ticket[0]['setting_value'];
$date_month_final = date('Y-m-d', strtotime("-$date_month days"));



$this->db->select("*");
$this->db->from("ip_quotes");
$this->db->order_by("urgent", "ASC");
$this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
$this->db->join("status", "status.id = ip_quotes.quote_status_id");


if ($user_store[0]['user_store'] != 1) {

    foreach ($status_array as $status_array) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("status.status", $status_array);
        $this->db->where("ip_quotes.store", $user_store[0]['user_store']);
        $this->db->where("attention", 0);
    }
} else
if ($user_store[0]['user_store'] == 1) {
    foreach ($status_array as $status_array) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("status.status", $status_array);
        $this->db->where("attention", 0);
    }
}

$get_data_ticket = $this->db->get();
$data_ticket = $get_data_ticket->result_array();
$ticket_rows = $get_data_ticket->num_rows();

//GET UNCOMPLETE ROWS
$this->db->select("*");
$this->db->join("orders", "orders.oid = ip_quotes.quote_number");
$this->db->where("ip_quotes.complete <>", 1);
$this->db->where("ip_quotes.payment_status <>", 0);
$this->db->where("ip_quotes.active", 0);
$this->db->where("orders.oid_ref", 0);
$this->db->where("ip_quotes.fraud", 0);
$ip_quotes_rows = $this->db->get("ip_quotes")->num_rows();
//FRAUD ROWS
$this->db->select("*");
$this->db->join("orders", "orders.oid = ip_quotes.quote_number");
$this->db->where("ip_quotes.complete <>", 1);
$this->db->where("ip_quotes.payment_status <>", 0);
$this->db->where("ip_quotes.active", 0);
$this->db->where("orders.oid_ref", 0);
$this->db->where("ip_quotes.fraud", 1);
$ip_fraud_rows = $this->db->get("ip_quotes")->num_rows();


//GET PRIORITY TICKETS QUANTITY
$this->db->select("*");
$this->db->order_by("urgent", "ASC");
$this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
$this->db->join("status", "status.id = ip_quotes.quote_status_id");
if ($user_store[0]['user_store'] != 1) {

    foreach ($status_array2 as $status_array2) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("ip_quotes.store", $user_store[0]['user_store']);
        $this->db->where("status.status", $status_array2);
        $this->db->where("ip_quotes.urgent = 2");
    }
} else if ($user_store[0]['user_store'] == 1) {
    foreach ($status_array2 as $status_array2) {
        $this->db->or_where("ip_quotes.quote_date_created > '$date_month_final'");
        $this->db->where("status.status", $status_array2);
        $this->db->where("ip_quotes.urgent = 2");
    }
}




$urgent_row = $this->db->get("ip_quotes")->num_rows();


//SET THE CARACTERE LIMIT TO DO NOT BROKE TICKETS TABLE STRUCTURE
$caractere_limit = 16;
?>