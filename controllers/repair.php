<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class repair extends Controller {

	function repair()
	{
		parent::Controller();	
		$this->load->model('Menus_model');	
		$this->load->model('Product_model');	
		$this->Menus_model->DoTracking();
		$this->Menus_model->GetStructure();		
		$this->Product_model->GetStructure('top');
		
		if (isset($this->session->userdata['noadd'])) $this->session->unset_userdata('noadd');
		if (isset($this->session->userdata['unregnoadd'])) $this->session->unset_userdata('unregnoadd');
			
		
		if (isset($this->session->userdata['user_id'])) {
			$this->load->model('Start_model');
			$this->load->model('Auth_model');
			$this->Auth_model->VerifyUser();	
		}
		$this->mysmarty->assign('session',$this->session->userdata);
		
			if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '') )
				{
				$this->mysmarty->assign('cartsession',$this->session->userdata['cart']);
				$this->mysmarty->assign('carttotal', $this->_CartTotal());
				}	
			}
		
	function index() 
	{
		$this->mysmarty->assign('geo', $this->_CleanAndExplode($this->_ReturnCC()));
		$this->mysmarty->assign('repairview', 'la');
		$this->mysmarty->view('repair/repair_main.html');
	}
	function cities($string = '')
	{	
		$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
		$this->mysmarty->assign('specials', $this->Product_model->GetTopSpecials());
		$this->mysmarty->assign('string', str_replace('-', ' ',$string));
		$this->mysmarty->assign('repairview', 'cities');
		$this->mysmarty->view('repair/repair_main.html');
	}
	function zips($string = '')
	{
		Redirect('repair/counties/losangeles');
	}
	function losangeles ()
	{
		$this->mysmarty->assign('geo', $this->_CleanAndExplode($this->_ReturnCC()));
		$this->mysmarty->assign('repairview', 'la');
		$this->mysmarty->view('repair/repair_main.html');
	}

function _CleanAndExplode($array)
	{
		$old = array("(", ")", ",",' ');
		$new = array("", "", "",'-');
		$count = 1;
		foreach ($array as $k => $v)
			{
				$tmp = explode('/', $v);
				$newarr[trim($k)] = array('zip' => $tmp,
										  'urlcity' => trim(str_replace($old, $new, $k)),
										  'count' => $count);
				$count++;
			}
		return $newarr;
	}
	
