<{panel name="main" class="login-form"}>
	<form id="<{edit_form_id}>" class="login" method="post" action="<{$system.component.url}>">
		<input type="hidden" name="login_form" value="1"/>
		<div><label for="username">Email</label><br/><input type="text" id="username" name="username"/></div>
		<div><label for="userpass">Password</label><br/><input type="password" id="userpass" name="userpass"/></div>
		<{if isset($errorMessage)}>
			<p class="alert"><{$errorMessage|default:''}></p>
		<{/if}>
	</form>
<{/panel}>