<?php

$this->db->select("*");

$stores = $this->db->get("ip_stores")->result_array();
$stores_rows = $this->db->get("ip_stores")->num_rows();
if ($stores_rows == 0) {
    echo "<tr style='text-align: left; '><td colspan = '6' style = 'padding-left: 10px;'>No stores registered.";
}

foreach ($stores as $stores) {

if($stores['id'] != 1){
    echo "<tr style='text-align: left; '><td style = 'padding-left: 10px;'>" . $stores['store_name'];
    echo "</td><td style = 'padding-left: 10px;'>" . $stores['store_address'];
    echo "</td><td style = 'padding-left: 10px;'>" . $stores['store_phone'];
    echo "</td><td style = 'padding-left: 10px;'>";
    if ($stores['store_tax'] == 1) {
        echo "Yes";
    } else {
        echo "No";
    }
    echo "</td><td style = 'padding-left: 10px;'>";
    if ($stores['tax_percent'] != 0) {
        echo $stores['tax_percent'] . "%";
    }
    echo "</td><td style = 'padding-left: 10px;'>"; if($stores['default'] == 1) {echo "Yes";} else {echo "No";}
    echo "</td><td style = 'padding-left: 10px;'><a href = '#' onclick='edit_function(" . $stores['id'] . ")'><img src = '../../../../assets/default/img/edit_icon.png' title ='Edit' style = 'width: 20px; height:20px;' /></a>";
    echo "</td><td style = 'padding-left: 10px;'><a href = '#' onclick='delete_function(" . $stores['id'] . ")'><img src = '../../../../assets/default/img/delete_icon.png' title ='Delete' style = 'width: 20px; height:20px;' /></a></td></tr>";
}
}
?> 