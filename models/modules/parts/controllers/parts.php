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

class Parts extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->warehouse = $this->load->database('warehouse', TRUE);
        $this->load->library('session');

        setlocale(LC_MONETARY, "en_US");
        date_default_timezone_set('America/Los_Angeles');
    }

    public function query($offset) {


        $part_where = $this->input->post('input_text');






        $this->warehouse->select("SQL_CALC_FOUND_ROWS *", FALSE);
        $this->warehouse->or_like('title', $part_where);
        $this->warehouse->or_like('psku', $part_where);
        $this->warehouse->or_like('bcn', $part_where);
        $this->warehouse->where("deleted", 0);
        $this->warehouse->where("nr", 0);



//$this->warehouse->from("warehouse");

        $get_warehouse_data = $this->warehouse->get("warehouse", 3000, $offset);

        if ($get_warehouse_data->num_rows() > 0) {
            $data['warehouse_data'] = $warehouse_data = $get_warehouse_data->result_array();
        }


        $this->load->library('Pagination');
        $config['base_url'] = site_url('parts/query');
        $config['total_rows'] = $get_warehouse_data->num_rows();
        $config['per_page'] = 10;


        $this->pagination->initialize($config);


        $this->load->view('parts/show_parts', $data);
    }

    public function index($offset) {





        $this->warehouse->select("SQL_CALC_FOUND_ROWS *", FALSE);

        $this->warehouse->like('title');
        $this->warehouse->where("deleted", 0);
        $this->warehouse->where("nr", 0);




//$this->warehouse->from("warehouse");

        $get_warehouse_data = $this->warehouse->get("warehouse", 3000, $offset);

        if ($get_warehouse_data->num_rows() > 0) {
            $data['warehouse_data'] = $warehouse_data = $get_warehouse_data->result_array();
        }


        $this->load->library('Pagination');
        $config['base_url'] = site_url('parts/index');
        $config['total_rows'] = $get_warehouse_data->num_rows();
        $config['per_page'] = 10;


        $this->pagination->initialize($config);
        $this->layout->buffer('content', 'parts/index', $data);
        $this->layout->render();
    }

    public function process_parts_selections() {



        $input_value = $this->input->post('input_text');

        $this->warehouse->select("SQL_CALC_FOUND_ROWS *", FALSE);
        $this->warehouse->or_like('title', $input_value);
        $this->warehouse->or_like('bcn', $input_value);
        $this->warehouse->where('sold_id', 0);
        $this->warehouse->where("deleted", 0);
        $this->warehouse->where("nr", 0);
        //echo json_encode($input_value);




        $get_warehouse_data = $this->warehouse->get("warehouse", 30);

        if ($get_warehouse_data->num_rows() > 0) {
            $data['warehouse_data'] = $warehouse_data = $get_warehouse_data->result_array();
        }

        $this->load->view("products/show_parts", $data);
    }

    public function insert_part_on_quote() {

        $ticket = strpos($this->input->post("invoice_ticket"), 'quotes/view');
        $invoice = strpos($this->input->post("invoice_ticket"), 'invoices/view');
        if ($ticket != "") {
            $ticket = 1;
            $invoice = 0;
        }
        if ($invoice != "") {
            $invoice = 1;
            $ticket = 0;
        }
        $part_bcn_array = $this->input->post('id_part');
        $user_id = $this->session->userdata('user_id');
        //GET TICKET_ID
        $this->db->select("*");
        $this->db->where("id_user", $user_id);
        $this->db->order_by("date", "DESC");
        $this->db->limit("1");
        $ticket_id = $this->db->get("ip_quote_id_aux")->result_array();


        //GET INVOICE_ID
        $this->db->select("*");
        $this->db->where("id_user", $user_id);
        $this->db->order_by("date", "DESC");
        $this->db->limit("1");
        $invoice_id = $this->db->get("ip_invoice_id_aux")->result_array();

        $part_bcn = str_replace("parts_ids[]=", "", $part_bcn_array);
        $part_bcn_replaced = explode("&", $part_bcn);



        foreach ($part_bcn_replaced as $part_bcn_replaced) {

            $this->warehouse->select("*");
            $this->warehouse->where("bcn", $part_bcn_replaced);
            $part_data = $this->warehouse->get("warehouse")->result_array();







            if ($ticket == 1) {

                foreach ($part_data as $part_data) {

                    $part_array_insert = array(
                        "quote_id" => $ticket_id[0]['id_ticket'],
                        "item_product_id" => $part_data['bcn'],
                        "item_date_added" => date('Y-m-d'),
                        "item_name" => $part_data['title'],
                        "item_description" => $part_data['notes'],
                        "item_quantity" => "1",
                        "item_price" => $part_data['cost'],
                        "wid" => $part_data['wid'],
                        "part" => 1
                    );
                }
                $this->db->insert("ip_quote_items", $part_array_insert);
            } else if ($invoice == 1) {

                foreach ($part_data as $part_data) {

                    $part_array_insert = array(
                        "invoice_id" => $invoice_id[0]['id_invoice'],
                        "item_product_id" => $part_data['bcn'],
                        "item_date_added" => date('Y-m-d'),
                        "item_name" => $part_data['title'],
                        "item_description" => $part_data['notes'],
                        "item_quantity" => "1",
                        "item_price" => $part_data['cost'],
                        "wid" => $part_data['wid'],
                        "part" => 1
                    );
                }

                $this->db->insert("ip_invoice_items", $part_array_insert);
            }
        }
    }

    public function erase_part_on_quote() {


        $part_bcn_array2 = $this->input->post('id_part2');
        $user_id2 = $this->session->userdata('user_id');

        echo $part_bcn_array2 . $user_id2;
    }

    public function insert_quote_id_aux() {
        $user_id = $this->session->userdata('user_id');

        $this->db->select("*");
        $this->db->where("id_user", $user_id);
        $this->db->where("id_ticket", $this->input->post("ticket_id"));
        $rows_ip_quote_id_aux = $this->db->get("ip_quote_id_aux")->num_rows();
        $insert_update_array = array(
            "id_ticket" => $this->input->post("ticket_id"),
            "id_user" => $user_id,
            "date" => date('Y-m-d h:i:s')
        );
        if ($rows_ip_quote_id_aux == 0) {

            //echo $this->input->post("ticket_id");
            $this->db->insert("ip_quote_id_aux", $insert_update_array);
        } else {
            $this->db->where("id_user", $user_id);
            $this->db->where("id_ticket", $this->input->post("ticket_id"));
            $this->db->update("ip_quote_id_aux", $insert_update_array);
        }
    }

    public function insert_invoice_id_aux() {

        $user_id = $this->session->userdata('user_id');

        $this->db->select("*");
        $this->db->where("id_user", $user_id);
        $this->db->where("id_invoice", $this->input->post("invoice_id"));
        $rows_ip_invoice_id_aux = $this->db->get("ip_invoice_id_aux")->num_rows();
        $insert_update_array = array(
            "id_invoice" => $this->input->post("invoice_id"),
            "id_user" => $user_id,
            "date" => date('Y-m-d h:i:s')
        );
        if ($rows_ip_invoice_id_aux == 0) {

            //echo $this->input->post("ticket_id");
            $this->db->insert("ip_invoice_id_aux", $insert_update_array);
        } else {
            $this->db->where("id_user", $user_id);
            //$this->db->where("id_invoice", $this->input->post("invoice_id"));
            $this->db->update("ip_invoice_id_aux", $insert_update_array);
        }
    }

}
