<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DeadEnd extends Controller {

	function DeadEnd()
	{
		parent::Controller();	
	}
	
	function index()
	{
	echo 'You have reached a dead end. Please try a different approach';				
	}

}
