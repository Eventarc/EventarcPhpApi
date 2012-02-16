<?php

/**
 * Eventarc API library.
 * You should look at the docs: http://api.eventarc.com/docs/
 *
 * Heres a quick demo:
 *
 * <code>
 *		<?php
 *		// Include the library
 *		require_once __DIR__.'/../Eventarc.php';
 *
 *		// Get an instance of the API
 *		$eventarc = new Eventarc('yourapikeygoesinhere', 'yourusername');
 *
 *		// Get a event list
 *		try {
 *			$events = $eventarc->event_list();
 *		} catch (Eventarc_Exception $e) {
 *			echo $e->getMessage(); // Troubles...
 *		}
 *
 * </code>
 *
 * @package		Eventarc
 * @category	Library
 * @author		Eventarc Team
 * @copyright	(c) 2008-2011 Eventarc Team
 * @license		http://api.eventarc.com/docs/license.txt
 * @link		http://api.eventarc.com/docs/
 */
class Eventarc
{
	const VERSION = '2.6.2'; // See http://semver.org/

	protected $params = array();
	public $server = 'https://api.eventarc.com/api/v2/';
	protected $method;
	public $response;
	public $error;
	public $u_id;
	public $u_name;
	public $u_apikey;
	public $last_payload;
	public $last_json_payload;
	public $history;
	public $keep_history = FALSE;

	/**
	 * Constructor
	 *
	 * Its easier to pass in your apikey and u_id/u_name with the constructor.
	 * Note that you can choose to pass either your username (u_name) or u_id to
	 * the api.
	 *
	 * @param string $u_apikey
	 * @param mixed $u_id Either your u_id or u_name
	 * @access public
	 * @return void
	 */
	public function __construct($u_apikey=FALSE, $u_id=FALSE)
	{
		if (is_numeric($u_id))
		{
			$this->u_id = $u_id;
		}
		elseif ($u_id)
		{
			$this->u_name = $u_id;
		}
		$this->u_apikey = $u_apikey;
	}

	/**
	 * Create an address. You can assign it to a event later.
	 *
	 * @param array $params
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcaddresscreate.html
	 * @return array The result array
	 */
	public function address_create(array $params)
	{
		return $this->call('eventarc.address.create', $params);
	}

	/**
	 * Update an address.
	 *
	 * @param array $params
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcaddresscreate.html
	 * @return array The result array
	 */
	public function address_update(array $params)
	{
		return $this->call('eventarc.address.update', $params);
	}

	/**
	 * Get an address.
	 * 
	 * @param array $params 
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcaddressget.html
	 * @return array The result array
	 */
	public function address_get($a_id)
	{
		return $this->call('eventarc.address.get', array('a_id' => $a_id));
	}

	/**
	 * Check a attendee in  
	 * 
	 * @param string $et_rego The attendees registration code
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcattendeecheckin.html
	 * @return array The result array
	 */
	public function attendee_checkin($e_id, $et_rego)
	{
		return $this->call('eventarc.checkin.create', array(
			'e_id' => $e_id,
			'et_rego' => $et_rego
			)
		);
	}

	/**
	 * Gets the details of an attendee
	 *
	 * @param int $at_id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcattendeeget.html
	 * @return array The result array
	 */
	public function attendee_get($at_id)
	{
		return $this->call('eventarc.attendee.get', array(
			'at_id' => $at_id
			)
		);
	}

	/**
	 * Get a list of attendees for a particular event
	 *
	 * @param int $e_id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcattendeelist.html
	 * @return array The result array
	 */
	public function attendee_list($e_id)
	{
		return $this->call('eventarc.attendee.list', array(
			'e_id' => $e_id
			)
		);
	}

	/**
	 * Resend a attendees confirmatin email. If the attendee is not valid or
	 * active then this will fail.
	 *
	 * @param int $at_id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcattendeeresendemail.html
	 * @return array The result array
	 */
	public function attendee_resendemail($at_id)
	{
		return $this->call('eventarc.attendee.resendemail', array(
			'at_id' => $at_id
			)
		);
	}

	/**
	 * Get an events list of tickets
	 *
	 * @param int $e_id The event whose tickets you want
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgettickets.html
	 * @return array The result array
	 */
	public function event_get_tickets($e_id)
	{
		$this->format_params(array('e_id' => $e_id));
		return $this->call('eventarc.event.gettickets');
	}

