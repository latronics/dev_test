<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Captcha_model extends Model 
{
    function Captcha_model()
    {
        parent::Model();
		$this->captcha_settings = array(
					'word'		 => rand(1000000,9999999),//'';
					'img_path'	 => './captcha/',
					'img_url'	 => $this->config->config['base_url'].'/captcha/',
					'font_path'	 => './fonts/texb.ttf',
					'img_width'	 => '120',
					'img_height' => 30,
					'expiration' => 3600
				);
    }


function DoCaptcha()
	{
		$this->load->plugin('captcha');	

		$this->captcha = create_captcha($this->captcha_settings);

		$this->mysmarty->assign("captcha", $this->captcha['image']);
		$this->data = array(
					'captcha_id'	=> '',
					'captcha_time'	=> $this->captcha['time'],
					'ip_address'	=> $this->input->ip_address(),
					'word'			=> $this->captcha['word']
				);
		$this->db->insert('captcha', $this->data);
	}
	
function CheckCaptcha($word = '')
	{
	$this->expiration = time()-$this->captcha_settings['expiration'];
	$this->db->where('word', $word); 
	$this->db->where('ip_address', $this->input->ip_address()); 
	$this->db->where('captcha_time >', $this->expiration); 
	$this->db->from('captcha');
	$this->result = $this->db->count_all_results();

	if ($this->result > 0)
	{
		return TRUE;
	}
	else
	{
		return FALSE;	
	}
}

function DeleteOldCaptchas()
	{
		$this->expiration = time()-$this->captcha_settings['expiration']; 
		$this->db->where('captcha_time <', $this->expiration);
		$this->db->delete('captcha');

		/*if( !$dirhandle = @opendir($this->config->config['pathtopublic']) )
              return;
		while( false !== ($filename = readdir($dirhandle)) ) {
               if( $filename != "." && $filename != ".." && $filename != "index.html" ) {
	                 $filename = $this->config->config['pathtopublic']. "/captcha/". $filename;
                          if( @filemtime($filename) < $this->expiration )
                                @unlink($filename);
                        }
             }*/
	}





}
?>
