<?php

/**
 * Copyright 2017 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may ob`tain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload;

use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Exception\GoogleException;
use DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Exception\ServiceException;
use DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Promise\PromiseInterface;
use DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\Request;
/**
 * Uploader that is a special case of the ResumableUploader where we can write
 * the file contents in a streaming manner.
 */
class StreamableUploader extends \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Upload\ResumableUploader
{
    /**
     * Triggers the upload process.
     *
     * @param int $remainder [optional] How much data to try and send. Must be in
     *        multiples of 262144. If null or not provided, send the all the
     *        remaining data and close the file.
     * @return array
     * @throws GoogleException
     */
    public function upload($writeSize = null)
    {
        if ($writeSize === 0) {
            return [];
        }
        // find or create the resumeUri
        $resumeUri = $this->getResumeUri();
        if ($writeSize) {
            $rangeEnd = $this->rangeStart + $writeSize - 1;
            $data = $this->data->read($writeSize);
        } else {
            $rangeEnd = '*';
            $data = $this->data->getContents();
            $writeSize = strlen($data);
        }
        // do the streaming write
        $headers = ['Content-Length' => $writeSize, 'Content-Type' => $this->contentType, 'Content-Range' => "bytes {$this->rangeStart}-{$rangeEnd}/*"];
        $request = new \DeliciousBrains\WP_Offload_Media\Gcp\GuzzleHttp\Psr7\Request('PUT', $resumeUri, $headers, $data);
        try {
            $response = $this->requestWrapper->send($request, $this->requestOptions);
        } catch (ServiceException $ex) {
            throw new \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Exception\GoogleException("Upload failed. Please use this URI to resume your upload: {$resumeUri}", $ex->getCode(), $ex);
        }
        // reset the buffer with the remaining contents
        $this->rangeStart += $writeSize;
        return json_decode($response->getBody(), true);
    }
    /**
     * Currently only the MultiPartUploader supports async.
     *
     * Any calls to this will throw a generic Google Exception.
     *
     * @return PromiseInterface
     * @throws GoogleException
     * @experimental The experimental flag means that while we believe this method
     *      or class is ready for use, it may change before release in backwards-
     *      incompatible ways. Please use with caution, and test thoroughly when
     *      upgrading.
     */
    public function uploadAsync()
    {
        throw new \DeliciousBrains\WP_Offload_Media\Gcp\Google\Cloud\Core\Exception\GoogleException('Currently only the MultiPartUploader supports async.');
    }
}
