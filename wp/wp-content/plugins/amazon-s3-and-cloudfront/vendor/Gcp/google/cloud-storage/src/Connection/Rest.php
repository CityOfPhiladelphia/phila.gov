<?php

/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Storage\Connection;

use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\RequestBuilder;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\RequestWrapper;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\RestTrait;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\AbstractUploader;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\MultipartUploader;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\ResumableUploader;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\StreamableUploader;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\UriTrait;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Storage\Connection\ConnectionInterface;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Storage\StorageClient;
use DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Promise\PromiseInterface;
use DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7;
use DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\Request;
use DeliciousBrains\WP_Offload_Media\Gcp\Psr\Http\Message\ResponseInterface;
/**
 * Implementation of the
 * [Google Cloud Storage JSON API](https://cloud.google.com/storage/docs/json_api/).
 */
class Rest implements \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Storage\Connection\ConnectionInterface
{
    use RestTrait;
    use UriTrait;
    const BASE_URI = 'https://www.googleapis.com/storage/v1/';
    const UPLOAD_URI = 'https://www.googleapis.com/upload/storage/v1/b/{bucket}/o{?query*}';
    const DOWNLOAD_URI = 'https://www.googleapis.com/storage/v1/b/{bucket}/o/{object}{?query*}';
    /**
     * @var string
     */
    private $projectId;
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config += ['serviceDefinitionPath' => __DIR__ . '/ServiceDefinition/storage-v1.json', 'componentVersion' => \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Storage\StorageClient::VERSION];
        $this->setRequestWrapper(new \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\RequestWrapper($config));
        $this->setRequestBuilder(new \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\RequestBuilder($config['serviceDefinitionPath'], self::BASE_URI));
        $this->projectId = $this->pluck('projectId', $config, false);
    }
    /**
     * @return string
     */
    public function projectId()
    {
        return $this->projectId;
    }
    /**
     * @param array $args
     */
    public function deleteAcl(array $args = [])
    {
        return $this->send($args['type'], 'delete', $args);
    }
    /**
     * @param array $args
     */
    public function getAcl(array $args = [])
    {
        return $this->send($args['type'], 'get', $args);
    }
    /**
     * @param array $args
     */
    public function listAcl(array $args = [])
    {
        return $this->send($args['type'], 'list', $args);
    }
    /**
     * @param array $args
     */
    public function insertAcl(array $args = [])
    {
        return $this->send($args['type'], 'insert', $args);
    }
    /**
     * @param array $args
     */
    public function patchAcl(array $args = [])
    {
        return $this->send($args['type'], 'patch', $args);
    }
    /**
     * @param array $args
     */
    public function deleteBucket(array $args = [])
    {
        return $this->send('buckets', 'delete', $args);
    }
    /**
     * @param array $args
     */
    public function getBucket(array $args = [])
    {
        return $this->send('buckets', 'get', $args);
    }
    /**
     * @param array $args
     */
    public function listBuckets(array $args = [])
    {
        return $this->send('buckets', 'list', $args);
    }
    /**
     * @param array $args
     */
    public function insertBucket(array $args = [])
    {
        return $this->send('buckets', 'insert', $args);
    }
    /**
     * @param array $args
     */
    public function patchBucket(array $args = [])
    {
        return $this->send('buckets', 'patch', $args);
    }
    /**
     * @param array $args
     */
    public function deleteObject(array $args = [])
    {
        return $this->send('objects', 'delete', $args);
    }
    /**
     * @param array $args
     */
    public function copyObject(array $args = [])
    {
        return $this->send('objects', 'copy', $args);
    }
    /**
     * @param array $args
     */
    public function rewriteObject(array $args = [])
    {
        return $this->send('objects', 'rewrite', $args);
    }
    /**
     * @param array $args
     */
    public function composeObject(array $args = [])
    {
        return $this->send('objects', 'compose', $args);
    }
    /**
     * @param array $args
     */
    public function getObject(array $args = [])
    {
        return $this->send('objects', 'get', $args);
    }
    /**
     * @param array $args
     */
    public function listObjects(array $args = [])
    {
        return $this->send('objects', 'list', $args);
    }
    /**
     * @param array $args
     */
    public function patchObject(array $args = [])
    {
        return $this->send('objects', 'patch', $args);
    }
    /**
     * @param array $args
     */
    public function downloadObject(array $args = [])
    {
        list($request, $requestOptions) = $this->buildDownloadObjectParams($args);
        return $this->requestWrapper->send($request, $requestOptions)->getBody();
    }
    /**
     * @param array $args
     * @experimental The experimental flag means that while we believe this method
     *      or class is ready for use, it may change before release in backwards-
     *      incompatible ways. Please use with caution, and test thoroughly when
     *      upgrading.
     */
    public function downloadObjectAsync(array $args = [])
    {
        list($request, $requestOptions) = $this->buildDownloadObjectParams($args);
        return $this->requestWrapper->sendAsync($request, $requestOptions)->then(function (\DeliciousBrains\WP_Offload_Media\Gcp\Psr\Http\Message\ResponseInterface $response) {
            return $response->getBody();
        });
    }
    /**
     * @param array $args
     */
    public function insertObject(array $args = [])
    {
        $args = $this->resolveUploadOptions($args);
        $uploadType = \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\AbstractUploader::UPLOAD_TYPE_RESUMABLE;
        if ($args['streamable']) {
            $uploaderClass = \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\StreamableUploader::class;
        } elseif ($args['resumable']) {
            $uploaderClass = \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\ResumableUploader::class;
        } else {
            $uploaderClass = \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\MultipartUploader::class;
            $uploadType = \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\AbstractUploader::UPLOAD_TYPE_MULTIPART;
        }
        $uriParams = ['bucket' => $args['bucket'], 'query' => ['predefinedAcl' => $args['predefinedAcl'], 'uploadType' => $uploadType, 'userProject' => $args['userProject']]];
        return new $uploaderClass($this->requestWrapper, $args['data'], $this->expandUri(self::UPLOAD_URI, $uriParams), $args['uploaderOptions']);
    }
    /**
     * @param array $args
     */
    private function resolveUploadOptions(array $args)
    {
        $args += ['bucket' => null, 'name' => null, 'validate' => true, 'resumable' => null, 'streamable' => null, 'predefinedAcl' => null, 'metadata' => [], 'userProject' => null];
        $args['data'] = \DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\stream_for($args['data']);
        if ($args['resumable'] === null) {
            $args['resumable'] = $args['data']->getSize() > \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\AbstractUploader::RESUMABLE_LIMIT;
        }
        if (!$args['name']) {
            $args['name'] = basename($args['data']->getMetadata('uri'));
        }
        // @todo add support for rolling hash
        if ($args['validate'] && !isset($args['metadata']['md5Hash'])) {
            $args['metadata']['md5Hash'] = base64_encode(\DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\hash($args['data'], 'md5', true));
        }
        $args['metadata']['name'] = $args['name'];
        unset($args['name']);
        $args['contentType'] = isset($args['metadata']['contentType']) ? $args['metadata']['contentType'] : \DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\mimetype_from_filename($args['metadata']['name']);
        $uploaderOptionKeys = ['restOptions', 'retries', 'requestTimeout', 'chunkSize', 'contentType', 'metadata', 'uploadProgressCallback'];
        $args['uploaderOptions'] = array_intersect_key($args, array_flip($uploaderOptionKeys));
        $args = array_diff_key($args, array_flip($uploaderOptionKeys));
        return $args;
    }
    /**
     * @param  array $args
     */
    public function getBucketIamPolicy(array $args)
    {
        return $this->send('buckets', 'getIamPolicy', $args);
    }
    /**
     * @param  array $args
     */
    public function setBucketIamPolicy(array $args)
    {
        return $this->send('buckets', 'setIamPolicy', $args);
    }
    /**
     * @param  array $args
     */
    public function testBucketIamPermissions(array $args)
    {
        return $this->send('buckets', 'testIamPermissions', $args);
    }
    /**
     * @param array $args
     */
    public function getNotification(array $args = [])
    {
        return $this->send('notifications', 'get', $args);
    }
    /**
     * @param array $args
     */
    public function deleteNotification(array $args = [])
    {
        return $this->send('notifications', 'delete', $args);
    }
    /**
     * @param array $args
     */
    public function insertNotification(array $args = [])
    {
        return $this->send('notifications', 'insert', $args);
    }
    /**
     * @param array $args
     */
    public function listNotifications(array $args = [])
    {
        return $this->send('notifications', 'list', $args);
    }
    /**
     * @param array $args
     */
    public function getServiceAccount(array $args = [])
    {
        return $this->send('projects.resources.serviceAccount', 'get', $args);
    }
    /**
     * @param array $args
     */
    public function lockRetentionPolicy(array $args = [])
    {
        return $this->send('buckets', 'lockRetentionPolicy', $args);
    }
    /**
     * @param array $args
     * @return array
     */
    private function buildDownloadObjectParams(array $args)
    {
        $args += ['bucket' => null, 'object' => null, 'generation' => null, 'userProject' => null];
        $requestOptions = array_intersect_key($args, ['restOptions' => null, 'retries' => null, 'restRetryFunction' => null, 'restDelayFunction' => null]);
        $uri = $this->expandUri(self::DOWNLOAD_URI, ['bucket' => $args['bucket'], 'object' => $args['object'], 'query' => ['generation' => $args['generation'], 'alt' => 'media', 'userProject' => $args['userProject']]]);
        return [new \DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\Request('GET', \DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\uri_for($uri)), $requestOptions];
    }
}
