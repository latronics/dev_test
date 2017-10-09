<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Controller {

	function Login()
	{
		parent::Controller();
		$this->load->model('Menus_model');	
		$this->load->model('Product_model');
		$this->load->model('Login_model');
		$this->Menus_model->GetStructure();		
		$this->Product_model->GetStructure('top');
		
		
		$this->mysmarty->assign('tmactive', 'login');
		$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
		$this->mysmarty->assign('sts', ReturnStates());
			if (isset($this->session->userdata['user_id'])) {
		$this->load->model('Start_model');
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyUser();	
		$this->mysmarty->assign('session',$this->session->userdata);
			
		}
		
		$this->fieldnames = array (
							   'bg' => array (
											  'Code' => 'Код',
											  'Username' => 'Потребителско име',
												'Password' => 'Желана парола',
												'Verification' => 'Код',
												'RetypePass' => 'Парола отново',
												'Email' => 'Е-мейл',
												'FirstName' => 'Собствено име',
												'LastName' => 'Фамилно име',
												'City' => 'Град',
												'PostCode' => 'Пощенски Код',
												'State' => 'Щат',
												'Country' => 'Държава',
												'Telephone' => 'Телефон',
												'Mobile' => 'Мобилен телефон',
												'Address' => 'Адрес'
											  ),
							   'en' => array (
											  'Code' => 'Verification',
											    'Username' => 'Username',
												'Password' => 'Password',
												'Verification' => 'Verification',
												'RetypePass' => 'Re-type Password',
												'Email' => 'E-mail',
												'FirstName' => 'First Name',
												'LastName' => 'Last Name',
												'City' => 'City',
												'PostCode' => 'PostCode',
												'Country' => 'Country',
												'State' => 'State',
												'Telephone' => 'Telephone',
												'Mobile' => 'Mobile',
												'Address' => 'Address'
											  )
							   );
		
		$this->messages = array (
							   'bg' => array (
											  'inv-code' => 'Невалиден код',
											  'accdeact' => 'Този акаунт е спрян. Моля, сръвжете се с администратора',
											  'wrusrpss' => 'Грешен е-мейл или парола',
											  'tknuser' => 'Това потребителско име е заето',
											  'emlreg' => 'Този Е-мейл адрес е регистратриран',
											  'success' => 'Усшено. Моля, проверете пощата си.',
											  'nouserwithemail' => 'Няма потребители с този е-мейл адрес'
											  
											  ),
							   'en' => array (
											  'inv-code' => 'Please specify if you are human',
											  'accdeact' => 'Account has been de-activated. Please contact the administator',
											  'wrusrpss' => 'Wrong e-mail or password',
											  'tknuser' => 'The username is taken',
											  'emlreg' => 'The e-mail address is already registered',
											  'success' => 'Success. Please check your email inbox AND YOUR SPAM BOX',
											  'nouserwithemail' => 'No users with this e-mail address'
											  )
							   );
		
	}
	
	function index()
	{	
	// LOAD SESSION
		if (isset($this->session->userdata['user_id'])) {
			echo 'You are already logged in<br><br>';
			}
		else{
		$this->mysmarty->assign('innerview', 'login');
		$returnhtml = $this->mysmarty->fetch('login/login_top.html');
	 	echo $returnhtml;}
		}

function GetBox()
{
	
echo '

';

}

function CheckIn() 
	{

				//$this->_CheckFromUser();

			   	$this->load->library('form_validation');
					   			
				$this->form_validation->set_rules('Email', $this->fieldnames[$this->config->config['language_abbr']]['Email'], 'trim|required|valid_email|xss_clean');
				$this->form_validation->set_rules('Password', $this->fieldnames[$this->config->config['language_abbr']]['Password'], 'trim|required|min_length[4]|xss_clean|md5|md5');
				//$this->form_validation->set_rules('Code', $this->fieldnames[$this->config->config['language_abbr']]['Verification'], 'trim|required|min_length[3]|xss_clean');
				
				//$this->captcha = $this->Captcha_model->CheckCaptcha($this->input->post('Code', TRUE));

				if ($this->form_validation->run() == FALSE)
				{	
					
					//if (!$this->captcha) $this->mysmarty->assign('errorcaptcha', $this->messages[$this->config->config['language_abbr']]['inv-code']);
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
			
			
					$this->mysmarty->assign('innerview', 'login');
					$returnhtml = $this->mysmarty->fetch('login/login_top.html');
	 				echo $returnhtml;
					
				  exit();
				}
				else 
				{
						$userdata = $this->Login_model->CheckUser(strtolower($this->form_validation->set_value('Email')), $this->form_validation->set_value('Password'));
						if ($userdata) {
									if ($userdata['active'] != 1) {
										//$this->Captcha_model->DoCaptcha();
										$this->mysmarty->assign('error', $this->messages[$this->config->config['language_abbr']]['accdeact']);
										
												$this->mysmarty->assign('innerview', 'login');
												$returnhtml = $this->mysmarty->fetch('login/login_top.html');
echo $returnhtml;
										
									}
									else 
									{	
										//$this->Captcha_model->DeleteOldCaptchas();
										
										if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '') ) 
										{
										$userdata['cart'] = $this->session->userdata['cart'];
										}
										$this->session->set_userdata($userdata);
										
										$this->Login_model->RecordLogin((int)$userdata['user_id'],$userdata['datein'])  ;							
										//$this->config->set_item('language_abbr', $userdata['lang']);		
									
									/*
									$this->ref = $this->agent->referrer();
									if (($this->ref != '') && ($this->ref != site_url().'Login/CheckIn')) header('Location: '.$this->ref);
									else Redirect('');
									*/
									echo '<img src="/images/loader.gif" ><Br><br>LOGGING IN... Please wait.';
									echo '
									
									<script type="text/javascript">
									<!--
									window.location = "'.$this->config->config['base_url'].'/My"
									//-->
									</script>
									
									';
									//Redirect($this->config->config['base_url']);
									exit();
									}
							}
							else 
							{	
								//$this->Captcha_model->DoCaptcha();
								$this->mysmarty->assign('error', $this->messages[$this->config->config['language_abbr']]['wrusrpss']);
								
										$this->mysmarty->assign('innerview', 'login');
										$returnhtml = $this->mysmarty->fetch('login/login_top.html');
echo $returnhtml;
								
							}
			}	
	}



