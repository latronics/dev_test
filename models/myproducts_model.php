<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myproducts_model extends Model 
{
    function Myproducts_model()
    {
        parent::Model();
    }
function RecordAction($eid = 0, $ebayid = 0, $datafrom = '', $datato = '', $adminid = 0, $transid = 0, $ctrl = '')
{
	$this->db->insert('ebay_actionlog',array('e_id' => $eid, 'ebay_id' => $ebayid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom, 'datato' => $datato, 'admin_id' => $adminid, 'trans_id' => $transid, 'ctrl' => $ctrl));
}
function GetWarehouse($search, $sort)
	{
		$this->db->select("p.p_id, p.p_title, p.p_type, p.p_quantity, p.p_pendquant, p.p_visibility, p.p_cat, c.p_cattitle, c.p_catid");	
		$this->db->where('p.p_cat = c.p_catid');
		$this->db->where('p.p_type', 0);
		if ($search != '') $this->db->like('p.p_title', $search);		
		switch ($sort) {
				case 'By_Id_Ascending':
				$this->db->order_by("p.p_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("p.p_id", "DESC");				
			break;
				case 'By_Title_Ascending':
				$this->db->order_by("p.p_title", "ASC");				
			break;
				case 'By_Title_Descending':
				$this->db->order_by("p.p_title", "DESC");				
			break;
				case 'By_Type_Ascending':
				$this->db->order_by("p.p_type", "ASC");				
			break;
				case 'By_Type_Descending':
				$this->db->order_by("p.p_type", "DESC");				
			break;
				case 'By_Visible_Ascending':
				$this->db->order_by("p.p_visibility", "ASC");				
			break;
				case 'By_Visible_Descending':
				$this->db->order_by("p.p_visibility", "DESC");				
			break;	
				case 'By_Category_Ascending':
				$this->db->order_by("c.p_cattitle", "ASC");				
			break;
				case 'By_Category_Descending':
				$this->db->order_by("c.p_cattitle", "DESC");
			break;	
				case 'By_Pending_Descending':
				$this->db->order_by("p.p_pendquant", "DESC");	
			break;
				case 'By_Pending_Ascending':
				$this->db->order_by("p.p_pendquant", "ASC");
			break;	
				case 'By_Quantity_Descending':
				$this->db->order_by("p.p_quantity", "DESC");	
			break;
				case 'By_Quantity_Ascending':
				default:
				$this->db->order_by("p.p_quantity", "ASC");
			break;
			}
		$query = $this->db->get('products AS p, product_categories AS c');
		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}	
		
	}
function GetWeight()
	{
		$this->db->select("p_id, p_title, p_weight, p_lbs, p_oz, p_origweight, p_visibility");	
		$this->db->where('p_cat != 34');
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}		
		
	}

function CheckWeightZero()
	{
		
		$weightzero = 0;
		$pricezero = 0;
		$quantityzero = 0;
		
		$this->db->select("p_id");	
		$this->db->where('p_lbs' , 0);
		$this->db->where('p_oz', 0);	
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			$weightzero = 1;
			}
			
		$this->db->select("p_id");	
		$this->db->where('p_price' , 0);
		$this->db->where('p_type !=', 2);	
		$query1 = $this->db->get('products');

		if ($query1->num_rows() > 0) 
			{
			$pricezero = 1;
			}
			
		$this->db->select("p_id");	
		$this->db->where('p_quantity' , 0);
		$this->db->where('p_visibility' , 1);
		$this->db->where('p_type', 0);	
		$query2 = $this->db->get('products');

		if ($query2->num_rows() > 0) 
			{
			$quantityzero = 1;
			}
			
		return array(
					 'weightzero' => $weightzero,
					 'pricezero' => $pricezero,
					 'quantityzero' => $quantityzero				 
					 );
	}

function GetVizZero()
	{
		
		$this->db->select("p_id, p_title, p_quantity, p_visibility");
		$this->db->order_by("p_quantity", "DESC");
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
				return $query->result_array();
			}
		
	}
