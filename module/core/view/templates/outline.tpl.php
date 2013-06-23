<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><?php echo $page['title']; ?></title>
		
		<?php foreach ($page['js'] as $js): ?>
		<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
		
		<?php foreach ($page['css'] as $css): ?>
		<link href="<?php echo $css; ?>" type="text/css" rel="stylesheet"/>
		<?php endforeach; ?>
	</head>
	
	<body>
		<div id="container">
			<?php $this->api->load_block('admin-menu-wrapper', 'system/block/admin-menu'); ?>
			
			<div id="header-wrapper">
				<div id="header">
					<?php $this->api->region('header'); ?>

					<div id="header-sidebar">
						<?php $this->api->load_block('login-control-wrapper', 'system/block/login-control'); ?>
						<?php $this->api->region('header-sidebar'); ?>
					</div>
				</div>
			</div>
			
			<?php $this->api->load_block('main-menu-wrapper', 'system/block/main-menu'); ?>

			<div id="main-wrapper">
				<div id="main">
					<?php $this->api->open('block', array('url' => $system['mainComponent']['url'], 'name' => 'main-column')); ?>
						<?php $this->api->import($system['templates']['main']); ?>
					<?php $this->api->close(); // block ?>
					<?php $this->api->region('sidebar'); ?>
				</div>
			</div>

			<div id="footer-wrapper">
				<?php $this->api->region('footer'); ?>
			</div>
		</div>
		
		<?php if ($this->api->access('/admin/logs')): ?>
		<?php $this->api->open('block', array('url' => '/admin/logs', 'name' => 'logs')); ?>
		<?php endif; ?>
		
		<script type="text/javascript"><?php $this->api->jss(); ?></script>
	</body>
</html>