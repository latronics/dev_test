<?php

$this->db->select("*");
$this->db->where("id", $id_store);
$stores_data = $this->db->get("ip_stores")->result_array();

echo "<font color = 'red'>Do you really want to delete </font><font color = 'blue'><b>" . $stores_data[0]['store_name'] . "</b></font><font color = 'red'> STORE?</font>";
?> <br><button onclick="validate_delete(1,<?php echo $id_store;?>)" class = "btn btn-success">Yes</button>&nbsp;<button onclick="validate_delete(0,<?php echo $id_store;?>)" class = "btn btn-danger">No</button>

<script>
    function validate_delete(yes_no,id_store){
        var decision = yes_no;
        var receive_id_store = id_store;
    
    $.post("<?php echo site_url('stores/validate_delete_store'); ?>", {
            decision: decision,
            receive_id_store: receive_id_store

        },
                function (data) {
                    //alert(data);
                    
                    location.reload();
                });
   }
</script>