function _ReturnCC()
	{
	return array(
'Acton' => '93510',
'Agoura' => '91301',
'Agua Dulce, Saugu' => '91350',
'Airport Worldway (City of LA)' => '90009',
'Alhambra' => '91801/91803',
'Altadena' => '91001',
'Arcadia' => '91006/91007',
'ARCO Towers (City of LA)' => '90071',
'Arleta (City of LA)' => '91331',
'Artesia' => '90680',
'Athens' => '90044',
'Atwater Village (City of LA)' => '90039',
'Avalon' => '90704',
'Azusa' => '91702',
'Baldwin Hills' => '90008',
'Baldwin Park' => '91706',
'Bassett' => '91746',
'Bel Air Estates (City of LA)' => '90049/90077',
'Bell' => '90201',
'Bell Gardens' => '90201',
'Bellflower' => '90706',
'Beverly Glen (City of LA)' => '90077/90210',
'Beverly Hills' => '90210/90211/90212',
'Biola Univ. (La Mirada)' => '90639',
'Boyle Heights (City of LA)' => '90033',
'Bradbury' => '91010',
'Brentwood (City of LA)' => '90049',
'Burbank' => '91501/91502/91506/91523',
'Burbank (Glenoaks)' => '91504',
'Burbank (Woodbury Univ.)	' => '91510',
'Cal State Dominguez Hills (Carson)	' => '90747',
'Cal State Long Beach (Long Beach)	' => '90840',
'Cal State Northridge (City of LA)	' => '91330',
'Cal Tech (Pasadena)	' => '91125/91126',
'Calabasas	' => '91302/91372',
'Canoga Park (City of LA)	' => '91303/91304',
'Canyon Country (Santa Clarita)	' => '91351',
'Carson	' => '90745/90746',
'Carson (CS Univ. Dominguez Hills)	' => '90747',
'Carson/Long Beach	' => '90810',
'Castaic	' => '91310/91384',
'Castellemare (City of LA)	' => '90272',
'Century City (City of LA)	' => '90067',
'Cerritos	' => '90701',
'Chatsworth (City of LA)	' => '91311',
'Cheviot Hills (City of LA)	' => '90064',
'Chinatown (City of LA)	' => '90012',
'City Terrace	' => '90063',
'Civic Center (City of LA)	' => '90012',
'Claremont	' => '91711',
'Commerce, City of	' => '90040',
'Compton	' => '90220/90221/90222',
'Country Club Park (City of LA)	' => '90019',
'Covina	' => '91722/91723/91724',
'Crenshaw (City of LA)	' => '90008',
'Cudahy	' => '90201',
'Culver City	' => '90230/90232',
'Cypress Park (City of LA)	' => '90065',
'Diamond Bar	' => '91765/91789',
'Dominguez Hills, Cal State (Carson)	' => '90747',
'Downey	' => '90240/90241/90242',
'Downtown Los Angeles (City of LA)	 ' => '90013/90014/90015/90017/90021',
'Eagle Rock (City of LA)	' => '90041',
'East Los Angeles	' => '90022',
'East Los Angeles (City of LA)	' => '90023',
'East Rancho Dominguez	' => '90221',
'Echo Park (City of LA)	' => '90026',
'Edwards AFB	' => '93523',
'El Monte	' => '91731/91732',
'El Segundo	' => '90245',
'El Sereno (City of LA)	' => '90032',
'Elizabeth Lake	' => '93532',
'Encino (City of LA)	' => '91316/91436',
'Federal Bldg (Lawndale)	' => '90261',
'Firestone Boy Scout Res.	' => '92621',
'Florence	' => '90001',
'Gardena	' => '90247/90248/90249',
'Glassell Park (City of LA)	' => '90065',
'Glendale	' => '91201/91202/91203/91204/91205/91206/91207/91208',
'Glendale (La Crescenta)	' => '91214',
'Glendale (Tropico)	' => '91204/91205',
'Glendale (Verdugo City)	' => '91046',
'Glendora	' => '91740/91741',
'Glenoaks (Burbank)	' => '91504',
'Granada Hills (City of LA)	' => '91344',
'Griffith Park (City of LA)	' => '90027',
'Hacienda Heights (La Puente)	' => '91745',
'Hancock Park (City of LA)	' => '90004/90020',
'Harbor City (City of LA)	' => '90710',
'Hawaiian Gardens	' => '90716',
'Hawthorne (Holly Park)	' => '90250',
'Hermosa Beach	' => '90254',
'Hi Vista	' => '93535',
'Hidden Hills	' => '91302',
'Highland Park (City of LA)	' => '90042',
'Hollywood (City of LA)	' => '90028/90029/90038/90068',
'Huntington Park	' => '90255',
'Hyde Park (City of LA)	' => '90043',
'Industry, City of	' => '91744/91746/91789',
'Inglewood	' => '90301/90302/90303/90305',
'Irwindale	' => '91706',
'Jefferson Park (City of LA)	' => '90018',
'Juniper Hills	' => '93543',
'Koreatown (City of LA)	' => '90005',
'La Canada-Flintridge	' => '91011',
'La Crescenta (Glendale)	' => '91214',
'La Habra Heights	' => '90631',
'La Mirada	' => '90638',
'La Mirada (Biola Univ.)	' => '90639',
'La Puente	' => '91744/91746',
'La Puente (Hacienda Heights)	' => '91745',
'La Puente (Rowland Heights)	' => '91748',
'La Verne	' => '91750',
'Ladera Heights (City of LA)	' => '90056',
'Lake Hughes	' => '93532',
'Lake Los Angeles	' => '93550/93591',
'Lake View Terrace (City of LA)	' => '91342',
'Lakewood	' => '90712/90713/90715',
'Lancaster	' => '93534/93535/93536',
'Lawndale	' => '90260',
'Lawndale (Federal Bldg)	' => '90261',
'LAX Area (City of LA)	' => '90045',
'Leimert Park (City of LA)	' => '90008',
'Lennox	' => '90304',
'Littlerock	' => '93543',
'Llano	' => '93544',
'Lomita	' => '90717',
'Long Beach	' => '90802/90803/90804/90805/90806/90807/90808/90813/90814/90815/90822',
'Long Beach (Cal State Long Beach)	' => '90840',
'Long Beach (McDonnell Douglas)	' => '90846',
'Long Beach (North Long Beach)	' => '90805',
'Long Beach (World Trade Ctr)	' => '90831/90832',
'Los Angeles (Airport Worldway)	' => '90009',
'Los Angeles (ARCO Towers)	' => '90071',
'Los Angeles (Arleta)	' => '91331',
'Los Angeles (Atwater Village)	' => '90039',
'Los Angeles (Bel Air Estates)	' => '90049/90077',
'Los Angeles (Beverly Glen)	' => '90077/90210',
'Los Angeles (Boyle Heights)	' => '90033',
'Los Angeles (Brentwood)	' => '90049',
'Los Angeles (Cal State Northridge)	' => '91330',
'Los Angeles (Canoga Park)	' => '91303/91304',
'Los Angeles (Century City)	' => '90067',
'Los Angeles (Chatsworth)	' => '91311',
'Los Angeles (Cheviot Hills)	' => '90064',
'Los Angeles (Chinatown)	' => '90012',
'Los Angeles (Civic Center)	' => '90012',
'Los Angeles (Country Club Park)	' => '90019',
'Los Angeles (Crenshaw)	' => '90008',
'Los Angeles (Cypress Park)	' => '90065',
'Los Angeles (Downtown)	 ' => '90013/90014/90015/90017/90021/90029',
'Los Angeles (Eagle Rock)	' => '90041',
'Los Angeles (East Los Angeles)	' => '90023',
'Los Angeles (Echo Park)	' => '90026',
'Los Angeles (El Sereno)	' => '90032',
'Los Angeles (Encino)	' => '91316/91436',
'Los Angeles (Glassell Park)	' => '90065',
'Los Angeles (Granada Hills)	' => '91344',
'Los Angeles (Griffith Park)	' => '90027',
'Los Angeles (Hancock Park)	' => '90004/90020',
'Los Angeles (Harbor City)	' => '90710',
'Los Angeles (Highland Park)	' => '90042',
'Los Angeles (Hollywood)	' => '90028/90038/90068',
'Los Angeles (Hyde Park)	' => '90043',
'Los Angeles (Jefferson Park)	' => '90018',
'Los Angeles (Koreatown)	' => '90005',
'Los Angeles (Ladera Heights)	' => '90056',
'Los Angeles (Lake View Terrace)	' => '91342',
'Los Angeles (LAX Area)	' => '90045',
'Los Angeles (Leimert Park)	' => '90008',
'Los Angeles (Los Feliz)	' => '90027',
'Los Angeles (Mar Vista)	' => '90066',
'Los Angeles (Mid City)	' => '90019',
'Los Angeles (Mission Hills)	' => '91345',
'Los Angeles (Montecito Heights)	' => '90031',
'Los Angeles (Mount Olympus)	' => '90046',
'Los Angeles (Mt. Washington)	' => '90065',
'Los Angeles (North Hills)	' => '91343',
'Los Angeles (North Hollywood)	 ' => '91601/91602/91604/91605/91606/91607',
'Los Angeles (Northridge)	' => '91324/91325',
'Los Angeles (Pacific Highlands)	' => '90272',
'Los Angeles (Pacific Palisades)	' => '90272',
'Los Angeles (Pacoima)	' => '91331',
'Los Angeles (Palms)	' => '90034',
'Los Angeles (Panorama City)	' => '91402',
'Los Angeles (Park La Brea)	' => '90036',
'Los Angeles (Pico Heights)	' => '90006',
'Los Angeles (Playa del Rey)	' => '90293',
'Los Angeles (Porter Ranch)	' => '91326',
'Los Angeles (Rancho Park)	' => '90064',
'Los Angeles (Reseda)	' => '91335',
'Los Angeles (San Pedro)	' => '90731/90732',
'Los Angeles (Sawtelle)	' => '90025',
'Los Angeles (Shadow Hills)	' => '91040',
'Los Angeles (Sherman Oaks)	' => '91403/91423',
'Los Angeles (Silverlake)	' => '90026',
'Los Angeles (South Central)	 ' => '90001/90003/90007/90011/90037/90047/90061/90062',
'Los Angeles (Studio City)	' => '91604',
'Los Angeles (Sun Valley)	' => '91352',
'Los Angeles (Sunland)	' => '91040',
'Los Angeles (Sylmar)	' => '91342',
'Los Angeles (Tarzana)	' => '91356',
'Los Angeles (Terminal Island)	' => '90731',
'Los Angeles (Toluca Lake)	' => '91602',
'Los Angeles (Tujunga)	' => '91042',
'Los Angeles (USC)	' => '90089',
'Los Angeles (Valley Village)	' => '91607',
'Los Angeles (Van Nuys)	 ' => '91401/91402/91403/91405/91406/91411/91423',
'Los Angeles (Venice)	' => '90291',
'Los Angeles (Watts)	' => '90002/90059',
'Los Angeles (West Adams)	' => '90016',
'Los Angeles (West Beverly)	' => '90048',
'Los Angeles (West Fairfax)	' => '90035',
'Los Angeles (West Hills)	' => '91307',
'Los Angeles (West Los Angeles)	' => '90025',
'Los Angeles (Westchester)	' => '90045',
'Los Angeles (Westlake)	' => '90057',
'Los Angeles (Westwood)	' => '90024',
'Los Angeles (Wilmington)	' => '90744',
'Los Angeles (Wilshire Blvd)	' => '90010',
'Los Angeles (Winnetka)	' => '91306',
'Los Angeles (Woodland Hills)	' => '91364/91367',
'Los Feliz (City of LA)	' => '90027',
'Los Nietos	' => '90606',
'Lynwood	' => '90262',
'Malibu	' => '90265',
'Manhattan Beach	' => '90266',
'Mar Vista (City of LA)	' => '90066',
'Marina del Rey	' => '90292',
'Maywood	' => '90270',
'McDonnell Douglas (Long Beach)	' => '90846',
'Mid City (City of LA)	' => '90019',
'Mission Hills (City of LA)	' => '91345',
'Monrovia	' => '91016',
'Montebello	' => '90640',
'Montecito Heights (City of LA)	' => '90031',
'Monterey Hills (City of LA)	' => '90032',
'Monterey Park	' => '91754/91755/91756',
'Montrose	' => '91020',
'Mount Olympus (City of LA)	' => '90046',
'Mount Wilson	' => '91023',
'Mt. Washington (City of LA)	' => '90065',
'Newhall (Santa Clarita)	' => '91321',
'North Hills (City of LA)	' => '91343',
'North Hollywood (City of LA)	 ' => '91601/91602/91604/91605/91606/91607',
'North Long Beach (Long Beach)	' => '90805',
'Northridge (City of LA)	' => '91324/91325',
'Northridge, Cal State Univ. (City of LA)	' => '91330',
'Norwalk	' => '90650',
'Oak Park	' => '91301',
'Pacific Highlands (City of LA)	' => '90272',
'Pacific Palisades (City of LA)	' => '90272',
'Pacoima (City of LA)	' => '91331',
'Palmdale	' => '93550/93551/93552/93591',
'Palms (City of LA)	' => '90034',
'Palos Verdes Estates	' => '90274',
'Panorama City (City of LA)	' => '91402',
'Paramount	' => '90723',
'Park La Brea (City of LA)	' => '90036',
'Pasadena	' => '91101/91103/91104/91105/91106/91107',
'Pasadena (Cal Tech)	' => '91125/91126',
'Pearblossom	' => '93553',
'Phillips Ranch	' => '91766',
'Pico Heights (City of LA)	' => '90006',
'Pico Rivera	' => '90660',
'Playa del Rey (City of LA)	' => '90293',
'Playa Vista (City of LA)	' => '90094',
'Pomona	' => '91766/91767/91768',
'Porter Ranch (City of LA)	' => '91326',
'Quartz Hill	' => '93536',
'Rancho Dominguez	' => '90220',
'Rancho Palos Verdes	' => '90275/90717/90732',
'Rancho Park (City of LA)	' => '90064',
'Redondo Beach	' => '90277/90278',
'Reseda (City of LA)	' => '91335',
'Rolling Hills	' => '90274',
'Rolling Hills Estates	' => '90274',
'Rosemead	' => '91770',
'Rosewood	' => '90222',
'Rowland Heights (La Puente)	' => '91748',
'San Dimas	' => '91773',
'San Fernando	' => '91340',
'San Gabriel	' => '91775/91776',
'San Marino	' => '91108',
'San Pedro (City of LA)	' => '90731/90733',
'Santa Clarita (Canyon Country)	' => '91351',
'Santa Clarita (Newhall)	' => '91321',
'Santa Clarita (Valencia)	' => '91354/91355',
'Santa Fe Springs	' => '90670',
'Santa Monica	' => '90401/90402/90403/90404/90405',
'Saugus, Agua Dulce	' => '91350',
'Sawtelle (City of LA)	' => '90025',
'Shadow Hills (City of LA)	' => '91040',
'Sherman Oaks (City of LA)	' => '91403/91423',
'Sierra Madre	' => '91024',
'Signal Hill	' => '90755',
'Silverlake (City of LA)	' => '90026',
'South Central (City of LA)	 ' => '90001/90003/90007/90011/90037/90047/90061/90062',
'South El Monte	' => '91733',
'South Gate	' => '90280',
'South Pasadena	' => '91030',
'South Whittier	' => '90605',
'Stevenson Ranch	' => '91381',
'Studio City (City of LA)	' => '91604',
'Sun Valley (City of LA)	' => '91352',
'Sunland (City of LA)	' => '91040',
'Sylmar (City of LA)	' => '91342',
'Tarzana (City of LA)	' => '91356',
'Temple City	' => '91780',
'Terminal Island (City of LA)	' => '90731',
'Toluca Lake (City of LA)	' => '91602',
'Topanga	' => '90290',
'Torrance	 ' => '90501/90502/90503/90504/90505/90506/90277/90278',
'Tropico (Glendale)	' => '91204/91205',
'Tujunga (City of LA)	' => '91042',
'Universal City	' => '91608',
'USC (City of LA)	' => '90089',
'VA Hospital (Sawtelle)	' => '90073',
'Valencia (Santa Clarita)	' => '91354/91355',
'Valinda	' => '91744',
'Valley Village (City of LA)	' => '91607',
'Valyermo	' => '93563',
'Van Nuys (City of LA)	 ' => '91401/91402/91403/91405/91406/91411/91423',
'Venice (City of LA)	' => '90291',
'Verdugo City (Glendale)	' => '91046',
'Vernon	' => '90058',
'View Park	' => '90043',
'Walnut	' => '91789',
'Walnut Park	' => '90255',
'Watts (City of LA)	' => '90002/90059',
'West Adams (City of LA)	' => '90016',
'West Beverly (City of LA)	' => '90048',
'West Covina	' => '91790/91791/91792/91793',
'West Fairfax (City of LA)	' => '90035',
'West Hills (City of LA)	' => '91307',
'West Hollywood	' => '90069',
'West Los Angeles (City of LA)	' => '90025',
'Westchester (City of LA)	' => '90045',
'Westlake (City of LA)	' => '90057',
'Westlake Village	' => '91361/91362',
'Westwood (City of LA)	' => '90024',
'Whittier	' => '90601/90602/90603/90604/90605',
'Whittier (Whittier College)	' => '90608',
'Whittier College (Whittier)	' => '90608',
'Willowbrook	' => '90059/90222',
'Wilmington (City of LA)	' => '90744',
'Wilshire Blvd (City of LA)	' => '90010',
'Windsor Hills	' => '90043',
'Winnetka (City of LA)	' => '91306',
'Woodbury Univ. (Burbank)	' => '91510',
'Woodland Hills (City of LA)	' => '91364/91367',
'World Trade Center (Long Beach)	' => '90831/90832');
		
	}
	
function _CartTotal() 
	{
			//////
	if ($this->config->config['cartclosed'] == 1) {
	echo '<div style="padding:10px;">';
	
	echo $this->config->config['cartclosemsg'];
	echo '<br><br>
	<a onclick="cartSlide()" href="#"><img src="'.Site_url().'images/close.png" style="margin-top:5px; margin-left:10px;" /></a>
	</div>';
	exit();
	}
	
	/////
	
	if (isset($this->session->userdata['cart'])) $crtdata = $this->session->userdata['cart'];
	else $crtdata = FALSE;
	
	$total = 0;
	if ($crtdata && $crtdata != '')
		{
			foreach ($crtdata as $key => $value) 
			{
			$total = $total + ((int)$value['quantity'] * (float)$value['p_price']);
			}
		}
		return (float)$total;
	}
}
