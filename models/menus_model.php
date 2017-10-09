<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Menus_model extends Model 
{
    function Menus_model()
    {
        parent::Model();
    }
	
function DoTracking() 
{
	$this->ip = addslashes(htmlspecialchars($_SERVER['REMOTE_ADDR']));
	if (isset($this->session->userdata['user_id'])) $this->user_id = $this->session->userdata['user_id'];
	else $this->user_id = 0;	
	if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != $this->config->config['base_url'].'/') $this->from = str_replace($this->config->config['base_url'], '', addslashes(htmlspecialchars($_SERVER['HTTP_REFERER'])));
	else $this->from = '>> Begin >> ';	
	$this->to = addslashes(htmlspecialchars($_SERVER['REQUEST_URI']));	
	if (count($_POST) > 0) $this->post = serialize($_POST);
	else $this->post = '';
	$this->db->insert('tracking',array('ip' => $this->ip, 'user_id' => $this->user_id, 'from' => $this->from, 'to' => $this->to, 'timelog' => CurrentTime(), 'post' => $this->post));
	
	/// DELETE OLDER THAN 10 DAYS
	
}
function GetPoll()
	{
		$this->db->select("p_id, title, opt1, opt2, opt3, opt4, opt5, opt6, opt7, opt8, opt9");
		$this->db->where('active', '1');
		$this->query = $this->db->get('poll', 1);
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->row_array();
			
/*			$this->poll = $this->query->row_array();
			$this->db->select("pr_id");
			$this->db->where('p_id', (int)$this->poll['p_id']);
			$this->db->where('ip', $this->input->ip_address());
			$this->pquery = $this->db->get('poll_results', 1);
			
			if ($this->pquery->num_rows() == 0) 
				{
				return $this->poll;
				}*/
			
			}	
			
	}

function GetPollResults()
	{
		
		$this->db->select("p_id, title, opt1, opt2, opt3, opt4, opt5, opt6, opt7, opt8, opt9");
		$this->db->where('active', '1');
		$this->query = $this->db->get('poll', 1);
		if ($this->query->num_rows() > 0) 
			{
			$actpoll = $this->query->row_array();			
	
			$this->db->select("answer");
			$this->db->where('p_id', (int)$actpoll['p_id']);
			$this->db->order_by("answer", "ASC");
			$this->query = $this->db->get('poll_results');
	
			if ($this->query->num_rows() > 0) 
				{
				$this->answers = $this->query->result_array();
	
	
				foreach ($this->answers as $value)
					{
						if (isset($this->answersum[$value['answer']])) $this->answersum[$value['answer']]++;					
						else ($this->answersum[$value['answer']] = 1);					
					}			
				$answers = $this->answersum;
				}	
				
				return array ('actpoll' => $actpoll, 'answers' => $answers);
			}
			

	
	}
	
function GetPartners() {
	
	$this->db->select("rid, title, logo, url");		
		$this->db->where('visible', '1');
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('partners');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
}
function GetTop() {
		
		$this->db->select('f_id, f_cat, f_title');
		$this->db->where('f_top', '1');
		$this->db->where('f_visibility', '1');
		$this->query = $this->db->get('faq');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->result_array();
			}
	}
function GetTopDIY() {
		
		$this->db->select('d_id, d_cat, d_title');
		$this->db->where('d_top', '1');
		$this->db->where('d_visibility', '1');
		$this->db->order_by("d_order", "ASC");
		
		$this->query = $this->db->get('diy');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->result_array();
			}
	}
function GetServices() {
		
		$this->db->select('sid, title');
		$this->db->where('top', '1');
		$this->db->where('visible', '1');
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->result_array();
			}
	}
function CountPollAnswers()
	{
		$this->db->select("opt1, opt2, opt3, opt4, opt5, opt6, opt7, opt8, opt9");
		$this->db->where('active', '1');
		$this->query = $this->db->get('poll', 1);
		if ($this->query->num_rows() > 0) 
			{
			$this->poll =  $this->query->row_array();
			$this->filled = array(
				   '1' => TRUE,
				   '2' => TRUE,
				   '3' => TRUE,
				   '4' => TRUE,
				   '5' => TRUE,
				   '6' => TRUE,
				   '7' => TRUE,
				   '8' => TRUE,
				   '9' => TRUE
				   );
			//printcool ($this->filled);
			if ($this->poll['opt3'] == '') $this->filled['3'] = FALSE;
			if ($this->poll['opt4'] == '') $this->filled['4'] = FALSE;
			if ($this->poll['opt5'] == '') $this->filled['5'] = FALSE;
			if ($this->poll['opt6'] == '') $this->filled['6'] = FALSE;
			if ($this->poll['opt7'] == '') $this->filled['7'] = FALSE;
			if ($this->poll['opt8'] == '') $this->filled['8'] = FALSE;
			if ($this->poll['opt9'] == '') $this->filled['9'] = FALSE;
			//printcool ($this->filled);
			return $this->filled;	
			}	
	}
