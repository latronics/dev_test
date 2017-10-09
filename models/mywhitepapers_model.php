<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Mywhitepapers_model extends Model 

{

    function Mywhitepapers_model()

    {

        parent::Model();

    }





function ListItems()

	{	

		$this->db->select("wid, title, visible, ordering, file");		
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('whitepapers');


		if ($this->query->num_rows() > 0) 

			{

			return $this->query->result_array();

			}	

	}

function CountDownloads() 
	{
	
				$this->db->select("wdid, wid");	
				$this->query = $this->db->get('whitepaper_downloads');
				if ($this->query->num_rows() > 0) 
					{
						foreach ($this->query->result_array() as $key =>$value) 
						{
						if (isset($this->nums[$value['wid']])) $this->nums[$value['wid']]++;
						else $this->nums[$value['wid']] = 1;
						}
					
					return $this->nums;
					}
}


function GetItem($id)

	{

		$this->db->where('wid', (int)$id);

		$this->query = $this->db->get('whitepapers');

		if ($this->query->num_rows() > 0) 

			{

			$this->result = $this->query->row_array();

			}

		return $this->result;

	}	

	function Getfile($id)

	{
		$this->db->select('file');
		$this->db->where('wid', (int)$id);

		$this->query = $this->db->get('whitepapers');

		if ($this->query->num_rows() > 0) 

			{

			$this->result = $this->query->row_array();
			return $this->result['file'];
			}



	}

function GetMaxOrder()

	{

		$this->db->select_max('ordering');

		$this->query = $this->db->get('whitepapers');

		if ($this->query->num_rows() > 0) 

			{

			$this->result = $this->query->row_array();
			$this->result['ordering']++;
			return $this->result['ordering'];
			}
			else
			{
				
				
				return 1;
			}



		



	}

function Delete($id)

	{	

		$this->db->where('wid', (int)$id);

		$this->result = $this->db->delete('whitepapers'); 

		return $this->result;

	}

function MakeVisible($id)

	{

		$this->db->update('whitepapers', array('visible' => 1), array('wid' => (int)$id));

	}

function MakeNotVisible($id)

	{

		$this->db->update('whitepapers', array('visible' => 0), array('wid' => (int)$id));

	}


function Update($id, $data)

	{

		$this->db->update('whitepapers', $data, array('wid' => (int)$id));

	}

function Insert($data)

	{

		$this->db->insert('whitepapers', $data);

	}

function ChangeOrder($id, $order)

	{			

		$this->db->update('whitepapers', array('ordering' => (int)$order), array('wid' => (int)$id));		

	}

	

function ReOrder()

	{	



		$this->db->select('wid, ordering');		
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('whitepapers');

		if ($this->query->num_rows() > 0) 

			{	

			$this->db_data = $this->query->result_array();

			$roll = 1;			

			foreach ($this->db_data as $udb) 

				{					

				$this->db->update('whitepapers', array('ordering' => $roll), array('wid' => (int)$udb['wid']));

				$roll++;

				}

			}

	}



}

?>