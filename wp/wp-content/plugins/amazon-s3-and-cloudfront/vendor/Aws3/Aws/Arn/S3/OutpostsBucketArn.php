<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\ResourceTypeAndIdTrait;
/**
 * This class represents an S3 Outposts bucket ARN, which is in the
 * following format:
 *
 * @internal
 */
class OutpostsBucketArn extends \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn implements \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3\BucketArnInterface, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3\OutpostsArnInterface
{
    use ResourceTypeAndIdTrait;
    /**
     * Parses a string into an associative array of components that represent
     * a OutpostsBucketArn
     *
     * @param $string
     * @return array
     */
    public static function parse($string)
    {
        $data = parent::parse($string);
        $data = self::parseResourceTypeAndId($data);
        return self::parseOutpostData($data);
    }
    public function getBucketName()
    {
        return $this->data['bucket_name'];
    }
    public function getOutpostId()
    {
        return $this->data['outpost_id'];
    }
    private static function parseOutpostData(array $data)
    {
        $resourceData = preg_split("/[\\/:]/", $data['resource_id'], 3);
        $data['outpost_id'] = isset($resourceData[0]) ? $resourceData[0] : null;
        $data['bucket_label'] = isset($resourceData[1]) ? $resourceData[1] : null;
        $data['bucket_name'] = isset($resourceData[2]) ? $resourceData[2] : null;
        return $data;
    }
    /**
     *
     * @param array $data
     */
    protected static function validate(array $data)
    {
        \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Arn::validate($data);
        if ($data['service'] !== 's3-outposts') {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 3rd component of an S3 Outposts" . " bucket ARN represents the service and must be 's3-outposts'.");
        }
        self::validateRegion($data, 'S3 Outposts bucket ARN');
        self::validateAccountId($data, 'S3 Outposts bucket ARN');
        if ($data['resource_type'] !== 'outpost') {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 6th component of an S3 Outposts" . " bucket ARN represents the resource type and must be" . " 'outpost'.");
        }
        if (!self::isValidHostLabel($data['outpost_id'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 7th component of an S3 Outposts" . " bucket ARN is required, represents the outpost ID, and" . " must be a valid host label.");
        }
        if ($data['bucket_label'] !== 'bucket') {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 8th component of an S3 Outposts" . " bucket ARN must be 'bucket'");
        }
        if (empty($data['bucket_name'])) {
            throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\Exception\InvalidArnException("The 9th component of an S3 Outposts" . " bucket ARN represents the bucket name and must not be empty.");
        }
    }
}
