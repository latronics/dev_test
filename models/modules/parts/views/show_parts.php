<div class="table-responsive">

        <table class="table table-striped">

            <thead>
                <tr>
                     <th><?php echo lang('product_sku'); ?></th>
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

        </table>
    </div>