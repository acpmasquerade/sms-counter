<?php

	// Library
	
	include_once __DIR__."/../sms_counter.php";

	// Testcases
	$testcases = file_get_contents(__DIR__."/testcase.txt");

	// Parse the file and separate results and message from the file

	$testcases = preg_split("/\n--\n/u", $testcases);

	// remove the first one
	array_shift($testcases);

	$encoding_array = array();
	$encoding_array[1] = "GSM_7BIT";
	$encoding_array[2] = "GSM_7BIT_EX";
	$encoding_array[3] = "UTF16";

	// bootstrap css
	echo <<<EOT
	<html>
	<meta charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-sortable.css" />
	<script type="text/javascript" src="bootstrap/js/jquery-2.1.0.min.js"></script>
	<script type="text/javascript" src="bootstrap/js/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bootstrap/js/bootstrap-sortable.js"></script>
	<div class='container'>
	<h1>
		<a target='_blank' href='http://github.com/acpmasquerade/sms-counter'>SMSCounter PHP Library</a>
	</h1>
	<div class="row">
		<div class='col-lg-8'>
			<h2>Test Runner</h2>
		</div>
		<div class="col-lg-4">
			<h3><small>@acpmasquerade</small></h3>
		</div>
	</div>

	<hr />
	<table class='table table-bordered table-striped sortable'>
		<thead>
			<tr>
				<th>S.No</th>
				<th>Text</th>
				<th>Expected Encoding</th>
				<th>Received Encoding</th>
				<th>Expected Length</th>
				<th>Received Length</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
EOT;

	$count = 0; 

	foreach($testcases as $t){

		$count++;
		
		if(strlen($t) == 0){
			continue;
		}

		list($encoding, $length, $text) = explode(" ", $t, 3);
		
		$encoding = $encoding_array[$encoding];
		
		$sms_count = SMSCounter::count($text);

		if($encoding == $sms_count->encoding){
			if($length == $sms_count->length){
				$tr_class = "success";
			}else{
				$tr_class = "warning";
			}
		}else{
			if($length != $sms_count->length){
				$tr_class = "danger";
			}else{
				$tr_class = "warning";
			}
		}

		$text_escaped = htmlspecialchars($text);

		echo <<<EOT
			<tr class='{$tr_class}'>
				<td>{$count}
				<td><small>{$text_escaped}</small>
				<td>{$encoding}
				<td>{$sms_count->encoding}
				<td>{$length}
				<td>{$sms_count->length}
				<td><label class='label label-{$tr_class}'>{$tr_class}</label>
EOT;

	}