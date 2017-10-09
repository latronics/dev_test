<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mygoogleshopping extends Controller {

	function index()
	{
	redirect('Mygoogleshopping/getdata');
        }
        function getdata()
        {
            $show_with_cat =  $this->input->post("nocategory");
            $data = $this->input->post("data");
            
            $explode = explode(" ", $data);
          
            
            if($show_with_cat == "on")
            {
                $this->db->where("gtaxonomy <>", "");
            }
            $this->db->where("e_title <>", "");
             foreach($explode as $explode)
           {
               $this->db->like("e_title", $explode);
           }
            
            
            $items = $this->db->get("ebay");
            $result['rows'] = $items->num_rows();
            $result['result'] = $items->result_object();
            $result['show_gcategory'] = $show_with_cat;
            $result['search_data'] = $data;
            $this->load->view("google/googlecategories", $result);
        }
        
        function loadgooglecat($term)
        {
           
            $this->db->like("cat_title", $term);
            $this->db->limit(10);
            $google_cats_data = $this->db->get("la_google_cat")->result_object();
            foreach($google_cats_data as $google_cats_data)
            {
                $google_cat_name[] = $google_cats_data->cat_title;
            }
            echo json_encode($google_cat_name);
        }
        
        function savedata()
        {
            
            $item_ids = $this->input->post("ebay_item");
            foreach($item_ids as $item_ids)
            {
                $this->db->set("gtaxonomy", $this->input->post("google_category"));
                $this->db->where("e_id", $item_ids);
                $this->db->update("ebayx");
            }
            redirect('Mygoogleshopping/getdata');
        }
}