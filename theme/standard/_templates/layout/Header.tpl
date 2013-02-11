<{ciderbit_read_form}><{/ciderbit_read_form}>
<{ciderbit_read_content}>

<h1 id="header_title"><a href="<{$private.siteAddr}>Home.html"><img src="<{theme}>img/logo.gif" alt="<{$private.siteName}>"/></a></h1>
<h2 id="header_subtitle"><span><{$private.siteDesc}></span></h2>
<div id="header_sidebar">
<!--	<div class="search">
		<form action="Home.html" method="post"><input type="text" class="text" name="search" /><input type="image" src="<{$private.siteAddr}>img/search.png"/></form>
	</div>-->
	<div id="header_sidebar_login">
		<{if $private.login->isAnonymous()}>
			<a href="javascript:<{ciderbit_control_action component="Login" okButtonLabel="Login" width=300 showResponse=false}>"><img src="<{theme}>img/lock.jpg" alt="Login"/></a></li>
		<{else}>
			<a href="javascript:<{ciderbit_control_action component="Login" args=['logout'=>1] showResponse=false}>"><img src="<{theme}>img/unlock.jpg" alt="Logout"/></a></li>
		<{/if}>
	</div>
	<div id="header_sidebar_langs">
		<{foreach $private.languages as $lang}>
			<{if $lang != $private.language}>
			<a class="lang" href="Home.html?lang=<{$lang}>"><img src="<{theme}>img/lang/40/<{$lang}>.jpg"/></a>
			<{/if}>
		<{/foreach}>
	</div>
	<!--	<div class="header_social_networks">
		<a target="blank" href="http://www.facebook.com/Dr.Gerboni"><img src="<{$private.siteAddr}>img/social_networks/facebook.png" width="30" height="30" alt="Facebook"/></a>
		<a target="blank" href="https://twitter.com/EGerboni"><img src="<{$private.siteAddr}>img/social_networks/twitter.png" width="30" height="30" alt="Twitter"/></a>
		<a target="blank" href="http://www.linkedin.com/profile/view?id=104603791&trk=tab_pro"><img src="<{$private.siteAddr}>img/social_networks/linkedin.png" width="30" height="30" alt="Linkedin"/></a>
	</div>-->
</div>
<ul id="header_menu">
<{foreach from=$menuItems item="menuItem"}>
	<li <{if $url == $menuItem->url}>class="selected" <{/if}>id="item_<{$menuItem->url}>"><a href="<{$private.siteAddr}><{$menuItem->url}>.html"><span><{$menuItem->getRead("title")}></span></a></li>
<{/foreach}>
<{ciderbit_restricted_area component="EditPage"}>
	<li><{ciderbit_control component="EditPage" width=800 height=500 title="Create new page" title="+" style="link"}></li>
<{/ciderbit_restricted_area}>
</ul>
<{/ciderbit_read_content}>