function GetZero()
	{
		$result = array(
					 'weightzero' => FALSE,
					 'pricezero' => FALSE,
					 'quantityzero' => FALSE
			);
		
		$this->db->select("p_id, p_title, p_lbs, p_oz, p_visibility");	
		$this->db->where('p_lbs' , 0);
		$this->db->where('p_oz', 0);	
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			$result['weightzero'] = $query->result_array();
			}
			
		
		$this->db->select("p_id, p_title, p_price, p_visibility");	
		$this->db->where('p_price' , 0);
		$this->db->where('p_type !=', 2);	
		$query2 = $this->db->get('products');

		if ($query2->num_rows() > 0) 
			{
			$result['pricezero'] = $query2->result_array();
			}
		
		$this->db->select("p_id, p_title, p_quantity, p_visibility");	
		$this->db->where('p_quantity' , 0);
		$this->db->where('p_visibility' , 1);
		$this->db->where('p_type', 0);	
		$query3 = $this->db->get('products');

		if ($query3->num_rows() > 0) 
			{
			$result['quantityzero'] = $query3->result_array();
			}
		
		return $result;
			
	}
function AllVisible($cat)
	{
		$this->db->update('products', array('p_visibility' => 1), array('p_cat' => (int)$cat));
	}
	
function AllInvisible($cat)
	{
		$this->db->update('products', array('p_visibility' => 0), array('p_cat' => (int)$cat));
	}

function GetAllToSef()
	{
		$this->db->select("p_id, p_title, p_sef");	
		$this->db->where('p_cat', '36');
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}		
		
	}
function GetAllPriceData()
	{
		$this->db->select("p_id, p_price, p_theirprice");	
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}		
		
	}	
function UpdateSef($data)
	{
		$this->db->update('products', array('p_sef' => $data['p_sef']), array('p_id' => (int)$data['p_id']));
	}
function UpdateProfit($data)
	{
		$this->db->update('products', array('p_profit' => $data['p_profit']), array('p_id' => (int)$data['p_id']));
	}
function Search($string)
	{
		$this->db->select("p_id, p_cat, p_title, p_type, p_order, p_visibility, p_top, p_new, p_availability, p_img1");		
		$this->db->like('p_title', $string);
		$this->db->order_by("p_cat", "ASC");
		$this->db->order_by("p_order", "ASC");	
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}	
		
	}
function ListItems($cat = '', $sortby = '')
	{	
		$this->cat = (int)$cat;
		$this->db->select("p_id, p_type, p_cat, p_sef, p_title, p_order, p_visibility, p_top, p_new, p_availability, p_img1");		
		if ($this->cat > 0) $this->db->where('p_cat', $this->cat);	
		$this->_SortBy($sortby);
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}	
	}
function ListXMLItems($cat = '')
	{	
		$sortby = '';
		$this->cat = (int)$cat;
		$this->db->select("p_id, p_cat, p_sef, p_price, p_desc, p_title, p_order, p_visibility, p_top, p_new, p_availability, p_img1, p_condition");		
		if ($this->cat > 0) $this->db->where('p_cat', $this->cat);	
		$this->db->where('p_visibility', '1');	
		$this->_SortBy($sortby);
		$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}	
	}
function GetAllAmmounts() {
	$this->db->select("p_id, p_cat");	
	$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			foreach ($query->result_array() as $key =>$value) {
			if (isset($this->nums[$value['p_cat']])) $this->nums[$value['p_cat']]++;
			else $this->nums[$value['p_cat']] = 1;
			}
			return $this->nums;
			}
	
	}
function GetPCATFromId($id = '')
	{
	$this->db->select("p_cat");	
	$this->db->where('p_id', (int)$id);
	$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			return $result['p_cat'];
			}
		
	}
function GetItem($id)
	{
		$this->db->where('p_id', (int)$id);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			}
		return $result;
	}	
	
function GetMaxOrder($by)
	{
		$this->db->select_max('p_order');
		$this->db->where('p_cat', (int)$by);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			}

		$result['p_order']++;
		return $result;
	}
function Delete($id)
	{		
		$cat = $this->GetPCATFromId((int)$id);
		$this->db->where('p_id', (int)$id);		
		$this->db->delete('products'); 
		
		$this->DeleteAssocProducts((int)$id);
		return $cat;
	}
