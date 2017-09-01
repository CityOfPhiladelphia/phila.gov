<?php

class wpcmsb_Integration {

	private static $instance;

	private $services = array();
	private $categories = array();

	private function __construct() {}

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function add_service( $name, wpcmsb_Service $service ) {
		$name = sanitize_key( $name );

		if ( empty( $name ) || isset( $this->services[$name] ) ) {
			return false;
		}

		$this->services[$name] = $service;
	}

	public function add_category( $name, $title ) {
		$name = sanitize_key( $name );

		if ( empty( $name ) || isset( $this->categories[$name] ) ) {
			return false;
		}

		$this->categories[$name] = $title;
	}

	public function service_exists( $name = '' ) {
		if ( '' == $name ) {
			return (bool) count( $this->services );
		} else {
			return isset( $this->services[$name] );
		}
	}

	public function get_service( $name ) {
		if ( $this->service_exists( $name ) ) {
			return $this->services[$name];
		} else {
			return false;
		}
	}

	public function list_services( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'include' => array() ) );

		$services = (array) $this->services;

		if ( ! empty( $args['include'] ) ) {
			$services = array_intersect_key( $services,
				array_flip( (array) $args['include'] ) );
		}

		foreach ( $services as $name => $service ) {
			$cats = array_intersect_key( $this->categories,
				array_flip( $service->get_categories() ) );
?>
<div class="card<?php echo $service->is_active() ? ' active' : ''; ?>" id="<?php echo esc_attr( $name ); ?>">
<h3 class="title"><?php echo esc_html( $service->get_title() ); ?></h3>
<div class="infobox">
<?php echo esc_html( implode( ', ', $cats ) ); ?>
<br />
<?php $service->link(); ?>
</div>
<br class="clear" />

<div class="inside">
<?php $service->display(); ?>
</div>
</div>
<?php
		}
	}

}

abstract class wpcmsb_Service {

	public function get_title() {
	}

	public function is_active() {
	}

	public function get_categories() {
	}

	public function link() {
	}

	public function load( $action = '' ) {
	}

	public function display() {
	}

	public function admin_notice() {
	}

}
