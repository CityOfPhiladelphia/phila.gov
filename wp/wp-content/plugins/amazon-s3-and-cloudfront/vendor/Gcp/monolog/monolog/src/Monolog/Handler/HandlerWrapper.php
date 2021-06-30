<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler;

use DeliciousBrains\WP_Offload_Media\Gcp\Monolog\ResettableInterface;
use DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Formatter\FormatterInterface;
/**
 * This simple wrapper class can be used to extend handlers functionality.
 *
 * Example: A custom filtering that can be applied to any handler.
 *
 * Inherit from this class and override handle() like this:
 *
 *   public function handle(array $record)
 *   {
 *        if ($record meets certain conditions) {
 *            return false;
 *        }
 *        return $this->handler->handle($record);
 *   }
 *
 * @author Alexey Karapetov <alexey@karapetov.com>
 */
class HandlerWrapper implements \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\HandlerInterface, \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\ProcessableHandlerInterface, \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\FormattableHandlerInterface, \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\ResettableInterface
{
    /**
     * @var HandlerInterface
     */
    protected $handler;
    public function __construct(\DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\HandlerInterface $handler)
    {
        $this->handler = $handler;
    }
    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record) : bool
    {
        return $this->handler->isHandling($record);
    }
    /**
     * {@inheritdoc}
     */
    public function handle(array $record) : bool
    {
        return $this->handler->handle($record);
    }
    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records) : void
    {
        $this->handler->handleBatch($records);
    }
    /**
     * {@inheritdoc}
     */
    public function close() : void
    {
        $this->handler->close();
    }
    /**
     * {@inheritdoc}
     */
    public function pushProcessor(callable $callback) : HandlerInterface
    {
        if ($this->handler instanceof ProcessableHandlerInterface) {
            $this->handler->pushProcessor($callback);
            return $this;
        }
        throw new \LogicException('The wrapped handler does not implement ' . \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\ProcessableHandlerInterface::class);
    }
    /**
     * {@inheritdoc}
     */
    public function popProcessor() : callable
    {
        if ($this->handler instanceof ProcessableHandlerInterface) {
            return $this->handler->popProcessor();
        }
        throw new \LogicException('The wrapped handler does not implement ' . \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\ProcessableHandlerInterface::class);
    }
    /**
     * {@inheritdoc}
     */
    public function setFormatter(\DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Formatter\FormatterInterface $formatter) : HandlerInterface
    {
        if ($this->handler instanceof FormattableHandlerInterface) {
            $this->handler->setFormatter($formatter);
        }
        throw new \LogicException('The wrapped handler does not implement ' . \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\FormattableHandlerInterface::class);
    }
    /**
     * {@inheritdoc}
     */
    public function getFormatter() : FormatterInterface
    {
        if ($this->handler instanceof FormattableHandlerInterface) {
            return $this->handler->getFormatter();
        }
        throw new \LogicException('The wrapped handler does not implement ' . \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Handler\FormattableHandlerInterface::class);
    }
    public function reset()
    {
        if ($this->handler instanceof ResettableInterface) {
            return $this->handler->reset();
        }
    }
}
