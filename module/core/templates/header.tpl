	<div id="admin-menu-wrapper">
		<ul id="admin-menu">
			<li><{link url="user/account"}>account<{/link}></li>
			<li><{link url="contents"}>contents<{/link}></li>
			<li><{link url="users"}>users<{/link}></li>
			<li><{link url="system"}>system<{/link}></li>
		</ul>
	</div>

	<div id="header-wrapper">
		<div id="header">
			<h1 id="header-title">
				<{link url="" ajax=false}>
					<img src="<{theme_path url="img/logo.png"}>" alt="<{$website.title}>"/>
				<{/link}>
			</h1>
			<{*<h2 id="header-subtitle"><span><{$website.subtitle}></span></h2>*}>

			<div id="header-sidebar">
				<{panel name="header-sidebar"}>
					<div id="header-sidebar-login">
						<{if !$user}>
							<{link url="user/login" okButtonLabel="Login" width=300 showResponse=false}><img src="<{theme_path url="img/login.jpg"}>" alt="Login"/><{/link}></li>
						<{else}>
							<{link url="user/logout" okButtonLabel="Logout" width=300 showResponse=false}><img src="<{theme_path url="img/logout.jpg"}>" alt="Logout"/><{/link}></li>
						<{/if}>
					</div>
					<div id="header-sidebar-langs">
						<{foreach $system.langs as $lang}>
							<{if $lang != $system.lang}>
							<a href="http://<{lang_link id=$lang}>"><img src="<{theme_path url="img/lang/40/`$lang`.jpg"}>"/></a>
							<{/if}>
						<{/foreach}>
					</div>
				<{/panel}>
			</div>
		</div>
	</div>

	<div id="main-menu-wrapper">
		<ul id="main-menu">
			<{foreach from=$page.mainMenu item="menuItem"}>
			<li <{if $page.url == $menuItem.url}>class="selected" <{/if}>id="item-<{$menuItem.id}>"><{link url=$menuItem.url}><span><{$menuItem.title}></span><{/link}></li>
			<{/foreach}>
			<{protected url="page/add"}>
			<li><{link url="page/add" width=800 height=500 title="Create new page"}>+<{/link}></li>
			<{/protected}>
			<li><{link url=""}>about<{/link}></li>
			<li><{link url=""}>live demo<{/link}></li>
			<li><{link url=""}>download<{/link}></li>
			<li><{link url=""}>docs<{/link}></li>
			<li><{link url=""}>contacts<{/link}></li>
		</ul>
	</div>
