<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Parser\Exception;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\HasMonitoringEventsTrait;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\MonitoringEventsInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\ResponseContainerInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Psr\Http\Message\ResponseInterface;
class ParserException extends \RuntimeException implements \DeliciousBrains\WP_Offload_Media\Aws3\Aws\MonitoringEventsInterface, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\ResponseContainerInterface
{
    use HasMonitoringEventsTrait;
    private $errorCode;
    private $requestId;
    private $response;
    public function __construct($message = '', $code = 0, $previous = null, array $context = [])
    {
        $this->errorCode = isset($context['error_code']) ? $context['error_code'] : null;
        $this->requestId = isset($context['request_id']) ? $context['request_id'] : null;
        $this->response = isset($context['response']) ? $context['response'] : null;
        parent::__construct($message, $code, $previous);
    }
    /**
     * Get the error code, if any.
     *
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    /**
     * Get the request ID, if any.
     *
     * @return string|null
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
    /**
     * Get the received HTTP response if any.
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
