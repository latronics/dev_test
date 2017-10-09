<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mystores extends Controller {

function Mystores()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->load->model('usc_model');
		$this->Auth_model->VerifyAdmin();
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Stores');	
		
		
	}
	
	
function index()
	{
		$this->mysmarty->view('mystores/mystores_select.html');
	}
function order($store = 'Usc')
	{
		if ($store != 'Usc' && $store != 'Hawthorne' && $store != 'Venice') $store = 'Usc';
		$this->mysmarty->assign('store', $store);	
		
		$this->load->library('form_validation');
		
					$this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|min_length[5]|numeric|xss_clean');
					$this->form_validation->set_rules('Email', 'Email', 'trim|required|valid_email|xss_clean');
					$this->form_validation->set_rules('FirstName','First Name', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('brand', 'Brand', 'trim|xss_clean');
					$this->form_validation->set_rules('model', 'Model', 'trim|xss_clean');
					$this->form_validation->set_rules('serial', 'Serial Number', 'trim|xss_clean');
					$this->form_validation->set_rules('datarec', 'Date Recovery', 'trim|xss_clean');
					$this->form_validation->set_rules('acc', 'Accessories included', 'trim|xss_clean');
					if ($this->input->post('subtype') == 'p')
						{
						$this->form_validation->set_rules('estprice', 'Cashiered amount', 'trim|xss_clean|callback__gtzero');
						}
					else
						{
							$this->form_validation->set_rules('estprice', 'Cashiered amount', 'trim|required|numeric|xss_clean');
						}
				
					$this->form_validation->set_rules('subtype', 'Order Type', 'trim|required|xss_clean');
					$this->form_validation->set_rules('item', 'Description / Product', 'trim|required|min_length[2]|xss_clean');				
					
					$idata = array (
									   'Telephone' =>  $this->input->post('Telephone', TRUE),
									   'Email' =>  $this->input->post('Email', TRUE),
									   'FirstName' =>  $this->input->post('FirstName', TRUE),
									   'LastName' =>  $this->input->post('LastName', TRUE),
									   'subtype' => $this->input->post('subtype', TRUE),
									   'brand' => $this->input->post('brand', TRUE), 
								  	   'model' => $this->input->post('model', TRUE), 
									   'serial' => $this->input->post('serial', TRUE), 
									   'datarec' => $this->input->post('datarec', TRUE), 
									   'acc' => $this->input->post('acc', TRUE),
									   'estprice' => $this->input->post('estprice', TRUE),
									   'item' => $this->input->post('item', TRUE)
									   );
					
					if ($this->form_validation->run() == FALSE)
						{	
							$this->mysmarty->assign('regdata', $idata);								
							$this->mysmarty->assign('errors', $this->form_validation->_error_array);
							$this->mysmarty->assign('mailto', FALSE);
							$this->mysmarty->assign('uscview', 'home');		
							$this->mysmarty->view('mystores/mystores_main.html');
							exit();
						}
					else
						{
							$time = CurrentTime();
							$date = CurrentUSADate();
							$userdata = array (
									   'pass' =>  md5(md5($this->form_validation->set_value('Telephone'))),
									   'email' =>  $this->form_validation->set_value('Email'),
									   'fname' =>  $this->form_validation->set_value('FirstName'),
									   'lname' =>  $this->form_validation->set_value('LastName'),
									   'reg_date' => $time,
									   'active' => 1,
									   'details' => serialize(array ('Telephone' => $this->form_validation->set_value('Telephone'))),
									   'usc' => 1
									   );
												
							$orderdata = array (
												'subtype' => $this->form_validation->set_value('subtype'),
												'fname' => $this->form_validation->set_value('FirstName'),
												'lname' => $this->form_validation->set_value('LastName'),
												'email' => $this->form_validation->set_value('Email'),
												'tel' => $this->form_validation->set_value('Telephone'),
												'complete' => 1,
												'complete_time' => $time,
												'status' => serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => $time))),
												'time' => $time,
												'admin' => $this->session->userdata['name']
												);
							
							if ($orderdata['subtype'] != 'p') $orderdata['comments'] = '<strong>Brand:</strong> '.$this->form_validation->set_value('brand').'<br>
																<strong>Model:</strong> '.$this->form_validation->set_value('model').'<br>
																<strong>Problem:</strong> '.$this->form_validation->set_value('item').'<br>
																<strong>Serial Number:</strong> '.$this->form_validation->set_value('serial').'<br>
																<strong>Data Recovery:</strong> '.$this->form_validation->set_value('datarec').'<br>
																<strong>Accessories Included:</strong> '.$this->form_validation->set_value('acc').'<br>
																<strong>Cashier:</strong> $'.sprintf("%.2f", (float)$this->form_validation->set_value('estprice'));
							else							
							{
								$orderdata['comments'] = '<strong>Product:</strong> '.$this->form_validation->set_value('item').'<br>';
								$orderdata['endprice'] = $this->form_validation->set_value('estprice');
								
							}
							
							if ($store == 'Venice') $orderdata['buytype'] = 8;
							elseif ($store == 'Hawthorne') $orderdata['buytype'] = 7;
							else $orderdata['buytype'] = 6;
							
							$viewdata = array (
									   'Telephone' =>  $this->form_validation->set_value('Telephone'),
									   'Email' =>  $this->form_validation->set_value('Email'),
									   'FirstName' =>  $this->form_validation->set_value('FirstName'),
									   'LastName' =>  $this->form_validation->set_value('LastName'),
									   'brand' => $this->form_validation->set_value('brand'), 
								  	   'model' => $this->form_validation->set_value('model'), 
									   'estprice' => $this->form_validation->set_value('estprice'),
									   'serial' => $this->form_validation->set_value('serial'),
									   'datarec' => $this->form_validation->set_value('datarec'),	
									   'acc' => $this->form_validation->set_value('acc'),	
									   'item' => $this->form_validation->set_value('item'),
									   'subtype' => $orderdata['subtype']
									   );   
							$exists = $this->usc_model->InsertUser($userdata);
							$oid = $this->usc_model->InsertOrder($orderdata);

							$this->mysmarty->assign('exists', $exists);				
							
							
							$this->mysmarty->assign('regdata', $viewdata);	
							$this->mysmarty->assign('uscview', 'ok');
							$this->mysmarty->assign('oid', $oid);
							$this->mysmarty->assign('mailto', TRUE);
							$this->mysmarty->assign('alldata', FALSE);
							$this->mysmarty->assign('time', FlipDateMail($time));
							$this->mysmarty->assign('date', str_replace('-','/', $date));
							
							
							if ($orderdata['subtype'] != 'p') $this->admindata['msg_title'] = 'Copy of your '.$store.' Store repair details.';
							else $this->admindata['msg_title'] = 'Copy of your '.$store.' Store purchase.';
							
							$this->admindata['msg_date'] = $time;
		
		
							$this->admindata['msg_body'] = $this->mysmarty->fetch('mystores/mystores_main.html');
							GoMail ($this->admindata, $orderdata['email']);
							
							$this->mysmarty->assign('alldata', TRUE);
							$this->admindata['msg_body'] = $this->mysmarty->fetch('mystores/mystores_main.html');							
								
								$rid = $this->usc_model->InsertReceipt($oid, $this->admindata['msg_body']);
								$this->usc_model->UpdateRID((int)$oid, (int)$rid);

							$this->maildata['msg_date'] = CurrentTime();
							
							if ($orderdata['subtype'] == 'p') $txttype = 'Product';
							elseif ($orderdata['subtype'] == 'r') $txttype = 'Repair';
							else $txttype = 'Product & Repair';
							
							$this->maildata['msg_title'] = 'New '.$store.' '.$txttype.' Order by '.$this->session->userdata['name'].' @ '.FlipDateMail($this->maildata['msg_date']);
							$this->maildata['msg_body'] = $orderdata['comments'];
							
							$this->mailid = 13;
							GoMail ($this->maildata);
							$this->load->model('Login_model');
							$this->Login_model->InsertHistoryData($this->maildata);
							
							$this->mysmarty->assign('alldata', FALSE);
							$this->mysmarty->assign('mailto', FALSE);
							$this->mysmarty->view('mystores/mystores_main.html');
							
						}
	
	}