	/**
	 * Get an events address
	 *
	 * @param int $e_id The event whose address you want
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgetaddress.html
	 * @return array The result array
	 */
	public function event_get_address($e_id)
	{
		$this->format_params(array('e_id' => $e_id));
		return $this->call('eventarc.event.getaddress');
	}

	/**
	 * Set an events address
	 *
	 * @param array $params
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventsetaddress.html
	 * @return array The result array
	 */
	public function event_set_address(array $params)
	{
		return $this->call('eventarc.event.setaddress', $params);
	}

	/**
	 * Get an events show fees status
	 *
	 * @param int $e_id The event
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgetshowfees.html
	 * @return array The result array
	 */
	public function event_get_showfees($e_id)
	{
		$this->format_params(array('e_id' => $e_id));
		return $this->call('eventarc.event.getshowfees');
	}

	/**
	 * Set an events show fees status
	 *
	 * @param int $e_id The event
	 * @param boolean $to_showfees The status you want to set show fees to
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventsetshowfees.html
	 * @return array The result array
	 */
	public function event_set_showfees($e_id, $to_showfees)
	{
		$this->format_params(array('e_id' => $e_id));

		// We need to set to_showfees as '1' or '0'
		if ($to_showfees !== '0' AND $to_showfees !== '1')
		{
			$to_showfees = ($to_showfees) ? '1' : '0';
		}

		$this->format_params(array('to_showfees' => $to_showfees));

		return $this->call('eventarc.event.setshowfees');
	}

	/**
	 * Get an events tracking status
	 *
	 * @param int $e_id The event
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgettracking.html
	 * @return array The result array
	 */
	public function event_get_tracking($e_id)
	{
		$this->format_params(array('e_id' => $e_id));
		return $this->call('eventarc.event.gettracking');
	}

	/**
	 * Set an events show fees status
	 *
	 * @param int $e_id The event
	 * @param boolean $ta_status The status you want to set tracking to
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventsettracking.html
	 * @return array The result array
	 */
	public function event_set_tracking($e_id, $ta_status)
	{
		$this->format_params(array('e_id' => $e_id));

		// We need to set to_ta_status as '1' or '0'
		if ($ta_status !== '0' AND $ta_status !== '1')
		{
			$ta_status = ($ta_status) ? '1' : '0';
		}

		$this->format_params(array('ta_status' => $ta_status));

		return $this->call('eventarc.event.settracking');
	}

	/**
	 * Get an events theme
	 *
	 * @param int $e_id The event whose theme you want
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgettheme.html
	 * @return array The result array
	 */
	public function event_get_theme($e_id)
	{
		$this->format_params(array('e_id' => $e_id));
		return $this->call('eventarc.event.gettheme');
	}

	/**
	 * Get an events edit url
	 *
	 * @param int $e_id The event you would like to edit
	 * @param string $e_returnurl The url you would like to return to after editing your event
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgettheme.html
	 * @return array The result array
	 */
	public function event_get_edit_url($e_id, $e_returnurl)
	{
		$this->format_params(array('e_id' => $e_id, 'e_returnurl' => $e_returnurl));
		return $this->call('eventarc.event.getediturl');
	}

	/**
	 * Get an events tickets pool
	 *
	 * @param int $e_id The event whose tickets pool you want
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgetticketspool.html
	 * @return array The result array
	 */
	public function event_get_tickets_pool($e_id)
	{
		$this->format_params(array('e_id' => $e_id));
		return $this->call('eventarc.event.getticketspool');
	}

	/**
	 * Get a particular event
	 *
	 * @param int $e_id The id of the event to get
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventget.html
	 * @return array The result array
	 */
	public function event_get($e_id)
	{
		return $this->call('eventarc.event.get', array(
			'e_id' => $e_id
			)
		);
	}

	/**
	 * Delete an event
	 *
	 * @param int $e_id The id of the event to delete
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventdelete.html
	 * @return array The result array
	 */
	public function event_delete($e_id)
	{
		return $this->call('eventarc.event.delete', array(
			'e_id' => $e_id
			)
		);
	}

	/**
	 * Get an events full details (address data, ticket data etc.)
	 *
	 * @param int $e_id The id of the event to get
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventgetfull.html
	 * @return array The result array
	 */
	public function event_get_full($e_id)
	{
		return $this->call('eventarc.event.getfull', array(
			'e_id' => $e_id
			)
		);
	}

