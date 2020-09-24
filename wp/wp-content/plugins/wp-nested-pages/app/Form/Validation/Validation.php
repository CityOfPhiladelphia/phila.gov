<?php 
namespace NestedPages\Form\Validation;

use NestedPages\Config\SettingsRepository;

/**
* Nested Pages Form Validation
*/
class Validation 
{
	/**
	* Settings Repository
	*/
	private $settings;

	public function __construct()
	{
		$this->settings = new SettingsRepository;
	}

	/**
	* Validate Post IDs in an array of posts
	*/
	public function validatePostIDs($posts)
	{
		foreach ($posts as $post)
		{
			if ( !is_numeric($post['id']) ){
				return wp_send_json(['status'=>'error', 'message'=>'Incorrect Form Field']);
			}
		}
	}

	/**
	* Validate IDs in an array (tax ids)
	*/
	public function validateIntegerArray($items)
	{
		foreach ( $items as $item )
		{
			if ( !is_numeric($item) ){
				return wp_send_json(['status'=>'error', 'message'=>'Incorrect Form Field']);
			}
		}
	}

	/**
	* Validate Date Input whether default WP or NP Datepicker
	*/
	public function validateDate($data)
	{
		if ( $this->settings->datepickerEnabled() ) return $this->validateDatepicker($data);
		return $this->validateWPDate($data);
	}

	/**
	* Validate Datepicker date and time
	* @since 1.3.1
	* @return string - formatted date
	*/
	private function validateDatepicker($data)
	{
		// Make sure fields are filled out
		if ( $data['np_date'] == "" || $data['np_time'] == "" ) return $this->sendDateError();
		
		$time_format = get_option('time_format');
		if ( $time_format == 'H:i' ){
			$this->checkValidTime($data['np_time']);
		} else {
			$this->checkValidFormattedTime($data['np_time']);
		}

		$date = date('Y-m-d H:i:s', strtotime($data['np_date'] . ' ' . $data['np_time'] . ' ' . $data['np_ampm']));
		if ( $date ==  '1970-01-01 00:00:00' ) return $this->sendDateError();
		return $date;
	}

	/**
	* Validate Default WP Date Fields
	* @since 1.3.1
	* @return string - formatted date
	*/
	private function validateWPDate($data)
	{
		$this->checkValidDate($data['mm'], $data['jj'], $data['aa']);
		$this->checkValidTime($data['hh'] . ':' . $data['mn']);

		// Validate all the fields are there
		if ( ($data['aa'] !== "") 
			&& ( $data['mm'] !== "" )
			&& ( $data['jj'] !== "" )
			&& ( $data['hh'] !== "" )
			&& ( $data['mm'] !== "" )
			&& ( $data['ss'] !== "" ) )
		{
			$date = strtotime($data['aa'] . '-' . $data['mm'] . '-' . $data['jj'] . ' ' . $data['hh'] . ':' . $data['mn'] . ':' . $data['ss']);
			return date('Y-m-d H:i:s', $date);
		}
		
		return $this->sendDateError();
		
	}

	/**
	* Check valid date
	*/
	private function checkValidDate($month, $day, $year)
	{
		if ( !wp_checkdate(intval($month), intval($day), intval($year),	$year . '-' . $month . '-' . $day))	return $this->sendDateError();
	}

	/**
	* Check for Valid 24 hour Time
	*/
	private function checkValidTime($time)
	{
		if (!preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $time)) return $this->sendDateError();
	}

	/**
	* Check for Valid 12 hour Time
	*/
	private function checkValidFormattedTime($time)
	{
		if (!preg_match("/^(1[0-2]|0?[1-9]):[0-5][0-9]/", $time)) return $this->sendDateError();
	}

	/**
	* Send Date Error
	* @return response
	*/
	private function sendDateError()
	{
		wp_send_json([
			'status' => 'error', 
			'message' => __('Please provide a valid date.', 'wp-nested-pages') 
		]);
		die();
	}

	/**
	* Validate new redirect/link fields
	*/
	public function validateRedirect($data)
	{
		if ( (!isset($data['np_link_title'])) || ($data['np_link_title'] == "") ){
			return wp_send_json(array('status' => 'error', 'message' => __('Please provide a menu title.', 'wp-nested-pages') ));
		}
		if ( (!isset($data['np_link_content'])) || ($data['np_link_content'] == "") ){
			return wp_send_json(array('status' => 'error', 'message' => __('Please provide a valid URL.', 'wp-nested-pages') ));
		}
	}

	/**
	* Validate a string isn't empty
	*/
	public function checkEmpty($var, $title)
	{
		if ( $var == "" ){
			$message = __('Please provide a ', 'wp-nested-pages') . $title;
			return wp_send_json(['status' => 'error', 'message' => $message]);
			die();
		}
	}

	/**
	* Validate New Pages
	*/
	public function validateNewPages($data)
	{
		// Check for Parent ID or Before/After IDs
		if ( !isset($data['before_id']) && !isset($data['after_id']) ){
			if ( (!isset($data['parent_id'])) || (!is_numeric($data['parent_id'])) ){
				$message = __('A valid parent page was not provided.', 'wp-nested-pages');
				return wp_send_json(['status' => 'error', 'message' => $message]);
				die();
			}
		}

		if ( isset($data['before_id']) && $data['before_id'] !== '' && !is_numeric($data['before_id']) ){
			$message = __('A valid parent page was not provided to insert before.', 'wp-nested-pages');
			return wp_send_json(['status' => 'error', 'message' => $message, 'data' => $data]);
			die();
		}

		if ( isset($data['after_id']) && $data['after_id'] !== '' && !is_numeric($data['after_id']) ){
			$message = __('A valid parent page was not provided to insert after.', 'wp-nested-pages');
			return wp_send_json(['status' => 'error', 'message' => $message, 'data' => $data]);
			die();
		}

		// Make sure there's at least one page
		if ( !isset($data['post_title']) ){
			$message = __('Please provide at least one page title.', 'wp-nested-pages');
			return wp_send_json(['status' => 'error', 'message' => $message]);
			die();
		}

		// Page fields cannot be blank
		foreach ( $data['post_title'] as $title ){
			if ( $title == "" ){
				$message = __('Page titles cannot be blank.', 'wp-nested-pages');
				return wp_send_json(['status' => 'error', 'message' => $message, 'data' => $data]);
				die();
			}
		}

		return true;
	}

	/**
	* Validate Custom Fields
	*/
	public function validateCustomFields($data)
	{
		$post_type = get_post_type_object($data['post_type']);
		$fields_left = apply_filters('nestedpages_quickedit_custom_fields', [], $post_type, 'left');
		$fields_right = apply_filters('nestedpages_quickedit_custom_fields', [], $post_type, 'right');
		$fields = array_merge($fields_left, $fields_right);
		if ( empty($fields) ) return true;
		foreach ( $fields as $field ){
			if ( !isset($field['required']) || !$field['required'] ) continue;
			$field_name = 'np_custom_' . $field['key'];
			if ( !isset($data[$field_name]) || $data[$field_name] == '' ){
				$message = ( isset($field['validation_message']) && $field['validation_message'] !== '' ) 
					? $field['validation_message']
					: sprintf(__('%s is required.', 'wp-nested-pages'), $field['label']);
				return wp_send_json(['status' => 'error', 'message' => $message, 'data' => $data]);
				die();
			}
		}
		return true;
	}
}