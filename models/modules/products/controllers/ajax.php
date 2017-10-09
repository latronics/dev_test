<?php

date_default_timezone_set('America/Los_Angeles');
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

    //public $ajax_controller = true;
    public function modal_product_lookups() {
        //$filter_family  = $this->input->get('filter_family');
        $filter_product = $this->input->get('filter_product');

        $this->load->model('mdl_products');
        $this->load->model('families/mdl_families');


        // Apply filters
        /*
          if((int)$filter_family) {
          $products = $this->mdl_products->by_family($filter_family);
          }
         */

        if (!empty($filter_product)) {
            $products = $this->mdl_products->by_product($filter_product);
        }
        $products = $this->mdl_products->get();
        $products = $this->mdl_products->result();

        $families = $this->mdl_families->get()->result();

        $data = array(
            'products' => $products,
            'families' => $families,
            'filter_product' => $filter_product,
                //'filter_family'  => $filter_family,
        );

        $this->layout->load_view('products/modal_product_lookups', $data);
    }

    public function update_parent() {
        $user_id = $this->session->userdata("user_id");
        $start = $this->input->post("start");
        $parent = $this->input->post("parent");
        foreach ($start as $start) {
            $this->db->where("product_id", $start);
            $product_data = $this->db->get("ip_products")->result_object();
            $product_row = $this->db->get("ip_products")->num_rows();

            if ($product_row == 0) {
                $this->db->set("category_parent", $parent);
                $this->db->where("category_id", $start);
                $this->db->update("ip_categories");

                $this->db - where("category_id", $start);
                $category_data = $this->db->get("ip_categories")->result_object();
                $clog_array = array(
                    "user_id" => $user_id,
                    "last_parent" => $category_data[0]->category_parent,
                    "last_name" => $category_data[0]->category_name,
                    "new_name" => $category_data[0]->category_name,
                    "new_parent" => $parent,
                    "date" => date("Y-m-d"),
                    "time" => date("H:i:s"),
                    "category_id" => $start
                );
                $this->db->insert("ip_track_pdatatree", $clog_array);
            } else {
                $this->db->set("pcategory_id", $parent);
                $this->db->where("product_id", $start);
                $this->db->update("ip_products");
                $plog_array = array(
                    "user_id" => $user_id,
                    "last_parent" => $product_data[0]->pcategory_id,
                    "last_name" => $product_data[0]->product_name,
                    "new_name" => $product_data[0]->product_name,
                    "new_parent" => $parent,
                    "date" => date("Y-m-d"),
                    "time" => date("H:i:s"),
                    "product_id" => $start,
                    "last_price" => $product_data[0]->product_price,
                    "new_price" => $product_data[0]->product_price,
                    "last_cost" => $product_data[0]->purchase_price,
                    "last_cost" => $product_data[0]->purchase_price
                );
                $this->db->insert("ip_track_pdatatree", $plog_array);
            }
        }
    }

    public function back_logdata() {
        $date_from = $this->input->post("date_from");
        $date_to = $this->input->post("date_to");
        $date_from = date('Y-m-d', strtotime(str_replace('-', '/', $date_from)));
        $date_to = date('Y-m-d', strtotime(str_replace('-', '/', $date_to)));
        $this->db->where("date", $date_from);
        $this->db->group_by("category_id");
        $this->db->order_by("id", "ASC");
        $log_result = $this->db->get("ip_track_pdatatree")->result_object();
        if ($log_result != null) {
            foreach ($log_result as $log_result) {
                if ($log_result->category_id != 0) {
                    $this->db->set("category_name", $log_result->last_name);
                    $this->db->set("category_parent", $log_result->last_parent);
                    $this->db->where("category_id", $log_result->category_id);
                    $this->db->update("ip_categories");
                } else if ($log_result->product_id != 0) {
                    $this->db->set("product_name", $log_result->last_name);
                    $this->db->set("pcategory_id", $log_result->last_parent);
                    $this->db->set("product_price", $log_result->last_price);
                    $this->db->set("purchase_price", $log_result->last_cost);
                    $this->db->where("product_id", $log_result->product_id);
                    $this->db->update("ip_products");
                }
            }
        } else {
            echo "false";
        }
    }

    public function search_catprod() {
        $key = $this->input->post("key");
        $this->db->like("category_name", $key, "after");
        $this->db->or_like("category_tag", $key, "after");
        $categories = $this->db->get("ip_categories")->result_object();
        $this->db->like("product_name", $key, "after");
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
        echo json_encode($data);
    }

    public function validate_cat() {
        $cat_prod_id = $this->input->post("cat_prod_id");
        $this->db->where("category_id", $cat_prod_id);
        $category_data = $this->db->get("ip_categories")->result_array();
        if ($category_data == null) {
            $this->db->where("product_id", $cat_prod_id);
            $product_data = $this->db->get("ip_products")->result_array();
            echo json_encode($product_data[0]);
        } else {
            echo json_encode($category_data[0]);
        }
    }

    public function delete_prod() {
        $product_id = $this->input->post("prod_id");
        $this->db->where("product_id", $product_id);
        $this->db->delete("ip_products");
    }

    public function delete_cat() {
        $cat_id = $this->input->post("cat_id");
        $this->db->where("category_id", $cat_id);
        $this->db->delete("ip_categories");
    }

    public function save_log() {
        $user_id = $this->session->userdata("user_id");
        $cat_id = $this->input->post("cat_id");
        $category_name = $this->input->post("category_name");
        $category_select = $this->input->post("category_select");
        $category_tag = $this->input->post("category_tag");


        if ($cat_id == null) {
            $prod_id = $this->input->post("prod_id");

            if ($prod_id != null) {
                $product_name = $this->input->post("product_name");
                $category_prod_select = $this->input->post("category_prod_select");
                $product_price = $this->input->post("product_price");
                $product_cost = $this->input->post("product_cost");

                //GET LAST PRODUCT MOVE
                $this->db->where("product_id", $prod_id);
                $old_productdata = $this->db->get("ip_products")->result_object();

                $last_pmovelog = array(
                    "user_id" => $user_id,
                    "last_parent" => $old_productdata[0]->pcategory_id,
                    "last_name" => $old_productdata[0]->product_name,
                    "new_name" => $product_name,
                    "new_parent" => $category_prod_select,
                    "date" => date("Y-m-d"),
                    "time" => date("H:i:s"),
                    "product_id" => $prod_id,
                    "last_price" => $old_productdata[0]->product_price,
                    "new_price" => $product_price,
                    "last_cost" => $old_productdata[0]->purchase_price,
                    "new_cost" => $product_cost,
                );
                $this->db->insert("ip_track_pdatatree", $last_pmovelog);
            }
        } else {
            //GET LAST CATEGORY MOVE
            $this->db->where("category_id", $cat_id);
            $old_categorydata = $this->db->get("ip_categories")->result_object();

            $last_cmovelog = array(
            "user_id" => $user_id,
            "last_parent" => $old_categorydata[0]->category_parent,
            "last_name" => $old_categorydata[0]->category_name,
            "new_name" => $category_name,
            "new_parent" => $category_select,
            "date" => date("Y-m-d"),
            "time" => date("H:i:s"),
            "category_id" => $cat_id,
            "last_tag" => $old_categorydata[0]->category_tag,
            "new_tag" => $category_tag
            );

            $this->db->insert("ip_track_pdatatree", $last_cmovelog);
        }
    }

    public function new_product() {
        $product_array = array(
            "product_name" => $this->input->post("product_name"),
            "pcategory_id" => $this->input->post("product_category"),
            "product_price" => $this->input->post("product_price"),
            "purchase_price" => $this->input->post('product_cost_new')
        );
        $this->db->insert("ip_products", $product_array);
    }

    public function valid_pass() {


        $user_answer = $this->input->post("user_answer");
        if ($user_answer == "Yes") {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function undo_event() {
        $user_id = $this->session->userdata("user_id");
        $logevent_id = $this->input->post("log_id");
        $this->db->where("id", $logevent_id);
        $log_data = $this->db->get("ip_track_pdatatree")->result_object();
        if ($log_data[0]->category_id != 0) {
            $this->db->where("category_id", $log_data[0]->category_id);
            $this->db->set("category_parent", $log_data[0]->last_parent);
            $this->db->set("category_name", $log_data[0]->last_name);
            $this->db->update("ip_categories");

            $log_array = array(
                "user_id" => $user_id,
                "last_parent" => $log_data[0]->new_parent,
                "last_name" => $log_data[0]->new_name,
                "new_name" => $log_data[0]->last_name,
                "new_parent" => $log_data[0]->last_parent,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
                "category_id" => $log_data[0]->category_id
            );

            //INSERT LOG AS CATEGORY
            $this->db->insert("ip_track_pdatatree", $log_array);
        } else if ($log_data[0]->product_id != 0) {
            $this->db->where("product_id", $log_data[0]->product_id);
            $this->db->set("pcategory_id", $log_data[0]->last_parent);
            $this->db->set("product_name", $log_data[0]->last_name);
            $this->db->set("product_price", $log_data[0]->last_price);
            $this->db->set("purchase_price", $log_data[0]->last_cost);
            $this->db->update("ip_products");

            $log_array = array(
                "user_id" => $user_id,
                "last_parent" => $log_data[0]->new_parent,
                "last_name" => $log_data[0]->new_name,
                "new_name" => $log_data[0]->last_name,
                "new_parent" => $log_data[0]->last_parent,
                "date" => date("Y-m-d"),
                "time" => date("H:i:s"),
                "product_id" => $log_data[0]->product_id,
                "last_price" => $log_data[0]->new_price,
                "new_price" => $log_data[0]->last_price,
                "last_cost" => $log_data[0]->new_cost,
                "new_cost" => $log_data[0]->last_cost
            );
            //INSERT LOG AS PRODUCT
            $this->db->insert("ip_track_pdatatree", $log_array);
        }
    }

    public function process_product_selections() {
        $this->load->model('mdl_products');

        $products = $this->mdl_products->where_in('product_id', $this->input->post('product_ids'))->get()->result();
        foreach ($products as $product) {
            $product->product_price = format_amount($product->product_price);
        }

        echo json_encode($products);
    }

}
