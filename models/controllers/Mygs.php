<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mygs extends Controller {

function Mygs()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->load->model('Myproducts_model');
		$this->Auth_model->VerifyAdmin();
		//if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		//if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}

		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('gotoebay',$this->session->flashdata('gotoebay'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'GS');	


	}
function index()
	{	Redirect('Mygoogledrive/Logs');
		$this->db->limit(2000);
		$this->db->order_by("gsid", "DESC");
		$q = $this->db->get('gsdata');
		$list = false;
		if ($q->num_rows() > 0) $list = $q->result_array();
		$this->mysmarty->assign('list', $list);	
		$this->mysmarty->view('mygs/mygs_main.html');
}

function listsheets()
{
		$q = $this->db->get('gs_sheets');
		$list = false;
		if ($q->num_rows() > 0) $list = $q->result_array();
		$this->mysmarty->assign('list', $list);	
		$this->mysmarty->assign('colmap', array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z'));		
		$this->mysmarty->view('mygs/mygs_sheetslist.html');
}
function testadd()
{


$key = '0ApHMD7nkSM4YdEJxcE5NRWtxVzJENGZaSjRPd19oMGc';

require_once($this->config->config['pathtopublic'].'/gsssettings.php');
		$spreadsheet_key = $key;
		
		$msg = '';
			try {
				// Create an object
				$this->load->library('Googlesheets');
				//$this->googlesheets->testme();
				// Create an access token using the Refresh Token saved in settings.php
				$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
				// Get Worksheet Information 
				$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($spreadsheet_key, $access_token);
				
				printcool ($spreadsheet_info);break;
				// Get the column cells
				$cells1 = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], 1, 1, 1, $spreadsheet_info['col_count'], $access_token);
				
				printcool ($spreadsheet_info);
			}
			catch(Exception $e) {
				$msg .= $e->getMessage();
			}
			
		echo $msg;

}


function addsheets()
{
		$key = $this->input->post('gskey', TRUE);
		$title = $this->input->post('gstitle', TRUE);

		if ($key == '' || $title == '') { echo 'Key or Title are empty'; exit(); }
		
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
			
		require_once($this->config->config['pathtopublic'].'/gsssettings.php');
		$spreadsheet_key = $key;
		
		$msg = '';
			try {
				// Create an object
				$this->load->library('Googlesheets');
				//$this->googlesheets->testme();
				// Create an access token using the Refresh Token saved in settings.php
				$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
				// Get Worksheet Information 
				$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($spreadsheet_key, $access_token);
				$pdata = array('gs_key' => $key, 'gs_title' => $title);
				$this->db->insert('gs_sheets', $pdata);			
				$sid = $this->db->insert_id();
				
				// Get the column cells
				foreach ($spreadsheet_info as $s)
				{
				$data = $pdata;
				$cells1 = $this->googlesheets->GetCells($s['worksheet_cells_feed_url'], 1, 1, 1, $s['col_count'], $access_token);				
				$data['gs_parent'] = $sid;
				$data['gs_wtitle'] = $s['title'];
				$data['worksheet_id'] = $s['worksheet_id'];
				$data['worksheet_list_feed_url'] = $s['worksheet_list_feed_url'];
				$data['worksheet_cells_feed_url'] = $s['worksheet_cells_feed_url'];
				$data['worksheet_edit_url'] = $s['worksheet_edit_url'];
				
				$colmap = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');	

				foreach ($cells1 as $c)
				{
					if ($c['value'] == 'Ebay Title') 
					{
						$data['field_title'] = $colmap[(int)$c['col']];
						$data['field_titlenum'] = $c['col'];
					}
					
					if ($c['value'] == 'Date Sold') 
					{
						$data['field_paidtime'] = $colmap[(int)$c['col']];
						$data['field_paidtimenum'] = $c['col'];
					}				
					
					if ($c['value'] == 'Price Sold') 
					{
						$data['field_paid'] = $colmap[(int)$c['col']];
						$data['field_paidnum'] = $c['col'];
					}
					
					if ($c['value'] == 'Where Sold') 
					{
						$data['field_type'] = $colmap[(int)$c['col']];
						$data['field_typenum'] = $c['col'];
					}
					
					if ($c['value'] == 'Auction ID') 
					{
						$data['field_auction'] = $colmap[(int)$c['col']];
						$data['field_auctionnum'] = $c['col'];
					}
				}			
				
				$this->db->insert('gs_sheets', $data);
				unset($data);
				}

				Redirect('Mygs/listsheets');
			}
			catch(Exception $e) {
				$msg .= $e->getMessage();
			}
			
		echo $msg;
			
		
}


