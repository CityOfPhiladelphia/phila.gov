<?php

class PHL_AQI {

  public $errors = [];
  public $airnow_quality = '';
  public $airnow = [
    'url' => 'http://www.airnowapi.org/aq/observation/zipCode/current/?format=application/json',
    'params' => [
      'zipCode' => '19107',
      'date' => '',
      'distance' => '25',
      'API_KEY' => '',
    ]
  ];
  public $airnow_quality_scale = [
    [
      'label' => 'Good',
      'range' => '0 to 50',
      'desc' => 'Air quality is satisfactory, and air pollution poses little or no risk.',
      'color' => [
        'name' => 'Green',
      ]
    ],
    [
      'label' => 'Moderate',
      'range' => '51 to 100',
      'desc' => 'Air quality is acceptable. However, there may be a risk for some people, particularly those who are unusually sensitive to air pollution.',
      'color' => [
        'name' => 'Yellow',
      ]
    ],
    [
      'label' => 'Unhealthy for Sensitive Groups',
      'range' => '101 to 150',
      'desc' => 'Members of sensitive groups may experience health effects. The general public is less likely to be affected.',
      'color' => [
        'name' => 'Orange',
      ]
    ],
    [
      'label' => 'Unhealthy',
      'range' => '151 to 200',
      'desc' => 'Some members of the general public may experience health effects. Members of sensitive groups may experience more serious health effects.',
      'color' => [
        'name' => 'Red',
      ]
    ],
    [
      'label' => 'Very Unhealthy',
      'range' => '201 to 300',
      'desc' => 'Health alert: The risk of health effects is increased for everyone.',
      'color' => [
        'name' => 'Purple',
      ]
    ],
    [
      'label' => 'Hazardous',
      'range' => '301 and higher',
      'desc' => 'Health warning of emergency conditions: everyone is more likely to be affected.',
      'color' => [
        'name' => 'Maroon',
      ]
    ]
  ];

  function __construct() {

    $this->set_aqi_key();
    add_shortcode( 'phl-aqi', array($this, 'add_aqi_shortcode') );
    add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
    $this->log_errors();

  }

  function set_aqi_key() {
    if (!defined('AQI_KEY')) {
      $this->errors[] = 'AQI_KEY is not defined.';
      return;
    }

    $this->airnow['params']['API_KEY'] = AQI_KEY;

  }

  function log_errors() {
    if (!empty($this->errors)) {
      foreach($this->errors as $err) {
        error_log($err, 0);
      }
    }
  }


    function register_scripts() {
      $page_id = get_the_ID();
      if ($page_id  ===  13050 ){
        wp_enqueue_script('highcharts-core', '//code.highcharts.com/6.2.0/highcharts.js', null, '', true);
        wp_enqueue_script('highcharts-more', '//code.highcharts.com/6.2.0/highcharts-more.js', null, '', true);
        wp_enqueue_script('phl-aqi-init', plugins_url('', __FILE__ ) . '/js/phl-aqi.js', 'jquery', '0.2', true);
        wp_enqueue_style('aqi-css', plugins_url('', __FILE__ ) . '/css/phl-aqi.css', null, '0.2', 'all');
      }
    }

  function add_aqi_shortcode() {

    $this->get_aqi();

    $localize = array(
      'aqi' => $this->airnow_quality,
    );

    wp_localize_script( 'phl-aqi-init', 'local', $localize );

    return $this->aqi_template();

  }

  function get_aqi() {

    if (!empty($this->errors)) {
      return;
    }

    //Set date paramater to today
    $this->airnow['params']['date'] = date('Y-m-d');

    //make request url
    $request_url = $this->airnow['url'];

    foreach ($this->airnow['params'] as $param_key => $param_value) {
      if (empty($param_value)) {
        $this->errors[] = 'Airnow param ' . $param_key . ' is empty';
        return;
      }
      $request_url .= '&' . $param_key . '=' . $param_value;
    }
    //make request
    $airnow_request = wp_remote_get($request_url);

    //Catchs curl timeout errors
    if (is_wp_error($airnow_request)) {
      if ($airnow_request->get_error_messages()) {
        foreach($airnow_request->get_error_messages() as $err) {
          $this->errors[] = 'WP_Error: ' . $err;
          return;
        }
      }
    }

    if ($airnow_request['response']['code'] !== 200 || !isset($airnow_request['body'])) {
      $this->errors[] = 'Airnow request failed with code ' . $airnow_request['response']['code'];
      return;
    }

    $response = json_decode($airnow_request['body']);

    //Format date
    $response[1]->parsedDate = date('M. j, Y g:i a', strtotime($response[1]->DateObserved . ' ' . $response[1]->HourObserved . ' hours'));

    $this->airnow_quality = $response[1];
  }

  function aqi_template() {
    ob_start();
    $scale = $this->airnow_quality_scale;
    include('aqi-template.php');
    $template = ob_get_contents();
    ob_end_clean();
    return $template;
  }


}


?>
