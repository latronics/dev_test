<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myusers extends Controller {

function Myusers()
	{
		parent::Controller();		
		$this->load->model('Myusers_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->Auth_model->CheckRole();
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}		
		if ($this->session->userdata['admin_id'] == 9) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Users');
		
	}
	
function index()
	{

        $page_checker = $this->uri->segment(1);
        $this->mysmarty->assign('page_checker', $page_checker);
		$this->mysmarty->assign('users', $this->Myusers_model->GetAllUsers());		
		$this->mysmarty->view('myusers/myusers_main.html');
	}

function SortUsers($params = '')
	{
		$this->mysmarty->assign('users', $this->Myusers_model->GetAllUsers($params));		
		$this->mysmarty->view('myusers/myusers_main.html');
	}
	
function ShowUser($userid = '')
	{	
		$this->id = (int)$userid;
		$this->mysmarty->assign('update', TRUE);	
		if ($this->id > 0) {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('FirstName', 'First Name', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('Email', 'E-Mail', 'trim|required|valid_email|xss_clean');
		
		$this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('Mobile', 'Mobile', 'trim|min_length[3]|xss_clean');
		
		$this->form_validation->set_rules('Address', 'Address', 'trim|required|min_length[5]|xss_clean');
		$this->form_validation->set_rules('City', 'City', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('PostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('State', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('Country', 'Country', 'trim|required|min_length[3]|xss_clean');
		
		if (!isset($_POST['same'])) {
			
		$this->form_validation->set_rules('dAddress', 'Address', 'trim|required|min_length[5]|xss_clean');
		$this->form_validation->set_rules('dCity', 'City', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('dPostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('dState', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dCountry', 'Country', 'trim|required|min_length[3]|xss_clean');
		
		}
 
		
				$this->regdata = array(										
									    'Email' => $this->input->post('Email', TRUE),
										   'FirstName' => $this->input->post('FirstName', TRUE),
										   'LastName' => $this->input->post('LastName', TRUE),						  										   
										   'Telephone' => $this->input->post('Telephone', TRUE),
										   'Mobile' => $this->input->post('Mobile', TRUE),
											   'Address' => $this->input->post('Address', TRUE),									
											    'City' => $this->input->post('City', TRUE),
											    'PostCode' => $this->input->post('PostCode', TRUE),
				 							    'State' => $this->input->post('State', TRUE),
		 									    'Country' => $this->input->post('Country', TRUE),												
												'dAddress' => $this->input->post('dAddress', TRUE),									
											    'dCity' => $this->input->post('dCity', TRUE),
											    'dPostCode' => $this->input->post('dPostCode', TRUE),
				 							    'dState' => $this->input->post('dState', TRUE),
		 									    'dCountry' => $this->input->post('dCountry', TRUE),
												'same' => $this->input->post('same', TRUE)
										
									    );
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->userdanni = $this->Myusers_model->GetUserAll($this->id);
				if (count($_POST) > 0) 
				{
					$this->mysmarty->assign('regdata', $this->regdata);
				}
				else
				{
				
					if (!isset($this->userdanni['dAddress']) &&	!isset($this->userdanni['dCity']) && !isset($this->userdanni['dPostCode']) && !isset($this->userdanni['dState']) && !isset($this->userdanni['dCountry'])) $this->userdanni['same'] = 1;
					$this->mysmarty->assign('regdata', $this->userdanni);
				}
				$this->mysmarty->assign('ctr', ReturnCountries());
				$this->mysmarty->assign('sts', ReturnStates());
				$this->mysmarty->assign('showuser', $this->userdanni);	
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myusers/myusers_edituser.html');
				exit();
			}
			else 
			{			
						$this->details_data = array(											
											'Telephone' => $this->form_validation->set_value('Telephone'),	
											'Mobile' => $this->form_validation->set_value('Mobile')											 
											 );
						
						$this->reg_data = array(												 
											 'fname' => $this->form_validation->set_value('FirstName'),
											 'lname' => $this->form_validation->set_value('LastName'),
											 'email' => $this->form_validation->set_value('Email'),
											 'discount' => (float)$this->form_validation->set_value('discount'),
											 'details' => serialize($this->details_data)
											 );
						$this->load->model('Start_model');
						$this->Start_model->UpdateUser((int)$this->id,$this->reg_data);
						
						$this->addressdata = array ('user_id' => (int)$this->id,
													'ua_type' => 'b',
													'Address' => $this->form_validation->set_value('Address'),
													'City' => $this->form_validation->set_value('City'),
													'PostCode' => $this->form_validation->set_value('PostCode'),
													'Country' => $this->form_validation->set_value('Country'),
													'State' => $this->form_validation->set_value('State'),
													);
						
						$this->Start_model->UpdateUserAddress((int)$this->id,$this->addressdata);
						
						if (!isset($_POST['same']) || $_POST['same'] != '1') {
							
						$this->daddressdata = array (
													 'user_id' => (int)$this->id,
													'ua_type' => 'd',
													'Address' => $this->form_validation->set_value('dAddress'),
													'City' => $this->form_validation->set_value('dCity'),
													'PostCode' => $this->form_validation->set_value('dPostCode'),
													'Country' => $this->form_validation->set_value('dCountry'),
													'State' => $this->form_validation->set_value('dState'),
													);	
						$this->load->model('Login_model');
						$this->Start_model->SameDeliveryAddress((int)$this->id);
						$this->Login_model->InsertUserAddress($this->daddressdata);	
						}
						else
						{
						$this->Start_model->SameDeliveryAddress((int)$this->id);
						}
					
						
			redirect("/Myusers"); exit();								
			}
	}
	else {
		redirect("/Myusers");
	}
}

function AddUser ()
	{	$this->mysmarty->assign('ctr', ReturnCountries('en'));
		$this->mysmarty->assign('sts', ReturnStates());
		$this->load->library('form_validation');
		
		$this->form_validation->set_message('matches', 'The Passwords do not mach!');
		$this->form_validation->set_rules('Password', 'Password', 'trim|required|min_length[5]|xss_clean|md5|md5');
		$this->form_validation->set_rules('PasswordRetype', 'Re-type Password', 'trim|required|min_length[5]|matches[Password]|xss_clean');
		
		$this->form_validation->set_rules('Email', 'Email', 'trim|required|valid_email|xss_clean');
		$this->form_validation->set_rules('FirstName', 'First Name', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[2]|xss_clean');

		$this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('Mobile', 'Mobile', 'trim|min_length[3]|xss_clean');
		
		$this->form_validation->set_rules('Address', 'Address', 'trim|required|min_length[5]|xss_clean');
		$this->form_validation->set_rules('City', 'City', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('PostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('State', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('Country', 'Country', 'trim|required|min_length[3]|xss_clean');
		
		if (!isset($_POST['same'])) {
			
		$this->form_validation->set_rules('dAddress', 'Address', 'trim|required|min_length[5]|xss_clean');
		$this->form_validation->set_rules('dCity', 'City', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('dPostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
		$this->form_validation->set_rules('dState', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dCountry', 'Country', 'trim|required|min_length[3]|xss_clean');
		
		}
	
		$this->regdata = array(										
									       'Password' => $this->input->post('Password', TRUE),
										   'PasswordRetype' => $this->input->post('PasswordRetype', TRUE),										   
										   'Email' => $this->input->post('Email', TRUE),
										   'FirstName' => $this->input->post('FirstName', TRUE),
										   'LastName' => $this->input->post('LastName', TRUE),						  										   
										   'Telephone' => $this->input->post('Telephone', TRUE),
										   'Mobile' => $this->input->post('Mobile', TRUE),
											   'Address' => $this->input->post('Address', TRUE),									
											    'City' => $this->input->post('City', TRUE),
											    'PostCode' => $this->input->post('PostCode', TRUE),
				 							    'State' => $this->input->post('State', TRUE),
		 									    'Country' => $this->input->post('Country', TRUE),												
												'dAddress' => $this->input->post('dAddress', TRUE),									
											    'dCity' => $this->input->post('dCity', TRUE),
											    'dPostCode' => $this->input->post('dPostCode', TRUE),
				 							    'dState' => $this->input->post('dState', TRUE),
		 									    'dCountry' => $this->input->post('dCountry', TRUE),
												'same' => $this->input->post('same', TRUE)
								);
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->mysmarty->assign('regdata', $this->regdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myusers/myusers_adduser.html');
				exit();
			}
		else 
			{
			$this->load->model('Login_model');
			//$this->checkusername = $this->Login_model->CheckUsernameExists($this->form_validation->set_value('name'));
			$this->checkemail = $this->Login_model->CheckEmailExists($this->form_validation->set_value('Email'));
					if (!$this->checkemail) 
							{
								$this->details_data = array(											
										    'Telephone' => $this->form_validation->set_value('Telephone'),	
										    'Mobile' => $this->form_validation->set_value('Mobile')
											 
											 );
														
								$this->reg_data = array(													 
													 'pass' => $this->form_validation->set_value('Password'),
													 'fname' => $this->form_validation->set_value('FirstName'),
													 'lname' => $this->form_validation->set_value('LastName'),
													 'email' => $this->form_validation->set_value('Email'),
													 'active' => 1,
													 'reg_date' => CurrentTime(),
													 'discount' => (float)$this->form_validation->set_value('discount'),
													 'details' => serialize($this->details_data)
													 );
								
								$this->userid = $this->Login_model->InsertUser($this->reg_data);
								
										$this->addressdata = array ('user_id' => (int)$this->userid,
																	'ua_type' => 'b',
																	'Address' => $this->form_validation->set_value('Address'),
																	'City' => $this->form_validation->set_value('City'),
																	'PostCode' => $this->form_validation->set_value('PostCode'),
																	'Country' => $this->form_validation->set_value('Country'),
																	'State' => $this->form_validation->set_value('State'),
																	);
										
										$this->Login_model->InsertUserAddress($this->addressdata);
										
										if (!isset($_POST['same'])) {
											
										$this->daddressdata = array (
																	 'user_id' => (int)$this->userid,
																	'ua_type' => 'd',
																	'Address' => $this->form_validation->set_value('dAddress'),
																	'City' => $this->form_validation->set_value('dCity'),
																	'PostCode' => $this->form_validation->set_value('dPostCode'),
																	'Country' => $this->form_validation->set_value('dCountry'),
																	'State' => $this->form_validation->set_value('dState'),
																	);							
										$this->Login_model->InsertUserAddress($this->daddressdata);
										}
								redirect("/Myusers"); exit();
							}
					else	
							{					
								//if ($this->checkusername) $this->existserrors['usernameexists'] = 'The username is taken';
								if ($this->checkemail) $this->existserrors['emailexists'] = 'The e-mail address is already registerd';
								$this->mysmarty->assign('regdata', $this->regdata);
								$this->mysmarty->assign('errors', $this->existserrors);
								$this->mysmarty->view('myusers/myusers_adduser.html');
								exit();
							}	
				
			}
}

function DeActivate ($userid = '')
	{	
		$this->id = (int)$userid;
		if ($this->id > 0) { 
		$this->Myusers_model->DeactivateUser($this->id);
		$this->userdetails = $this->Myusers_model->GetUser($this->id);
		$this->_InformUserDeActivated($this->userdetails);
		}
		Redirect("/Myusers");
	}

function Activate ($userid = '')
	{	
		$this->id = (int)$userid;
		if ($this->id > 0) 
		{ 
		$this->Myusers_model->ActivateUser($this->id);
		$this->userdetails = $this->Myusers_model->GetUser($this->id);
		$this->_InformUserActivated($this->userdetails);
		}
		Redirect("/Myusers");
	}

function DeleteUser ($userid = '')
	{
		$this->id = (int)$userid;
		if ($this->id > 0) 
			{
			$this->check = $this->Myusers_model->CheckUserExists($this->id);
			if ((int)$this->check > 0) $this->Myusers_model->DeleteUser($this->id);
			}
		Redirect("/Myusers");
	}
	
function _InformUserActivated ($userdata = '') {

						$this->msg_data = array ('msg_title' => 'Account activated @ '.FlipDateMail(CurrentTime()),
											'msg_body' => 'Your account has been activated. You may now login.<br><br>
											 <a href="'.base_url().'Login/">Login here</a><br><br>
											 Thank You.								
											',
											'msg_date' => CurrentTime()
											);	
												
						GoMail($this->msg_data, $userdata['email']);
	}
	
function _InformUserDeActivated ($userdata = '') {

						$this->msg_data = array ('msg_title' => 'Account deactivation @ '.FlipDateMail(CurrentTime()),
											'msg_body' => 'Your account has been deactivated. <br><br>
											Please contact the administrator regarding you account<br><br>
											 Thank You.								
											',
											'msg_date' => CurrentTime()
											);	
					
						GoMail($this->msg_data, $userdata['email']);
	}
}