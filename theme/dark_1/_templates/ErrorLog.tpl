<{ciderbit_read_form}><{/ciderbit_read_form}>

<div>Search by script url <{ciderbit_control_filter path="script_url" type="STARTS" instant=true}></div>
<div>Start date <{ciderbit_control_filter path="date_time_request" type=">=" instant=true}></div>
<div>End date <{ciderbit_control_filter path="date_time_request" type="<=" instant=true}></div>
<{ciderbit_read_content element="div"}>
<{foreach $logs as $log}>
	<div>
		<h2><{$log->script_url}> <{$log->id}></h2>
	<{$log->getRead("id")}>
	</div>
<{/foreach}>
<{ciderbit_control_paging source=$logs}>
<{/ciderbit_read_content}>