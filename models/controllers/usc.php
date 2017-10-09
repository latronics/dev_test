<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class usc extends Controller {

	function usc()
	{		
		parent::Controller();	
		
		/*if (($_SERVER['REMOTE_ADDR'] != '93.152.154.46') && ($_SERVER['REMOTE_ADDR'] != '85.130.28.178') &&  ($_SERVER['REMOTE_ADDR'] != '87.121.161.130')) {
			exit();
		}*/
		$this->load->model('Menus_model');	
		$this->load->model('Product_model');	
		$this->load->model('usc_model');
		$this->Menus_model->DoTracking();
		$this->Menus_model->GetStructure();		
		$this->Product_model->GetStructure('top');
		
		if (isset($this->session->userdata['noadd'])) $this->session->unset_userdata('noadd');
		if (isset($this->session->userdata['unregnoadd'])) $this->session->unset_userdata('unregnoadd');
			
		
		if (isset($this->session->userdata['user_id'])) {
			$this->load->model('Start_model');
			$this->load->model('Auth_model');
			$this->Auth_model->VerifyUser();	
			
		}
		$this->mysmarty->assign('session',$this->session->userdata);
					
	}
		
	function index() 
	{
		Redirect ('Mystores/Order/Usc');
		$this->load->model('Captcha_model');
		$this->Captcha_model->DoCaptcha();
		$this->load->library('form_validation');
		
					$this->form_validation->set_rules('Code','Verification', 'trim|required|min_length[3]|xss_clean');
					$this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|min_length[5]|numeric|xss_clean');
					$this->form_validation->set_rules('Email', 'Email', 'trim|required|valid_email|xss_clean');
					$this->form_validation->set_rules('FirstName','First Name', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('brand', 'Brand', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('model', 'Model', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('estprice', 'Estimated price', 'trim|required|numeric|xss_clean');
					$this->form_validation->set_rules('item', 'Description', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('Code', 'Verification', 'trim|required|min_length[2]|xss_clean|callback__captcha_check');
					
					$idata = array (
									   'Telephone' =>  $this->input->post('Telephone', TRUE),
									   'Email' =>  $this->input->post('Email', TRUE),
									   'FirstName' =>  $this->input->post('FirstName', TRUE),
									   'LastName' =>  $this->input->post('LastName', TRUE),
									   'brand' => $this->input->post('brand', TRUE), 
								  	   'model' => $this->input->post('model', TRUE), 
									   'estprice' => $this->input->post('estprice', TRUE),
									   'item' => $this->input->post('item', TRUE)
									   );
					
					if ($this->form_validation->run() == FALSE)
						{	
							$this->Captcha_model->DoCaptcha();
							$this->mysmarty->assign('regdata', $idata);								
							$this->mysmarty->assign('errors', $this->form_validation->_error_array);
							
							$this->mysmarty->assign('uscview', 'home');		
							$this->mysmarty->view('usc/usc_main.html');
							exit();
						}
					else
						{
							$time = CurrentTime();
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
												'buytype' => 6,
												'fname' => $this->form_validation->set_value('FirstName'),
												'lname' => $this->form_validation->set_value('LastName'),
												'email' => $this->form_validation->set_value('Email'),
												'tel' => $this->form_validation->set_value('Telephone'),
												'complete' => 1,
												'complete_time' => $time,
												'status' => serialize(array(array('status' => 1, 'comment' => '', 'notified' => 1, 'time' => $time))),
												'comments' => '<strong>Brand:</strong> '.$this->form_validation->set_value('brand').'<br>
																<strong>Model:</strong> '.$this->form_validation->set_value('model').'<br>
																<strong>Problem:</strong> '.$this->form_validation->set_value('item').'<br>
																<strong>Estimated price:</strong> $'.sprintf("%.2f", (float)$this->form_validation->set_value('estprice')),
												'time' => $time
												);
							$viewdata = array (
									   'Telephone' =>  $this->form_validation->set_value('Telephone'),
									   'Email' =>  $this->form_validation->set_value('Email'),
									   'FirstName' =>  $this->form_validation->set_value('FirstName'),
									   'LastName' =>  $this->form_validation->set_value('LastName'),
									   'brand' => $this->form_validation->set_value('brand'), 
								  	   'model' => $this->form_validation->set_value('model'), 
									   'estprice' => $this->form_validation->set_value('estprice'),
									   'item' => $this->form_validation->set_value('item')
									   );   
							$exists = $this->usc_model->InsertUser($userdata);
							$this->usc_model->InsertOrder($orderdata);

							$this->mysmarty->assign('exists', $exists);

							
							
							
							$this->mysmarty->assign('regdata', $viewdata);	
							$this->mysmarty->assign('uscview', 'ok');
							$this->mysmarty->assign('mailto', TRUE);
							$this->mysmarty->assign('time', FlipDateMail($time));
							
							
							$this->admindata['msg_title'] = 'Copy of your outlet repair details.';
							$this->admindata['msg_date'] = $time;
		
							$this->admindata['msg_body'] = $this->mysmarty->fetch('usc/usc_main.html');
							GoMail ($this->admindata, $orderdata['email']);		
							$this->mysmarty->assign('mailto', FALSE);
							$this->mysmarty->view('usc/usc_main.html');
						}
	}

function _captcha_check($str = '')
	{		
		$captcha = $this->Captcha_model->CheckCaptcha($this->input->xss_clean(htmlentities($str, ENT_QUOTES, 'UTF-8')));
		
		if (!$captcha)
		{
			$this->form_validation->set_message('captcha_check', 'Invalid security code');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
function _check_useremail_exists($str = '')
	{
		$checkemail = $this->usc_model->CheckEmailExists($this->input->xss_clean(htmlentities($str, ENT_QUOTES, 'UTF-8')));
		if ($checkemail)
		{	
			$this->form_validation->set_message('_check_useremail_exists', 'This e-mail address is already registered. Please <a href="'.base_url().'Login/Existing/'.$this->input->xss_clean(htmlentities($str, ENT_QUOTES, 'UTF-8')).'">log in here.</a>');
			return FALSE;
		}
		else
		{				
			return TRUE;
		}
	}
}
