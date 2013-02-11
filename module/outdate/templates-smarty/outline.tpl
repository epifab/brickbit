<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><{$page.title}></title>
		
		<script type="text/javascript" src="<{path url="js/jquery-1.8.2.js"}>"></script>
		<script type="text/javascript" src="<{path url="js/jquery_ui/jquery-ui-1.9.0.custom.js"}>"></script>
		<script type="text/javascript" src="<{path url="js/jquery.form.js"}>"></script>
		<script type="text/javascript" src="<{path url="js/jquery.ciderbit.js"}>"></script>
		<{foreach from=$page.js item="js"}>
		<script type="text/javascript" src="<{$js}>"></script>
		<{/foreach}>
		
		<link href="<{path url="js/jquery_ui/css/jquery_ui.css"}>" type="text/css" rel="stylesheet"/>
		<link href="<{theme_path url="css/layout.css"}>" type="text/css" rel="stylesheet"/>
		<{foreach from=$page.css item="css"}>
		<link href="<{$css}>" type="text/css" rel="stylesheet"/>
		<{/foreach}>
	</head>
	
	<body>
		<div id="container">
			<{region name="header"}>

			<div id="main-wrapper">
				<div id="main">
					<{include file=$system.templates.main}>

					<{region name="sidebar"}>
				</div>
			</div>

			<div id="footer-wrapper">
				<{region name="footer"}>
			</div>

		</div>
		<script type="text/javascript"><{javascript}></script>
	</body>
</html>