function PrintReceipt($oid = 0)
	{	

		$data = $this->usc_model->PrintReceipt((int)$oid);		
		if ($data) {
			echo ' <html>
			<head>
			<script language="Javascript1.2">
			  <!--
			  function printpage() {
			  window.print();
			  }
			  //-->
			</script>
			</head><body  onload="printpage()">';
			echo $data['receipt'];
			echo '</body></html>';
			}
		else
		{ 
			echo 'Receipt Not Found';
		}
		/*
			
						$this->load->model('Myorders_model');
						$viewdata = $this->Myorders_model->GetOrder($oid);
							
							$this->mysmarty->assign('regdata', $viewdata);	
							$this->mysmarty->assign('uscview', 'ok');
							$this->mysmarty->assign('oid', $oid);
							$this->mysmarty->assign('mailto', TRUE);							
							$this->mysmarty->assign('time', FlipDateMail($time));
							$this->mysmarty->assign('date', str_replace('-','/', $date));
		
							$this->mysmarty->assign('alldata', TRUE);
							$this->admindata['msg_body'] = $this->mysmarty->fetch('mystores/mystores_main.html');							

		echo ' <html>
			<head>
			<script language="Javascript1.2">
			  <!--
			  function printpage() {
			  window.print();
			  }
			  //-->
			</script>
			</head><body  onload="printpage()">';
			echo $this->admindata['msg_body'];
			echo '</body></html>';		
		*/
		
		
		
	}
	
function _gtzero($str)
	{

		if ((float)$str == 0)
		{
		$this->form_validation->set_message('_gtzero', 'For Product sales - please input cashiered amount');
			return FALSE;
		}
		else
		{			
			return TRUE;
		}
		
	}
}