function GetActivePollID ()
	{
		$this->db->select("p_id");
		$this->db->where('active', '1');
		$this->query = $this->db->get('poll', 1);
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->row_array();	
			}
	}
function CheckIfHasAnswered($ip = '', $activepollid)
	{
			$this->db->select("pr_id");
			$this->db->where('p_id', (int)$activepollid);
			$this->db->where('ip', $ip);
			$this->pquery = $this->db->get('poll_results', 1);
			
			if ($this->pquery->num_rows() > 0) 
				{
				return 	TRUE;
				}
	}
	
function InsertPollAnswer($pid = '', $date = '', $answer = '', $ip = '')
	{
	$this->db->insert('poll_results', array(
											'p_id' => (int)$pid, 
											'date' => $date, 
											'ip' => $ip, 
											'answer' => (int)$answer
											)
					  );			
	}
	
function GetMainMenu($place = '') {
		$this->db->select('s_id, s_type, s_level, s_levelparentid, s_menu, s_seourl, s_title');
		$this->db->where('s_menu', (int)$place);
		$this->db->where('s_visible', '1');
		$this->db->where('s_level', '0');
		$this->db->order_by("s_order", "ASC");
		$this->menu = $this->db->get('navigation');
		
		if ($this->menu->num_rows() > 0) 
			{
				return $this->menu->result_array();
			}
	}
function GetSubMenu ($place = '', $level = '', $s_sef = '', $parentid = '')
	{	
		if ((int)$parentid == 0) $this->id = $this->SefToID($s_sef);		
		else $this->id = (int)$parentid;	
		$this->db->select('s_id, s_type, s_level, s_levelparentid, s_menu, s_seourl, s_title');
		$this->db->where('s_levelparentid', $this->id);
		$this->db->where('s_menu', (int)$place);
		$this->db->where('s_level', strval((int)$level));
		$this->db->where('s_visible', '1');
		$this->db->order_by("s_order", "ASC");
		$this->subquerydata = $this->db->get('navigation');
			if ($this->subquerydata->num_rows() > 0) 
				{
				return $this->subquerydata->result_array();
				}
	}
function GetSubTree ($place) 
	{
	
		$this->db->select('s_id, s_type, s_level, s_levelparentid, s_menu, s_seourl, s_title');
		$this->db->where('s_menu', (int)$place);
		$this->db->where('s_level' , '1');
		$this->db->where('s_visible', '1');
		$this->db->order_by("s_order", "ASC");
		$this->subquerydata = $this->db->get('navigation');
			if ($this->subquerydata->num_rows() > 0) 
				{
				return $this->subquerydata->result_array();
				}
	}
function GetSubSubTree ($place) 
	{
	
		$this->db->select('s_id, s_type, s_level, s_levelparentid, s_menu, s_seourl, s_title');
		$this->db->where('s_menu', (int)$place);
		$this->db->where('s_level' , '2');
		$this->db->where('s_visible', '1');
		$this->db->order_by("s_order", "ASC");
		$this->subquerydata = $this->db->get('navigation');
			if ($this->subquerydata->num_rows() > 0) 
				{
				foreach ($this->subquerydata->result_array() as $value) 
					{
					$return[$value['s_levelparentid']][] = $value;
					}
				return $return;
				}
	}
