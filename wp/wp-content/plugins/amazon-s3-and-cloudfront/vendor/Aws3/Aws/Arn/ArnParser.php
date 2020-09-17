<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3\AccessPointArn as S3AccessPointArn;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3\BucketArn;
/**
 * @internal
 */
class ArnParser
{
    /**
     * @param $string
     * @return bool
     */
    public static function isArn($string)
    {
        return strpos($string, 'arn:') === 0;
    }
    /**
     * Parses a string and returns an instance of ArnInterface. Returns a
     * specific type of Arn object if it has a specific class representation
     * or a generic Arn object if not.
     *
     * @param $string
     * @return ArnInterface
     */
    public static function parse($string)
    {
        $data = \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn::parse($string);
        if (substr($data['resource'], 0, 11) === 'accesspoint') {
            if ($data['service'] === 's3') {
                return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3\AccessPointArn($string);
            }
            return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\AccessPointArn($string);
        }
        return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn($data);
    }
}
