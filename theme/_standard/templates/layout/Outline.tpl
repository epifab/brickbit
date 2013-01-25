<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><{$private.pageTitle|default:"`$private.siteName` | `$private.componentName`"}></title>
		
		<script type="text/javascript" src="<{$private.siteAddr}>js/jquery-1.8.2.js"></script>
		<script type="text/javascript" src="<{$private.siteAddr}>js/jquery_ui/jquery-ui-1.9.0.custom.js"></script>
<!--		<script type="text/javascript" src="<{$private.siteAddr}>js/jquery.autocomplete.js"></script>-->
		<script type="text/javascript" src="<{$private.siteAddr}>js/nivoslider/jquery.nivoslider.js"></script>
		<script type="text/javascript" src="<{$private.siteAddr}>js/jquery.form.js"></script>
		<script type="text/javascript" src="<{$private.siteAddr}>js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
		<script type="text/javascript" src="<{$private.siteAddr}>js/jquery.xmca.js"></script>
		<script type="text/javascript" src="<{$private.siteAddr}>js/plupload/js/plupload.full.js"></script>
		<script type="text/javascript" src="<{$private.siteAddr}>js/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
		
		<link href="<{$private.siteAddr}>css/xmca.css" type="text/css" rel="stylesheet"/>
		<link href="<{$private.siteAddr}>js/jquery_ui/css/jquery_ui.css" type="text/css" rel="stylesheet"/>
<!--		<link href="<{$private.siteAddr}>css/jquery.autocomplete.css" type="text/css" rel="stylesheet"/>-->
		<link href="<{$private.siteAddr}>js/nivoslider/css/jquery.nivoslider.css" type="text/css" rel="stylesheet"/>
		<link href="<{$private.siteAddr}>js/nivoslider/css/default.css" type="text/css" rel="stylesheet"/>
		<link href="<{$private.siteAddr}>js/nivoslider/css/pascal.css" type="text/css" rel="stylesheet"/>
		<link href="<{$private.siteAddr}>js/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css"/>
		<link href="<{$private.siteAddr}>css/layout.css" type="text/css" rel="stylesheet"/>
	</head>
	
	<body>

</head>
<body>
		
		<div id="main">
		
			<div id="header">
				<{xmca_nested_component component="Header" prefix="header" args=["url" => $url]}>
			</div>
			
			<div id="container">
				<{include file=$private.mainTemplate}>
			</div>

			<div id="footer">
				<{xmca_nested_component component="Footer" prefix="footer" args=[]}>
			</div>
		</div>

		<script type="text/javascript"><{xmca_get_javascript}></script>
	</body>
</html>