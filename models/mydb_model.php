<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mydb_model extends Model 
{
    function Mydb_model()
    {
        parent::Model();
    }


function GetOne ($select = '', $table = '', $where1 = '', $where2 = '') 
{
	$this->db->select($select);
	$this->db->where($where1, $where2);
	$this->query = $this->db->get($table);
	if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result[$select];
			}
}

function GetRow ($select = '', $table = '', $where1 = '', $where2 = '') 
{
	$this->db->select($select);
	$this->db->where($where1, $where2);
	$this->query = $this->db->get($table);
	if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result;
			}
}
function GetAll ($select = '', $table = '', $where1 = '', $where2 = '') 
{
	$this->db->select($select);
	$this->db->where($where1, $where2);
	$this->query = $this->db->get($table);
	if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->result_array();
			return $this->result;
			}
}	
}
?>
