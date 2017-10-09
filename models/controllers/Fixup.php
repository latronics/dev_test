<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fixup extends Controller {

	function Fixup()
	{		
		parent::Controller();	
					
	}
	function index() 
	{
	}
	function ebayrevise()
	{
		$this->db->query('DROP TABLE ebay_revise');
		$this->db->query("CREATE TABLE IF NOT EXISTS `ebay_revise` (
  `er_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL,
  `e_type` enum('q','p') NOT NULL,
  `e_val` varchar(50) NOT NULL,
  `place` varchar(255) DEFAULT NULL,
  `admin` varchar(20) DEFAULT NULL,
  KEY `er_id` (`er_id`),
  KEY `e_id` (`e_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
echo 'OK';
	}
			
}
	