function SefToID ($sef = '') {
		$this->db->select('s_id');
		$this->db->where('s_seourl', $sef);
		$this->db->where('s_visible', '1');
		$this->subquery = $this->db->get('navigation', 1);
		if ($this->subquery->num_rows() > 0) 
			{
			$this->parid =  $this->subquery->row_array();
			return $this->parid['s_id'];
			}
	
	
}
	function GetParent($parentid) 
	{
		$this->db->select('s_levelparentid, s_seourl');
		$this->db->where('s_id', (int)$parentid);
		$this->db->where('s_visible', '1');
		$this->subquery = $this->db->get('navigation', 1);
		if ($this->subquery->num_rows() > 0) 
			{
			return $this->subquery->row_array();
			}
	}
	function GetParentSef($id) 
	{
		$this->db->select('s_seourl');
		$this->db->where('s_id', (int)$id);
		$this->db->where('s_visible', '1');
		$this->subquery = $this->db->get('navigation', 1);
		if ($this->subquery->num_rows() > 0) 
			{
			$this->sublevel = $this->subquery->row_array();
			//if ($this->sublevelkey['s_type'] == 'm') return $this->sublevelkey['s_id'];
			return $this->sublevel['s_seourl'];
			}
	}
	function GetItem($s_title) 
	{
		$this->db->select('s_id, s_type, s_level, s_levelparentid, s_menu, s_order, s_seourl, s_title, s_visible, s_link, s_body');
		$this->db->where('s_seourl', $s_title);
		$this->db->where('s_visible', '1');
		$this->query = $this->db->get('navigation');
		if ($this->query->num_rows() > 0) 
			{
			$this->itemresult = $this->query->row_array();
			return $this->itemresult;
			}
	}
	
	function Search($string) 
	{
		$this->db->select('s_seourl,  s_title, s_body, s_type, s_menu');
		$this->db->where('s_visible', '1');
		$this->db->where('s_type', 's');
		 $this->db->like('s_title', $string, 'both');
		 $this->db->orlike('s_body', $string, 'both');	
	    $this->db->order_by("s_order", "ASC");
		$this->db->distinct();
		$this->nquery = $this->db->get('navigation');
		if ($this->nquery->num_rows() > 0) 
			{
			return $this->nquery->result_array();			
			}					
	}
function GetOtherSitemapData() 
	{
		//NEWS
		$this->db->select('n_id, n_title, n_desc, n_img, n_date');
		$this->db->where('n_visibility', '1');
		$this->db->order_by("n_date", "DESC");
		$this->nquery = $this->db->get('news');
			if ($this->nquery->num_rows() > 0) 
				{
					$this->sitemapdata['news'] = $this->nquery->result_array();
				}
		//FAQ CATEGORIES
		
		$this->db->select('f_catid, f_cattitle');
		$this->db->order_by("f_cattitle", "ASC");
		$this->fqquery = $this->db->get('faq_categories');
		if ($this->fqquery->num_rows() > 0) 
			{
				$this->sitemapdata['faqcat'] = $this->fqquery->result_array();
			}
		//FAQ
		
		$this->db->select('f_id, f_cat, f_title, f_order, f_visibility,	f_top, f_desc');
		$this->db->where('f_visibility', '1');
		$this->db->order_by("f_order", "ASC");
		$this->fquery = $this->db->get('faq');
		
		if ($this->fquery->num_rows() > 0) 
			{
				foreach ($this->fquery->result_array() as $fkey => $fval) 
				{
				$this->sitemapdata['faq'][$fval['f_cat']][] = $fval;
				}
			
			}
			
		//Services
		
		$this->db->select('sid, title');
		$this->db->where('visible', '1');
		$this->db->order_by("ordering", "ASC");
		
		$this->squery = $this->db->get('solutions');
		if ($this->squery->num_rows() > 0) 
			{
				$this->sitemapdata['services'] = $this->squery->result_array();
			}
			
		//DIY
		
		$this->db->select('d_id, d_cat, d_title');
		$this->db->where('d_top', '1');
		$this->db->where('d_visibility', '1');
		$this->db->order_by("d_order", "ASC");
		
		$this->dquery = $this->db->get('diy');
		
		if ($this->dquery->num_rows() > 0) 
			{
				$this->sitemapdata['diy'] = $this->dquery->result_array();
			}
			
		//Product Categories
		
		$this->db->order_by("p_cattitle", "ASC");
		$this->pcs = $this->db->get('product_categories');
		
		if ($this->pcs ->num_rows() > 0) 
			{
				$this->sitemapdata['prodcats'] = $this->pcs->result_array();
			}
		
		//Product List
		
		$this->db->select('p_id, p_sef, p_title, p_cat');
		$this->db->where('p_visibility', '1');
		$this->db->order_by("p_cat", "ASC");
		$this->db->order_by("p_order", "ASC");
		$this->pquery = $this->db->get('products');
		if ($this->pquery->num_rows() > 0) 
				{
					
					foreach ($this->pquery->result_array() as $pkey => $pval) 
						{
						$this->sitemapdata['products'][$pval['p_cat']][] = $pval;
						}
				}
				
		if (count($this->sitemapdata) > 0) return $this->sitemapdata;
			
	}