	/**
	 * Create the event that you have built. See docs for this one.
	 *
	 * @param mixed $params
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return array The result array
	 */
	public function event_create($params=FALSE)
	{
		return $this->call('eventarc.event.create', $params);
	}

	/**
	 * Copies an events STUFF to a new event. BETA BETA
	 *
	 * @param int $e_id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcopy.html
	 * @return array The result array
	 */
	public function event_copy($e_id)
	{
		return $this->call('eventarc.event.copy', array('e_id' => $e_id));
	}

	/**
	 * Update an event
	 *
	 * @param int $e_id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventedit.html
	 * @return array The result array
	 */
	public function event_update(array $params)
	{
		return $this->call('eventarc.event.update', $params);
	}

	/**
	 * Get your list of events.
	 * This returns all of your events. If you want you can provide a status to
	 * filter the events returned. Valid statuses are:
	 * active, pending, draft
	 *
	 * @param string $e_status You can try active, pending, draft or leave it
	 * out and get all of them.
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventlist.html
	 * @return array The result array
	 */
	public function event_list($e_status=FALSE)
	{
		if ($e_status)
		{
			$this->format_params(array('e_status' => $e_status));
		}
		return $this->call('eventarc.event.list');
	}

	public function event_whitelabel($e_id, array $pe_data)
	{
		$this->format_params(array('e_id' => $e_id) + $pe_data);

		return $this->call('eventarc.event.whitelabel');
	}

	/**
	 * Create a discount code
	 *
	 * @param int $e_id The event
	 * @param array $dc_data The data for the discount code (see docs)
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcdiscountcodecreate.html
	 * @return array The result array
	 */
	public function discountcode_create($e_id, $dc_data)
	{
		$this->format_params(array('e_id' => $e_id));
		$this->format_params($dc_data);

		return $this->call('eventarc.discountcode.create');
	}

	/**
	 * Create a group and love it
	 *
	 * @param array $params This contains your group details
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcgroupcreate.html
	 * @return array The result array
	 */
	public function group_create(array $params)
	{
		// If g_parent is not set, set default to 0 (the first group)
		if ( ! array_key_exists('g_parent', $params))
		{
			$params['g_parent'] = 0;
		}
		return $this->call('eventarc.group.create', $params);
	}

	/**
	 * Get a particular group
	 *
	 * @param int $g_id The id of the group to get
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcgroupget.html
	 * @return array The result array
	 */
	public function group_get($g_id)
	{
		return $this->call('eventarc.group.get', array(
			'g_id' => $g_id
			)
		);
	}

	/**
	 * Get a list of your eventarc groups.
	 * Note that these are referred to and displayed as 'folders' in the
	 * myeventarc interface. You know, those folder-y things on the left hand
	 * side?
	 *
	 * Groups are just a convenient way of grouping events.
	 *
	 * @param int $u_id Your user id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcgrouplist.html
	 * @return array The result array
	 */
	public function group_list($u_id=FALSE)
	{
		if ( ! $u_id)
		{
			$u_id = $this->u_id;
		}
		return $this->call('eventarc.group.list', array(
			'u_id' => $u_id
			)
		);
	}

	/**
	 * Create a payment config
	 *
	 * @param array $params The config details
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcpaymentcreateconfig.html
	 * @return array The result array
	 */
	public function payment_createconfig(array $params)
	{
		return $this->call('eventarc.payment.createconfig', $params);
	}

	/**
	 * List all of your payment configs
	 *
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcpaymentlistconfigs.html
	 * @return array The result array
	 */
	public function payment_listconfigs()
	{
		return $this->call('eventarc.payment.listconfigs');
	}

	/**
	 * Assign a payment config to a event
	 *
	 * @param int $pc_id The payment config id
	 * @param int $e_id  The event id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcpaymentassignconfig.html
	 * @return array The result array
	 */
	public function payment_assignconfig($pc_id, $e_id)
	{
		return $this->call('eventarc.payment.assignconfig', array(
			'e_id' => $e_id,
			'pc_id' => $pc_id
			)
		);
	}

	/**
	 * Login to the api and get your apikey. With a bit of luck you should only
	 * need to do this once to grab your apikey.
	 *
	 * @param string $u_name
	 * @param string $u_password
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcuserlogin.html
	 * @return array The result array
	 */
	public function user_login($u_name, $u_password)
	{
		return $this->call('eventarc.user.login', array(
			'u_name' => $u_name,
			'u_password' => $u_password
			)
		);
	}

