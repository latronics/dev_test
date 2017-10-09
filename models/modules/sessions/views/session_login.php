<?php
$this->db->select("*");
$data_session_start = $this->db->get("365admin_valid_session")->result_array();

$this->db->select("*");
$this->db->where("session_start", $data_session_start[0]['session_start']);
$get_username = $this->db->get("session")->result_array();

$this->db->select("*");
$this->db->where("user_name", $get_username[0]['user_name']);
$user_name = $this->db->get("ip_users")->result_array();

$this->db->select("*");
$this->db->where("setting_key", "365laptoprepair");
$param_system_365 = $this->db->get("ip_settings")->result_array();

if (($param_system_365[0]['setting_value'] == 1) && ($data_session_start[0]['session_start'] == "")) {
    echo "This system just can be opened from 365laptoprepair";
    return 0;
}
?>
<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

    <head>
        <title>
<?php
if ($this->mdl_settings->setting('custom_title') != '') {
    echo $this->mdl_settings->setting('custom_title');
} else {
    echo '365Admin';
}
?>
        </title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width">
        <meta name="robots" content="NOINDEX,NOFOLLOW">

        <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

        <link href="<?php echo base_url(); ?>assets/default/css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/default/css/custom.css" rel="stylesheet">

    </head>


    <div id ="general" hidden>
        <input type="text" id = "param_general" value="<?php echo $param_system_365[0]['setting_value']; ?>" hidden>
        <noscript>
        <div class="alert alert-danger no-margin"><?php echo lang('please_enable_js'); ?></div>
        </noscript>

        <br>

        <div class="container">

            <div id="login"
                 class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

                <div class="row"><?php $this->layout->load_view('layout/alerts'); ?></div>

<?php if ($login_logo) { ?>
                    <img src="<?php echo base_url(); ?>uploads/<?php echo $login_logo; ?>" class="login-logo img-responsive">
                <?php } else { ?>
                    <h1><?php echo lang('login'); ?></h1>
                <?php } ?>

                <form class="form-horizontal" method="post"
                      action="<?php echo site_url($this->uri->uri_string()); ?>">

                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3">
                            <label for="email" class="control-label">User</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="email" id="email" class="form-control"
                                   placeholder="User" value="<?php
                if ($user_name[0]['user_name'] != "") {
                    echo $user_name[0]['user_name'];
                }
                ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3">
                            <label for="password" class="control-label"><?php echo lang('password'); ?></label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="password" name="password" id="password" class="form-control"
                                   placeholder="<?php echo lang('password'); ?>"<?php if (!empty($_POST['password'])) : ?> value="<?php echo $_POST['email']; ?>"<?php endif; ?> value="<?php
                                   if ($user_name[0]['user_password'] != "") {
                                       echo $user_name[0]['user_password'];
                                       
                                   }
                ?>">
                        </div>
                    </div>

                    <input type="submit" name="btn_login" id ="btn_login" class="btn btn-block btn-primary"
                           value="<?php echo lang('login'); ?>">

                </form>

                <div class="text-right">
                    <small>
                        <a href="<?php echo site_url('sessions/passwordreset'); ?>" class="text-muted">
<?php echo lang('forgot_your_password'); ?>
                        </a>
                    </small>
                </div>

            </div>
        </div>


    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $("#email").focus();
            $("#email").empty();
            $("#password").empty();
            var param_general = $("#param_general").val();
            if (param_general == 0) {
                $("#general").show();

            } else
            {
                $("#btn_login").click();

            }


        });



    </script>
</html>
