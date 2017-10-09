<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mycalendar extends Controller {
    var $startDay = 0;
    var $startMonth = 1;
    var $dayNames = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
    var $monthNames = array("January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December");
    var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	
	function Mycalendar()
	{
		parent::Controller();
		$this->_Start();
		$this->mysmarty->assign('area', 'COMM');
		if ($this->session->userdata['admin_id'] != '1' && $this->session->userdata['admin_id'] != 2) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		$this->load->model('Myproducts_model');
		$this->load->model('Myorders_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
	}
	
	function index()
	{	
		Redirect("/Mycalendar/Show");
	}
	function Mark($day = '',$month = '',$year = '',$mark = '')
	{
		if ((int)$mark == 0)
		{
			$this->db->where('c_day', (int)$day);
			$this->db->where('c_month', (int)$month);
			$this->db->where('c_year', (int)$year);
			$this->db->delete('comm_calendar');	
		}
		else 
		{
			$this->db->select("c_id");	
			$this->db->where('c_year', (int)$year);
			$this->db->where('c_month', (int)$month);
			$this->db->where('c_day', (int)$day);
			$this->query = $this->db->get('comm_calendar');
	
			if ($this->query->num_rows() > 0)  $this->db->update('comm_calendar', array('c_mark' => (int)$mark), array('c_day' => (int)$day, 'c_month' => (int)$month, 'c_year' => (int)$year));
			else 	$this->db->insert('comm_calendar', array('c_day' => (int)$day, 'c_month' => (int)$month, 'c_year' => (int)$year, 'c_mark' => (int)$mark));	
		}
	Redirect("Mycalendar/Show/".(int)$month.'/'.(int)$year);
		
	}
	function Pay($day = '',$month = '',$year = '',$pay = '')
	{

			$this->db->select("c_id");	
			$this->db->where('c_year', (int)$year);
			$this->db->where('c_month', (int)$month);
			$this->db->where('c_day', (int)$day);
			$this->query = $this->db->get('comm_calendar');	
			$this->db->update('comm_calendar', array('c_pay' => (int)$pay), array('c_day' => (int)$day, 'c_month' => (int)$month, 'c_year' => (int)$year));

	Redirect("Mycalendar/Show/".(int)$month.'/'.(int)$year);
		
	}
	function Show($month = '',$year = '')
	{	
		$d = getdate(time());
		if (((int)$month == 0) || ((int)$month > 12)) $month = $d["mon"];
		if ((int)$year == 0) $year = $d["year"];
	
		$this->db->select("c_id, c_day, c_mark, c_pay");	
		$this->db->order_by("c_day", "ASC");
		$this->db->where('c_year', (int)$year);
		$this->db->where('c_month', (int)$month);
		$this->query = $this->db->get('comm_calendar');
		
		if ($this->query->num_rows() > 0) {
			$this->countdays = 0;
			foreach ($this->query->result_array() as $k=>$v) 
				{
					if ($v['c_mark'] == 1 && $v['c_pay'] == 0) $this->countdays = $this->countdays+1;
					elseif ($v['c_mark'] == 2 && $v['c_pay'] == 0) $this->countdays = $this->countdays+0.5;

					$this->data[$v['c_day']] = array('mark' => $v['c_mark'], 'pay' => $v['c_pay'], 'counted' => $this->countdays);
				}
			$this->mysmarty->assign('count', $this->countdays);		
		}
		else $this->data = FALSE;
		$this->mysmarty->assign('days', $this->data);
		$this->mysmarty->assign('month', (int)$month);
		$this->mysmarty->assign('year', (int)$year);
		$this->mysmarty->assign('calendar', $this->_getMonthView($month, $year));
		
		$this->_MonthlyReports();
	
		$this->mysmarty->view('mycomm/mycomm_calendar.html');
	}
	function _MonthlyReports()
		{
			
			$lastyear = 2011;
			$lastmonth = 7;
		$list = $this->Myorders_model->ListReports();
		//printcool ($list);
		$outlettotal = 0;
		$webtotal = 0;		
		$montlytotal = 0;
		$web = array();
		$outlet = array();

		foreach ($list as $k => $v)
			{
				if ($v['buytype'] == '2' || $v['buytype'] == '4' || $v['buytype'] == '6' || $v['buytype'] == '7')
					{
						if ($v['endprice'] > 0)
						{
						$date = explode(' ', $v['time']);
						$date = explode('-', $date[0]);
						$month = $date[1];
						$year = $date[0];
						if ($year >= $lastyear && $month > $lastmonth)
						{
						$outlet[$year][$month][$v['oid']]['sum'] = (float)$v['endprice'];
						$outlettotal = $outlettotal + (float)$outlet[$year][$month][$v['oid']]['sum'];
						$monouttot[$year][$month][] = (float)$outlet[$year][$month][$v['oid']]['sum'];
						}
						}
						
					}
				elseif ($v['buytype'] == '5')
					{
						if ($v['complete'] == '1' && $v['endprice'] > 0) 
							{
								$date = explode(' ', $v['time']);
								$date = explode('-', $date[0]);
								$month = $date[1];
								$year = $date[0];
								if ($year >= $lastyear && $month > $lastmonth)
								{
								$web[$year][$month][$v['oid']]['oid_ref'] = $v['oid_ref'];
								$web[$year][$month][$v['oid']]['sum'] = (float)$v['endprice'];
								$webtotal = $webtotal + (float)$web[$year][$month][$v['oid']]['sum'];
								$monwebtot[$year][$month][] = (float)$web[$year][$month][$v['oid']]['sum'];
								}
							}
					}
				else 
					{
						
						if ($v['complete'] == '1' && $v['endprice'] > 0) 
							{
								$date = explode(' ', $v['time']);
								$date = explode('-', $date[0]);
								$month = $date[1];
								$year = $date[0];
								if ($year >= $lastyear && $month > $lastmonth)
								{
								if ($v['buytype'] == '1')
									{
										$web[$year][$month][$v['oid']]['sum'] = (float)$v['endprice'] + $v['endprice_delivery'];
										$webtotal = $webtotal + (float)$web[$year][$month][$v['oid']]['sum'];
										$monwebtot[$year][$month][] = (float)$web[$year][$month][$v['oid']]['sum'];
									}
								else 
									{
										$web[$year][$month][$v['oid']]['sum'] = (float)$v['endprice'];
										$webtotal = $webtotal + (float)$web[$year][$month][$v['oid']]['sum'];
										$monwebtot[$year][$month][] = (float)$web[$year][$month][$v['oid']]['sum'];
									}
								}
							}
					}				
			}
			if (isset($monwebtot)) foreach ($monwebtot as $mwtk => $mwtv)
				{
				foreach ($mwtv as $mwtmk => $mwtmv)
					{					
					$montlytotalweb[$mwtk][$mwtmk] = array_sum($mwtmv);	
					
					}
					
				}
			if (isset($monouttot)) foreach ($monouttot as $motk => $motv)
				{
				foreach ($motv as $motmk => $motmv)
					{
					$montlytotaloutlet[$motk][$motmk] = array_sum($motmv);						
					}
					
				}
			
			
			$this->mysmarty->assign('monthnow', date('m'));
			$this->mysmarty->assign('endday', $this->getLastDayOfMonth(date('m'), date("Y")));
			$this->mysmarty->assign('now', date('d'));			
			$this->mysmarty->assign('monthlytotalsoutlet', $montlytotaloutlet);
			$this->mysmarty->assign('monthlytotalsweb', $montlytotalweb);
			$this->mysmarty->assign('webtotal', $webtotal);
			$this->mysmarty->assign('outlettotal', $outlettotal);
			$this->mysmarty->assign('webtotalpercent', ($webtotal/100)*2);
			$this->mysmarty->assign('outlettotalpercent', $outlettotal/100);
			$this->mysmarty->assign('weblist', $web);
			$this->mysmarty->assign('outletlist', $outlet);	
			
			
		}


function getLastDayOfMonth($month, $year)
{
return idate('d', mktime(0, 0, 0, ($month + 1), 0, $year));
}

    function _getDayNames()
    {
        return $this->dayNames;        
    }
    function _setDayNames($names)
    {
        $this->dayNames = $names;
    }
    function _getMonthNames()
    {
        return $this->monthNames;
    }
    function _setMonthNames($names)
    {
        $this->monthNames = $names;
    }
      function _getStartDay()
    {
        return '0';
    }
    function _setStartDay($day)
    {
        $this->startDay = $day;
    }
    function _getStartMonth()
    {
        return $this->startMonth;
    }
    function _setStartMonth($month)
    {
        $this->startMonth = $month;
    }
    function _getCalendarLink($month, $year)
    {
		
        return (int)$month."/".(int)$year;
    }
    function _getDateLink($day, $month, $year)
    {
        return "";
    }
    function _getCurrentMonthView()
    {
        $d = getdate(time());
        return $this->_getMonthView($d["mon"], $d["year"]);
    }
    function _getCurrentYearView()
    {
        $d = getdate(time());
        return $this->_getYearView($d["year"]);
    }
    function _getMonthView($month, $year)
    {
        return $this->_getMonthHTML($month, $year);
    }
    function _getYearView($year)
    {
        return $this->_getYearHTML($year);
    }
    function _getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }
   
        $d = $this->daysInMonth[$month - 1];
   
        if ($month == 2)
        {
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }
    
        return $d;
    }
    function _getMonthHTML($m, $y, $showYear = 1)
    {
        $s = "";
        
        $a = $this->_adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];        
        
    	$daysInMonth = $this->_getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
    	$first = $date["wday"];
		$monthName = $this->monthNames[$month - 1];	 
    	
    	$prev = $this->_adjustDate($month - 1, $year);
    	$next = $this->_adjustDate($month + 1, $year);
    	
    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->_getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->_getCalendarLink($next[0], $next[1]);
    	}
    	else
    	{
    	    $prevMonth = "";
    	    $nextMonth = "";
    	}
    	
    	$header = $monthName . (($showYear > 0) ? " " . $year : "");
    	
    	$s .= "<table class=\"calendar\" >\n";
    	$s .= "<tr>\n";
    	$s .= "<th >" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"".base_url()."Mycalendar/Show/$prevMonth\">&lt;&lt;</a>")  . "</th>\n";
    	$s .= "<th colspan=\"5\">$header</th>\n"; 
    	$s .= "<th>" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"".base_url()."Mycalendar/Show/$nextMonth\">&gt;&gt;</a>")  . "</th>\n";
    	$s .= "</tr>\n";
    	
    	$s .= "<tr>\n";
				$s .= "<th>" . $this->dayNames[($this->startDay)%7] . "</th>\n";
    	$s .= "<th>" . $this->dayNames[($this->startDay+1)%7] . "</th>\n";
    	$s .= "<th>" . $this->dayNames[($this->startDay+2)%7] . "</th>\n";
    	$s .= "<th>" . $this->dayNames[($this->startDay+3)%7] . "</th>\n";
    	$s .= "<th>" . $this->dayNames[($this->startDay+4)%7] . "</th>\n";
    	$s .= "<th>" . $this->dayNames[($this->startDay+5)%7] . "</th>\n";
    	$s .= "<th>" . $this->dayNames[($this->startDay+6)%7] . "</th>\n"; 
    	$s .= "</tr>\n";    	
  
    	$d = $this->startDay + 1 - $first;
    	while ($d > 1)
    	{
    	    $d -= 7;
    	}
        $today = getdate(time());
    	
    	while ($d <= $daysInMonth)
    	{
    	    $s .= "<tr>\n";       
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
        	    $class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "calendarToday" : "calendar";
					if (($i == 0) || ($i == 6)) $s .= "<td class=\"calendarspecday\">";       
					else $s .= "<td valign='top'>";  
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
					
					
					$s .= '<div class="calendarday">';
					
    	            $link = $this->_getDateLink($d, $month, $year);
					
    	            $s .= (($link == "") ? $d : "<a href=\"$link\">$d</a>");
					
					$s .= '</div>';
					
					
					
					if ($this->session->userdata['admin_id'] == '1') $s .= '<br clear="all"><a href="'.base_url().'Mycalendar/Mark/'.$d.'/'.(int)$month.'/'.(int)$year.'/0">X</a> <a href="'.base_url().'Mycalendar/Mark/'.$d.'/'.(int)$month.'/'.(int)$year.'/1">1</a> <a href="'.base_url().'Mycalendar/Mark/'.$d.'/'.(int)$month.'/'.(int)$year.'/2">1/2</a> <a href="'.base_url().'Mycalendar/Mark/'.$d.'/'.(int)$month.'/'.(int)$year.'/3">N</a> <a href="'.base_url().'Mycalendar/Pay/'.$d.'/'.(int)$month.'/'.(int)$year.'/1">P</a> <a href="'.base_url().'Mycalendar/Pay/'.$d.'/'.(int)$month.'/'.(int)$year.'/0">xP</a>';
					
					if (isset($this->data[$d]) && $this->data[$d]['mark'] == 1) $s .= '<br><br><span style="font-size:16px; color:#000;">Full</span>';
					elseif (isset($this->data[$d]) && $this->data[$d]['mark'] == 2) $s .= '<br><br><span style="font-size:16px; color:#000;">Half</span>';
					elseif (isset($this->data[$d]) && $this->data[$d]['mark'] == 3) $s .= '<br><br><span style="font-size:16px; color:#F00;">No</span>';
					else {
						if (($i != 0) && ($i != 6)) $s .= '<br><br><span style="font-size:10px;">unmarked</span>';
					}
					if (isset($this->data[$d]) && $this->data[$d]['pay'] == 1) $s .= '<br><span style="font-size:12px; background:#ffc17f; color:#fff; padding:1px;">PAID</span>';
					if (isset($this->data[$d]) && (isset($this->data[$d]['counted']) && $this->data[$d]['counted'] != 0) && ($this->data[$d]['mark'] == 1 || $this->data[$d]['mark'] == 2 && $this->data[$d]['pay'] != 1)) $s .= '<br><span style="font-size:10px; color:green; font-weight:bolder;">'.$this->data[$d]['counted'].'</span>';
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
								
					
		
				$s .= "</td>\n";       
        	    $d++;
    	    }
    	    $s .= "</tr>\n";    
    	}
    	
    	$s .= "</table>\n";
    	
    	return $s;  	
    }
    function _getYearHTML($year)
    {
        $s = "";
    	$prev = $this->_getCalendarLink(0, $year - 1);
    	$next = $this->_getCalendarLink(0, $year + 1);
        
        $s .= "<table class=\"calendar\" border=\"0\" >\n";
        $s .= "<tr>";
    	$s .= "<td  valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\">&lt;&lt;</a>")  . "</td>\n";
        $s .= "<td class=\"calendarHeader\" valign=\"top\" align=\"center\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";
    	$s .= "<td  valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(0 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(1 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(2 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(3 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(4 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(5 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(6 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(7 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(8 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(9 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(10 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"calendar\" valign=\"top\">" . $this->_getMonthHTML(11 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "</table>\n";
        
        return $s;
    }
    function _adjustDate($month, $year)
    {
        $a = array();  
        $a[0] = $month;
        $a[1] = $year;
        
        while ($a[0] > 12)
        {
            $a[0] -= 12;
            $a[1]++;
        }
        
        while ($a[0] <= 0)
        {
            $a[0] += 12;
            $a[1]--;
        }
        
        return $a;
    }
    function _GoHome($id = '') {
		if ((int)$id == 0) { 
			Redirect("/".$this->go['ctr']);
			exit();
			}
}
	
function _Start() {
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);	
}
    
}