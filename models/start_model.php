<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Start_model extends Model 
{
    function Start_model()
    {
        parent::Model();
    }

function InsertHistoryData($history_data) 
	{
	$this->db->insert('admin_history',$history_data);	
	}
function GetUserDetails ($userid) 
	{
		$this->db->select('user_id, pass, reg_date, email AS Email, fname AS FirstName, lname AS LastName, details');
		$this->getresult = $this->db->get_where('users',array('user_id' => (int)$userid),1);
			if ($this->getresult->num_rows() > 0) 
			{
				$this->found = $this->getresult->row_array();
				$this->foundtmp = unserialize($this->found['details']);
				 $this->found['Telephone'] = $this->foundtmp['Telephone'];
				if (isset($this->foundtmp['Mobile'])) $this->found['Mobile'] = $this->foundtmp['Mobile'];
				else $this->found['Mobile'] = '';
			}
			
			
			$this->db->where('user_id', (int)$userid);
			$this->db->where('ua_type', 'b');
			$this->bquery = $this->db->get('users_addresses');

			if ($this->bquery->num_rows() > 0) 
				{
					foreach ($this->bquery->result_array() as $row)
					{
					$this->found['Address'] = $row['Address'];
					$this->found['City'] = $row['City'];
					$this->found['PostCode'] = $row['PostCode'];
					$this->found['State'] = $row['State'];
					$this->found['Country'] = $row['Country'];					
					}
				}	
				
			$this->db->where('user_id', (int)$userid);
			$this->db->where('ua_type', 'd');
			$this->cquery = $this->db->get('users_addresses');

			if ($this->cquery->num_rows() > 0) 
				{
					foreach ($this->cquery->result_array() as $row)
					{
					$this->found['dAddress'] = $row['Address'];
					$this->found['dCity'] = $row['City'];
					$this->found['dPostCode'] = $row['PostCode'];
					$this->found['dState'] = $row['State'];
					$this->found['dCountry'] = $row['Country'];					
					}
				}
			else 
				{
					$this->found['same'] = 1;
				}
			
		if ($this->getresult->num_rows() > 0) 
			{	
			return 	$this->found;	
			}
	}
function CheckPassMatch ($userid, $pass) 
	{
		$this->db->select('user_id, pass');
		$this->getresult = $this->db->get_where('users',array('user_id' => (int)$userid),1);
			if ($this->getresult->num_rows() > 0) 
			{	
				$this->found = $this->getresult->row_array();
				if ($this->found['pass'] == $pass) 
					{
					$this->toupdate = (int)$this->found['user_id'];
					}
					else 
					{
					$this->toupdate = FALSE;
					}
			}
			else 
			{
				$this->toupdate = FALSE;
			}
		return 	$this->toupdate;	
	}

function UpdateAllUserDetails($user_id, $newpass, $email, $fname, $lname)
	{
	$this->db->where('user_id',(int)$user_id);
	$this->db->limit(1);
	$this->db->update('users', array('pass' => $newpass, 'fname' => $fname, 'lname' => $lname, 'email' => $email));			
	}
	
function UpdateUserDetails($user_id, $data)
	{
	$this->db->where('user_id', (int)$user_id);
	$this->db->limit(1);
	$this->db->update('users', $data);			
	}
function UpdateUser($id, $data)
	{
	$this->db->limit(1);
	$this->db->update('users', $data, array('user_id' => (int)$id));				
	}
function UpdateUserAddress($id, $data)
	{
	//$this->db->limit(1);
	$this->db->where('user_id', (int)$id);
	$this->db->where('ua_type', $data['ua_type']);
	$this->db->delete('users_addresses');
	$data['user_id'] = (int)$id;
	$data['ua_type'] = $data['ua_type'];
	$this->db->insert('users_addresses', $data);					
	}
function SameDeliveryAddress($userid)
	{
	//$this->db->limit(1);	
	$this->db->where('user_id', (int)$userid);
	$this->db->where('ua_type', 'd');
	$this->db->delete('users_addresses');
	}
}
?>
