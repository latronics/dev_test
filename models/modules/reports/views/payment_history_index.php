<div id="headerbar">
    <h1><?php echo lang('payment_history'); ?></h1>
</div>
<?php

$user_id = $this->session->userdata("user_id");
$this->db->where("user_id", $user_id);
$user_data = $this->db->get("ip_users")->result_object();
$store = $user_data[0]->user_store;

?>
<div id="content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div id="report_options" class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-print"></i>
                <?php echo lang('report_options'); ?>
            </h3>
        </div>

        <div class="panel-body">

            <form method="post"
                  action="<?php echo site_url($this->uri->uri_string()); ?>" target="_blank">

                <div class="form-group has-feedback">
                    <label for="from_date">
                        <?php echo lang('from_date'); ?>
                    </label>

                    <div class="input-group">
                        <input name="from_date" id="from_date"
                               class="form-control datepicker">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar fa-fw"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group has-feedback">
                    <label for="to_date">
                        <?php echo lang('to_date'); ?>
                    </label>

                    <div class="input-group">
                        <input name="to_date" id="to_date"
                               class="form-control datepicker">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar fa-fw"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label for="store">
                        <?php echo 'Store'; ?>
                    </label>

                    <div>
                        <?php if ($store == 1) {   ?>
                            <select name="store" id="store" class="form-control">
                                <?php foreach ($store_data as $store_data) {
                                    ?>
                                    <option value="<?php echo $store_data['id']; ?>"><?php echo $store_data['store_name']; ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <select name="store" id="store" class="form-control">
                                <?php foreach ($store_data as $store_data) {
                                    if ($store_data['id'] == $store) {
                                        ?>
                                        <option value="<?php echo $store_data['id']; ?>"><?php echo $store_data['store_name']; ?></option>
                                    <?php }
                                }
                                ?>
                            </select>

<?php } ?>
                    </div>

                </div>
                <div class="form-group has-feedback">
                    <label for="products">
<?php echo 'Products'; ?>
                    </label>

                    <div>
                        <select name="products" id="products" class="form-control">
                            <option value="all">All</option>
                            <?php foreach ($products_data as $products_data) {
                                ?>
                                <option value="<?php echo $products_data['product_id']; ?>"><?php echo $products_data['product_name']; ?></option>
<?php } ?>
                        </select>

                    </div>

                </div>
                <input type="submit" class="btn btn-success" name="btn_submit"
                       value="<?php echo lang('run_report'); ?>">

            </form>
        </div>

    </div>

</div>