function CheckOut()
	{	
		if (isset($this->session->userdata['user_id'])) $this->Login_model->RecordLogout ((int)$this->session->userdata['user_id'], $this->session->userdata['datein']);
		$this->session->sess_destroy();
		$this->load->library('user_agent');
		//$this->ref = $this->agent->referrer();
		//if ($this->ref != '') header('Location: '.$this->ref);
		//else
		Redirect('');
		exit();
	}

function Register()
	{	
		$this->load->model('Captcha_model');
		$this->Captcha_model->DoCaptcha();

				$this->mysmarty->assign('innerview', 'loginregister');
				$returnhtml = $this->mysmarty->fetch('login/login_register.html');
				echo $returnhtml;

	}

function CheckRegistration() 
	{
					//$this->_CheckFromUser();
					$this->load->model('Captcha_model');	
	   				$this->load->library('form_validation');

					if ($this->config->config['language_abbr'] == 'en') $this->form_validation->set_message('matches', 'The Passwords do not mach!');
					else $this->form_validation->set_message('matches', 'Паролите не съвпадат');
					
					//$this->form_validation->set_rules('Password', $this->fieldnames[$this->config->config['language_abbr']]['Password'], 'trim|required|min_length[5]|xss_clean|md5|md5');
					//$this->form_validation->set_rules('PasswordRetype', $this->fieldnames[$this->config->config['language_abbr']]['RetypePass'], 'trim|required|min_length[5]|matches[Password]|xss_clean');
					$this->form_validation->set_rules('Email', $this->fieldnames[$this->config->config['language_abbr']]['Email'], 'trim|required|valid_email|xss_clean');
					
					$this->form_validation->set_rules('FirstName', $this->fieldnames[$this->config->config['language_abbr']]['FirstName'], 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('LastName', $this->fieldnames[$this->config->config['language_abbr']]['LastName'], 'trim|required|min_length[2]|xss_clean');
					
					$this->form_validation->set_rules('Telephone', $this->fieldnames[$this->config->config['language_abbr']]['Telephone'], 'trim|required|xss_clean|numeric|max_length[50]');
					$this->form_validation->set_rules('Mobile', $this->fieldnames[$this->config->config['language_abbr']]['Mobile'], 'trim|xss_clean|numeric|max_length[50]');
					
					
					$this->form_validation->set_rules('Address', $this->fieldnames[$this->config->config['language_abbr']]['Address'], 'trim|required|xss_clean|min_length[5]');
					
					$this->form_validation->set_rules('City', $this->fieldnames[$this->config->config['language_abbr']]['City'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('PostCode', $this->fieldnames[$this->config->config['language_abbr']]['PostCode'], 'trim|required|xss_clean|max_length[20]');
					$this->form_validation->set_rules('Country', $this->fieldnames[$this->config->config['language_abbr']]['Country'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('State', $this->fieldnames[$this->config->config['language_abbr']]['State'], 'trim|required|xss_clean|max_length[50]');
					
					if (!isset($_POST['same'])) {
									 
					$this->form_validation->set_rules('dAddress', $this->fieldnames[$this->config->config['language_abbr']]['Address'], 'trim|required|xss_clean|min_length[5]');
					
					$this->form_validation->set_rules('dCity', $this->fieldnames[$this->config->config['language_abbr']]['City'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('dPostCode', $this->fieldnames[$this->config->config['language_abbr']]['PostCode'], 'trim|required|xss_clean|max_length[20]');
					$this->form_validation->set_rules('dCountry', $this->fieldnames[$this->config->config['language_abbr']]['Country'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('dState', $this->fieldnames[$this->config->config['language_abbr']]['State'], 'trim|required|xss_clean|max_length[50]');	
									 
					 }
										
					$this->regdata = array(
										   //'Password' => $this->input->post('Password', TRUE),
										  // 'PasswordRetype' => $this->input->post('PasswordRetype', TRUE),										   
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
		

				$this->captcha = $this->Captcha_model->CheckCaptcha();
	
		if (($this->form_validation->run() == FALSE) || !$this->captcha)
			{	
				$this->Captcha_model->DoCaptcha();
				$this->mysmarty->assign('regdata', $this->regdata);								
				if (!$this->captcha) $this->mysmarty->assign('errorcaptcha', $this->messages[$this->config->config['language_abbr']]['inv-code']);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);

								$this->mysmarty->assign('innerview', 'loginregister');
								$returnhtml = $this->mysmarty->fetch('login/login_register.html');
echo $returnhtml;
				exit();

			}
			else 
			{					
						$this->checkemail = $this->Login_model->CheckEmailExists(strtolower($this->form_validation->set_value('Email')));
							if (!$this->checkemail) 
									{
										$this->load->helper('arithmetic');
										$this->confirm_code = rand_string(50);
										$this->load->library('user_agent');
										$this->accessdata = array(
															 'Reg I.P. Address' => $this->input->ip_address(),
															 'Referer' => $this->agent->referrer(),
															 'User Agent' => $this->agent->agent_string()
															 );
										$this->compileddetails = array(																	   
																	   'Telephone' => $this->form_validation->set_value('Telephone'),
																	   'Mobile' => $this->form_validation->set_value('Mobile')
																	   
																	   );
										
										$this->reg_data = array(														 
														 'pass' => md5(md5($this->form_validation->set_value('Telephone'))),
														 'fname' => $this->form_validation->set_value('FirstName'),
														 'lname' => $this->form_validation->set_value('LastName'),
														 'email' => strtolower($this->form_validation->set_value('Email')),
														 'confirm_code' => $this->confirm_code,
														 'reg_date' => CurrentTime(),
														 'details' => serialize($this->compileddetails)		,
														 'active' => 1
														 );
										
											
																	
							
									$this->userid = $this->Login_model->InsertUser($this->reg_data);
									
									$this->addressdata = array (		'user_id' => $this->userid ,
																		'ua_type' => 'b',
															  		   'Address' => $this->form_validation->set_value('Address'),													
																	   'City' => $this->form_validation->set_value('City'),
																	   'PostCode' => $this->form_validation->set_value('PostCode'),
																	   'State' => $this->form_validation->set_value('State'),
																	   'Country' => $this->form_validation->set_value('Country')
																	);
									
									$this->Login_model->InsertUserAddress($this->addressdata);
									
									if (!isset($_POST['same']) || $_POST['same'] != '1') 
									{
										$this->daddressdata = array (   'user_id' => $this->userid ,
																	 	'ua_type' => 'd',
															  		   'Address' => $this->form_validation->set_value('dAddress'),													
																	   'City' => $this->form_validation->set_value('dCity'),
																	   'PostCode' => $this->form_validation->set_value('dPostCode'),
																	   'State' => $this->form_validation->set_value('dState'),
																	   'Country' => $this->form_validation->set_value('dCountry')
																	);			  
										
										$this->Login_model->InsertUserAddress($this->daddressdata);
													  
									  }
									
									$this->load->helper('mailmsg');
										
										//MailUserConfirmRegistration($this->userid, $this->reg_data['email'] ,$this->confirm_code, $this->config->config['language_abbr']);
					//					
										//$this->history_data = MailUnconfirmedRegistationToAdmin($this->reg_data,$this->accessdata);
										$this->history_data = MailNewUser($this->reg_data, $this->compileddetails);
										
										$this->Login_model->InsertHistoryData($this->history_data);
										
										echo 'Registration Complete. You may login now';
									//Redirect("/Login/WaitingConfirmation"); exit();
									//echo "ok, awaiting confirmation"; exit();
									}
								else
									{	
										$this->Captcha_model->DoCaptcha();
										//if ($this->checkusername) $this->existserrors['usernameexists'] = $this->messages[$this->config->config['language_abbr']]['tknuser'];
										if ($this->checkemail) $this->existserrors['emailexists'] = $this->messages[$this->config->config['language_abbr']]['emlreg'];
										$this->mysmarty->assign('regdata', $this->regdata);
										$this->mysmarty->assign('errors', $this->existserrors);
										
														$this->mysmarty->assign('innerview', 'loginregister');
														$returnhtml = $this->mysmarty->fetch('login/login_register.html');
echo $returnhtml;
										exit();									
									}
			}	
	}

function WaitingConfirmation() 
	{
		$this->mysmarty->assign('status', "preconfirm");
		
					$this->mysmarty->assign('innerview', 'confirmreg');
					$this->mysmarty->view('welcome/welcome_main.html');
	}

function ForgotPassword() 
	{	$this->load->model('Captcha_model');
		if (count($_POST) == 0)
		{
		
		$this->Captcha_model->DoCaptcha();
						$this->mysmarty->assign('innerview', 'forgotpass');
						$returnhtml = $this->mysmarty->fetch('login/login_forgotpass.html');
						echo $returnhtml;
		}
		else
		{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('Email', 'E-mail', 'trim|required|valid_email|xss_clean');

		$this->captcha = $this->Captcha_model->CheckCaptcha();

		if (($this->form_validation->run() == FALSE) || !$this->captcha)
			{	
				if (!$this->captcha) $this->mysmarty->assign('errorcaptcha', $this->messages[$this->config->config['language_abbr']]['inv-code']);
				$this->Captcha_model->DoCaptcha();
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
						$this->mysmarty->assign('innerview', 'forgotpass');
						$returnhtml = $this->mysmarty->fetch('login/login_forgotpass.html');
echo $returnhtml;
				exit();
			}
			else 
			{	
				$this->userdata = $this->Login_model->HandleNewUserPassword(strtolower($this->form_validation->set_value('Email')));
				if ($this->userdata) {
					$this->load->helper('mailmsg');
					MailNewUserPassword($this->userdata, $this->config->config['language_abbr']);
					$this->historydata = MailNewUserPasswordAdmin($this->userdata);
					//$this->Login_model->InsertHistoryData($this->historydata);
					$this->mysmarty->assign('okmsg', $this->messages[$this->config->config['language_abbr']]['success']);	
				}
				else {					
					$errors['Email'] = $this->messages[$this->config->config['language_abbr']]['nouserwithemail'];
					$this->mysmarty->assign('errors', $errors);
				}
				
				$this->Captcha_model->DoCaptcha();
						$this->mysmarty->assign('innerview', 'forgotpass');
						$returnhtml = $this->mysmarty->fetch('login/login_forgotpass.html');
echo $returnhtml;
			}
		}
}

function ConfirmRegistration($confirm_code = '',$id = '') 
	{	
		$this->codelength = strlen($confirm_code);
		switch ($this->codelength) { 
					case 50: 
							$this->user = $this->Login_model->ConfirmRegistration($this->input->xss_clean($confirm_code),(int)$this->input->xss_clean($id));
								if ((int)$this->user['user_id'] > 0) 
								{
									$this->session->set_flashdata('status', "confirmed");
									$this->load->helper('mailmsg');
									$this->history_data = MailConfirmedRegistrationToAdmin($this->user['email'],CurrentTime());
									$this->Login_model->InsertHistoryData($this->history_data);
								}
								else 
								{
								$this->session->set_flashdata('status',  "nouser");
								}
				break;
					case 0:
					$this->session->set_flashdata('status',  "noaccess");
				break;
					default:
					$this->session->set_flashdata('status',  "nouser");

				}
		Redirect("Login/ConfirmationResult");
	}	
	
function ConfirmationResult() 
	{
		$this->mysmarty->assign('status', $this->session->flashdata('status'));	
						$this->mysmarty->assign('innerview', 'confirmreg');
						$this->mysmarty->view('welcome/welcome_main.html');
		
	}

function _CheckFromUser()    
		{				
						$this->urls = array(
											'/Login',
											'/Login/',
											'/Login/CheckIn',
											'/Login/Register',
											'/Login/Register/',
											'/Login/CheckRegistration',											
											);
						$this->load->library('user_agent');
						$referer = $this->agent->referrer();
						$good = 0;
						foreach ($this->urls as $value) {
							if (strtolower($referer) == strtolower($this->config->config['base_url'].$this->config->config['index_page'].$value)) $good++;				
						}
						if ((int)$good == 0) { 
							$deadend_data = array (
											'd_ip' => $this->input->ip_address(),
											'd_ref' => $referer,
											'd_agent' => $this->agent->agent_string(),
											'd_plat' => $this->agent->platform(),
											'd_date' => CurrentTime()
											   );						
							$this->db->insert('dead_ends', $deadend_data);
						Redirect('DeadEnd'); 
						exit(); 							
						}
		}
		
function lowercaseing()
{

		$this->db->select('user_id, email');
		$query = $this->db->get('users');
		printcool ($query->result_array());	
		foreach ($query->result_array() as $q)
		{
			//$this->db->update('users', array('email' => strtolower($q['email'])), array('user_id' => $q['user_id']));	
			
			
		}
}

}