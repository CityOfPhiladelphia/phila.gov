<?php

namespace DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Node;

/**
 * Description
 */
class Variable extends \DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Node\Abstraction
{
    /**
     * Variable name without modifier 
     * e.g. 'term:1' becomes 'term'
     */
    public $name, $options = array('modifier' => null, 'value' => null);
    public function __construct($token, array $options = array())
    {
        parent::__construct($token);
        $this->options = $options + $this->options;
        // normalize var name e.g. from 'term:1' becomes 'term'
        $name = $token;
        if ($options['modifier'] === ':') {
            $name = substr($name, 0, strpos($name, $options['modifier']));
        }
        $this->name = $name;
    }
}
