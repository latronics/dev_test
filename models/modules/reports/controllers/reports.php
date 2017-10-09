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

class Reports extends Admin_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_reports');
    }

    public function sales_by_client() {

        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->sales_by_client($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post('client_name')),
            );

            $this->load->view('reports/sales_by_client', $data);

            //$this->load->helper('mpdf');
            //pdf_create($html, lang('sales_by_client'), true);
        } else {
            $this->layout->buffer('content', 'reports/sales_by_client_index')->render();
        }
    }

    public function payment_history() {

        if ($this->input->post('btn_submit')) {
            $this->db->where("id", $this->input->post("store"));
            $store = $this->db->get("ip_stores")->result_array();
            $store = $store[0]['store_name'];
            $this->db->where("product_id", $this->input->post("products"));
            $product = $this->db->get("ip_products")->result_array();
            $product = $product[0]['product_name'];
            $from_date = date_to_mysql($this->input->post('from_date'));
            $to_date = date_to_mysql($this->input->post('to_date'));
            $stores_data = $this->db->get("ip_stores")->result_object();


            $products = $this->input->post("products");

            if ($this->input->post("store") == 1) {
                foreach ($stores_data as $stores_data) {
                    if (($products != null) && ($products != "all")) {
                        $this->db->join("ip_invoices", "ip_invoices.invoice_id = ip_invoice_items.invoice_id");
                        $this->db->join("ip_invoice_items", "ip_invoice_items.invoice_id = ip_invoices.invoice_id");
                        $this->db->where("ip_invoice_items.item_product_id", $products);
                    }
                    $this->db->join("ip_stores", "ip_stores.id = ip_payments.store");
                    $this->db->join("ip_payment_methods", "ip_payment_methods.payment_method_id = ip_payments.payment_method_id");
                    $this->db->where('ip_payments.payment_date >=', $from_date);
                    $this->db->where('ip_payments.payment_date <=', $to_date);
                    $this->db->where('ip_payments.store', $stores_data->id);
                    if (($stores_data->store_name != "Website Sale") && ($stores_data->store_name != "Website Repair")) {
                        $this->db->where("ip_payment_methods.payment_method_name", "Cash");
                    }


                    $total_stores = $this->db->get("ip_payments")->result_object();
                    foreach ($total_stores as $total_stores) {
                        $sum[$stores_data->id] += $total_stores->payment_amount;
                    }
                }
            }



            $data = array(
                'results' => json_encode($this->mdl_reports->payment_history($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post("store"), $this->input->post("products"))),
                'result_toprint' => $this->mdl_reports->payment_history($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post("store"), $this->input->post("products")),
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'store' => $store,
                'product' => $product,
                'store_total' => $sum
            );
            $this->load->view('reports/payment_history', $data);

            //$this->load->helper('mpdf');
            //pdf_create($html, lang('payment_history'), true);
        } else {
            $store_data['store_data'] = $this->db->get("ip_stores")->result_array();
            $store_data['products_data'] = $this->db->get("ip_products")->result_array();
            $this->layout->buffer('content', 'reports/payment_history_index', $store_data)->render();
        }
    }

    public function invoice_aging() {
        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->invoice_aging()
            );

            $html = $this->load->view('reports/invoice_aging', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, lang('invoice_aging'), true);
        }

        $this->layout->buffer('content', 'reports/invoice_aging_index')->render();
    }

    public function sales_by_year() {

        if ($this->input->post('btn_submit')) {
            $data = array(
                'results' => $this->mdl_reports->sales_by_year($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post('minQuantity'), $this->input->post('maxQuantity'), $this->input->post('checkboxTax'))
            );

            $html = $this->load->view('reports/sales_by_year', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, lang('sales_by_date'), true);
        }

        $this->layout->buffer('content', 'reports/sales_by_year_index')->render();
    }

}