	/**
	 * Get a users details. Get your own.
	 *
	 * @param int $u_id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcuserget.html
	 * @return array The result array
	 */
	public function user_get($u_id=FALSE)
	{
		if ( ! $u_id)
		{
			$u_id = $this->u_id;
		}
		return $this->call('eventarc.user.get', array(
			'u_id' => $u_id
			)
		);
	}

	/**
	 * Create and add a widget to a event.
	 *
	 * @param array $params
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcwidgetcreate.html
	 * @return array The result array
	 */
	public function widget_create($e_id, array $widget_data)
	{
		$this->add_array_item('wd_data', $widget_data);
		return $this->call('eventarc.widget.create', array(
			'e_id' => $e_id
			)
		);
	}

	/**
	 * Get a list of your images.
	 *
	 * @param int $u_id Your user id
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcimagelist.html
	 * @return array The result array
	 */
	public function image_list($u_id=FALSE)
	{
		if ( ! $u_id)
		{
			$u_id = $this->u_id;
		}
		return $this->call('eventarc.image.list', array(
			'u_id' => $u_id
			)
		);
	}

	/**
	 * Upload an image to Eventarc. You can then use these images in various
	 * spots. This is still BETA functionality. It may change.
	 *
	 * TODO Wrap this up into 'send_payload'
	 *
	 * @param mixed $image The image (path) to upload
	 * @param string $i_name The name of the image (for your benefit)
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarcimageupload.html
	 * @return array The result array
	 */
	public function image_upload($image, $i_name='')
	{
		// Make sure the image is valid
		if ( ! is_readable($image))
		{
			// Thats not a file
			throw new Eventarcapi_Exception(
				'Invalid input. The image path supplied is not valid.', 126);
		}

		// Create the payload
		$this->setup_u_data();
		$this->method = 'eventarc.image.upload';
		$this->params['i_data'] = array();
		$this->params['i_data']['i_name'] = $i_name;
		$image = '@'.$image;

		// Prepare the payload
		$payload = array(
			'jsonrpc' => '2.0',
			'method' => $this->method,
			'id' => time(),
			'params' => $this->params
		);

		// Convert payload to JSON
		if (($json_payload = json_encode($payload)) === NULL)
		{
			// JSON encode failed
			throw new Eventarcapi_Exception(
				'We were unable to encode the data into JSON', 123);
		}

		// NOW place the payload in a POST var
		$upload_payload = array(
			'image' => $image,
			'request' => $json_payload);

		// Send the payload and wait for a response
		$ch = curl_init();

		// Set URL and other appropriate options
		// NOTE: this is using different curl options then send_payload
		curl_setopt($ch, CURLOPT_URL, $this->server);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $upload_payload);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// Grab URL, and print
		$response = curl_exec($ch);

		if (curl_errno($ch) > 0)
		{
			// Error
			throw new Eventarcapi_Exception(
				'Failed to contact server: '.print_r(curl_error($ch), TRUE),
				123);
		}

		curl_close($ch);

