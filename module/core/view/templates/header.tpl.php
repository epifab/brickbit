	<div id="admin-menu-wrapper">
		<ul id="admin-menu">
			<?php if ($user): ?>
			<li><?php $this->api->open('link',array(
						'url' => 'user/' . $user->id,
						'title' => 'Your account'
					)); ?>account<?php echo $this->api->close(); ?>
			</li>
			<?php endif; ?>
			<li><?php $this->api->open('link',array(
						'url' => 'contents',
						'title' => 'Contets'
					)); ?>contents<?php echo $this->api->close(); ?>
			</li>
			<li><?php $this->api->open('link',array(
						'url' => 'users',
						'title' => 'Settings'
					)); ?>users<?php echo $this->api->close(); ?>
			</li>
			<li><?php $this->api->open('link',array(
						'url' => 'system/settings',
						'title' => 'Settings'
					)); ?>settings<?php echo $this->api->close(); ?>
			</li>
		</ul>
	</div>

	<div id="header-wrapper">
		<div id="header">
			<h1 id="header-title">
				<?php $this->api->open('link', array(
					'url' => '',
					'ajax' => false
				)); ?><img src="<?php echo $this->api->theme_path("img/logo.png"); ?>" alt="<?php echo $website['title']; ?>"/>
				<?php echo $this->api->close(); ?>
			</h1>
			<?php // <h2 id="header-subtitle"><span><{$website.subtitle </span></h2> ?>

			<div id="header-sidebar">
				<?php $this->api->open('panel', array(
					'name' => "header-sidebar"
				)); ?>
					<div id="header-sidebar-login">
						<?php if ($user->anonymous): ?>
							<li><?php $this->api->open('link', array(
									'url' => 'user/login',
									'okButtonLabel' => $this->api->t("Login"),
									'width' => 300,
									'showResponse' => false
								)); ?><img src="<?php echo $this->api->theme_path("img/login.jpg"); ?>" alt="Login"/>
								<?php echo $this->api->close(); ?>
							</li>
						<?php else: ?>
							<li>
								<?php $this->api->open('link', array(
									'url' => 'user/logout',
									'okButtonLabel' => $this->api->t('Logout'),
									'width' => 300,
									'showResponse' => false
								)); ?><img src="<?php echo $this->api->theme_path('img/logout.jpg'); ?>" alt="Logout"/>
								<?php echo $this->api->close(); ?>
							</li>
						<?php endif; ?>
					</div>
					<div id="header-sidebar-langs">
						<?php foreach ($system['langs'] as $lang): ?>
							<?php if ($lang != $system['lang']): ?>
							<a href="<?php echo $this->api->lang_path($lang); ?>">
								<img alt="<?php echo $lang; ?>" src="<?php echo $this->api->theme_path('img/lang/40/' . $lang . '.jpg'); ?>"/>
							</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php echo $this->api->close(); ?>
			</div>
		</div>
	</div>

	<div id="main-menu-wrapper">
		<ul id="main-menu">
			<?php foreach ($page['mainMenu'] as $menuItem): ?>
				<li <?php if ($page['url'] == $menuItem['url']): ?>class="selected" <?php endif; ?>id="item-<?php echo $menuItem['id']; ?>">
					<?php $this->api->open('link', array(
						'ajax' => false,
						'url' => $menuItem['url']
					)); ?><?php echo $menuItem['title']; ?><?php echo $this->api->close(); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>