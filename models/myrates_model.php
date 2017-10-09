<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Myrates_model extends Model 
{
    function Myrates_model()
    {
        parent::Model();
    }
function GetRates()
	{	
		$this->query = $this->db->get('delivery_rates');
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function Update($id, $data)
	{
		$this->db->update('delivery_rates', $data, array('dr_id' => (int)$id));		
	}
}
?>