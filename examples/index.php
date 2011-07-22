<?php

/**
 * Eventarc api example file
 *
 * You will need a valid username and password for these examples to work
 *
 * These examples will show:
 * - Getting information about the user (you)
 * - Getting a list of the groups
 * - Creating a group
 * - Creating a simple event
 * - Getting a list of events
 * - Getting a list of events for a group
 * - Editing an event
 *
 */

// !! NOTE
// You need to replace these with your own values or this api will be very upset
$u_apikey = '8de323378f4427ec9b38'; // 'd24a754b11e269159873';
$u_id = 3;
$u_name = 'testuser';

// We will use the following variables for various api calls. They will be set
//  from the results of other api calls.
$g_id = FALSE; // Group id
$e_id = FALSE; // Event id
$g_data = FALSE; // Group data

// Get a copy of eventarcapi
$eventarc = new Eventarc($u_apikey, $u_id);

// Tell the api to store the history for the calls we are making
$eventarc->keep_history = TRUE;

try {
	$result = $eventarc->event_list('pending');
	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

try {
	$result = $eventarc->attendee_checkin(4,'XA10SPCC-1');
	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

try {
	$result = $eventarc->user_login('testuser','password');

	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

try {
	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	
/**
 * EXAMPLE eventarc.user.get
 * 
 * Get all the data available for ourselves
 *
 */

// Because we set the u_id and u_apikey in the constructor, we don't need to
//  worry about adding them in here as they are auto added.
$params = array();

try {
	$result = $eventarc->call(
		'eventarc.user.get',
		$params);
	$result = $eventarc->user_get();

	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

try {
	$result = $eventarc->event_get_tickets(1826);

	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

/**
 * EXAMPLE eventarc.attendee.list
 * 
 * Get all the attendees for an event
 *
 */

// Because we set the u_id and u_apikey in the constructor, we don't need to
//  worry about adding them in here as they are auto added.
$params = array();

try {
	$result = $eventarc->attendee_list(4);

	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	


try {
	$result = $eventarc->attendee_get(10);

	// The result will contain the user data
	// var_dump($result);{"jsonrpc":"2.0","id":1310969055,"result":{"email_sent":true}}
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

try {
	$result = $eventarc->attendee_resendemail(10);

	// The result will contain the user data
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

/**
 * EXAMPLE eventarc.group.list
 * 
 * Get all of our groups
 *
 */

// Because we set the u_id and u_apikey in the constructor, we don't need to
//  worry about adding them in here as they are auto added.
$params = array();

try {
	$result = $eventarc->call(
		'eventarc.group.list',
		$params);
	$result = $eventarc->group_list();

	// The result will contain the group list
	// Lets store the first group for using in later api calls
	$g_id = $result[0]['g_id'];
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

/**
 * EXAMPLE eventarc.group.get
 * 
 * Get the details of the first group to come through from the previous api call
 *
 */

// We need to send the group id for the group we want to get. It needs to be
//  within a 'g_data' array. However the eventarcapi will automatically put any
//  variable that is prefixed with a character and underscore into the correct
//  data array. ie. u_name is put into u_data as u_data['u_id'] = 123;
$params = array('g_id' => $g_id);

try {
	$result = $eventarc->call(
		'eventarc.group.get',
		$params);
	$result = $eventarc->group_get($g_id);

	// The result will contain the group information
	$g_data = $result;
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($eventarc->error);
}	

/**
 * EXAMPLE eventarc.group.create
 * 
 * Lets create a group within the group we just 'got'. If g_id has not been set,
 * (set within eventarc.group.list) then this method is going to explode and
 * fail.
 */

// Lets put together some random stuff for this group
$params = array(
	'g_name'	=> 'group'.rand(1,500),
	'g_parent'	=> $g_id,
);

try {
	$result = $eventarc->call(
		'eventarc.group.create',
		$params);
	$result = $eventarc->group_create($params);

	// The result will contain the group information
	$g_id = $result['g_id'];
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

/**
 * EXAMPLE eventarc.event.create
 * 
 * Lets create a event within the group we just 'created'. 
 * 
 */

// Lets make a default event
$params = array(
	'e_name' => 'My test event No. '.rand(1,500),
	'e_description' => 'This is my event <strong>description</strong><img src="pic.jpg">',
	'e_start' => date('Y-m-d G:i:s', time() + (7 * 24 * 60 * 60)), // Next week
	'e_stop' => date('Y-m-d G:i:s', time() + (7 * 24 * 60 * 60) + 60), // Next week plus one minute
	'e_deadline' => date('Y-m-d G:i:s', time() + (7 * 24 * 60 * 60) - 60), // Next week minus one minute
	'e_status' => 'active', // Either active, draft or deleted
	'e_timezone' => 'Australia/Melbourne', // If you omit this it will default to your users timezone
	'e_craccccc' => 'fail',
	'g_id' => $g_id,
	'u_id' => $u_id,

	// While we are at it, lets add some extra widgets. By default eventarc will
	//  create a firstname, surname and email "widget" for the form. But you can
	//  add other custom widgets quite easily too.

	// Lets create a text field first
	'0_wd_name' => 'my_custom_text_field',
	'0_wd_type' => 'text',
	'0_wd_label' => 'My text field',
	'0_wd_description' => 'Please enter something into the text field...',
	'0_wd_library' => 'W_custom',
	'0_wd_validation' => 'required|length[1,100]',
	'0_wd_order' => '10',

	// Now lets create a dropdown box
	'1_wd_name' => 'my_dropdown_box',
	'1_wd_type' => 'dropdown',
	'1_wd_label' => 'What you doing?',
	'1_wd_description' => 'Please tell us all we want to know.',
	'1_wd_library' => 'W_custom',
	'1_wd_children'=> 'Yes|No|Maybe',
	'1_wd_validation' => '',
	'1_wd_order' => '11',

);

try {
	$result = $eventarc->call(
		'eventarc.event.create',
		$params);

	// The result will contain the group information
	$e_data = $result;
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

// Lets make a default event
$e_data = array(
	'e_name' => 'My test event No. '.rand(1,500),
	'e_description' => 'This is my event <strong>description</strong>',
	'e_start' => date('Y-m-d G:i:s', time() + (7 * 24 * 60 * 60)), // Next week
	'e_stop' => date('Y-m-d G:i:s', time() + (7 * 24 * 60 * 60) + 60), // Next week plus one minute
	'e_deadline' => date('Y-m-d G:i:s', time() + (7 * 24 * 60 * 60) - 60), // Next week minus one minute
	'e_status' => 'active', // Either active, draft or deleted
	'e_timezone' => 'Australia/Melbourne', // If you omit this it will default to your users timezone
	'g_id' => $g_id,
	'u_id' => $u_id,
	'e_pushurl' => 'http://myeventarc.cascade/ae/catcher'
);

// Add address
$a_data = array(
	'a_type' => 'venue',
	'a_add1' => 'Floor 123, Suite 321',
	'a_add2' => '123 Street Avenue',
	'a_city' => 'Melbourne',
	'a_state' => 'Victoria',
	'a_post' => '3000',
	'a_country' => 'Australia'
);

	// While we are at it, lets add some extra widgets. By default eventarc will
	//  create a firstname, surname and email "widget" for the form. But you can
	//  add other custom widgets quite easily too.
$custom_text_widget = array(
	// Lets create a text field first
	'wd_name' => 'my_custom_text_field',
	'wd_type' => 'text',
	'wd_label' => 'My text field',
	'wd_description' => 'Please enter something into the text field...',
	'wd_library' => 'W_custom',
	'wd_validation' => 'required|length[1,100]',
	'wd_order' => '10',
);

$custom_dropdown_widget = array(
	// Now lets create a dropdown box
	'wd_name' => 'my_dropdown_box',
	'wd_type' => 'dropdown',
	'wd_label' => 'What you doing?',
	'wd_description' => 'Please tell us all we want to know.',
	'wd_library' => 'W_custom',
	'wd_children'=> 'Yes|No|Maybe',
	'wd_validation' => '',
	'wd_order' => '11',
);

$pay_via_invoice_widget = array(
	// Now lets create a dropdown box
	'wd_name' => 'paybyinvoice',
	'wd_type' => 'invisible',
	'wd_label' => 'What you doing?',
	'wd_description' => 'Please tell us all we want to know.',
	'wd_library' => 'W_custom',
	'wd_children'=> 'Yes|No|Maybe',
	'wd_validation' => '',
	'wd_order' => '11',
);
$general_ticket = array(
	't_name' => 'Just a ticket',
	't_description' => 'This is just a normal ticket',
	't_total' => '1234',
	't_price' => '0',
	't_earlybird' => '0',
	't_order' => '1',
	't_type' => 'normal',
	't_defaultquantity' => 10
);

$another_ticket = array(
	't_name' => 'Some other ticket',
	't_description' => 'This is just another normal ticket',
	't_total' => '23',
	't_price' => '0',
	't_earlybird' => '0',
	't_order' => '2',
	't_type' => 'normal',
	't_defaultquantity' => 10
);

$theme = array(
	'th_body_bgcolor' => 'cc0000'
);

try {
	$result = $eventarc
		->add_event($e_data)
		->add_address($a_data)
		->add_widget($custom_text_widget)
		->add_widget($custom_dropdown_widget)
		->add_ticket_limit(2000) 
		->add_ticket_show_fees(TRUE)
		->add_ticket($general_ticket)
		->add_ticket($another_ticket)
		->add_theme($theme)
		->event_create();

	// The result will contain the group information
	$e_data = $result;
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($e);
}	

/**
 * EXAMPLE eventarc.event.list
 * 
 * Get all of our events
 *
 */

// Because we set the u_id and u_apikey in the constructor, we don't need to
//  worry about adding them in here as they are auto added.
$params = array();

try {
//	$result = $eventarc->call(
//		'eventarc.event.list',
//		$params);

	// The result will contain the event list
	// var_dump($result);
}
catch (Eventarcapi_Exception $e)
{
	// Check $eventarc->error to see why this failed
	// var_dump($eventarc->error);
}	




?>
<!DOCTYPE html> 
<html> 
<head> 
	<meta charset="UTF-8" /> 
	<title>Eventarc api examples</title> 
	
	<link rel="shortcut icon" href="http://eventarc.com/favicon.ico" /> 
	<link rel="icon" href="http://eventarc.com/images/favicon.ico" type="image/x-icon" /> 
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.8/themes/base/jquery-ui.css" type="text/css" media="all" /> 
	<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" /> 
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script> 
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.8/jquery-ui.min.js" type="text/javascript"></script> 
	<script src="http://jquery-ui.googlecode.com/svn/tags/latest/external/jquery.bgiframe-2.1.2.js" type="text/javascript"></script> 
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.8/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script> 
	<style type="text/css"> 
		.startHidden {display:none;}
	</style> 
</head> 
 
<body> 
	<script>
	$(function() {
		$( "#accordion" ).accordion({
			autoHeight: false	
		});
	});
	</script>

<div id="accordion">
	<?php 
	// Display results in accordion
	$i = 1;
	foreach ($eventarc->history as $api_call)
	{
		$error = FALSE;
		if (array_key_exists('response', $api_call)
			AND array_key_exists('error', $api_call['response']))
		{
			$result = ' : <span style="color:red">Failed</span>';
			$error = TRUE;
		}
		else
		{
			$result = ' : <span style="color:green">Passed</span>';
			$error = FALSE;
		}
		echo '<h3><a href="#">'.$api_call['method'].$result.'</a></h3>';
		echo '<div>';
		echo '<div id="tabs'.$i.'">
			<ul>
				<li><a href="#tabs'.$i.'-0">Method</a></li>
				<li><a href="#tabs'.$i.'-1">Result</a></li>
				<li><a href="#tabs'.$i.'-2">JSON result</a></li>
				<li><a href="#tabs'.$i.'-3">Values sent</a></li>
				<li><a href="#tabs'.$i.'-4">JSON sent</a></li>
			</ul>';
		echo '<div id="tabs'.$i.'-0" style="height:200px;"><p><pre>';
		echo print_r($api_call['method'], TRUE);
		echo '</pre></p></div>';
		echo '<div id="tabs'.$i.'-1"><p><pre>';
		echo print_r($api_call['response'], TRUE);
		echo '</pre></p></div>';
		echo '<div id="tabs'.$i.'-2"><p><pre>';
		echo print_r($api_call['json_response'], TRUE);
		echo '</pre></p></div>';
		echo '<div id="tabs'.$i.'-3"><p><pre>';
		echo print_r($api_call['payload'], TRUE);
		echo '</pre></p></div>';
		echo '<div id="tabs'.$i.'-4"><p><pre>';
		echo print_r($api_call['json_payload'], TRUE);
		echo '</pre></p></div>';
		echo '<script> $(function() { $( "#tabs'.$i.'" ).tabs(); 	});	</script></div>';
		echo '</div>';
		$i++;
	}


	?>
	</div>
</body>
</html>

