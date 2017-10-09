<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myinventory extends Controller {

function Myinventory()
	{
		parent::Controller();		
		$this->load->model('Myebay_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		//if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		//if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('gotoebay',$this->session->flashdata('gotoebay'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Ebay');	
		$this->mysmarty->assign('ebupd', TRUE);
		
		$this->actabrv = array('e_img1' => 'Image 1', 'e_img2' => 'Image 2', 'e_img3' => 'Image 3', 'e_img4' => 'Image 4', 'quantity' => 'Local Quantity', 'e_part' => 'BCN', 'e_qpart' => 'BCN Count', 'buyItNowPrice' => 'Price', 'e_title' => 'Title', 'e_sef' => 'SEF URL', 'e_condition' => 'Condition', 'e_model' => 'Model', 'e_compat' => 'Compatibility', 'ebayquantity' => 'Local eBay Quantity', 'Ebay Quantity' => 'Local eBay Quantity', 'idpath' => 'Image Dir.', 'e_desc' => 'Descripion', 'upc' => 'UPC', 'e_manuf' => 'Manufacturer', 'e_package' => 'Package', 'location' => 'Location', 'pCTitle' => 'Pri.Cat. Title', 'ebay_submitted' => 'Submitted','ebay_id' => 'eBay ID', 'sn' => 'Transaction BCN', 'asc' => 'ActShipCost', 'storeCatTitle' => 'Store Category', 'storeCatID' => 'Store Cat. ID');

	}
function index()
	{	
		exit('Nothing here yet');
	}
}