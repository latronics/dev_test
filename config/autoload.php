<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//$autoload['libraries'] = array('database', 'session', 'xmlrpc');

		// take the referer
		/*$thereferer = strtolower($_SERVER['HTTP_REFERER']);
		// see if it comes from google
		if (strpos($thereferer,"google")) {
			// delete all before q=
			$a = substr($thereferer, strpos($thereferer,"q="));		
			// delete q=
			$a = substr($a,2);
			// delete all FROM the next & onwards
			if (strpos($a,"&")) {
				$a = substr($a, 0,strpos($a,"&"));
			}	
			// we have the results.
			$mygooglekeyword = urldecode($a);
		}	
		*/
/*
if (isset($_SERVER['HTTP_REFERER'])) $thereferer = strtolower($_SERVER['HTTP_REFERER']);
else $thereferer = '';
if (strpos($thereferer,"google")) {
$autoload['libraries'] = array('database','Mysmarty','MY_Language');
}
else
{*/
$autoload['libraries'] = array('database','Mysmarty','session','MY_Language');
/*
}*/
//$autoload['helper'] = array('url', 'file');
$autoload['helper'] = array('url','lazy');
//$autoload['plugin'] = array('captcha', 'js_calendar');
$autoload['plugin'] = array();
//$autoload['model'] = array('model1', 'model2');
$autoload['model'] = array();


/*
|  Auto-load Config files
|  $autoload['config'] = array('config1', 'config2');
|
| NOTE: This item is intended for use ONLY if you have created custom
| config files.  Otherwise, leave it blank.
*/

$autoload['config'] = array();


/*
|  Auto-load Language files
|	$autoload['language'] = array('lang1', 'lang2');
|
| NOTE: Do not include the "_lang" part of your file.  For example 
| "codeigniter_lang.php" would be referenced as array('codeigniter');
*/

$autoload['language'] = array();

/* End of file autoload.php */
/* Location: ./system/application/config/autoload.php */