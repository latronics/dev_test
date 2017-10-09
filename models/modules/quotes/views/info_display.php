<?php
//VARIABLE DECLARATIONS
$count = 0;
$x = 0;
$y = 0;

$array_aux = array(
    '0' => 'Diagnosing',
    '1' => 'Waiting on approval',
    '2' => 'Ordered Parts',
    '3' => 'Repairing',
    '4' => 'Repair Completed',
    '5' => 'Accepted by client',
    '6' => 'New Order',
    '7' => 'Waiting for package',
    '8' => 'Repair denied',
    '9' => 'Returned to shop'
);

//GET USER STORE
$this->db->select("*");
$this->db->where("user_id", $this->session->userdata('user_id'));
$user_store = $this->db->get("ip_users")->result_array();


//CLEANING 365admin_valid_session TRASH
$this->db->select("*");
$clean_trash = $this->db->get("365admin_valid_session")->result_array();

if ($clean_trash[0]['session_start'] != "") {
    $this->db->where("session_start", $clean_trash[0]['session_start']);
    $this->db->delete("365admin_valid_session");
}

//GET NUMBER OF STATUS
$this->db->select("*");
$rows_status = $this->db->get("status")->num_rows();


//GET THE YEARS QUANTITY TO SHOW THE TICKETS
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'years_ticket'");
$get_years_ticket = $this->db->get();
$years_ticket = $get_years_ticket->result_array();

//GET THE DAYS QUANTITY TO SHOW THE TICKETS
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'days_ticket'");
$get_days_ticket = $this->db->get();
$days_ticket = $get_days_ticket->result_array();

//GET THE DAYS QUANTITY TO TURN TICKETS URGENT(GENERAL)
$this->db->select("*");
$this->db->from("ip_settings");
$this->db->where("setting_key = 'days_urgent'");
$get_days_urgent = $this->db->get();
$days_urgent = $get_days_urgent->result_array();


//CREATE THE URGENT ARRAY TO UPDATE THE TABLE IP_QUOTES SETTING
$urgent = array(
    'urgent' => 2
);
//CREATE THE RETURN URGENT ARRAY TO UPDATE THE TABLE IP_QUOTES SETTING
$return_urgent = array(
    'urgent' => 0
);
$middle_urgent = array(
    'urgent' => 1
);

//CHECK WHAT STATUS PARAMETERS ARE SETTED ON SETTINGS AND ADJUST THE DATA
$this->db->select("*");
$this->db->where("setting_key", "show_status_ticket");
$status_ipsettings = $this->db->get("ip_settings")->result_array();

$get_status = str_replace("show_status_ticket[]", "", $status_ipsettings[0]['setting_value']);
$get_status2 = str_replace("=", "", $get_status);
$get_status3 = str_replace("+", " ", $get_status2);
$status_array = explode("&", $get_status3);
$status_array2 = explode("&", $get_status3);











//GET THE TICKETS INFORMATION USING THE SETTINGS AUTOMATIC   

$date_full = date('Y-m-d');
$date_month = $days_ticket[0]['setting_value'];
$date_year -= $years_ticket[0]['setting_value'];
$date_month_final = date('Y-m-d', strtotime("-$date_month days"));




$this->db->order_by("urgent", "ASC");
$this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
$this->db->join("status", "status.id = ip_quotes.quote_status_id");


