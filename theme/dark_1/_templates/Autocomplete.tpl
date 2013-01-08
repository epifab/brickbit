{"recordsets": [
<{foreach name="recordsets" from=$recordsets item="recordset"}>
	{<{foreach name="paths" from=$paths item="path"}>"<{$path}>": "<{$recordset->search($path)|replace:'"':'\\"'}>"<{if !$smarty.foreach.paths.last}>, <{/if}><{/foreach}>}<{if !$smarty.foreach.recordsets.last}>,<{/if}>

<{/foreach}>
]}