<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ($_SERVER['REMOTE_ADDR'] != '93.152.154.46') exit();

	
class Unserialize extends Controller {

	function Unserialize()
	{
		parent::Controller();	
	}
	
	function index()
	{
	$serial  ='a:37:{s:8:"mc_gross";s:5:"14.89";s:22:"protection_eligibility";s:8:"Eligible";s:14:"address_status";s:9:"confirmed";s:8:"payer_id";s:13:"F6KJWGSEWPC7J";s:3:"tax";s:4:"0.00";s:14:"address_street";s:18:"1234 Margaret Ave.";s:12:"payment_date";s:25:"15:07:13 Mar 08, 2010 PST";s:14:"payment_status";s:9:"Completed";s:7:"charset";s:12:"windows-1252";s:11:"address_zip";s:5:"96150";s:10:"first_name";s:7:"Richard";s:6:"mc_fee";s:4:"0.73";s:20:"address_country_code";s:2:"US";s:12:"address_name";s:12:"Richard Dart";s:6:"custom";s:0:"";s:12:"payer_status";s:8:"verified";s:8:"business";s:26:"paypal@365laptoprepair.com";s:15:"address_country";s:13:"United States";s:12:"address_city";s:16:"South Lake Tahoe";s:8:"quantity";s:1:"1";s:11:"payer_email";s:26:"dartandassoc@sbcglobal.net";s:6:"txn_id";s:17:"12645297AP0068522";s:12:"payment_type";s:7:"instant";s:9:"last_name";s:4:"Dart";s:13:"address_state";s:2:"CA";s:14:"receiver_email";s:26:"paypal@365laptoprepair.com";s:11:"payment_fee";s:4:"0.73";s:11:"receiver_id";s:13:"RH4XWM4WKM7LW";s:8:"txn_type";s:10:"web_accept";s:9:"item_name";s:26:"Laptop repair order 100262";s:11:"mc_currency";s:3:"USD";s:11:"item_number";s:6:"100262";s:17:"residence_country";s:2:"US";s:15:"handling_amount";s:4:"0.00";s:19:"transaction_subject";s:26:"Laptop repair order 100262";s:13:"payment_gross";s:5:"14.89";s:8:"shipping";s:4:"0.00";}';
	$serial = '';
	$serial = unserialize($serial);
	printcool ($serial);
	}
	function Email()
	{
	
		$this->msg_data = array ('msg_title' => 'Test e-mail from '.$this->config->config['sitename'],
											'msg_body' => 'Test e-mail from '.$this->config->config['sitename'],
											'msg_date' => CurrentTime()
											);			
		
		GoMail($this->msg_data, 'drusev82@yahoo.com', '', 1);
		GoMail($this->msg_data, 'drusev82@hotmail.com', '', 1);
		GoMail($this->msg_data, 'reece@abv.bg', '', 1);
		GoMail($this->msg_data, 'mr.reece@gmail.com', '', 1);
		GoMail($this->msg_data, 'info@1websolutions.net', '', 1);
		
		$this->msg_data = array ('msg_title' => 'Проба кирилица емейл от '.$this->config->config['sitename'],
											'msg_body' => 'Проба кирилица емейл от '.$this->config->config['sitename'],
											'msg_date' => CurrentTime()
											);			
		
		GoMail($this->msg_data, 'drusev82@yahoo.com', '', 1);
		GoMail($this->msg_data, 'drusev82@hotmail.com', '', 1);
		GoMail($this->msg_data, 'reece@abv.bg', '', 1);
		GoMail($this->msg_data, 'mr.reece@gmail.com', '', 1);
		GoMail($this->msg_data, 'info@1websolutions.net', '', 1);
		
		echo 'All is sent';
		$this->email->print_debugger();
	}

}