		// Process the response
		return $this->process_response($response);
	}

	/**
	 * NOTE: The following 'add_*' functions should only be used when creating
	 * an event. Check out the documentation for eventarc.event.create to see
	 * how they work. They should return 'this' so they can be chained easily.
	 *
	 * add_event
	 * add_address
	 * add_widget
	 * add_ticket
	 * add_theme
	 * add_ticket_limit
	 * add_ticket_show_fees
	 */

	/**
	 * Add event data  (see the docs for how to use this..)
	 *
	 * @param array $event_data Thats an array of event data
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_event(array $event_data)
	{
		$this->format_params($event_data);
		return $this;
	}

	/**
	 * Add address data  (see the docs for how to use this..)
	 *
	 * @param array $address_data Thats an array of address data
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_address(array $address_data)
	{
		$this->format_params($address_data);
		return $this;
	}

	/**
	 * Add widget data  (see the docs for how to use this..)
	 *
	 * @param array $widget_data Thats an array of widget data
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_widget(array $widget_data)
	{
		$this->add_array_item('wd_data', $widget_data);
		return $this;
	}

	/**
	 * Add ticket data  (see the docs for how to use this..)
	 *
	 * @param array $ticket_data Thats an array of ticket data
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_ticket(array $ticket_data)
	{
		$this->add_array_item('t_data', $ticket_data);
		return $this;
	}

	/**
	 * Add theme data  (see the docs for how to use this..)
	 *
	 * @param array $theme_data Thats an array of theme data
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_theme(array $theme_data)
	{
		$this->format_params($theme_data);
		return $this;
	}

	/**
	 * Add a ticket limit (see the docs for this one)
	 *
	 * @param int $to_total
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_ticket_limit($to_total)
	{
		$this->format_params(array('to_total' => $to_total));
		return $this;
	}

	/**
	 * Add a ticket limit (see the docs for this one)
	 *
	 * @param int $to_total
	 * @access public
	 * @link http://api.eventarc.com/docs/eventarceventcreate.html
	 * @return Eventarc Returns this class so you can chain, chain, chain.
	 */
	public function add_ticket_show_fees($show_fees=FALSE)
	{
		$this->format_params(array('to_showfees' => ($show_fees)?1:0));
		return $this;
	}

	/**
	 * Call a eventarc API method
	 *
	 * @param string $method The method to call. I'd make sure it was valid.
	 * @param mixed $data The data to send to the method.
	 * @access public
	 * @return array The result array
	 */
	public function call($method_in, $data = FALSE)
	{
		if ( ! is_string($method_in))
		{
			// The method must be a string
			throw new Eventarcapi_Exception(
				'Invalid method called. Try using a string.', 123);
		}

		$this->method = $method_in;

		// Format the params if any given
		if ( ! is_array($data))
		{
			$data = array();
		}

		// Add the $data to the params
		$this->format_params($data);

		// Call the api
		return $this->send_payload();
	}

	/**
	 * Add array item to the internal params.
	 *
	 * @param string $key
	 * @param array $item
	 * @access private
	 * @return void
	 */
	private function add_array_item($key, array $item)
	{
		if ( ! array_key_exists($key, $this->params))
		{
			$this->params[$key] = array();
		}
		$this->params[$key][] = $item;
	}

	/**
	 * Take the given data and convert it into the correct format. The basic
	 * syntax is to put variables into their own 'data' array.
	 *
	 * eg. u_forename would go in the u_data array.
	 *
	 * If there is a digit as the first character then that is indicating that
	 * the variables are par tof an array.
	 *
	 * eg. 0_t_name, 1_t_name, 2_t_name would result in:
	 * t_data = array ( array (t_name), array(t_name), array(t_name))
	 *
	 * If there is a error with an item from the array (ie. it does not have a
	 * underscore to indicate its data array), then it is put in at the root of
	 * the array as is. (GIGO)
	 *
	 * Once the data has been converted it is added to $this->params
	 *
	 * @param array $data The data to format
	 * @access private
	 * @return void
	 */
	private function format_params(array $data)
	{
		foreach ($data as $key=>$value)
		{
			// All keys are lowercase
			$key = strtolower($key);

			// Split key on underscore
			$split = explode('_', $key);

			// Check for a key that is not an array or data set
			if (count($split) === 1)
			{
				// This item is not an array or in a data array, so send it as
				//  it is
				$this->params[$key] = $value;
				continue;
			}

			// Check for a a-z character(s) which indicates we are making a data
			//  array for this key
			// eg. t_name
			if (ctype_lower($split[0]))
			{
				$data_name = $split[0].'_data';

				// Make a data array for this key, but check to see if one
				//  already exists
				if ( ! array_key_exists($data_name, $this->params))
				{
					$this->params[$data_name] = array();
				}

				// Add the data
				$this->params[$data_name][$key] = $value;
				continue;
			}

			// Check for an array
			// eg. 5_t_name
			if (count($split) === 3
				AND ctype_digit($split[0])
				AND ctype_lower($split[1]))
			{
				// Check if the key already exists
				$data_name = $split[1].'_data';
				$index = $split[0];
				$new_key = $split[1].'_'.$split[2];

				if ( ! array_key_exists($data_name, $this->params))
				{
					$this->params[$data_name] = array();
				}

				// Check to see if the array item already exists
				if ( ! isset($this->params[$data_name][$index]))
				{
					$this->params[$data_name][$index] = array();
				}

				$this->params[$data_name][$index][$new_key] = $value;
			}
		}

		// Setup the u_data business (apikey etc.)
		$this->setup_u_data();
	}

	private function setup_u_data()
	{
		// If they have set the api key and u_id (in the constructor or
		//  directly) then add these to the params too
		if ($this->u_id OR $this->u_name OR $this->u_apikey)
		{
			// Do not overwrite them
			if ( ! array_key_exists('u_data', $this->params))
			{
				$this->params['u_data'] = array();
			}

			if ($this->u_id AND ! array_key_exists('u_id', $this->params['u_data']))
			{
				$this->params['u_data']['u_id'] = $this->u_id;
			}

			if ($this->u_name AND ! array_key_exists('u_name', $this->params['u_data']))
			{
				$this->params['u_data']['u_name'] = $this->u_name;
			}

			if ($this->u_apikey AND ! array_key_exists('u_apikey', $this->params['u_data']))
			{
				$this->params['u_data']['u_apikey'] = $this->u_apikey;
			}
		}
	}

	/**
	 * Send the payload!
	 *
	 * @access private
	 * @return array The reponse!
	 */
	private function send_payload()
	{
		// Prepare the payload
		$payload = array(
			'jsonrpc' => '2.0',
			'method' => $this->method,
			'id' => time(),
			'params' => $this->params
		);

		// Convert payload to JSON
		if (($json_payload = json_encode($payload)) === NULL)
		{
			// JSON encode failed
			throw new Eventarcapi_Exception(
				'We were unable to encode the data into JSON', 123);
		}

		// If we are recording the history, then... record it
		if ($this->keep_history)
		{
			$this->history[$this->method] = array(
				'method'		=> $this->method,
				'params'		=> $this->params,
				'payload'		=> $payload,
				'json_payload'	=> $json_payload
			);
		}

		// Save the payload(s)
		$this->last_payload = $payload;
		$this->last_json_payload = $json_payload;

		// Wipe the params
		$this->params = array();

		// Send the payload and wait for a response
		$ch = curl_init();

		// Set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $this->server);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FILETIME, TRUE);

		// Grab URL, and print
		$response = curl_exec($ch);

		if (curl_errno($ch) > 0)
		{
			// Error
			throw new Eventarcapi_Exception(
				'Failed to contact server: '.print_r(curl_error($ch), TRUE),
				123);
		}

		curl_close($ch);

		// Process the response
		return $this->process_response($response);
	}

	/**
	 * This processes the response from the API
	 *
	 * @param string $response This should be JSON
	 * @access private
	 * @return array The result
	 */
	private function process_response($response)
	{
		// The response should be valid JSON
		$decoded = json_decode($response, TRUE);

		// Check for JSON errors
		switch (json_last_error())
		{
			case JSON_ERROR_NONE:
				// The JSON is valid
				$this->response = $decoded;
				break;
			case JSON_ERROR_DEPTH:
				throw new Eventarcapi_Exception(
					'JSON nested too deep', 400);
				break;
			case JSON_ERROR_CTRL_CHAR:
				throw new Eventarcapi_Exception(
					'Invalid control character in JSON', 400);
				break;
			case JSON_ERROR_SYNTAX:
				// HOW did you DO this?!
				var_dump($this->history);
				die($response);
				throw new Eventarcapi_Exception(
					'Invalid JSON syntax', 400);
				break;
			case JSON_ERROR_UTF8:
				throw new Eventarcapi_Exception(
					'Incorrect character encoding', 400);
				break;
			default:
				throw new Eventarcapi_Exception(
					'Unknown JSON error', 400);
				break;
		}

		// If we are recording the history, then... record it
		if ($this->keep_history)
		{
			$this->history[$this->method]['response'] = $decoded;
			$this->history[$this->method]['json_response'] = $response;
		}

		// Check to see if the request was successful
		if (array_key_exists('error', $this->response))
		{
			// Store the error
			$this->error = $this->response['error'];

			// There was an error, throw an exception
			throw new Eventarcapi_Exception(
				$this->response['error']['message'],
				$this->response['error']['code']);
		}

		// We should now have a result object with the results within
		if ( ! array_key_exists('result', $this->response))
		{
			// The JSON is not valid as there either has to be a error object or
			//  a result object
			throw new Eventarcapi_Exception(
				'The response was not valid JSON-RPC.', 123);
		}

		// Return the result
		return $this->response['result'];
	}
}

/**
 * Eventarcapi_Exception
 *
 * @uses Exception
 * @package Eventarcapi
 * @copyright 2009-2011 Eventarc
 */
class Eventarcapi_Exception extends Exception {}
