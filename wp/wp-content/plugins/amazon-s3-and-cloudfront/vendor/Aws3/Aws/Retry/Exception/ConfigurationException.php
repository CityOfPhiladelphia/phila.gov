<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Retry\Exception;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\HasMonitoringEventsTrait;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with retry configuration
 */
class ConfigurationException extends \RuntimeException implements \DeliciousBrains\WP_Offload_Media\Aws3\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
