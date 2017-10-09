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

class Products extends Admin_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_products');
    }

    public function index($page = 0) {


        $this->mdl_products->paginate(site_url('products/index'), $page);
        $products = $this->mdl_products->result();
        $categories = $this->db->get("ip_categories")->result_object();
        $products_tree = $this->db->get("ip_products")->result_object();
        foreach ($categories as $categories_totree) {

            $data_final[] = array(
                "id" => $categories_totree->category_id,
                "value" => $categories_totree->category_name,
                "parent_id" => $categories_totree->category_parent
            );
        }
        foreach ($products_tree as $products_tree) {
            $data_final[] = array(
                "id" => $products_tree->product_id,
                "value" => $products_tree->product_name,
                "parent_id" => $products_tree->pcategory_id
            );
        }
        $data = json_encode($data_final);
        $data = str_replace("][", ",", $data_final);

        //GET DATATREE PRODUCTS LOG
        $this->db->join("ip_users","ip_users.user_id = ip_track_pdatatree.user_id");
        $this->db->order_by("id", "DESC");
        $ip_track_pdatatree = $this->db->get("ip_track_pdatatree")->result_object();
       
        
        $this->layout->set("categories", json_encode($data));
        $this->layout->set("categories_list", $categories);
        $this->layout->set('products', $products);
        $this->layout->set("log_data", $ip_track_pdatatree);
        $this->layout->buffer('content', 'products/index');
        $this->layout->render();
    }

    public function categories() {
        $category_id = $this->input->post("cat_id");
        $category_name = $this->input->post("category_name");
        $category_parent = $this->input->post("category_select");
        $category_tag = $this->input->post("category_tag");
        if ($category_id == 'null') {
            $cat_array = array(
                "category_name" => $category_name,
                "category_parent" => $category_parent,
                "category_tag" => $category_tag
            );
            $this->db->insert("ip_categories", $cat_array);
        } else {
            $this->db->set("category_name", $category_name);
            $this->db->set("category_parent", $category_parent);
            $this->db->set("category_tag", $category_tag);
            $this->db->where("category_id", $category_id);
            $this->db->update("ip_categories");
        }
    }

    public function products_update() {
        
        $product_id = $this->input->post("prod_id");
        $product_name = $this->input->post("product_name");
        $product_parent = $this->input->post("category_prod_select");
        $product_price = $this->input->post("product_price");
        $product_cost = $this->input->post("product_cost");
        
        $this->db->set("product_name", $product_name);
        $this->db->set("pcategory_id", $product_parent);
        $this->db->set("product_price", $product_price);
        $this->db->set("purchase_price", $product_cost);
        $this->db->where("product_id", $product_id);
        $this->db->update("ip_products");
    }

    public function products_show() {

        $this->warehouse = $this->load->database('warehouse', TRUE);


        $this->warehouse->select("*");
        $this->warehouse->where("deleted", 0);
        $this->warehouse->where("nr", 0);
        $this->warehouse->limit(100);
//$this->warehouse->from("warehouse");

        $get_warehouse_data = $this->warehouse->get("warehouse");

        if ($get_warehouse_data->num_rows() > 0) {
            $data['warehouse_data'] = $warehouse_data = $get_warehouse_data->result_array();
        }

        $this->load->view("products_show", $data);
    }

    public function services_show() {

        $this->load->view("services_show");
    }
    public function show_create_prodform()
    {
        $this->load->view('products/form');
    }
    public function form($id = null) {
        if ($this->input->post('btn_cancel')) {
            redirect('products');
        }

        if ($this->mdl_products->run_validation()) {
// Get the db array
            $db_array = $this->mdl_products->db_array();

            $this->mdl_products->save($id, $db_array);

            redirect('products');
        }

        if ($id and ! $this->input->post('btn_submit')) {
            if (!$this->mdl_products->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('families/mdl_families');
        $this->load->model('tax_rates/mdl_tax_rates');

        $this->layout->set(
                array(
                    'families' => $this->mdl_families->get()->result(),
                    'tax_rates' => $this->mdl_tax_rates->get()->result(),
                )
        );

        $this->layout->buffer('content', 'products/form');
        $this->layout->render();
    }

    public function delete($id) {
        $this->mdl_products->delete($id);
        redirect('products');
    }

}
