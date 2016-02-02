<?php

/**
 * Class MC4WP_Queue
 *
 * @ignore
 */
class MC4WP_Queue {

	/**
	 * @var MC4WP_Queue_Job[]
	 */
	protected $jobs;

	/**
	 * @var string
	 */
	protected $option_name;

	/**
	 * @var bool
	 */
	protected $dirty = false;

	/**
	 * MC4WP_Ecommerce_Queue constructor.
	 *
	 * @param string $option_name
	 */
	public function __construct( $option_name ) {
		$this->option_name = $option_name;

		register_shutdown_function( array( $this, 'save' ) );
	}

	/**
	 * Load jobs from option
	 */
	protected function load() {
		$jobs = get_option( $this->option_name, array() );

		if( ! is_array( $jobs ) ) {
			$jobs = array();
		}

		$this->jobs = $jobs;
	}

	/**
	 * Get all jobs in the queue
	 *
	 * @return MC4WP_Queue_Job[] Array of jobs
	 */
	public function all() {

		if( is_null( $this->jobs ) ) {
			$this->load();
		}

		return $this->jobs;
	}

	/**
	 * Add job to queue
	 *
	 * @param mixed $data
	 */
	public function put( $data ) {

		if( is_null( $this->jobs ) ) {
			$this->load();
		}

		$job = new MC4WP_Queue_Job( $data );
		$this->jobs[] = $job;
		$this->dirty = true;
	}

	/**
	 * Get all jobs in the queue
	 *
	 * @return MC4WP_Queue_Job|false
	 */
	public function get() {

		if( is_null( $this->jobs ) ) {
			$this->load();
		}

		// do we have jobs?
		if( count( $this->jobs ) === 0 ) {
			return false;
		}

		// return first element
		return reset( $this->jobs );
	}

	/**
	 * @param MC4WP_Queue_Job $job
	 */
	public function delete( MC4WP_Queue_Job $job ) {

		if( is_null( $this->jobs ) ) {
			$this->load();
		}

		$index = array_search( $job, $this->jobs, true );

		// check for "false" here, as 0 is a valid index.
		if( $index !== false ) {
			unset( $this->jobs[ $index ] );
			$this->jobs = array_values( $this->jobs );
			$this->dirty = true;
		}
	}

	/**
	 * Reset queue
	 */
	public function reset() {
		$this->jobs = array();
		$this->dirty = true;
	}

	/**
	 * Save the queue
	 */
	public function save() {

		if( ! $this->dirty || is_null( $this->jobs ) ) {
			return false;
		}

		$success = update_option( $this->option_name, $this->jobs );

		if( $success ) {
			$this->dirty = false;
		}

		return $success;
	}


}