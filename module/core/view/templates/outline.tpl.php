<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><?php echo $page['title']; ?></title>
		
		<script type="text/javascript" src="<?php echo $this->api->path('js/jquery-1.8.2.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo $this->api->path('js/jquery_ui/jquery-ui-1.9.0.custom.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo $this->api->path('js/jquery.form.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo $this->api->path('js/jquery.ciderbit.js'); ?>"></script>

		<?php foreach ($page['js'] as $js): ?>
		<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
		
		<link href="<?php echo $this->api->path('js/jquery_ui/css/jquery_ui.css'); ?>" type="text/css" rel="stylesheet"/>
		<link href="<?php echo $this->api->theme_path('css/layout.css'); ?>" type="text/css" rel="stylesheet"/>
		<?php foreach ($page['css'] as $css): ?>
		<link href="<?php echo $css; ?>" type="text/css" rel="stylesheet"/>
		<?php endforeach; ?>
	</head>
	
	<body>
		<div id="container">
			<?php $this->api->region('header'); ?>

			<div id="main-wrapper">
				<div id="main">
					<?php $this->api->load($system['templates']['main']); ?>
					<?php $this->api->region('sidebar'); ?>
				</div>
			</div>

			<div id="footer-wrapper">
				<?php $this->api->region('footer'); ?>
			</div>

		</div>
		<script type="text/javascript"><?php $this->api->jss(); ?></script>
	</body>
</html>