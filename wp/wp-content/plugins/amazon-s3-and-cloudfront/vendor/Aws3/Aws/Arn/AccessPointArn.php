<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException;
/**
 * @internal
 */
class AccessPointArn extends \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn implements \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\AccessPointArnInterface
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
        $data = self::parseResourceTypeAndId($data);
        $data['accesspoint_name'] = $data['resource_id'];
        return $data;
    }
    public function getAccesspointName()
    {
        return $this->data['accesspoint_name'];
    }
    /**
     * Validation specific to AccessPointArn
     *
     * @param array $data
     */
    protected static function validate(array $data)
    {
        self::validateRegion($data, 'access point ARN');
        self::validateAccountId($data, 'access point ARN');
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
}