function addsheet()
{
		$key = $this->input->post('gskey', TRUE);
		$title = $this->input->post('gstitle', TRUE);

		if ($key == '' || $title == '') { echo 'Key or Title are empty'; exit(); }
		
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
			
		require_once($this->config->config['pathtopublic'].'/gsssettings.php');
		$spreadsheet_key = $key;
		
		$msg = '';
			try {
				// Create an object
				$this->load->library('Googlesheets');
				//$this->googlesheets->testme();
				// Create an access token using the Refresh Token saved in settings.php
				$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
				// Get Worksheet Information 
				$spreadsheet_info = $this->googlesheets->GetWorksheetInformation($spreadsheet_key, $access_token);
				// Get the column cells
				$cells1 = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], 1, 1, 1, $spreadsheet_info['col_count'], $access_token);
				$data = array('gs_key' => $key, 'gs_title' => $title);
				
				$colmap = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');	
				
				foreach ($cells1 as $c)
				{
					if ($c['value'] == 'Ebay Title') 
					{
						$data['field_title'] = $colmap[(int)$c['col']];
						$data['field_titlenum'] = $c['col'];
					}
					
					if ($c['value'] == 'Date Sold') 
					{
						$data['field_paidtime'] = $colmap[(int)$c['col']];
						$data['field_paidtimenum'] = $c['col'];
					}				
					
					if ($c['value'] == 'Price Sold') 
					{
						$data['field_paid'] = $colmap[(int)$c['col']];
						$data['field_paidnum'] = $c['col'];
					}
					
					if ($c['value'] == 'Where Sold') 
					{
						$data['field_type'] = $colmap[(int)$c['col']];
						$data['field_typenum'] = $c['col'];
					}
					
					if ($c['value'] == 'Auction ID') 
					{
						$data['field_auction'] = $colmap[(int)$c['col']];
						$data['field_auctionnum'] = $c['col'];
					}
				}
	
				$this->db->insert('gs_sheets', $data);
				Redirect('Mygs/listsheets');
			}
			catch(Exception $e) {
				$msg .= $e->getMessage();
			}
			
		echo $msg;
			
		
}
function savesheet()
{
	$colmap = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');	
	
	$this->db->update('gs_sheets', array('field_title' => $colmap[(int)$this->input->post('field_titlenum')],
	'field_titlenum' => (int)$this->input->post('field_titlenum'),
	'field_paidtime' => $colmap[(int)$this->input->post('field_paidtimenum')],
	'field_paidtimenum' =>  (int)$this->input->post('field_paidtimenum'),
	'field_paid' => $colmap[(int)$this->input->post('field_paidnum')],
	'field_paidnum' => (int)$this->input->post('field_paidnum'),
	'field_type' => $colmap[(int)$this->input->post('field_typenum')],
	'field_typenum' => (int)$this->input->post('field_typenum'),
	'field_auction' => $colmap[(int)$this->input->post('field_auctionnum')],
	'field_auctionnum' => (int)$this->input->post('field_auctionnum')), array('gs_id' => (int)$this->input->post('gs_id')));
	$this->session->set_flashdata('success_msg', 'SUCCESS - Updated Sheet '.(int)$this->input->post('gs_id'));
	Redirect('Mygs/listsheets');
	
}
function deletesheet($id = 0)
{
	if ((int)$id == 0 ) echo 'NO ID';
	
		$this->db->select("e_id");		
		$this->db->where("gsid1", (int)$id);
		$this->db->or_where("gsid2", (int)$id);		
		$this->db->or_where("gsid3", (int)$id);		
		$this->db->or_where("gsid4", (int)$id);		
		$this->db->or_where("gsid5", (int)$id);	
		$this->db->order_by("e_id", "DESC");	

		$this->query = $this->db->get('ebay');

		if ($this->query->num_rows() > 0) 
		{	
			echo 'Cannot Delete - There are ebay items linked to this sheet:<br><br>';
			//	printcool ($this->query->result_array());
			foreach ($this->query->result_array() as $v)
 			{
				echo '<a href='.Site_url().'Myebay/Edit/'.$v['e_id'].'" target="_blank">'.$v['e_id'].'</a><br>';			
			}
			exit();
		}
		else
		{
			$this->db->where('gs_id', (int)$id);
			$this->db->delete('gs_sheets'); 
			$this->session->set_flashdata('success_msg', 'SUCCESS - Deleted Sheet '.(int)$id);
			Redirect('Mygs/listsheets');
		}
}
}