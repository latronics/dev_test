<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gapitest extends Controller {

function gapitest()
	{
		parent::Controller();
		}
function _XML2Array($parent)
{
    $array = array();

    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
            && (1 === count($node) ? $node = array($node) : 1)
            && $node = & $node[];

        $node = $element->count() ? $this->_XML2Array($element) : trim($element);
    }

    return $array;
}
	function GetShortAccessToken($client_id, $redirect_uri, $client_secret, $code) {	

		$url_token = 'https://accounts.google.com/o/oauth2/token';
		$curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_token);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		$data = json_decode(curl_exec($ch), true);	
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception(NULL, 'Error : Failed to receieve access token');
		return $data;
	}
	 function GetRefreshedAccessToken($client_id, $refresh_token, $client_secret) {	

		$url_token = 'https://accounts.google.com/o/oauth2/token';			
		$curlPost = 'client_id=' . $client_id . '&client_secret=' . $client_secret . '&refresh_token='. $refresh_token . '&grant_type=refresh_token';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_token);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		return curl_exec($ch);
		
		//Done again in OAuth2.php/line 180
		/*$data = json_decode(curl_exec($ch), true);	
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception(NULL, 'Error : Failed to refresh access token');
		return $data['access_token'];*/
	}

/**
 * Retrieve a list of File resources.
 *
 * @param Google_DriveService $service Drive API service instance.
 * @return Array List of Google_DriveFile resources.
 */
function retrieveAllFiles($service, $params) {
  $result = array();
  $pageToken = NULL;

  do {
    try {
      $parameters = array();
      if ($pageToken) {
        $parameters['pageToken'] = $pageToken;
      }
      $files = $service->files->listFiles($parameters);

      $result = array_merge($result, $files->getItems());
      $pageToken = $files->getNextPageToken();
    } catch (Exception $e) {
      print "An error occurred: " . $e->getMessage();
      $pageToken = NULL;
    }
  } while ($pageToken);
  return $result;
}

function test2()
{
	require_once $this->config->config['gapi'].'/autoload.php';

	require_once($this->config->config['pathtopublic'].'/gsssettings.php');
	$client = new Google_Client();
		$client->setClientId(GOOGLE_CLIENT_ID);
		$client->setClientSecret(GOOGLE_CLIENT_SECRET);
		$client->setRedirectUri(GOOGLE_REDIRECT_URI);
		$client->setScopes(array('https://www.googleapis.com/auth/drive'));
		$authUrl = $client->createAuthUrl();
		echo $authUrl;
		
		
		
		
		
		
}
function index()
	{	
	
	
require_once $this->config->config['gapi'].'/autoload.php';

require_once($this->config->config['pathtopublic'].'/gsssettings.php');

  $client = new Google_Client();
  $client->setApplicationName("LaTronics");
  $client->setDeveloperKey(GOOGLE_CLIENT_ID);
// $client->setAccessType('online');
  $client->setClientId(GOOGLE_CLIENT_ID);
  $client->setClientSecret(GOOGLE_CLIENT_SECRET);
  $client->setRedirectUri(GOOGLE_REDIRECT_URI);
  $client->setScopes(array('https://www.googleapis.com/auth/drive'));  
  $service = new Google_Service_Drive($client);
  $authUrl = $client->createAuthUrl();
  
  //$access_token = $client->refreshToken(GOOGLE_REFRESH_TOKEN);
  //printcool ($access_token);
  
  
  
  ///
  
  $service = new Google_Service_Drive($client);
/*
$authUrl = $client->createAuthUrl();
echo '<br><br><a href="'.$authUrl.'">AUTHENTICATE</a><br><br>';
//Request authorization
print "Please visit:\n$authUrl\n\n";
print "Please enter the auth code:\n";
*/

//printcool ($client->authenticate($accessToken));
//$accessToken = $this->GetShortAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REDIRECT_URI, GOOGLE_CLIENT_SECRET,$accessToken);

//$authCode = trim(fgets(STDIN));
// Exchange authorization code for access token
//$accessToken = $client->authenticate($authCode);

$client->setAccessToken($this->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET));
//
  
  //$client->authenticate($access_token);
  //$client->setAccessToken(GOOGLE_REFRESH_TOKEN);
  $service = new Google_Service_Drive($client);



printcool ($service);
    
//printcool ($this->_XML2Array($service));

$parameters = array();
$parameters['q'] = "fullText contains 'me'";
$files = $this->retrieveAllFiles($service,$parameters);
//->files->listFiles($parameters);
printcool ($files);
break;
  //$optParams = array('fullText' => 'free-ebooks');
  //$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);

  foreach ($results as $item) {
    echo $item['volumeInfo']['title'], "<br /> \n";
  }
  
/*
$client = new Google_Client();
// Get your credentials from the console
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setScopes(array('https://www.googleapis.com/auth/drive'));

$service = new Google_Service_Drive($client);
var_dump($serice);
break;
$authUrl = $client->createAuthUrl();

//Request authorization
print "Please visit:\n$authUrl\n\n";
print "Please enter the auth code:\n";
$authCode = trim(fgets(STDIN));

// Exchange authorization code for access token
$accessToken = $client->authenticate($authCode);
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
	
	*/
	
	
	
	
	
	
	
	
	
	}

}