<?php

// Check for requirement when install app
$compatibilities =  [
  [
      'type' => 'requirement',
      'name' => 'PHP version',
      'check' => version_compare(PHP_VERSION, '5.5.9', '>='),
      'note' => 'PHP 5.5.9 or higher is required.',
  ],
  [
      'type' => 'requirement',
      'name' => 'OpenSSL Extension',
      'check' => extension_loaded('openssl'),
      'note' => 'OpenSSL PHP Extension is required.',
  ],
  [
      'type' => 'requirement',
      'name' => 'Mbstring PHP Extension',
      'check' => extension_loaded('mbstring'),
      'note' => 'Mbstring PHP Extension is required.',
  ],
  [
      'type' => 'requirement',
      'name' => 'PDO PHP extension',
      'check' => extension_loaded('pdo'),
      'note' => 'PDO PHP extension is required.',
  ],
  [
      'type' => 'requirement',
      'name' => 'Tokenizer PHP Extension',
      'check' => extension_loaded('tokenizer'),
      'note' => 'Tokenizer PHP Extension is required.',
  ],
  [
      'type' => 'requirement',
      'name' => 'PHP Zip Archive',
      'check' => class_exists('ZipArchive', false),
      'note' => 'PHP Zip Archive is required.',
  ],
  [
      'type' => 'requirement',
      'name' => 'IMAP Extension',
      'check' => extension_loaded('imap'),
      'note' => 'PHP IMAP Extension is required.',
  ],
  [
      'type' => 'permission',
      'name' => 'Storage/app/',
      'check' => file_exists('../../storage/app') &&
          is_dir('../../storage/app') &&
          (is_writable('../../storage/app')) &&
          substr(sprintf('%o', fileperms('../../storage/app')), -4) >= 775,
      'note' => 'The directory must be writable by the web server (0775).',
  ],
  [
      'type' => 'permission',
      'name' => 'Storage/framework/',
      'check' => file_exists('../../storage/framework') && is_dir('../../storage/framework') && (is_writable('../../storage/framework')) &&
          substr(sprintf('%o', fileperms('../../storage/app')), -4) >= 775,
      'note' => 'The directory must be writable by the web server (0775).',
  ],
  [
      'type' => 'permission',
      'name' => 'Storage/logs/',
      'check' => file_exists('../../storage/logs') && is_dir('../../storage/logs') && (is_writable('../../storage/logs')) &&
          substr(sprintf('%o', fileperms('../../storage/logs')), -4) >= 775,
      'note' => 'The directory must be writable by the web server (0775).',
  ],
  [
      'type' => 'permission',
      'name' => 'bootstrap/cache/',
      'check' => file_exists('../../bootstrap/cache') && is_dir('../../bootstrap/cache') && (is_writable('../../bootstrap/cache')) &&
          substr(sprintf('%o', fileperms('../../bootstrap/cache')), -4) >= 775,
      'note' => 'The directory must be writable by the web server (0775).',
  ],
];

//var_dump($compatibilities);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Requirement - Acelle Installation</title>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="apple-touch-icon" sizes="57x57" href="http://local:8000/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="http://local:8000/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="http://local:8000/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="http://local:8000/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="http://local:8000/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="http://local:8000/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="http://local:8000/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="http://local:8000/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="http://local:8000/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="http://local:8000/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="http://local:8000/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="http://local:8000/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="http://local:8000/favicon/favicon-16x16.png">
	<link rel="manifest" href="http://local:8000/favicon/manifest.json">
	
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="http://local:8000/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	
	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Source+Code+Pro:400,600|Open+Sans:300italic,400italic,600italic,700italic,700,300,600,400" rel="stylesheet" type="text/css">
	<link href="http://local:8000/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="http://local:8000/assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="http://local:8000/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="http://local:8000/assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="http://local:8000/assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="http://local:8000/assets/css/colors.css" rel="stylesheet" type="text/css">
	<link href="http://local:8000/css/app.css" rel="stylesheet" type="text/css">
    <link href="http://local:8000/css/theme.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->	
	<!-- Core JS files -->
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/loaders/blockui.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/ui/nicescroll.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/ui/drilldown.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/selects/select2.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/validation/validate.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/visualization/d3/d3.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/ui/moment/moment.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/pickers/daterangepicker.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/notifications/bootbox.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/notifications/sweet_alert.min.js"></script>
		
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/styling/switch.min.js"></script>
		
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script type="text/javascript" src="http://local:8000/js/jquery.numeric.min.js"></script>
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/notifications/pnotify.min.js"></script>
			
	<link rel="stylesheet" href="http://local:8000/js/scrollbar/jquery.mCustomScrollbar.css">
	<script type="text/javascript" src="http://local:8000/js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>

		
	<script type="text/javascript" src="http://local:8000/assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script type="text/javascript" src="http://local:8000/js/validate.js"></script>

	<script type="text/javascript" src="http://local:8000/js/app.js"></script>
	<!-- /theme JS files -->
	
	<script>
		var DATATABLE_TRANSLATE_URL = 'http://local:8000/datatable_locale';
		var JVALIDATE_TRANSLATE_URL = 'http://local:8000/jquery_validate_locale';
		var APP_URL = 'http://local:8000';
		var LANG_OK = 'OK';
		var LANG_DELETE_VALIDATE = 'Please enter the text exactly as it is displayed to confirm deletion.';
		var LANG_DATE_FORMAT = 'yyyy-mm-dd';
	</script>	<!-- Set active menu -->
	<script>
		$(document).ready(function() {
			for (i=0; i < 10; i++) {
					$("li[rel"+i+"='InstallController']").addClass("active");
			}
			for (i=0; i < 10; i++) {
					$("li[rel"+i+"='InstallController/systemCompatibility']").addClass("active");
			}
		});
		</script>
		<!-- /set active menu -->
		
		<!-- display flash message -->
		<script>

		</script>
		<!-- /display flash message -->
