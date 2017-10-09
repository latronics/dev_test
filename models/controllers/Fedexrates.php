<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

exit();

class Fedexrates extends Controller {

	function Fedexrates()
	{
		parent::Controller();	
	}
	
	function index()
	{

				$currentdata['Address']['StreetLines'][0] = '4709 Campbell Dr';
				$currentdata['Address']['City'] = 'Culver City'; ///////////
				/*[v7:Notifications] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [v7:Severity] => SUCCESS
                                                            [v7:Source] => crs
                                                            [v7:Code] => 
                                                            [v7:Message] => Request was successfully processed. 
                                                            [v7:LocalizedMessage] => Request was successfully processed. 
                                                        )

                                                )*/
				$currentdata['Address']['StateOrProvinceCode'] = 'CA';
				$currentdata['Address']['PostalCode'] = '90230';
				$currentdata['Address']['CountryCode'] = 'US';
				$currentdata['Address']['OrigCountry'] = 'United States of America';
				
				//if ($currentdata['Address']['CountryCode'] == 'US') $currentdata['quote'] = $this->_PrepareFedex($currentdata, $return);
		echo 1;
		$this->_PrepareFedex($currentdata, 1);				
		echo 2;
		//$currentdata['uspsquote'] = $this->_PrepareUsps($currentdata, $inputdelivery);				
	
		
	
	
	}
	
	function _PrepareFedex($client) 
{

$this->load->library('fedex');
$this->load->library('xml');
			
$homeaddress =  array('Address' => array(
                       					'StreetLines' => array('Persnk 22'),
                       					'City' => 'Sofia',
                       					'StateOrProvinceCode' => 'BG',
                      					'PostalCode' => '1407',
                       					'CountryCode' => 'BG')); 
$clientaddress = $client;
										
$what = array('Weight' => array('Value' => 0.25,
                                'Units' => 'LB')
                                );
$shipment = array('PackagingType' => 'YOUR_PACKAGING');
// valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
$now = CurrentTime();

	$xmlto = $this->fedex->fedexgo($clientaddress, $homeaddress, $what, $shipment);		
			$result['toraw'] = $this->xml->createArray($xmlto);	
			
			
					foreach ($result['toraw']['soapenv:Header']['soapenv:Body'][0]['v7:RateReply'][0]['v7:RateReplyDetails'] as $tkey => $tvalue)
					{
						foreach ($tvalue['v7:RatedShipmentDetails'] as $tsdkey => $tsdvalue)
						{
						if (($tsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'RATED_ACCOUNT') || ($tsdvalue['v7:ShipmentRateDetail'][0]['v7:RateType'] == 'PAYOR_ACCOUNT')) $ratescalc[] = $tsdvalue['v7:ShipmentRateDetail'][0]['v7:TotalNetCharge'][0]['v7:Amount'];
						}						
											 
						if (isset($ratescalc[1]) && ($ratescalc[1] > $ratescalc[0])) $ratescalced = (float)$ratescalc[1];
						else $ratescalced = (float)$ratescalc[0];
						$result['to'][] = array('type' => $tvalue['v7:ServiceType'], 'sum' => $ratescalced);
					
						unset ($ratescalced);
						unset ($ratescalc);																	
					}
					
				$result['to'] = array_reverse($result['to']);						
printcool($result['to']);

}
	
}
