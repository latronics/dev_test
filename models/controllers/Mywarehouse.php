<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mywarehouse extends Controller {

function Mywarehouse()
	{
		

		//exit('Commiting update, please wait  1-2 mins...');
		parent::Controller();
		
		$this->load->model('Mywarehouse_model'); 
		$this->load->model('Auth_model');
		
		if ($this->router->method != 'Comm') 
		{
		$this->Auth_model->VerifyAdmin();
		$this->Auth_model->CheckWarehouse();	
		
		$showparts = $this->session->userdata('showparts');
		$showparents = $this->session->userdata('showparents');

		if (!$showparts && !$showparents) 
		{
			$this->session->set_userdata('showparents', 1);
			//$this->session->set_userdata('showparts', 1);
			$this->mysmarty->assign('session',$this->session->userdata);
		}
		
		
	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Warehouse');
		$this->mysmarty->assign('hot', TRUE);
		$this->mysmarty->assign('newlayout', TRUE);
		$this->mysmarty->assign('jslog', TRUE);
		}
		$this->load->model('Myseller_model');
		$this->statuses = $this->Myseller_model->assignstatuses();
		$this->warehousefields = array(
						5 => array('sku', 'SKU', '',1),
						2 => array('bcn', 'BCN', 'min_length[8]|max_length[8]|',1),
						1 => array('aucid', 'Auction ID', 'required|',1),
						3 => array('mfgpart', 'MFG Part', 'required|',1),
						4 => array('mfgname', 'MFG Name', '',1),
						6 => array('title', 'Title', 'required|',1),
						7 => array('location', 'Location', '',0),
						8 => array('notes', 'Notes', '',0),
						9 => array('problem', 'Problem', '',0),
						10 => array('tech', 'Tech', '',0),
						11 => array('dates', 'Dates', '',0),
						12 => array('repairlog', 'Repair Log', '',0),
						14 => array('adminid', 'Admin ID', '',0)		
						);
		ksort($this->warehousefields);
		$this->_logallpost();
	

		//if ((int)$this->session->userdata['admin_id'] == 1) printcool ($this->session->userdata);
	}
    function OrderModal() {
        //CALL 365ADMIN DB
        $admin365['admin365'] = $this->load->database('365admin', TRUE);

        $stores = $admin365['admin365']->get("ip_stores")->result_object();

        $data = array(
            'client_name' => $this->input->post('client_name'),
            //'clients' => $this->mdl_clients->get()->result(),
            'stores' => $stores,
            'admin365' => $admin365['admin365']
        );


        $this->load->view('mywarehouse/new_order/modal_create_order', $data);
    }
function Comm()
{

		//echo ($this->router->method);
		
		if (isset($_POST['kp']) && isset($_POST['un']) && isset($_POST['buytype']) && isset($_POST['orderid']))
		{
			$kp = trim($this->input->post('kp', TRUE));	
			if (strlen($kp) != 250) exit('Error 101');
			if ($kp != '/1=?6|[\zb+QQG&v>ZxS9n#r27 \p."UtpJr?!P-AOo%HW[}_m]T{\.}a?ZsVr~k]#wEgk6ry+R|9-!SDr*[R>I>ku23h9f[Pl?k)Rb+qx4O?ZOv-3O_(B&-e$o9b.jEk}xD_x:GU8T/hZvO0 `gLQaM/2aY%W#7MyHS`z2}6wH+j"gK-D$rA9KG3GhB;aBIW,lM@PQ$SL, rx:5t;3]{q;:8Ub>]w{&wX;_a!H."(/zUeyY)6"{{**,j,') exit('Error 102');
			$un = $this->input->post('un', TRUE);	
			if (strlen($un) > 20) exit('Error 103');
			$un = explode('_', $un);			
			$now = mktime();
			if ($un[0] != '365inpl' || !isset($un[1]) || (isset($un[1]) && ($now-(int)$un[1] > 60))) exit('Error 104');
			
			$buytype = (int)$this->input->post('buytype', TRUE);
			$orderid = (int)$this->input->post('orderid', TRUE);
			$buyer = (int)$this->input->post('buyer', TRUE);
			$warehouseid = (int)$this->input->post('warehouseid', TRUE);
			
			if ($buytype == 0 || $orderid == 0/* || $warehouseid === 0*/) exit('Error 105');
			
			GoMail(array ('msg_title' => '365 Post @ '.CurrentTime(), 'msg_body' => printcool ($_POST, true, 'THIAGO POST'), 'msg_date' => CurrentTime()), 'mitko@rusev.me', $this->config->config['no_reply_email']);
			
		/*	$this->BCNSalesAttach()
{
	if (isset($_POST['wid']) && isset($_POST['soldid']) && isset($_POST['subid'] 0 ) && isset($_POST['channel'] 4 ) && isset($_POST['remove'] 0))
	{*/
			
			
			echo json_encode(array('woid' => 666));
			
		}
		else exit('Error 100');
		
		/*
		
		$post = array(
		'kp' => '/1=?6|[\zb+QQG&v>ZxS9n#r27 \p."UtpJr?!P-AOo%HW[}_m]T{\.}a?ZsVr~k]#wEgk6ry+R|9-!SDr*[R>I>ku23h9f[Pl?k)Rb+qx4O?ZOv-3O_(B&-e$o9b.jEk}xD_x:GU8T/hZvO0 `gLQaM/2aY%W#7MyHS`z2}6wH+j"gK-D$rA9KG3GhB;aBIW,lM@PQ$SL, rx:5t;3]{q;:8Ub>]w{&wX_a!H."(/zUeyY)6"{{**,j,',
		'un' => '365inpl_'.mktime(),
    'buytype' => $buytype,
    'orderid' => $orderid,
    'buyer' => $buyer,
    'shipping' => $shipping,
    'warehouse' => array(array('warehouseid' => $wid, 'pricesold' => $pricesold), array('warehouseid' => $wid, 'pricesold' => $pricesold), array('warehouseid' => $wid, 'pricesold' => $pricesold)) /and so on as much bcns as you have/
)

);
*/
}
function experiment()
{
	
	
	
	
	$this->mysmarty->view('mywarehouse/experiment.html');
	
}
function subexperiment()
{
	
	
	
	
	$this->mysmarty->view('mywarehouse/subexperiment.html');
	
}
function BCNselection()
{
	$id = (int)$this->input->post('listingid',true);	
	$this->mysmarty->assign('id', (int)$id);
	$this->mysmarty->assign('hot', TRUE);
	echo $this->mysmarty->fetch('myseller/selection.html');	
	
}
function ReloadBCNS($orderid, $ordertype)
{
	
}
function finddup()
	{
		/*
		$query = $this->db->query('SELECT bcn, COUNT(*) c FROM warehouse GROUP BY bcn HAVING c > 1');
		if ($query->num_rows() > 0) 
		{
			printcool ($query->result_array());
		}
		
		*/
		//if ($this->session->userdata['admin_id'] != 1) exit();
		$bcn = $this->Mywarehouse_model->GetNextBcn(115);
				
		$this->db->select('wid, title, waid, aucid, warranty, deleted, bcn, bcn_p1');
		//if ($this->session->userdata['auclimit'] > 0) $this->db->where('waid', (int)$this->session->userdata['auclimit']);
		$this->db->order_by("wid", "ASC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $k => $v)
			{
				 $bcns[$v['bcn']][$v['wid']] = $v;	
			}	
		}
		echo '<table cellpadding="2" cellspacing="2" border="1"><tr><th>BCN</th><th>Title</th><th>Auction ID</th><th>Auction Title</th><th>New</th><th>Keep</th></tr>';
		foreach ($bcns as $k => $v)
		{
			//printcool (count($bcns[$k]));
			if (count($bcns[$k]) > 1)
			{ 
				foreach ($bcns[$k] as $bk => $bv)
				{
					if (trim($bv['warranty']) != 'KEEP')
					{
					$e = $this->Mywarehouse_model->CheckBCNDoesNotExists('115-'.$bcn);
					if (!$e) 
						{
						//if ($bv['bcn_p1'] != '115')  printcool ($bv['bcn_p1']);
						echo '<tr><td>'.$bv['bcn'].'</td><td>'.$bv['title'].'</td><td>'.$bv['waid'].'</td><td>'.$bv['aucid'].'</td><td style="width:200px;">115-'.$bcn.'</td><td>'.$bv['warranty'].'</td></tr>'; 
						//$this->db->update('warehouse', array('bcn' => '115-'.$bcn, 'bcn_p2' => (int)$bcn, 'oldbcn' => $bv['bcn'].' - Dupped'), array('wid' => $bv['wid']));

						}
						else echo '<tr><td colspan="6"></td></tr>';
					$bcn++;
					}
					else
					echo '<tr><td><strong>'.$bv['bcn'].'</strong></td><td><strong>'.$bv['title'].'</strong></td><td><strong>'.$bv['waid'].'</strong></td><td><strong>'.$bv['aucid'].'</strong></td><td style="width:200px; text-align:right;"><em><strong>'.$bv['bcn'].'</strong></em></td><td><strong>'.$bv['warranty'].'</strong></td></tr>'; 
				}
				//printcool ($bcns[$k]);
			}
		}
		echo '</table>';
		
	}
function logger($id = '')
{
	if (isset($_POST['date'])) $date = $this->input->post('date', TRUE);
	else 
	{
		if ((int)$id == 0) $date = date('m/d/Y');
		else $date = false;	
	}

	if (isset($_POST['admin'])) $admin = $this->input->post('admin', TRUE);
	else $admin = false;
	
	if ((int)$id == 0) $list = $this->Mywarehouse_model->GetLog($date, $admin);
	else $list = $this->Mywarehouse_model->GetBCNLog((int)$id, $date, $admin);
		
	$fielset = array(
		'headers' => "'BCN', 'At','What', 'From', 'To', 'Admin', 'Time'",
		/*'rowheaders' => $list['headers'], */
		'width' => "120, 150, 80, 125, 200, 200, 150", 
		'startcols' => 8, 
		'startrows' => 10, 		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true,renderer: "html"},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');
		
	
	$adms = $this->Mywarehouse_model->GetAdminList();

	if ($list)
		{
			$loaddata = '';
			//['ctrl']
			foreach ($list as $k => $l)
			{
				$loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/bcndetails/".cstr($l['wid'])."\" target=\"_blank\">".cstr($l['bcn'])."</a>',  '<a href=\"".$l['url']."\" target=\"_blank\">".cstr(str_replace('/Mywarehouse/', '', $l['url']))."</a>', '".cstr($l['field'])."', '".cstr($l['datafrom'])."', '".cstr($l['datato'])."', '".cstr($adms[$l['admin']])."', '".cstr($l['time'])."'],
				";				
			}		
		}	
		
		$this->mysmarty->assign('headers', $fielset['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
		$this->mysmarty->assign('width', $fielset['width']);
		$this->mysmarty->assign('startcols', $fielset['startcols']);
		$this->mysmarty->assign('startrows', $fielset['startrows']);
		$this->mysmarty->assign('colmap', $fielset['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));	
		$this->mysmarty->assign('id', (int)$id);	
	
	$this->mysmarty->assign('admins', $adms);
	$this->mysmarty->assign('admin', $admin);
	$this->mysmarty->assign('date', $date);
	$this->mysmarty->assign('cal', TRUE);
	$this->mysmarty->assign('logger', TRUE);
	$this->mysmarty->view('mywarehouse/logger.html');	
}
function order($id = 0)
{
	$noenter = '
<script type="text/javascript"> 

function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey; 
</script> 
';

	$this->load->model('Myseller_model');
	$this->statuses = $this->Myseller_model->assignstatuses();
	$this->mysmarty->assign('nowarehouseorders', TRUE);
	
	if ((int)$id > 0)
	{
			$this->OrderAccounting($id, false, true);
			//$wids = $this->Mywarehouse_model->GetOrderWIDs((int)$id);	
			//$this->mysmarty->assign('wids', $wids);
			//$this->mysmarty->assign('cwids', count($wids));
			$this->mysmarty->assign('go', TRUE);
			$noenter = '';
			$dbo = $this->Mywarehouse_model->GetOrder((int)$id);
		
			$idarray[] = $dbo['woid'];
			$this->load->model('Myseller_model'); 	
			$this->Myseller_model->getSales($idarray, 4);
			
		$this->mysmarty->assign('orderid', (int)$id);
	}
		$o['buyer'] = $this->input->post('buyer', TRUE);
		$o['shipped'] = (float)$this->input->post('shipped', TRUE);
		$o['wholeprice'] = (float)$this->input->post('wholeprice', TRUE);
		$o['notes'] = $this->input->post('notes', TRUE);
		$o['subchannel'] = $this->input->post('subchannel', TRUE);		
		$o['sc_id'] = $this->input->post('sc_id', TRUE);		
		
		if ((int)$id > 0 && !$_POST) $o = $dbo;
		$this->mysmarty->assign('order', $o);
	
	
	$this->load->library('form_validation');
		$this->form_validation->set_rules('buyer', 'Buyer', 'trim|required|xss_clean');
	
	if ($this->form_validation->run() == FALSE)
		{
			$this->mysmarty->assign('errors', $this->form_validation->_error_array);
		}
		else
		{		
			$o['time'] = CurrentTime();
				$o['timemk'] = mktime();
				if ((int)$id == 0)
				{					
					 $this->db->insert('warehouse_orders', $o);
					 $id = $this->db->insert_id();	
				}
				else $this->db->update('warehouse_orders', $o, array('woid' => (int)$id));
				$this->session->set_flashdata('success_msg', 'Complete');
				Redirect('Mywarehouse/order/'.(int)$id);
			
		}
//CALL 365ADMIN DB
    $admin365['admin365'] = $this->load->database('365admin', TRUE);


    //PASSING ITEMS TO VIEW
    $admin365['admin365']->where("invoice_id", $id);
    $items = $admin365['admin365']->get("ip_invoice_items")->result_object();

    //PASSING INVOICE DATA TO VIEW
    $admin365['admin365']->select(" *, ip_invoices.store");
    $admin365['admin365']->where("ip_invoices.invoice_id", $id);
    $admin365['admin365']->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
    $admin365['admin365']->join("ip_invoice_amounts", "ip_invoice_amounts.invoice_id = ip_invoices.invoice_id");
    $admin365['admin365']->join("ip_payments", "ip_payments.invoice_id = ip_invoices.invoice_id", "left");
    $admin365['admin365']->join("ip_invoice_items", "ip_invoice_items.invoice_id = ip_invoices.invoice_id", "left");
    $invoice_data = $admin365['admin365']->get("ip_invoices")->result_object();




    $admin365['items'] = $items;
    $admin365['invoice_id'] = $id;
    $admin365['invoice'] = $invoice_data[0];
    $admin365['payment_methods'] = $admin365['admin365']->get("ip_payment_methods")->result_object();
    $admin365['stores'] = $admin365['admin365']->get("ip_stores")->result_object();


    $this->mysmarty->assign('date', CurrentTime());
	$this->mysmarty->assign('hot', true);
	$this->mysmarty->assign('noenter', $noenter);

	$this->mysmarty->view('mywarehouse/order.html');	
}
function PrintLabel($waid = 0, $wid = 0)
{
	$this->db->select('wid, bcn, title, aucid,mfgpart');
	if ((int)$wid > 0) $this->db->where('wid', (int)$wid);
	else 
	{
		$this->db->where("waid", (int)$waid);
		$this->db->where("deleted", 0);
		$this->db->order_by("wid", "DESC");
	}
	$dataset = array();
	$ds = $this->db->get('warehouse');
	$xml = '<?xml version="1.0" encoding="utf-8"?>
';
	if ($ds->num_rows() > 0)
	{
		foreach ($ds->result_array() as $v) 
		{
/*			$v['xml']= '<?xml version="1.0" encoding="utf-8"?>
<diecutlabel version="8.0" units="twips">
    <paperorientation>Landscape</paperorientation>
    <id>Storage</id>
    <papername>30258 Diskette</papername>
    <drawcommands>
   	 <roundrectangle x="0" y="0" width="3060" height="3960" rx="270" ry="270" />
    </drawcommands>
    <objectinfo>
   	 <barcodeobject>
   		 <name>BCN BARCODE</name>
   		 <forecolor alpha="255" red="0" green="0" blue="0" />
   		 <backcolor alpha="0" red="255" green="255" blue="255" />
   		 <linkedobjectname></linkedobjectname>
   		 <rotation>Rotation0</rotation>
   		 <ismirrored>False</ismirrored>
   		 <isvariable>True</isvariable>
   		 <text>5T32-1000</text>
   		 <type>Code128Auto</type>
   		 <size>Small</size>
   		 <textposition>Top</textposition>
   		 <textfont family="Arial" size="12" bold="True" italic="False" underline="False" strikeout="False" />
   		 <checksumfont family="Arial" size="8" bold="False" italic="False" underline="False" strikeout="False" />
   		 <textembedding>None</textembedding>
   		 <eclevel>0</eclevel>
   		 <horizontalalignment>Center</horizontalalignment>
   		 <quietzonespadding left="0" top="0" right="0" bottom="0" />
   	 </barcodeobject>
   	 <bounds x="317" y="2275" width="2235" height="630" />
    </objectinfo>
    <objectinfo>
   	 <textobject>
   		 <name>TEXT</name>
   		 <forecolor alpha="255" red="0" green="0" blue="0" />
   		 <backcolor alpha="0" red="255" green="255" blue="255" />
   		 <linkedobjectname></linkedobjectname>
   		 <rotation>Rotation0</rotation>
   		 <ismirrored>False</ismirrored>
   		 <isvariable>False</isvariable>
   		 <horizontalalignment>Left</horizontalalignment>
   		 <verticalalignment>Top</verticalalignment>
   		 <textfitmode>None</textfitmode>
   		 <usefullfontheight>True</usefullfontheight>
   		 <verticalized>False</verticalized>
   		 <styledtext>
   			 <element>
   				 <string>Text Text Text Text Text text
Text Text Text Text Text text
</string>
   				 <attributes>
   					 <Font Family="Arial" Size="10" Bold="False" Italic="False" Underline="False" Strikeout="False" />
   					 <forecolor alpha="255" red="0" green="0" blue="0" />
   				 </attributes>
   			 </element>
   		 </styledtext>
   	 </textobject>
   	 <bounds x="317" y="1860" width="2610" height="435" />
    </objectinfo>
    <objectinfo>
   	 <textobject>
   		 <name>AucID</name>
   		 <forecolor alpha="255" red="0" green="0" blue="0" />
   		 <backcolor alpha="0" red="255" green="255" blue="255" />
   		 <linkedobjectname></linkedobjectname>
   		 <rotation>Rotation0</rotation>
   		 <ismirrored>False</ismirrored>
   		 <isvariable>False</isvariable>
   		 <horizontalalignment>Left</horizontalalignment>
   		 <verticalalignment>Top</verticalalignment>
   		 <textfitmode>None</textfitmode>
   		 <usefullfontheight>True</usefullfontheight>
   		 <verticalized>False</verticalized>
   		 <styledtext>
   			 <element>
   				 <string>Auction ID
</string>
   				 <attributes>
   					 <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
   					 <forecolor alpha="255" red="0" green="0" blue="0" />
   				 </attributes>
   			 </element>
   		 </styledtext>
   	 </textobject>
   	 <bounds x="317" y="1365" width="2490" height="240" />
    </objectinfo>
    <objectinfo>
   	 <textobject>
   		 <name>MFGPN</name>
   		 <forecolor alpha="255" red="0" green="0" blue="0" />
   		 <backcolor alpha="0" red="255" green="255" blue="255" />
   		 <linkedobjectname></linkedobjectname>
   		 <rotation>Rotation0</rotation>
   		 <ismirrored>False</ismirrored>
   		 <isvariable>False</isvariable>
   		 <horizontalalignment>Left</horizontalalignment>
   		 <verticalalignment>Top</verticalalignment>
   		 <textfitmode>None</textfitmode>
   		 <usefullfontheight>True</usefullfontheight>
   		 <verticalized>False</verticalized>
   		 <styledtext>
   			 <element>
   				 <string>MFG PN</string>
   				 <attributes>
   					 <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
   					 <forecolor alpha="255" red="0" green="0" blue="0" />
   				 </attributes>
   			 </element>
   		 </styledtext>
   	 </textobject>
   	 <bounds x="317" y="1605" width="2400" height="255" />
    </objectinfo>
</diecutlabel>

';*/
			$dataset[] = $v;	
				
		}
	}	
	$this->mysmarty->assign('dataset', $dataset);
	$this->mysmarty->view('mywarehouse/printlabel.html');	
}
function setinital()
{
	$wnavfrom = $this->session->userdata('wnavfrom');
	$wnavto = $this->session->userdata('wnavto');			
				
	$initial = (int)$this->input->post('initial', TRUE);
	if ($initial == 1) $this->session->set_userdata('winitial', 1);
	else $this->session->unset_userdata('winitial');
	//if ($wnavfrom && $wnavto) echo $_SERVER['HTTP_REFERER'];
	//else echo $initial;
	
	 echo $_SERVER['HTTP_REFERER'];
	
}
function setshowparts()
{			
	$showparts = (int)$this->input->post('showparts', TRUE);

	if ((int)$showparts == 1) $this->session->set_userdata('showparts', 1);
	else
	{
		$this->session->unset_userdata('showparents');
		$this->session->unset_userdata('showparts');
	}
	echo $_SERVER['HTTP_REFERER'];
	
}
function setshowparents()
{	
	$showparents = (int)$this->input->post('showparents', TRUE);
	
	if ($showparents == 1) $this->session->set_userdata('showparents', 1);
	else 
	{
		$this->session->set_userdata('showparts', 1);
		$this->session->unset_userdata('showparents');
	}
	echo $_SERVER['HTTP_REFERER'];
	
}
function SetNav($clean=false)
	{
		if (!$clean)
		{
			$this->session->set_userdata('wnavfrom', $this->input->post('ofrom', TRUE));	
			$this->session->set_userdata('wnavto',  $this->input->post('oto', TRUE));			
		}
		else
		{
			$this->session->unset_userdata('wnavfrom');
			$this->session->unset_userdata('wnavto');
			
		}	
		if (isset($_POST['status'])) $this->session->set_userdata('showstatus', trim($this->input->post('status',true)));
		header('location: ' .$_SERVER['HTTP_REFERER']);				
	}
function DoAudit($quiet = false)
{
	if ((int)$this->input->post('id') == 0) echo 'ERROR';
	$audit = CurrentTime();	
	$date = explode (' ',$audit);
	$time = explode (':', $date[1]);
	$date = explode ('-',$date[0]);
	$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
	$this->db->update('warehouse', array('audit' => $audit, 'auditmk' => $mk), array('wid' => (int)$this->input->post('id')));
	$data = $this->Mywarehouse_model->GetBCNListingID((int)$this->input->post('id'));
	$this->db->insert('warehouse_audits', array('action_id' => (int)$this->input->post('id'), 'wlabel' => $data['bcn'], 'cur_eid' => $data['cur_eid'], 'wtime' => $audit, 'admin' => (int)$this->session->userdata['admin_id']));
	if (!$quiet) echo ($audit);	
}
function DolAudit()
{
	if ((int)$this->input->post('id') == 0) echo 'ERROR';
	$audit = CurrentTime();
	$date = explode (' ',$audit);
	$time = explode (':', $date[1]);
	$date = explode ('-',$date[0]);
	$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
	$this->db->update('ebay', array('audit' => $audit, 'auditmk' => $mk), array('e_id' => (int)$this->input->post('id')));
	$this->db->insert('warehouse_audits', array('wlisting' => 1, 'action_id' => (int)$this->input->post('id'), 'cur_eid' => (int)$this->input->post('id'), 'wtime' => $audit, 'admin' => (int)$this->session->userdata['admin_id']));
	echo ($audit);	
}
function UpdateLocations()
{
	if ((int)$this->input->post('id') == 0) exit ('ERROR');
	$listingid = (int)$this->input->post('id');
	$location = trim($this->input->post('value', true));
	$dbdata = $this->Myseller_model->getBase(array((int)$listingid), true);
	$ids = array();
	if (count($dbdata) > 0)		foreach ($dbdata as $wid)
			{
				if ($location != $wid['location'])
				{
					$audit = CurrentTime();	
					$date = explode (' ',$audit);
					$time = explode (':', $date[1]);
					$date = explode ('-',$date[0]);
					$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
					
					$this->db->update('warehouse', array('location' => $location, 'audit' => $audit, 'auditmk' => $mk), array('wid' => (int)$wid['wid']));	
							
			 		$this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'location', $wid['location'], $location);	
					$this->Mywarehouse_model->DoLocation($location, (int)$wid['wid']);
					$ids[$wid['wid']] = true ;				
				}
			}
	echo json_encode($ids);
}
function DomlAudit()
{
	if ((int)$this->input->post('id') == 0) exit ('ERROR');
	$listingid = (int)$this->input->post('id');
	$audit = CurrentTime();	
	$date = explode (' ',$audit);
	$time = explode (':', $date[1]);
	$date = explode ('-',$date[0]);
	$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
	$dbdata = $this->Myseller_model->getBase(array((int)$listingid), true);
	$ids = array();
	if (count($dbdata) > 0)		foreach ($dbdata as $wid)
			{
				if ($audit != $wid['audit'])
				{
					$this->db->update('warehouse', array('audit' => $audit, 'auditmk' => $mk), array('wid' => (int)$wid['wid']));
					$this->db->insert('warehouse_audits', array('action_id' => (int)$wid['wid'], 'wlabel' => $wid['bcn'], 'cur_eid' => (int)$wid['listingid'], 'wtime' => $audit, 'admin' => (int)$this->session->userdata['admin_id']));						 		
					$ids[$wid['wid']] = true ;				
				}
			}
	echo json_encode(array('ids' => $ids, 'value' => $audit));
}
function MassAudit($smk = '', $checked = false)
{
	if(isset($_POST) && $_POST)
	{	
		$audit = CurrentTime();	
		$date = explode (' ',$audit);
		$time = explode (':', $date[1]);
		$date = explode ('-',$date[0]);
		$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
		
		
		if ($checked == 'PRINT')
		{
			$print = TRUE;
			$checked = false;	
		}
		
		
		if ((bool)$checked !== false && isset($_POST['changes']) && is_array($_POST['changes']))
		{
			$sessback = $this->_loadsession($this->session->userdata('sessfile'));
			$saveid = $sessback['acclot'];
			$saverel = $sessback['accrel'];
			
			$changes = $_POST['changes'];
	
			if (is_array($_POST['changes'])) { foreach($_POST['changes'] as $d)
			{
				//printcool ($d);
				if ($d[3] == 1) echo 1111111;
				if (count($d) == 4)
				{																								
					if ($d[3] == 1) $updt['mass_sel'] = (int)$smk;
					else $updt['mass_sel'] = NULL;
					//printcool ($updt);
					echo  '*'.$saverel[(int)$d[0]]['wid'];
					$this->db->update('warehouse', $updt, array('wid' => $saverel[(int)$d[0]]['wid']));
					unset($updt);
				}
			}}
		exit('OK');	
		}
		
		$parseddata = explode(PHP_EOL, trim($this->input->post('bcns', TRUE)));
		foreach ($parseddata as $p)
		{
			if (trim($p) != '') $inputbcns[] = $p;	
		}
	
		$this->mysmarty->assign('bcns', trim($this->input->post('bcns', TRUE)));
		if (isset($inputbcns))
		{	
			$sql = 'SELECT w.wid, w.bcn, w.bcn_p1, w.bcn_p2, w.bcn_p3, w.title, w.status, w.listingid, w.location, w.sku, w.psku, w.audit, w.mass_sel, w.sold_id, w.channel, w.aucid, w.mfgpart, e.ebended, e.e_id FROM (warehouse w) LEFT JOIN ebay e ON `e`.`e_id` = `w`.listingid WHERE `w`.`deleted` = 0 AND `w`.`nr` = 0  AND (';			
		
			$c = 0;
			foreach ($inputbcns as $f)
			{				
				if ($c == 0)  $sql .= '`w`.`bcn` = "'.trim($f).'" OR `w`.`lot` = "'.trim($f).'" OR `w`.`oldbcn` = "'.trim($f).'"';
				else $sql .= ' OR `w`.`bcn` = "'.trim($f).'" OR `w`.`lot` = "'.trim($f).'" OR `w`.`oldbcn` = "'.trim($f).'"';
				$c++;
			}
				$sql .= ')';
				
			$q =  $this->db->query($sql);
				
			if ($q->num_rows() > 0)
			{
				$found = $q->result_array();
				
				if ((int)$smk > 0) $sesmk = (int)$smk;
				else $sesmk = mktime();
				
				if (isset($_POST['parts']))
				{
					$this->mysmarty->assign('partschecked', TRUE);
					$sql = 'SELECT w.wid, w.bcn, w.bcn_p1, w.bcn_p2, w.bcn_p3, w.title, w.status, w.listingid, w.location,  w.sku, w.psku, w.audit, w.mass_sel, w.sold_id, w.channel, w.aucid, w.mfgpart , e.ebended, e.e_id FROM (warehouse w) LEFT JOIN ebay e ON `e`.`e_id` = `w`.listingid WHERE `w`.`deleted` = 0 AND `w`.`nr` = 0  AND (';	
					$c = 0;
					foreach ($found as $f)
					{
						if (trim($f['bcn_p2']) == '')
						{
							if ($c == 0)  $sql .= '(`w`.`bcn_p1` = "'.trim($f['bcn_p1']).'")';
							else $sql .= ' OR (`w`.`bcn_p1` = "'.trim($f['bcn_p1']).'")';
						}
						else
						{
							if ($c == 0)  $sql .= '(`w`.`bcn_p1` = "'.trim($f['bcn_p1']).'" AND `w`.`bcn_p2` = "'.trim($f['bcn_p2']).'")';
							else $sql .= ' OR (`w`.`bcn_p1` = "'.trim($f['bcn_p1']).'" AND `w`.`bcn_p2` = "'.trim($f['bcn_p2']).'")';
						}
						$c++;
					}
					$sql .= ') AND `w`.`bcn_p3` IS NOT NULL';
					$p =  $this->db->query($sql);
				
					if ($p->num_rows() > 0)
					{
						foreach ($p->result_array() as $part)
						{
							$found[] = $part;	
						}
					}
					
				}
				if (isset($_POST['butact']) && $_POST['butact'] == 'Change SKU')							
				{
					$sku = trim($this->input->post('sku', TRUE));
					
					$this->db->select('wsid, is_p, parent, name');
					$this->db->where('name', $sku);
					$ss = $this->db->get('warehouse_sku');
					if ($ss->num_rows() > 0) $foundsku = $ss->row_array();
					
				}
				foreach ($inputbcns as $i => $v)
				{
				foreach ($found as $k => $fnd)
				{
					
						if ($fnd['bcn_p3'] != '')
						{
							if ($fnd['bcn_p3'] != '' && $fnd['bcn_p2'] != '')  $bcn = $fnd['bcn_p1'].'-'.$fnd['bcn_p2'].'-'.$fnd['bcn_p3'];	
							elseif ($fnd['bcn_p3'] != '' && $fnd['bcn_p2'] == '')$bcn = $fnd['bcn_p1'];	
							elseif ($fnd['bcn_p2'] != '') $bcn = $fnd['bcn_p1'].'-'.$fnd['bcn_p2'];						
							else $bcn = $fnd['bcn_p1'];
							
							if ($fnd['bcn_p3'] != '' && $fnd['bcn_p2'] != '')   $parent = $fnd['bcn_p1'].'-'.$fnd['bcn_p2'];	
							elseif ($fnd['bcn_p3'] != '' && $fnd['bcn_p2'] == '')$parent = $fnd['bcn_p1'];	
							elseif ($fnd['bcn_p2'] != '')  $parent = $fnd['bcn_p1'].'-'.$fnd['bcn_p2'];						
							else  $parent = $fnd['bcn_p1'];
							
						}
						
						else $parent = $bcn = $fnd['bcn'];
						//printcool ($parent);
						if ((strtolower($bcn) == strtolower($inputbcns[$i])) || (strtolower($parent) == strtolower($inputbcns[$i])))
						{
							if (isset($_POST['butact']) && $_POST['butact'] == 'Audit Checked' && (int)$fnd['mass_sel'] == $sesmk)
								{							
									$this->db->update('warehouse', array('audit' => $audit, 'auditmk' => $mk), array('wid' => (int)$fnd['wid']));
									$fnd['audit'] = $audit;
								}
							if (isset($_POST['butact']) && $_POST['butact'] == 'Change Location'  && (int)$fnd['mass_sel'] == $sesmk)							
								{
									$location = trim(ucwords($this->input->post('location', TRUE)));
									if ($location != $fnd['location'])
										{					
											$this->db->update('warehouse', array('location' => $location), array('wid' => (int)$fnd['wid']));								
											$this->Auth_model->wlog($fnd['bcn'], $fnd['wid'], 'location', $fnd['location'], $location);	
											$this->Mywarehouse_model->DoLocation($location, (int)$fnd['wid']);	
											$fnd['location'] = $location;
										}			 
								}
							
							if (isset($_POST['butact']) && $_POST['butact'] == 'Change SKU'  && (int)$fnd['mass_sel'] == $sesmk)							
								{
									if (isset($foundsku))
									{
										if ($foundsku['is_p'] == 1)
										{
											if ($foundsku['wsid'] != $fnd['psku'])
											{					
												$this->db->update('warehouse', array('psku' => $foundsku['wsid'], 'sku' => $foundsku['parent']), array('wid' => (int)$fnd['wid']));								
												$this->Auth_model->wlog($fnd['bcn'], $fnd['wid'], 'psku', $fnd['psku'], $foundsku['wsid']);	
												$this->Auth_model->wlog($fnd['bcn'], $fnd['wid'], 'sku', $fnd['sku'], $foundsku['parent']);	
												
												$fnd['psku'] = $foundsku['wsid'];
												$fnd['sku'] = $foundsku['parent'];
											}
										}
										else
										{
											if ($foundsku['wsid'] != $fnd['sku'])
											{					
												$this->db->update('warehouse', array('sku' => $foundsku['wsid']), array('wid' => (int)$fnd['wid']));								
												$this->Auth_model->wlog($fnd['bcn'], $fnd['wid'], 'sku', $fnd['sku'], $foundsku['wsid']);
												
												$fnd['sku'] = $foundsku['wsid'];
												
											}
										}												 
									}
								}
							if (isset($_POST['butact']) && $_POST['butact'] == 'Print Checked' && (int)$fnd['mass_sel'] == $sesmk)
							{
								
								//$dataset[$i]['queprint'] = 1;
								$dataset[$k]['bcn'] = $fnd['bcn'];
								$dataset[$k]['ptitle'] = substr($fnd['title'], 0, 25);
								$dataset[$k]['ptitle2'] = substr($fnd['title'], 25, 25);
								$dataset[$k]['aucid'] = $fnd['aucid'];
								$dataset[$k]['mfgpart'] = $fnd['mfgpart'];
								$dataset[$k]['wid'] = $fnd['wid'];
							}	
 
							 $ordered[$fnd['wid']] = $fnd;
						}
				}
				}
			
				//printcool ($found);
				//printcool ($ordered);
				$fieldset = array(
				'headers' => "'GO', 'Checked', 'BCN','Listing Status', 'Listing ID','SKU', 'PSKU','Location','Audit','Title'",
				'width' => "60, 60, 100, 180, 120,100,100, 125,145,  500", 
				'startcols' => 7,
				'startrows' => count($found), 
				'colmap' => '{readOnly: true, renderer: "html"},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}'
				);
				
				
				
				if ($ordered) foreach ($ordered as $k => $fnd)
				{
					if ((int)$fnd['mass_sel'] == $sesmk) $fnd['mass_sel'] = 1;
					else $fnd['mass_sel'] = 0;
					
					$h[] = array('wid' => $fnd['wid'], 'bcn' => $fnd['bcn']);	
					
					$liststatus = '';
					if ($fnd['e_id'] == '') $liststatus = 'No Listing / ';
					elseif ($fnd['ebended'] == '') $liststatus = 'Active / ';
					elseif ($fnd['ebended'] != '') $liststatus = 'Ended / ';
								
					$loaddata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($fnd['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($fnd['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($fnd['sold_id'])."/".cstr($fnd['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>",$fnd['mass_sel'],cstr($fnd['bcn']),$liststatus.cstr($fnd['status']), cstr($fnd['listingid']), cstr($fnd['sku']), cstr($fnd['psku']),cstr($fnd['location']),cstr($fnd['audit']),cstr($fnd['title']));
					
				}
				
				$sesfile = $this->_savesession(array('accrel' => $h, 'acclot' => ''));
				$this->session->set_userdata(array('sessfile' => $sesfile));
				
				$this->mysmarty->assign('headers', $fieldset['headers']);
				$this->mysmarty->assign('rowheaders', $fieldset['rowheaders']);
				$this->mysmarty->assign('width', $fieldset['width']);
				$this->mysmarty->assign('startcols', $fieldset['startcols']);
				$this->mysmarty->assign('startrows', $fieldset['startrows']);
				$this->mysmarty->assign('colmap', $fieldset['colmap']);
				$this->mysmarty->assign('loaddata', json_encode($loaddata));		
				$this->mysmarty->assign('copyrows', count($ordered));
				
				$this->mysmarty->assign('sesmk', $sesmk);
				
				$this->mysmarty->assign('hot', TRUE);
				
				if (isset($dataset))
				{
					$this->mysmarty->assign('dataset', $dataset);
					$this->mysmarty->assign('noprintbutton', TRUE);
					
					$this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printlabel.html'));	
				}			
				
			}
		}
	}
	
	$this->mysmarty->view('mywarehouse/massaudit.html');	
	
}
function MoveLocation($location = 0, $tocontainer = 0)
{
 	if ((int)$location > 0)
	{
		$this->db->update('locations', array('cabid' => (int)$tocontainer),  array('loc_id' => (int)$location));
		echo 1;		
	}
 
}
function LocationManager($loc_id = 0, $cont_id = 0, $print=false)
{
	$this->load->model('Mywarehouselocations_model'); 
	
	$actionmsg = '';
	if ($_POST)
	{
		$formdata = array(
		'cont_id' => (int)trim($this->input->post('cont_id',true)),
		'addbcns' => trim($this->input->post('addbcns',true)),
		'loc_name' => ucwords(trim($this->input->post('loc_name',true)))		
		);
		$this->mysmarty->assign('formdata', $formdata);

	if (isset($_POST['action_button']))
	{
		if ($_POST['action_button'] == 'Create Container')
		{
			$act_loc_id = $this->Mywarehouselocations_model->GetLocation(ucwords($formdata['loc_name']));
			if (!$act_loc_id) 
			{
				$act_loc_id = $this->Mywarehouselocations_model->NewLocation($formdata['loc_name']);
				$actionmsg = 'Created New Location '.$formdata['loc_name'].'<br>';
			}
			
			$act_cont_id = $this->Mywarehouselocations_model->NewContainer($act_loc_id);
			$actionmsg .= 'Created New Container '.$act_cont_id.'<br>';				
			
			$this->Mywarehouselocations_model->AddBCNs($formdata['addbcns'], $act_cont_id, $act_loc_id);
			if ($formdata['addbcns'] != '') $actionmsg .= 'Added BCNS to Container '.$act_cont_id.'<br>';			
			
			$this->session->set_flashdata('success_msg', $_POST['action_button'].' Result:<br><br>'.$actionmsg);
			
			if (isset($_POST['ccprint']) && (int)$_POST['ccprint'] == 1) $print = 1;
			Redirect('Mywarehouse/LocationManager/'.$act_loc_id.'/'.$act_cont_id.'/'.$print);
		}
		
		if ($_POST['action_button'] == 'Move Container')
		{
			
			$act_cont_id = $this->Mywarehouselocations_model->GetContainer($formdata['cont_id']);
					
			if (!$act_cont_id) 
			{
				$this->mysmarty->assign('nferror', 'Container '.$formdata['cont_id'].' Doesn\'t Exist<br>');	
			}
			else
			{
				
				$act_loc_id = $this->Mywarehouselocations_model->GetLocation(ucwords($formdata['loc_name']));
			
				if (!$act_loc_id) 
				{
					$act_loc_id = $this->Mywarehouselocations_model->NewLocation(ucwords($formdata['loc_name']));
					$actionmsg = 'Created New Location '.$formdata['loc_name'].'<br>';
				}
				$cnt = $this->Mywarehouselocations_model->ChangeBCNsContainer($act_cont_id, $act_loc_id);	
				$this->Mywarehouselocations_model->UpdateContainer($act_cont_id, $act_loc_id);					
				$actionmsg .= 'Moved Container '.$act_cont_id.' to '.$formdata['loc_name'].'<br>';
				$actionmsg .= 'Moved '.$cnt.' BCNs<br>';
				$this->session->set_flashdata('success_msg', $_POST['action_button'].' Result:<br><br>'.$actionmsg);
				Redirect('Mywarehouse/LocationManager/'.$act_loc_id.'/'.$act_cont_id);	
			}
		}
		if ($_POST['action_button'] == 'Add To Container')
		{
	
			$act_cont_id = $this->Mywarehouselocations_model->GetContainer($formdata['cont_id']);		
			if (!$act_cont_id) 
			{
				$this->mysmarty->assign('nferror', 'Container '.$formdata['cont_id'].' Doesn\'t Exist<br>');
			}
			else
			{
				$act_loc_id = $this->Mywarehouselocations_model->GetContainerLocation($act_cont_id);				
				$cnt = $this->Mywarehouselocations_model->AddBCNs($formdata['addbcns'], $act_cont_id, $act_loc_id);
				$actionmsg .= 'Added '.$cnt.' BCNS to Container '.$act_cont_id.'<br>';				
				$this->session->set_flashdata('success_msg', $_POST['action_button'].' Result:<br><br>'.$actionmsg);
				Redirect('Mywarehouse/LocationManager/'.$act_loc_id.'/'.$act_cont_id);
			}
			
		}
		if ($_POST['action_button'] == 'Add To Location')
		{
			$act_loc_id = $this->Mywarehouselocations_model->GetLocation($formdata['loc_name']);
			if (!$act_loc_id) 
			{
				$act_loc_id = $this->Mywarehouselocations_model->NewLocation($formdata['loc_name']);
				$actionmsg = 'Created New Location '.$formdata['loc_name'].'<br>';
			}
			
			$cnt = $this->Mywarehouselocations_model->AddBCNs($formdata['addbcns'], 0, $act_loc_id);
			$actionmsg .= 'Added '.$cnt.' BCNS to Location '.$formdata['loc_name'].'<br>';
			$this->session->set_flashdata('success_msg', $_POST['action_button'].' Result:<br><br>'.$actionmsg);
			Redirect('Mywarehouse/LocationManager/'.$act_loc_id);	
		}		
	}

	if (isset($_POST['locationlookup']) && $_POST['locationlookup'] != '')
		{
			$loc_id = $this->Mywarehouselocations_model->GetLocation(ucwords($formdata['loc_name']));
			if ($loc_id) Redirect('Mywarehouse/LocationManager/'.$loc_id);
			else 
			{				
				$actionmsg .= 'Location '.$$formdata['loc_name'].' Not Found<br>';
				$this->session->set_flashdata('success_msg', $_POST['action_button'].' Result:<br><br>'.$actionmsg);				
			}
		}
	}

	$loc_bcns = false;
	$cont_bcns = false;
	if ((int)$loc_id > 0)
	{
		 $loc_bcns = $this->Mywarehouselocations_model->GetLocationBCNs($loc_id);
		 $lloaddata = array();
		if ($loc_bcns) foreach ($loc_bcns as $lb)
		{
			$liststatus = '';
			if ($lb['e_id'] == '') $liststatus = 'No Listing / ';
			elseif ($lb['ebended'] == '') $liststatus = 'Active / ';
			elseif ($lb['ebended'] != '') $liststatus = 'Ended / ';
		$lloaddata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($lb['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($lb['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($lb['sold_id'])."/".cstr($lb['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>",cstr($lb['bcn']),cstr($lb['cont_id']),$liststatus.cstr($lb['status']), cstr($lb['listingid']),cstr($lb['title']));
		}
	}
	if ((int)$cont_id > 0)
	{
		$cont_bcns = $this->Mywarehouselocations_model->GetContainerBCNs($cont_id);
		$cloaddata = array();
		if ($cont_bcns) foreach ($cont_bcns as $cb)
		{
			$liststatus = '';
			if ($cb['e_id'] == '') $liststatus = 'No Listing / ';
			elseif ($cb['ebended'] == '') $liststatus = 'Active / ';
			elseif ($cb['ebended'] != '') $liststatus = 'Ended / ';
		$cloaddata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($cb['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($cb['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($cb['sold_id'])."/".cstr($cb['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>",cstr($cb['bcn']),$liststatus.cstr($cb['status']), cstr($cb['listingid']),cstr($cb['title']));
		}

	}
	
				$lfieldset = array(
				'headers' => "'GO', 'BCN','Container', 'Listing Status', 'Listing ID','Title'",
				'width' => "60, 100, 100, 180, 125, 500", 
				'startcols' => 6,
				'startrows' => count($lloaddata), 
				'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"}'
				);

				$this->mysmarty->assign('lheaders', $lfieldset['headers']);
				$this->mysmarty->assign('lrowheaders', $lfieldset['rowheaders']);
				$this->mysmarty->assign('lwidth', $lfieldset['width']);
				$this->mysmarty->assign('lstartcols', $lfieldset['startcols']);
				$this->mysmarty->assign('lstartrows', $lfieldset['startrows']);
				$this->mysmarty->assign('lcolmap', $lfieldset['colmap']);
				$this->mysmarty->assign('lloaddata', json_encode($lloaddata));		
				$this->mysmarty->assign('lcopyrows', count($loc_bcns));
				
				$this->mysmarty->assign('hot', TRUE);
				
				$cfieldset = array(
				'headers' => "'GO', 'BCN', 'Listing Status', 'Listing ID','Title'",
				'width' => "60, 100,  180, 125, 500", 
				'startcols' => 5,
				'startrows' => count($cloaddata), 
				'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"}'
				);

				$this->mysmarty->assign('cheaders', $cfieldset['headers']);
				$this->mysmarty->assign('crowheaders', $cfieldset['rowheaders']);
				$this->mysmarty->assign('cwidth', $cfieldset['width']);
				$this->mysmarty->assign('cstartcols', $cfieldset['startcols']);
				$this->mysmarty->assign('cstartrows', $cfieldset['startrows']);
				$this->mysmarty->assign('ccolmap', $cfieldset['colmap']);
				$this->mysmarty->assign('cloaddata', json_encode($cloaddata));		
				$this->mysmarty->assign('ccopyrows', count($cont_bcns));
				
				$this->mysmarty->assign('hot', TRUE);
	
	$this->db->order_by('cab_order', 'ASC');
	$cb = $this->db->get('locations_cabinets');
	$cabs[0] = 'No Cabinet';
	
	if ($cb->num_rows() > 0)
	{
		foreach ($cb->result_array() as $c)
		{
			$cabs[$c['cabid']] = $c['cab_name'];
		}
	}	
	$this->mysmarty->assign('cabs', $cabs);
	$nav = $this->Mywarehouselocations_model->GetNavigation();
	$this->mysmarty->assign('nav', $nav);
	$this->mysmarty->assign('loc_bcns', $loc_bcns);	
	$this->mysmarty->assign('cont_bcns', $cont_bcns);	
	$this->mysmarty->assign('loc_id', $loc_id);	
	$this->mysmarty->assign('cont_id', $cont_id);
	
	$this->mysmarty->assign('actionmsg', $actionmsg);
	
	$this->mysmarty->assign('nextcontainer', $this->Mywarehouselocations_model->NextContainer());	
	$sessbcns = $this->session->flashdata('sessbcns', $sessbcns);
	
	
	
	if($print) $this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printcontainer.html'));	
	
	if (is_array($sessbcns) && count($sessbcns) > 0)
	{
		//$this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printcontainer.html'));	
		/*$sbcn = $this->Mywarehouselocations_model->GetSessBCNs($sessbcns);
	
		if ($sbcn)
		{
			foreach ($sbcn as $i => $fnd)
			{
				$dataset[$i]['bcn'] = $fnd['bcn'];
				$dataset[$i]['ptitle'] = substr($fnd['title'], 0, 25);
				$dataset[$i]['ptitle2'] = substr($fnd['title'], 25, 25);
				$dataset[$i]['aucid'] = $fnd['aucid'];
				$dataset[$i]['mfgpart'] = $fnd['mfgpart'];
				$dataset[$i]['wid'] = $fnd['wid'];
			}
	
			if (isset($dataset))
				{
					$this->mysmarty->assign('dataset', $dataset);
					$this->mysmarty->assign('noprintbutton', TRUE);
					$this->mysmarty->assign('containerlaber', TRUE);
					$this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printlabel.html'));	
				}	
		}*/
	}
	$this->mysmarty->view('mywarehouse/locationmanager.html');	
}
function DeleteCabinet($cabid)
{
	if ((int)$cabid > 0)
	{
		$this->db->select('loc_id');
		$this->db->where('cabid', (int)$cabid);

		$ldb =$this->db->get('locations');
		if ($ldb->num_rows() > 0)
		{
			foreach ($ldb->result_array() as $l)
			{
				$loc[] = $l['loc_id'];	
			}
			$this->db->update('locations', array('cabid' => 0), array('cabid' => (int)$cabid));			 
			echo json_encode($loc);
		}
		$this->db->where('cabid', (int)$cabid);
		$this->db->delete('locations_cabinets');
	}
}
function Cabinets($loc = 0, $cab = 0)
{
	
	if ((int)$loc > 0)
	{
		if((int)$cab !=0 ) $cab = (int)$this->input->post('cabid', TRUE);
		$this->db->update('locations', array('cabid' => (int)$cab), array('loc_id' => (int)$loc));	
	}
	
	
	$this->db->order_by('cab_order', 'ASC');
	$cb = $this->db->get('locations_cabinets');
	$cabs[0] = array('cab_name' => 'No Cabinet', 'cab_order' => 0);
	$cabnext= 0;
	if ($cb->num_rows() > 0)
	{
		foreach ($cb->result_array() as $c)
		{
			$cabs[$c['cabid']]	= array('cab_name' => $c['cab_name'], 'cab_order' => $c['cab_order']);
			if ($c['cab_order'] > $cabnext) $cabnext = $c['cab_order'];
		}
		$cabnext++;
	}	$this->mysmarty->assign('cabs', $cabs); 
	$this->mysmarty->assign('cabnext', $cabnext);
	$lc = $this->db->get('locations');
	
	if ($lc->num_rows() > 0)
	{
		foreach ($lc->result_array() as $l)
		{
			$locs[$l['cabid']][$l['loc_id']] = $l['loc_name'];
		}
		
	} $this->mysmarty->assign('locs', $locs);
	$this->mysmarty->view('mywarehouse/locationcabinets.html');
}	
function SaveCabinet($id)
{
	if ((int)$id == 0) $this->db->insert('locations_cabinets', array('cab_name' => $this->input->post('cab_name', TRUE), 'cab_order' => (int)$this->input->post('cab_order', TRUE)));
	else $this->db->update('locations_cabinets', array('cab_name' => $this->input->post('cab_name', TRUE), 'cab_order' => (int)$this->input->post('cab_order', TRUE)), array('cabid' => (int)$id));
	Redirect('Mywarehouse/Cabinets');
}
function UpdateLocationName($locid)
{
	$this->db->update('locations', array('loc_name' => trim($this->input->post('val',TRUE))), array('loc_id' => (int)$locid));	
}
function UpdateContainerName($contid)
{
	$this->db->update('locations_containers', array('cont_name' => trim($this->input->post('val',TRUE))), array('cont_id' => (int)$contid));	
}
function partingsearchforlistings($id = '', $skuedit = false)
{
	if ($id != '')
	{
	if (isset($_POST['fieldvalue']))
	{
		$this->mysmarty->assign('search', trim($_POST['fieldvalue']));
		$this->mysmarty->assign('res', $this->Mywarehouse_model->SearchListings(trim($_POST['fieldvalue'])));
	}
	$this->mysmarty->assign('id', trim($id));
	if ($skuedit && $skuedit != 'undefined') $this->mysmarty->assign('skuedit', 1);
	echo $this->mysmarty->fetch('mywarehouse/parting_search.html');		
	
	}
	else echo 'Error';	
}
function partingpopulatelocation($listing)
{
	if (isset($_POST['location']))
	{
		$location = trim(ucwords($this->input->post('location', true)));
		$dbdata = $this->Myseller_model->getBase(array((int)$listing), true);
		if (count($dbdata) > 0)		foreach ($dbdata as $wid)
		{
			if ($location != $wid['location'])
			{
				
				$audit = CurrentTime();	
				$date = explode (' ',$audit);
				$time = explode (':', $date[1]);
				$date = explode ('-',$date[0]);
				$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);

				$this->db->update('warehouse', array('location' => $location, 'audit' => $audit, 'auditmk' => $mk), array('wid' => (int)$wid['wid']));	
				$this->Mywarehouse_model->DoLocation($location, (int)$wid['wid']);		
				$this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'location', $wid['location'], $location);	
			}
		}

		$this->mysmarty->assign('id', (int)$id);
		$this->mysmarty->assign('eid', (int)$listing);
		$res = $this->Mywarehouse_model->getbcnsforskulisting($id, $listing);
		$this->mysmarty->assign('res', $res);
		
		$this->load->model('Myseller_model');
		$this->Myseller_model->assignstatuses();
		$this->Myseller_model->assignchannels();
		$this->mysmarty->assign('parting', TRUE);
		echo json_encode(array('html'=> $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int)$id), 'bcncnt'=> json_encode(count($res))));	
		
		
	}	
}
function Multipartingrun($id = '', $listing)
{
	if (isset($_POST['data']))
	{
		$this->db->select('wid');
		$c = 1;
		foreach ($_POST['data'] as $k => $v)
		{
			if (trim($v[0]) != '' && trim($v[0]) != 'null')
			{
			if ($c == 1)
			{
				$this->db->where('bcn', trim($v[0]));
				$this->db->or_where('oldbcn', trim($v[0]));
			}
			else 
			{
				$this->db->or_where('bcn', trim($v[0]));
				$this->db->or_where('oldbcn', trim($v[0]));
			}
			$c++;
			}
		}
		$w = $this->db->get('warehouse');
		
		if ($w->num_rows() > 0)
		{
			$c = 0;
			foreach ($w->result_array() as $wid)
			{
				$c++;
				if ($c != $w->num_rows()) $this->noshowdata = true;
				elseif ($this->noshowdata) unset($this->noshowdata);
				$this->partstr = '';
				$this->partingrunbcn($id, $listing, $wid['wid']);				
			}			
		} else echo json_encode(array('html'=> 'No Matches', 'allbcnt' => 0, 'bcncnt'=> 0));
	}
}
function reparent($wid = '')
{
		$this->db->select("wid, waid, bcn, title, bcn_p1, bcn_p2, bcn_p3, aucid");	
		$this->db->where('wid', (int)$wid);

		$query = $this->db->get('warehouse');
		if ($query->num_rows() > 0)
		{
			$found = $query->row_array();
			$this->db->select("bcn_p3");	
			$this->db->where('bcn_p1', $found['bcn_p1']);
			if (trim($found['bcn_p2'] != '')) $this->db->where('bcn_p2', $found['bcn_p2']);
			else $this->db->where('bcn_p2', NULL);
			$this->db->order_by("wid", "DESC");
			$f = $this->db->get('warehouse');
			if ($f->num_rows() > 0)
			{
				$r = $f->row_array();
				$found['bcn_p3'] = (int)$r['bcn_p3'];
				$found['bcn_p3']++;				
			}	
			else
			{
				$found['bcn_p3'] = 1;
			}
		
		
			$this->db->update('warehouse', array('bcn_p3' => (int)$found['bcn_p3'], 'bcn' => $found['bcn'].'-'.$found['bcn_p3']), array('wid' => (int)$wid));
			$this->Auth_model->wlog($found['bcn'], (int)$wid, 'bcn', $found['bcn'], $found['bcn'].'-'.$found['bcn_p3']);
			$this->db->insert('warehouse', array(
			'waid' => $found['waid'],
			'title' => $found['title'],
			'bcn' => $found['bcn'],
			'bcn_p1' => $found['bcn_p1'],
			'bcn_p2' => $found['bcn_p2'],
			'aucid' => $found['aucid'],
			'status' => 'Parted',
			'adminid' => (int)$this->session->userdata['admin_id'],
			'dates' => serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())))
			));
			Redirect('Mywarehouse/bcndetails/'.$this->db->insert_id());
		}
}
function partingrunbcn($id = '', $listing, $wid = '')
{

	if ($id != '' && (int)$wid > 0)
	{
		$lastbcn = $this->Mywarehouse_model->PartingBCN((int)$wid);
		
		//$this->db->select("wid, bcn, bcn_p1, bcn_p2, bcn_p3");	
		if (!isset($this->partstr)) $this->partstr = '';
		$lastupd['prevstatus'] = $lastbcn['status'];
		$lastupd['status'] = 'Parted';
		$lastupd['location'] = 'Parted';
		$lastupd['status_notes'] = 'Changed from: '.$lastbcn['status'].' By Parting';
		$this->db->update('warehouse',$lastupd, array('wid' => $lastbcn['wid']));
		$this->Auth_model->wlog($lastbcn['bcn'], $lastbcn['wid'], 'status', $lastbcn['status'], $lastupd['status']);
		$newbcn['waid'] = $lastbcn['waid'];
		$newbcn['aucid'] = $lastbcn['aucid'];
		if (strlen($lastbcn['bcn_p1']) < 3 && $lastbcn['bcn_p1'] != 'G' ) $newbcn['bcn_p1'] = sprintf('%03u', $lastbcn['bcn_p1']);
		else $newbcn['bcn_p1'] = $lastbcn['bcn_p1'];
		$newbcn['bcn_p2'] = $lastbcn['bcn_p2'];
		$newbcn['bcn_p3'] = (int)$lastbcn['bcn_p3'];		
		$newbcn['bcn_p3']++;
		if (trim($newbcn['bcn_p2']) != '') $newbcn['bcn'] = $newbcn['bcn_p1'].'-'.$newbcn['bcn_p2'].'-'.$newbcn['bcn_p3'];
		else $newbcn['bcn'] = $newbcn['bcn_p1'].'-'.$newbcn['bcn_p3'];
		$this->partstr .= $newbcn['bcn'].' '.$this->partstr;
		//printcool ($newbcn);
		//exit();
		$newbcn['listingid'] = $listing;
		$newbcn['psku'] = (int)$id;
		$newbcn['title'] = $this->Mywarehouse_model->GetSkuTitle((int)$id);
		if (isset($_POST['status']) && $_POST['status'] != '') $newbcn['status'] = trim($this->input->post('status',true));
		else $newbcn['status'] = 'Listed';
		if (isset($_POST['location']) && $_POST['location'] != '') $newbcn['location'] = trim(ucwords($this->input->post('location',true)));
		$newbcn['adminid'] = (int)$this->session->userdata['admin_id'];
		$newbcn['dates'] = serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())));
		
		$this->db->insert('warehouse', $newbcn);
		$newbcn['wid'] = $this->db->insert_id();
		foreach ($newbcn as $k => $v)
		{
			if ($k !='bcn_p1' && $k !='bcn_p2' && $k !='bcn_p3' && $k !='vended') $this->Auth_model->newlog($newbcn['bcn'], $newbcn['wid'], $k, $v);
			if ($k == 'location')$this->Mywarehouse_model->DoLocation($v, (int)$newbcn['wid']);		
		}
		unset($newbcn);
		//$this->db->insert('warehouse_sku_listing_bcn', array('sku' => $id, 'listing' => $listing, 'wid' => $this->db->insert_id(), 'datetime' => CurrentTime(), 'admin'=> (int)$this->session->userdata['admin_id']));
		
		$this->Myseller_model->runAssigner($listing, -1);
		
		$this->mysmarty->assign('id', (int)$id);
		$this->mysmarty->assign('eid', (int)$listing);
		$res = $this->Mywarehouse_model->getbcnsforskulisting($id, $listing);
		$this->mysmarty->assign('res', $res);
		
		$this->load->model('Myseller_model');
		$this->Myseller_model->assignstatuses();
		$this->Myseller_model->assignchannels();
		$this->mysmarty->assign('parting', TRUE);
		if (!isset($this->noshowdata)) echo json_encode(array('html'=> $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int)$id), 'partstring' => $this->partstr, 'bcncnt'=> json_encode(count($res))));
		
		/*
		Serverside

$html = '<div>This is Html</div>';
$data = json_encode(array('page_title'=>'My Page'));
$response = array('html'=>$html, 'data'=>$data);
echo json_encode($response);
Clientside

//Ajax success function...

success: function(serverResponse){
    $("body > .container").html(serverResponse.html);
    var data = JSON.parse(serverResponse.data);
    $("title").html(data.page_title)
  }
  */
	}	
}
function partingsremovebcn($id = '', $listing, $wid = '')
	{
		$this->db->update('warehouse', array('listingid' => 0, 'psku' => 0, 'unparted_admin' => (int)$this->session->userdata['admin_id']), array('wid' => (int)$wid));
		$this->mysmarty->assign('id', (int)$id);
		$this->mysmarty->assign('eid', (int)$listing);
		$res = $this->Mywarehouse_model->getbcnsforskulisting($id, $listing);
		$this->mysmarty->assign('res', $res);		
		
		$this->load->model('Myseller_model');
		$this->Myseller_model->assignstatuses();
		$this->Myseller_model->assignchannels();
		$this->mysmarty->assign('parting', TRUE);
		echo json_encode(array('html'=> $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int)$id), 'bcncnt'=> json_encode(count($res))));
		
		
	}
function partingsearchforbcn($id = '', $listing)
{
	if ($id != '')
	{
	if (isset($_POST['fieldvalue']))
	{
		$this->mysmarty->assign('search', trim($_POST['fieldvalue']));
		$this->mysmarty->assign('res', $this->Mywarehouse_model->SearchBCN(trim($_POST['fieldvalue'])));		
	}
	$this->mysmarty->assign('eid', (int)$listing);
	$this->mysmarty->assign('id', trim($id));
	echo $this->mysmarty->fetch('mywarehouse/parting_search_bcns.html');		
	
	}
	else echo 'Error';	
}
function AddSkuToBcn($wid = 0, $wsid = 0, $gobackto = false)
{
	$data = $this->Mywarehouse_model->GetBCNDetails((int)$wid);
	if ($wsid != (int)$data['bcn']['sku'])
			{
					$this->Auth_model->wlog($data['bcn']['bcn'], (int)$wid, 'sku', (int)$data['bcn']['sku'], (int)$wsid);
					$this->db->update('warehouse', array('sku' => (int)$wsid), array('wid' => (int)$wid));
			}
	
	if ($gobackto && $gobackto == 'bcndetails') Redirect('Mywarehouse/bcndetails/'.$wid);
	else $this->parting($wid, TRUE);	
}

function parting($id = '', $framed = false, $fromsku = false)
{	
	if ((int)$id > 0 || ($fromsku && (int)$fromsku > 0)) 
	{	
		if (!$fromsku) 
		{
			$data = $this->Mywarehouse_model->GetBCNDetails((int)$id);
			$parent = (int)$data['bcn']['sku'];	
		}
		else $parent = $fromsku;

	
		
	if (isset($_POST['assignsku']))
	{
		$skuid = $this->Mywarehouse_model->seeksku(trim($this->input->post('assignsku', true)));
		
		if ($skuid != (int)$data['bcn']['sku'])
			{
					$this->Auth_model->wlog($data['bcn']['bcn'], (int)$id, 'sku', (int)$data['bcn']['sku'], $skuid);
					$this->db->update('warehouse', array('sku' => $skuid), array('wid' => (int)$id));
					$parent = $data['bcn']['sku'] = $skuid;
			}	
		//Redirect('Mywarehouse/parting/'.$id);		
	}
	$bulk = $this->Mywarehouse_model->GetSkusAndListingsAndBCNs((int)$parent);	
	if (isset($_POST['searchsku']))
	{
		$searchsku = trim($this->input->post('searchsku', TRUE));
	
		$q = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 0 AND (`title` LIKE "%'.$searchsku.'%" OR `name` LIKE "%'.$searchsku.'%" OR `upc` LIKE "%'.$searchsku.'%") ORDER BY `wsid` ASC');	
		$this->mysmarty->assign('srstr',$searchsku);
		if ($q->num_rows() > 0) $this->mysmarty->assign('sr',$q->result_array());
		else $this->mysmarty->assign('sr', false);
	}
	if (isset($_POST['updatelocation']))
	{
		$updatelocation = trim($this->input->post('updatelocation', TRUE));
		$list = $this->Mywarehouse_model->GetUpdateLocation((int)$parent);
		if ($list)
		{
			foreach ($list as $w)
			{
			if ($w['location'] != trim($updatelocation))
				{
					$this->Auth_model->wlog($w['bcn'], $w['wid'], 'location', $w['location'], trim($updatelocation));
					$this->db->update('warehouse', array('location' => trim($updatelocation)), array('wid' => $w['wid']));
					$this->Mywarehouse_model->DoLocation(trim($updatelocation), (int)$w['wid']);
				}
			}
		}
		$this->mysmarty->assign('success', 'Location updated for '.count($list).' items.');
		//Redirect('Mywarehouse/parting/'.$id);	
	}
	
		$numparts = $this->input->post('numparts');
		
		if ($numparts && (int)$numparts > 0)
		{
			$this->mysmarty->assign('numparts',(int)$numparts);
			$this->mysmarty->assign('setlocation', $this->input->post('setlocation',true));
			$start = $this->Mywarehouse_model->getnextsku();
			$numparts = $numparts + $start;
	
			$subbin = false;
			while ($start < (int)$numparts)
			{
				$start++;
				$attachwid = (int)$id;
				if ($fromsku) $attachwid = 0;

				$this->db->insert('warehouse_sku', array('name' => 'PSK'.$start, 'is_p' => 1, 'parent' => (int)$parent,'seq' => $start, 'wid' => (int)$attachwid, 'datetime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
				if (!$fromsku) $subbin[$this->db->insert_id()] = array('bcn' =>$data['bcn']['bcn'].'-'.$start, 'name' => 'PSK'.$start);				
			}
			if ($fromsku) $bulk = $this->Mywarehouse_model->GetSkusAndListingsAndBCNs((int)$parent);
			$this->mysmarty->assign('subbin', $subbin);
		}	
		
		if (isset($bulk['sku'])&& is_array($bulk['sku']) && count($bulk['sku']) > 0)
		foreach ($bulk['sku'] as $k => $v)
		{
			$img = false;
			if ($v['img'] != '')
			{
				$img = $v['img'];
				$img = str_replace('/ebay_images/', '' , $img);
				$img = explode('/', $img);
				$img = str_replace('thumb_', '',$img[1]);
			}
			if (isset($bulk['listings'][$v['wsid']]) && count($bulk['listings'][$v['wsid']]) > 0)
			{
				$c = 1;
				foreach ($bulk['listings'][$v['wsid']] as $lk => $lv)
				{
					$limg = $lv['e_img1'];
					if (trim($limg) == '') $limg = false;
					
					$fix = false;
					if ($c == 1 && $limg && !$img) $fix = true;
					elseif ($c == 1 && $limg && $img != $limg)  $fix = true;
					elseif ($limg && !$img && !$fix)  $fix = true;
					if ($fix) 
					{
						$this->db->update('warehouse_sku', array('img' => '/ebay_images/'.$lv['idpath'].'/thumb_'.$lv['e_img1']), array('wsid' => $v['wsid']));
						$this->mysmarty->assign('success', 'Image for SKU ID '.$v['wsid'].' Updated from Listing '.$lv['e_id']);	
					}
					$c++;
				}
			}
		}
	
	$this->mysmarty->assign('framed', $framed);
	
	if (!$fromsku)
	{
		if ($framed) $data = $this->Mywarehouse_model->GetBCNDetails((int)$id);
		
		$data['bcn']['name'] = $this->Mywarehouse_model->getsku($parent);
		$this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction((int)$data['bcn']['waid']));
		$l = $data['bcn'];
		$this->mysmarty->assign('data', $data);
	}
	else
	{
		 $this->mysmarty->assign('fromsku', $fromsku);
		 $this->mysmarty->assign('data', array('bcn' => array('sku' => $fromsku)));
	}
		
	$this->mysmarty->assign('bulk', $bulk);
		
	$this->load->model('Myseller_model');
	$this->Myseller_model->assignstatuses();
	$this->Myseller_model->assignchannels();
			
	$this->mysmarty->assign('parting', TRUE);
	if ($framed) echo $this->mysmarty->fetch('mywarehouse/partingarea.html');

	else $this->mysmarty->view('mywarehouse/parting.html');
	}
}
function sku()
	{		
		$this->mysmarty->assign('sku', $this->Mywarehouse_model->GetSKUS());
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->view('mywarehouse/sku.html');	
	}
function skudetails($id = '')
{
	$return = false;
	if (isset($_POST['skuid'])) { $id= (int)$_POST['skuid']; $return = true;}
	
	$sku = $this->Mywarehouse_model->getskudata((int)$id);
	if ($sku)
	{
	$this->mysmarty->assign('skudetails', TRUE);
	$this->mysmarty->assign('sku', $sku);
	$this->mysmarty->assign('warehouse', $this->skudetailswarehouse((int)$sku['wsid'], (int)$sku['is_p'], $return));	
	$this->mysmarty->assign('listings', $this->skudetailslistings((int)$sku['wsid']));
	$this->mysmarty->assign('framed', TRUE);
	$this->mysmarty->assign('fromsku', (int)$id);
	$bulk = $this->Mywarehouse_model->GetSkusAndListingsAndBCNs((int)$sku['wsid']);	
	$this->mysmarty->assign('bulk', $bulk);
	$this->mysmarty->assign('data', array('bcn' => array('sku' => (int)$sku['wsid'])));
	if ($sku['is_p'] == 0) 
	{
		$this->mysmarty->assign('parting', TRUE);
		$this->mysmarty->assign('partinghtml', $this->mysmarty->fetch('mywarehouse/parting.html'));
	}
		
	}
	$this->mysmarty->view('mywarehouse/skudetails.html');
}
function AddBcnsToSku($wsid = 0)
{
	if (isset($_POST['data']) && count($_POST['data']) > 0)
		{
			$this->db->select('wid');

			$cnt = 0;
			foreach ($_POST['data'] as $p)
			{
				if (trim($p[0]) != '')
				{
				
				if ($cnt == 0) $this->db->where('bcn', trim($p[0]));
				else $this->db->or_where('bcn', trim($p[0]));
				$cnt++;		
				}
			}
			if ((int)$cnt == 0) exit('None');
			$w = $this->db->get('warehouse');
			if ($w->num_rows() >0)
			{
				$_POST['wsid'] = (int)$wsid;
				foreach ($w->result_array() as $wid)
				{
					//$wids[] = $wid['wid'];	
					$_POST['wid'] =  $wid['wid'];
					$this->AddBcnToSku();
				}
				/*if (isset($wids))
				{	
				//printcool ($_POST['data']);
					//$cnt = 	count($wids);
					//$start = 0;
					$_POST['wsid'] = (int)$wsid;
					foreach ($wids as $w)
					{
						$_POST['wid'] = $w['wid'];
					 	//if ($start == $cnt) $this->AddBcnToSku(); 
						//else $this->AddBcnToSku(1);
						$this->AddBcnToSku(1); 
						//$start++;
					}
				}*/
			}
		}
}
function AddBcnToSku($display = false)
{
	if (isset($_POST['wsid']) && (int)$_POST['wsid'] > 0 && isset($_POST['wid']) && (int)$_POST['wid'] > 0 )	
	{
		$this->db->select('is_p');
		$this->db->where('wsid', (int)$_POST['wsid']);
		$s = $this->db->get('warehouse_sku');
		if ($s->num_rows() > 0)
		{
			$isp = $s->row_array();
			if ($isp['is_p'] == 0)
			{
				$this->db->update('warehouse', array('sku'=>(int)$_POST['wsid']), array('wid' => (int)$_POST['wid']));	
			}
			else
			{
				$this->db->update('warehouse', array('psku'=>(int)$_POST['wsid']), array('wid' => (int)$_POST['wid']));
			}
		
			//if (!$display) echo $this->skudetailswarehouse((int)$_POST['wsid'], (int)$isp['is_p']);			
		}		
	}
}
function CreateListingSku($listing)
{
	$title = $this->Mywarehouse_model->GetListingTitleAndCondition((int)$listing, TRUE);
	$skuid = $this->Mywarehouse_model->seeksku(trim($title));

	$this->db->update('warehouse_sku', array('name' => $title, 'name' => 'SKU'.$skuid), array('wsid' => $skuid));
	$this->db->insert('warehouse_sku_listing', array('wsid' => $skuid, 'listing' => (int)$listing, 'datetime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id'])); 
	$this->db->update('warehouse', array('sku' => $skuid), array('listingid' => $listing));
	Redirect('Mywarehouse/skudetails/'.$skuid);
}
function RemoveSkuBCN ()
{
	if (isset($_POST['wid']) && (int)$_POST['wid'] > 0 )	
	{
		$this->db->update('warehouse', array('sku' => 0), array ('wid' => $_POST['wid']));
	}	
}
function skudetailswarehouse($id = '', $isp ='', $justdata = false)
{
	 $wh = $this->Mywarehouse_model->getskubcns((int)$id, (int)$isp);
	 $qty=$parted = 0;
	 $listed=array();
	 $statuses = array();
	 $loaddata = '';
	 if ($wh) foreach ($wh as $w)
	 {
		 $qty++;
		 if ($w['listingid'] > 0) $listed[$w['listingid']] = true;
		 if ($w['status'] == 'Parted') $parted++;		 
		else {
				if (isset($statuses[$w['status']])) $statuses[$w['status']]++;
				else $statuses[$w['status']] = 1;
			}
		 $returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($w['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($w['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($w['sold_id'])."/".cstr($w['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;<a href=\"#\" onClick=\"RemoveBcnFromSku(".$w['wid'].");\" style=\"color:#0066CC;\"><img src=\"/images/admin/delete.png\" class=\"linkimage\" /></a>",cstr($w['bcn']),cstr($w['oldbcn']),cstr($w['location']),cstr($w['status']),cstr($w['listingid']));
	 }
	 if (count($wh) > 0)
			{
			foreach ($returndata as $r)
				{
					$loaddata .= "["; 
					foreach ($r as $rr)
					{
						$loaddata .= "'".$rr."',"; 
					}
					$loaddata .= "],"; 
					
				}	
			}
	 if ($justdata) {
		 				$data['loaddata'] = $returndata;
						$data['qty'] = $qty;
						$data['listed'] = count($listed);
						$data['parted'] = $parted;
						$data['statuses'] = $statuses;
						
		 				echo json_encode($data); exit(); 
			}
	 
	 $this->mysmarty->assign('qty', $qty);
	 $this->mysmarty->assign('listed', count($listed));
	 $this->mysmarty->assign('parted', $parted);
	 $this->mysmarty->assign('statuses', $statuses);
	$this->mysmarty->assign('hot', TRUE);
	 $fielset = array(
		'headers' => "'Go', 'BCN','Old BCN', 'Location', 'Status', 'Listing ID'",
		'width' => "90, 100, 100,100,100, 100", 
		'startcols' => 4,		
		'colmap' => '{readOnly: true, renderer: "html"}, {readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');
		
		$this->mysmarty->assign('headers', $fielset['headers']);
		$this->mysmarty->assign('width', $fielset['width']);
		$this->mysmarty->assign('startcols', $fielset['startcols']);
		$this->mysmarty->assign('startrows', count($wh));
		$this->mysmarty->assign('autosaveurl', '');
		$this->mysmarty->assign('colmap', $fielset['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('copyrows', count($wh));
		$this->mysmarty->assign('skuid', (int)$id);
		$this->mysmarty->assign('isp', $isp);
		
	 return $this->mysmarty->fetch('mywarehouse/skudetails_warehouse.html');
}
function skudetailslistings($id)
{
	$this->load->model('Myebay_model'); 
	$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
	$this->mysmarty->assign('list', $this->Mywarehouse_model->getskulistings((int)$id));
	$this->mysmarty->assign('skuedit', (int)$id);
	
	return $this->mysmarty->fetch('mywarehouse/skudetails_listings.html');
}
function skuremovelisting($listing, $wsid)
{
		$this->db->where('listing', (int)$listing);
		$this->db->where('wsid', (int)$wsid);
		$this->db->delete('warehouse_sku_listing'); 
		echo 1;
}
function skumanager($action = '', $string = '')
{
	if ($action == 'search') 
	{
		if ($string == 'ses') 
		{
			$s = trim($this->session->userdata('searchtitle'));
			$this->session->unset_userdata('searchtitle');
		}
		elseif (isset($_POST['skutitle'])) $s = addslashes(trim($this->input->post('skutitle', TRUE)));
	}
	elseif ($action == 'create') 
	{
		$new['title'] = htmlspecialchars(trim($this->input->post('title', TRUE)));
		
		if ($new['title'] != '')
		{
		$new['name'] = htmlspecialchars($this->input->post('label', TRUE));
		$new['upc'] = $this->input->post('upc', TRUE);
		$new['tags'] = $this->input->post('tags', TRUE);
		$new['related'] = $this->input->post('related', TRUE);
		$new['is_p'] = 0;
		$new['datetime'] = CurrentTime();
		$new['admin'] = (int)$this->session->userdata['admin_id'];
				
		$this->db->insert('warehouse_sku', $new);
		$ins =$this->db->insert_id();
		if ($new['name'] == '')$this->db->update('warehouse_sku', array('name' => 'SK'.$ins), array('wsid' => $ins));		
		$this->session->set_userdata('searchtitle', $new['title']);
		Redirect('Mywarehouse/skumanager/');
		}
		else { $s = ''; $this->mysmarty->assign('error', 'Title is required');}
	}
	else $s = '';
	
	
	if (isset($s))
	{
		 $sklist = $this->Mywarehouse_model->SearchSKUS($s);
		 //printcool ($sklist);
		if ($this->session->userdata('searchtitle'))
		{
			$s = trim($this->session->userdata('searchtitle'));
			$this->session->unset_userdata('searchtitle');
		}
		 
		 $this->mysmarty->assign('skus', $sklist['skus']);
		 $this->mysmarty->assign('lstcnt', $sklist['lstcnt']);
		 $this->mysmarty->assign('bcncnt', $sklist['bcncnt']);
		 $this->mysmarty->assign('parted', $sklist['parted']);
		 $this->mysmarty->assign('listed', $sklist['listed']);
		 $this->mysmarty->assign('sold', $sklist['sold']);
		 $this->mysmarty->assign('searchtitle', $s);
	}	
	$this->mysmarty->view('mywarehouse/skumanager.html');	
}
function skucreatecategory($id = 0)
{//printcool ($_POST);
	$crcat = trim($this->input->post('crcat', false));
	//$ptcat = trim($this->input->post('ptcat', false));	
	
	if (isset($_POST['crcat']) && trim($_POST['crcat']))// && isset($_POST['ptcat']))	
	{
		
		$this->db->where('wsc_id',(int)$id);
		$this->mysmarty->assign('opencat',array('wsc_id' => (int)$id));
		$c = $this->db->get('warehouse_sku_categories');
		if ($c->num_rows() == 0) $cc =array('wsc_id' => 0, 'wsc_title' => 'Top Level');
		else $cc = $c->row_array();
			
			$this->mysmarty->assign('opencat',$cc);
			//printcool ($cc);
			$this->db->insert('warehouse_sku_categories', array('wsc_title' => $crcat, 'wsc_parent' => $cc['wsc_id']));
			$insid = $this->db->insert_id();
			$this->session->set_flashdata('success_msg', $crcat.' Created Under '.$cc['wsc_title']);				
			Redirect('Mywarehouse/skucategories/'.$insid);	
	}
	else
	{
		$this->mysmarty->assign('ctpterror', 'Something is empty, that should be, go back and check if you have put in a category name!');
		$this->mysmarty->assign('ctptct', $crcat);
		//$this->mysmarty->assign('ctptpt', $ptcat);
		$this->skucategories();
	}
}
function skuinputcategory($id = 0)
{
	if (isset($_POST['skus']) && trim($_POST['skus']) != '' && (int)$id>0)// && isset($_POST['ptcat']))	
	{
		//$ctcat = trim($this->input->post('ctcat', false));
		//$ptcat = trim($this->input->post('ptcat', false));	
		$this->db->where('wsc_id',(int)$id );
		$c = $this->db->get('warehouse_sku_categories');
		
		$this->mysmarty->assign('inskus', $this->input->post('skus', true));
		//$this->mysmarty->assign('inptpt', $ptcat);
					
		if ($c->num_rows() > 0)
		{
			$cc = $c->row_array();
			$this->mysmarty->assign('opencat',$cc);
			$thedata = explode(PHP_EOL, trim($this->input->post('skus')));
			
			if (count($thedata) > 0)
			{
			$this->db->select('wsid, name');
			$c  = 1;
			foreach ($thedata as $k => $v)
			{
				if (trim($v) != '')
				{
					if ($c ==1) $this->db->where('name', trim($v));
					else $this->db->or_where('name', trim($v));
					$c++;
				}
			}
			$str = '';
			$s = $this->db->get('warehouse_sku');
				if ($s->num_rows() > 0)
				{
					foreach ($s->result_array() as $ss)
					{
						$str .= $ss['name'].', ';
						$this->db->update('warehouse_sku', array('wsc_id' => $cc['wsc_id']), array('wsid' => $ss['wsid']));
					}

					$this->session->set_flashdata('success_msg', 'SKUS: '.rtrim($str, ', ').' put in '.$cc['wsc_title']);	
					Redirect('Mywarehouse/skucategories/'.$id);
				}
				else
				{
					$this->mysmarty->assign('inpterror', 'Cannot find SKUs.');
					
					$this->skucategories();
				}
			}
			else
			{
				$this->mysmarty->assign('inpterror', 'You haven\'t entered any SKUs');
				$this->skucategories();	
			}
		}
		else
		{
			$this->mysmarty->assign('inpterror', 'Cannot find category to put in.');
			$this->skucategories();
		}
	}
}

function skucategories($id = 0)
{
	if (isset($_POST['postindex']) && isset($_POST['postparent']) && isset($_POST['poststart']))
	{
		$movedid = $this->input->post('poststart', true);
		$movetoparent = $this->input->post('postparent');
		if (substr($movedid, 0, 1) == 'C')
		{
	
			$this->db->update('warehouse_sku_categories', array('wsc_parent' => str_replace('C', '',$movetoparent)), array('wsc_id' =>  str_replace('C', '',$movedid)));
			echo 'Category Move Success';
		}
		if (substr($movedid, 0, 1) == 'S')
		{
			if (substr($movetoparent, 0, 1) == 'C')
			{
			$this->db->update('warehouse_sku', array('wsc_id' =>  str_replace('C', '',$movetoparent)), array('wsid' => str_replace('S', '',$movedid)));
			echo 'SKU Move Success';
			}
			else echo '';
		}
		$out = array('index' => $_POST['postindex'], 'parent' => $_POST['postparent'], 'start' => $_POST['poststart']);
		
		//echo json_encode($out);		
		exit();	
	}
	
	/*
	$this->load->helper('directory');
	$this->load->helper('file');
	
	$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
						$store = simplexml_load_string($responseXml);						
					
						$sc = array();
						if (isset($store->Store->CustomCategories->CustomCategory))
						{
							foreach ($store->Store->CustomCategories->CustomCategory as $s)
							{
								$a = (array)$s;
								$sc[(int)$a['CategoryID']] = array('wsc_id' => (int)$a['CategoryID'], 'wsc_title' => (string)$a['Name']);	
							}			
						}
					printcool ($sc);	
			foreach ($sc as $k =>$v)
			{
				$this->db->insert('warehouse_sku_categories', $v);
			}

		*/	
		$sklist = $this->Mywarehouse_model->SearchSKUS('');
		 $this->mysmarty->assign('parents', $sklist['parents']);
		 $this->mysmarty->assign('children', $sklist['children']);
		 
	
	/*
	var smalltreedata = [
    {id:"root", value:"Films data", open:true, data:[
		{ id:"1", open:true, value:"The Shawshank Redemption", data:[
			{ id:"1.1", value:"Part 1" },
			{ id:"1.2", value:"Part 2", data:[
				{ id:"1.2.1", value:"Page 1" },
				{ id:"1.2.2", value:"Page 2" },
				{ id:"1.2.3", value:"Page 3" },
				{ id:"1.2.4", value:"Page 4" },
				{ id:"1.2.5", value:"Page 5" }
			]},
			{ id:"1.3", value:"Part 3" }
		]},
		{ id:"2", open:true, value:"The Godfather", data:[
			{ id:"2.1", value:"Part 1" },
			{ id:"2.2", value:"Part 2" }
		]}
	]}
];
*/
	if ((int)$id > 0)
	{
		$this->mysmarty->assign('taxonomy', explode("\n",taxonomyfill()));
		$this->load->model('Myebay_model');
 
		$this->mysmarty->assign('categories', $this->Myebay_model->GetDistinctUsedEbayCategories());
		$this->mysmarty->assign('searchcat', '');
		
		if (isset($_POST['opencat']))
			{
				
				$opencat['wsc_title'] = $this->input->post('wsc_title',true);
				$opencat['brand'] = $this->input->post('brand',true);
				$opencat['model'] = $this->input->post('model',true);
				$opencat['mpn'] = $this->input->post('mpn',true);
				$opencat['upcgtin'] = $this->input->post('upcgtin',true);
				$opencat['ebaycat'] = $this->input->post('primaryCategory',true);
				$opencat['googlecat'] = $this->input->post('googlecat',true);
				$opencat['lbs'] = (int)$this->input->post('lbs',true);
				$opencat['oz'] = (int)$this->input->post('oz',true);
				if ($opencat['wsc_title'] == '' || $opencat['brand'] == '' || $opencat['mpn'] == '' || $opencat['upcgtin'] == '' || $opencat['ebaycat'] == '' || $opencat['googlecat'] == '')
				{
					$opencat['wsc_id'] = (int)$id;
					$this->mysmarty->assign('opencat',$opencat);
					$missingrequired = TRUE;
					$this->mysmarty->assign('missingrequired', $missingrequired);												
				}
				else 
				{
					$this->mysmarty->assign('saved', TRUE);							
					$this->db->update('warehouse_sku_categories', $opencat, array('wsc_id' => (int)$id));
				}
			}
		if (!isset($missingrequired))
			{
			$this->db->where('wsc_id', (int)$id);
			$gc = $this->db->get('warehouse_sku_categories');
			
			if ($gc->num_rows() > 0)
			{
				$this->mysmarty->assign('opencat', $gc->row_array());
			}
		}
		
	}
	$this->db->select('wsid,wsc_id, is_p,parent,name');
	$this->db->where('wsc_id !=', 0);
	$dbsku = $this->db->get('warehouse_sku');
	if ($dbsku->num_rows() >0)
	{
		foreach ($dbsku->result_array() as $s)
		{
			$this->skutree[$s['wsc_id']][] = array('id' => 'S'.$s['wsid'],  'value' => $s['name'].' <a href="/Mywarehouse/skudetails/'.$s['wsid'].'\"><img src="/images/admin/b_search.png" border="0"></a>', 'icon'=> "sku");		
		}		
		
	}
	
	$this->db->select('wsc_id,wsc_title,wsc_parent' );
	$this->db->order_by('wsc_title', 'asc');
	$sc = $this->db->get('warehouse_sku_categories');
	if ($sc->num_rows() > 0)
	{
		foreach ($sc->result_array() as $s)
		{
			//$this->tree[$s['wsc_id']] = array();
			
			//if ((int)$id > 0 && (int)$id = $s['wsc_id']) 
			$data =  array('id' => 'C'.$s['wsc_id'],  'value' => $s['wsc_title'].' <a href="/Mywarehouse/skucategories/'.$s['wsc_id'].'\"><img src="/images/admin/add.png" border="0"></a>', 'parent' => 'C'.$s['wsc_parent'], 'open' => true , 'data' => array());	
			if ($s['wsc_parent'] == 0) $tree[] = $data;
			else $this->childtree[] = $data;
			
			$dataset[] = $s['wsc_title'];
			//else $tree[$s['wsc_parent']][] = array('id' => 'C'.$s['wsc_id'],  'value' => $s['wsc_title'].' <a href="/Mywarehouse/skucategories/'.$s['wsc_id'].'\"><img src="/images/admin/b_search.png" border="0"></a>', 'data' => array());	
		
		}
		$this->mysmarty->assign('dataset', $dataset);
	}
	
	//http://stackoverflow.com/questions/8656682/getting-all-children-for-a-deep-multidimensional-array
	$treearray = array(
		'id' => 'root',
		'value' => 'Root',
		'open' => true,
		'data' => array()

	);
	//$c = 1; 
	foreach($tree as $k=>$v)
	{
		if($v['parent']==0)// && $c < 4
		{
			$treearray['data'][] = array(
										'id' => $v['id'],
										'value' => $v['value'],
										'open' => true,
										//'icon'=> "sku",
										'data' => $this->_findSkuCategoryChildren($v['id'],array())
									);//printcool($v['id']);
	//		$c++;
		}
		
	}
	//printcool ($treearray);
	/*$treearray = array(
		'id' => 'root',
		'value' => 'Root',
		'data' => array(0 => array(
								'id' => '1111',
								'value' => '1111',
								'data' => array(
											'id' => '11112222',
											'value' => '11112222',
											'data' => array(
														'id' => '1111222223333',
														'value' => '111122222333',
														'data' =>array()
														)
											)
									),
							1 =>array(
								'id' => '222112211',
								'value' => '2221111',
								'data' => array()
								)		
						)
					);*/
	$this->mysmarty->assign('treedata', json_encode($treearray));

	$this->mysmarty->assign('skutree', true);
	$this->mysmarty->view('mywarehouse/skucategories.html');	
}
function _findSkuCategoryChildren( $parent_id, $child_array)
	{
		if (isset($this->childtree)) foreach($this->childtree as $k=>$v)
		{
			if($v['parent']==$parent_id)
			{
				$child_array[] = array(
									'id' => $v['id'],
									'value' => $v['value'],
									'open' => true,
									'data' => $this->_findSkuCategoryChildren($v['id'],array())
								);
				unset($this->childtree[$k]);
			}
		}	//printcool ($child_array);	
		
		if (isset($this->skutree[str_replace('C', '', $parent_id)]))
		{
			foreach ($this->skutree[str_replace('C', '', $parent_id)] as $k=>$v)
			{
			 $child_array[] = $v;
			}

		}
		
		return $child_array;
	}
function insertlistingtosku()
{
	if (isset($_POST['sku']) && (int)$_POST['sku'] > 0 && isset($_POST['listing']) && (int)$_POST['listing'] > 0)	
	{
		$this->db->insert('warehouse_sku_listing', array('wsid' => (int)$this->input->post('sku', true), 'listing' => (int)$this->input->post('listing', true), 'datetime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id'])); 
		echo ($this->skudetailslistings((int)$this->input->post('sku', true)));
	}
}
function insertskulisting()
{
	if (isset($_POST['sku']) && (int)$_POST['sku'] > 0 && isset($_POST['listing']) && (int)$_POST['listing'] > 0)	
	{
		$this->db->insert('warehouse_sku_listing', array('wsid' => (int)$this->input->post('sku', true), 'listing' => (int)$this->input->post('listing', true), 'datetime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id'])); 
		$listingdata = $this->Mywarehouse_model->GetListingTitleAndCondition((int)$this->input->post('listing', true));
		if ($listingdata)
		{
			$condition = 'Undefined ('.$listingdata['Condition'].')';			
			if ($listingdata['Condition'] == '1000') $condition = 'New';
		    if ($listingdata['Condition'] == '1500') $condition = 'New other (see details)';
		    if ($listingdata['Condition'] == '1750') $condition = 'New with defects';
		    if ($listingdata['Condition'] == '2000') $condition = 'Manufacturer refurbished';
		    if ($listingdata['Condition'] == '2500') $condition = 'Seller refurbished';
		    if ($listingdata['Condition'] == '3000') $condition = 'Used';
		  	if ($listingdata['Condition'] == '4000') $condition = 'Very Good';
		    if ($listingdata['Condition'] == '5000') $condition = 'Good';
		    if ($listingdata['Condition'] == '6000') $condition = 'Acceptable';
		    if ($listingdata['Condition'] == '7000') $condition = 'For parts or not working';			
			
			//$listingstring = substr($listingdata['e_title'],0,120);
			$listingstring = $listingdata['e_title'];
			$submitstat = '';
			if ($listingdata['ebay_submitted'] == '') $submitstat .= 'Not Submitted<br>';
			else 
			{
			
			if ($listingdata['ebay_id'] != 0) { $cellstyle = 'class="ok"'; $submitstat .= '<br><a href="http://www.ebay.com/itm/'.$listingdata['ebay_id'].'" target="_blank">eBay ItemID: <strong class="active">'.$listingdata['ebay_id'].'</strong></a><br />';			}
			if ($listingdata['ebended'] != '' && $listingdata['endedreason'] != '')
			{
				  $submitstat .= '<br><span style="color:red; font-weight:bolder;">'.$listingdata['endedreason'].'</span><br>

            <span id="resubmit_'.(int)$_POST['listing'].'" style="cursor:pointer; color:#FF40D4;" onClick="Resubmit(\''.(int)$_POST['listing'].'\');">Resubmit</span><br>';
				$cellstyle = 'class="redbg"';
			}
			else $submitstat .= 'Submitted: '.$listingdata['ebay_submitted'].'<br>';			
			}
		
			$listingimg = $listingdata['e_img1'];
			$listingidpath = $listingdata['idpath'];
			
			$res = $this->Mywarehouse_model->getbcnsforskulisting((int)$_POST['sku'], (int)$_POST['listing']);
			
			$this->mysmarty->assign('res', $res);
			$this->mysmarty->assign('eid', (int)$_POST['listing']);
			$this->mysmarty->assign('id', (int)$this->input->post('sku', true));
			
			$imgexists = $this->Mywarehouse_model->GetSKUImage((int)$this->input->post('sku', true));
			if (!$imgexists && isset($_POST['imgurl']) && trim($_POST['imgurl']) != '')
			{
				 $this->db->update('warehouse_sku', array('img' => trim($this->input->post('imgurl', true))), array('wsid' => (int)$this->input->post('sku', true)));		
				 $returnimg	= trim($this->input->post('imgurl', true));
			}
			else $returnimg = $imgexists;
			
			$this->load->model('Myseller_model');
			$this->Myseller_model->assignstatuses();
			$this->Myseller_model->assignchannels();
			
			$this->mysmarty->assign('parting', TRUE);
			
			$this->mysmarty->assign('dyn', TRUE);
			$v['wsid'] = (int)$this->input->post('sku', true);
			$lv['listing'] = (int)$_POST['listing']; 
			$this->mysmarty->assign('v', $v);
			$this->mysmarty->assign('lv', $lv);
			
			echo json_encode(array('html'=> $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'),'paste' => $this->mysmarty->fetch('mywarehouse/parting_paste.html'),  'bcncnt'=> count($res), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int)$this->input->post('sku', true)), 'img' => $returnimg, 'linkimg' => thumb2original($returnimg), 'listingstring' => $listingstring, 'submitstat' => $submitstat, 'cellstyle' => $cellstyle, 'condition' =>$condition, 'e_condition'=>$listingdata['e_condition'], 'listingimg' => $listingimg, 'listingidpath' => $listingidpath));
		}
		else echo '';
	}
	else echo '';
}
/*function testdecode()
{
	if ($_POST){ echo json_encode(array('html'=> '<html></html>', 'bcncnt'=> 2, 'img' => '/ebay/imgpath.jpg', 'listingstring' => '<br>something to string')); exit();}
	$this->mysmarty->view('mywarehouse/jsontest.html');
}*/
function test()
{exit();
$id = 71;
		$this->db->select('listing');
		$this->db->where('wsid', (int)$id);
		$q1 = $this->db->get('warehouse_sku_listing');
		if ($q1->num_rows() > 0)
		{
			$this->db->select("wid");
			$start = 1;
			foreach ($q1->result_array() as $l)
			{
				if ($start == 1) $this->db->where('listingid', (int)$l['listing']);
				else $this->db->or_where('listingid', (int)$l['listing']);
				$start++;
			}
		}
		else return 0;
		$q= $this->db->get('warehouse');	
		if ($q->num_rows() > 0) echo ($q->num_rows());
		else echo 0;		
	
}
function removeskulisting()
{
	if (isset($_POST['sku']) && (int)$_POST['sku'] > 0 && isset($_POST['listing']) && (int)$_POST['listing'] > 0)	
	{
		$this->db->where('wsid', (int)$this->input->post('sku', true));
		$this->db->where('listing', (int)$this->input->post('listing', true));
		$this->db->delete('warehouse_sku_listing');
		
		$data = $this->Mywarehouse_model->getlistingandskucount((int)$this->input->post('sku', true));
		echo json_encode(array('listings' => $data['listings'], 'allbcnt' => $data['bcn']));
		
		//echo 'ok';
	}
	else echo '';
}
function insertskulistingbcn()
{
	if (isset($_POST['wid']) && (int)$_POST['wid'] > 0 && isset($_POST['sku']) && $_POST['sku'] != '' && isset($_POST['listing']) && (int)$_POST['listing'] > 0)	
	{
		$this->db->insert('warehouse_sku_listing_bcn', array('wid' => (int)$_POST['wid'], 'sku' => trim($this->input->post('sku', true)), 'listing' => (int)$_POST['listing'], 'datetime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
		
	echo 'ok';
	}	
}
function removeskulistingbcn()
{
	if (isset($_POST['wid']) && (int)$_POST['wid'] > 0 && isset($_POST['sku']) && $_POST['sku'] != '' && isset($_POST['listing']) && (int)$_POST['listing'] > 0)	
	{
		$this->db->where('wid', (int)$_POST['wid']);
		$this->db->where('sku', trim($this->input->post('sku', true)));
		$this->db->where('listing', (int)$_POST['listing']);
		$this->db->delete('warehouse_sku_listing_bcn');
		
		echo 'ok';
	}
}
function reattach ($wid, $listing, $soldid, $soldsubid = 0, $go = false)
{
	$owid = $wid ;
	$wid = $this->Mywarehouse_model->bcn2wid(trim($wid));
	Redirect('Mywarehouse/bcndetails/'.$wid);
	$widdata = $this->Mywarehouse_model->getbcnattachdata((int)$wid);
if (!$go)
{
	printcool ($widdata['listingid']);
printcool ($widdata['sold_id']);
printcool ($widdata['sold_subid']);
printcool ('<a href="/Mywarehouse/reattach/'.$owid.'/'.$listing.'/'.$soldid.'/'.$soldsubid.'/1">GO</a>');
printcool ($widdata);

}if ($go)
	{
	
	
	if ((int)$soldsubid == 0) $channel = 1;
	else $channel = 2;
	$this->db->update('warehouse', array('listingid' => (int)$listing, 'sold_id' => (int)$soldid, 'sold_subid' => $soldsubid, 'channel' => $channel, 'vended' => 1), array('wid' => (int)$wid));
	Redirect('Mywarehouse/bcndetails/'.$wid);
	}
}
function bcndetails($id = '', $savetype = false)
{
	if ((int)$id > 0) 
	{
		$data = $this->Mywarehouse_model->GetBCNDetails((int)$id);
		if (isset($_POST['assignsku']))
		{
			$skuid = $this->Mywarehouse_model->seeksku(trim($this->input->post('assignsku', true)));
			if ($skuid != (int)$data['bcn']['sku'])
			{
					$this->Auth_model->wlog($data['bcn']['bcn'], (int)$id, 'sku', (int)$data['bcn']['sku'], $skuid);
					$this->db->update('warehouse', array('sku' => $skuid), array('wid' => (int)$id));
			}
			Redirect('Mywarehouse/bcndetails/'.$id);		
		}
		elseif (isset($_POST['searchsku']))
		{
			$searchsku = trim($this->input->post('searchsku', TRUE));
		
			$q = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 0 AND (`title` LIKE "%'.$searchsku.'%" OR `name` LIKE "%'.$searchsku.'%" OR `upc` LIKE "%'.$searchsku.'%") ORDER BY `wsid` ASC');	
			$this->mysmarty->assign('srstr',$searchsku);
			if ($q->num_rows() > 0) $this->mysmarty->assign('sr',$q->result_array());
			else $this->mysmarty->assign('sr', false);
		}
	
		
		$data['bcn']['name'] = $this->Mywarehouse_model->getsku((int)$data['bcn']['sku']);
		$this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction((int)$data['bcn']['waid']));
		$data['bcn']['queprint'] = 1;
		$l = $data['bcn'];
		$this->mysmarty->assign('data', $data);
		$this->mysmarty->assign('hot', TRUE);
		$this->mysmarty->assign('updated', CurrentTime());
				
		$adms = $this->Mywarehouse_model->GetAdminList();
		$this->mysmarty->assign('admins', $adms);		
		
		if ($savetype && $savetype == 'testing')
		{
			if(isset($_POST) && $_POST)
			{	
					$tcolMap = array(
							0 => 'oldbcn',
							1 => 'arctitle',
							2 => 'location',
							3 => 'status',
							4 => 'status_notes',
							5 => 'sn',
							6 => 'post',
							7 => 'battery',
							8 => 'charger',
							9 => 'hddstatus',
							10 => 'problems',
							11 => 'notes',							
							12 => 'partsneeded',
							13 => 'warranty'
							  );
					
					$btcolMap = array(
							0 => 'Old BCN',
							1 => 'Arc Title',
							2 => 'Location',
							3 => 'Status',
							4 => 'Status Notes',
							5 => 'SN',
							6 => 'POST',
							7 => 'Battery',
							8 => 'Charger',
							9 => 'HDD Status',
							10 => 'Problems',
							11 => 'Notes',							
							12 => 'Parts Needed',
							13 => 'Warranty'									
							  );
					
					$out = '';					
					$sout = '';			
					foreach($_POST as $d)
					{
						foreach($d as $dd)
						{
						
						if ($dd[2] != $dd[3])
						{
						$this->Auth_model->wlog($l['bcn'], (int)$id, $tcolMap[(int)$dd[1]], $dd[2], $dd[3]);
						$out .= ' "'.$btcolMap[(int)$dd[1]].'" for BCN '.$l['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
						$sout .= $l['bcn'].'/"'.$btcolMap[(int)$dd[1]].'" Changed ';
						
						$updt[$tcolMap[(int)$dd[1]]] = $dd[3];						
						$updt['tech'] = (int)$this->session->userdata['admin_id'];
						$updt['techlastupdate'] = CurrentTime();
						if ($tcolMap[(int)$dd[1]] == 'status') $updt['status_notes'] = 'Changed from: '.$dd[2];							
																				// $this->Mywarehouse_model->GetStatusNotes((int)$id).' | 
																				
						//GoMail(array ('msg_title' => 'BCN DETAILS UPDATE @ '.CurrentTime(), 'msg_body' => printcool ($updt, true, '$updt').printcool ($d, true, '$d').printcool ($tcolMap, true, '$tcolMap').$sout, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							
						if ($tcolMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;
							$this->db->insert('warehouse_audits', array('action_id' => (int)$id, 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$id);
						}
						$this->db->update('warehouse', $updt, array('wid' => (int)$id));
						unset($updt);; 
							
						if ($tcolMap[(int)$dd[1]] == 'paid' || $tcolMap[(int)$dd[1]] == 'cost' || $tcolMap[(int)$dd[1]] == 'sellingfee'|| $tcolMap[(int)$dd[1]] == 'shipped_actual' || ($tcolMap[(int)$dd[1]] == 'status' && $dd[3] == 'Scrap')) 
						{
							$this->load->model('Myseller_model'); 
							$nope = array();
							 $this->Myseller_model->HandleBCN($nope,$nope,(int)$id);	
							//$this->Mywarehouse_model->ReProcessNetProfit((int)$id);	
						}
						$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
						}
						}				
					}				
					echo json_encode($out);	
					exit();
			}			
		}	
		
		$tloaddata .= "['".cstr($l['oldbcn'])."', '".cstr($l['arctitle'])."', '".cstr($l['location'])."', '".cstr($l['status'])."', '".cstr($l['status_notes'])."','".cstr($l['sn'])."', '".cstr($l['post'])."', '".cstr($l['battery'])."', '".cstr($l['charger'])."', '".cstr($l['hddstatus'])."', '".cstr($l['problems'])."', '".cstr($l['notes'])."',   '".cstr($l['partsneeded'])."','".cstr($l['warranty'])."', '".cstr($l['techlastupdate'])."', '".$adms[$l['tech']]."'],
				";			
	
		
		$tfielset = array('testing' => array(
		'headers' => "'Old BCN',  'Arc Title','Location', 'Status', 'Status notes','SN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes',  'Parts Needed', 'Warranty',  'LastUpdt', 'Tech'",
		/*'rowheaders' => $list['headers'], */
		'width' => "100, 200, 120,125, 125, 125, 50, 50, 50, 150, 200, 100, 125, 125, 125, 125", 
		'startcols' => 16,
		'startrows' => 1, 
		'autosaveurl' => '/Mywarehouse/bcndetails/'.(int)$id.'/testing',
		'colmap' => '{},{},{},{type: "dropdown", source: ['.$this->statuses['testingstring'].']},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{},{},{},{},{},{readOnly: true},{readOnly: true}') 
		);
		
		$this->mysmarty->assign('theaders', $tfielset['testing']['headers']);
		$this->mysmarty->assign('trowheaders', $tfielset['testing']['rowheaders']);
		$this->mysmarty->assign('twidth', $tfielset['testing']['width']);
		$this->mysmarty->assign('tstartcols', $tfielset['testing']['startcols']);
		$this->mysmarty->assign('tstartrows', $tfielset['testing']['startrows']);
		$this->mysmarty->assign('tautosaveurl', $tfielset['testing']['autosaveurl']);
		$this->mysmarty->assign('tcolmap', $tfielset['testing']['colmap']);
		$this->mysmarty->assign('tloaddata', rtrim($tloaddata, ','));

		if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
		{
			$aloaddata .= "['".cstr($l['status'])."','".cstr($l['listed'])."', '".cstr($l['listed_date'])."', '".cstr($l['sold_date'])."', '".cstr($l['sold'])."',  '".cstr($l['soldqn'])."', '".cstr($l['paid'])."', '".cstr($l['shipped'])."','".cstr($l['shipped_actual'])."' ,'".cstr($l['shipped_inbound'])."', '".cstr($l['ordernotes'])."', '".cstr($l['sellingfee'])."', '".cstr($l['paypal_fee'])."', '".cstr($l['netprofit'])."', '".cstr($l['cost'])."',  '".cstr($l['aupdt'])."'],
				";		
		}
		else
		{
			$aloaddata .= "['".cstr($l['status'])."','".cstr($l['listed'])."', '".cstr($l['listed_date'])."', '".cstr($l['sold_date'])."', '".cstr($l['sold'])."', '".cstr($l['soldqn'])."', '".cstr($l['paid'])."',  '".cstr($l['shipped'])."', '".cstr($l['shipped_actual'])."','".cstr($l['shipped_inbound'])."',  '".cstr($l['ordernotes'])."', '".cstr($l['aupdt'])."'],
				";			
		}
		if ($savetype && $savetype == 'accounting')
		{
			if(isset($_POST) && $_POST)
			{	
					$acolMap = array(
							0 => 'status',
							1 => 'listed',
							2 => 'listed_date',
							3 => 'sold_date',
							4 => 'sold',
							5 => 'soldqn', 
							6 => 'paid',
							7 => 'shipped',
							8 => 'shipped_actual',
							9 => 'shipped_inbound',
							10 => 'ordernotes',
							11 => 'sellingfee',
							12 => 'paypal_fee',
							13 => 'netprofit',
							14 => 'cost'
								
							  );					
					$bacolMap = array(	
							0 => 'Status',
							1 => 'Where Listed',
							2 => 'Date Listed',
							3 => 'Date Sold',
							4 => 'Where Sold',
							5 => 'Sold QN',
							6 => 'Price Sold',
							7 => 'Shipping Cost',
							8 => 'Actual Sh.',//
							9 => 'Inbound Sh.',//
							10 => 'Order Notes',
							11 => 'Selling Fee',
							12 => 'Paypal Fee',//
							13 => 'Net Profit',//
							14 => 'Cost'	   //
												
							  );
					
					if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
					{						
						
						unset($acolMap[11]);
						unset($acolMap[12]);
						unset($acolMap[13]);
						unset($acolMap[14]);
						unset($bacolMap[11]);
						unset($bacolMap[12]);
						unset($bacolMap[13]);
						unset($bacolMap[14]);
					}
					
					
					$out = '';	
					$sout = '';					
					foreach($_POST as $d)
					{
						foreach($d as $dd)
						{		
						$dd[3] = floatercheck($acolMap[(int)$dd[1]], $dd[3]);				
						if ($dd[2] != $dd[3])
						{
					
						$this->Auth_model->wlog($l['bcn'], (int)$id, $acolMap[(int)$dd[1]], $dd[2], $dd[3]);		
						$out .= ' "'.$bacolMap[(int)$dd[1]].'" for BCN '.$l['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
						$sout .= $l['bcn'].'/"'.$bacolMap[(int)$dd[1]].'" Changed ';
						
						$updt = array($acolMap[(int)$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
						if ($acolMap[(int)$dd[1]] == 'status')
                        {
                            $updt['status_notes'] = 'Changed from: '.$dd[2];
                            if ($dd[3] == 'On Hold') $updt['vended'] = 2;
                            elseif ($dd[3] == 'Sold') $updt['vended'] = 1;
                            else  $updt['vended'] = 0;
                        }
																				// $this->Mywarehouse_model->GetStatusNotes((int)$id).' | 
																				
						$this->db->update('warehouse', $updt, array('wid' => (int)$id));
						if ($acolMap[(int)$dd[1]] == 'paid' || $acolMap[(int)$dd[1]] == 'cost' || $acolMap[(int)$dd[1]] == 'sellingfee' || $acolMap[(int)$dd[1]] == 'paypal_fee' || $acolMap[(int)$dd[1]] == 'shipped_actual' || ($acolMap[(int)$dd[1]] == 'status' && $dd[3] == 'Scrap'))
						{
							$this->load->model('Myseller_model'); 
							$nope = array();
							 $this->Myseller_model->HandleBCN($nope,$nope,(int)$id);	
							//$this->Mywarehouse_model->ReProcessNetProfit((int)$id);	
						}
						$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
						}
						}
					}				
					echo json_encode($out);	
					exit();
			}			
		}	
			
		if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
		{
		$afielset = array('accounting' => array(
		'headers' => "'Status','Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Sold QN', 'Price Sold', 'Shipping Cost', 'Actual Sh.', 'Inbound Sh.', 'Order Notes', 'Selling Fee', 'Paypal Fee', 'Net Profit', 'Cost',  'Last Upd'",
		/*'rowheaders' => $list['headers'], */
		'width' => "125, 125, 125, 125, 125, 80, 125, 125, 125, 125,125, 125, 125, 125, 125, 125, 125",
		'startcols' => 16,
		'startrows' => 1, 
		'autosaveurl' => '/Mywarehouse/bcndetails/'.(int)$id.'/accounting',		
		'colmap' => '{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{},{readOnly: true},{},{},{},{},{},{},{},{readOnly: true},{},{readOnly: true}')
		);
		}
		else
		{
		$afielset = array('accounting' => array(

		'headers' => "'Status', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Sold QN', 'Price Sold', 'Shipping Cost', 'Actual Sh.', 'Inbound Sh.', 'Order Notes',  'Last Upd'",
		/*'rowheaders' => $list['headers'], */
		'width' => "125, 125, 125, 125, 125, 80, 125, 125, 125, 125, 125, 125", 
		'startcols' => 12, 
		'startrows' => 1, 
		'autosaveurl' => '/Mywarehouse/bcndetails/'.(int)$id.'/accounting',		
		'colmap' => '{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{},{readOnly: true},{},{},{},{},{},{readOnly: true}')
		);
		}
		
		$this->mysmarty->assign('aheaders', $afielset['accounting']['headers']);
		$this->mysmarty->assign('arowheaders', $afielset['accounting']['rowheaders']);
		$this->mysmarty->assign('awidth', $afielset['accounting']['width']);
		$this->mysmarty->assign('astartcols', $afielset['accounting']['startcols']);
		$this->mysmarty->assign('astartrows', $afielset['accounting']['startrows']);
		$this->mysmarty->assign('aautosaveurl', $afielset['accounting']['autosaveurl']);
		$this->mysmarty->assign('acolmap', $afielset['accounting']['colmap']);
		$this->mysmarty->assign('aloaddata', rtrim($aloaddata, ','));	
		
		if ($savetype && $savetype == 'returns')
		{
			if(isset($_POST) && $_POST)
			{	
					$rcolMap = array(
							0 => 'return_datesold',
							1 => 'return_pricesold',
							2 => 'return_sellingfee',
							3 => 'return_shippingcost',
							4 => 'return_netprofit',
							5 => 'return_wheresold', 
							6 => 'returned',
							7 => 'returned_notes',
							8 => 'returned_time',
							9 => 'returned_recieved',
							10 => 'returned_refunded',
							11 => 'returned_extracost',
						12 => 'cust_reason',
						13 => 'cust_xtrcost'
						  );
					
					$brcolMap = array(
							0 => 'Date Sold',
							1 => 'Price Sold',
							2 => 'Selling Fee',
							3 => 'Shipping Cost',
							4 => 'Net Profit',
							5 => 'Where Sold', 
							6 => 'Return',
							7 => 'Ret. Notes',
							8 => 'Time',
							9 => 'Recieved',
							10 => 'Refunded',
							11 => 'Xtra Cost',
						12 => 'Customer_reason',
						13 => 'Customer_xtrcost'
							  );
					
					$out = '';					
					$sout = '';			
					foreach($_POST as $d)
					{
						foreach($d as $dd)
						{
						$dd[3] = floatercheck($rcolMap[(int)$dd[1]], $dd[3]);
						if ($dd[2] != $dd[3])
						{
						$this->Auth_model->wlog($l['bcn'], (int)$id, $tcolMap[(int)$dd[1]], $dd[2], $dd[3]);
						$out .= ' "'.$btcolMap[(int)$dd[1]].'" for BCN '.$l['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
						$sout .= $l['bcn'].'/"'.$btcolMap[(int)$dd[1]].'" Changed ';
						
						$updt[$tcolMap[(int)$dd[1]]] = $dd[3];
																										
						$this->db->update('warehouse', $updt, array('wid' => (int)$id));
						unset($updt);
						


						$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
						}
						}				
					}				
					echo json_encode($out);	
					exit();
			}			
		}	
		
		$rloaddata .= "['".cstr($l['return_datesold'])."', '".cstr($l['return_pricesold'])."', '".cstr($l['return_sellingfee'])."', '".cstr($l['return_shippingcost'])."', '".cstr($l['return_netprofit'])."','".cstr($l['return_wheresold'])."', '".cstr($l['returned'])."', '".cstr($l['returned_notes'])."', '".cstr($l['returned_time'])."', '".cstr($l['returned_recieved'])."', '".cstr($l['returned_refunded'])."', '".cstr($l['returned_extracost'])."','".cstr($l['cust_reason'])."','".cstr($l['cust_xtrcost'])."'],				
				";			
		
		$rfielset = array('returns' => array(
		'headers' => "'Date Sold', 'Price Sold', 'Selling Fee', 'Shopping Cost', 'Net Profit', 'Where Sold', 'Return', 'Ret. Notes', 'Time', 'Recieved', 'Refunded', 'Xtra Cost', 'Customer Reason', 'Customer Xtra Cost'",
		/*'rowheaders' => $list['headers'], */
		'width' => "120, 120,120,120,120,120,120,120,120,120,120,120,120,120",
		'startcols' => 14,
		'startrows' => 1, 

		'autosaveurl' => '/Mywarehouse/bcndetails/'.(int)$id.'/returns',
		'colmap' => '{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}')
		);
		
		$this->mysmarty->assign('rheaders', $rfielset['returns']['headers']);
		$this->mysmarty->assign('rrowheaders', $rfielset['returns']['rowheaders']);
		$this->mysmarty->assign('rwidth', $rfielset['returns']['width']);
		$this->mysmarty->assign('rstartcols', $rfielset['returns']['startcols']);
		$this->mysmarty->assign('rstartrows', $rfielset['returns']['startrows']);
		$this->mysmarty->assign('rautosaveurl', $rfielset['returns']['autosaveurl']);
		$this->mysmarty->assign('rcolmap', $rfielset['returns']['colmap']);
		$this->mysmarty->assign('rloaddata', rtrim($rloaddata, ','));
		
		$tcolMap = array(
							0 => 'oldbcn',
							1 => 'title',
							2 => 'location',
							3 => 'status',
							4 => 'status_notes',
							5 => 'sn',
							6 => 'post',
							7 => 'battery',
							8 => 'charger',
							9 => 'hddstatus',
							10 => 'problems',
							11 => 'notes',							
							12 => 'partsneeded',
							13 => 'warranty'
						  );	
		$acolMap = array(
							0 => 'status',
							1 => 'listed',
							2 => 'listed_date',
							3 => 'sold_date',
							4 => 'sold',
							5 => 'soldqn', 
							6 => 'paid',
							7 => 'shipped',
							8 => 'shipped_actual',
							9 => 'shipped_inbound',
							10 => 'ordernotes',
							11 => 'sellingfee',
							12 => 'netprofit',
							13 => 'cost'													
						  );
		$rcolMap = array(
							0 => 'return_datesold',
							1 => 'return_pricesold',
							2 => 'return_sellingfee',
							3 => 'return_shippingcost',
							4 => 'return_netprofit',
							5 => 'return_wheresold', 
							6 => 'returned',
							7 => 'returned_notes',
							8 => 'returned_time',
							9 => 'returned_recieved',
							10 => 'returned_refunded',
							11 => 'returned_extracost'									
						  );
		
		/*if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
					{
						$acolMap[9] = 'cost';
						$acolMap[10] = 'sold';
						$acolMap[11] = 'status';
						unset($acolMap[12]);	
					}
			*/			  
		$history['accounting'] = false;
		$history['testing'] = false;
		$history['other'] = false;
		$history['returns'] = false;
		
		if ($data['logs'])foreach($data['logs'] as $l)
		{
			if (in_array($l['field'], $acolMap))
			{
				if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
					{
						if ($l['field'] != 'sellingfee' && $l['field'] != 'shipped' && $l['field'] != 'netprofit' && $l['field'] != 'cost') $history['accounting'][] = $l;
					}
					else $history['accounting'][] = $l;
			}
			elseif 	(in_array($l['field'], $tcolMap)) $history['testing'][] = $l;
			//elseif 	(in_array($l['field'], $rcolMap)) $history['returns'][] = $l;
			else $history['other'][] = $l;
		}
		$this->mysmarty->assign('history', $history);
		
		$printdata = $data['bcn'];
		$printdata['ptitle'] = substr($data['bcn']['title'], 0, 25);
		$printdata['ptitle2'] = substr($data['bcn']['title'], 25, 25);
		$dataset[] = $printdata;	
		$this->mysmarty->assign('dataset', $dataset);
		$this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printlabel.html'));
		
	}
	$this->mysmarty->view('mywarehouse/details.html');		
}
function fnsku()
{
	if (isset($_POST['fnsku']))
	{
		$fnsku = $this->input->post('fnsku', TRUE);
		$nupref = $this->input->post('nupref', TRUE);
		$text = $this->input->post('text', TRUE);
		$loop = (int)$this->input->post('loop', TRUE);
		$this->mysmarty->assign('fnsku', $fnsku);
		$this->mysmarty->assign('text', $text);
		$text =  str_replace("'", "\'", $nupref.$text);
		$this->mysmarty->assign('loop', $loop);
		$this->mysmarty->assign('nupref', $nupref);
		$this->mysmarty->assign('readytoprint', TRUE);
		$start = 1;
		while ($start <= $loop)
		{
		
		$printdata['fnsku'] = str_replace("'", "\'", $fnsku);
		$printdata['ptitle'] = substr($text, 0, 23);
		$printdata['ptitle2'] = substr($text, 24, 23);
		$printdata['ptitle3'] = substr($text, 47, 23);
		$printdata['ptitle4'] = substr($text, 70, 23);
		$dataset[] = $printdata;
		$start++;
		}
			
		$this->mysmarty->assign('dataset', $dataset);
		$this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printfnlabel.html'));
	}
	$this->mysmarty->view('mywarehouse/fnlabel.html');	
}
function delproc()
{
	exit();
		$this->db->where('waid', 183);
		$this->db->delete('warehouse');
		$this->db->where('waid', 183);
		$this->db->delete('s');
	
}
function DeleteAuction($waid = 0)
{
	if ((int)$this->session->userdata['admin_id'] == 1 || (int)$this->session->userdata['admin_id'] == 2)
	{						
		//$this->Auth_model->wlog($this->Mywarehouse_model->wid2bcn($waid), 0, 'Auction', '', 'DELETED');
		$this->db->update('warehouse_auctions',array('deleted' => 1), array('waid' => (int)$waid)); 
		$this->db->update('warehouse',array('deleted' => 1), array('waid' => (int)$waid));
		Redirect('Mywarehouse');
	}
	else exit('You do not have privileges for this action');	
}
function saveeditor($complete = false)
	{	
	if ($complete) 
	{
		$this->mysmarty->assign('confirm', TRUE);	
		$sessback = $this->_loadsession($this->session->userdata('formfile'));
	  	$this->mysmarty->assign('data', $sessback['formdata']);	
		$this->mysmarty->assign('allauc', $this->Mywarehouse_model->GetAllAuctions());	
		
		$this->mysmarty->view('mywarehouse/BulkStockRecieve_preview.html');
	}
	else
	{
		$colMap = array(
					0 => 'qty',
					1 => 'cost',
					2 => 'mfgname',
					3 => 'mfgpart',
					4 => 'title',
					5 => 'lot',
					6 => 'sku'
				  );
				  
	if (isset($_POST['data']) && $_POST['data']) 
	{
   		$this->load->helper('security');
		//$cnt = 0;
		foreach ($_POST['data'] as $k => $v)
		{
			//$cnt++; 
			//if ($cnt <= 330)
			//{
				foreach ($v as $kk => $vv)
				{
				if (isset($colMap[$kk]))
					{						
					 if ($kk == 1 && trim($vv) != '') $data[$k][$colMap[$kk]] = floatercheck($colMap[$kk], addslashes(xss_clean($vv)));					
					 else $data[$k][$colMap[$kk]] = addslashes(xss_clean($vv));	
					}
				}

			//}
		}
		$formfile = $this->_savesession(array('formdata' => $data));
		$this->session->set_userdata(array('formfile' => $formfile));
		//$this->session->set_userdata('formdata', $data);
		//json_encode($your_array);
		//$this->mysmarty->assign('data', $data);
		//$out['result'] = $this->mysmarty->fetch('mywarehouse/BulkStockRecieve_preview.html');	
		$out['result'] = 'OK';	
		echo json_encode($out);	
	}
	else
	{
		$out['result'] = 'No Data';	
		echo json_encode($out);	
	}
	//Gomail(array('msg_title' => 'Save report', 'msg_body' => printcool($data,true)), 'mr.reece@gmail.com');
	/*
	for ($r = 0, $rlen = count($_POST['data']); $r < $rlen; $r++) {
      $rowId = $r + 1;
      for ($c = 0, $clen = count($_POST['data'][$r]); $c < $clen; $c++) {
        if (!isset($colMap[$c])) {
          continue;
        }
		*/
  
  }
}
function EditAuction($id ='', $page = 0)
{
	if(isset($_POST) && $_POST)
		{	
			$wdata = array();
			if (isset($_POST['wtitle'])) $wdata['wtitle'] = $this->input->post('wtitle', true);
			if (isset($_POST['wcost'])) $wdata['wcost'] = $this->input->post('wcost', true);
			if (isset($_POST['wnotes'])) $wdata['wnotes'] = $this->input->post('wnotes', true);
			if (isset($_POST['wdate'])) $wdata['wdate'] = $this->input->post('wdate', true);
			if (isset($_POST['wvendor'])) $wdata['wvendor'] = $this->input->post('wvendor', true);
			$wdata['wacat'] = (int)$this->input->post('wacat', true);
			if (isset($_POST['shipping'])) $wdata['shipping'] = (float)$this->input->post('shipping', true);
			if (isset($_POST['expenses'])) $wdata['expenses'] = (float)$this->input->post('expenses', true);
			
			if (count($wdata) > 0)
			{
				$this->db->update('warehouse_auctions', $wdata, array('waid' => (int)$id));
				$this->session->set_flashdata('success_msg', 'Auction updated');		
				
				//$olddata  = $this->Mywarehouse_model->GetAuction((int)$id);
				//if ($wdata['wcost'] != $olddata['wcost'])
				//{
					
				$this->load->helper('security');
				$costyes = 0;
				$quantityyes = 0;
				$quantityno = 0;
				$totalitems = 0;
				
				if ((float)$wdata['wcost'] > 0)
				{
					
				$datapool = $this->Mywarehouse_model->getwarehousepricing((int)$id);
				$olddata = $datapool;
				if (!$datapool) exit('No warehouse items');
				$totalitems = count($datapool);
				foreach ($datapool as $k => $d)
				{						
					if (trim(xss_clean($d['cost'])) != '')
					{//
						$costyes = $costyes+($d['cost']);
						$quantityyes++;
					}
					else
					{			
						$quantityno++;
					}
				}
				
				//printcool ($totalitems);
				//printcool ($costyes);
				//printcool ($quantityyes);
				//printcool ($quantityno);
				
				$spreadprice =  sprintf("%01.4f", ($wdata['wcost']/$totalitems));	
							
				if ($quantityyes > 0 && $quantityno > 0) $spreadprice = sprintf("%01.4f", (($wdata['wcost']-$costyes)/$quantityno));				
				}
				else $spreadprice = '';	
		//printcool ($spreadprice);
		//exit();
					if ((float)$wdata['wcost'] > 0)
					{
						foreach ($datapool as $k => $d)
						{		
							if (trim(xss_clean($d['cost'])) != '') 
							{
								$insert['cost'] = trim(xss_clean($d['cost']));
								$this->Auth_model->wlog($d['bcn'], (int)$d['wid'], 'cost', $olddata[$k]['cost'], $insert['cost'], 'EditAuction'.$id);
							}
							else 
							{
								$insert['cost'] = $spreadprice;
								$this->Auth_model->wlog($d['bcn'], (int)$d['wid'], 'cost', 'EMPTY', $insert['cost'], 'EditAuction'.$id);
								
							}
							$this->db->update('warehouse', $insert, array('wid' => $d['wid'])); 						
							unset($insert);
						}
				}
				
				
				
			}
			Redirect('Mywarehouse/open/'.(int)$page);
		}
	$this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());	
	$this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction((int)$id));
	$this->mysmarty->assign('page', $page);
	$this->mysmarty->view('mywarehouse/edit_auction.html');
}
function ttt()
{
	$db['bcn_p1'] = date("m").substr(date("y"), 1, 1);
	printcool($this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']));
}
function SaveBulkStock()
{
	
	$db['dates'] = serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())));
	$db['adminid'] = (int)$this->session->userdata['admin_id'];
	$db['insid'] = (int)$this->Mywarehouse_model->GetNextInsertOrder();
	$db['bcn_p1'] = date("m").substr(date("y"), 1, 1);
	
	$datapool = $this->input->post('data');
	//printcool ($datapool); break;
	$datapool = array_reverse($datapool);
	
	$auction = $this->input->post('auction', true);
	if (!isset($auction['id']) || (isset($auction['id']) && trim($auction['id']) == '')) exit('You have not specified Auction ID');
	
	$this->load->helper('security');
	
	$adata['wtitle'] = trim(xss_clean($auction['id']));
	$cdata['wcost'] = (float)floater(trim(xss_clean($auction['cost'])));
	
	$cdata['shipping'] = (float)floater(trim(xss_clean($auction['shipping'])));
	$cdata['expenses'] = (float)floater(trim(xss_clean($auction['expenses'])));
	
	$adata['wvendor'] = trim(xss_clean($auction['vendor']));
	$adata['wdate'] = CurrentTime();
	$adata['wnotes'] = trim(xss_clean($auction['notes']));
	$adata['wadmin'] = $db['adminid'];
	

	$db['waid'] = $this->Mywarehouse_model->HandleAuction($adata);

	$nextbcn = $this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']);

	//if ((int)$db['bcn_p1'] == 095) $nextbcn = sprintf('%05u',$this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']));
	//else $nextbcn = sprintf('%04u',$this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']));
	
	$insertbatch = array();
	$count = 0;
	
	$costyes = 0;
	$quantityyes = 0;
	$quantityno = 0;
	$totalitems = 0;
	
	if ($cdata['wcost'] > 0)
	{
	foreach ($datapool as $k => $d)
	{		
		if ((int)$d['qty'] < 1) $d['qty'] = 1;
		$totalitems = $totalitems+$d['qty'];
		if (trim(xss_clean($d['cost'])) != '')
		{
			$costyes = $costyes+($d['cost']*$d['qty']);
			$quantityyes = $quantityyes+$d['qty'];
		}
		else
		{			
			$quantityno = $quantityno+$d['qty'];	
		}
	}
	$costdata = 'Lot Cost: '.$cdata['wcost'];
	$spreadprice =  sprintf("%01.4f", ($cdata['wcost']/$totalitems));
	$costdata .= ', Spread Orig: $'.$spreadprice;	
	$costdata .= ', Lot MOD: $'.sprintf("%01.4f", ($cdata['wcost']-$costyes));
	if ($quantityyes > 0 && $quantityno != 0) $spreadprice = sprintf("%01.4f", (($cdata['wcost']-$costyes)/$quantityno));	
	$costdata .= ', Spread MOD: $'.$spreadprice;		
	$costdata .= ', CostYes: $'.$costyes;
	$costdata .= ', QuantityYes: '.$quantityyes.'.pcs';
	$costdata .= ', QuantityNO: '.$quantityno.'.pcs';
	$costdata .= ', TotalItems: '.$totalitems.'.pcs';
		
//	$this->db->update('warehouse_auctions', array('costdata' => $costdata), array('waid' => $db['waid']));
	$this->db->insert('warehouse_auction_expenses', array('wa_id' => (int)$db['waid'],'exp_type'=>'Cost','exp_value'=>$cdata['wcost'],'exp_title' => 'Inbound Costs','exp_time' => CurrentTime(),'exp_time_mk' => mktime(), 'exp_admin' => (int)$this->session->userdata['admin_id'],'exp_notes'=>$costdata));
		
	}
	else $spreadprice = '';	
	
	if ((float)$cdata['shipping'] > 0) { $inboundshipping = $cdata['shipping']/$totalitems; $this->db->insert('warehouse_auction_expenses', array('wa_id' => (int)$db['waid'],'exp_type'=>'Shipping','exp_value'=>$cdata['shipping'],'exp_title' => 'Inbound Shipping','exp_time' => CurrentTime(),  'exp_time_mk' => mktime(), 'exp_admin' => (int)$this->session->userdata['admin_id'])); }
	else $inboundshipping = 0;
	
	if ((float)$cdata['expenses'] > 0) $this->db->insert('warehouse_auction_expenses', array('wa_id' => (int)$db['waid'],'exp_type'=>'Expense','exp_value'=>$cdata['expenses'],'exp_title' => 'Expenses','exp_time' => CurrentTime(), 'exp_time_mk' => mktime(), 'exp_admin' => (int)$this->session->userdata['admin_id']));
	
	foreach ($datapool as $k => $d)
	{	
		if ((int)$d['qty'] < 1) $d['qty'] = 1;

		for ($i = $nextbcn; $i < ($nextbcn + (int)$d['qty']); $i++) 
		{
			 if ($i > 0)
			 {
				 $insert['dates'] = $db['dates'];
				 $insert['createddate'] = CurrentTime();
				 $insert['createddatemk'] = mktime();
				 $insert['adminid'] = $db['adminid'];
				 $insert['insid'] = $db['insid'];
				 $insert['bcn_p1'] = sprintf('%03u', (int)$db['bcn_p1']);
				 $insert['bcn_p2'] = (int)$i;
				// $insert['bcn'] = $db['bcn_p1'].'-'.sprintf('%04u', $insert['bcn_p2']);
				 $insert['bcn'] = $db['bcn_p1'].'-'.$insert['bcn_p2'];
				 $insert['waid'] = $db['waid'];
				 $insert['aucid'] = addslashes($adata['wtitle']);
				 $insert['status'] = 'Not Tested';
				// $insert['shipped_inbound'] = $inboundshipping;
				 
				if (trim(xss_clean($d['cost'])) != '') $insert['cost'] = floater(xss_clean($d['cost']));
				else $insert['cost'] = $spreadprice;
				if (trim(xss_clean($d['title'])) != '') $insert['title'] = xss_clean(addslashes(trim($d['title'])));
				if (trim(xss_clean($d['title'])) != '') $insert['arctitle'] = xss_clean(addslashes(trim($d['title'])));
				if (trim(xss_clean($d['mfgpart'])) != '') $insert['mfgpart'] = trim(xss_clean($d['mfgpart']));
				if (trim(xss_clean($d['mfgname'])) != '') $insert['mfgname'] = trim(xss_clean($d['mfgname']));
				if (trim(xss_clean($d['lot'])) != '') $insert['oldbcn'] = trim(xss_clean($d['lot']));
				//$insert['test'] = 1;
				
				$e = $this->Mywarehouse_model->CheckBCNDoesNotExists($insert['bcn']);
				if (!$e) $this->db->insert('warehouse', $insert); 
				else 
				{
					GoMail(array ('msg_title' => 'SaveBulkStock  Duplicate BCN @ '.CurrentTime(), 'msg_body' => printcool ($nextbcn, true,'nextbcn').printcool ($i, true,'looping').printcool ($insert, true,'insert').printcool($db, true, '$db'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
					exit('DUPLICATE BCN FOUND EXISTS');
				}
				//$insertbatch[] = $insert;
				unset($insert);
				$count++;
				 
			 }			
		}
		$nextbcn = $i;
	}

	if ($count > 0) 
	{
		//$this->db->insert_batch('warehouse', $insertbatch); 
		$this->session->set_flashdata('success_msg', 'Inserted '.($count).' inventory items');
	}
	else
	{
		GoMail(array ('msg_title' => 'SaveBulkStock No inventory items inserted @ '.CurrentTime(), 'msg_body' => printcool ($datapool, true,'datapool'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		 $this->session->set_flashdata('error_msg', 'No inventory items inserted');
	}
	
	Redirect('Mywarehouse/RecieveReport/'.$db['waid']);
}
function servicedb()
{
	$this->db->select("`wid`, `waid`, `deleted`, `aucid`, `title`, `dates`, `adminid`");
		
		$this->db->where("deleted", 1);		
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			printcool ($this->query->result_array());
			foreach ($this->query->result_array() as $k => $v)
			{
			$this->db->where('wid', (int)$v['wid']);
		    //$this->db->delete('warehouse'); 	
			}
		}
}
function saveskutitle($id)
	{
		if ((int)$id == 0) echo 'ERROR';			
		$this->db->update('warehouse_sku', array('title' => htmlspecialchars(trim($this->input->post('str')))), array('wsid' => (int)$id));
		$this->db->select("title");
		$this->db->where("wsid", (int)$id);
		$this->query = $this->db->get('warehouse_sku');
		if ($this->query->num_rows() > 0) 
		{	
			$title = $this->query->row_array();
			echo (htmlspecialchars_decode($title['title']));
		}
		else echo 'ERROR';
	}	
function UpdateSkuFields()
{
	if ((int)$this->input->post('id') == 0) exit();
		$input = CleanInput(trim($this->input->post('value')));		
		$this->db->update('warehouse_sku', array(CleanInput(trim($this->input->post('field'))) => $input), array('wsid' => (int)$this->input->post('id')));
		echo ($input);
}
function UpdateFields()
	{
		if ((int)$this->input->post('id') == 0) echo 'ERROR';
		$input = CleanInput(trim($this->input->post('value')));
		//gomail(array('msg_title'=>$this->input->post('field'), 'msg_body' => printcool ($input,true)), 'mr.reece@gmail.com');
		$updatearray[CleanInput(trim($this->input->post('field')))] = $input;
		if (isset($_POST['tech']) && $_POST['tech']) 
		{
			$updatearray['tech'] = (int)$this->session->userdata['admin_id'];
			$updatearray['techlastupdate'] = CurrentTime();
		}
		else 
		{
			$updatearray['adminid'] = (int)$this->session->userdata['admin_id'];
		}
		
		$view = false;
		if (isset($_POST['view'])) $view = trim($this->input->post('view', true));
	
		$from = trim($this->Mywarehouse_model->GetField(CleanInput(trim($this->input->post('field'))), (int)$this->input->post('id')));

		if ($from != $input)
		{
		$field = CleanInput(trim($this->input->post('field')));
		$input = floatercheck($field, $input);
		if ($field == 'location')
						{			
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updatearray['audit'] = $audit;
							$updatearray['auditmk'] = $mk;
							$this->db->insert('warehouse_audits', array('action_id' => (int)$this->input->post('id'), 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));							
							$this->Mywarehouse_model->DoLocation($input, (int)$this->input->post('id'));
						}
		
		$this->Auth_model->wlog($this->Mywarehouse_model->wid2bcn((int)$this->input->post('id')), (int)$this->input->post('id'), $field, $from, $input, $view);
		
		$this->db->update('warehouse', $updatearray, array('wid' => (int)$this->input->post('id')));
		}
		echo ($input);
	}	
function RefreshField()
	{
		if (!isset($_POST['field'])) exit('ERROR');
		if (trim(CleanInput($_POST['field'] == 'techlastupdate'))) $field = trim(CleanInput($_POST['field']));
		elseif (trim(CleanInput($_POST['field'] == 'tech'))) $field = trim(CleanInput($_POST['field']));
		else exit ('ERROR');
		if ($field) echo ($this->Mywarehouse_model->GetField($field, (int)$_POST['id']));
	}
function index()
	{	
	
	/*
	if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
	{
		
	}
	*/
		$this->open();
	}
function _LaunchDates()
{
		$this->mysmarty->assign('wd1from', date('m/j/Y'));	
		$this->mysmarty->assign('wd1to', date('m/j/Y', strtotime("-7 days")));	
		$this->mysmarty->assign('wd2from', date('m/j/Y', strtotime("-8 days")));	
		$this->mysmarty->assign('wd2to', date('m/j/Y', strtotime("-14 days")));	
		$this->mysmarty->assign('wd3from', date('m/j/Y', strtotime("-15 days")));	
		$this->mysmarty->assign('wd3to',  date('m/j/Y', strtotime("-21 days")));
		$this->mysmarty->assign('wd4from', date('m/j/Y', strtotime("-22 days")));	
		$this->mysmarty->assign('wd4to',  date('m/j/Y', strtotime("-28 days")));
		$this->mysmarty->assign('wcal', TRUE);	
}
function open($page = 1, $cat = 1)
	{	
		ini_set('memory_limit','2048M');
		$this->_LaunchDates();
		$this->session->unset_userdata('showstatus');
		$this->session->set_userdata('warehouse_area', '');
				
		$load = $this->Mywarehouse_model->GetList((int)$page, (int)$cat);
		//printcool ($load);
		$this->mysmarty->assign('list', $load['results']);
		$this->mysmarty->assign('pages', $load['pages']);
		unset($load['results']);
		unset($load['pages']);
		
		if (count($load['per']['statuses']) > 0) foreach ($load['per']['statuses'] as $k => $v) ksort($load['per']['statuses'][$k]);
		if (is_array($load['sum']['statuses'])) ksort($load['sum']['statuses']);
		$this->mysmarty->assign('calc', $load);
	
		
		/*$this->mysmarty->assign('accounting', $load['accounting']);
		$this->mysmarty->assign('accounting_sold', $load['accounting_sold']);
		$this->mysmarty->assign('accounting_notsold', $load['accounting_notsold']);
		$this->mysmarty->assign('accounting_ns_hold', $load['accounting_ns_hold']);
		$this->mysmarty->assign('accounting_ns_other', $load['accounting_ns_other']);

		$this->mysmarty->assign('sumaccounting', $load['sumaccounting']);
		$this->mysmarty->assign('sumaccounting_sold', $load['sumaccounting_sold']);		
		$this->mysmarty->assign('sumaccounting_notsold', $load['sumaccounting_notsold']);
		$this->mysmarty->assign('sumaccounting_ns_hold', $load['sumaccounting_ns_hold']);
		$this->mysmarty->assign('sumaccounting_ns_other', $load['sumaccounting_ns_other']);
		
		$this->mysmarty->assign('cnt', $load['cnt']);
		$this->mysmarty->assign('cnt_sold', $load['cnt_sold']);
		$this->mysmarty->assign('cnt_notsold', $load['cnt_notsold']);
		$this->mysmarty->assign('cnt_ns_hold', $load['cnt_ns_hold']);
		$this->mysmarty->assign('cnt_ns_other', $load['cnt_ns_other']);
		$this->mysmarty->assign('cntaccounting', $load['cntaccounting']);
		$this->mysmarty->assign('cntaccounting_sold', $load['cntaccounting_sold']);		
		$this->mysmarty->assign('cntaccounting_notsold', $load['cntaccounting_notsold']);
		$this->mysmarty->assign('cntaccounting_ns_other', $load['cntaccounting_ns_other']);
		$this->mysmarty->assign('cntaccounting_ns_hold', $load['cntaccounting_ns_hold']);
		
		$this->mysmarty->assign('statuses', $load['statuses']);
		$this->mysmarty->assign('sumstatuses', $load['sumstatuses']);
		$this->mysmarty->assign('location', $load['location']);
		$this->mysmarty->assign('locationsum', $load['locationsum']);
		$this->mysmarty->assign('auctionshipping', $load['auctionshipping']);
		$this->mysmarty->assign('auctionexpenses', $load['auctionexpenses']);
		$this->mysmarty->assign('eachauctionshipping', $load['eachauctionshipping']);
		$this->mysmarty->assign('eachauctionexpenses', $load['eachauctionexpenses']);
		
		$this->mysmarty->assign('sn', $load['sn']);
		$this->mysmarty->assign('snsum', $load['snsum']);
		*/
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('cat', (int)$cat);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());	
		$this->mysmarty->view('mywarehouse/main.html');
	}
function ModWarehouseCat($id = 0)
{
	if ($_POST['wcat'] != '')
	{	
		if ($id == 0) $this->db->insert('warehouse_auction_categories', array('wacat_title' => $this->input->post('wcat', TRUE)));
		else $this->db->update('warehouse_auction_categories', array('wacat_title' => $this->input->post('wcat', TRUE)), array('wacat_id' => (int)$id));
	}
	Redirect('Mywarehouse/EditAuctionCategories');
	//Redirect('Mywarehouse/open/'.(int)$page);	
}
function DeleteAucCategory($wacat = '')
{
	$this->db->where('wacat_id', (int)$wacat);
	$this->db->delete('warehouse_auction_categories'); 
	$this->db->select('waid');
	$this->db->where('wacat', (int)$wacat);
	$d = $this->db->get('warehouse_auctions');
	if ($d->num_rows() > 0) 
		{
			foreach ($d->result_array() as $v)
			{
				$this->db->update('warehouse_auctions', array('wacat' => 0), array('waid' => $v['waid']));				
			}
		}
	Redirect('Mywarehouse/EditAuctionCategories');
}
function EditAuctionCategories()
{
	$this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());	

		$this->mysmarty->view('mywarehouse/main_cat_auc.html');	
}
function moveauctions()
{exit();
	$this->db->select('waid');
	$this->db->where('waid <', 149);
	$this->db->where('waid >', 0);
	$d = $this->db->get('warehouse_auctions');
	if ($d->num_rows() > 0) 
		{
			foreach ($d->result_array() as $v)
			{
				$this->db->update('warehouse_auctions', array('wacat' => 6), array('waid' => $v['waid']));				
			}
		}
}
function packs($aucid = '')
	{
		if (trim($aucid) == '') exit('Bad ID');
		$load = $this->Mywarehouse_model->GetPacks(trim($aucid));
		$this->mysmarty->assign('list', $load);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction(trim($aucid)));
		$this->mysmarty->view('mywarehouse/main_auc.html');
	}
function TestingSku($sku = 0, $return = false)
{
	$single = false;
	$this->mysmarty->assign('sku', (int)$sku);	
	$this->testing(0,'', $return, (int)$sku);
	
}
function testing($id = '', $focus = '', $return = false, $sku = false)
{//if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
	$this->session->set_userdata('warehouse_area', 'testing');
	if (is_int($sku)) $id = $sku;
	if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
	$list = $this->Mywarehouse_model->GetTesting((int)$id, $sku);

	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'oldbcn',
							3 => 'arctitle',
							4 => 'location',
							5 => 'status',
							6 => 'status_notes',
							7 => 'sn',
							8 => 'post',
							9 => 'battery',
							10 => 'charger',
							11 => 'hddstatus',
							12 => 'problems',
							13 => 'notes',							
							14 => 'partsneeded',
							15 => 'warranty'
						  );
				
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'Old BCN',
							3 => 'Arc Title',
							4 => 'Location',							
							5 => 'Status',
							6 => 'Status Notes',
							7 => 'SN',
							8 => 'POST',
							9 => 'Battery',
							10 => 'Charger',
							11 => 'HDD Status',
							12 => 'Problems',
							13 => 'Notes',
							14 => 'Parts Needed',
							15 => 'Warranty'
											
						  );
				$out = '';
				$sout = '';
				$sessback = $this->_loadsession($this->session->userdata('sessfile'));
				$saveid = $sessback['acclot'];
				$saverel = $sessback['accrel'];
				
				//printcool ($_POST);

				if ($saveid != (int)$id) { echo json_encode('!!!!! CANNOT SAVE. YOU HAVE ANOTHER TESTING EDITOR OPEN !!!!!'); }	//printcool ($_POST);
				else {
				foreach($_POST as $d)

				{
					foreach($d as $dd)
					{
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					if ($dd[2] != $dd[3])
					{
					$this->Auth_model->wlog($saverel[(int)$dd[0]]['bcn'], $saverel[(int)$dd[0]]['wid'], $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$saverel[(int)$dd[0]]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $saverel[(int)$dd[0]]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					if ($colMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;
							$this->db->insert('warehouse_audits', array('action_id' => (int)$saverel[(int)$dd[0]]['wid'], 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$saverel[(int)$dd[0]]['wid']);
						}
					
					$this->db->update('warehouse', array($colMap[(int)$dd[1]] => $dd[3], 'tech' => (int)$this->session->userdata['admin_id'], 'techlastupdate' => CurrentTime()), array('wid' => $saverel[(int)$dd[0]]['wid']));
					
					
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}				
				}				
				echo json_encode($out);	
				}
		}
		else 
		{
			
			
			
			//
	
		$fielset = array('testing' => array(
		'headers' => "'GO', 'BCN','Old BCN', 'Arc Title','Location', 'Status', 'Status notes','SN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes',  'Parts Needed', 'Warranty',  'LastUpdt', 'Tech'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 150, 120, 125, 125,125, 50, 50, 50, 100, 150, 200,125, 125, 125, 125", 
		'startcols' => 18,
		'startrows' => count($list['data']), 
		'autosaveurl' => "/Mywarehouse/Testing/".(int)$id,
		'reloadurl' => "/Mywarehouse/Testing/".(int)$id.'/0/TRUE',
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "dropdown", source: ['.$this->statuses['testingstring'].']},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{},{},{},{},{},{readOnly: true},{readOnly: true}') 
		);
	
		
		if ($list)
		{	
			if (is_array($id) || is_int($sku)) 
				{
					$cn = 1;
					if (is_int($sku)) $goid = $list['auctions'];
					else $goid = $id;
					
					foreach ($goid as $i)
					{
						if ($cn == 1) $this->db->where("waid", (int)$i['waid']);				
						else $this->db->or_where("waid", (int)$i['waid']);				
						$cn++;
						$this->mysmarty->assign('multipleids', TRUE);
					}					
				}
				else $this->db->where("waid", (int)$id);
							
				$this->query = $this->db->get('warehouse_auctions');
				if ($this->query->num_rows() > 0) 
				{
					$auctionresults = $this->query->result_array();
//printcool ($auctionresults);
//printcool ($list['data']);
					$calc = $this->Mywarehouse_model->doaccounting($id, $list['data'],$auctionresults);	
					//printcool ($calc['sum']);	
					$this->mysmarty->assign('idata', $calc);
				}

			
			$sesfile = $this->_savesession(array('accrel' => $list['headers'], 'acclot' => (int)$id));
			$this->session->set_userdata(array('sessfile' => $sesfile));
			$loaddata = '';
			$adms = $this->Mywarehouse_model->GetAdminList();
			
			foreach ($list['data'] as $k => $l)
			{
			
				$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>",cstr($l['bcn']),cstr($l['oldbcn']),cstr($l['arctitle']),cstr($l['location']),cstr($l['status']),cstr($l['status_notes']),cstr($l['sn']),cstr($l['post']),cstr($l['battery']),cstr($l['charger']),cstr($l['hddstatus']),cstr($l['problems']),cstr($l['notes']),cstr($l['partsneeded']),cstr($l['warranty']),cstr($l['techlastupdate']),$adms[$l['tech']]);
			}		
		}	
		
		$loaddata = '';
		if (count($list['data']) > 0)
			{
			foreach ($returndata as $kr => $r)
				{
					$loaddata .= "["; 
					foreach ($r as $krr => $rr)
					{
						$loaddata .= "'".$rr."',"; 
						$returndata[$kr][$krr]= stripslashes($rr);
					}
					$loaddata .= "],"; 
					
				}	
			}	
			if ($return)
			{	
				echo json_encode($returndata);

				exit();
			}
		
		if (is_int($sku))
		{ 	
			$fielset['testing']['autosaveurl'] = '/Mywarehouse/TestingSku/'.(int)$sku;
			$fielset['testing']['reloadurl'] = '/Mywarehouse/TestingSku/'.(int)$sku.'/TRUE';
		}
		$this->mysmarty->assign('headers', $fielset['testing']['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['testing']['rowheaders']);
		$this->mysmarty->assign('width', $fielset['testing']['width']);
		$this->mysmarty->assign('startcols', $fielset['testing']['startcols']);
		$this->mysmarty->assign('startrows', $fielset['testing']['startrows']);
		$this->mysmarty->assign('autosaveurl', $fielset['testing']['autosaveurl']);
		$this->mysmarty->assign('reloadurl', $fielset['testing']['reloadurl']);
		$this->mysmarty->assign('colmap', $fielset['testing']['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('list', $list['data']);
		$this->mysmarty->assign('copyrows', count($list['data']));
		$this->mysmarty->assign('id', (int)$id);
		$au =$this->Mywarehouse_model->AuctionIdToName((int)$id);
		
		if ($au) 
		{
			$this->mysmarty->assign('atitle', $au['wtitle']);
			$this->mysmarty->assign('anotes', $au['wnotes']);
		}
		
		$this->mysmarty->assign('focus', (int)$focus);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
		
		
		if ((int)$focus > 0) $this->SingleTesting((int)$focus);
		
		$this->mysmarty->view('mywarehouse/testing.html');
		
		}
}
function SingleTesting($id = '')
{//if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
	$list = $this->Mywarehouse_model->GetSingle((int)$id);	
	
	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'oldbcn',
							3 => 'arctitle',
							4 => 'location',
							5 => 'status',
							6 => 'status_notes',
							7 => 'sn',
							8 => 'post',
							9 => 'battery',
							10 => 'charger',
							11 => 'hddstatus',
							12 => 'problems',
							13 => 'notes',							
							14 => 'partsneeded',
							15 => 'warranty'
						  );
				
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'Old BCN',
							3 => 'Arc Title',
							4 => 'Location',
							5 => 'Status',
							6 => 'Status Notes',
							7 => 'SN',
							8 => 'POST',
							9 => 'Battery',
							10 => 'Charger',
							11 => 'HDD Status',
							12 => 'Problems',
							13 => 'Notes',							
							14 => 'Parts Needed',
							15 => 'Warranty'
											
						  );
				
				$out = '';

				$sout = '';						
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					
					if ($dd[2] != $dd[3])
					{
					$this->Auth_model->wlog($list['data'][0]['bcn'], (int)$id, $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$list['data'][0]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $list['data'][0]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					$this->db->update('warehouse', array($colMap[(int)$dd[1]] => $dd[3], 'tech' => (int)$this->session->userdata['admin_id'], 'techlastupdate' => CurrentTime()), array('wid' => (int)$id));
					
					if ($colMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;
							$this->db->update('warehouse', $updt, array('wid' => (int)$id));
							$this->db->insert('warehouse_audits', array('action_id' => (int)$id, 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$id);
						}
						
						
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}				
				}				
				echo json_encode($out);		
		}
		else 
		{
		if ($list)
		{			
			$loaddata = '';
			$adms = $this->Mywarehouse_model->GetAdminList();
			foreach ($list['data'] as $k => $l)
			{
				$loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>', '".cstr($l['bcn'])."', '".cstr($l['oldbcn'])."', '".cstr($l['arctitle'])."', '".cstr($l['location'])."', '".cstr($l['status'])."', '".cstr($l['status_notes'])."', '".cstr($l['sn'])."', '".cstr($l['post'])."', '".cstr($l['battery'])."', '".cstr($l['charger'])."', '".cstr($l['hddstatus'])."', '".cstr($l['problems'])."', '".cstr($l['notes'])."', '".cstr($l['partsneeded'])."','".cstr($l['warranty'])."', '".cstr($l['techlastupdate'])."', '".$adms[$l['tech']]."'],
				";				
			}		
		}	
		
		$fielset = array('testing' => array(
		'headers' => "'GO', 'BCN','Old BCN', 'Arc Title','Location', 'Status', 'Status notes','SN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes',  'Parts Needed', 'Warranty',  'LastUpdt', 'Tech'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 150, 120, 125, 125,125, 50, 50, 50, 100, 150, 200,125, 125, 125, 125", 
		'startcols' => 18,  
		'startrows' => 1, 
		'autosaveurl' => "/Mywarehouse/SingleTesting/".(int)$id,
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "dropdown", source: ['.$this->statuses['testingstring'].']},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{},{},{},{},{},{readOnly: true},{readOnly: true}') 
		);
		
		$this->mysmarty->assign('sheaders', $fielset['testing']['headers']);
		$this->mysmarty->assign('srowheaders', $fielset['testing']['rowheaders']);
		$this->mysmarty->assign('swidth', $fielset['testing']['width']);
		$this->mysmarty->assign('sstartcols', $fielset['testing']['startcols']);
		$this->mysmarty->assign('sstartrows', $fielset['testing']['startrows']);
		$this->mysmarty->assign('sautosaveurl', $fielset['testing']['autosaveurl']);
		$this->mysmarty->assign('scolmap', $fielset['testing']['colmap']);
		$this->mysmarty->assign('sloaddata', rtrim($loaddata, ','));		
		}
}

function fixpaidnosold()
{
		exit();

}
function AccountingSku($sku = 0, $return = false)
{
	$single = false;
	$this->mysmarty->assign('sku', (int)$sku);	
	$this->Accounting(0,'', $return,'', '', $single, (int)$sku);
	
}
function AccountingStatus($cat = 0, $single= false, $return = false)
{
	if ($single == 'FALSE') $single = false;
	
	ini_set('memory_limit','1024M');
	$status = $this->session->userdata('showstatus');
	if (isset($_POST['status'])) $status = trim($this->input->post('status',true));
	unset($_POST['status']);	
	$this->mysmarty->assign('showstatus', $status);	
	$this->session->set_userdata('showstatus', $status);
	$catlink = (int)$cat;
	if (!$single)
	{		
		$this->db->select('waid');
		$this->db->where('wacat', (int)$cat);
		$this->db->where("deleted", 0);
		$c = $this->db->get('warehouse_auctions');
		if ($c->num_rows() > 0)
		{
			
			$this->Accounting($c->result_array(),'', $return,addslashes($status), $catlink, $single);
		}
	}
	else $this->Accounting((int)$cat,'', $return,$status, $catlink, $single);
	
}
function SingleAccounting($id = '') 
{ //if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
	$this->_LaunchDates();
	$list = $this->Mywarehouse_model->GetSingle((int)$id);	
                                               
	if(isset($_POST) && $_POST)
		{
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'oldbcn',
							3 => 'title',
							4 => 'location',
							5 => 'audit',
							6 => 'status',
							 
							7 => 'sold_id',
							8 => 'soldqn', 							
							9 => 'paid',
							10 => 'shipped',
							11 => 'shipped_actual',
							12 => 'shipped_inbound',
							13 => 'ordernotes',
							14 => 'sellingfee',
							15 => 'netprofit',
							16 => 'cost',
							
							17 => 'listed',
							18 => 'listed_date',
							19 => 'sold_date',
							20 => 'sold'
												
						  );
				
				
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'Old BCN',
							3 => 'Title',
							4 => 'Location',
							5 => 'Audit',
							6 => 'Status',
							
							7 => 'Sold ID',
							8 => 'Sold QN',
							9 => 'Price Sold',
							10 => 'Shipping Cost',
							11 => 'Actual Sh.',
							12 => 'Inbound Sh.',
							13 => 'Order Notes',
							14 => 'Selling Fee',
							15 => 'Net Profit',
							16 => 'Cost',							
														 
							17 => 'Where Listed',
							18 => 'Date Listed',
							19 => 'Date Sold',
							20 => 'Where Sold'
						  );
				if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
				{					
					$colMap[14] =$colMap[17];
					$colMap[15] =$colMap[18];
					$colMap[16] =$colMap[19];	
					unset($colMap[17]);
                                        unset($colMap[18]);
					unset($colMap[19]);
					$bcolMap[14] =$bcolMap[17];
					$bcolMap[15] =$bcolMap[18];
					$bcolMap[16] =$bcolMap[19];
					unset($bcolMap[17]);
					unset($bcolMap[18]);
					unset($bcolMap[19]);
				}				
				
				
				$out = '';				
				$sout = '';						
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					$dd[3] = floatercheck($colMap[(int)$dd[1]], $dd[3]);
					if ($dd[2] != $dd[3])
					{
					$this->Auth_model->wlog($list['data'][0]['bcn'], (int)$id, $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$list['data'][0]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';					
					$sout .= $list['data'][0]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					$updt = array($colMap[(int)$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
					if ($colMap[(int)$dd[1]] == 'status')  $updt['status_notes'] = 'Changed from: '.$dd[2];					
																				// $this->Mywarehouse_model->GetStatusNotes((int)$id).' | 					
					if ($colMap[(int)$dd[1]] == 'audit') { $updt['audit'] = CurrentTime(); $this->db->insert('warehouse_audits', array('action_id' => (int)$id, 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id'])); }
					
					if ($colMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;		
							$this->db->insert('warehouse_audits', array('action_id' => (int)$id, 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));			
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$id);	
						}
					
					$this->db->update('warehouse', $updt, array('wid' => (int)$id));					
								
					if ($colMap[(int)$dd[1]] == 'paid' || $colMap[(int)$dd[1]] == 'cost' || $colMap[(int)$dd[1]] == 'sellingfee'|| $colMap[(int)$dd[1]] == 'shipped_actual' || ($colMap[(int)$dd[1]] == 'status' && $dd[3] == 'Scrap')) 
					{
						$this->load->model('Myseller_model'); 
							 $nope = array();
							 $this->Myseller_model->HandleBCN($nope,$nope,(int)$id);	
						//$this->Mywarehouse_model->ReProcessNetProfit((int)$id);	
					}
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}
				}				
				echo json_encode($out);		
		}
		else 
		{
		if ($list)
		{			
			$loaddata = '';
			$adms = $this->Mywarehouse_model->GetAdminList();
			foreach ($list['data'] as $k => $l)
			{
				if (trim($l['audit']) != '') $audit = 1;
					else $audit = 0;	
					
				if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
				{
				$loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>', '".cstr($l['bcn'])."', '".cstr($l['oldbcn'])."', '".cstr($l['title'])."', '".cstr($l['location'])."', '".cstr($audit)."','".cstr($l['status'])."', '".cstr($l['sold_id'])."','".cstr($l['soldqn'])."','".cstr($l['paid'])."',  '".cstr($l['shipped'])."', '".cstr($l['shipped_actual'])."', '".cstr($l['shipped_inbound'])."', '".cstr($l['ordernotes'])."', '".cstr($l['sellingfee'])."',  '".cstr($l['netprofit'])."', '".cstr($l['cost'])."', '".cstr($l['listed'])."', '".cstr($l['listed_date'])."', '".cstr($l['sold_date'])."', '".cstr($l['sold'])."', '".cstr($l['aupdt'])."'],
				";
				
				//HERE 
				}
				else $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>', '".cstr($l['bcn'])."', '".cstr($l['oldbcn'])."', '".cstr($l['title'])."', '".cstr($l['location'])."', '".cstr($audit)."','".cstr($l['status'])."', '".cstr($l['sold_id'])."','".cstr($l['soldqn'])."','".cstr($l['paid'])."',   '".cstr($l['shipped'])."',  '".cstr($l['shipped_actual'])."', '".cstr($l['shipped_inbound'])."','".cstr($l['ordernotes'])."',  '".cstr($l['listed'])."', '".cstr($l['listed_date'])."', '".cstr($l['sold_date'])."', '".cstr($l['sold'])."', '".cstr($l['aupdt'])."'],
				";
				//HERE
				
			}		
		}	
		
		if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
		{
		$fielset = array('accounting' => array(
		'headers' => "'GO','BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status','Sold ID', 'Sold QN', 'Price Sold', 'Sh.Cost', 'Act.Sh.',  'Inb.Sh.','Order Notes', 'SellFee', 'Net', 'Cost','Where Listed', 'Date Listed', 'Date Sold', 'Where Sold',   'Last Upd'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 300, 100, 40, 125,  80, 80, 60, 60,  60,125,80, 80, 80, 80, 80, 80, 80, 80, 80", 
		'startcols' => 22, 
		'startrows' => 1, 
		'autosaveurl' => "/Mywarehouse/SingleAccounting/".(int)$id,		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{readOnly: true},{readOnly: true},{},{},{},{},{},{},{readOnly: true},{},{},{},{},{},{readOnly: true}')
		);
		}
		else
		{
		$fielset = array('accounting' => array(
		'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status','Sold ID', 'Sold QN.', 'Price Sold', 'Sh.Cost', 'Act.Sh.', 'Inb.Sh.', 'Order Notes', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Last Upd'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 300, 100, 40, 125, 80, 80, 80, 60, 60,60, 125, 80, 80, 80, 80, 80", 
		'startcols' => 19, 
		'startrows' => 1, 
		'autosaveurl' => "/Mywarehouse/SingleAccounting/".(int)$id,		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{readOnly: true},{readOnly: true},{},{},{},{},{},{},{},{},{},{readOnly: true}')
		);			
		}
	
		
		$this->mysmarty->assign('sheaders', $fielset['accounting']['headers']);
		$this->mysmarty->assign('srowheaders', $fielset['accounting']['rowheaders']);
		$this->mysmarty->assign('swidth', $fielset['accounting']['width']);
		$this->mysmarty->assign('sstartcols', $fielset['accounting']['startcols']);
		$this->mysmarty->assign('sstartrows', $fielset['accounting']['startrows']);
		$this->mysmarty->assign('sautosaveurl', $fielset['accounting']['autosaveurl']);
		$this->mysmarty->assign('scolmap', $fielset['accounting']['colmap']);
		$this->mysmarty->assign('sloaddata', rtrim($loaddata, ','));
		
		//$this->mysmarty->assign('focus', (int)$id);
		
		//$this->mysmarty->view('mywarehouse/accounting.html');
		}
}
function Accounting($id = '', $focus = '', $return = false, $status = '', $catlink = false, $single= false, $sku = false)
{ //if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');

ini_set('memory_limit','2048M');

	if (!isset($this->ghostpage)) $ghostpage = false;
	else $ghostpage = (int)$this->ghostpage;	
	
	if (!isset($this->lesspage)) { $lesspage = false; $lesstype = false; }
	else { $lesspage = (int)$this->lesspage; $lesstype = $this->lesstype; }	
	$this->_LaunchDates();
	if ($status == '') $this->session->unset_userdata('showstatus');
	if ($id === 0) $list = array(); 
	elseif ($id === '')  $id = $this->Mywarehouse_model->GetLastAuc(); 
	if (is_int($sku)) $list = $this->Mywarehouse_model->GetAccounting($id, $status, $sku);
	elseif (is_array($id)) $list = $this->Mywarehouse_model->GetAccounting($id, $status);
	else $list = $this->Mywarehouse_model->GetAccounting((int)$id, $status, false, $ghostpage, $lesspage,$lesstype);	
	if (is_int($sku)) $accurl = 'AccountingSku/'.(int)$sku.'/TRUE';
	elseif (is_int($catlink)) $accurl = 'AccountingStatus/'.$catlink.'/TRUE';
	else $accurl = 'Accounting/'.$id;
			
	//if ($id == 0) printcool ($list);
	$this->session->set_userdata('warehouse_area', 'accounting');
 
	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'oldbcn',
							3 => 'title',
							4 => 'location',
							5 => 'audit',
							6 => 'status',
							
							7 => 'sold_id',
							8 => 'soldqn', 							
							9 => 'paid',
							10 => 'shipped',
							11 => 'shipped_actual',
							12 => 'shipped_inbound',
							13 => 'ordernotes',
							14 => 'sellingfee',
							15 => 'netprofit',
							16 => 'cost',							
							
							17 => 'listed',
							18 => 'listed_date',
							19 => 'sold_date',
							20 => 'sold'										
						  );
				
				
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'Old BCN',
							3 => 'Title',
							4 => 'Location',
							5 => 'Audit',
							6 => 'Status',
							
							7 => 'Sold ID',
							8 => 'Sold QN',
							9 => 'Price Sold',
							10 => 'Shipping Cost',
							11 => 'Actual Sh.',
							12 => 'Inbound Sh.',
							13 => 'Order Notes',
							14 => 'Selling Fee',
							15 => 'Net Profit',
							16 => 'Cost', 

							17 => 'Where Listed',
							18 => 'Date Listed',
							19 => 'Date Sold',
							20 => 'Where Sold'
						  );
				
				if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
				{			
					$colMap[14] =$colMap[17];
					$colMap[15] =$colMap[18];
					$colMap[16] =$colMap[19];	
					unset($colMap[17]);
					unset($colMap[18]);
					unset($colMap[19]);
					$bcolMap[14] =$bcolMap[17];
					$bcolMap[15] =$bcolMap[18];
					$bcolMap[16] =$bcolMap[19];
					unset($bcolMap[17]);
					unset($bcolMap[18]);
					unset($bcolMap[19]); 
				}	
				
				$out = '';
				$sout = '';
				$sessback = $this->_loadsession($this->session->userdata('sessfile'));
				$saveid = $sessback['acclot'];
				$saverel = $sessback['accrel'];
				$saveurl = $sessback['accurl'];
				
				if ($saveid != (int)$id && $saveurl != $catlink) { echo json_encode(array('msg' => '!!!!! CANNOT SAVE. YOU HAVE ANOTHER ACCOUNTING EDITOR OPEN !!!!!')); }	//printcool ($_POST);
				else {					
					
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{					
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					$dd[3] = floatercheck($colMap[(int)$dd[1]], $dd[3]);
					if ($dd[2] != $dd[3])
					{
						if (!isset($saverel[(int)$dd[0]]['bcn'])) GoMail(array ('msg_title' => '!isset($saverel[(int)$dd[0]][bcn]) @ '.CurrentTime(), 'msg_body' => printcool ($d, true, 'D').printcool ($saverel,true,'saverel').printcool($_POST, true, 'POST'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						if (!isset($saverel[(int)$dd[0]]['wid'])) GoMail(array ('msg_title' => '!isset($saverel[(int)$dd[0]][wid]) @ '.CurrentTime(), 'msg_body' => printcool ($d, true, 'D').printcool ($saverel,true,'saverel').printcool($_POST, true, 'POST'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						
					$this->Auth_model->wlog($saverel[(int)$dd[0]]['bcn'], $saverel[(int)$dd[0]]['wid'], $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$saverel[(int)$dd[0]]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $saverel[(int)$dd[0]]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					
					$updt = array($colMap[(int)$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
					if ($colMap[(int)$dd[1]] == 'status')
                    {
                        $updt['status_notes'] = 'Changed from: '.$dd[2];

                        if ($dd[3] == 'On Hold') $updt['vended'] = 2;
                        elseif ($dd[3] == 'Sold') $updt['vended'] = 1;
                        else $updt['vended'] = 0;
																				// $this->Mywarehouse_model->GetStatusNotes((int)$id).' |
                    }
                        if ($colMap[(int)$dd[1]] == 'audit') { $updt['audit'] = CurrentTime(); $this->db->insert('warehouse_audits', array('action_id' => $saverel[(int)$dd[0]]['wid'], 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id'])); }
					
					if ($colMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;			
							$this->db->insert('warehouse_audits', array('action_id' => (int)$saverel[(int)$dd[0]]['wid'], 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));	
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$saverel[(int)$dd[0]]['wid']);				
						}
					
					$this->db->update('warehouse', $updt, array('wid' => $saverel[(int)$dd[0]]['wid']));					
					if ($colMap[(int)$dd[1]] == 'paid' || $colMap[(int)$dd[1]] == 'cost' || $colMap[(int)$dd[1]] == 'sellingfee'|| $colMap[(int)$dd[1]] == 'shipped_actual' || ($colMap[(int)$dd[1]] == 'status' && $dd[3] == 'Scrap'))
					{
						$this->load->model('Myseller_model'); 
						$nope = array();
						$this->Myseller_model->HandleBCN($nope,$nope,(int)$saverel[(int)$dd[0]]['wid']);	
						// $this->Mywarehouse_model->ReProcessNetProfit((int)$saverel[(int)$dd[0]]['wid']);
					}
					unset($updt);
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}
				}				
				echo json_encode($out);	
				}
		}
		else 
		{
		
		if ($list)
		{
				if (is_array($id) || is_int($sku)) 
				{
					$cn = 1;
					if (is_int($sku)) $goid = $list['auctions'];
					else $goid = $id;
					
					foreach ($goid as $i)
					{
						if ($cn == 1) $this->db->where("waid", (int)$i['waid']);				
						else $this->db->or_where("waid", (int)$i['waid']);				
						$cn++;
						$this->mysmarty->assign('multipleids', TRUE);
					}					
				}
				else $this->db->where("waid", (int)$id);				
				$this->query = $this->db->get('warehouse_auctions');
				if ($this->query->num_rows() > 0) 
				{
					$auctionresults = $this->query->result_array();
					
//printcool ($auctionresults);
//printcool ($list['data']);
					$calc = $this->Mywarehouse_model->doaccounting($id, $list['data'],$auctionresults);	
					
					//printcool ($calc['sum']);	
					$this->mysmarty->assign('idata', $calc);
				}
			
			$sesfile = $this->_savesession(array('accrel' => $list['headers'], 'acclot' => (int)$id, 'accurl' => $accurl));
			$this->session->set_userdata(array('sessfile' => $sesfile));
			$loaddata = '';
			$adms = $this->Mywarehouse_model->GetAdminList();
			foreach ($list['data'] as $k => $l)
			{
				if (trim($l['audit']) != '') $audit = 1;
					else $audit = 0;	
					
				if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
				{
					$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>",
					cstr($l['bcn']),cstr($l['oldbcn']),cstr($l['title']),cstr($l['location']),cstr($audit),cstr($l['status']),cstr($l['sold_id']),cstr($l['soldqn']),cstr($l['paid']),cstr($l['shipped']),cstr($l['shipped_actual']),cstr($l['shipped_inbound']),cstr($l['ordernotes']),cstr($l['sellingfee']),cstr($l['netprofit']),cstr($l['cost']),cstr($l['listed']),cstr($l['listed_date']),cstr($l['sold_date']),cstr($l['sold']),cstr($l['aupdt']));
				}
				else $returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>",cstr($l['bcn']),cstr($l['oldbcn']),cstr($l['title']),cstr($l['location']),cstr($audit),cstr($l['status']),cstr($l['sold_id']),cstr($l['soldqn']),cstr($l['paid']),cstr($l['shipped']),cstr($l['shipped_actual']),cstr($l['shipped_inbound']),cstr($l['ordernotes']),cstr($l['listed']),cstr($l['listed_date']),cstr($l['sold_date']),cstr($l['sold']),cstr($l['aupdt']));						
			}		
		}
		$loaddata = '';
		if (count($list['data']) > 0)
			{
			foreach ($returndata as $kr => $r)
				{
					$loaddata .= "["; 
					foreach ($r as $krr => $rr)
					{
						$loaddata .= "'".$rr."',"; 
						$returndata[$kr][$krr]= stripslashes($rr);
					}
					$loaddata .= "],"; 
					
				}	
			}	
			if ($return)
			{
				echo json_encode($returndata);

				exit();
			}
		
			
		
		
		//printcool ($list['headers']);
		$this->mysmarty->assign('list', $list['data']);
		$this->mysmarty->assign('id', (int)$id);
		$au =$this->Mywarehouse_model->AuctionIdToName((int)$id);		
		if ($au) 
		{
			$this->mysmarty->assign('atitle', $au['wtitle']);
			$this->mysmarty->assign('anotes', $au['wnotes']);
		}
		else
		{
			$this->mysmarty->assign('atitle', '');
			$this->mysmarty->assign('anotes', '');
		}
		$this->mysmarty->assign('focus', (int)$focus);
		//$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
		
		if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
		{

//HERE
		$fielset = array('accounting' => array(
		'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status', 'Sold ID','Sold QN','Price Sold', 'Sh.Cost','Act.Sh.','Inb.Sh.', 'Order Notes', 'SellFee', 'Net', 'Cost',  'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold','Last Upd'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 300, 100, 40, 125,  80, 80, 60, 60,  60,125,80, 80, 80, 80, 80, 80, 80, 80, 80", 
		'startcols' => 22, 
		'startrows' => 10, 
		'autosaveurl' => "/Mywarehouse/Accounting/".(int)$id,	
		'reloadurl' => "/Mywarehouse/Accounting/".(int)$id.'/0/TRUE',		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{readOnly: true},{readOnly: true},{},{},{},{},{},{},{readOnly: true},{},{},{},{},{},{readOnly: true}')
		);
		}
		else
		{
			
	///HERE
		$fielset = array('accounting' => array(
		'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status',  'Sold ID','Sold QN', 'Price Sold', 'Cost','Act.Sh.', 'Inb.Sh.','Order Notes', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Last Upd'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 300, 100, 40, 125, 80, 80, 80, 60, 60,60, 125, 80, 80, 80, 80, 80", 
		'startcols' => 19, 
		'startrows' => 10, 
		'autosaveurl' => "/Mywarehouse/Accounting/".(int)$id,	
		'reloadurl' => "/Mywarehouse/Accounting/".(int)$id.'/0/TRUE',			
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{readOnly: true},{readOnly: true},{},{},{},{},{},{},{},{},{},{readOnly: true}')
		);			
		}
		

		if (is_int($catlink) && $catlink > 0) 
		{	
			$true = 'TRUE';
			if (!$single) $true = 'FALSE';		
			$fielset['accounting']['autosaveurl'] = '/Mywarehouse/AccountingStatus/'.$catlink.'/'.$true;
			$fielset['accounting']['reloadurl'] = '/Mywarehouse/AccountingStatus/'.$catlink.'/'.$true.'/TRUE';
		}
		elseif (is_int($sku))
		{ 	
			$fielset['accounting']['autosaveurl'] = '/Mywarehouse/AccountingSku/'.(int)$sku;
			$fielset['accounting']['reloadurl'] = '/Mywarehouse/AccountingSku/'.(int)$sku.'/TRUE';
		}
		elseif ($ghostpage)
		{
			$fielset['accounting']['autosaveurl'] = '/Mywarehouse/Ghost/'.$ghostpage;
			$fielset['accounting']['reloadurl'] = '/Mywarehouse/Ghost/'.(int)$ghostpage.'/TRUE';
		}
		elseif ($lesspage)
		{
			$fielset['accounting']['autosaveurl'] = '/Mywarehouse/Less/'.$lesspage.'/'.$lesstype;
			$fielset['accounting']['reloadurl'] = '/Mywarehouse/Less/'.(int)$lesspage.'/'.$lesstype.'/TRUE';
			
		}
		$this->mysmarty->assign('lesspage', $lesspage);
		$this->mysmarty->assign('lesstype', $lesstype);
		
		
		$this->mysmarty->assign('headers', $fielset['accounting']['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['accounting']['rowheaders']);
		$this->mysmarty->assign('width', $fielset['accounting']['width']);
		$this->mysmarty->assign('startcols', $fielset['accounting']['startcols']);
		$this->mysmarty->assign('startrows', $fielset['accounting']['startrows']);
		$this->mysmarty->assign('autosaveurl', $fielset['accounting']['autosaveurl']);
		$this->mysmarty->assign('reloadurl', $fielset['accounting']['reloadurl']);
		$this->mysmarty->assign('colmap', $fielset['accounting']['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('copyrows', count($list['data']));
		
		$this->mysmarty->assign('pages', $list['pagearray']);
		$this->mysmarty->assign('page', $list['page']);
		
		
		if ((int)$focus > 0) $this->SingleAccounting((int)$focus);
		
		$this->mysmarty->view('mywarehouse/accounting.html');		
	}
}
function OrderAccountingSave($id = 0)
{
	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'oldbcn',
							3 => 'title',
							4 => 'status',							
							5 => 'paid',
							6 => 'shipped_actual',
							7 => 'sellingfee',
							8 => 'sold_date',
							9 => 'sold', 
							10 => 'location',
							11 => 'ordernotes'
						  );
				
				
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'Old BCN',
							3 => 'Title',
							4 => 'Status',
							5 => 'Price Sold',
							6 => 'Actual Sh.',
							7 => 'Fee',
							8 => 'Date Sold',
							9 => 'Where Sold',							
							10 => 'Location',
							11 => 'Order Notes'
						  );
				
				/*if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
				{					
					unset($colMap[18]);
					unset($colMap[19]);
					unset($colMap[20]);
					unset($bcolMap[18]);
					unset($bcolMap[19]);
					unset($bcolMap[20]);
				}	*/
				
				$out = '';
				$sout = '';
				$sessback = $this->_loadsession($this->session->userdata('sessfile'));
				$saveid = $sessback['accord'];
				$saverel = $sessback['accrel'];
				if ($saveid != (int)$id) { echo json_encode(array('msg' => '!!!!! CANNOT SAVE. YOU HAVE ANOTHER ACCOUNTING EDITOR OPEN !!!!!')); }	//printcool ($_POST);
				else {
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					$dd[3] = floatercheck($colMap[(int)$dd[1]], $dd[3]);
					if ($dd[2] != $dd[3])
					{
						
						if (!isset($saverel[(int)$dd[0]]['bcn'])) GoMail(array ('msg_title' => '!isset($saverel[(int)$dd[0]][bcn]) @ '.CurrentTime(), 'msg_body' => printcool ($d, true, 'D').printcool ($saverel,true,'saverel').printcool($_POST, true, 'POST'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						if (!isset($saverel[(int)$dd[0]]['wid'])) GoMail(array ('msg_title' => '!isset($saverel[(int)$dd[0]][wid]) @ '.CurrentTime(), 'msg_body' => printcool ($d, true, 'D').printcool ($saverel,true,'saverel').printcool($_POST, true, 'POST'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							

					$this->Auth_model->wlog($saverel[(int)$dd[0]]['bcn'], $saverel[(int)$dd[0]]['wid'], $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$saverel[(int)$dd[0]]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $saverel[(int)$dd[0]]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					
					$updt = array($colMap[(int)$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
					if ($colMap[(int)$dd[1]] == 'status')
                    {
                        $updt['status_notes'] = 'Changed from: '.$dd[2];
                        if ($dd[3] == 'On Hold') $updt['vended'] = 2;
                        elseif ($dd[3] == 'Sold') $updt['vended'] = 1;
                        else $updt['vended'] = 0;
                    }
																				// $this->Mywarehouse_model->GetStatusNotes((int)$id).' | 
					if ($colMap[(int)$dd[1]] == 'audit') { $updt['audit'] = CurrentTime(); $this->db->insert('warehouse_audits', array('action_id' => $saverel[(int)$dd[0]]['wid'], 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id'])); }
					
					if ($colMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;
							$this->db->insert('warehouse_audits', array('action_id' => (int)$id, 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$id);	
						}
					
					$this->db->update('warehouse', $updt, array('wid' => $saverel[(int)$dd[0]]['wid']));					
					if ($colMap[(int)$dd[1]] == 'paid' || $colMap[(int)$dd[1]] == 'cost' || $colMap[(int)$dd[1]] == 'sellingfee'|| $colMap[(int)$dd[1]] == 'shipped_actual' || ($tcolMap[(int)$dd[1]] == 'status' && $dd[3] == 'Scrap'))
					{
						$this->load->model('Myseller_model'); 
				
						 $nope = array();
						$this->Myseller_model->HandleBCN($nope,$nope,(int)$saverel[(int)$dd[0]]['wid']);	
						
						// $this->Mywarehouse_model->ReProcessNetProfit((int)$saverel[(int)$dd[0]]['wid']);
					}
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}
				}				
				echo json_encode($out);	
				}
		}
}
function OrderAccounting($id = 0, $return = false, $display = false)
{
	if ((int)$id == 0) { echo ''; exit(); }	
	//$_POST['soldid'] =14;
	
		$sql = 'SELECT wid, oldbcn, bcn, title, status, generic, waid, channel, sold_id, sold_subid, paid, shipped_actual, sellingfee, sold_date, sold, location, ordernotes FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` != 0 AND `channel` = 4 AND `sold_id` = '.(int)$id;
		$q =  $this->db->query($sql);
		$list['data'] = array();
	
		if ($q->num_rows() > 0)
		{
			$list['data'] = $q->result_array();
		}
	
		foreach ($list['data'] as $k => $l)
			{
				$h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);				
			}		
			
			
			$sesfile = $this->_savesession(array('accrel' => $h, 'accord' => (int)$id));
			$this->session->set_userdata(array('sessfile' => $sesfile));
			
			
			
			$loaddata = '';
			$adms = $this->Mywarehouse_model->GetAdminList();
			foreach ($list['data'] as $k => $l)
			{
				if (trim($l['audit']) != '') $audit = 1;
					else $audit = 0;	
					
				/*if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
				{			
						
					$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", cstr($l['bcn']), cstr($l['oldbcn']), cstr($l['title']), cstr($l['location']),cstr($audit),cstr($l['status']),cstr($l['listed']),cstr($l['listed_date']),cstr($l['sold_date']),cstr($l['sold']), cstr($l['sold_id']), cstr($l['soldqn']),cstr($l['paid']),cstr($l['shipped']), cstr($l['shipped_actual']),cstr($l['shipped_inbound']),cstr($l['ordernotes']),cstr($l['sellingfee']), cstr($l['netprofit']),cstr($l['cost']),cstr($l['aupdt']));
				}
				else
				{*/									

					$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", cstr($l['bcn']), cstr($l['oldbcn']), cstr($l['title']), cstr($l['status']), cstr($l['paid']), cstr($l['shipped_actual']), cstr($l['sellingfee']), cstr($l['sold_date']), cstr($l['sold']), cstr($l['location']), cstr($l['ordernotes']));						
				//}				
			}	
			if (count($list['data']) > 0)
			{
			foreach ($returndata as $kr => $r)
				{
					$loaddata .= "["; 
					foreach ($r as $krr => $rr)
					{
						$loaddata .= "'".$rr."',"; 
						$returndata[$kr][$krr]= stripslashes($rr);
					}
					$loaddata .= "],"; 
					
				}	
			}	
			if ($return)
			{
				//echo '['.rtrim($loaddata, ',').']';
				echo json_encode($returndata);

				exit();
			}
			
		//printcool ($list['headers']);
		$this->mysmarty->assign('list', $list['data']);
		$this->mysmarty->assign('id', (int)$_POST['soldid']);
		
		$fielset = array('accounting' => array(
		'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Status', 'Price Sold', 'Actual Sh.', 'Fee', 'Date Sold', 'Where Sold', 'Location', 'Order Notes'",
		
		'width' => "60, 80, 100, 300, 125, 125, 125, 100, 125, 125, 80, 140", 
		'startcols' => 12, 
		'startrows' => 10, 
		'autosaveurl' => "/Mywarehouse/OrderAccountingSave/".(int)$id,		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{readOnly: true},{},{},{}')
		);
		
		/*if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
		{

		$fielset = array('accounting' => array(
		'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Sold ID', 'Sold QN', 'Price Sold', 'Shipping Cost','Actual Sh.', 'Inbound Sh.','Order Notes', 'Selling Fee', 'Net Profit', 'Cost', 'Last Upd'",
		
		'width' => "60, 80, 100, 300, 125, 80, 125, 125, 125, 125, 125, 80, 80, 125, 125, 125, 125, 125,125, 125, 125, 125", 
		'startcols' => 22, 
		'startrows' => 10, 
		'autosaveurl' => "/Mywarehouse/OrderAccountingSave/".(int)$id,		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{},{readOnly: true},{readOnly: true},{},{},{},{},{},{},{readOnly: true},{},{readOnly: true}')
		);
		}
		else
		{
		$fielset = array('accounting' => array(
		'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold',  'Sold ID', 'Sold QN', 'Price Sold', 'Shipping Cost','Actual Sh.', 'Inbound Sh.', 'Order Notes',  'Last Upd'",
		
		'width' => "60, 80, 100, 300, 125, 80, 125, 125, 125, 125, 125, 80, 80, 125, 125, 125,125, 125,  125", 
		'startcols' => 19, 
		'startrows' => 10, 
		'autosaveurl' => "/Mywarehouse/OrderAccountingSave/".(int)$id,			
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{},{readOnly: true},{readOnly: true},{},{},{},{},{},{readOnly: true}')
		);			
		}
		*/
		
		
		$this->mysmarty->assign('headers', $fielset['accounting']['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['accounting']['rowheaders']);
		$this->mysmarty->assign('width', $fielset['accounting']['width']);
		$this->mysmarty->assign('startcols', $fielset['accounting']['startcols']);
		$this->mysmarty->assign('startrows', $fielset['accounting']['startrows']);
		$this->mysmarty->assign('autosaveurl', $fielset['accounting']['autosaveurl']);
		$this->mysmarty->assign('colmap', $fielset['accounting']['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('copyrows', count($list['data']));
		
		$this->mysmarty->assign('order', TRUE);
		
		if (!$display) echo $this->mysmarty->fetch('mywarehouse/accounting.html');		

}
function Audits($page = 1, $actionid = 0, $wlisting = 0)		
{
		$this->load->model('Myebay_model'); 
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());			
		$data = $this->Mywarehouse_model->getAudits((int)$page, (int)$actionid, (int)$wlisting);
		$this->mysmarty->assign('list', $data['results']);
		$this->mysmarty->assign('pages', $data['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('actionid', (int)$actionid);
		$this->mysmarty->assign('wlisting', (int)$wlisting);		
		$this->mysmarty->view('mywarehouse/auditlog.html');		
}
function fix()
{
	exit();
	$this->db->select('wid, bcn');
	$this->db->where('wid >=', 15344);
	$this->db->where('wid <=', 15526);
	$this->query = $this->db->get('warehouse');
	if ($this->query->num_rows() > 0) 
	{
		$a = $this->query->result_array();
		foreach ($a as $v => $vv)
		{
			$this->db->update('warehouse', array('deleted'=>1), array('wid'=> $vv['wid']));	
			
		}
	}		
}
/*
function Accountingload($id = '')
{
	if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
	$list = $this->Mywarehouse_model->GetAccounting((int)$id);
	if ($list)
	{
		$this->session->set_userdata(array('accrel' => $list['headers'], 'acclot' => (int)$id));
		$echo = array();
		
		foreach ($list['data'] as $k => $l)
		{
			foreach($l as $kk => $ll)
			{
				$echo[$k][$kk] = $ll;
			}
		}		
		echo json_encode($echo);
	}		
}
*/
/*
function Testingload($id = '')
{
	if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
	$list = $this->Mywarehouse_model->GetTesting((int)$id);
	if ($list)
	{
		$this->session->set_userdata(array('accrel' => $list['headers'], 'acclot' => (int)$id));
		$echo = array();
		$adms = $this->Mywarehouse_model->GetAdminList();
		foreach ($list['data'] as $k => $l)
		{
			foreach($l as $kk => $ll)
			{
				if ($kk == 'tech') $echo[$k][$kk] = $adms[$ll];
				//elseif ($kk == 'post') $echo[$k][$kk] = int2jsonval($ll);
				else $echo[$k][$kk] = $ll;
			}
		}		
		echo json_encode($echo);
	}	
}
*/
function bcnreturn($id = '')
{
	if ($_POST)
	{
		if (isset($_POST['cust_return'])) $item['cust_return'] = (int)$this->input->post('cust_return');
		if (isset($_POST['cust_status'])) $item['cust_status'] = (int)$this->input->post('cust_status');
		if (isset($_POST['cust_reason'])) $item['cust_reason'] = $this->input->post('cust_reason', TRUE);
		if (isset($_POST['cust_xtrcost'])) $item['cust_xtrcost'] = floater($this->input->post('cust_xtrcost', TRUE));
		if (isset($_POST['vendor_reason'])) $item['vendor_reason'] = $this->input->post('vendor_reason',TRUE);		
		if (isset($_POST['vendor_return'])) $item['vendor_return'] = (int)$this->input->post('vendor_return');	

		if (isset($item)) $this->db->update('warehouse', $item, array('wid' => (int)$id));
		$this->mysmarty->assign('saved', TRUE);		
	}
	$ret = $this->Mywarehouse_model->GetReturnData((int)$id);
	if (!$ret) exit('No item found');
	$this->mysmarty->assign('item', $ret);

	$this->mysmarty->view('mywarehouse/return.html');	
	
}
function label($label = '', $focus = '')
{
		$this->mysmarty->assign('list', $this->Mywarehouse_model->GetLabel((int)$id));
		$this->mysmarty->assign('id', (int)$id);
		$this->mysmarty->assign('focus', (int)$focus);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->view('mywarehouse/label.html');		
}
function bcns($page = 1)
{
		$load = $this->Mywarehouse_model->GetBCNs((int)$page);
		$this->mysmarty->assign('list', $load['results']);
		$this->mysmarty->assign('pages', $load['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->view('mywarehouse/bcns.html');	
}
function BulkStockSaveLastBCN()
{
	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'bcn',
							1 => 'aucid',
							2 => 'mfgpart',
							3 => 'mfgname',
							4 => 'sku',
							5 => 'sn',
							6 => 'title',
							7 => 'location'
						  );
			
				$bcolMap = array(
							0 => 'BCN',
							1 => 'AucID',
							2 => 'MFG Part',
							3 => 'MFG Name',
							4 => 'SKU',
							5 => 'SN',
							6 => 'Title',
							7 => 'Location'
						  );
				$out = '';
				$sout = '';
				$sessback = $this->_loadsession($this->session->userdata('sessfile'));
				$saveid = $sessback['acclot'];
				$saverel = $sessback['accrel'];
						  //printcool ($_POST);
					foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					if ($dd[2] != $dd[3])
					{
					$this->Auth_model->wlog($saverel[(int)$dd[0]]['bcn'], (int)$saverel[(int)$dd[0]]['wid'], $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$saverel[(int)$dd[0]]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $saverel[(int)$dd[0]]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					$this->db->update('warehouse', array($colMap[(int)$dd[1]] => $dd[3]), array('wid' => (int)$saverel[(int)$dd[0]]['wid']));
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					
					if ($colMap[(int)$dd[1]] == 'location') $this->Mywarehouse_model->DoLocation($dd[3], (int)$saverel[(int)$dd[0]]['wid']);	
					}
					}				
				}				
				echo json_encode($out);	
		}
	
}
function MassUpdater($complete = false, $process = false)
	{
	if ($process)
	{
		if (isset($_POST['data']) && count($_POST['data']) > 0)
		{
			$result = $this->session->userdata('formdata');			
			$change = 0;
			$removed =  $this->session->userdata('removed');
			foreach ($_POST['data'] as $p)
			{
				if ($p[0] == 1)
				{ 
					unset($result[trim($p[1])]);
					$change++;
					$removed[] = trim($p[1]);
				}				

			}			
			if ($change > 0)
			{
				$this->session->unset_userdata('formdata');
				$this->session->set_userdata('formdata', $result);	
				$this->session->set_userdata('removed', $removed);	
			}
			exit();	
		}
	}

	else
	{
	if ($complete) 
	{
		$result = $this->session->userdata('formdata');
		
		$fromlot = $this->session->userdata('fromlot');
		$location = $this->session->userdata('location');
		$status = $this->session->userdata('status');
		$sku = $this->session->userdata('sku');	
		$notfound = $this->session->userdata('notfound');		
		$this->mysmarty->assign('notfound', $notfound);
		$this->mysmarty->assign('removed', $this->session->userdata('removed'));
		
		
		if (isset($_POST['update']) && $_POST['update']) 
			{
					$poststatus = trim($this->input->post('status', TRUE));
					$postlocation = trim(ucwords($this->input->post('location', TRUE)));
					$postsku = trim($this->input->post('sku', TRUE));
					$posttitle = trim($this->input->post('title', TRUE));
					$update = array();
					if ($poststatus != '')  $update['status'] = $poststatus;
					if ($postlocation != '') $update['location'] = $postlocation;
					if ($posttitle != '') $update['title'] = $posttitle;
					if ($postsku != '') 
					{						
						$skulabel = trim($postsku);
						$update['sku'] = $this->Mywarehouse_model->seeksku($skulabel);	
						if (!$update['sku']) unset($update['sku']);					
					}
					if (count($update) > 0)
					{
						$updated = array();
						if ($result && count($result) >0)foreach ($result as $k => $v)
						{
							$old = $this->Mywarehouse_model->GetField('status, location, sku, title, status_notes',(int)$v['wid'], TRUE);							
							if ($postlocation != '')
							{								
								$this->Auth_model->wlog($k, (int)$v['wid'], 'location', $old['location'], $update['location']);									
								$location[(int)$v['wid']] = $update['location'];
								$this->Mywarehouse_model->DoLocation($update['location'], (int)$v['wid']);	
							}
							if ($postsku != '' && $update['sku'])
							{
								$this->Auth_model->wlog($k, (int)$v['wid'], 'sku', $old['sku'], $update['sku']);
								$sku[(int)$v['wid']] = $update['sku'];
							}
							if ($posttitle != '' && $update['title'])
							{
								$this->Auth_model->wlog($k, (int)$v['wid'], 'title', $old['title'], $update['title']);
								$title[(int)$v['wid']] = $update['title'];
							}
							if ($poststatus != '') 
							{
								$this->Auth_model->wlog($k, (int)$v['wid'], 'status', $old['status'], $update['status']);
								$update['status_notes'] = 'Changed from: '.$old['status'];
															//	$old['status_note'].' | 
								$status[(int)$v['wid']] = $update['status'];
							}
							$this->db->update('warehouse', $update, array('wid' => (int)$v['wid']));
							$updated[] = (int)$v['wid'];	
								
						}
						
						$this->session->set_userdata('location', $location);
						$this->session->set_userdata('status', $status);
						$this->session->set_userdata('sku', $sku);
						$this->session->set_userdata('title', $title);
						
						$this->mysmarty->assign('updated', count($updated));
					}
					$this->mysmarty->assign('poststatus', $poststatus);
					$this->mysmarty->assign('postlocation', $postlocation);
					$this->mysmarty->assign('posttitle', $posttitle);
					if (isset($update['sku'])) $this->mysmarty->assign('postsku', $update['sku']);
					else $this->mysmarty->assign('postsku', '');
					$this->mysmarty->assign('postskulabel', $postsku);
					
			}
		
		
		
		
		$loaddata = '';

		if (isset($result) && $result && count($result) > 0)foreach ($result as $k => $v)
			{
				$loaddata .= "[";
				$loaddata .= "'0',";
				$loaddata .= "'".$k."',";
				
				if (isset($_POST['update']) && $_POST['update']) 
				{
					if ($v['wid'] != '')
					{
						$loaddata .= "'";
						if (isset($fromlot[$k])) $loaddata .= "<sup>".$fromlot[$k]."</sup>";
						 $loaddata .= " <a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".$k."\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', ";
						 
						 
						 if ($posttitle != '') $loaddata .= "'<span style=\"font-size:16px; color:#090;\">".cstr($posttitle)."</span>',";
						else $loaddata .= "'".cstr($v['title'])." (No Change)', ";
						
						 
						if ($postlocation != '') $loaddata .= "'<span style=\"font-size:16px; color:#090;\">".cstr($postlocation)."</span>', ";
						else $loaddata .= "'<span style=\"font-size:16px; color:red;\">X</span>', ";
						if ($poststatus != '') $loaddata .= "'<span style=\"font-size:16px; color:#090;\">".cstr($poststatus)."</span>', ";
						else $loaddata .= "'<span style=\"font-size:16px; color:red;\">X</span>', ";
						if ($postsku != '') $loaddata .= "'<span style=\"font-size:16px; color:#090;\">".cstr($postsku)."</span>'";
						else $loaddata .= "'<span style=\"font-size:16px; color:red;\">X</span>'";		
					}
					else  $loaddata .= "'No Warehouse Match', '','','',''";
					
				}
				else
				{				
					if ($v['wid'] != '')
					{
						$loaddata .= "'";
						if (isset($fromlot[$k])) $loaddata .= "<sup>".$fromlot[$k]."</sup>";					
						$loaddata .= " <a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".$k."\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', ";
						$loaddata .= "'".cstr($v['title'])."', ";
						$loaddata .= "'".cstr($location[$v['wid']])."', ";
						$loaddata .= "'".cstr($status[$v['wid']])."', ";
						$loaddata .= "'".cstr($sku[$v['wid']])."'";
					}
					else  $loaddata .= "'No Warehouse Match', '','','',''";		
				}				
				$loaddata .= "],
					";
		
			}	
			
		$fielset = array(
		'headers' => "'DO', 'GO', 'Info', 'Title', 'Location', 'Status', 'SKU'",
		/*'rowheaders' => $list['headers'], */
		'width' => "30, 100, 80, 200, 125, 125, 125", 
		'startcols' => 7,		
		'colmap' => '{type: \'checkbox\', checkedTemplate: 0, uncheckedTemplate: 1},{readOnly: true, renderer: "html"}, {readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"},{readOnly: true, renderer: "html"}');
		
		$this->mysmarty->assign('headers', $fielset['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
		$this->mysmarty->assign('width', $fielset['width']);
		$this->mysmarty->assign('startcols', $fielset['startcols']);
		$this->mysmarty->assign('startrows', count($result));
		$this->mysmarty->assign('autosaveurl', '');
		$this->mysmarty->assign('colmap', $fielset['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('copyrows', count($result));
		
		
		$this->mysmarty->assign('fromlot', $fromlot);
	 	$this->mysmarty->assign('result', $result);
		$this->mysmarty->assign('location', $location);
		$this->mysmarty->assign('status', $status);
		$this->mysmarty->assign('sku', $sku);
		$this->mysmarty->assign('rows',count($result)+1);
		$this->mysmarty->assign('statuses',$this->statuses['allarray']);
		$this->mysmarty->view('mywarehouse/MassUpdater.html');
	}
	else
	{					  
			if (isset($_POST['data']) && $_POST['data']) 
			{
				$fromlot = array();
				$this->load->helper('security');
				$sql = 'SELECT wid, bcn, oldbcn, lot, location, status, sku, title FROM warehouse WHERE ';
				$c = 1;
				foreach ($_POST['data'] as $k => $v)
				{
					foreach ($v as $kk => $vv)
					{
						if (trim($vv) != '')
						{
							if ($c == 1)  $sql .= '(upper(bcn) = "'.strtoupper(addslashes(xss_clean(trim($vv)))).'" OR upper(lot) = "'.strtoupper(addslashes(xss_clean(trim($vv)))).'" OR upper(oldbcn) = "'.strtoupper(addslashes(xss_clean(trim($vv)))).'") ';
							else $sql .= 'OR (upper(bcn) = "'.strtoupper(addslashes(xss_clean(trim($vv)))).'" OR upper(lot) = "'.strtoupper(addslashes(xss_clean(trim($vv)))).'" OR upper(oldbcn) = "'.strtoupper(addslashes(xss_clean(trim($vv)))).'") ';
							$c++;
							$alldata[addslashes(xss_clean(trim(strtoupper($vv))))] = false;	
						}
					}
				}
				$w = $this->db->query($sql);
				if ($w->num_rows() > 0)
				{
					foreach ($w->result_array() as $wv)
					{
						if (isset($alldata[addslashes(xss_clean(trim(strtoupper($wv['bcn']))))])) unset($alldata[addslashes(xss_clean(trim(strtoupper($wv['bcn']))))]);	
						if (isset($data[addslashes(xss_clean(trim(strtoupper($wv['lot']))))])) { unset($data[addslashes(xss_clean(trim(strtoupper($wv['lot']))))]); $fromlot[$wv['bcn']] = strtoupper($wv['lot']);  unset($alldata[addslashes(xss_clean(trim(strtoupper($wv['lot']))))]);}
						if (isset($data[addslashes(xss_clean(trim(strtoupper($wv['oldbcn']))))])) { unset($data[addslashes(xss_clean(trim(strtoupper($wv['oldbcn']))))]); $fromlot[$wv['bcn']] = strtoupper($wv['oldbcn']); unset($alldata[addslashes(xss_clean(trim(strtoupper($wv['oldbcn']))))]);}
						$data[$wv['bcn']]['wid'] = $wv['wid'];	
						$data[$wv['bcn']]['title'] = $wv['title'];	
						$status[$wv['wid']] = $wv['status'];
						$location[$wv['wid']] = $wv['location'];
						$sku[$wv['wid']] = $wv['sku'];
										
						
					}
				}
				
				$this->session->set_userdata('formdata', $data);					
				$this->session->set_userdata('fromlot', $fromlot);
				$this->session->set_userdata('location', $location);
				$this->session->set_userdata('status', $status);
				$this->session->set_userdata('sku', $sku);
				$this->session->set_userdata('notfound', $alldata);
			}
			else
			{
				$this->session->unset_userdata('formdata');
				$this->session->unset_userdata('fromlot');
				$this->session->unset_userdata('removed');
				$this->session->unset_userdata('notfound');
				$this->mysmarty->assign('result', false);
				$this->mysmarty->view('mywarehouse/MassUpdater.html');
			}
	}
	}
		
}
function BulkStockRecieve()
	{
		$this->session->unset_userdata('formdata');
		// STAGE 1
		$this->load->library('form_validation');
		
		foreach ($this->warehousefields as $k => $v) 
		{
			if ($v[3] == 1) $this->form_validation->set_rules($v[0], $v[1], 'trim|'.$v[2].'xss_clean');
		}
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|is_natural_no_zero|xss_clean');
				
		if ($this->form_validation->run() == FALSE)
		{
			foreach ($this->warehousefields as $k => $v) $input[$v[0]] = $this->input->post($v[0], TRUE);	
			
			if ($input['bcn'] == '') 
			{				
				$bcnp1 = date("m").substr(date("y"), 1, 1);
				$bcnp2 = sprintf('%04u',$this->Mywarehouse_model->GetNextBcn((int)$bcnp1));
				$input['bcn'] = $bcnp1.'-'.$bcnp2;								
			}
			$this->mysmarty->assign('input', $input);
			$this->mysmarty->assign('fields', $this->warehousefields);
			
			$load = $this->Mywarehouse_model->GetBCNs(1);
			if ($load)
			{
				$sesfile = $this->_savesession(array('accrel' => $load['headers'], 'acclot' => 0));
				$this->session->set_userdata(array('sessfile' => $sesfile));
		
				$loaddata = '';
				$adms = $this->Mywarehouse_model->GetAdminList();
				foreach ($load['results'] as $k => $l)
				{
					$loaddata .= "['".cstr($l['bcn'])."', '".cstr($l['aucid'])."', '".cstr($l['mfgpart'])."', '".cstr($l['mfgname'])."', '".cstr($l['sku'])."', '".cstr($l['sn'])."', '".cstr($l['title'])."', '".cstr($l['location'])."'],
					";				
				}		
			}
			$fielset = array(
			'headers' => "'BCN', 'AucID', 'MFG Part', 'MFG Name', 'SKU' , 'SN', 'Title', 'Location'",
			/*'rowheaders' => $list['headers'], */
			'width' => "80, 100, 180, 125, 100, 115, 250, 160", 
			'startcols' => 8, 
			'startrows' => 10, 
			'autosaveurl' => "/Mywarehouse/BulkStockSaveLastBCN/",		
			'colmap' => "{readOnly: true},{readOnly: true},{},{},{},{},{},{}"
			);
			
			$this->mysmarty->assign('headers', $fielset['headers']);
			$this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
			$this->mysmarty->assign('width', $fielset['width']);
			$this->mysmarty->assign('startcols', $fielset['startcols']);
			$this->mysmarty->assign('startrows', $fielset['startrows']);
			$this->mysmarty->assign('autosaveurl', $fielset['autosaveurl']);
			$this->mysmarty->assign('colmap', $fielset['colmap']);
			$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
			$this->mysmarty->assign('copyrows', count($list['data']));
			$this->mysmarty->assign('bcns', $load['results']);
			
			$this->mysmarty->assign('quantity', ((int)$this->input->post('quantity') > 0 ? (int)$this->input->post('quantity') : 1));				
			$this->mysmarty->assign('errors', $this->form_validation->_error_array);
			$this->mysmarty->assign('last', $this->Mywarehouse_model->GetLast());
			$this->mysmarty->view('mywarehouse/BulkStockRecieve.html');						
		}		
		else
		{
		
			foreach ($this->warehousefields as $k => $v) if ($v[3] == 1) $db[$v[0]] = $this->form_validation->set_value($v[0]);
			$bcnparts = explode('-', $db['bcn']);
			$db['bcn_p1'] = $bcnparts[0];
			$db['bcn_p2'] = $bcnparts[1];
			//$db['bcn_p1'] = substr($db['bcn'], 0, 3);
			//$db['bcn_p2'] = substr($db['bcn'], 4, 4);
			$db['dates'] = serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())));
			$db['adminid'] = (int)$this->session->userdata['admin_id'];
			$db['insid'] = (int)$this->Mywarehouse_model->GetNextInsertOrder();
			for ($i = $db['bcn_p2']; $i < ($bcnparts[1] + (int)$this->input->post('quantity')); $i++) 
			{
				 if ($i > 1)
				 {
					 $db['bcn_p2'] = sprintf('%04u', $i);
					 $db['bcn'] = $bcnparts[0].'-'.$db['bcn_p2'];
				 }
				 $ins[$i] = $this->Mywarehouse_model->Insert($db);
			}
			//$this->session->set_flashdata('success_msg', '"'.$db['title'].'" Inserted as record '.$ins);
			//$this->session->set_flashdata('action', (int)$ins);			
			Redirect ('Mywarehouse/BulkStockRecieve#'.(int)$ins);
		}
	}
function BulkItemRecieve()
{
	if (!isset($_POST['bulk'])) exit('No data submitted');
	$thedata = explode(PHP_EOL, trim($this->input->post('bulk')));
	
	foreach ($thedata as $k => $v)
	{
		$v = trim($v);
		if ($v == '') unset($thedata[$k]);
		else 
		{
			if (isset($_POST['bcns'])) $parseddata[$k]['data'] = explode("\t", $v);
			else $parseddata[$k]['data'] = explode(',', $v);
			foreach ($parseddata[$k]['data'] as $fk => $fv) $parseddata[$k]['data'][$fk] = trim($fv);
			$parseddata[$k]['fieldcount'] = count($parseddata[$k]['data']);	
		}
	}
	if (isset($parseddata))
	{
		if (!isset($_POST['confirm']))
		{
			$this->mysmarty->assign('textareastring', trim($this->input->post('bulk')));
			if (isset($_POST['bcns'])) 
			{
				$this->mysmarty->assign('bcnview', TRUE);
			}
			$this->mysmarty->assign('parseddata', $parseddata);
			$this->mysmarty->view('mywarehouse/BulkItemRecieve.html');		
		}
		else
		{
			$db['bcn_p1'] = date("m").substr(date("y"), 1, 1);
			$db['insid'] = (int)$this->Mywarehouse_model->GetNextInsertOrder();
			$db['adminid'] = (int)$this->session->userdata['admin_id'];
			
			$bcnp2 = sprintf('%04u',$this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']));
			
			foreach ($parseddata as $k => $v)
			{
				//if ((int)$v['data'][0] > 1000) exit('More than 1000 quantity found. Please do not crash the server with such a value!');
				for ($i = $bcnp2; $i < ($bcnp2 + (int)$v['data'][0]); $i++) 
				{
					 $db['mfgpart'] = $v['data'][1];
					 $db['aucid'] = $v['data'][2];
					 $db['bcn_p2'] = sprintf('%04u', $i);
					 $db['bcn'] = $db['bcn_p1'].'-'.$db['bcn_p2'];
					
					 $ins[$i] = $this->Mywarehouse_model->Insert($db);					 
				}
				$bcnp2 = $i;				

			}
		Redirect ('Mywarehouse/BulkStockRecieve#'.(int)$ins);			
		}
	}		
}
function Ghost($page = 1, $reload = false)
{
	ini_set('memory_limit','1024M');
	$this->ghostpage = (int)$page;
	$this->Accounting(0, '', $reload);
}
function Less($page = 1, $field = 'Cost', $reload = false)
{
	ini_set('memory_limit','1024M');
	$this->lesspage = (int)$page;
	$this->lesstype = trim($field);
	$this->Accounting(0, '', $reload);
}
function RecieveReport($id = 0, $return = false)
{
	if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
	$list = $this->Mywarehouse_model->GetPacks((int)$id);	

	if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'oldbcn',
							3 => 'mfgpart',
							4 => 'mfgname',
							5 => 'psku',
							6 => 'sku',
							7 => 'sn',
							8 => 'title',
							9 => 'nr',
							10 => 'location',
							11 => 'notes',
							12 => 'adminid',
							13 => 'dates',
							14 => 'printed',
							15 => 'queprint'
						  );
				
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'Old BCN',
							3 => 'MFG Part',
							4 => 'MFG Name',
							5 => 'PSKU',
							6 => 'SKU',
							7 => 'SN',
							8 => 'Title',
							9 => 'Not Recieved',
							10 => 'Location',
							11 => 'Notes',
							12 => 'Admin',
							13 => 'Dates',
							14 => 'Label Printed',
							15 => 'Qued'
						  );
				$out = '';
				$sout = '';
				$sessback = $this->_loadsession($this->session->userdata('sessfile'));
				$saveid = $sessback['acclot'];
				$saverel = $sessback['accrel'];
				
						  //printcool ($_POST);
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					//$dd[0] ROW
					//$dd[1] COL
					//$dd[2] FROM VAL
					//$dd[3] TO VAL
					//printcool ($dd);
					//printcool ($list[(int)$dd[0]][$colMap[(int)$dd[1]]]);
					//$saverel[(int)$dd[0]]['wid']
					if ($dd[2] != $dd[3])
					{
					$this->Auth_model->wlog($saverel[(int)$dd[0]]['bcn'], $saverel[(int)$dd[0]]['wid'], $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$saverel[(int)$dd[0]]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $saverel[(int)$dd[0]]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					$updt = array($colMap[(int)$dd[1]] => $dd[3]);
					if ($colMap[(int)$dd[1]] == 'nr') 
					{
						//$updt['status_notes'] = $this->Mywarehouse_model->GetStatusNotes($saverel[(int)$dd[0]]['wid']).' | Changed from: '.$dd[2];						
						$updt['status_notes'] = 'Changed from: '.$dd[2];	
						$updt['status'] = 'Not Received';
						$updt['location'] = 'Not Received';
						$this->Mywarehouse_model->DoLocation($updt['location'], (int)$saverel[(int)$dd[0]]['wid']);	
					}
					
					if ($colMap[(int)$dd[1]] == 'location')
						{							
							$audit = CurrentTime();	
							$date = explode (' ',$audit);
							$time = explode (':', $date[1]);
							$date = explode ('-',$date[0]);
							$mk = mktime ($time[0],$time[1],$time[2], $date[1],$date[2],$date[0]);
							$updt['audit'] = $audit;
							$updt['auditmk'] = $mk;
							$this->db->insert('warehouse_audits', array('action_id' => (int)$id, 'wtime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
							$this->Mywarehouse_model->DoLocation($dd[3], (int)$saverel[(int)$dd[0]]['wid']);	
						}
						
					$this->db->update('warehouse', $updt, array('wid' => $saverel[(int)$dd[0]]['wid']));
					
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}				
				}				
				echo json_encode($out);	
		}
		else 
		{
		
		if ($list)
		{
			$sesfile = $this->_savesession(array('accrel' => $list['headers'], 'acclot' => (int)$id));
			$this->session->set_userdata(array('sessfile' => $sesfile));
			
			$adms = $this->Mywarehouse_model->GetAdminList();
			foreach ($list['data'] as $k => $l)
			{

				$list['data'][$k]['ptitle'] = $l['ptitle'] = substr($l['title'], 0, 25);
				$list['data'][$k]['ptitle2'] = $l['ptitle2'] = substr($l['title'], 25, 25);
	
				$l['ndates'] = '';				
				if(is_array($l['dates']) && count($l['dates']) > 0)foreach ($l['dates'] as $k => $v)
				{
					if (is_array($v)) foreach ($v as $kk => $vv)
					{
						if ($kk != 'createdstamp')  $l['ndates'] .= ucwords($kk).': '.$vv.' |';
					}
				}
				$returndata[] = array('<a target="_blank" href="/Mywarehouse/gotobcn/'.cstr($l['bcn']).'"><img src="/images/admin/table.png" border="0"></a><a target="_blank" href="/Myebay/Search/'.cstr($l['listingid']).'"><img src="/images/admin/b_search.png" border="0"></a><a target="_blank" href="/Myebay/ShowOrder/'.cstr($l['sold_id']).'/'.cstr($l['channel']).'"><img src="/images/admin/shoppingbag.png" border="0"></a>', cstr($l['bcn']),cstr($l['oldbcn']),cstr($l['mfgpart']),cstr($l['mfgname']),cstr($l['psku']),cstr($l['sku']),cstr($l['sn']),cstr($l['title']),cstr($l['nr']),cstr($l['location']),cstr($l['notes']),$adms[$l['adminid']],cstr($l['ndates']),cstr($l['printed']),cstr($l['queprint']));
				
				$printdata[] = array(cstr($l['queprint']),cstr($l['wid']),cstr($l['bcn']),cstr($l['ptitle']),cstr($l['ptitle2']),cstr($l['aucid']),cstr($l['mfgpart']));
			}		
		}	
		
		$loaddata = '';
		if (count($list['data']) > 0)
			{
			foreach ($returndata as $kr => $r)
				{
					$loaddata .= "["; 
					foreach ($r as $krr => $rr)
					{
						$loaddata .= "'".$rr."',"; 
						$returndata[$kr][$krr]= stripslashes($rr);
					}
					$loaddata .= "],"; 
					
				}	
			}	
			if ($return)
			{	
				echo json_encode($printdata);
				exit();
			}
		
		
			
		//printcool ($list['headers']);
		$this->mysmarty->assign('list', $list['data']);
		$this->mysmarty->assign('id', (int)$id);
		$this->mysmarty->assign('cal', TRUE);
		$au =$this->Mywarehouse_model->AuctionIdToName((int)$id);		
		if ($au) 
		{
			$this->mysmarty->assign('atitle', $au['wtitle']);
			$this->mysmarty->assign('anotes', $au['wnotes']);
		}
		else
		{
			$this->mysmarty->assign('atitle', '');
			$this->mysmarty->assign('anotes', '');
		}
		//$this->mysmarty->assign('focus', (int)$focus);
		//$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
		
		
		/*
		bcn
mfgpart
mfgname
psku
sku
sn
title
location
notes
adminid
dates
		*/
		$fielset = array(
		'headers' => "'Go', 'BCN', 'Old BCN', 'MFG Part', 'MFG Name','PKU' , 'SKU' , 'SN', 'Title', 'Not Rec.', 'Location', 'Notes', 'Admin', 'Dates', 'Label Printed', 'Qued'",
		/*'rowheaders' => $list['headers'], */
		'width' => "70, 60, 100, 125, 125, 100, 100, 115, 180, 80 ,120, 180, 110, 165,110, 40", 
		'startcols' => count($list['data']), 
		'startrows' => 10, 
		'autosaveurl' => "/Mywarehouse/RecieveReport/".(int)$id,
		'reloadurl' => "/Mywarehouse/RecieveReport/".(int)$id.'/TRUE',
		'reloadprinturl' => "/Mywarehouse/RecieveReport/".(int)$id.'/TRUE',		
		'colmap' => "{readOnly: true, renderer: 'html'},{readOnly: true},{},{},{},{},{},{},{},{type: 'checkbox', checkedTemplate: 1, uncheckedTemplate: 0},{},{},{readOnly: true},{readOnly: true}, {readOnly: true}, {type: 'checkbox', checkedTemplate: 1, uncheckedTemplate: 0}"
		);
		
		
				$this->db->where("waid", (int)$id);				
				$this->query = $this->db->get('warehouse_auctions');
				if ($this->query->num_rows() > 0) 
				{
					$auctionresults = $this->query->result_array();
					
//printcool ($auctionresults);
//printcool ($list['data']);
					$calc = $this->Mywarehouse_model->doaccounting($id, $list['data'],$auctionresults);	
					
					//printcool ($calc['sum']);	
					$this->mysmarty->assign('idata', $calc);
				}
				
				
		$this->mysmarty->assign('headers', $fielset['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
		$this->mysmarty->assign('width', $fielset['width']);
		$this->mysmarty->assign('startcols', $fielset['startcols']);
		$this->mysmarty->assign('startrows', $fielset['startrows']);
		$this->mysmarty->assign('autosaveurl', $fielset['autosaveurl']);
		$this->mysmarty->assign('reloadurl', $fielset['reloadurl']);
		$this->mysmarty->assign('reloadprinturl', $fielset['reloadprinturl']);
		$this->mysmarty->assign('colmap', $fielset['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('copyrows', count($list['data']));
		
		$this->mysmarty->assign('dataset', $list['data']);
		$this->mysmarty->assign('labelprinter', $this->mysmarty->fetch('mywarehouse/printlabel.html'));
		
		$this->mysmarty->view('mywarehouse/RecieveReport.html');			
		
	}	
}
function SavePrinted()
{
	$printeditems = $_POST['printeditem'];
	//GoMail(array ('msg_title' => 'Printed Items', 'msg_body' => printcool ($printeditems, true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);	
	
	if (is_array($printeditems) && count($printeditems) > 0)
	{
		$ct = CurrentTime();
		foreach ($printeditems as $p) $this->db->update('warehouse', array('printed' => $ct, 'queprint' => 0), array('wid' => (int)$p));
	}
	
}
function gotobcn($bcn = '')
{
	if (trim($bcn) != '' )
	{

		Redirect('Mywarehouse/bcndetails/'.$this->Mywarehouse_model->bcn2wid(trim($bcn)));
	}
	//$this->Finder();	
}
function savelistingid()
{
	if (isset($_POST['soldid']) && isset($_POST['listingid']))
	{
		$this->db->update('ebay_transactions', array ('e_id' => (int)$_POST['listingid'], 'autoid' => 1), array ('et_id' => (int)$_POST['soldid']));	
		echo 1;		
	}	
}
function showselection ()
{
	if (isset($_POST['listingid']) && isset($_POST['soldid']) && isset($_POST['subid']) && isset($_POST['channel']))
	{		  
		$this->Myseller_model->assignchannels();
		$this->mysmarty->assign('selection', $this->Myseller_model->getSalesListings(array(0 => true,(int)$_POST['listingid'] => true), true, false, false)); 
		$this->mysmarty->assign('sales', (int)$_POST['channel']);
		$this->mysmarty->assign('id', (int)$_POST['soldid']);
		$this->mysmarty->assign('subid', (int)$_POST['subid']);
		$this->mysmarty->assign('listingid', (int)$_POST['listingid']);
		echo $this->mysmarty->fetch('myseller/availbcn.html');		
	}
}
function startshowselection()
{
	if (isset($_POST['id']) && isset($_POST['sales']))
	{
		$this->mysmarty->assign('sales', (int)$_POST['sales']);
		$this->mysmarty->assign('id', (int)$_POST['id']);
		echo $this->mysmarty->fetch('myseller/selection.html');		
	}	
}
function selectionsearch($editor = false, $sales = '')
{
	if (!isset($_POST['id']))
	{
		if (isset($_POST['data']) && count($_POST['data']) > 0)
		{
			foreach ($_POST['data'] as $p) $from[] = trim($p[0]);
			$to = false;
			$id = (int)$editor;
			if (trim($sales)!= '') { $sales = trim($sales); $this->mysmarty->assign('sales', (int)$sales); $this->mysmarty->assign('subid', 0);}
			else $sales = '';
			
		}
		else exit('Bad Data');
	}
	else
	{
		$from = trim($this->input->post('from'));
		$to = trim($this->input->post('to'));
		$id = trim($this->input->post('id'));
		if (trim($_POST['sales'])!= '')  { $sales = trim($this->input->post('sales')); $this->mysmarty->assign('sales', (int)$sales); $this->mysmarty->assign('subid', 0); }		else $sales = '';
		
	}
	
	$res = $this->Mywarehouse_model->GetSelection($from, $to, $id, $sales);


	$this->mysmarty->assign('id', (int)$id);
	$exact = array();
	if ($res) 
	{
		$this->mysmarty->assign('selection', $res);
		
		foreach ($res as $k => $w)
			{				
				if (is_array($from))
				{
					foreach ($from as $f)
					{
						if (trim($f) != '' && trim($f) == $w['bcn'] && $sales == 4 && $w['sold_id'] == 0) $exact[] = $w['wid'];
					}
				}
			}

	}
	 
	$this->Myseller_model->assignchannels();
	//if (!isset($_POST['id'])) echo json_encode($this->mysmarty->fetch('myseller/availbcn.html'));
	//else echo $this->mysmarty->fetch('myseller/availbcn.html');	
	echo json_encode(array('html' => $this->mysmarty->fetch('myseller/availbcn.html'), 'exact' => $exact));
}
function BCNStringSearch($existingwid = 0)
{
    $string = trim($this->input->post('bcnstsrc',true));
    if ($string != '')
    {
    $this->db->select('wid,sold_id, sold_subid, channel');
    $this->db->where('wid', (int)$existingwid);
    $ex = $this->db->get('warehouse');
   // printcool($ex);
    if ($ex->num_rows() > 0)
    {
        $exbcn = $ex->row_array();
        
        $this->db->select('wid, bcn, status, vended, sold_id, channel');
        //$this->db->where('vended', 0);
        $this->db->where('deleted', 0);
        $this->db->where('nr', 0);
        //$this->db->where('sold_id', 0);
        $this->db->where('bcn', $string);
        $this->db->where('wid !=', (int)$existingwid);
        $res = $this->db->get('warehouse');
        //echo $string;
        $retstr = '';
        if ($res->num_rows() > 0)
        {
            //$retstr = '<table cellpadding="4" cellspacing="0" border="0">';
            foreach ($res->result_array() as $r)
            {
                //SOLD
                if ($r['vended'] == 1) $retstr .= $r['bcn'].' is SOLD<br>Status: '.$r['status'].'<Br>Order '.$r['sold_id'].' - Channel: '.$r['channel'].'<br>';
                
                //ON HOLD    
                elseif ($r['vended'] == 2) $retstr .= '<button onclick="OrderBCNSwap(\''.$exbcn['wid'].'\',\''.$r['wid'].'\',\''.$exbcn['sold_id'].'\',\''.$exbcn['sold_subid'].'\',\''.$exbcn['channel'].'\');">'.$r['bcn'].' ('.$r['status'].')<Br>Order '.$r['sold_id'].' - Channel: '.$r['channel'].'</button><br>';
                
                
                //LISTED    
                else $retstr .= '<button onclick="OrderBCNSwap(\''.$exbcn['wid'].'\',\''.$r['wid'].'\',\''.$exbcn['sold_id'].'\',\''.$exbcn['sold_subid'].'\',\''.$exbcn['channel'].'\');">'.$r['bcn'].' ('.$r['status'].')</button><br>';

            }
            //$retstr = '</table>';
            echo '<br>'.$retstr;
        }
        else  echo 'No Match for searched BCN: "'.$string.'"';
    }
    else echo 'No Match for existing BCN: "'.(int)$existingwid.'"';
        
    } else echo 'No Match for: "'.$string.'"';
        
    
}
function OrderBcnSwap($ewid = 0, $nwid = 0)
{
   if ((int)$ewid > 0 && (int)$nwid > 0) 
   {
       $this->db->select('wid, bcn, sold_id, channel,vended');
       $this->db->where('wid', (int)$ewid);
       $this->db->or_where('wid', (int)$nwid);
       $w = $this->db->get('warehouse');
       if ($w->num_rows > 0)
       {
          foreach ($w->result_array() as $wf)
          {
              if ($wf['wid'] == (int)$ewid) $existing = $wf;
              elseif ($wf['wid'] == (int)$nwid) $new = $wf;              
          }
          
          
        if (isset($existing) AND isset($new))
        {
            //printcool ($new);
            //printcool ($existing);
            //printcool ($_POST);
            $_POST['soldid'] = (int)$new['sold_id'];
            $_POST['subid'] = (int)$new['sold_subid'];
            $_POST['channel'] = (int)$new['channel'];
            $this->vended = (int)$new['vended'];
            $_POST['wid'] = (int)$ewid;
            $_POST['remove'] = 0; 
            //printcool ($_POST);
            $this->BCNSalesAttach(false, false);

            $_POST['soldid'] = (int)$existing['sold_id'];
            $_POST['subid'] = (int)$existing['sold_subid'];
            $_POST['channel'] = (int)$existing['channel'];
            $this->vended = (int)$existing['vended'];
            $_POST['wid'] = (int)$nwid;
            $_POST['remove'] = 0;  
            //printcool ($_POST);
            $this->BCNSalesAttach(TRUE,FALSE);   
        }
       }
   }
    
}
function MakeAllStatus()
{
	if (isset($_POST['listingid']) && isset($_POST['status']))
	{
		if (in_array(trim($_POST['status']), $this->statuses['listingarray']))
		{

			$dbdata = $this->Myseller_model->getBase(array((int)$_POST['listingid']), true);
			foreach ($dbdata as $wid)
			{
				if (trim($_POST['status']) != $wid['status'] && $wid['status'] != 'FBA')
				{
					$data['status'] = trim($_POST['status']);
			
					$data['status_notes'] = 'Changed from "'.$wid['status'].'" - MakeAllStatus by '.$this->session->userdata['ownnames'];
					//if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
					//else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];	
			
					$this->db->update('warehouse', $data, array('wid' => (int)$wid['wid']));
					if ($data['status'] == 'Scrap')
					{
						$this->load->model('Myseller_model'); 
						 $nope = array();
						$this->Myseller_model->HandleBCN($nope,$nope,(int)$_POST['wid']);
			
						// $this->Mywarehouse_model->ReProcessNetProfit((int)$_POST['wid']);	
					}
			 		$this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'status', $wid['status'], $data['status']);
					
					if ($data['status'] == 'Listed') $actionqn = -1;
					else $actionqn = 1;
					$this->Myseller_model->runAssigner($wid['listingid'], $actionqn);
				}
			}
			echo $this->_getbcnsnippet((int)$_POST['listingid'], false, 'listing');	
		}
		else echo 'Bad Data';
	}
}
function DelGreens()
{
	if (isset($_POST['listingid']))
	{
			$dbdata = $this->Myseller_model->getBase(array((int)$_POST['listingid']), true);
			foreach ($dbdata as $wid)
			{
				if ($wid['generic'] == 1 && $wid['regen'] == 1)
				{			
					$this->db->update('warehouse', array('deleted' => 1), array('wid' => (int)$wid['wid']));
			
			 		$this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'status', 'DELETED', $data['status']);
					
					if ($data['status'] == 'Listed') $actionqn = -1;
					else $actionqn = 1;
					$this->Myseller_model->runAssigner($wid['listingid'], $actionqn);
				}
			}
			echo $this->_getbcnsnippet((int)$_POST['listingid'], false, 'listing');			
	}
	else echo 'Bad Data';
}
function SetShipped()
{
	if (isset($_POST['soldid']) && isset($_POST['channel']))
	{
		
		$wids = $this->Myseller_model->getSales(array((int)$_POST['soldid']), $_POST['channel'], true, true);
		if ($wids) 
		{
			foreach ($wids as $wid)
			{
				//$data['status'] = 'Sold';
				//$data['location'] = 'Sold';
				//$data['vended'] = 1;	
				//$data['shipped_date'] = CurrentTime();
				//$data['status_notes'] = 'Changed from "'.$wid['status'].'" - SetShipped by '.$this->session->userdata['ownnames'];
				//if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
				//else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];			
								
				//$this->db->update('warehouse', $data, array('wid' => (int)$wid['wid']));	
				//LOG CHANGES
				//foreach ($data as $k => $v)
				//{//printcool ($v); printcool ($wid[$k]);
				// if ($v != $wid[$k]) $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
				//}
				$listingid = $wid['listingid'];
				$_POST['subid'] = $subid = $wid['sold_subid'];
				$_POST['wid'] = $wid['wid'];
				$_POST['remove'] = 0;
				$this->BCNSalesAttach(FALSE);
			}

			
			if ($_POST['channel'] == 2)
			{
				$this->db->select('admin, revs, order');
				$this->db->where('oid', (int)$_POST['soldid']);
				$q = $this->db->get('orders');
				if ($q->num_rows() > 0) 
				{	$res = $q->row_array();
					$res['revs']++;						
					if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
					else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];
					
					$res['order'] = unserialize($res['order']); 
					if (is_array($res['order']))
					foreach ($res['order'] as $k => $ov) 
					{
						if ($k == $subid) $qty = $ov['quantity'];
					}
					
					$this->db->update('orders', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('oid'=>(int)$_POST['soldid']));
				}
			}
			elseif ($_POST['channel'] == 4)
			{
				$this->db->select('admin, revs');
				$this->db->where('woid', (int)$_POST['soldid']);
				$q = $this->db->get('warehouse_orders');
				if ($q->num_rows() > 0) 
				{	$res = $q->row_array();
					$res['revs']++;	
					if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
					else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];
					$this->db->update('warehouse_orders', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('woid' => (int)$_POST['soldid']));
				}
			}
			else
			{
				$this->db->select('admin, revs, qty');
				$this->db->where('et_id', (int)$_POST['soldid']);
				$q = $this->db->get('ebay_transactions');
				if ($q->num_rows() > 0) 
				{	$res = $q->row_array();
					$res['revs']++;	
					$qty = $res['qty'];
					if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
					else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];
					$this->db->update('ebay_transactions', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('et_id' => (int)$_POST['soldid']));
				}
				
			}
	
			$this->Myseller_model->getSalesListings(array(0 => true,$listingid => true));			  
			$this->Myseller_model->getSales(array((int)$_POST['soldid']), (int)$_POST['channel']); 
			//printcool($listingid);
			$data = $this->Mywarehouse_model->getsaleattachdata((int)$_POST['channel'], (int)$_POST['soldid'],$listingid,0);		
			$this->mysmarty->assign('sales', (int)$_POST['channel']);
			$this->mysmarty->assign('eid', $listingid);
			$this->mysmarty->assign('id', (int)$_POST['soldid']);
			if ((int)$_POST['channel'] == 2) $this->mysmarty->assign('subid', (int)$subid);
			$this->mysmarty->assign('quantity', $data['qty']);
			//printcool ($data['qty']);
			$this->mysmarty->assign('mark', TRUE);
			$this->mysmarty->assign('updatetime', CurrentTimeR());	
			echo $this->mysmarty->fetch('myseller/bcnarea.html');
			
		}
		
	}
	 else echo 'Bad Data';		
	
}
function savechanneldata()
{
	if (isset($_POST['listingid']) && isset($_POST['channel']) && isset($_POST['val']) && isset($_POST['datatype']))
	{
		if (trim($_POST['datatype']) == 'price') {$datatype = 'price_ch'; $val = floater($_POST['val']); }
		else { $datatype = 'qn_ch'; $val = (int)$_POST['val']; $qn = true; }
		
		if ((int)$_POST['channel'] > 3) $channel = 3;
		else $channel = (int)$_POST['channel'];
		if (isset($qn))
		{
			$bcncount = $this->Myseller_model->getSalesListings(array(0 => true,(int)$_POST['listingid'] => true),  false, true);
			if ($val > $bcncount)
			{
				$diff = $val-$bcncount;
				$nogo = true;	
			}
		}
		
			$field = $datatype.$channel;
			if (!isset($nogo))
			{
				$this->db->update('ebay', array($field => $val), array('e_id' => (int)$_POST['listingid']));
				$this->load->model('Myebay_model');
				$item = $this->Myebay_model->GetItem((int)$_POST['listingid']);
				
				$adms = $this->Mywarehouse_model->GetAdminList();
				
				
							$ra['admin'] = $adms[(int)$_POST['adminid']];
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'savechanneldata';
							$ra['field'] = $field;
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$_POST['listingid'];
							$ra['ebay_id'] = $item['ebay_id'];
							$ra['datafrom'] = $item[$field];
							$ra['datato'] = $val;										
							$this->db->insert('ebay_actionlog', $ra);							
								
				if($field == 'price_ch1')
				{						
						//$this->db->insert('autopilot_log', array('apl_listingid' => (int)$_POST['listingid'], 'apl_from' => $item[$field], 'apl_to' => $val, 'apl_adminid' => (int)$this->session->userdata['admin_id'], 'apl_time' => CurrentTime(), 'apl_tstime' => mktime()));
						
						$this->load->model('Myautopilot_model');	
							$this->Myautopilot_model->ResetRules((int)$_POST['listingid']);
							
							$this->Myautopilot_model->LogPriceChange((int)$_POST['listingid'], $item[$field], $val, (int)$_POST['adminid']);
				}
			}
			
			
			if (isset($diff)) echo $diff;//$this->ListingGhostGen(array('listingid' => (int)$_POST['listingid'], 'qn' => $diff));
			else
			{
				 echo 0;
				 if ($channel == 2)
					{
						$etype = 'p';
						if (isset($qn)) $etype = 'q';
						 $this->Myseller_model->que_rev((int)$_POST['listingid'], $etype, $val, $adms[(int)$_POST['adminid']]);	
					}
			}
	}
}
function commit()
{
	$db = array('admin_history', 'ebay','ebay_actionlog', 'ebay_transactions', 'orders', 'warehouse', 'warehouse_auctions','warehouse_log');
	$run = false;
	$allpaths = array(
	'../system_dev/application' => '../system_la/'.$this->config->config['pathtoapplication'],
	'../dev/js' => '../public_html/js',
	'../dev/css' => '../public_html/css'	
	);
	
	$donot[] = '/config/config.php';
	$donot[] = '/config/database.php';
	$donot[] = '/libraries/ebay/shipping.txt';
	$donot[] = '/libraries/ebay/trans.txt';
	$donot[] = '/views/header.html';
	$donot[] = '/controllers/show.php';
	
	foreach ($allpaths as $ak => $av)
	{
	$devpaths = $this->_getFileList($ak, true);
	$livepaths = $this->_getFileList($av, true);
	
	
	foreach ($devpaths as $v) $devkeypaths[str_replace($ak,'',$v['name'])] = $v; 	
	foreach ($livepaths as $v)
	{
		if (isset($devkeypaths[str_replace($av,'',$v['name'])]) && $v['size'] != $devkeypaths[str_replace($av,'',$v['name'])]['size'])
		{			
			if (!in_array(str_replace($av,'',$v['name']), $donot))
			{
			if ($run)
			{
				
				if (copy($devkeypaths[str_replace($av,'',$v['name'])]['name'], $v['name'])) {
						echo "Copied $file...<br>\n";
				
				}
			}
			else printcool ($v['name']);
			}
		}
	}
	}
	
	
	

}
function _getFileList($dir, $recurse=false)
  {
    $retval = array();

    // add trailing slash if missing
    if(substr($dir, -1) != "/") $dir .= "/";

    // open pointer to directory and read list of files
    $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
    while(false !== ($entry = $d->read())) {
      // skip hidden files
      if($entry[0] == ".") continue;
      if(is_dir("$dir$entry")) {
        $retval[] = array(
          "name" => "$dir$entry/",
          "type" => filetype("$dir$entry"),
          "size" => 0,
          "lastmod" => filemtime("$dir$entry")
        );
        if($recurse && is_readable("$dir$entry/")) {
          $retval = array_merge($retval, $this->_getFileList("$dir$entry/", true));
        }
      } elseif(is_readable("$dir$entry")) {
        $retval[] = array(
          "name" => "$dir$entry",
          "type" => mime_content_type("$dir$entry"),
          "size" => filesize("$dir$entry"),
          "lastmod" => filemtime("$dir$entry")
        );
      }
    }
    $d->close();

    return $retval;
  }
function BCNListingAttach()
{
	if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['remove']))
	{
        $posted= $this->input->post('wid',true);
        $wids = array();
        $proccnt = 0;
        if (is_array($posted) && count($posted) > 0)
        {
            foreach ($posted as $p)
            {
                if ((int)$p > 1) $wids[] = (int)$p;
            }
        }
        else $wids[] = (int)$posted;

       //printcool ($posted);
       //printcool ($wids);
        foreach($wids as $onewid)
        {
            $proccnt++;
            //if (is_array($posted) && count($posted) > 0) GoMail(array ('msg_title' => 'BCNListingAttach WID POST  @'.CurrentTime(), 'msg_body' => printcool($wids,true,'$wids').printcool($onewid,true,'$onewid').printcool($_POST,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

            $wid = $this->Mywarehouse_model->getbcnattachdata((int)$onewid);
            $title = $this->Mywarehouse_model->GetListingTitleAndCondition((int)$_POST['listingid'], true);
            if ($wid) {

                if ((int)$_POST['remove'] == 1) {
                    $data['prevlistingid'] = $wid['listingid'];
                    $data['listingid'] = 0;
                    if ($wid['status'] != 'Scrap') $data['status'] = 'Not Listed';
                    $data['listed'] = '';
                    $data['listed_date'] = '';

                    $actionqn = 1;
                } else {
                    $data['listingid'] = (int)$_POST['listingid'];
                    $data['status'] = 'Listed';
                    if ($title && $title != '') $data['title'] = $title;
                    $data['listed'] = 'eBay ' . (int)$_POST['listingid'];
                    $data['listed_date'] = CurrentTime();


                    $actionqn = -1;
                }
                foreach ($data as $k => $v) {
                    if ($wid[$k] != $v) $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);
                }
                if ($title != $wid['title']) {
                    $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'title', $wid['title'], $title);
                    $data['title'] = $title;
                }
                $data['status_notes'] = 'Changed from "' . $wid['status'] . '" - Listing ' . $data['listingid'] . ' by ' . $this->session->userdata['ownnames'];
                //if (trim($wid['status_notes']) == '')  = $statusnotes;
                //else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];

                $this->db->update('warehouse', $data, array('wid' => $onewid));


                $this->Myseller_model->runAssigner((int)$_POST['listingid'], $actionqn);

                if (count($wids) == $proccnt) echo $this->_getbcnsnippet((int)$_POST['listingid'], false, 'listing');
            } else echo 0;
        }
	}else echo 0;
}
function test0()
{
	
	$this->Myseller_model->test1();
}
function test1()
{
	$wid = $this->Mywarehouse_model->getbcnattachdata(86601);
	printcool ($wid);

	$data['netprofit'] = ((float)$data['paid']+(float)$data['shipped'])-((float)$wid['cost']+(float)$data['sellingfee']+(float)$data['shipped_actual']);
	
	printcool();
}
function BCNSalesAttach($assign = TRUE, $secondaryassign = TRUE)
{
	if (isset($_POST['wid']) && isset($_POST['soldid']) && isset($_POST['subid']) && isset($_POST['channel']) && isset($_POST['remove']))
	{
        $posted= $this->input->post('wid',true);
        $wids = array();
        $proccnt = 0;
        if (is_array($posted) && count($posted) > 0)
        {
            foreach ($posted as $p)
            {
                if ((int)$p > 1) $wids[] = (int)$p;
            }
        }
        else $wids[] = (int)$posted;

        //printcool ($posted);
        //printcool ($wids);
        foreach($wids as $onewid)
        {
            if (is_array($posted) && count($posted) > 0) GoMail(array ('msg_title' => 'BCNSalesAttach WID POST  @'.CurrentTime(), 'msg_body' => printcool($wids,true,'$wids').printcool($onewid,true,'$onewid').printcool($_POST,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            $proccnt++;
            $wid = $this->Mywarehouse_model->getbcnattachdata((int)$onewid);
            $data = $this->Mywarehouse_model->getsaleattachdata((int)$_POST['channel'], (int)$_POST['soldid'], $wid['listingid'], (int)$_POST['remove']);
            $sales = $this->Myseller_model->getSales(array((int)$_POST['soldid']), (int)$_POST['channel'], TRUE, TRUE);
            if ((int)$_POST['channel'] != 4) {
                if ($assign) {
                    if ((int)$_POST['remove'] == 0 && ((int)count($sales) == $data['qty'] && $data['qty'] >= 0)) {
                        if ($data['qty'] == 1) {
                            $toremove = $sales[0];
                        } else {
                            echo (int)count($sales); /*printcool ($_POST); printcool ($data); */
                            exit();
                        }
                    }
                }
            }

            //printcool ($wid, '', 'getbcndata');
            if ($wid) {
                //printcool ($data, '', 'getsalesdata');
                $qty = $data['qty'];
                $mark = $data['mark'];
                unset($data['qty']);
                unset($data['mark']);
                $data['channel'] = (int)$_POST['channel'];
                if ((int)$_POST['remove'] == 1) {
                    if ((int)$_POST['channel'] == 1) $chan = 'eBay';
                    elseif ((int)$_POST['channel'] == 2) $chan = 'WebSite';
                    elseif ((int)$_POST['channel'] == 4) $chan = 'Warehouse';

                    if (isset($_POST['returns']) && $_POST['returns'] == 'YES') {
                        $nocleanstat = true;
                        $data['return_datesold'] = $wid['sold_date'];
                        $data['return_pricesold'] = floater($wid['paid']);
                        $data['return_sellingfee'] = floater($wid['sellingfee']);
                        $data['return_shippingcost'] = floater($wid['shipped_actual']);
                        $data['return_netprofit'] = floater($wid['netprofit']);
                        $data['return_wheresold'] = $chan;

                        if ($data['channel'] == 1) {
                            $this->db->select('transid, itemid');
                            $this->db->where('et_id', (int)$wid['sold_id']);
                            $dbres = $this->db->get('ebay_transactions');
                            if ($dbres->num_rows() > 0) {
                                $dbresdata = $dbres->row_array();
                                $this->_getReturnData($dbresdata['itemid'], $dbresdata['transid'], (int)$wid['sold_id']);
                            }
                        }
                        $return['wid'] = (int)$wid['wid'];
                        $return['sold_id'] = (int)$wid['sold_id'];
                        $return['channel'] = (int)$data['channel'];
                        if (trim($data['return_datesold']) != '') $return['uts'] = date2mk($data['return_datesold']);
                        else $return['uts'] = 0;
                        $return['created'] = $data['return_datesold'];
                        $return['fee'] = $data['return_sellingfee'] / $qty;
                        $return['paid'] = $data['return_pricesold'] / $qty;
                        $return['return_id'] = 0;
                        $return['returnID'] = 0;

                        switch ($return['channel']) {
                            case 1:
                                $tbl = 'ebay_transactions WHERE ';
                                $fld = 'et_id';
                                $sql = 'SELECT ' . $fld . ', return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost, returnid, ebayreturntime, ebayRefundAmount, returnQuantity, ebayreturnshipment FROM ' . $tbl;

                                break;
                            case 2:
                                $tbl = 'orders WHERE ';
                                $fld = 'oid';
                                $sql = 'SELECT ' . $fld . ', return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost FROM ' . $tbl;

                                break;
                            case 4:
                                $tbl = 'warehouse_orders WHERE ';
                                $fld = 'woid';
                                $sql = 'SELECT ' . $fld . ', return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost FROM ' . $tbl;

                                break;
                        }
                        $sql .= $fld . ' = ' . (int)$return['sold_id'];
                        $d = $this->db->query($sql);
                        if ($d->num_rows() > 0) {
                            $r = $d->row_array();

                            $return['uts'] = date2mk($r['returned_time']);
                            $return['created'] = $r['returned_time'];

                            if ($r['returned'] == 1) $return['returned_amount'] = floater($wid['paid'] / $qty);
                            else $return['returned_amount'] = 0;

                            if (floater($r['returned_extracost']) > 0) $return['extra_cost'] = floater($r['returned_extracost'] / $qty);
                            else $return['extra_cost'] = 0;

                            $return['return_id'] = $r['return_id'];

                            if ($return['channel'] == 1) {
                                $return['returnID'] = $r['returnid'];
                                if (trim($r['ebayreturntime']) != '') {
                                    $return['refund_date'] = $r['ebayreturntime'];
                                    $return['refund_date_mk'] = date2mk($return['refund_date']);
                                }

                                $rqty = (int)$r['returnQuantity'];
                                if ($rqty == 0) $rqty = 1;

                                $return['return_total_qty'] = $rqty;
                                if (floater($r['ebayRefundAmount']) > 0) $return['returned_amount'] = floater(($r['ebayRefundAmount'] / $rqty));
                                else $return['returned_amount'] = 0;
                                if (floater($r['ebayreturnshipment']) > 0) $return['return_shipping'] = floater(($r['ebayreturnshipment'] / $rqty));
                                else $return['return_shipping'] = 0;
                            }
                        }
                        $this->Myseller_model->Details_Returned($return);

                    } else $this->Myseller_model->Details_Removed_Sold((int)$wid['wid'], (int)$wid['sold_id'], (int)$wid['channel']);
                    $data['sold_date'] = '';
                    $data['paid'] = 0;
                    $data['shipped'] = 0;
                    $data['shipped_actual'] = 0;
                    //$data['ordernotes'] = '';
                    $data['sellingfee'] = 0;
                    if ($wid['status'] == 'Sold') {
                        $data['location'] = '';
                        $this->Mywarehouse_model->DoLocation('', (int)$wid['wid']);
                    }
                    $data['sold_id'] = 0;
                    $data['sold_subid'] = 0;
                    if (!isset($nocleanstat)) {
                        if ((int)$_POST['channel'] == 4 && ($wid['prevstatus'] != 'Sold' && $wid['prevstatus'] != 'On Hold')) $data['status'] = $wid['prevstatus'];
                        else {
                            if ($data['status'] != 'Scrap') $data['status'] = 'Listed';
                        }
                    }
                    $data['sold'] = '';
                    $data['vended'] = 0;
                    $actionqn = -1;
                    $data['soldqn'] = '';
                    $data['setshipped'] = 0;
                    $data['trans_date'] = '';
                    $data['trans_mk'] = 0;
                    $data['netprofit'] = 0;


                    $data['ordernotes'] = str_replace($chan . ' Order ' . $_POST['soldid'] . ' | ', '', $wid['ordernotes']);

                } else {
                    $data['sold_id'] = (int)$_POST['soldid'];
                    $data['sold_subid'] = (int)$_POST['subid'];
                    if ($mark == 1 || (int)$_POST['channel'] == 4 || (!$assign && $secondaryassign)) {
                        $data['status'] = 'Sold';
                        $data['location'] = 'Sold';
                        $this->Mywarehouse_model->DoLocation($data['location'], (int)$wid['wid']);

                        if (isset($this->vended)) $_POST['logvedned'] = $data['vended'] = $this->vended;
                        else $_POST['logvedned'] = $data['vended'] = 1;
                        $data['setshipped'] = mktime();
                        if ((int)$_POST['channel'] == 1) $data['sold'] = 'eBay';
                        elseif ((int)$_POST['channel'] == 2) $data['sold'] = 'WebSite';
                        elseif ((int)$_POST['channel'] == 4) $data['sold'] = 'Warehouse';
                        //$data['ordernotes'] = $data['sold'].' Order '.$data['sold_id'].' | '.$data['ordernotes'];
                        if ((int)$_POST['channel'] == 4) {
                            $data['paid'] = floater($wid['paid']);
                            $data['shipped_actual'] = floater($wid['shipped_actual']);
                            if (trim($_POST['ieprice']) != '') $data['paid'] = floater($_POST['ieprice']);
                            if (trim($_POST['ieshipping']) != '') $data['shipped_actual'] = floater($_POST['ieshipping']);

                            $data['netprofit'] = $this->Myseller_model->NetProfitCalc((float)$data['paid'], 0, (float)$data['shipped_inbound'], (float)$wid['cost'], (float)$data['sellingfee'], (float)$data['shipped_actual'], 0);
                            //floater(((float)$data['paid'])-((float)$wid['cost']+(float)$wid['sellingfee']+(float)$data['shipped_actual']));
                            //$data['trans_date'] = CurrentTime();
                            //$data['trans_mk'] = $data['setshipped'];
                            $this->Myseller_model->Details_Sold($data, $wid['wid'], (int)$_POST['soldid'], (int)$_POST['channel']);
                            if (trim($_POST['ieprice']) == '') unset($data['paid']);
                            unset($data['shipped']);
                            if (trim($_POST['ieshipping']) == '') unset($data['shipped_actual']);
                            unset($data['sellingfee']);
                        } elseif ((int)$_POST['channel'] == 1) {
                            $data['paypal_fee'] = floater($this->Myseller_model->PayPalFee(((float)$data['paid'] + (float)$data['shipped_actual'])));
                            $data['netprofit'] = floater($this->Myseller_model->NetProfitCalc((float)$data['paid'], (float)$data['shipped'], (float)$data['shipped_inbound'], (float)$wid['cost'], (float)$data['sellingfee'], (float)$data['shipped_actual'], (float)$data['paypal_fee']));
                            //$data['netprofit'] = floater(((float)$data['paid']+(float)$data['shipped'])-((float)$wid['cost']+(float)$data['sellingfee']+(float)$data['paypalfee']+(float)$data['shipped_actual']));
                            $this->Myseller_model->Details_Sold($data, $wid['wid'], (int)$_POST['soldid'], (int)$_POST['channel']);
                        } else {
                            $data['netprofit'] = floater($this->Myseller_model->NetProfitCalc((float)$data['paid'], (float)$data['shipped'], (float)$data['shipped_inbound'], (float)$wid['cost'], (float)$data['sellingfee'], (float)$data['shipped_actual'], 0));
                            $this->Myseller_model->Details_Sold($data, $wid['wid'], (int)$_POST['soldid'], (int)$_POST['channel']);
                        }


                    } else {
                        $data['status'] = 'On Hold';
                        //$data['location'] = 'On Hold';
                        $_POST['logvedned'] = $data['vended'] = 2;
                        unset($data['paid']);
                        unset($data['shipped']);
                        unset($data['shipped_actual']);
                        unset($data['sellingfee']);
                        unset($data['soldqn']);
                        unset($data['sold_date']);
                        unset($data['ordernotes']);
                    }
                    $data['prevstatus'] = $wid['status'];
                    $actionqn = 1;

                }
                //MARK COMPLETE - vended = 1;
                if ((int)$_POST['channel'] == 2) $sdata = 'WebSite Sale ' . (int)$_POST['soldid'] . '/' . (int)$_POST['subid'];
                elseif ((int)$_POST['channel'] == 4) $sdata = 'Warehouse Sale ' . (int)$_POST['soldid'];
                else $sdata = 'eBay Sale ' . (int)$_POST['soldid'];

                $data['status_notes'] = 'Changed from "' . $wid['status'] . '" - ' . $sdata . ' by ' . $this->session->userdata['ownnames'];
                //if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
                //else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];

                //printcool ($data, '', 'update');
                $this->db->update('warehouse', $data, array('wid' => (int)$onewid));
                //LOG CHANGES
                foreach ($data as $k => $v) {//printcool ($v); printcool ($wid[$k]);
                    if ($v != $wid[$k]) $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);
                }

                if ((int)$wid['listingid'] > 0 && $assign) $this->Myseller_model->runAssigner($wid['listingid'], $actionqn);

                if (isset($toremove)) {
                    $_POST['wid'] = $toremove['wid'];
                    $_POST['remove'] = 1;
                    $this->BCNSalesAttach();
                    exit();
                }
                if ((int)$_POST['channel'] == 4) $this->Myseller_model->UpdateSoldQN($_POST['channel'], (int)$_POST['soldid']);
                else $this->Myseller_model->SaveSoldQN($data['channel'], $data['sold_id'], $data['sold_subid'], $qty);

                if ($assign) {
                    $this->Myseller_model->getSalesListings(array(0 => true, $wid['listingid'] => true));
                    $sales = $this->Myseller_model->getSales(array((int)$_POST['soldid']), (int)$_POST['channel']);

                    $this->mysmarty->assign('sales', (int)$_POST['channel']);
                    $this->mysmarty->assign('eid', $wid['listingid']);
                    $this->mysmarty->assign('id', (int)$_POST['soldid']);
                    if ((int)$_POST['channel'] == 2) $this->mysmarty->assign('subid', (int)$_POST['subid']);
                    $this->mysmarty->assign('quantity', $qty);
                    $this->mysmarty->assign('updatetime', CurrentTimeR());
                    if (count($wids) == $proccnt) echo json_encode(array('html' => $this->mysmarty->fetch('myseller/bcnarea.html'), 'countqn' => (int)count($sales), 'qn' => (int)$qty, 'soldid' => (int)$_POST['soldid'], 'subid' => (int)$_POST['subid']));
                }

            } else echo 0;
        }
	}else echo 0;
}
function CheckForEbayReturn($et_id,$display = false)
{
	$this->db->select('transid, itemid');
	$this->db->where('et_id', (int)$et_id);
	$e = $this->db->get('ebay_transactions');
	if ($e->num_rows() > 0)
	{
		$tr = $e->row_array();
		$this->_getReturnData($tr['itemid'], $tr['transid'], (int)$et_id, $display);	
	}	
}
function _getReturnData($item_id, $transaction_id, $et_id, $display= false)
{
	/*
	GET https://api.ebay.com/post-order/v2/return/search?
  item_id=string&
  transaction_id=string&
  return_state=ReturnCountFilterEnum&
  offset=integer&
  limit=integer&
  sort=ReturnSortField&
  creation_date_range_from=string&
  creation_date_range_to=string&
  states=ReturnStateEnum
	*/
	
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');	
        require($this->config->config['ebaypath'].'get-common/keys.php');

         $url = 'https://api.ebay.com/post-order/v2/return/search?item_id='.$item_id.'&transaction_id='.$transaction_id; //?fieldgroups=FULL
         //Setup cURL
         $header = array(
                        'Accept: application/json',
                        'Authorization: TOKEN '.$userToken,
                        'Content-Type: application/json',
                        'X-EBAY-C-MARKETPLACE-ID: EBAY-US'
                         );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            $this->_notify('Returns Curl error',curl_error($ch));
			return false;
        }
        curl_close($ch); 
        $data = (json_decode($response,true));
		if (isset($data['members']))
		{	
		//printcool($data['members'][0]['returnId']);
		//printcool($data['members'][0]['sellerTotalRefund']['estimatedRefundAmount']['value']);
		//printcool(CleanBadDate($data['members'][0]['creationInfo']['creationDate']['value']));
		if ($display)
		{
			printcool($data['members'][0]);
		}
		else $this->db->update('ebay_transactions', array('returnid'=>$data['members'][0]['returnId'], 'ebayRefundAmount'=>$data['members'][0]['sellerTotalRefund']['estimatedRefundAmount']['value'], 'ebayreturntime'=>CleanBadDate($data['members'][0]['creationInfo']['creationDate']['value'])), array('et_id' => (int)$et_id));
		}	
		
}
function OrderShowAllBcns()
{
	if (isset($_POST['soldid']) && isset($_POST['subid']) && isset($_POST['channel']))
	{
			$sales = $this->Myseller_model->getSales(array((int)$_POST['soldid']), (int)$_POST['channel']);
			$this->mysmarty->assign('showall', TRUE);			
			$this->mysmarty->assign('sales', (int)$_POST['channel']);
			$this->mysmarty->assign('id', (int)$_POST['soldid']);
			if ((int)$_POST['channel'] == 2) $this->mysmarty->assign('subid', (int)$_POST['subid']);
			echo $this->mysmarty->fetch('myseller/bcnarea.html');
	}
}
function ChannelSave()
{
	if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['channel']))
	{		
		$wid = $this->Mywarehouse_model->getbcnattachdata((int)$_POST['wid']);
		if ($wid)
		{
			$data['channel'] = (int)$_POST['channel'];		
			printcool ($data, false, (int)$_POST['wid']);
			$this->db->update('warehouse', $data, array('wid' => (int)$_POST['wid']));
			
			 $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'channel', $wid['channel'], $data['channel']);
			
			echo $this->_getbcnsnippet((int)$_POST['listingid'], false, 'listing');	
		}else echo 0;		
	}else echo 0;
}
function StatusSave()
{
	if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['status']))
	{		
		$wid = $this->Mywarehouse_model->getbcnattachdata((int)$_POST['wid']);
		if ($wid)
		{
			$data['status'] = trim($_POST['status']);
			
			$data['status_notes'] = 'Changed from "'.$wid['status'].'" - StatusSave by '.$this->session->userdata['ownnames'];
			//if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
			//else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];	
			
			
			$this->db->update('warehouse', $data, array('wid' => (int)$_POST['wid']));
			if ($data['status'] == 'Scrap')
			{
				$this->load->model('Myseller_model'); 
				 $nope = array();
						$this->Myseller_model->HandleBCN($nope,$nope,(int)$_POST['wid']);
						
				// $this->Mywarehouse_model->ReProcessNetProfit((int)$_POST['wid']);	
			}
			 $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'status', $wid['status'], $data['status']);
			 if ($data['status'] == 'Listed') $actionqn = -1;
			else $actionqn = 1;
			$this->Myseller_model->runAssigner($wid['listingid'], $actionqn);
			echo $this->_getbcnsnippet((int)$_POST['listingid'], false, 'listing');	
		}else echo 0;		
	}else echo 0;
}
function RenameSKUField($id = 0)
{
	if (isset($_POST['val']) && trim($_POST['val']) != '' && isset($_POST['field']) && trim($_POST['field']) != '')
	{
		$this->db->update('warehouse_sku', array(trim($this->input->post('field', true)) => trim($this->input->post('val', true))), array('wsid' => (int)$id));
		echo 1;		
	}	
	else echo 0;
}
function UpdateGhost($wid = 0)
{
	if (isset($_POST['val']) && trim($_POST['val']) != '')
	{
		
		$wid = (int)$wid;
		$val = htmlspecialchars(trim($this->input->post('val',true)));
		$sales = (int)$this->input->post('sales',true);
		$parentwid = $this->Mywarehouse_model->getbcnattachdata($wid);										
		$this->db->where('bcn', trim($val));
		$this->db->or_where('lot', trim($val));
		$this->db->or_where('oldbcn', trim($val));
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)
		{
			$matchedwid = $q->row_array();
			if ($matchedwid['sold_id'] > 0 || ($matchedwid['status'] == "Sold" || $matchedwid['status'] == "On Hold")) echo json_encode(array('msg' => 'Assigned to a sale', 'val' => $parentwid['bcn']));
			elseif ($parentwid && $matchedwid['listingid'] == $parentwid['listingid']) echo json_encode(array('msg' => 'Assigned to same listing', 'val' => $parentwid['bcn']));
			elseif ($parentwid && $matchedwid['listingid'] > 0) echo json_encode(array('msg' => 'Assigned to another listing', 'val' => $parentwid['bcn']));
			else 
			{
				
				if ($sales == 0)
				{
					$this->db->update('warehouse', array('listingid' => $parentwid['listingid'], 'status' => $parentwid['status'], 'vended' => $parentwid['vended']), array('wid' => $matchedwid['wid']));
					$this->Auth_model->wlog($matchedwid['bcn'], $matchedwid['wid'], 'listingid', $matchedwid['listingid'], $parentwid['listingid']);
					$this->Auth_model->wlog($matchedwid['bcn'], $matchedwid['wid'], 'status', $matchedwid['status'], $parentwid['status']);
					$this->db->update('warehouse', array('listingid' => 0, 'deleted' => 1), array('wid' => $parentwid['wid']));
					$this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'listingid', $parentwid['listingid'], 0);
					$this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'deleted', 0, 1);
					
					$this->Myseller_model->getBase(array($parentwid['listingid']));				
					$this->mysmarty->assign('eid', (int)$parentwid['listingid']);
					$this->mysmarty->assign('id', (int)$parentwid['listingid']);
					$areaid = $parentwid['listingid'];
					
				}
				else
				{
					$data = $this->Mywarehouse_model->getsaleattachdata($parentwid['channel'], $parentwid['sold_id'], $parentwid['listingid'],0);
				
					$qty = $data['qty'];
					unset($data['qty']);
					unset($data['mark']);
					$data['sold_id'] = $parentwid['sold_id'];
					$data['sold_subid'] = $parentwid['sold_subid'];
					$data['status'] = $parentwid['status'];
					$data['sold'] = $parentwid['sold'];
					$data['vended'] = $parentwid['vended'];
					$data['channel'] = $parentwid['channel'];
					
					$this->db->update('warehouse' ,$data, array('wid' => $matchedwid['wid']));
					$this->db->update('warehouse', array('sold_id' => 0, 'deleted' => 1), array('wid' => $parentwid['wid']));
					$this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'sold_id', $parentwid['sold_id'], 0);
					$this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'deleted', 0, 1);
					foreach ($data as $k => $v)
					{//printcool ($v); printcool ($wid[$k]);
					 if ($v != $matchedwid[$k]) $this->Auth_model->wlog($matchedwid['bcn'], $matchedwid['wid'], $k, $matchedwid[$k], $v);	
					}					
		
					$this->Myseller_model->getSalesListings(array(0 => true,$parentwid['listingid'] => true));			 
					$this->Myseller_model->getSales(array((int)$parentwid['sold_id']), (int)$parentwid['channel']);
								
					$this->mysmarty->assign('sales', (int)$parentwid['channel']);
					$this->mysmarty->assign('eid', $parentwid['listingid']);
					$this->mysmarty->assign('id', (int)$parentwid['sold_id']);
					$areaid = $parentwid['sold_id'];
					if ($parentwid['channel'] == 2) $this->mysmarty->assign('subid', (int)$parentwid['sold_subid']);
					$this->mysmarty->assign('quantity', $qty);
					
				}
				
				$this->mysmarty->assign('updatetime', CurrentTimeR());	
				echo json_encode(array('html' => $this->mysmarty->fetch('myseller/bcnarea.html'), 'areaid' => $areaid));		
						
			}
			
		}
		else
		{			
			if ($parentwid)
			{
				$this->db->update('warehouse', array('bcn' => $val, 'bcn_p1' => $val, 'generic' =>0), array('wid' => $parentwid['wid']));
				 $this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'bcn', $parentwid['bcn'], $val);
				 echo 1;
			}
		}
	}
											
}
function clean()
{
$this->db->where('wid >', 85485);
$this->db->delete('warehouse');

}
function ListingGhostGen($called = false)
{ 
	if ((!$called && isset($_POST['listingid']) && isset($_POST['amount']) && isset($_POST['issale'])) || ($called && isset($called['listingid']) && isset($called['qn'])))
	{ 
	if ($_POST['issale'] == 0) $title = $this->Mywarehouse_model->GetListingTitleAndCondition((int)$_POST['listingid'], true);
		
		$this->db->select("bcn");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->where('bcn_p1' , "G");
		//$this->db->order_by("bcn_p2", "DESC");
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse', 1);
		
			if (!$called) $listingid = (int)$_POST['listingid'];
			else $listingid = $called['listingid'];
			if ($w->num_rows() > 0)
			{
				$next = $w->row_array();
				$next = (int)str_replace('G', '', trim($next['bcn']));	
			}
			else $next = 1;
//printcool ($next);
			if (!$called) $amount = (int)$_POST['amount'];
			else $amount = $called['qn'];
			
			$start = 1;
			while ($start <= $amount)
						{
							
							$next++;
							
							/*$this->db->where('bcn', "G".$next);
							$this->db->or_where('lot', "G".$next);
							$this->db->or_where('oldbcn', "G".$next);
							$q = $this->db->get('warehouse');
							if ($q->num_rows() > 0)
							{
								$next++;
							}*/
							$array['waid'] = 0;
							$array['bcn'] = "G".$next;
							$array['bcn_p1'] = "G";
							$array['bcn_p2'] = $next;
							if ($_POST['issale'] == 1)
							{
								$array['sold_id'] = $listingid;
								$array['status'] = 'Sold';
								$array['sold'] = 'Warehouse';
								$array['sold_date'] = CurrentTime();		
								//$array['ordernotes'] = 'Order '.$listingid;	
								$array['channel'] = 4;	
								$array['vended'] = 1;						
							}
							else
							{
								$array['listingid'] = $listingid;
								$array['status'] = 'Listed';
								if ($title) $array['title'] = $title;
								$array['listed_date'] = CurrentTime();
								$array['listed'] = 'eBay '.$listingid;
							}
							$array['createddate'] = CurrentTime();
				 			$array['createddatemk'] = mktime();
							$array['generic'] = 1;							
							$array['adminid'] = (int)$this->session->userdata['admin_id']; 
							$this->db->insert('warehouse', $array);
							$array['wid'] = $this->db->insert_id();
							foreach ($array as $k => $v)
							{
								if ($k !='bcn_p1' && $k !='bcn_p2' && $k !='bcn_p3' && $k !='vended')$this->Auth_model->newlog($array['bcn'], $array['wid'], $k, $v);		
							}
							unset($array);
							$start++;
							
						}
			
			
			$actionqn = 0 - $amount;	
				
			if ($_POST['issale'] == 1)
			{ 
				echo $this->_getbcnsnippet($listingid, false, 'sale');
			}
			else
			{
				$this->Myseller_model->runAssigner((int)$_POST['listingid'], $actionqn);
				echo $this->_getbcnsnippet($listingid, false, 'listing');					
			}
 	
	}else echo 0;		
}
function _getbcnsnippet($id, $subid, $type)
{	
	if ($type == 'listing') 
	{
		
		$this->Myseller_model->getBase(array($id));
		$this->Myseller_model->getOnHold(array($id));
	}
	$this->mysmarty->assign('id', $id);	
	$this->Myseller_model->getChannelData($id);
	$this->mysmarty->assign('updatetime', CurrentTimeR());	
	echo $this->mysmarty->fetch('myseller/bcnarea.html');
}
function Finder($return = false, $listingview = false, $skuid = false, $type = false)
{
	if ($return)
	{
		 $str = trim($this->input->post('src'));
		 if (!isset($_POST['listingid'])) $listingid = false;
		 else $listingid = (int)$this->input->post('listingid');
		 $this->mysmarty->assign('listingid', $listingid);
		 $this->mysmarty->assign('action', (int)$this->input->post('wid'));
	}
	else 
	{
		if (isset($this->gotobcn)) $str = $this->gotobcn;
		else $str = trim($this->input->post('find1'));
		$listingid = false;
	}
	
	if ($return)
	{
		if ($skuid) $to = trim($this->input->post('srcto'));
		else $to= false;
		if ($type && $type == 'Vendor')	$listingid = $skuid;
		$this->mysmarty->assign('list', $this->Mywarehouse_model->GetFound($str, $listingid, $to,$type));
		if ($listingview)
		{
			 $this->mysmarty->assign('listingview', $listingview);
			 if ($skuid) $this->mysmarty->assign('skuid', (int)$skuid);
			  if ($type) $this->mysmarty->assign('type', $type);
		}
		echo $this->mysmarty->fetch('mywarehouse/finder_listing.html');	
	}
	else 
	{
		if ($str != '') $list = $this->Mywarehouse_model->GetFound($str, $listingid);
		else
		{
			$this->session->set_flashdata('error_msg', 'Empty search value');
			if (($_SERVER['HTTP_REFERER'] != $this->config->config['base_url'].'/Mywarehouse/Finder') && ($_SERVER['HTTP_REFERER'] != ''))
			{ 			
				header("Location: ".$_SERVER['HTTP_REFERER']);
				exit();
			}
		}
		if (count($list) == 1)
		{
		
			$warehouse_area = $this->session->userdata('warehouse_area');
			//$warehouse_area2 = $this->session->userdata('warehouse_area2');
			
			$this->session->set_userdata('warehouse_area2', $this->session->userdata('warehouse_area'));
			$this->session->set_userdata('warehouse_area', '');			
			if ((int)$this->input->post('redirect', true) == 2)  Redirect('Mywarehouse/Testing/'.$list[0]['waid'].'/'.$list[0]['wid']);
			elseif ((int)$this->input->post('redirect', true) == 3)  Redirect('Mywarehouse/Accounting/'.$list[0]['waid'].'/'.$list[0]['wid']);
			elseif ((int)$this->input->post('redirect', true) == 4)  Redirect('Mywarehouse/Parting/'.$list[0]['wid']);
			elseif ((int)$this->input->post('redirect', true) == 5)  Redirect('Myebay/Search/'.$list[0]['listingid']);
			elseif ((int)$this->input->post('redirect', true) == 6)
			{
				if ($list[0]['psku'] != 0) Redirect('Mywarehouse/Skudetails/'.$list[0]['psku']);
				else   Redirect('Mywarehouse/Skudetails/'.$list[0]['sku']);
			}
			elseif ($warehouse_area == 'testing'/* || $warehouse_area2 == 'testing'*/) Redirect('Mywarehouse/Testing/'.$list[0]['waid'].'/'.$list[0]['wid']);
			elseif ($warehouse_area == 'accounting'/* || $warehouse_area2 == 'accounting'*/) Redirect('Mywarehouse/Accounting/'.$list[0]['waid'].'/'.$list[0]['wid']);
			else  Redirect('Mywarehouse/bcndetails/'.$list[0]['wid']);
		
		}
		else
		{
			//$this->session->set_flashdata('error_msg', 'No Results');
		}
		$this->mysmarty->assign('list', $list);
		$this->mysmarty->assign('find1', $str);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->view('mywarehouse/finder_main.html');	
	}
}
function AttachBcnToListing()
{
	$this->Mywarehouse_model->AttachBcnToListing((int)$this->input->post('wid'), (int)$this->input->post('listingid'));
}
function DettachBcnFromListing()
{
	$this->Mywarehouse_model->DettachBcnFromListing((int)$this->input->post('wid'), (int)$this->input->post('listingid'));
}
function GetAttachedBcns()
{
	$listingid = (int)$this->input->post('listingid');
	$this->mysmarty->assign('list', $this->Mywarehouse_model->GetAttachedBcns($listingid));
	$this->mysmarty->assign('attached', TRUE);
	$this->mysmarty->assign('listingid', $listingid);
	$this->mysmarty->assign('action', (int)$this->input->post('wid'));
	echo $this->mysmarty->fetch('mywarehouse/finder_listing.html');		
}
function PasteBcnToReturn($orderid = 0, $ordertype = 0)
{
	$this->load->model('Myebay_model');
	$returnid = $this->Myebay_model->getReturnID((int)$orderid, (int)$ordertype);
	if (isset($_POST['data']) && count($_POST['data']) > 0)
		{
			foreach ($_POST['data'] as $p) $from[] = trim($p[0]);
			$to = false;
			$id = (int)$returnid;
		}
		else exit('Bad Data');	
	
	$res = $this->Mywarehouse_model->GetSelection($from, $to, $id, '', TRUE, TRUE);
	
	if ($res && $returnid) 
	{
		$_POST['returnid'] = (int)$returnid;
		foreach ($res as $r)
		{
			$_POST['wid'] = $r['wid'];
			$this->AddBcnToReturn();
		}
	}	
}
function AddBcnToReturn()
{
	$returnid = (int)$this->input->post('returnid', true);
	$wid = (int)$this->input->post('wid', true);
	$this->db->update('warehouse', array('return_id' => $returnid), array('wid' => $wid));
}
function Returned($page = 1)
{
	$adms = $this->Mywarehouse_model->GetAdminList();
}
function Returns($orderid = 0, $ordertype = 0, $reload = false)
{
	/*//if both are 0 -> display from / to, and bcn hot box
		-> post to return items
		-> display matches with bcn/status/apply checkbox in table
		-> onclick add bcn to return (create new vendor return row and 
	//count all order bcns / sku/bcn/titel/cost/notes/statys/returnstatus
	//load into HOT
		//buttons in html for recieved and payment refunded
	//reload function
	//save function
	*/
	$adms = $this->Mywarehouse_model->GetAdminList();
	
	if ((int)$orderid == 0 && (int)$ordertype == 0)
	{
		if (isset($_POST['vtitle']))
		{
			$this->db->insert('returns', array('orderid'=> 0, 'channel'=> 5, 'vtitle' => $this->input->post('vtitle', false), 'vdate' => CurrentTime(), 'adminid' => (int)$this->session->userdata['admin_id']));
			$orderid = $this->db->insert_id();
			$this->db->update('returns', array('orderid' => $orderid), array('vid' => $orderid));
			Redirect('Mywarehouse/Returns/'. $orderid.'/5');	
		}
		$this->db->where('channel', 5);
		$this->db->order_by('vid', 'DESC');
		$vrl = $this->db->get('returns');
		$vr = false;
		if ($vrl->num_rows() > 0)
		{
			$vr = $vrl->result_array();	
		}
		$this->mysmarty->assign('vritems', $vr);

		$this->mysmarty->assign('adms', $adms);
		
		$this->mysmarty->display('mywarehouse/returns.html');
	}
	else
	{
		if(isset($_POST) && $_POST)
		{	
				$colMap = array(
							0 => 'GO',
							1 => 'bcn',
							2 => 'sku',
							3 => 'title',
							4 => 'cost',
							5 => 'status',
							6 => 'status_notes',
							7 => 'returnstatus',
							8 => 'returned',
							9 => 'returned_notes',
							10 => 'returned_time',
							11 => 'returned_recieved', 
							12 => 'returned_refunded',
							13 => 'returned_extracost'	
						  );
				$bcolMap = array(
							0 => 'GO',
							1 => 'BCN',
							2 => 'SKU',
							3 => 'Title',
							4 => 'Cost',
							5 => 'Status',
							6 => 'Status Notes',
							7 => 'Return Status',
							8 => 'Returned',
							9 => 'Reason',
							10 => 'Time',
							11 => 'Received',
							12 => 'Refunded',
							13 => 'XtraCost'
						  );

				$out = '';
				$sout = '';
				$sessback = $this->_loadsession($this->session->userdata('sessfile'));
				
				$saveid = $sessback['ord'];
				$saverel = $sessback['rel'];
				if ($saveid != (int)$id) { echo json_encode(array('msg' => '!!!!! CANNOT SAVE. YOU HAVE ANOTHER EDITOR OPEN !!!!!')); }	//printcool ($_POST);
				else {
				foreach($_POST as $d)
				{
					foreach($d as $dd)
					{
					$dd[3] = floatercheck($colMap[(int)$dd[1]], $dd[3]);
					if ($dd[2] != $dd[3])
					{
						
						if (!isset($saverel[(int)$dd[0]]['bcn'])) GoMail(array ('msg_title' => '!isset($saverel[(int)$dd[0]][bcn]) @ '.CurrentTime(), 'msg_body' => printcool ($d, true, 'D').printcool ($saverel,true,'saverel').printcool($_POST, true, 'POST'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						if (!isset($saverel[(int)$dd[0]]['wid'])) GoMail(array ('msg_title' => '!isset($saverel[(int)$dd[0]][wid]) @ '.CurrentTime(), 'msg_body' => printcool ($d, true, 'D').printcool ($saverel,true,'saverel').printcool($_POST, true, 'POST'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
					
					if ($colMap[(int)$dd[1]] == 'returned')
					{
					if ($dd[3] == 'Replace') $dd[3] = 2;
					else $dd[3] = 1;
					}
							
					$this->Auth_model->wlog($saverel[(int)$dd[0]]['bcn'], $saverel[(int)$dd[0]]['wid'], $colMap[(int)$dd[1]], $dd[2], $dd[3]);
					
					$out .= ' "'.$bcolMap[(int)$dd[1]].'" for BCN '.$saverel[(int)$dd[0]]['bcn'].' Changed from "'.$dd[2].'" to "'.$dd[3].'" ';
					$sout .= $saverel[(int)$dd[0]]['bcn'].'/"'.$bcolMap[(int)$dd[1]].'" Changed ';
					
					
					$updt = array($colMap[(int)$dd[1]] => $dd[3]);
					
					$this->db->update('warehouse', $updt, array('wid' => $saverel[(int)$dd[0]]['wid']));					
					
					if ($colMap[(int)$dd[1]] == 'paid' || $colMap[(int)$dd[1]] == 'cost' || $colMap[(int)$dd[1]] == 'sellingfee'|| $colMap[(int)$dd[1]] == 'shipped_actual' || ($colMap[(int)$dd[1]] == 'status' && $dd[3] == 'Scrap'))
					{
						$this->load->model('Myseller_model'); 
						 $nope = array();
						$this->Myseller_model->HandleBCN($nope,$nope,(int)$saverel[(int)$dd[0]]['wid']);
						//$this->Mywarehouse_model->ReProcessNetProfit((int)$saverel[(int)$dd[0]]['wid']);
					}
					$out = array('msg' => $out, 'smsg' => $sout, 'row' =>$dd[0], 'col' => $dd['1']);
					}
					}
				}				
				echo json_encode($out);	
				}
		}
		else
		{	
	 
		$this->load->model('Myebay_model');
	
		if ((int)$orderid > 0 && (int)$ordertype == 0) 
		{
			$bcns = $this->Myebay_model->getOrderReturnedBCN(0, true, (int)$orderid); //(int)$orderid is page			
			$this->mysmarty->assign('pages', $bcns['pages']);
			$this->mysmarty->assign('page', (int)$orderid);
			$bcns = $bcns['data']; 
		}
		else $bcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int)$orderid, (int)$ordertype), true);
		if ($bcns)
		{
			$list['data'] = $bcns;
			
			foreach ($list['data'] as $k => $l)
			{
				$h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);				
			}		
			
			
			$sesfile = $this->_savesession(array('rel' => $h, 'ord' => (int)$id));
			$this->session->set_userdata(array('sessfile' => $sesfile));
			
			
			
			$loaddata = '';
			
			foreach ($list['data'] as $k => $l)
			{
				$returned_recieved = cstr($l['returned_recieved']);
				$returned_refunded = cstr($l['returned_refunded']);
				if ($returned_recieved == '') $returned_recieved = '<button onClick="setreceived('.(int)$orderid.','.(int)$ordertype.',1, '.$l['wid'].')">Received</button>';
				if ($returned_refunded == '') $returned_refunded = '<button onClick="setrefunded('.(int)$orderid.','.(int)$ordertype.',1, '.$l['wid'].')">Payment Refunded</button>';
				if ($l['returned'] == 2) $l['returned'] = 'Replace';
				else $l['returned'] = 'Refund';
				$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", 
				cstr($l['bcn']), cstr($l['sku']), cstr($l['title']), cstr($l['cost']),cstr($l['status']),cstr($l['status_notes']),cstr($l['returnstatus']),cstr($l['returned']),cstr($l['returned_notes']),cstr($l['returned_time']), $returned_recieved, $returned_refunded,cstr($l['returned_extracost']));
				
				
			}
			if (count($list['data']) > 0)
			{
			foreach ($returndata as $kr => $r)
				{
					$loaddata .= "["; 
					foreach ($r as $krr => $rr)
					{
						$loaddata .= "'".$rr."',"; 
						$returndata[$kr][$krr]= stripslashes($rr);
					}
					$loaddata .= "],"; 
					
				}	
			}	
			if ($reload)
			{
				echo json_encode($returndata);

				exit();
			}
			
			$this->load->model('Myseller_model');
			$this->statuses = $this->Myseller_model->assignstatuses();
			
			}
			
			$fielset = array(
		'headers' => "'GO', 'BCN', 'SKU', 'Title', 'Cost', 'Status', 'Status Notes', 'Return Status', 'Returned', 'Reason', 'Time', 'Received', 'Refunded', 'XtraCost'",
		/*'rowheaders' => $list['headers'], */
		'width' => "60, 80, 100, 200, 80, 125, 125, 125, 125, 125, 125, 125, 125, 125", 
		'startcols' => 14, 
		'startrows' => count($list['data']), 
		'autosaveurl' => "/Mywarehouse/Returns/".(int)$orderid."/".(int)$ordertype."/true",		
		'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{readOnly: true},{},{},{type: "dropdown", source: ['.$this->statuses['testingstring'].']},{},{type: "dropdown", source: ["To Be Returned", "Waiting on vendor","Awaiting Shipment","Completed"]},{type: "dropdown", source: ["Refund","Replace"]},{},{},{renderer: "html"},{renderer: "html"},{}');
		
				
		$this->mysmarty->assign('headers', $fielset['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
		$this->mysmarty->assign('width', $fielset['width']);
		$this->mysmarty->assign('startcols', $fielset['startcols']);
		$this->mysmarty->assign('startrows', $fielset['startrows']);
		$this->mysmarty->assign('autosaveurl',$fielset['autosaveurl']);
		$this->mysmarty->assign('colmap', $fielset['colmap']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('copyrows', count($list['data']));	
		
		$this->mysmarty->assign('orderid', (int)$orderid);
		$this->mysmarty->assign('ordertype', (int)$ordertype);
		
		$this->mysmarty->display('mywarehouse/returns.html');
		}
	}
}


function ProcessOldEbay()
{
	exit();
		$this->db->select('e_id, e_title, e_manuf, e_part, e_model, e_compat, e_package, e_condition');
		$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "ASC");
		$q = $this->db->get('ebay');
		
		
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $r)
			{
				
				//echo '<tr>';
				//echo '<td>'.$r['e_id'].'</td>';
				//echo '<td>'.$r['e_title'].'</td>';
				//echo '<td>'.$r['e_manuf'].'</td>';
				$r['e_part'] = array_map('trim',explode(',', $r['e_part']));
				//foreach ($r['e_part'] as $p)
				//	{						
						//$r['e_part'][] = trim($p);	
				//	}
				//echo '<td>'.$r['e_model'].'</td>'; echo '<td>'.$r['e_compat'].'</td>';
				//echo '<td>'.$r['e_package'].'</td>';
				//echo '<td>'.$r['e_condition'].'</td>';
				//echo '</tr>';				
				$bcn_p1 = date("m").substr(date("y"), 1, 1);
				foreach($r['e_part'] as $k => $v)			
				{
					 if (trim($r['e_compat']) != '') $data['mfgpart'] = $r['e_model'].' | '.$r['e_compat'];
					 else $data['mfgpart'] = $r['e_model'];
					$data['mfgname'] = $r['e_manuf'];
					$data['title'] = $r['e_title'];
					$data['listingid'] = $r['e_id'];	
					$data['aucid'] = 'eBayDB '. ceil($r['e_id']/100);
					
				
					$bcn_p2 = sprintf('%05u',$this->Mywarehouse_model->GetNextBcn((int)$bcn_p1));
			
					$data['bcn'] = $bcn_p1.'-'.$bcn_p2.'-'.$v;
					$data['bcn_p1'] = $bcn_p1;
					$data['bcn_p2'] = $bcn_p2;
					$data['bcn_p3'] = $v;
					$data['notes'] = $r['e_package'];
					$data['problems'] = $r['e_condition'];
					//printcool ($data);
					//$this->db->insert('warehouse', $data);
				}	
			}
		}
		
		echo 'COMPLETE';
}
function GetWarehouseItems()
{
	if (!isset($_POST['aucid'])) exit('No ID');
	$aucid = trim($this->input->post('aucid'));
	$list = $this->Mywarehouse_model->GetWarehouseItems($aucid);
	$sellingfee = 0;
	$shippingfee = 0;
	$emptysellingfee = 0;
	$emptyshippingfee = 0;
	if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
	{		
		if ($list) foreach ($list as $l)
		{
			if ((float)$l['sellingfee'] == 0) $emptysellingfee++;
			if ((float)$l['shipped'] == 0) $emptyshippingfee++;
			$sellingfee = $sellingfee+(float)$l['sellingfee'];
			$shippingfee = $shippingfee+(float)$l['shipped'];
		}		
	}
	$this->mysmarty->assign('sellingfee', $sellingfee);
	$this->mysmarty->assign('shippingfee', $shippingfee);
	$this->mysmarty->assign('emptysellingfee', $emptysellingfee);
	$this->mysmarty->assign('emptyshippingfee', $emptyshippingfee);
	$this->mysmarty->assign('list', $list);
	$this->mysmarty->assign('aucid', $aucid);
	$this->mysmarty->assign('showparts', $this->session->userdata('showparts'));
	$this->mysmarty->assign('showparents', $this->session->userdata('showparents'));
	$this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction(trim($aucid)));
	echo $this->mysmarty->fetch('mywarehouse/warehouse_items.html');		
	
}
function FillAcutions()
{
	exit();
	$this->db->select("distinct aucid", false);
		$this->query = $this->db->get('warehouse');
		//printcool($this->query->result_array());
		foreach ($this->query->result_array() as $r)
		{
			$this->db->insert('s', array('wtitle' => $r['aucid'], 'wdate' => CurrentTime()));
			$this->db->update('warehouse', array('waid' => $this->db->insert_id()), array('aucid' => $r['aucid']));			
		}	
}
function FillLastAuctionReport()
{
	exit();
		$this->db->select("wid, waid");
		$this->query = $this->db->get('warehouse');
		//printcool($this->query->result_array());
		foreach ($this->query->result_array() as $r)
		{
			$this->db->update('warehouse', array('insid' => $r['waid']), array('wid' => $r['wid']));			
		}	
}
function GetEbayLiveDBData($mod = 0, $type = 0)
{
	$ebl = array('active' => false, 'sold' => false, 'unsold' => false);
	$query = $this->db->get('ebay_live');
	foreach ($query->result_array() as $r)
	{
		if ($r['etype'] == 's') $ebl['sold'][] = $r;
		elseif ($r['etype'] == 'u') $ebl['unsold'][] = $r;
		else $ebl['active'][] = $r;
		
		if ($r['eid'] > 0)
		{
			/*if (($r['lq'] != $r['ebavq']) || $r['lebq'] != $r['ebtq'])	
			{
				$this->db->select('el_id, eid, ebavq, ebtq, lq, lebq, itemid');
				$this->db->where('el_id', (int)$r['el_id']);
				$query = $this->db->get('ebay_live');
				if ($query->num_rows() > 0) 
				{
				$rm = $query->row_array();
				$this->db->update('ebay', array('qn_ch1'=>$r['ebavq'], 'ebayquantity'=>$r['ebtq']), array('e_id' => (int)$rm['eid']));
				$this->db->update('ebay_live', array('lq'=>$rm['ebavq'], 'lebq'=>$rm['ebtq']), array('el_id' => (int)$r['el_id']));

				$ra['admin'] = $this->session->userdata['ownnames'];
				$ra['time'] = CurrentTimeR();
				$ra['ctrl'] = 'UpdateQuantityFromActive';
				$ra['field'] = 'qn_ch1';
				$ra['atype'] = 'Q';
				$ra['e_id'] = (int)$r['eid'];
				$ra['ebay_id'] = (int)$r['itemid'];
				$ra['datafrom'] = $r['lq'];
				$ra['datato'] = (int)$r['ebavq'];							
				if ($ra['datafrom'] != $ra['datato']) $this->db->insert('ebay_actionlog', $ra);
				$ra['field'] = 'ebayquantity'; 				
				$ra['datafrom'] = $r['lebq'];
				$ra['datato'] = (int)$r['ebtq'];	
				if ($ra['datafrom'] != $ra['datato']) $this->db->insert('ebay_actionlog', $ra);
				}	
			 
			}*/
		}		
	}
	$this->mysmarty->assign('ebl', $ebl);
	$this->mysmarty->assign('mod', $mod);
	$this->mysmarty->assign('livetype', $type);
	$this->mysmarty->view('myebay/myebay_ebl.html');	
}






/*
function testloadurl()
{
	echo json_encode(array(array('id' => 1, 'qty' => 10, 'title' => 'title 1'), array('id' => 2, 'qty' => 20, 'title' => 'title 2'), array('id' => 3, 'qty' => 30, 'title' => 'title 3')));
}
function testsaveurl()
{
	Gomail(array('msg_title' => 'testsave', 'msg_body' => printcool($_POST,true)), 'mr.reece@gmail.com');
	$out['result'] = 'OK';	
	echo json_encode($out);	
}
function testautosaveurl()
{
	Gomail(array('msg_title' => 'testautosave', 'msg_body' => printcool($_POST,true)), 'mr.reece@gmail.com');
	
	$colMap = array(
					0 => 'qty',
					1 => 'cost',
					2 => 'mfgname',
					3 => 'mfgpart',
					4 => 'title',
					5 => 'lot'
				  );
	
	
	foreach($_POST as $d)
	{
		//$d[0] ROW
		//$d[1] COL
		//$d[2] FROM VAL
		//$d[3] TO VAL
		
		
		
	}
	$out['result'] = 'OK';	
	echo json_encode($out);	
}
function TestFullEditor()
	{
		
		$fielset = array('accounting' => array(
		'headers' => "'BCN', 'Where Listed', 'Listed Date', 'Where Sold' , 'Sold Date', 'Paid', 'Paid Date', 'Shipped', 'Shipped Date'", 
		'width' => "80, 125, 125, 125, 125, 125, 125, 125, 125", 
		'startcols' => 9, 
		'startrows' => 100, 
		'saveurl' => "/Mywarehouse/testsaveurl/", 
		'autosaveurl' => "/Mywarehouse/testautosaveurl/",
		'loadurl' => "/Mywarehouse/testloadurl/", 
		'colmap' => "{readOnly: true},{},{},{},{},{},{},{},{}")
		);
		
		$this->mysmarty->assign('headers', $fielset['accounting']['headers']);
		$this->mysmarty->assign('width', $fielset['accounting']['width']);
		$this->mysmarty->assign('startcols', $fielset['accounting']['startcols']);
		$this->mysmarty->assign('startrows', $fielset['accounting']['startrows']);
		$this->mysmarty->assign('saveurl', $fielset['accounting']['saveurl']);
		$this->mysmarty->assign('autosaveurl', $fielset['accounting']['autosaveurl']);
		$this->mysmarty->assign('loadurl', $fielset['accounting']['loadurl']);
		$this->mysmarty->assign('colmap', $fielset['accounting']['colmap']);
		
		$this->mysmarty->view('mywarehouse/editorgo.html');
		
	}
	
function TestEditorPre()
{
	
		$fielset = array('testing' => array(
		'headers' => "'BCN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes', 'Sell', 'LastUpdt', 'Tech'",
		'width' => "100, 50, 50, 50, 250, 250, 250, 50, 125, 125", 
		'startcols' => 9, 
		'startrows' => 10,  
		'autosaveurl' => "/Mywarehouse/Testing/".(int)$id, 
		'colmap' => '{data: 0, readOnly: true},{data: 1, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 2, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 3, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 4},{data: 5},{data: 6},{data: 7, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 8, readOnly: true},{data: 9, readOnly: true}') 
		);
		
		if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
		$list = $this->Mywarehouse_model->GetTesting((int)$id);
		if ($list)
		{
			$this->session->set_userdata(array('accrel' => $list['headers'], 'acclot' => (int)$id));
			$loaddata = '';
			$adms = $this->Mywarehouse_model->GetAdminList();
			foreach ($list['data'] as $k => $l)
			{
				$loaddata .= "['".$l['bcn']."', '".$l['post']."', '".$l['battery']."', '".$l['charger']."', '".$l['hddstatus']."', '".$l['problems']."', '".$l['notes']."', '".$l['tosell']."', '".$l['techlastupdate']."', '".$adms[$l['tech']]."'],
				";				
			}		
		}	

		$this->mysmarty->assign('headers', $fielset['testing']['headers']);
		$this->mysmarty->assign('rowheaders', $fielset['testing']['rowheaders']);
		$this->mysmarty->assign('width', $fielset['testing']['width']);
		$this->mysmarty->assign('startcols', $fielset['testing']['startcols']);
		$this->mysmarty->assign('startrows', $fielset['testing']['startrows']);		
		$this->mysmarty->assign('autosaveurl', $fielset['testing']['autosaveurl']);
		$this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
		$this->mysmarty->assign('colmap', $fielset['testing']['colmap']);
		
		$this->mysmarty->assign('id', (int)$id);
		$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());	
		$this->mysmarty->view('mywarehouse/editorpre.html');	
	
	
	
	
	
	
}*/



function parsebcn()
{exit();
$this->db->select('wid, bcn_p1, bcn_p2, bcn_p3');
$this->db->where("bcnparsed",0);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			//printcool($this->query->result_array());
			
			foreach ($this->query->result_array() as $k => $l)
			{
				$l['bcn_p2'] = (int)$l['bcn_p2'];
				if ($l['bcn_p3'] == '') $l['bcn'] = $l['bcn_p1'].'-'.$l['bcn_p2'];
				else $l['bcn'] = $l['bcn_p1'].'-'.$l['bcn_p2'].'-'.$l['bcn_p3'];
					
				$this->db->update('warehouse', array('bcn' => $l['bcn'], 'bcn_p2' => $l['bcn_p2'], 'bcnparsed' => 1), array('wid' => (int)$l['wid']));
			}
		}
}




function testpost()
{exit();
	if ($_POST) { printcool ($_POST); exit();}

echo '<form method="post" action="/Mywarehouse/testpost">';

$min = 1;
$max = 1000;


while ($min < $max)
{
	echo '<input type="text" name="col1'.$min.'" value="t">';
	echo '<input type="text" name="col2'.$min.'" value="t">';
	echo '<input type="text" name="col3'.$min.'" value="t">';
	echo '<input type="text" name="col4'.$min.'" value="t">';
	echo '<input type="text" name="col5'.$min.'" value="t">';
	echo '<input type="text" name="col6'.$min.'" value="t">';
	
	
$min++;	
}

echo '<input type="submit" name="go" vaalue="GO">';

echo '</form>';
	
}
function testmiv()
{
	$this->_logallpost();
phpinfo();
}


function _savesession($data = '')
{
	if (!is_array($data))  exit('Unable to write session - Data is not Array');
	$arr = json_encode($data);
	$name = mktime().(int)$this->session->userdata['admin_id'].'.txt';
	$this->load->helper('file');
	if (!write_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/sess/'.$name, $arr))
	{
		 exit('Unable to write session - File write error');
	}
	return $name;
}
function _loadsession($filename = '')
{ 
	if ($filename == '') exit('Error reading session');
	$this->load->helper('file');
	$newarr = read_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/sess/'.$filename);
	//unlink($this->config->config['pathtosystem'].'/application/sess/'.$filename);
	return json_decode($newarr,true);			
}

function _logallpost()
{
	$name = CurrentTime().'_'.(int)$this->session->userdata['admin_id'].'_'.str_replace(' Mywarehouse ','', str_replace('/',' ', $_SERVER['REQUEST_URI'])).'.txt';
	 if($_POST) file_put_contents($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/sess/post/'.$name, urldecode(file_get_contents("php://input")));
}

function readpostlog()
{
	$this->load->helper('directory');
	$map = array_reverse($this->_dir_map_sort(directory_map('../system_la/'.$this->config->config['pathtoapplication'].'/sess/post/')));
	$this->load->helper('file');
	if (count($map) > 0)
	{
		$data = array();
		$adms = $this->Mywarehouse_model->GetAdminList();	
		$tcolMap = array(
							0 => 'BCN',
							1 => 'Title',
							2 => 'SN',
							3 => 'POST',
							4 => 'Battery',
							5 => 'Charger',
							6 => 'HDD Status',
							7 => 'Problems',
							8 => 'Notes',
							9 => 'Status',
							10 => 'Status Notes',
							11 => 'Parts Needed',
							12 => 'Warranty'
											
						  );
						
		foreach ($map as $m)
		{
			
			$item = explode('_', $m);
			$guess = explode(' ',$item[2]);
			
			$stuff = read_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/sess/post/'.$m);
			echo $adms[$item[1]].' @ '.$item[0].' from '.str_replace('.txt', '' ,$item[2]);
			echo '<br>';
			
	
			if (trim($guess[0]) == 'Testing')
			{
				$info = explode('changes[0][]', $stuff);
				$row = str_replace('=', '', str_replace('&', '', $info[1]));
				$field = str_replace('=', '', str_replace('&', '', $info[2]));
				$from = str_replace('=', '', str_replace('&', '', $info[3]));
				$to = str_replace('=', '', str_replace('&', '', $info[4]));
				echo 'Row: '.$row.' - Field: <strong>'.$tcolMap[(int)$field].'</strong> - From: "'.$from.'" - To: "'.$to.'"';
			}
			else echo str_replace('changes[0][]', 'VAL', $stuff);
			echo '<br>-<br>';
		}
	}	
}
function _dir_map_sort($array)
{
    $dirs = array();
    $files = array();

    foreach ($array as $key => $val)
    {
        if (is_array($val)) // if is dir
        {
            // run dir array through function to sort subdirs and files
            // unless it's empty
            $dirs[$key] = (!empty($array)) ? dir_map_sort($val) : $val;
        }
        else
        {
            $files[$key] = $val;
        }
    }

    ksort($dirs); // sort by key (dir name)
    asort($files); // sort by value (file name)

    // put the sorted arrays back together
    // swap $dirs and $files if you'd rather have files listed first
    return array_merge($dirs, $files); 
}
function forcefind()
{
	$this->db->select('wid, channel, sold_id, listingid, sold_date');
$this->db->where('listingid', 13034);
	$e = $this->db->get('warehouse');
	if ($e->num_rows() > 0)
	{
		printcool($e->result_array());
	}
		
}
function forceprocess()
{
	
//
$ids = array(25420,25419,25418,25417,25416,25415,25414,25413,25410,25407,25406,25412,25411,25408,25405,25404,25403,25402,25401,25400,25399,25398,25397,25396,25395,25394,25393,25392,25391,25389);

foreach ($ids as $i)
{
$this->db->where('et_id', $i);
$e = $this->db->get('ebay_transactions');
if ($e->num_rows() > 0)
{
	$ebt = $e->row_array();
	$this->load->model('Myseller_model');
	$this->Myseller_model->AssignBCN($ebt , 1);
}
}

}

function testarray()
{
    $posted= $this->input->post('wid',true);
    $wids = array();
    if (is_array($posted) && count($posted) > 0) foreach($posted as $p)  if ((int)$p > 0)$wids[] = (int)$p;
    else $wids[] = (int)$posted;
    foreach($wids as $wid)
    {
        //whole salesbcnattach and bcnlistingattach

    }
}
}