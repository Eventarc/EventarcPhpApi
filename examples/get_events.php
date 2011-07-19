<?php

/**
 * Eventarc API get events :
 *
 * This will display a list of events.
 *
 * @package		Eventarc
 * @category	Example
 * @author		Eventarc Team
 * @copyright	(c) 2008-2011 Eventarc Team
 * @license		http://api.eventarc.com/docs/license.txt
 * @link		http://api.eventarc.com/docs/
 */

// Include the library
require_once __DIR__.'/../Eventarc.php';

// Get a instance of the API
$eventarc = new Eventarc('your-api-key-goes-here', 'your-user-name-goes-here');

try
{
	$events = $eventarc->event_list();
}
catch (Eventarc_Exception $e)
{
	echo 'Bad times: '.$e->getMessage();
}

var_dump($events);
