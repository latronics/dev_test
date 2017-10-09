<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class usc_model extends Model 
{
    function usc_model()
    {
        parent::Model();
    }

function InsertUser($reg_data) 
	{
		$this->db->select('user_id');
		$this->getresult = $this->db->get_where('users',array('email' => $reg_data['email']),1);
		if ($this->getresult->num_rows() > 0) 
			{
				$userid = $this->getresult->row_array();
					/*$this->db->where('user_id',(int)$userid['user_id']);
					$this->db->limit(1);
					$this->db->update('users', array('pass' => $reg_data['pass'], 'fname' => $reg_data['fname'], 'lname' => $reg_data['lname']));	*/
				return $userid['user_id'];
			}
		else
			{		
				$this->db->insert('users',$reg_data);
				return 0;
			}
	}
function InsertOrder($data) 
	{	
		$this->db->insert('orders',$data);
		return $this->db->insert_id();
	}
function InsertReceipt($id, $data)
	{
		$this->db->insert('order_receipts', array('oid' => $id, 'receipt' => $data));
		return $this->db->insert_id();
	}
function UpdateRID($oid = 0, $rid = 0)
	{	
		if ($oid > 0 && $rid > 0) {
			$this->db->update('orders', array('rid' => (int)$rid), array('oid' => (int)$oid));

		}
	}
	
function PrintReceipt($id = '')
	{
		$this->db->where('oid', (int)$id);
		$this->query = $this->db->get('order_receipts');

		if ($this->query->num_rows() > 0) 
			{
			 return $this->query->row_array();
			}
	}
}
?>