function MakeVisible($id)
	{
		$this->db->update('products', array('p_visibility' => 1), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function MakeNotVisible($id)
	{
		$this->db->update('products', array('p_visibility' => 0), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function MakeTop($id)
	{
		$this->db->update('products', array('p_top' => 1), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function UnTop($id)
	{
		$this->db->update('products', array('p_top' => 0), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function MakeCatTop($id)
	{
		$this->db->update('product_categories', array('p_top' => 1), array('p_catid' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function UnCatTop($id)
	{
		$this->db->update('product_categories', array('p_top' => 0), array('p_catid' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function MakeCatVis($id)
	{
		$this->db->update('product_categories', array('p_vis' => 1), array('p_catid' => (int)$id));
	}
function UnCatVis($id)
	{
		$this->db->update('product_categories', array('p_vis' => 0), array('p_catid' => (int)$id));		
	}
function AllZeroCatInvis($catid)
	{
		$this->db->select('p_catid, p_parent');
		$this->db->where('p_parent', (int)$catid);
		$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{	
				$this->db->distinct();
				$this->db->select("p_cat");
				$squery = $this->db->get('products');
				if ($squery->num_rows() > 0) 
					{
						foreach ($squery->result_array() as $pv) 
							{
								$cat[$pv['p_cat']] = $pv;
							}
					}
			
			foreach ($query->result_array() as $key =>$value) {
			if (!isset($cat[$value['p_catid']]) && $value['p_parent'] > 0) $this->UnCatVis($value['p_catid']);
			
				}
			}
		
	}
function MakeNew($id)
	{
		$this->db->update('products', array('p_new' => 1), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function UnNew($id)
	{
		$this->db->update('products', array('p_new' => 0), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function MakeInStock($id)
	{
		$this->db->update('products', array('p_availability' => 1), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function MakeOutStock($id)
	{
		$this->db->update('products', array('p_availability' => 0), array('p_id' => (int)$id));
		return $this->GetPCATFromId((int)$id);
	}
function Update($id, $data)
	{
		$this->db->update('products', $data, array('p_id' => (int)$id));
	}
function Insert($data)
	{
		$this->db->insert('products', $data);
		return $this->db->insert_id();
	}
function ChangeOrder($id, $order)
	{			
		$this->db->update('products', array('p_order' => (int)$order), array('p_id' => (int)$id));		
	}
function ChangeQuantity($id, $quantity, $pending)
	{			
		$this->db->update('products', array('p_quantity' => (int)$quantity, 'p_pendquant' => (int)$pending), array('p_id' => (int)$id));		
	}	
function ChangeCategory($id, $pcat)
	{			
		$this->db->update('products', array('p_cat' => (int)$pcat), array('p_id' => (int)$id));		
	}
	
function ReOrder($by, $sortby = '')
	{	
		$this->by = (int)$by;
	    
		if ($this->by > 0) 
		{	
		$this->db->select('p_id, p_order');
		$this->db->where('p_cat', $this->by);
		
		$this->_SortBy($sortby);
		
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{	
			$this->db_data = $query->result_array();
			$roll = 1;			
			foreach ($this->db_data as $udb) 
				{					
				$this->db->update('products', array('p_order' => $roll), array('p_id' => (int)$udb['p_id']));
				$roll++;
				}
			}
		}
	}
function ReOrderCat()
	{	
	
	 $cats = $this->GetAllCategories();
	 
	 $rollc1 = 1;
	 foreach ($cats[0] as $c1)
	 {
			//echo $rollc1.'_'.$c1['p_catid'].'---    ';
			$this->db->update('product_categories', array('p_order' => $rollc1), array('p_catid' => (int)$c1['p_catid']));
			$rollc1++;
			
			if (isset($cats[$c1['p_catid']]))
				{
					$rollc2 = 1;
					foreach ($cats[$c1['p_catid']]  as $c2)
					{
						//echo $rollc2.'_'.$c2['p_catid'].'---    ';
						$this->db->update('product_categories', array('p_order' => $rollc2), array('p_catid' => (int)$c2['p_catid']));
						$rollc2++;	
					}				
				
					if (isset($cats[$c2['p_catid']]))
					{
						$rollc3 = 1;
						foreach ($cats[$c2['p_catid']]  as $c3)
						{
							//echo $rollc3.'_'.$c3['p_catid'].'---    ';
							$this->db->update('product_categories', array('p_order' => $rollc3), array('p_catid' => (int)$c3['p_catid']));
							$rollc3++;	
						}						
							if (isset($cats[$c3['p_catid']]))
							{
								$rollc4 = 1;
								foreach ($cats[$c3['p_catid']]  as $c4)
								{
									//echo $rollc4.'_'.$c4['p_catid'].'---    ';
									$this->db->update('product_categories', array('p_order' => $rollc4), array('p_catid' => (int)$c4['p_catid']));
									$rollc4++;	
								}				
							}
					}
				}		
	 }


}

function GetAllCategories() 
	{
	$this->db->order_by("p_order", "ASC");
	$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{

			foreach ($query->result_array() as $key =>$value) {
			$this->allcats[$value['p_parent']][$value['p_catid']] = $value;			
			}
			return $this->allcats;
			}
	}
function GetAllListCategories() 
	{
	$this->db->order_by("p_catid", "ASC");
	$this->db->order_by("p_order", "ASC");
	$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{

			foreach ($query->result_array() as $key =>$value) {
			$this->allcats[0][$value['p_catid']] = $value;			
			}
			return $this->allcats;
			}
	}
function GetCategory($id)
	{
		$this->db->where('p_catid', (int)$id);
		$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			return $result;
			}
	}
function CheckCategorySefExists($sef, $id = '')
	{
		$this->db->where('p_sef', $sef);
		$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			if ($result['p_catid'] != (int)$id) return $result;
			}
	}
function UpdateCategory($id, $data)
	{
		$this->db->update('product_categories', $data, array('p_catid' => (int)$id));		
	}
function InsertCategory($data)
	{
		$this->db->insert('product_categories', $data);
		return $this->db->insert_id();
	}
function DeleteCategory($id)
	{	
		$this->db->where('p_catid', (int)$id);
		$result = $this->db->delete('product_categories'); 
		return $result;
	}
	
function UpdateBodyCategory($id, $body, $caption, $cols)
	{
		$this->db->update('product_categories', array('p_body' => $body, 'p_caption' => $caption, 'p_columns' => $cols), array('p_catid' => (int)$id));		
	}
function GetBodyCategory($id)
	{
		$this->db->select('p_body, p_cattitle, p_caption, p_columns');
		$this->db->where('p_catid', (int)$id);
		$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{
			return $query->row_array();
			}
	}
function GetOldImage($id) 
	{	
		$this->db->select('p_img');
		$this->db->where('p_catid', (int)$id);
		$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			return $result['p_img'];
			}
		
	}
function DeleteoldImage($id) 
	{	
		$this->db->select('p_img');
		$this->db->where('p_catid', (int)$id);
		$query = $this->db->get('product_categories');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();			
			$this->data = array ('p_img' => '');	
			$this->db->update('product_categories', $this->data, array('p_catid' => (int)$id));			
			return $result['p_img'];
		}
	}
function GetOldProductImage($id, $place) 
	{	
		$this->db->select('p_img'.(int)$place);
		$this->db->where('p_id', (int)$id);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			return $result['p_img'.(int)$place];
			}
	}
function GetOldProductImageAd($id) 
	{	
		$this->db->select('p_ad');
		$this->db->where('p_id', (int)$id);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			return $result['p_ad'];
			}
	}
	
function DeleteProductImage($id, $place) 
	{	
		$this->db->select('p_img'.(int)$place);
		$this->db->where('p_id', (int)$id);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();			
			$this->data = array ('p_img'.(int)$place => '');	
			$this->db->update('products', $this->data, array('p_id' => (int)$id));			
			return $result['p_img'.(int)$place];
		}
	}
function DeleteAdImage($id) 
	{	
		$this->db->select('p_ad');
		$this->db->where('p_id', (int)$id);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();			
			$this->data = array ('p_ad' => '');	
			$this->db->update('products', $this->data, array('p_id' => (int)$id));			
			return $result['p_ad'];
		}
	}
function CheckProductSefExists($sef, $id = '')
	{
		$this->db->where('p_sef', $sef);
		$query = $this->db->get('products');
		if ($query->num_rows() > 0) 
			{
			$result = $query->row_array();
			if ($result['p_id'] != (int)$id) return $result;
			}
	}
	
function GetTopSpecialsAds () 
	{
			$this->db->select('p_ad');
			$this->db->where('p_cat', '34');
			$this->db->where('p_visibility', '1');
			$this->db->where('p_top', '1');
			$this->db->limit(4);
			$this->db->order_by("p_order", "ASC");
			$this->pquery = $this->db->get('products');
			if ($this->pquery->num_rows() > 0) 
				{
					return $this->pquery->result_array();		
				}
	
	}
	
	
function CountProducts() 
	{
	
				$this->db->select("spid, sid");	
				$query = $this->db->get('product_assoc');
				if ($query->num_rows() > 0) 
					{
						foreach ($query->result_array() as $key =>$value) 
						{
						if (isset($this->nums[$value['sid']])) $this->nums[$value['sid']]++;
						else $this->nums[$value['sid']] = 1;
						}
					
					return $this->nums;
					}
}
function AddProducts($pid, $sid)
	{
	$data['p_id'] = (int)$pid;
	$data['sid'] = (int)$sid;
	$this->db->insert('product_assoc', $data);		
	}

function DeleteProducts($spid)
	{
		$this->db->where('spid', (int)$spid);
		$this->db->delete('product_assoc'); 
	}

function DeleteAssocProducts($pid)
	{
		$this->db->where('sid', (int)$pid);
		$this->db->delete('product_assoc'); 
	}
	
function ListProducts($sid) {
	
	$this->db->select("s.spid, p.p_id, p.p_title");	
	$this->db->order_by("p.p_order", "ASC");
	$this->db->where('s.p_id = p.p_id');
	$this->db->where('s.sid', (int)$sid);
		$query = $this->db->get('product_assoc AS s, products AS p');

		if ($query->num_rows() > 0) 
			{
			foreach ($query->result_array() as $key => $value) {
				$this->dataset[$value['p_id']] = $value;				
				}
			return $this->dataset;				
			}	
	}	
	
function ListAllProducts() {	
	$this->db->select("p_id, p_title");	
	$this->db->order_by("p_title", "ASC");
	$query = $this->db->get('products');

		if ($query->num_rows() > 0) 
			{
			return $query->result_array();
			}	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
// Helpful functions // 
function _SortBy($sortby = '') 
	{
	switch ($sortby) {
				case 'By_Id_Ascending':
				$this->db->order_by("p_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("p_id", "DESC");				
			break;
				case 'By_Title_Ascending':
				$this->db->order_by("p_title", "ASC");				
			break;
				case 'By_Title_Descending':
				$this->db->order_by("p_title", "DESC");				
			break;
				case 'By_Type_Ascending':
				$this->db->order_by("p_type", "ASC");				
			break;
				case 'By_Type_Descending':
				$this->db->order_by("p_type", "DESC");				
			break;
				case 'By_Visible_Ascending':
				$this->db->order_by("p_visibility", "ASC");				
			break;
				case 'By_Visible_Descending':
				$this->db->order_by("p_visibility", "DESC");				
			break;
				case 'By_Top_Ascending':
				$this->db->order_by("p_top", "ASC");				
			break;
				case 'By_Top_Descending':
				$this->db->order_by("p_top", "DESC");				
			break;
				case 'By_New_Ascending':
				$this->db->order_by("p_new", "ASC");				
			break;
				case 'By_New_Descending':
				$this->db->order_by("p_new", "DESC");				
			break;
				case 'By_Category_Ascending':
				$this->db->order_by("p_cat", "ASC");				
			break;
				case 'By_Category_Descending':
				$this->db->order_by("p_cat", "DESC");				
			break;
				case 'By_Order_Descending':
				$this->db->order_by("p_order", "DESC");	
			break;
				case 'By_Order_Ascending':
				default:
				$this->db->order_by("p_order", "ASC");	
			break;
			}
	}



}
?>