<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weborders extends Controller {

	function index()
	{
            $this->db->where("complete", 1);
            $this->db->order_by("oid", "desc");
            $data['orders'] = json_encode($this->db->get("orders")->result_object());
            $this->load->view("myorders/weborders", $data);
        }
        public function order_search()
        {
        }
}