<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException;
/**
 * @internal
 */
class AccessPointArn extends \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn implements \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\ArnInterface
{
    use ResourceTypeAndIdTrait;
    /**
     * AccessPointArn constructor
     *
     * @param $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        static::validate($this->data);
    }
    public static function parse($string)
    {
        $data = parent::parse($string);
        return self::parseResourceTypeAndId($data);
    }
    /**
     * Validation specific to AccessPointArn
     *
     * @param array $data
     */
    protected static function validate(array $data)
    {
        if (empty($data['region'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 4th component of an access point ARN" . " represents the region and must not be empty.");
        }
        if (empty($data['account_id'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 5th component of an access point ARN" . " represents the account ID and must not be empty.");
        }
        if (!self::isValidHostLabel($data['account_id'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The account ID in an access point ARN" . " must be a valid host label value.");
        }
        if ($data['resource_type'] !== 'accesspoint') {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 6th component of an access point ARN" . " represents the resource type and must be 'accesspoint'.");
        }
        if (empty($data['resource_id'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 7th component of an access point ARN" . " represents the resource ID and must not be empty.");
        }
        if (strpos($data['resource_id'], ':') !== false) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The resource ID component of an access" . " point ARN must not contain additional components" . " (delimited by ':').");
        }
        if (!self::isValidHostLabel($data['resource_id'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The resource ID in an access point ARN" . " must be a valid host label value.");
        }
    }
    protected static function isValidHostLabel($string)
    {
        $length = strlen($string);
        if ($length < 1 || $length > 63) {
            return false;
        }
        if ($value = preg_match("/^[a-zA-Z0-9-]+\$/", $string)) {
            return true;
        }
        return false;
    }
}
