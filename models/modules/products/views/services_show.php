


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


<?php foreach ($services as $services) { ?>
                    <tr>
                        <td><?php echo $services->family_name; ?></td>
                        <td><?php echo $services->product_sku; ?></td>
                        <td><?php echo $services->product_name; ?></td>
                        <td><?php echo nl2br($services->product_description); ?></td>
                        <td><?php echo format_currency($services->product_price); ?></td>
                        <td><?php echo ($services->tax_rate_id) ? $services->tax_rate_name : lang('none'); ?></td>
                        <td>
                            <a href="<?php echo site_url('products/form/' . $services->product_id); ?>"
                               title="<?php echo lang('edit'); ?>"><i class="fa fa-edit fa-margin"></i></a>
                            <a href="<?php echo site_url('products/delete/' . $services->product_id); ?>"
                               title="<?php echo lang('delete'); ?>"
                               onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');"><i
                                    class="fa fa-trash-o fa-margin"></i></a>
                        </td>
                    </tr>
<?php } ?>

            </tbody>

        </table>
    </div>
</div>