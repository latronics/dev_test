<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myusers_model extends Model 
{
    function Myusers_model()
    {
        parent::Model();
    }

function GetAllUsers($params = '')
	{
		$this->db->select("user_id, fname, lname, email, active, reg_date");
		switch ($params) {
				case 'By_Id_Ascending':
				$this->db->order_by("user_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("user_id", "DESC");
			break;				
				case 'By_First_Name_Ascending':
				$this->db->order_by("fname", "ASC");
			break;
				case 'By_First_Name_Descending':
				$this->db->order_by("fname", "DESC");
			break;
				case 'By_Last_Name_Ascending':
				$this->db->order_by("lname", "ASC");
			break;
				case 'By_Last_Name_Descending':
				$this->db->order_by("lname", "DESC");
			break;
				case 'By_Email_Ascending':
				$this->db->order_by("email", "ASC");
			break;
				case 'By_Email_Descending':
				$this->db->order_by("email", "DESC");
			break;
				case 'By_Active_Ascending':
				$this->db->order_by("active", "ASC");
			break;
				case 'By_Active_Descending':
				$this->db->order_by("active", "DESC");
			break;
				case 'By_Registration_Ascending':
				$this->db->order_by("reg_date", "ASC");
			break;
				default:
				
		}
		//$this->db->where('usc', 0);
		$this->db->order_by("reg_date", "DESC");
		$this->query = $this->db->get('users');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}

function GetUser($userid, $format = false)
	{
		$this->db->select("user_id, fname, lname, email, active, reg_date, discount, details");
		$this->db->where('user_id', (int)$userid);
		$this->query = $this->db->get('users');

		if ($this->query->num_rows() > 0) 
			{
				foreach ($this->query->result() as $row)
				{
						$result['user_id'] = $row->user_id;
						$result['fname'] = $row->fname;
						$result['lname'] = $row->lname;
						$result['email'] = $row->email;
						$result['active'] = $row->active;
						$result['reg_date'] = $row->reg_date;
						$result['details'] = unserialize($row->details);
				
				}
			return $result;
			}	
	}
	
function GetUserAll($userid)
	{
		$this->db->select("user_id, fname, lname, email, active, reg_date, discount, details");
		$this->db->where('user_id', (int)$userid);
		$this->aquery = $this->db->get('users');

		if ($this->aquery->num_rows() > 0) 
			{
				foreach ($this->aquery->result_array() as $row)
				{
						$result['user_id'] = $row['user_id'];
						$result['FirstName'] = $row['fname'];
						$result['LastName'] = $row['lname'];
						$result['Email'] = $row['email'];
						$result['active'] = $row['active'];
						$result['reg_date'] = $row['reg_date'];
						$details = unserialize($row['details']);
						$result['Telephone'] = $details['Telephone'];
						$result['Mobile'] = $details['Mobile'];
				
				}
			}	
		$this->db->where('user_id', (int)$userid);
		$this->db->where('ua_type', 'b');
		$this->bquery = $this->db->get('users_addresses');

		if ($this->bquery->num_rows() > 0) 
			{
				foreach ($this->bquery->result_array() as $row)
				{
				$result['Address'] = $row['Address'];
				$result['City'] = $row['City'];
				$result['PostCode'] = $row['PostCode'];
				$result['State'] = $row['State'];
				$result['Country'] = $row['Country'];					
				}
			}	
			
		$this->db->where('user_id', (int)$userid);
		$this->db->where('ua_type', 'd');
		$this->cquery = $this->db->get('users_addresses');

		if ($this->cquery->num_rows() > 0) 
			{
				foreach ($this->cquery->result_array() as $row)
				{
				$result['dAddress'] = $row['Address'];
				$result['dCity'] = $row['City'];
				$result['dPostCode'] = $row['PostCode'];
				$result['dState'] = $row['State'];
				$result['dCountry'] = $row['Country'];					
				}
			}	
		if ($this->aquery->num_rows() > 0) 
			{
				return $result;
			}
	}
function GetUserAddress($userid, $type)
	{
		$this->db->where('user_id', (int)$userid);
		$this->db->where('ua_type', $type);
		$this->query = $this->db->get('users_addresses');

		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}	
	}
function CheckUserExists($userid)
	{
		$this->db->select("user_id");
		$this->db->where('user_id', (int)$userid);
		$this->query = $this->db->get('users');

		if ($this->query->num_rows() > 0) 
			{
			return $userid;
			}	
			else
			{
			return 0;
			}
	}
function DeleteUser($userid)
	{	
		$this->db->where('user_id', (int)$userid);
		$this->db->delete('users'); 
		$this->db->where('user_id', (int)$userid);
		$this->db->delete('users_addresses');
	}
function DeactivateUser($userid)
	{
		$this->db->update('users', array('active' => 0), array('user_id' => (int)$userid));
	}
function ActivateUser($userid)
	{
		$this->db->update('users', array('active' => 1), array('user_id' => (int)$userid));
	}
function UpdateUser($userid, $data)
	{
		$this->db->update('users', $data, array('user_id' => (int)$user_id));		
	}
}
?>
