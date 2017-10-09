

<table class="table table-bordered table-condensed no-margin">
  

    <?php
    
    foreach ($data as $data) {
        
        $this->db->select("status");
        $this->db->where("id", $data['status_id']);
        $result = $this->db->get("status")->result_array();
        ?>
        <tr>
            <td>
                <a href="<?php echo site_url('/quotes/status/' . $data['css_label']); ?>">

                    <?php
                    //echo $total['label'] . "(<font color = 'green'>" . $qtd . "</font>)";

                    echo $data['status'] . "(<font color = 'green'>" . $data['status_quantity'] . "</font>)";
                    ?>
                </a>
            </td></tr>
    <?php } ?>

</table>


