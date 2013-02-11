<{ciderbit_edit_form}>

<table class="ciderbit_dataedit">
	<tr>
		<th>Email</th>
		<td>
			<input class="text l" type="text" name="email" class="text xl"/>
			<{if isset($errorTitle)}><p class="alert"><{$errorTitle}></p><{/if}>
		</td>
	</tr>
</table>
<{/ciderbit_edit_form}>