function GetNewsData ()
	{	
		$this->db->select('n_id, n_title, n_desc, n_img, n_date');
		$this->db->where('n_top', '1');
		$this->db->where('n_visibility', '1');
		$this->db->order_by("n_date", "DESC");
		$this->nquery = $this->db->get('news', 6);
			if ($this->nquery->num_rows() > 0) 
				{
					$this->newsdata = $this->nquery->result_array();
					$this->newsdata['n_date'] = str_replace('-', '/', $this->newsdata['n_date']);
				}
				
		
		if (isset($this->newsdata)) return $this->newsdata;
	}
	
function GetNews ($id)
	{
		$this->db->select('n_id, n_title, n_desc, n_img, n_date');
		$this->db->where('n_id', (int)$id);
		$this->query = $this->db->get('news', 1);
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->row_array();	
			}
	}
	
	
function ListNews() 
	{
		$this->db->select('n_id, n_title, n_desc, n_img, n_date');
		$this->db->where('n_visibility', '1');
		$this->db->order_by("n_date", "DESC");
		$this->nlquery = $this->db->get('news');
			if ($this->nlquery->num_rows() > 0) 
				{
					$this->newsdata = $this->nlquery->result_array();
							foreach ($this->newsdata as $key => $value) 
									{
										$this->newsdata[$key]['n_date'] = str_replace('-', '/', $value['n_date']);
									}
					return $this->newsdata;	
				}
	}
	
function GetFaq($id = '') {
		
		$this->db->select('f_id, f_cat, f_title, f_order, f_visibility,	f_top, f_desc');
		
		$this->db->where('f_id', (int)$id);
		$this->db->where('f_visibility', '1');
		$this->query = $this->db->get('faq');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
	}
function ListFaq($type) 
	{	
		$this->db->select('f_id, f_cat, f_title, f_order, f_visibility,	f_top, f_desc');
		if ((int)$type > 0) $this->db->where('f_cat', (int)$type);
		$this->db->where('f_visibility', '1');
		$this->db->order_by("f_order", "ASC");
		$this->query = $this->db->get('faq');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->result_array();
			}
	}
function ListFaqCategories() 
	{	
		$this->db->select('f_catid, f_cattitle');
		$this->db->order_by("f_cattitle", "ASC");
		$this->query = $this->db->get('faq_categories');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->result_array();
			}
	}
function GetOpenFaqCat($id = '') {
		$this->db->select('f_catid, f_cattitle');
		$this->db->where('f_catid', (int)$id);
		$this->query = $this->db->get('faq_categories');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
	}
	function UnsubscribeEmail($id = '')
	{
		$this->db->where('n_id', (int)$id);
		$this->result = $this->db->delete('newsletter_subscribers'); 
	}
	
	function FindNewsletterEmail($email) {
		$this->db->select('n_id, n_code');
		$this->db->where('n_email', $email);
		$this->query = $this->db->get('newsletter_subscribers');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
		
	}
	function FindNewsletterCode($code) {
		$this->db->select('n_id, n_email');
		$this->db->where('n_code', $code);
		$this->query = $this->db->get('newsletter_subscribers');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
		
	}
	function SubscribeEmail($email = '')
	{
		$this->db->insert('newsletter_subscribers', array('n_email' => $email, 'n_code' => mt_rand(100000000000000000, 999999999999999999), 'n_regdate' => CurrentTime()));
	}
	
	function GetUserContactData($id = '')
	{
		$this->db->select('user_id, fname, lname, email');
		$this->db->where('user_id', (int)$id);
		$this->query = $this->db->get('users');
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
			
	}
	function SaveContactForm($data) {
		$this->db->insert('form_contact', $data);	
		return $this->db->insert_id();
	}
	
	function InsertHistoryData($history_data) 
	{
	$this->db->insert('admin_history',$history_data);	
	}
	
/////Structure get all function

function GetStructure() {
		$this->mysmarty->assign('mainmenu', array('2' => $this->GetMainMenu('2')));
		$this->mysmarty->assign('topmenu', $this->GetMainMenu('1'));
		$this->mysmarty->assign('footer', $this->GetMainMenu('3'));
		$this->subtree['left'] = $this->GetSubtree(2);		
		$this->subtree['footer'] = $this->GetSubtree(3);	
		$this->mysmarty->assign('subtree', $this->subtree);
		$this->mysmarty->assign('newsdata', $this->GetNewsdata());
		$this->mysmarty->assign('top', $this->GetTop());
		$this->mysmarty->assign('topdiy', $this->GetTopDIY());
		$this->mysmarty->assign('services', $this->GetServices());
		$this->mysmarty->assign('poll', $this->GetPoll());
		$this->mysmarty->assign('partners', $this->GetPartners());
		$this->mysmarty->assign('url', $_SERVER['REQUEST_URI']);
	
}
	
	
}
?>