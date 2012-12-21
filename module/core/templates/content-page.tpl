<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><{$page.title}></title>
		
		<{foreach from=$page.js item="js"}>
		<script type="text/javascript" src="<{$js}>"></script>
		<{/foreach}>
		
		<{foreach from=$page.css item="css"}>
		<link href="<{$css}>" type="text/css" rel="stylesheet"/>
		<{/foreach}>
	</head>
	
	<body>
		<div id="main">

			<div id="header">
				<h1 id="header-title">
					<a href="<{$website.base}>">
						<img src="<{theme_path url="img/website-logo.jpg"}>" alt="<{$website.title}>"/>
					</a>
				</h1>
				
				<h2 id="header-subtitle"><span><{$website.subtitle}></span></h2>
				
				<{block name="asd"}>
				<div id="header-sidebar">
				<!--	<div class="search">
						<form action="Home.html" method="post"><input type="text" class="text" name="search" /><input type="image" src="<{$private.siteAddr}>img/search.png"/></form>
					</div>-->
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
							<{link url="lang/`$lang`"}><img src="<{theme_path url="img/lang/40/`$lang`.jpg"}>"/><{/link}>
							<{/if}>
						<{/foreach}>
					</div>
				</div>
				<ul id="main-menu">
					<{foreach from=$page.mainMenu item="menuItem"}>
					<li <{if $page.url == $menuItem.url}>class="selected" <{/if}>id="item-<{$menuItem.id}>"><{link url=$menuItem.url}><span><{$menuItem.title}></span><{/link}></li>
					<{/foreach}>
					<{protected url="page/add"}>
					<li><{link url="page/add" width=800 height=500 title="Create new page"}>+<{/link}></li>
					<{/protected}>
				</ul>
				<{/block}>
			</div>
			
			<div id="container">
				<{include file=$private.mainTemplate}>
			</div>

			<div id="footer">
				<{*load url=$page.footerUrl*}>
			</div>
		</div>

		<script type="text/javascript"><{javascript}></script>
	</body>
</html>