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
namespace DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Formatter;

use DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger;
use DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Utils;
/**
 * Formats incoming records into an HTML table
 *
 * This is especially useful for html email logging
 *
 * @author Tiago Brito <tlfbrito@gmail.com>
 */
class HtmlFormatter extends \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Formatter\NormalizerFormatter
{
    /**
     * Translates Monolog log levels to html color priorities.
     */
    protected $logLevels = [\DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::DEBUG => '#CCCCCC', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::INFO => '#28A745', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::NOTICE => '#17A2B8', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::WARNING => '#FFC107', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::ERROR => '#FD7E14', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::CRITICAL => '#DC3545', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::ALERT => '#821722', \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Logger::EMERGENCY => '#000000'];
    /**
     * @param string|null $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct(?string $dateFormat = null)
    {
        parent::__construct($dateFormat);
    }
    /**
     * Creates an HTML table row
     *
     * @param string $th       Row header content
     * @param string $td       Row standard cell content
     * @param bool   $escapeTd false if td content must not be html escaped
     */
    protected function addRow(string $th, string $td = ' ', bool $escapeTd = true) : string
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
        if ($escapeTd) {
            $td = '<pre>' . htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8') . '</pre>';
        }
        return "<tr style=\"padding: 4px;text-align: left;\">\n<th style=\"vertical-align: top;background: #ccc;color: #000\" width=\"100\">{$th}:</th>\n<td style=\"padding: 4px;text-align: left;vertical-align: top;background: #eee;color: #000\">" . $td . "</td>\n</tr>";
    }
    /**
     * Create a HTML h1 tag
     *
     * @param  string $title Text to be in the h1
     * @param  int    $level Error level
     * @return string
     */
    protected function addTitle(string $title, int $level) : string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');
        return '<h1 style="background: ' . $this->logLevels[$level] . ';color: #ffffff;padding: 5px;" class="monolog-output">' . $title . '</h1>';
    }
    /**
     * Formats a log record.
     *
     * @param  array  $record A record to format
     * @return string The formatted record
     */
    public function format(array $record) : string
    {
        $output = $this->addTitle($record['level_name'], $record['level']);
        $output .= '<table cellspacing="1" width="100%" class="monolog-output">';
        $output .= $this->addRow('Message', (string) $record['message']);
        $output .= $this->addRow('Time', $this->formatDate($record['datetime']));
        $output .= $this->addRow('Channel', $record['channel']);
        if ($record['context']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record['context'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Context', $embeddedTable, false);
        }
        if ($record['extra']) {
            $embeddedTable = '<table cellspacing="1" width="100%">';
            foreach ($record['extra'] as $key => $value) {
                $embeddedTable .= $this->addRow((string) $key, $this->convertToString($value));
            }
            $embeddedTable .= '</table>';
            $output .= $this->addRow('Extra', $embeddedTable, false);
        }
        return $output . '</table>';
    }
    /**
     * Formats a set of log records.
     *
     * @param  array  $records A set of records to format
     * @return string The formatted set of records
     */
    public function formatBatch(array $records) : string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }
        return $message;
    }
    protected function convertToString($data) : string
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }
        $data = $this->normalize($data);
        return \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Utils::jsonEncode($data, JSON_PRETTY_PRINT | \DeliciousBrains\WP_Offload_Media\Gcp\Monolog\Utils::DEFAULT_JSON_FLAGS, true);
    }
}
