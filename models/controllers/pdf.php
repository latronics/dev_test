<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

////MITKO
/// TEST CONTROLLER FOR CREATION OF PDF...

class pdf extends Controller {

	function pdf()
	{		
		parent::Controller();	
					
	}
		
	function index() 
	{
	
	$this->load->plugin('to_pdf');
     $html = 'Hello<br><br><br><span style="font-family:Verdana; font-size:40px;">This is a PDF <strong>TEST</strong></span><Br><br><br>Thank You!';
     pdf_create($html, 'filename');
	
	}
	
	
}
