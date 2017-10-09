<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myadmin extends Controller {

	function Myadmin()
	{
		parent::Controller();
		$this->load->model('Myadmin_model');
		$this->load->model('Captcha_model');
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('nobg', TRUE);
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
	}
	
	function index()
	{
		$sess = $this->session->userdata;
		$ip = $this->input->ip_address();
		$check = array('admin_id','type','name','ownnames','email','active','warehouse','listings','orders','accounting','lastip', 'auclimit');
		$active = TRUE;
		foreach ($check as $c)
		{
			if (!isset($sess[$c])) $active = false;
		}		
		if ($active)
		{
			$this->db->select('active, lastip');
			$this->db->where('admin_id', (int)$sess['admin_id']);
			$adm = $this->db->get('administrators');
			if ($adm->num_rows == 1)
			{
				 $admin = $adm->row_array();

				 if ($admin['active'] == 1 && $admin['lastip'] == $ip) Redirect('Mywarehouse');
			}
		}
		$this->session->sess_destroy();
		//$this->Captcha_model->DoCaptcha();
		$this->mysmarty->view('myadmin/myadmin_main.html');	
		
	}

//////////


	function CheckIn() {

				//$this->_CheckFromAdmin();
				
			   	$this->load->library('form_validation');
					   			
				$this->form_validation->set_rules('Username', 'Username', 'trim|required|max_length[15]|xss_clean');
				$this->form_validation->set_rules('Password', 'Password', 'trim|required|min_length[5]|xss_clean|md5|md5');


				if ($this->form_validation->run() == FALSE)
				{	
					//$this->Captcha_model->DoCaptcha();			
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					$this->mysmarty->view('myadmin/myadmin_main.html');
				exit();
				}
				else 
				{
					//$this->captcha = $this->Captcha_model->CheckCaptcha();
		
					//if ($this->captcha)
						//{
						$userdata = $this->Myadmin_model->CheckUser($this->form_validation->set_value('Username'), $this->form_validation->set_value('Password'));
						if ($userdata) {
									if ($userdata['active'] != 1) {
										$this->Captcha_model->DoCaptcha();
										$this->mysmarty->assign('error', 'Account has been de-activated. Please contact support');
										$this->mysmarty->view('myadmin/myadmin_main.html');
									}
									else 
									{	
										$userdata['lastip'] = $this->input->ip_address();
										$this->session->set_userdata($userdata);							
										$this->db->update('administrators', array('lastip' =>$userdata['lastip']), array('admin_id' => (int)$userdata['admin_id']));
										if ($userdata['admin_id'] > 1)
										{ 
											$this->Myadmin_model->RecordLogin((int)$userdata['admin_id'],$userdata['datein'])  ;							
											
										}
									$this->session->set_flashdata('success_msg', 'Welcome '.$userdata['ownnames']);
									redirect('Mywarehouse');
									exit();
									}
			
							}
							else 
							{	
								$this->Captcha_model->DoCaptcha();
								$this->mysmarty->assign('error', 'Wrong username or password');
								$this->mysmarty->view('myadmin/myadmin_main.html');	
							}
						//}
						//else 
						//{
						//	$this->Captcha_model->DoCaptcha();
						//	$this->mysmarty->assign('error', 'Please verify you are human');
						//	$this->mysmarty->view('myadmin/myadmin_main.html');							
						//}
			}	
	}


//////////


	function CheckOut()
	{	
		/*
		    [user_id] => 1
    		[type] => visitor
		    [name] => mitko
		    [ownnames] => Dimiter Roussev
		    [email] => mr.reece@gmail.com
		    [active] => 1
		    [reg_date] => 2009-02-06 03:54:03
		    [datein] => 2009-02-07 05:23:02
		*/
	
		if (isset($this->session->userdata['admin_id'])) $this->Myadmin_model->RecordLogout ((int)$this->session->userdata['admin_id'], $this->session->userdata['datein']);
		$this->session->sess_destroy();
		redirect("/Myadmin");
		exit();
	}

//////////

	function ForgotPassword() 
	{
		if (count($_POST) == 0)
		{
		$this->Captcha_model->DoCaptcha();
		$this->mysmarty->view('myadmin/myadmin_forgotpass.html');	
		}
		else 
		{		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('Email', 'E-mail', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('Code', 'Verification', 'trim|required|min_length[3]|xss_clean');

		$this->captcha = $this->Captcha_model->CheckCaptcha($this->input->post('Code', TRUE));
		
		if (($this->form_validation->run() == FALSE) || !$this->captcha)
			{	
				if (!$this->captcha) $this->mysmarty->assign('errorcaptcha', 'Invalid Security Code');
				$this->Captcha_model->DoCaptcha();
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myadmin/myadmin_forgotpass.html');
				exit();

			}
			else 
			{	
				$this->userdata = $this->Myadmin_model->HandleNewUserPassword($this->form_validation->set_value('Email'));
				if ($this->userdata) {
					$this->load->helper('mailmsg');
					MailNewAdminPassword($this->userdata);
					$this->historydata = MailNewAdminPasswordAdmin($this->userdata);
					$this->Myadmin_model->InsertHistoryData($this->historydata);
					$this->mysmarty->assign('okmsg', 'Success. Please check your email.');	
				}
				else {
					$errors['Email'] = 'No user with this email address';
					$this->mysmarty->assign('errors', $errors);
				}
				$this->mysmarty->view('myadmin/myadmin_forgotpass.html');	
			}
	}
}


//////////

function _CheckFromAdmin()    
		{				
						$this->load->library('user_agent');
						$referer = $this->agent->referrer();
						$oururl = $this->config->config['base_url'];
						
						str_replace(strtolower($oururl), "", strtolower($referer), $count);

						if ((int)$count == 0)
						{						
						
						$msg_data['msg_title'] = 'Myadmin outside login attempt';
						$msg_data['msg_body'] = '
						IP Address: '.$this->input->ip_address().'<br>
						Referer: '.$referer.'<br>
						User Agent: '.$this->agent->agent_string().'<br>
						Platform: '.$this->agent->platform().'<Br>
						Date: '.CurrentTime().'						
						';
						$msg_data['msg_date'] = CurrentTime();				
						
						GoMail($msg_data);		
						exit(); 
					
						}
		}
}
