<{xmca_read_form}><{/xmca_read_form}>

<div>Search by script url <{xmca_control_filter path="script_url" type="STARTS" instant=true}></div>
<div>Start date <{xmca_control_filter path="date_time_request" type=">=" instant=true}></div>
<div>End date <{xmca_control_filter path="date_time_request" type="<=" instant=true}></div>
<{xmca_read_content element="div"}>
<{foreach $logs as $log}>
	<div>
		<h2><{$log->script_url}> <{$log->id}></h2>
	<{$log->getRead("id")}>
	</div>
<{/foreach}>
<{xmca_control_paging source=$logs}>
<{/xmca_read_content}>