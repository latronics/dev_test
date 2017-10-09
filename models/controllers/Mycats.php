<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mycats extends Controller {

    function LockCats($unlock = 0) {
        $e_id = $this->input->post("e_id");
        $category_type = $this->input->post("category_type");
        if($category_type == "lockall")
        {
            $this->db->where("e_id", $e_id);
            $this->db->set("lock_google_cat", 1);
            $this->db->set("lock_amazon_cat", 1);
            $this->db->set("lock_ebay_cat", 1);
            $this->db->update("ebay");
        }else if ($category_type == "unlockall")
        {
            $this->db->where("e_id", $e_id);
            $this->db->set("lock_google_cat", 0);
            $this->db->set("lock_amazon_cat", 0);
            $this->db->set("lock_ebay_cat", 0);
            $this->db->update("ebay");
        }else
        {
        
        if ($unlock == 0) {
            if ($category_type == "google") {
                $this->db->where("e_id", $e_id);
                $this->db->set("lock_google_cat", 1);
                $this->db->update("ebay");
            } else if ($category_type == "amazon") {
                $this->db->where("e_id", $e_id);
                $this->db->set("lock_amazon_cat", 1);
                $this->db->update("ebay");
            } else if ($category_type == "ebay") {
                $this->db->where("e_id", $e_id);
                $this->db->set("lock_ebay_cat", 1);
                $this->db->update("ebay");
            }
        }else if ($unlock == 1)
        {
            if ($category_type == "google") {
                $this->db->where("e_id", $e_id);
                $this->db->set("lock_google_cat", 0);
                $this->db->update("ebay");
            } else if ($category_type == "amazon") {
                $this->db->where("e_id", $e_id);
                $this->db->set("lock_amazon_cat", 0);
                $this->db->update("ebay");
            } else if ($category_type == "ebay") {
                $this->db->where("e_id", $e_id);
                $this->db->set("lock_ebay_cat", 0);
                $this->db->update("ebay");
            }  
        }
        }
    }

}
