<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mygoogle extends Controller {

function Mydev()
	{
		parent::Controller();	
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);		
	}
	
function index()
	{	
		require_once $this->config->config['gpath'].'Google_Client.php';
		require_once $this->config->config['gpath'].'contrib/Google_DriveService.php';
		
		$client = new Google_Client();
		// Get your credentials from the APIs Console
		$client->setClientId('897031851175-fvmjm1839mdch6l529oiamcjvk0b02ih.apps.googleusercontent.com');
		$client->setClientSecret('guk1Q45ONf2QrUZM04CJ_CYL');
		$client->setRedirectUri('http://www.la-tronics.com/r.php');
		$client->setScopes(array('https://www.googleapis.com/auth/drive'));
		
		$service = new Google_DriveService($client);
		
		$authUrl = $client->createAuthUrl();
		
		//Request authorization
		print '<a href="'.$authUrl.'">AUTHORIZE</a>';
		
		if (isset($this->session->userdata['gcode'])) $authCode = $this->session->userdata['gcode'];
		elseif ($this->code) $authCode = $this->code;
		else exit();
		
		//$authCode = trim(fgets(STDIN));
		// Exchange authorization code for access token
		if (isset($this->session->userdata['accessToken'])) $accessToken = $this->session->userdata['accessToken'];
		else 
		{ 
			$accessToken = $client->authenticate($authCode);
			$this->session->set_userdata('accessToken', $accessToken);	
		}
		$client->setAccessToken($accessToken);
		
		//Insert a file
		$file = new Google_DriveFile();
		$file->setTitle('My document');
		$file->setDescription('A test document');
		$file->setMimeType('text/plain');
		
		$data = file_get_contents('document.txt');
		
		$createdFile = $service->files->insert($file, array(
			  'data' => $data,
			  'mimeType' => 'text/plain',
			));
		
		print_r($createdFile);
			}
	
function code($code = '')
	{	
		$code = $this->input->xss_clean($code);
		$code = str_replace('-SL1SH-', '/', $code);
		$code = str_replace('-UN2DS-', '_', $code);
		$code = str_replace('-DO3T-', '.', $code);		
		$this->code = addslashes($code);
		$this->session->set_userdata('gcode', $this->code);
		var_dump($this->code);
		$this->index();
	}
function test()
	{
		echo $this->session->userdata['gcode'];
	}

}
