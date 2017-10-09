<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Backup extends Controller {

	function Backup()
	{
		parent::Controller();
		$this->load->dbutil();
		$this->load->helper('file');
		set_time_limit(600);
		ini_set('mysql.connect_timeout', 600);
		ini_set('max_execution_time', 600);  
		ini_set('default_socket_timeout', 600);
		
	}
	function cleantrash()
	{
			$dir = $this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication']."/sess/";
			/*** cycle through all files in the directory ***/
			foreach (glob($dir."*.txt") as $file) {
			/*** if file is 24 hours (86400 seconds) old then delete it ***/
			if (filemtime($file) < time() - 172800) {
				unlink($file);
				}
			}	
			$dir = $this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication']."/sess/post/";
			/*** cycle through all files in the directory ***/
			foreach (glob($dir."*.txt") as $file) {
			/*** if file is 24 hours (86400 seconds) old then delete it ***/
			if (filemtime($file) < time() - 172800) {
				unlink($file);
				}
			}	
			$dir = $this->config->config['pathtosystem'].'/backup/';
			/*** cycle through all files in the directory ***/
			foreach (glob($dir."*") as $file) {
			/*** if file is 24 hours (86400 seconds) old then delete it ***/
			if (filemtime($file) < time() - 864000) {
				unlink($file);
				}
			}	
			$dir = $this->config->config['pathtosystem'].'/logs/';
			/*** cycle through all files in the directory ***/
			foreach (glob($dir."*.php") as $file) {
			/*** if file is 24 hours (86400 seconds) old then delete it ***/
			if (filemtime($file) < time() - 1296000) {
				unlink($file);
				}
			}
	}
	function index()
	{
		ini_set('memory_limit','1024M');
		phpinfo();
		exit();
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup();				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup); 

		/*
		if (($_SERVER['REMOTE_ADDR'] != '93.152.144.229') && ($_SERVER['REMOTE_ADDR'] != '87.121.161.130') &&  ($_SERVER['REMOTE_ADDR'] != '75.80.117.61')) { echo 'WTF ?!?, who are you ?!?'; exit();}
		
		$dirsystem = $this->config->config['pathtosystem'];
		$systemfilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'-_System.tar';
		system("tar cf $systemfilename $dirsystem");
		system("gzip -9 $systemfilename $systemfilename");		

		$dirwww = $this->config->config['pathtopublic'];
		$wwwwfilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_www.tar';
		system("tar cf $wwwwfilename $dirwww");
		system("gzip -9 $wwwwfilename $wwwwfilename");
		*/
		echo 'OK';
	}
	
	function ebay()
	{
		ini_set('memory_limit','2048M');
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup(array('tables'=> array('ebay')));				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_ebay.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup); 
		echo 'OK';
	}
	function warehouse()
	{
		ini_set('memory_limit','2048M');
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup(array('tables'=> array('warehouse')));				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_warehouse.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup);
		echo 'OK';
	}
	function ebaytransactions()
	{
		ini_set('memory_limit','2048M');
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup(array('tables'=> array('ebay_transactions')));				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_ebay_transactions.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup);
		echo 'OK';
	}
	function logs()
	{
		ini_set('memory_limit','2048M');
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup(array('tables'=> array('ebay_actionlog','ebay_revise_log')));				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_ebay_actionlog_bay_revise_log.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup); 
		echo 'OK';
		
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup(array('tables'=> array('warehouse_log')));				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_warehouse_log.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup); 
		echo 'OK';		
	}
	
	function orders()
	{
		ini_set('memory_limit','2048M');
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		$backup =& $this->dbutil->backup(array('tables'=> array('orders','warehouse_orders')));				
		$databasefilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_orders_warehouse_orders.sql.gz';
		//echo $databasefilename;
		write_file($databasefilename, $backup); 
		echo 'OK';
	}
	
	
	
	
	function Files()
	{
		if (($_SERVER['REMOTE_ADDR'] != '93.152.150.71') && ($_SERVER['REMOTE_ADDR'] != '87.121.161.130') &&  ($_SERVER['REMOTE_ADDR'] != '75.80.117.61')) { echo 'WTF ?!?, who are you ?!?'; exit();}
		
		$datetime_string = date("Y.m.d").'-'.date("H_i_s");
		
		$dirsystem = $this->config->config['pathtosystem'];
		$systemfilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'-_System.tar';
		system("tar cf $systemfilename $dirsystem");
		system("gzip -9 $systemfilename $systemfilename");		

		$dirwww = $this->config->config['pathtopublic'];
		$wwwwfilename = $this->config->config['pathtosystem'].'/backup/'.$datetime_string.'_'.$this->config->config['sitename'].'_-_www.tar';
		//system("tar cf $wwwwfilename $dirwww");
		//system("gzip -9 $wwwwfilename $wwwwfilename");
		
		echo 'OK';
	
	}
	
}