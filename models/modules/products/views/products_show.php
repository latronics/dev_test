

<div class="pull-right" id = "show_skip_services">
    <?php  echo pager(site_url('products/index'), 'mdl_products'); ?>
</div>
<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
                <tr>
                    <th><?php echo lang('family'); ?></th>
                    <th><?php echo lang('product_sku'); ?></th>
                    <th><?php echo lang('product_name'); ?></th>
                    <th><?php echo lang('product_description'); ?></th>
                    <th><?php echo lang('product_price'); ?></th>
                    <th><?php echo lang('tax_rate'); ?></th>
                    <th><?php echo lang('options'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php  foreach ($warehouse_data as $products) { ?>
                    <tr>
                        <td><?php echo "Parts"; ?></td>
                        <td><?php echo $products['psku']; ?></td>
                        <td><?php echo $products['title']; ?></td>
                        <td><?php echo $products['notes']; ?></td>
                        <td><?php echo "$".$products['cost']; ?></td>
                        <td><?php echo "00.00"; ?></td>
                        <td><?php echo "None"; ?></td>

                    </tr>




                <?php } ?>



            </tbody>

        </table>
    </div>
</div>