<?php
class Myebayrefunds extends Controller
{
	function Myebayrefunds()
	{
		parent::Controller();
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Refunds');
	}
	function index()
	{
		$this->db->orderby('er_id', 'DESC');
		$e = $this->db->get("ebay_refunds")->result_array();
		foreach ($e as $k => $v)
		{
			$e[$k]['responseHistory'] = unserialize($v['responseHistory']);
			if ($v['itemizedRefundDetail'] != '') $e[$k]['itemizedRefundDetail'] = unserialize($v['itemizedRefundDetail']);
		}
		$this->mysmarty->assign('refunds', $e);

		$this->mysmarty->view('myebay/refunds.html');
	}
}