if ($user_store[0]['user_store'] != 1) {

    foreach ($status_array as $status_array) {
        if ($display_data['all'] == 1) {
            $this->db->or_where("ip_quotes.quote_date_created > '" . $display_data['date_from'] . "'");
        } else {
            $this->db->or_where("ip_quotes.quote_date_created between '" . $display_data['date_from'] . "' and '" . $display_data['date_to'] . "'");
        }

        $this->db->where("ip_clients.client_name like", "%" . $display_data['input_data'] . "%");
        $this->db->where("status.status", $status_array);
        $this->db->where("ip_quotes.store", $user_store[0]['user_store']);
    }
} else
if ($user_store[0]['user_store'] == 1) {
    foreach ($status_array as $status_array) {
        if ($display_data['all'] == 1) {
            $this->db->or_where("ip_quotes.quote_date_created > '" . $display_data['date_from'] . "'");
        } else {
            if ($display_data['numeric'] == 0) {
                $this->db->or_where("ip_quotes.quote_date_created between '" . $display_data['date_from'] . "' and '" . $display_data['date_to'] . "'");
            }
        }
        if ($display_data['numeric'] == 1) {
            $this->db->or_where("ip_quotes.quote_number like ", "" . $display_data['input_data'] . "%");
        } else {
            $this->db->where("ip_clients.client_name like", "%" . $display_data['input_data'] . "%");
        }
        $this->db->where("status.status", $status_array);
    }
}


$get_data_ticket = $this->db->get("ip_quotes");
$data_ticket = $get_data_ticket->result_array();
$ticket_rows = $get_data_ticket->num_rows();



//GET PRIORITY TICKETS QUANTITY
$this->db->select("*");
$this->db->order_by("urgent", "ASC");
$this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
$this->db->join("status", "status.id = ip_quotes.quote_status_id");
if ($user_store[0]['user_store'] != 1) {

    foreach ($status_array2 as $status_array2) {
        if ($display_data['all'] == 1) {
            $this->db->or_where("ip_quotes.quote_date_created > '" . $display_data['date_from'] . "'");
        } else {
            if ($display_data['numeric'] == 1) {
                $this->db->where("ip_quotes.quote_number like ", "" . $display_data['input_data'] . "%");
            } else {
                $this->db->where("ip_clients.client_name like", "%" . $display_data['input_data'] . "%");
                $this->db->where("ip_quotes.quote_date_created between '" . $display_data['date_from'] . "' and '" . $display_data['date_to'] . "'");
            }
        }


        $this->db->where("ip_quotes.store", $user_store[0]['user_store']);
        $this->db->where("status.status", $status_array2);
        $this->db->where("ip_quotes.urgent = 2");
    }
} else if ($user_store[0]['user_store'] == 1) {
    foreach ($status_array2 as $status_array2) {
        if ($display_data['all'] == 1) {
            $this->db->or_where("ip_quotes.quote_date_created > '" . $display_data['date_from'] . "'");
        } else {
            if ($display_data['numeric'] == 0) {
                $this->db->or_where("ip_clients.client_name like", "%" . $display_data['input_data'] . "%");
                $this->db->where("ip_quotes.quote_date_created between '" . $display_data['date_from'] . "' and '" . $display_data['date_to'] . "'");
            }
            else if ($display_data['numeric'] == 1) {
            $this->db->or_where("ip_quotes.quote_number like ", "" . $display_data['input_data'] . "%");
        } 
        }
        
        $this->db->where("status.status", $status_array2);
        $this->db->where("ip_quotes.urgent = 2");
    }
}




$urgent_row = $this->db->get("ip_quotes")->num_rows();


//SET THE CARACTERE LIMIT TO DO NOT BROKE TICKETS TABLE STRUCTURE
$caractere_limit = 16;
?>
<div class="portlet" >
    <div class="portlet-header" style="width:1210px;">
        <div class="col-xs-12">
            <div id="panel-quote-overview" class="panel panel-default" >
                <div class="panel-heading" style="cursor:move; ">

                    <b><i class="fa fa-bar-chart-display fa-margin"></i><?php echo lang('information_display'); ?> | </b><?php
