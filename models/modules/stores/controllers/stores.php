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

class Stores extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
        $this->load->library('session');
    }

    public function index() {

        $this->layout->buffer('content', 'stores/index');
        $this->layout->render();
    }

    public function process_store_info() {


        $radio_info = str_replace("store_default[]=", "", $this->input->post('store_default'));

        $part_bcn = str_replace("store_tax_active[]=", "", $this->input->post('store_tax_active'));
        $validate_default = array(
            'default' => $radio_info
        );


        $validate_store = array(
            'store_name' => $this->input->post('store_name'),
            'store_address' => $this->input->post('store_address')
        );
        $insert_store = array(
            'store_name' => $this->input->post('store_name'),
            'store_address' => $this->input->post('store_address'),
            'store_phone' => $this->input->post('store_phone'),
            'store_tax' => $part_bcn,
            'tax_percent' => $this->input->post('store_tax_percent'),
            'default' => $radio_info
        );



        $this->db->select("*");
        $this->db->where($validate_store);

        $rows = $this->db->get("ip_stores")->num_rows();

        if ($rows == 0) {
            if ($radio_info == 1) {
                $this->db->select("default");
                $this->db->where($validate_default);
                $rows_default = $this->db->get("ip_stores")->num_rows();
                if ($rows_default != 0) {
                    echo -11;
                    return 0;
                }
            }
            $this->db->insert('ip_stores', $insert_store);
        } else {
            echo -13;
        }
    }

    public function edit_store() {
        $id_store['id_store'] = $this->input->post('id_store');
        $this->load->view('edit_store', $id_store);
    }

    public function validate_edit_store() {

        $store_default_edit = str_replace("store_default_edit[]=", "", $this->input->post('store_default_edit'));

        $part_bcn_edit = str_replace("store_tax_active_edit[]=", "", $this->input->post('store_tax_active'));
        $validate_info = array(
            'store_name' => $this->input->post('store_name'),
            'store_address' => $this->input->post('store_address')
        );
        $store_info = array(
            'store_name' => $this->input->post('store_name'),
            'store_address' => $this->input->post('store_address'),
            'store_phone' => $this->input->post('store_phone'),
            'store_tax' => $part_bcn_edit,
            'tax_percent' => $this->input->post('store_tax_percent'),
            'default' => $store_default_edit
        );
        print_r($store_info);
        if ($store_default_edit == 1) {

            $x = array(
                'default' => '0'
            );
            $this->db->where('default', 1);
            $this->db->update('ip_stores', $x);
        }


        $this->db->select("*");
        $this->db->where($validate_info);
        $rows = $this->db->get("ip_stores")->num_rows();

        $this->db->where('id', $this->input->post('store_id'));
        $this->db->update('ip_stores', $store_info);
    }

    public function delete_store() {
        $id_store['id_store'] = $this->input->post('id_store');
        $this->load->view('delete_store', $id_store);
    }

    public function validate_delete_store() {
        $decision = $this->input->post('decision');
        $receive_id_store = $this->input->post('receive_id_store');


        if ($decision == 1) {

            $this->db->where('id', $receive_id_store);
            $this->db->delete('ip_stores');
        }
    }
    public function update_store_user()
    {
        $user_store = array (
            "user_store" => $this->input->post("store_id")
            
        );
        $this->db->where("user_id", $this->session->userdata('user_id'));
        $this->db->update("ip_users",$user_store);
        
    }

}
