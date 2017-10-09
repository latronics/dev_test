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

class mdl_parts extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    public function record_count() {
        $this->warehouse = $this->load->db('warehouse');
        return $this->warehouse->count_all("warehouse");
    }

    public function fetch_countries($limit, $start) {
        $this->warehouse->limit($limit, $start);
        $query = $this->warehouse->get("warehouse");

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
   }
}