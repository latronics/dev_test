
<div id="input">
    <table class="" border="0" align="center"><?php
        echo "<tr align = 'center'><td colspan='2' style='padding-top:10px;'>" . $this->pagination->create_links();
        echo "</td></tr>"
        ?>

        <tr align="center"><td><input name ="search_input" id ="search_input" type="text" style="padding-right:0px; border-radius: 6px; width:600px;" placeholder="Type Sku, Title or Bcn" onkeyup="send_input_text()"/></td><td style="padding-left: 10px; border-radius: 6px;"></td></tr></table></div>
<div id="content" class="table-content" >

    <div id ="show_parts"><div>
            <div id ="index_parts"><table class="table table-striped">

                    <thead>
                        <tr>
                            <th ><?php echo lang('product_sku'); ?></th>
                            <th style="width:100px;"><?php echo lang('bcn'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('product_name'); ?></th>
                            <th><?php echo lang('product_description'); ?></th>
                            <th><?php echo lang('product_price'); ?></th>

                        </tr>
                    </thead>

                    <tbody id = "result">
                        <?php foreach ($warehouse_data as $products) { ?>
                            <tr>
                                
                                <td><?php echo $products['psku']; ?></td>
                                <td><?php echo $products['bcn']; ?></td>
                                <td><?php echo $products['status']; ?></td>
                                <td><?php echo $products['title']; ?></td>
                                <td><?php echo $products['notes']; ?></td>
                                 
                                <td><?php echo "$" . number_format($products['cost'], 2); ?></td>


                            </tr>




                        <?php } ?>



                    </tbody>

                </table></div>       


        </div>
        <script>

            function send_input_text() {
                var search_input = $("#search_input").val();

                $.post("<?php echo site_url('parts/query'); ?>", {
                    input_text: search_input
                },
                        function (data) {
                            //alert(data);

                            $("#show_parts").html(data);
                            $("#index_parts").hide();
                        });

            }


        </script>
