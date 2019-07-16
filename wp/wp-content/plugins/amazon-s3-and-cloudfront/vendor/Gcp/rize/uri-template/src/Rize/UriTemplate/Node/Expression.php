<?php

namespace DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Node;

use DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Parser;
use DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Operator;
/**
 * Description
 */
class Expression extends \DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Node\Abstraction
{
    /**
     * @var Operator\Abstraction
     */
    private $operator;
    /**
     * @var array
     */
    private $variables = array();
    /**
     * Whether to do a forward lookup for a given separator
     * @var string
     */
    private $forwardLookupSeparator;
    public function __construct($token, \DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Operator\Abstraction $operator, array $variables = null, $forwardLookupSeparator = null)
    {
        parent::__construct($token);
        $this->operator = $operator;
        $this->variables = $variables;
        $this->forwardLookupSeparator = $forwardLookupSeparator;
    }
    /**
     * @return Operator\Abstraction
     */
    public function getOperator()
    {
        return $this->operator;
    }
    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
    /**
     * @return string
     */
    public function getForwardLookupSeparator()
    {
        return $this->forwardLookupSeparator;
    }
    /**
     * @param string $forwardLookupSeparator
     */
    public function setForwardLookupSeparator($forwardLookupSeparator)
    {
        $this->forwardLookupSeparator = $forwardLookupSeparator;
    }
    /**
     * @param Parser $parser
     * @param array $params
     * @return null|string
     */
    public function expand(\DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Parser $parser, array $params = array())
    {
        $data = array();
        $op = $this->operator;
        // check for variable modifiers
        foreach ($this->variables as $var) {
            $val = $op->expand($parser, $var, $params);
            // skip null value
            if (!is_null($val)) {
                $data[] = $val;
            }
        }
        return $data ? $op->first . implode($op->sep, $data) : null;
    }
    /**
     * Matches given URI against current node
     *
     * @param Parser $parser
     * @param string $uri
     * @param array  $params
     * @param bool $strict
     * @return null|array `uri and params` or `null` if not match and $strict is true
     */
    public function match(\DeliciousBrains\WP_Offload_Media\Gcp\Rize\UriTemplate\Parser $parser, $uri, $params = array(), $strict = false)
    {
        $op = $this->operator;
        // check expression operator first
        if ($op->id && $uri[0] !== $op->id) {
            return array($uri, $params);
        }
        // remove operator from input
        if ($op->id) {
            $uri = substr($uri, 1);
        }
        foreach ($this->sortVariables($this->variables) as $var) {
            /** @var \Rize\UriTemplate\Node\Variable $regex */
            $regex = '#' . $op->toRegex($parser, $var) . '#';
            $val = null;
            // do a forward lookup and get just the relevant part
            $remainingUri = '';
            $preparedUri = $uri;
            if ($this->forwardLookupSeparator) {
                $lastOccurrenceOfSeparator = stripos($uri, $this->forwardLookupSeparator);
                $preparedUri = substr($uri, 0, $lastOccurrenceOfSeparator);
                $remainingUri = substr($uri, $lastOccurrenceOfSeparator);
            }
            if (preg_match($regex, $preparedUri, $match)) {
                // remove matched part from input
                $preparedUri = preg_replace($regex, '', $preparedUri, $limit = 1);
                $val = $op->extract($parser, $var, $match[0]);
            } else {
                if ($strict) {
                    return null;
                }
            }
            $uri = $preparedUri . $remainingUri;
            $params[$var->getToken()] = $val;
        }
        return array($uri, $params);
    }
    /**
     * Sort variables before extracting data from uri.
     * We have to sort vars by non-explode to explode.
     *
     * @param array $vars
     * @return array
     */
    protected function sortVariables(array $vars)
    {
        usort($vars, function ($a, $b) {
            return $a->options['modifier'] >= $b->options['modifier'] ? 1 : -1;
        });
        return $vars;
    }
}
