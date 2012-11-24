<div id="footer_left">
	<h3>www.cambiamentodue.it &copy; 2011</h3>
	<h4>Developed by Fabio Epifani <a href="http://www.episoft.it">www.episoft.it</a></h4>
</div>
<div id="footer_right">
	<ul class="footer_pages">
	<{foreach from=$menuItems item="menuItem"}>
		<li><a href="<{$private.siteAddr}><{$menuItem->url}>.html"><span><{$menuItem->getRead("title")}></span></a></li>
	<{/foreach}>
	</ul>
</div>
<div class="clear"></div>