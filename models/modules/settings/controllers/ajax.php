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

class Ajax extends Admin_Controller {

    public $ajax_controller = true;

    public function get_cron_key() {
        echo random_string('alnum', 16);
//$this->input->post('textbox');



        $datastring_insert = array(
            'setting_key' => "show_status_ticket",
            'setting_value' => $this->input->post('checkbox')
        );
        $datastring_update = array(
            'setting_value' => $this->input->post('checkbox')
        );
        
$receive_textbox = $this->input->post('textbox');
$get_days = str_replace("show_status_ticket[]", "", $receive_textbox);
$get_days2 = str_replace("=", "", $get_days);
$get_days3 = str_replace("+", " ", $get_days2);
$get_days4 = str_replace("show_status_text[]", "", $get_days3);
$days_array = explode("&", $get_days4);


        

        

        
        
        $this->db->select("*");
        $this->db->where("setting_key", "show_status_ticket");
        $rows = $this->db->get("ip_settings")->num_rows();

        if ($rows == 0) {
            $this->db->insert("ip_settings", $datastring_insert);
        } else {
            $this->db->where("setting_key", "show_status_ticket");
            $this->db->update('ip_settings', $datastring_update);
        }
        
        $x =1;
        foreach ($days_array as $days_array) {

                                    if (strlen($days_array)) {
 $datastring_textbox_update = array(
            'days_urgent' => $days_array
        );
 
                                       

            $this->db->where("id", $x);
            $this->db->update('status', $datastring_textbox_update);
                                           
                                               
                                            }
                                            $x++;
                                    }
                                   
                                
     
            
        
        
        
        
        
    }
    
     

}
