<div class="notify <{if $system.responseType == 'ERROR'}>error<{else}>success<{/if}>">
	<h2><{$message.title|default:'Done'}></h2>
	<{$message.body|default:''}>
</div>