if ($data_ticket != NULL) {
    echo "Open Tickets(<font color = 'green'><b>" . $ticket_rows . "</b></font>) -    Urgent Tickets(<font color = 'red'><b>" . $urgent_row . "</b></font>)";
}
?>
                    <span class="pull-right text-muted"></span>
                </div>
                <div class="col-xs-12 col-md-6  smart_panel" style = "margin-left:-16px; width:1195px; height: 330px; border-radius:0px;">

                    <div id="panel-quote-overview" class="panel panel-default" style = "width:auto; height: auto;">



                        <table class="smart_panel">
                            <tr>

<?php
$x_aux = 0;
$ $i = 0;
$j = $ticket_rows;
if ($data_ticket != NULL) {
    while (($i <= $j) || ($i >= $J)) {

        $date_limit = date('Y-m-d', strtotime($data_ticket[$j - 1]['quote_date_created'] . " + " . $data_ticket[$j - 1]['days_urgent'] . " days"));
        ?>


                                        <?php
                                        if ($i == 7) {
                                            echo"</tr><tr>";
                                            $i = 0;
                                        }
                                        $calc_days = $data_ticket[$j - 1]['days_urgent'] / 2;


                                        $between_date = date_diff(date_create(date('Y-m-d')), date_create($date_limit))->format('%d');
                                        //echo $between_date;


                                        if ($date_limit >= date('Y-m-d')) {
                                            $this->db->where("quote_id", $data_ticket[$j - 1]['quote_id']);
                                            $this->db->update('ip_quotes', $return_urgent);
                                        } /* else if ($between_date == $calc_days) {
                                          $this->db->where("quote_id", $data_ticket[$j - 1]['quote_id']);
                                          $this->db->update('ip_quotes', $middle_urgent);
                                          } */ else if ($date_limit < date('Y-m-d')) {
                                            $this->db->where("quote_id", $data_ticket[$j - 1]['quote_id']);
                                            $this->db->update('ip_quotes', $urgent);
                                        }
                                        ?>
                                        <td style="background-image:url(../../../../assets/default/img/<?php
                                        if ($data_ticket[$j - 1]['urgent'] == 2) {


                                            echo "paper_urgent.png";
                                        } else if ($data_ticket[$j - 1]['urgent'] == 1) {
                                            echo "paper_yellow.png";
                                        } else {

                                            echo "paper_white.png";
                                        }
                                        ?>);<?php ?>" >
                                            <font style="font-size:24px; font-weight: bold;"><?php echo anchor('quotes/view/' . $data_ticket[$j - 1]['quote_id'], ($data_ticket[$j - 1]['quote_number'] ? $data_ticket[$j - 1]['quote_number'] : $data_ticket[$j - 1]['quote_id'])) . ""; ?></font>
                                            <br><?php
                                            if (strlen($data_ticket[$j - 1]['client_name']) < $caractere_limit) {
                                                echo $data_ticket[$j - 1]['client_name'];
                                            } else if (strlen($data_ticket[$j - 1]['client_email']) > $caractere_limit) {
                                                echo substr($data_ticket[$j - 1]['client_name'], 0, $caractere_limit) . '...';
                                            }
                                            ?>
                                            <br><?php
                                            if (($data_ticket[$j - 1]['client_email'] != "") && (strlen($data_ticket[$j - 1]['client_email']) < $caractere_limit)) {
                                                echo $data_ticket[$j - 1]['client_email'];
                                            }
                                            ?>
                                            <?php
                                            if (strlen($data_ticket[$j - 1]['client_email']) > $caractere_limit) {

                                                echo substr($data_ticket[$j - 1]['client_email'], 0, $caractere_limit) . '...';
                                            }
                                            ?>
                                            <br><?php echo $data_ticket[$j - 1]['client_phone']; ?>
                                            <br><?php echo "<p class='" . $data_ticket[$j - 1]['css_label'] . "'>" . $data_ticket[$j - 1]['status'] . "</p>"; ?>

                                        </td>






        <?php
        $i++;
        $j--;
        if ($j == 0) {
            break;
        }
    }
}
?>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
            <div class="portlet-content" >
            </div>
        </div>



    </div></div>     
