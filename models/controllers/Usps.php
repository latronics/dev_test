<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usps extends Controller {

	function Usps()
	{
		parent::Controller();	
	}
	
	function index()
	{
	
	$this->load->library('uspsrates');
	
	$this->uspsrates->setServer("http://production.shippingapis.com/ShippingAPI.dll");
	$this->uspsrates->setUserName("640LOSAN0902");
	$this->uspsrates->setPass("683GF04NK255");
	$this->uspsrates->setService("All");
	
	$this->uspsrates->setDestZip("10001");
	$this->uspsrates->setOrigZip("90230");
	$this->uspsrates->setWeight(10, 0);
	$this->uspsrates->setContainer("Flat Rate Box");
	$this->uspsrates->setCountry("USA");
	$this->uspsrates->setMachinable("true");
	$price = $this->uspsrates->getPrice(); 
	$price = objectToArray($price);
	foreach($price['result'] as $k => $v)
	{
		$price['result'][$k] = $v['mailservice'];
	}
	$result[] = array('DestinationZIP' => $price['dest_zip'],
					'OriginationZIP' => $price['orig_zip'],
					'WeightLBS' => $price['pounds'],
					'WeightOz' => $price['ounces'],
					'Country' => $price['country'],
					'Services' =>$price['result']
					);
	printcool ('USA, CA to NY, 1LBS');
	printcool ($result);
	unset($price);
	unset($result);
	//////////////////////
	$this->uspsrates->setDestZip("10001");
	$this->uspsrates->setOrigZip("90230");
	$this->uspsrates->setWeight(11, 0);
	$this->uspsrates->setCountry("USA");
	
	$price = $this->uspsrates->getPrice(); 
	$price = objectToArray($price);
	foreach($price['result'] as $k => $v)
	{
		$price['result'][$k] = $v['mailservice'];
	}
	$result[] = array('DestinationZIP' => $price['dest_zip'],
					'OriginationZIP' => $price['orig_zip'],
					'WeightLBS' => $price['pounds'],
					'WeightOz' => $price['ounces'],
					'Country' => $price['country'],
					'Services' =>$price['result']
					);
	
	printcool ('USA, CA to NY, 10LBS');
	printcool ($result);
		unset($price);
	unset($result);
	////////////////////////
	$this->uspsrates->setDestZip("10001");
	$this->uspsrates->setOrigZip("90230");
	$this->uspsrates->setWeight(25, 0);
	$this->uspsrates->setCountry("USA");
	
	 $price = $this->uspsrates->getPrice(); 
	$price = objectToArray($price);
	foreach($price['result'] as $k => $v)
	{
		$price['result'][$k] = $v['mailservice'];
	}
	$result[] = array('DestinationZIP' => $price['dest_zip'],
					'OriginationZIP' => $price['orig_zip'],
					'WeightLBS' => $price['pounds'],
					'WeightOz' => $price['ounces'],
					'Country' => $price['country'],
					'Services' =>$price['result']
					);
	printcool ('USA, CA to NY, 25LBS');
	printcool ($result);
		unset($price);
	unset($result);
	/////////////////////////
	
	
	
	
	$this->uspsrates->setDestZip("10001");
	$this->uspsrates->setOrigZip("90230");
	$this->uspsrates->setWeight(1, 0);
	$this->uspsrates->setContainer("Flat Rate Box");
	$this->uspsrates->setCountry("Bulgaria");
	$this->uspsrates->setMachinable("true");
	$price = $this->uspsrates->getPrice(); 
	$price = objectToArray($price);
	foreach($price['result'] as $k => $v)
	{
		$price['result'][$k] = $v['mailservice'];
	}
	$result[] = array('DestinationZIP' => $price['dest_zip'],
					'OriginationZIP' => $price['orig_zip'],
					'WeightLBS' => $price['pounds'],
					'WeightOz' => $price['ounces'],
					'Country' => $price['country'],
					'Services' =>$price['result']
					);
	printcool ('INTL, CA to Bulgaria, 1LBS');
	printcool ($result);
		unset($price);
	unset($result);
	////////////////////
	$this->uspsrates->setDestZip("10001");
	$this->uspsrates->setOrigZip("90230");
	$this->uspsrates->setWeight(10, 0);
	$this->uspsrates->setCountry("Bulgaria");
	
	$price = $this->uspsrates->getPrice(); 
	$price = objectToArray($price);
	foreach($price['result'] as $k => $v)
	{
		$price['result'][$k] = $v['mailservice'];
	}
	$result[] = array('DestinationZIP' => $price['dest_zip'],
					'OriginationZIP' => $price['orig_zip'],
					'WeightLBS' => $price['pounds'],
					'WeightOz' => $price['ounces'],
					'Country' => $price['country'],
					'Services' =>$price['result']
					);
	
	printcool ('INTL, CA to Bulgaria, 10LBS');
	printcool ($result);
		unset($price);
	unset($result);
	/////////////////
	$this->uspsrates->setDestZip("10001");
	$this->uspsrates->setOrigZip("90230");
	$this->uspsrates->setWeight(25, 0);
	$this->uspsrates->setCountry("Bulgaria");
	
	 $price = $this->uspsrates->getPrice(); 
	$price = objectToArray($price);
	foreach($price['result'] as $k => $v)
	{
		$price['result'][$k] = $v['mailservice'];
	}
	$result[] = array('DestinationZIP' => $price['dest_zip'],
					'OriginationZIP' => $price['orig_zip'],
					'WeightLBS' => $price['pounds'],
					'WeightOz' => $price['ounces'],
					'Country' => $price['country'],
					'Services' => $price['result']
					);
	printcool ('INTL, CA to Bulgaria, 25LBS');
	printcool ($result);
		unset($price);
	unset($result);
	
	
	
	

	
	
	}

}