</head>

<body class="bg-slate-800">

	<!-- Page container -->
	<div class="page-container login-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">
				<div class="row">
					<div class="col-sm-1 col-md-1">
						
					</div>
					<div class="col-sm-10 col-md-10">
					
						<div class="text-center login-header">
							<a class="main-logo-big" href="http://local:8000">
								<img src="http://local:8000/images/logo_big.png" alt="">
							</a>
                            
              <h3 class="text-center text-muted2" style="color: #ccc">Installation</h3>
						</div>
                        
          <ul class="nav nav-pills campaign-steps install-steps">					
						<li class="active enabled">
							<a href="http://local:8000/install/system-compatibility">
								<i class="icon-server"></i> System compatibility
							</a>
						</li>
            <li class=" enabled">
							<a href="http://local:8000/install/site-info">
								<i class="icon-gear"></i> Configuration
							</a>
						</li>
						<li class=" ">
							<a href="http://local:8000/install/database">
								<i class="icon-database"></i> Database
							</a>
						</li>
						<li class=" ">
							<a href="http://local:8000/install/cron-jobs">
								<i class="icon-alarm"></i> Cron jobs
							</a>
						</li>
						<li class=" ">
							<a href="http://local:8000/install/finish">
								<i class="icon-checkmark4"></i> Finish
							</a>
						</li>
					</ul>                        
                        <div class="panel panel-flat" style="border-radius: 0 0 3px 3px">
                            <div class="panel-body">
								
                                
	<h3 class="text-teal-800"><i class="icon-puzzle2"></i> Requirements</h3>

    <div class="row">
        <div class="col-md-12">
            <ul class="modern-listing mt-0">
                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								PHP version
							</h5>
							<p>
								PHP 5.5.9 or higher is required.
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								OpenSSL Extension
							</h5>
							<p>
								OpenSSL PHP Extension is required.
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								Mbstring PHP Extension
							</h5>
							<p>
								Mbstring PHP Extension is required.
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								PDO PHP extension
							</h5>
							<p>
								PDO PHP extension is required.
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								Tokenizer PHP Extension
							</h5>
							<p>
								Tokenizer PHP Extension is required.
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								PHP Zip Archive
							</h5>
							<p>
								PHP Zip Archive is required.
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								IMAP Extension
							</h5>
							<p>
								PHP IMAP Extension is required.
							</p>
						</li>
					                					                					                					                					                            </ul>
        </div>
    </div>
		
	<h3 class="text-teal-800"><i class="icon-file-check"></i> Permissions</h3>

    <div class="row">
        <div class="col-md-12">
            <ul class="modern-listing mt-0">
                					                					                					                					                					                					                					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								Storage/app/
							</h5>
							<p>
								The directory must be writable by the web server (0775).
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								Storage/framework/
							</h5>
							<p>
								The directory must be writable by the web server (0775).
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								Storage/logs/
							</h5>
							<p>
								The directory must be writable by the web server (0775).
							</p>
						</li>
					                											<li>
															<i class="icon-checkmark4 text-success"></i>
														<h5 class="mt-0 mb-0 text-semibold">
								bootstrap/cache/
							</h5>
							<p>
								The directory must be writable by the web server (0775).
							</p>
						</li>
					                            </ul>
        </div>
    </div>
	
	<div class="text-right">                                    
					<a href="http://local:8000/install/site-info" class="btn btn-primary bg-teal">Next <i class="icon-arrow-right14 position-right"></i></a>
			</div>

                            </div>
						</div>
					</div>
				</div>
			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->


		<!-- Footer -->
		<div class="footer text-white">
			&copy; 2016. <span class="text-white">Acelle Email Marketing Application</span> by <a href="http://acellemail.com" class="text-white" target="_blank">Acellemail.com</a>			
		</div>
		<!-- /footer -->

	</div>
	<!-- /page container -->

</body>
</html>
