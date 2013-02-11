<div class="ciderbit_login_form">
	<form id="<{$private.formId}>" class="login" method="post" action="<{$private.componentAddr}>">
		<input type="hidden" name="login_form" value="1"/>
		<div><label for="username">Email</label><br/><input type="text" id="username" name="username"/></div>
		<div><label for="userpass">Password</label><br/><input type="password" id="userpass" name="userpass"/></div>
		<{if isset($errorMessage)}>
			<p class="alert"><{$errorMessage|default:''}></p>
		<{/if}>
	</form>
</div>