<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo translate('reset_password');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/login_assets/css/bootstrap.min.css');?>">
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/login_assets/fonts/font-awesome/css/font-awesome.min.css');?>">
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/login_assets/fonts/flaticon/font/flaticon.css');?>">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png');?>" type="image/x-icon" >

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@70;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/login_assets/css/style.css');?>">
	<script type="text/javascript">
		var base_url = '<?php echo base_url() ?>';
	</script>
</head>
<body id="top">
<div class="page_loader"></div>

<!-- Login 26 start -->
<div class="login-26">
    <div id="particles-js"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 align-items-center">
                <div class="form-section">
                    <div class="logo-2">
                        <a href="">
                            <img src="<?=$this->application_model->getBranchImage($branch_id, 'logo')?>" alt="logo">
                        </a>
                    </div>


			<form class="form-horizontal" method="post" accept-charset="utf-8">
								<?php echo $this->app_lib->generateCSRF(); ?>

						
                        <div class="form-group clearfix <?php if (form_error('password')) echo 'has-error'; ?>">
                            <input name="password" type="password" class="form-control" autocomplete="off" placeholder="Type new Password" aria-label="Password">
<?php echo form_error('password'); ?>
						</div>
						
                       
                <div class="form-group clearfix <?php if (form_error('password')) echo 'has-error'; ?>">
                            <input name="c_password" type="password" class="form-control" autocomplete="off" placeholder="Confirm password" aria-label="Password">
<?php echo form_error('c_password'); ?>
						</div>
                       
                       
                       
                       
                       
                       
                       
                        <div class="form-group mb-0 clearfix">
                            <button type="submit" class="btn btn-lg btn-primary btn-theme">Change password</button>
                        </div>
                        <div class="extra-login clearfix">
                            <span>Our Socail Account</span>
                        </div>
                        <div class="clearfix"></div>
                        <ul class="social-list">
                            <li><a href="<?php echo $global_config['facebook_url'];?>" class="facebook-color"><i class="fa fa-facebook facebook-i"></i><span>Facebook</span></a></li>
                            <li><a href="<?php echo $global_config['twitter_url'];?>" class="twitter-color"><i class="fa fa-twitter twitter-i"></i><span>Twitter</span></a></li>
                            <li><a href="<?php echo $global_config['youtube_url'];?>" class="google-color"><i class="fa fa-google google-i"></i><span>Youtube</span></a></li>
                        </ul>
                   <?php echo form_close(); ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login 26 end -->

<!-- External JS libraries -->
<script src="<?php echo base_url('assets/login_assets/js/jquery-3.6.0.min.js');?>"></script>
<script src="<?php echo base_url('assets/login_assets/js/bootstrap.bundle.min.js');?>"></script>
<script src="<?php echo base_url('assets/login_assets/js/jquery.validate.min.js');?>"></script>
<script src="<?php echo base_url('assets/login_assets/js/app.js');?>"></script>
<!-- Custom JS Script -->
		<?php
		$alertclass = "";
		if($this->session->flashdata('alert-message-success')){
			$alertclass = "success";
		} else if ($this->session->flashdata('alert-message-error')){
			$alertclass = "error";
		} else if ($this->session->flashdata('alert-message-info')){
			$alertclass = "info";
		}
		if($alertclass != ''):
			$alert_message = $this->session->flashdata('alert-message-'. $alertclass);
			?>
			<script type="text/javascript">
				swal({
					toast: true,
					position: 'top-end',
					type: '<?php echo $alertclass;?>',
					title: '<?php echo $alert_message;?>',
					confirmButtonClass: 'btn btn-default',
					buttonsStyling: false,
					timer: 8000
				})
			</script>
		<?php endif; ?>
</body>

</html>
