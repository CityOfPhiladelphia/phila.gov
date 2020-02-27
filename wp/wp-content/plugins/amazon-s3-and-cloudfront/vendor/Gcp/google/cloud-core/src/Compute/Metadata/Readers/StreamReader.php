<?php

/**
 * Copyright 2015 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Compute\Metadata\Readers;

use DeliciousBrains\WP_Offload_Media\Gcp\Google\Auth\Credentials\GCECredentials;
/**
 * A class only reading the metadata URL with an appropriate header.
 *
 * This class makes it easy to test the MetadataStream class.
 */
class StreamReader implements \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Compute\Metadata\Readers\ReaderInterface
{
    /**
     * The base PATH for the metadata.
     *
     * @deprecated
     */
    const BASE_URL = 'http://169.254.169.254/computeMetadata/v1/';
    /**
     * The header whose presence indicates GCE presence.
     *
     * @deprecated
     */
    const FLAVOR_HEADER = 'Metadata-Flavor: Google';
    /**
     * A common context for this reader.
     */
    private $context;
    /**
     * We create the common context in the constructor.
     */
    public function __construct()
    {
        $options = ['http' => ['method' => 'GET', 'header' => \DeliciousBrains\WP_Offload_Media\Gcp\Google\Auth\Credentials\GCECredentials::FLAVOR_HEADER . ': Google']];
        $this->context = $this->createStreamContext($options);
    }
    /**
     * Read the metadata for a given path.
     *
     * @param string $path The metadata path, relative to `/computeMetadata/v1/`.
     * @return string
     */
    public function read($path)
    {
        $url = sprintf('http://%s/computeMetadata/v1/%s', \DeliciousBrains\WP_Offload_Media\Gcp\Google\Auth\Credentials\GCECredentials::METADATA_IP, $path);
        return $this->getMetadata($url);
    }
    /**
     * Abstracted for testing.
     *
     * @param array $options
     * @return resource
     * @codeCoverageIgnore
     */
    protected function createStreamContext(array $options)
    {
        return stream_context_create($options);
    }
    /**
     * Abstracted for testing.
     *
     * @param string $url
     * @return string
     * @codeCoverageIgnore
     */
    protected function getMetadata($url)
    {
        return file_get_contents($url, false, $this->context);
    }
}
