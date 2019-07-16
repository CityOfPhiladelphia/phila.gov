<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler;

use RollbarNotifier;
use Exception;
use DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger;
/**
 * Sends errors to Rollbar
 *
 * If the context data contains a `payload` key, that is used as an array
 * of payload options to RollbarNotifier's report_message/report_exception methods.
 *
 * Rollbar's context info will contain the context + extra keys from the log record
 * merged, and then on top of that a few keys:
 *
 *  - level (rollbar level name)
 *  - monolog_level (monolog level name, raw level, as rollbar only has 5 but monolog 8)
 *  - channel
 *  - datetime (unix timestamp)
 *
 * @author Paul Statezny <paulstatezny@gmail.com>
 */
class RollbarHandler extends \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\AbstractProcessingHandler
{
    /**
     * Rollbar notifier
     *
     * @var RollbarNotifier
     */
    protected $rollbarNotifier;
    protected $levelMap = array(\DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::DEBUG => 'debug', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::INFO => 'info', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::NOTICE => 'info', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::WARNING => 'warning', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::ERROR => 'error', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::CRITICAL => 'critical', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::ALERT => 'critical', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::EMERGENCY => 'critical');
    /**
     * Records whether any log records have been added since the last flush of the rollbar notifier
     *
     * @var bool
     */
    private $hasRecords = false;
    protected $initialized = false;
    /**
     * @param RollbarNotifier $rollbarNotifier RollbarNotifier object constructed with valid token
     * @param int             $level           The minimum logging level at which this handler will be triggered
     * @param bool            $bubble          Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(\RollbarNotifier $rollbarNotifier, $level = \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::ERROR, $bubble = true)
    {
        $this->rollbarNotifier = $rollbarNotifier;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (!$this->initialized) {
            // __destructor() doesn't get called on Fatal errors
            register_shutdown_function(array($this, 'close'));
            $this->initialized = true;
        }
        $context = $record['context'];
        $payload = array();
        if (isset($context['payload'])) {
            $payload = $context['payload'];
            unset($context['payload']);
        }
        $context = array_merge($context, $record['extra'], array('level' => $this->levelMap[$record['level']], 'monolog_level' => $record['level_name'], 'channel' => $record['channel'], 'datetime' => $record['datetime']->format('U')));
        if (isset($context['exception']) && $context['exception'] instanceof Exception) {
            $payload['level'] = $context['level'];
            $exception = $context['exception'];
            unset($context['exception']);
            $this->rollbarNotifier->report_exception($exception, $context, $payload);
        } else {
            $this->rollbarNotifier->report_message($record['message'], $context['level'], $context, $payload);
        }
        $this->hasRecords = true;
    }
    public function flush()
    {
        if ($this->hasRecords) {
            $this->rollbarNotifier->flush();
            $this->hasRecords = false;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->flush();
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->flush();
        parent::reset();
    }
}
