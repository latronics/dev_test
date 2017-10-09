<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myadmins_model extends Model 
{
    function Myadmins_model()
    {
        parent::Model();
    }

function GetAllUsers()
	{
		$this->db->select("admin_id, type, name, ownnames, email, active, warehouse, listings, orders, accounting,auclimit");
		$this->db->order_by("admin_id", "ASC");
		$this->query = $this->db->get('administrators');
		if ($this->query->num_rows() > 0) return $this->query->result_array();	
	}

function GetUser($userid)
	{
		$this->db->select("admin_id,type,name,ownnames,email,active, warehouse, listings, orders");
		$this->db->where('admin_id', (int)$userid);
		$this->query = $this->db->get('administrators');
		if ($this->query->num_rows() > 0) return $this->query->row_array();
	}
	
function GetUserAll($userid)
	{
		$this->db->where('admin_id', (int)$userid);
		$this->aquery = $this->db->get('administrators');
		if ($this->aquery->num_rows() > 0)  return $this->aquery->row_array();
	}
function CheckUserExists($name)
	{
		$this->db->select("admin_id");
		$this->db->where('name', $name);
		$this->query = $this->db->get('administrators');
		if ($this->query->num_rows() > 0) return $name;
	}
function CheckUserExistsUpdate($name,$id)
	{
		$this->db->select("admin_id");
		$this->db->where('name', $name);
		$this->db->where('admin_id !=', (int)$id);
		$this->query = $this->db->get('administrators');
		if ($this->query->num_rows() > 0) return $name;
	}
function DeleteUser($userid)
	{	
		$this->db->where('admin_id', (int)$userid);
		$this->db->delete('administrators');
	}
function DeactivateUser($userid)
	{
		$this->db->update('administrators', array('active' => 0), array('admin_id' => (int)$userid));
	}
function ActivateUser($userid)
	{
		$this->db->update('administrators', array('active' => 1), array('admin_id' => (int)$userid));
	}
function UpdateUser($data, $userid)
	{
		$this->db->update('administrators', $data, array('admin_id' => (int)$userid));		
	}
function InsertUser($data)
	{
		$this->db->insert('administrators', $data);
	}
}
?>
