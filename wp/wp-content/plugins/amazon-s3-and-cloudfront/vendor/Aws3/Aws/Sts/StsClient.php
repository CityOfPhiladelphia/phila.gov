<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\Sts;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\AwsClient;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\CacheInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Credentials\Credentials;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Result;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Sts\RegionalEndpoints\ConfigurationProvider;
/**
 * This client is used to interact with the **AWS Security Token Service (AWS STS)**.
 *
 * @method \Aws\Result assumeRole(array $args = [])
 * @method \GuzzleHttp\Promise\Promise assumeRoleAsync(array $args = [])
 * @method \Aws\Result assumeRoleWithSAML(array $args = [])
 * @method \GuzzleHttp\Promise\Promise assumeRoleWithSAMLAsync(array $args = [])
 * @method \Aws\Result assumeRoleWithWebIdentity(array $args = [])
 * @method \GuzzleHttp\Promise\Promise assumeRoleWithWebIdentityAsync(array $args = [])
 * @method \Aws\Result decodeAuthorizationMessage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise decodeAuthorizationMessageAsync(array $args = [])
 * @method \Aws\Result getAccessKeyInfo(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAccessKeyInfoAsync(array $args = [])
 * @method \Aws\Result getCallerIdentity(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCallerIdentityAsync(array $args = [])
 * @method \Aws\Result getFederationToken(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getFederationTokenAsync(array $args = [])
 * @method \Aws\Result getSessionToken(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSessionTokenAsync(array $args = [])
 */
class StsClient extends \DeliciousBrains\WP_Offload_Media\Aws3\Aws\AwsClient
{
    /**
     * {@inheritdoc}
     *
     * In addition to the options available to
     * {@see \Aws\AwsClient::__construct}, StsClient accepts the following
     * options:
     *
     * - sts_regional_endpoints:
     *   (Aws\Sts\RegionalEndpoints\ConfigurationInterface|Aws\CacheInterface\|callable|string|array)
     *   Specifies whether to use regional or legacy endpoints for legacy regions.
     *   Provide an Aws\Sts\RegionalEndpoints\ConfigurationInterface object, an
     *   instance of Aws\CacheInterface, a callable configuration provider used
     *   to create endpoint configuration, a string value of `legacy` or
     *   `regional`, or an associative array with the following keys:
     *   endpoint_types (string)  Set to `legacy` or `regional`, defaults to
     *   `legacy`
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (!isset($args['sts_regional_endpoints']) || $args['sts_regional_endpoints'] instanceof CacheInterface) {
            $args['sts_regional_endpoints'] = \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Sts\RegionalEndpoints\ConfigurationProvider::defaultProvider($args);
        }
        parent::__construct($args);
    }
    /**
     * Creates credentials from the result of an STS operations
     *
     * @param Result $result Result of an STS operation
     *
     * @return Credentials
     * @throws \InvalidArgumentException if the result contains no credentials
     */
    public function createCredentials(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Result $result)
    {
        if (!$result->hasKey('Credentials')) {
            throw new \InvalidArgumentException('Result contains no credentials');
        }
        $c = $result['Credentials'];
        return new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\Credentials\Credentials($c['AccessKeyId'], $c['SecretAccessKey'], isset($c['SessionToken']) ? $c['SessionToken'] : null, isset($c['Expiration']) && $c['Expiration'] instanceof \DateTimeInterface ? (int) $c['Expiration']->format('U') : null);
    }
}
