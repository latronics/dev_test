<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//$this->_CheckFromUser();
					$this->load->model('Captcha_model');	
	   				$this->load->library('form_validation');

					if ($this->config->config['language_abbr'] == 'en') $this->form_validation->set_message('matches', 'The Passwords do not mach!');
					else $this->form_validation->set_message('matches', 'Паролите не съвпадат');
					
					$this->form_validation->set_rules('Password', $this->fieldnames[$this->config->config['language_abbr']]['Password'], 'trim|required|min_length[5]|xss_clean|md5|md5');
					$this->form_validation->set_rules('PasswordRetype', $this->fieldnames[$this->config->config['language_abbr']]['RetypePass'], 'trim|required|min_length[5]|matches[Password]|xss_clean');
					$this->form_validation->set_rules('Email', $this->fieldnames[$this->config->config['language_abbr']]['Email'], 'trim|required|valid_email|xss_clean');
					
					$this->form_validation->set_rules('FirstName', $this->fieldnames[$this->config->config['language_abbr']]['FirstName'], 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('LastName', $this->fieldnames[$this->config->config['language_abbr']]['LastName'], 'trim|required|min_length[2]|xss_clean');
					
					$this->form_validation->set_rules('Telephone', $this->fieldnames[$this->config->config['language_abbr']]['Telephone'], 'trim|required|xss_clean|numeric|max_length[50]');
					$this->form_validation->set_rules('Mobile', $this->fieldnames[$this->config->config['language_abbr']]['Mobile'], 'trim|xss_clean|numeric|max_length[50]');
					
					
					$this->form_validation->set_rules('Address', $this->fieldnames[$this->config->config['language_abbr']]['Address'], 'trim|required|xss_clean|min_length[10]');
					
					$this->form_validation->set_rules('City', $this->fieldnames[$this->config->config['language_abbr']]['City'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('PostCode', $this->fieldnames[$this->config->config['language_abbr']]['PostCode'], 'trim|required|xss_clean|max_length[20]');
					$this->form_validation->set_rules('Country', $this->fieldnames[$this->config->config['language_abbr']]['Country'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('State', $this->fieldnames[$this->config->config['language_abbr']]['State'], 'trim|required|xss_clean|max_length[50]');
					
					if (!isset($_POST['same'])) {
									 
					$this->form_validation->set_rules('dAddress', $this->fieldnames[$this->config->config['language_abbr']]['Address'], 'trim|required|xss_clean|min_length[10]');
					
					$this->form_validation->set_rules('dCity', $this->fieldnames[$this->config->config['language_abbr']]['City'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('dPostCode', $this->fieldnames[$this->config->config['language_abbr']]['PostCode'], 'trim|required|xss_clean|max_length[20]');
					$this->form_validation->set_rules('dCountry', $this->fieldnames[$this->config->config['language_abbr']]['Country'], 'trim|required|xss_clean|max_length[50]');
					$this->form_validation->set_rules('dState', $this->fieldnames[$this->config->config['language_abbr']]['State'], 'trim|required|xss_clean|max_length[50]');	
									 
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
		

				$this->captcha = $this->Captcha_model->CheckCaptcha();
	
		if (($this->form_validation->run() == FALSE) || !$this->captcha)
			{	
				$this->Captcha_model->DoCaptcha();
				$this->mysmarty->assign('regdata', $this->regdata);								
				if (!$this->captcha) $this->mysmarty->assign('errorcaptcha', $this->messages[$this->config->config['language_abbr']]['inv-code']);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);

								$this->mysmarty->assign('innerview', 'loginregister');
								$this->mysmarty->view('welcome/welcome_main.html');
				exit();

			}
			else 
			{					
						$this->checkemail = $this->Login_model->CheckEmailExists($this->form_validation->set_value('Email'));
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
														 'pass' => $this->form_validation->set_value('Password'),
														 'fname' => $this->form_validation->set_value('FirstName'),
														 'lname' => $this->form_validation->set_value('LastName'),
														 'email' => $this->form_validation->set_value('Email'),
														 'confirm_code' => $this->confirm_code,
														 'reg_date' => CurrentTime(),
														 'details' => serialize($this->compileddetails)															
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
									
									if (!isset($_POST['same'])) {
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
										
										MailUserConfirmRegistration($this->userid, $this->reg_data['email'] ,$this->confirm_code, $this->config->config['language_abbr']);
										
										$this->history_data = MailUnconfirmedRegistationToAdmin($this->reg_data,$this->accessdata);
										$this->Login_model->InsertHistoryData($this->history_data);
										
									Redirect("/Login/WaitingConfirmation"); exit();
									}
								else
									{	
										$this->Captcha_model->DoCaptcha();
										//if ($this->checkusername) $this->existserrors['usernameexists'] = $this->messages[$this->config->config['language_abbr']]['tknuser'];
										if ($this->checkemail) $this->existserrors['emailexists'] = $this->messages[$this->config->config['language_abbr']]['emlreg'];
										$this->mysmarty->assign('regdata', $this->regdata);
										$this->mysmarty->assign('errors', $this->existserrors);
										
														$this->mysmarty->assign('innerview', 'loginregister');
														$this->mysmarty->view('welcome/welcome_main.html');
										exit();									
									}
			}	



