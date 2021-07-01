<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Service;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\ArnInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\S3\OutpostsArnInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Endpoint\PartitionEndpointProvider;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Exception\InvalidRegionException;
/**
 * @internal
 */
trait EndpointRegionHelperTrait
{
    /** @var array */
    private $config;
    /** @var PartitionEndpointProvider */
    private $partitionProvider;
    /** @var string */
    private $region;
    /** @var Service */
    private $service;
    private function getPartitionSuffix(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\ArnInterface $arn, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Endpoint\PartitionEndpointProvider $provider)
    {
        $partition = $provider->getPartition($arn->getRegion(), $arn->getService());
        return $partition->getDnsSuffix();
    }
    private function getSigningRegion($region, $service, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Endpoint\PartitionEndpointProvider $provider)
    {
        $partition = $provider->getPartition($region, $service);
        $data = $partition->toArray();
        if (isset($data['services'][$service]['endpoints'][$region]['credentialScope']['region'])) {
            return $data['services'][$service]['endpoints'][$region]['credentialScope']['region'];
        }
        return $region;
    }
    private function isFipsPseudoRegion($region)
    {
        return strpos($region, 'fips-') !== false || strpos($region, '-fips') !== false;
    }
    private function isMatchingSigningRegion($arnRegion, $clientRegion, $service, \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Endpoint\PartitionEndpointProvider $provider)
    {
        $arnRegion = $this->stripPseudoRegions(strtolower($arnRegion));
        $clientRegion = $this->stripPseudoRegions(strtolower($clientRegion));
        if ($arnRegion === $clientRegion) {
            return true;
        }
        if ($this->getSigningRegion($clientRegion, $service, $provider) === $arnRegion) {
            return true;
        }
        return false;
    }
    private function stripPseudoRegions($region)
    {
        return str_replace(['fips-', '-fips'], ['', ''], $region);
    }
    private function validateFipsNotUsedWithOutposts(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\ArnInterface $arn)
    {
        if ($arn instanceof OutpostsArnInterface) {
            if (empty($this->config['use_arn_region']) || !$this->config['use_arn_region']->isUseArnRegion()) {
                $region = $this->region;
            } else {
                $region = $arn->getRegion();
            }
            if ($this->isFipsPseudoRegion($region)) {
                throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Exception\InvalidRegionException('Fips is currently not supported with S3 Outposts access' . ' points. Please provide a non-fips region or do not supply an' . ' access point ARN.');
            }
        }
    }
    private function validateMatchingRegion(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Arn\ArnInterface $arn)
    {
        if (!$this->isMatchingSigningRegion($arn->getRegion(), $this->region, $this->service->getEndpointPrefix(), $this->partitionProvider)) {
            if (empty($this->config['use_arn_region']) || !$this->config['use_arn_region']->isUseArnRegion()) {
                throw new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Exception\InvalidRegionException('The region' . " specified in the ARN (" . $arn->getRegion() . ") does not match the client region (" . "{$this->region}).");
            }
        }
    }
}
