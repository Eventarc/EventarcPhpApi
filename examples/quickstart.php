<?php
/**
 * Eventarc API quickstart :
 *
 * This will display a list of events. It adds caching, just so you don't forget
 * to add caching in yourself.
 *
 * @package		Eventarc
 * @category 	Example
 * @author		Eventarc Team
 * @copyright 	(c) 2008-2011 Eventarc Team
 * @license		http://api.eventarc.com/docs/license.txt
 * @link		http://api.eventarc.com/docs/quickstart.html
 */

// Include the library
require_once __DIR__.'/../Eventarc.php';

// Add in your authentication details
$u_name = 'testuser';
$u_apikey = '8de323378f4427ec9b38';

// Get an apikey then store it if we have not already
// NOTE: Do this one time only, then store the apikey, OK? You do not want to
// run foul of the eventarc rate limit. You do not.
//
/*
if (empty($u_apikey))
{
	$eventarc = new Eventarc;
	try
	{
		$login = $eventarc->user_login($u_name, 'password');
		$u_apikey = $login_data['u_apikey'];

		// Re-make eventarc with the apikey
		$eventarc = new Eventarc($u_apikey, $u_name);
	}
	catch (Eventarc_Exception $e)
	{
		echo 'Bad times: '.$e->getMessage();
	}
}
*/

// Get a instance of the API
$eventarc = new Eventarc($u_apikey, $u_name);

// Now get your events. You DO have events don't you?
// Because you lazy people will probably copy and paste this example, I am going
// to include caching. You don't want to be hitting the eventarc api everytime
// someone loads a page. It will slow you down and annoy eventarc (not to
// mention you will probably go over the hourly rate limit causing you
// embarrassment). Cache it!!!
// SO, if you have caching, replace this bit with your own stuff.
$caching = TRUE;
$events = array();

if ($caching AND extension_loaded('memcached'))
{
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	$events = $m->get('event_list_active');
}

// Get the active events
if (empty($events))
{
	try
	{
		$events = $eventarc->event_list('active');
		
		// Save to cache?
		if ($caching AND extension_loaded('memcached'))
		{
			$m->set('event_list_active', $events);
		}
	}
	catch (Eventarc_Exception $e)
	{
		echo 'Bad times: '.$e->getMessage();
	}
}

// Now display the events
foreach ($events as $event)
{
	$url = $event['e_url'];
	$deadline = date('l dS \o\f F Y h:i:s A', strtotime($event['e_deadline']));
	$name = $event['e_name'];

	echo 'Event name: '.$name.' url: '.$url.' Deadline: '.$deadline."\n";
}
