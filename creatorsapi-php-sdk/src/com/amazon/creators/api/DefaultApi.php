<?php
/**
 * Copyright 2025 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */


namespace Amazon\CreatorsAPI\v1\com\amazon\creators\api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Amazon\CreatorsAPI\v1\ApiException;
use Amazon\CreatorsAPI\v1\Configuration;
use Amazon\CreatorsAPI\v1\FormDataProcessor;
use Amazon\CreatorsAPI\v1\HeaderSelector;
use Amazon\CreatorsAPI\v1\ObjectSerializer;
use Amazon\CreatorsAPI\v1\com\amazon\creators\auth\OAuth2Config;
use Amazon\CreatorsAPI\v1\com\amazon\creators\auth\OAuth2TokenManager;

/**
 * DefaultApi Class Doc Comment
 */
class DefaultApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /**
     * @var OAuth2TokenManager OAuth2 token manager
     */
    protected $tokenManager;

    /**
     * @var string|null Cached credential ID for change detection
     */
    private $cachedCredentialId = null;

    /**
     * @var string|null Cached credential secret for change detection
     */
    private $cachedCredentialSecret = null;

    /**
     * @var string|null Cached version for change detection
     */
    private $cachedVersion = null;

    /**
     * @var string|null Cached auth endpoint for change detection
     */
    private $cachedAuthEndpoint = null;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'getBrowseNodes' => [
            'application/json',
        ],
        'getFeed' => [
            'application/json',
        ],
        'getItems' => [
            'application/json',
        ],
        'getReport' => [
            'application/json',
        ],
        'getVariations' => [
            'application/json',
        ],
        'listFeeds' => [
            'application/json',
        ],
        'listReports' => [
            'application/json',
        ],
        'searchItems' => [
            'application/json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
        int $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
        // Token manager created lazily on first API call
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get or create OAuth2 token manager with lazy initialization and credential change detection
     *
     * @throws \InvalidArgumentException If OAuth2 configuration is missing
     * @return OAuth2TokenManager
     */
    private function getOrCreateTokenManager(): OAuth2TokenManager
    {
        // Get current configuration values
        $currentCredentialId = $this->config->getCredentialId();
        $currentCredentialSecret = $this->config->getCredentialSecret();
        $currentVersion = $this->config->getVersion();
        $currentAuthEndpoint = $this->config->getAuthEndpoint();

        // Check if token manager needs to be created or recreated
        $needsRecreation = $this->tokenManager === null ||
            $this->cachedCredentialId !== $currentCredentialId ||
            $this->cachedCredentialSecret !== $currentCredentialSecret ||
            $this->cachedVersion !== $currentVersion ||
            $this->cachedAuthEndpoint !== $currentAuthEndpoint;

        if ($needsRecreation) {
            // Validate required OAuth2 configuration
            if (!$currentCredentialId || !$currentCredentialSecret || !$currentVersion) {
                throw new \InvalidArgumentException(
                    "Missing OAuth2 configuration. Please specify credentialId, credentialSecret, and version."
                );
            }

            // Create new token manager with current credentials
            $oauthConfig = new OAuth2Config(
                $currentCredentialId,
                $currentCredentialSecret,
                $currentVersion,
                $currentAuthEndpoint
            );
            $this->tokenManager = new OAuth2TokenManager($oauthConfig);

            // Cache current configuration values
            $this->cachedCredentialId = $currentCredentialId;
            $this->cachedCredentialSecret = $currentCredentialSecret;
            $this->cachedVersion = $currentVersion;
            $this->cachedAuthEndpoint = $currentAuthEndpoint;
        }
        
        return $this->tokenManager;
    }

    /**
     * Build OAuth2 and custom headers for requests
     *
     * @param string $resourcePath The API resource path
     * @return array Array of headers
     */
    private function buildAuthenticatedHeaders(string $resourcePath): array
    {
        // Get OAuth2 token (creates/recreates token manager if needed)
        $token = $this->getOrCreateTokenManager()->getToken();
        
        // Build OAuth2 headers
        $oauthHeaders = [
            'Authorization' => "Bearer {$token}, Version {$this->config->getVersion()}"
        ];
        
        return $oauthHeaders;
    }

    /**
     * Operation getBrowseNodes
     *
     * @param  string $xMarketplace Target Amazon Locale. Type: String Default Value: None Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesRequestContent $getBrowseNodesRequestContent getBrowseNodesRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getBrowseNodes'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function getBrowseNodes($xMarketplace, $getBrowseNodesRequestContent, string $contentType = self::contentTypes['getBrowseNodes'][0])
    {
        list($response) = $this->getBrowseNodesWithHttpInfo($xMarketplace, $getBrowseNodesRequestContent, $contentType);
        return $response;
    }

    /**
     * Operation getBrowseNodesWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. Type: String Default Value: None Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesRequestContent $getBrowseNodesRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getBrowseNodes'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function getBrowseNodesWithHttpInfo($xMarketplace, $getBrowseNodesRequestContent, string $contentType = self::contentTypes['getBrowseNodes'][0])
    {
        $request = $this->getBrowseNodesRequest($xMarketplace, $getBrowseNodesRequestContent, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 429:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation getBrowseNodesAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. Type: String Default Value: None Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesRequestContent $getBrowseNodesRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getBrowseNodes'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getBrowseNodesAsync($xMarketplace, $getBrowseNodesRequestContent, string $contentType = self::contentTypes['getBrowseNodes'][0])
    {
        return $this->getBrowseNodesAsyncWithHttpInfo($xMarketplace, $getBrowseNodesRequestContent, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getBrowseNodesAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. Type: String Default Value: None Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesRequestContent $getBrowseNodesRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getBrowseNodes'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getBrowseNodesAsyncWithHttpInfo($xMarketplace, $getBrowseNodesRequestContent, string $contentType = self::contentTypes['getBrowseNodes'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResponseContent';
        $request = $this->getBrowseNodesRequest($xMarketplace, $getBrowseNodesRequestContent, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getBrowseNodes'
     *
     * @param  string $xMarketplace Target Amazon Locale. Type: String Default Value: None Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesRequestContent $getBrowseNodesRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getBrowseNodes'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getBrowseNodesRequest($xMarketplace, $getBrowseNodesRequestContent, string $contentType = self::contentTypes['getBrowseNodes'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling getBrowseNodes'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.getBrowseNodes, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.getBrowseNodes, must conform to the pattern /.*\\S.*/.");
        }
        
        // verify the required parameter 'getBrowseNodesRequestContent' is set
        if ($getBrowseNodesRequestContent === null || (is_array($getBrowseNodesRequestContent) && count($getBrowseNodesRequestContent) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $getBrowseNodesRequestContent when calling getBrowseNodes'
            );
        }


        $resourcePath = '/catalog/v1/getBrowseNodes';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (isset($getBrowseNodesRequestContent)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($getBrowseNodesRequestContent));
            } else {
                $httpBody = $getBrowseNodesRequestContent;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getFeed
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedRequestContent $getFeedRequestContent getFeedRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getFeed'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function getFeed($xMarketplace, $getFeedRequestContent, string $contentType = self::contentTypes['getFeed'][0])
    {
        list($response) = $this->getFeedWithHttpInfo($xMarketplace, $getFeedRequestContent, $contentType);
        return $response;
    }

    /**
     * Operation getFeedWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedRequestContent $getFeedRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getFeed'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function getFeedWithHttpInfo($xMarketplace, $getFeedRequestContent, string $contentType = self::contentTypes['getFeed'][0])
    {
        $request = $this->getFeedRequest($xMarketplace, $getFeedRequestContent, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation getFeedAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedRequestContent $getFeedRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getFeed'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getFeedAsync($xMarketplace, $getFeedRequestContent, string $contentType = self::contentTypes['getFeed'][0])
    {
        return $this->getFeedAsyncWithHttpInfo($xMarketplace, $getFeedRequestContent, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getFeedAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedRequestContent $getFeedRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getFeed'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getFeedAsyncWithHttpInfo($xMarketplace, $getFeedRequestContent, string $contentType = self::contentTypes['getFeed'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedResponseContent';
        $request = $this->getFeedRequest($xMarketplace, $getFeedRequestContent, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getFeed'
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetFeedRequestContent $getFeedRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getFeed'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getFeedRequest($xMarketplace, $getFeedRequestContent, string $contentType = self::contentTypes['getFeed'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling getFeed'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.getFeed, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.getFeed, must conform to the pattern /.*\\S.*/.");
        }
        
        // verify the required parameter 'getFeedRequestContent' is set
        if ($getFeedRequestContent === null || (is_array($getFeedRequestContent) && count($getFeedRequestContent) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $getFeedRequestContent when calling getFeed'
            );
        }


        $resourcePath = '/catalog/v1/getFeed';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (isset($getFeedRequestContent)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($getFeedRequestContent));
            } else {
                $httpBody = $getFeedRequestContent;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getItems
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent $getItemsRequestContent getItemsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getItems'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function getItems($xMarketplace, $getItemsRequestContent, string $contentType = self::contentTypes['getItems'][0])
    {
        list($response) = $this->getItemsWithHttpInfo($xMarketplace, $getItemsRequestContent, $contentType);
        return $response;
    }

    /**
     * Operation getItemsWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent $getItemsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getItems'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function getItemsWithHttpInfo($xMarketplace, $getItemsRequestContent, string $contentType = self::contentTypes['getItems'][0])
    {
        $request = $this->getItemsRequest($xMarketplace, $getItemsRequestContent, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 429:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation getItemsAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent $getItemsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getItems'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getItemsAsync($xMarketplace, $getItemsRequestContent, string $contentType = self::contentTypes['getItems'][0])
    {
        return $this->getItemsAsyncWithHttpInfo($xMarketplace, $getItemsRequestContent, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getItemsAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent $getItemsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getItems'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getItemsAsyncWithHttpInfo($xMarketplace, $getItemsRequestContent, string $contentType = self::contentTypes['getItems'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResponseContent';
        $request = $this->getItemsRequest($xMarketplace, $getItemsRequestContent, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getItems'
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsRequestContent $getItemsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getItems'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getItemsRequest($xMarketplace, $getItemsRequestContent, string $contentType = self::contentTypes['getItems'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling getItems'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.getItems, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.getItems, must conform to the pattern /.*\\S.*/.");
        }
        
        // verify the required parameter 'getItemsRequestContent' is set
        if ($getItemsRequestContent === null || (is_array($getItemsRequestContent) && count($getItemsRequestContent) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $getItemsRequestContent when calling getItems'
            );
        }


        $resourcePath = '/catalog/v1/getItems';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (isset($getItemsRequestContent)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($getItemsRequestContent));
            } else {
                $httpBody = $getItemsRequestContent;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getReport
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportRequestContent $getReportRequestContent getReportRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getReport'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function getReport($xMarketplace, $getReportRequestContent, string $contentType = self::contentTypes['getReport'][0])
    {
        list($response) = $this->getReportWithHttpInfo($xMarketplace, $getReportRequestContent, $contentType);
        return $response;
    }

    /**
     * Operation getReportWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportRequestContent $getReportRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getReport'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function getReportWithHttpInfo($xMarketplace, $getReportRequestContent, string $contentType = self::contentTypes['getReport'][0])
    {
        $request = $this->getReportRequest($xMarketplace, $getReportRequestContent, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation getReportAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportRequestContent $getReportRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getReport'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getReportAsync($xMarketplace, $getReportRequestContent, string $contentType = self::contentTypes['getReport'][0])
    {
        return $this->getReportAsyncWithHttpInfo($xMarketplace, $getReportRequestContent, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getReportAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportRequestContent $getReportRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getReport'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getReportAsyncWithHttpInfo($xMarketplace, $getReportRequestContent, string $contentType = self::contentTypes['getReport'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportResponseContent';
        $request = $this->getReportRequest($xMarketplace, $getReportRequestContent, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getReport'
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetReportRequestContent $getReportRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getReport'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getReportRequest($xMarketplace, $getReportRequestContent, string $contentType = self::contentTypes['getReport'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling getReport'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.getReport, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.getReport, must conform to the pattern /.*\\S.*/.");
        }
        
        // verify the required parameter 'getReportRequestContent' is set
        if ($getReportRequestContent === null || (is_array($getReportRequestContent) && count($getReportRequestContent) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $getReportRequestContent when calling getReport'
            );
        }


        $resourcePath = '/reports/v1/getReport';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (isset($getReportRequestContent)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($getReportRequestContent));
            } else {
                $httpBody = $getReportRequestContent;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVariations
     *
     * @param  string $xMarketplace Target Amazon Locale. This specifies the marketplace where the items should be searched. Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsRequestContent $getVariationsRequestContent getVariationsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVariations'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function getVariations($xMarketplace, $getVariationsRequestContent, string $contentType = self::contentTypes['getVariations'][0])
    {
        list($response) = $this->getVariationsWithHttpInfo($xMarketplace, $getVariationsRequestContent, $contentType);
        return $response;
    }

    /**
     * Operation getVariationsWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. This specifies the marketplace where the items should be searched. Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsRequestContent $getVariationsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVariations'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVariationsWithHttpInfo($xMarketplace, $getVariationsRequestContent, string $contentType = self::contentTypes['getVariations'][0])
    {
        $request = $this->getVariationsRequest($xMarketplace, $getVariationsRequestContent, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 429:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation getVariationsAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. This specifies the marketplace where the items should be searched. Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsRequestContent $getVariationsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVariations'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getVariationsAsync($xMarketplace, $getVariationsRequestContent, string $contentType = self::contentTypes['getVariations'][0])
    {
        return $this->getVariationsAsyncWithHttpInfo($xMarketplace, $getVariationsRequestContent, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVariationsAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. This specifies the marketplace where the items should be searched. Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsRequestContent $getVariationsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVariations'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getVariationsAsyncWithHttpInfo($xMarketplace, $getVariationsRequestContent, string $contentType = self::contentTypes['getVariations'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsResponseContent';
        $request = $this->getVariationsRequest($xMarketplace, $getVariationsRequestContent, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVariations'
     *
     * @param  string $xMarketplace Target Amazon Locale. This specifies the marketplace where the items should be searched. Example: &#39;www.amazon.com&#39; (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetVariationsRequestContent $getVariationsRequestContent (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVariations'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVariationsRequest($xMarketplace, $getVariationsRequestContent, string $contentType = self::contentTypes['getVariations'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling getVariations'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.getVariations, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.getVariations, must conform to the pattern /.*\\S.*/.");
        }
        
        // verify the required parameter 'getVariationsRequestContent' is set
        if ($getVariationsRequestContent === null || (is_array($getVariationsRequestContent) && count($getVariationsRequestContent) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $getVariationsRequestContent when calling getVariations'
            );
        }


        $resourcePath = '/catalog/v1/getVariations';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (isset($getVariationsRequestContent)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($getVariationsRequestContent));
            } else {
                $httpBody = $getVariationsRequestContent;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation listFeeds
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFeeds'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListFeedsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function listFeeds($xMarketplace, string $contentType = self::contentTypes['listFeeds'][0])
    {
        list($response) = $this->listFeedsWithHttpInfo($xMarketplace, $contentType);
        return $response;
    }

    /**
     * Operation listFeedsWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFeeds'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListFeedsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function listFeedsWithHttpInfo($xMarketplace, string $contentType = self::contentTypes['listFeeds'][0])
    {
        $request = $this->listFeedsRequest($xMarketplace, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListFeedsResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListFeedsResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListFeedsResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation listFeedsAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFeeds'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listFeedsAsync($xMarketplace, string $contentType = self::contentTypes['listFeeds'][0])
    {
        return $this->listFeedsAsyncWithHttpInfo($xMarketplace, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listFeedsAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFeeds'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listFeedsAsyncWithHttpInfo($xMarketplace, string $contentType = self::contentTypes['listFeeds'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListFeedsResponseContent';
        $request = $this->listFeedsRequest($xMarketplace, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'listFeeds'
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFeeds'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listFeedsRequest($xMarketplace, string $contentType = self::contentTypes['listFeeds'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling listFeeds'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.listFeeds, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.listFeeds, must conform to the pattern /.*\\S.*/.");
        }
        

        $resourcePath = '/catalog/v1/listFeeds';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation listReports
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listReports'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListReportsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function listReports($xMarketplace, string $contentType = self::contentTypes['listReports'][0])
    {
        list($response) = $this->listReportsWithHttpInfo($xMarketplace, $contentType);
        return $response;
    }

    /**
     * Operation listReportsWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listReports'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListReportsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function listReportsWithHttpInfo($xMarketplace, string $contentType = self::contentTypes['listReports'][0])
    {
        $request = $this->listReportsRequest($xMarketplace, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListReportsResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListReportsResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListReportsResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation listReportsAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listReports'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listReportsAsync($xMarketplace, string $contentType = self::contentTypes['listReports'][0])
    {
        return $this->listReportsAsyncWithHttpInfo($xMarketplace, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listReportsAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listReports'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listReportsAsyncWithHttpInfo($xMarketplace, string $contentType = self::contentTypes['listReports'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ListReportsResponseContent';
        $request = $this->listReportsRequest($xMarketplace, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'listReports'
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listReports'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listReportsRequest($xMarketplace, string $contentType = self::contentTypes['listReports'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling listReports'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.listReports, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.listReports, must conform to the pattern /.*\\S.*/.");
        }
        

        $resourcePath = '/reports/v1/listReports';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation searchItems
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent|null $searchItemsRequestContent searchItemsRequestContent (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchItems'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent
     */
    public function searchItems($xMarketplace, $searchItemsRequestContent = null, string $contentType = self::contentTypes['searchItems'][0])
    {
        list($response) = $this->searchItemsWithHttpInfo($xMarketplace, $searchItemsRequestContent, $contentType);
        return $response;
    }

    /**
     * Operation searchItemsWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent|null $searchItemsRequestContent (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchItems'] to see the possible values for this operation
     *
     * @throws \Amazon\CreatorsAPI\v1\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent|\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent, HTTP status code, HTTP response headers (array of strings)
     */
    public function searchItemsWithHttpInfo($xMarketplace, $searchItemsRequestContent = null, string $contentType = self::contentTypes['searchItems'][0])
    {
        $request = $this->searchItemsRequest($xMarketplace, $searchItemsRequestContent, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 429:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ValidationExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\UnauthorizedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\AccessDeniedExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ResourceNotFoundExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ThrottleExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\InternalServerExceptionResponseContent',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation searchItemsAsync
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent|null $searchItemsRequestContent (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchItems'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function searchItemsAsync($xMarketplace, $searchItemsRequestContent = null, string $contentType = self::contentTypes['searchItems'][0])
    {
        return $this->searchItemsAsyncWithHttpInfo($xMarketplace, $searchItemsRequestContent, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation searchItemsAsyncWithHttpInfo
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent|null $searchItemsRequestContent (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchItems'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function searchItemsAsyncWithHttpInfo($xMarketplace, $searchItemsRequestContent = null, string $contentType = self::contentTypes['searchItems'][0])
    {
        $returnType = '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResponseContent';
        $request = $this->searchItemsRequest($xMarketplace, $searchItemsRequestContent, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'searchItems'
     *
     * @param  string $xMarketplace Target Amazon Locale. (required)
     * @param  \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsRequestContent|null $searchItemsRequestContent (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchItems'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function searchItemsRequest($xMarketplace, $searchItemsRequestContent = null, string $contentType = self::contentTypes['searchItems'][0])
    {

        // verify the required parameter 'xMarketplace' is set
        if ($xMarketplace === null || (is_array($xMarketplace) && count($xMarketplace) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xMarketplace when calling searchItems'
            );
        }
        if (strlen($xMarketplace) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$xMarketplace" when calling DefaultApi.searchItems, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/.*\\S.*/", $xMarketplace)) {
            throw new \InvalidArgumentException("invalid value for \"xMarketplace\" when calling DefaultApi.searchItems, must conform to the pattern /.*\\S.*/.");
        }
        


        $resourcePath = '/catalog/v1/searchItems';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($xMarketplace !== null) {
            $headerParams['x-marketplace'] = ObjectSerializer::toHeaderValue($xMarketplace);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // Build OAuth2 and custom headers
        $authenticatedHeaders = $this->buildAuthenticatedHeaders($resourcePath);

        // for model (json/xml)
        if (isset($searchItemsRequestContent)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($searchItemsRequestContent));
            } else {
                $httpBody = $searchItemsRequestContent;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers,
            $authenticatedHeaders
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }

    private function handleResponseWithDataType(
        string $dataType,
        RequestInterface $request,
        ResponseInterface $response
    ): array {
        if ($dataType === '\SplFileObject') {
            $content = $response->getBody(); //stream goes to serializer
        } else {
            $content = (string) $response->getBody();
            if ($dataType !== 'string') {
                try {
                    $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    throw new ApiException(
                        sprintf(
                            'Error JSON decoding server response (%s)',
                            $request->getUri()
                        ),
                        $response->getStatusCode(),
                        $response->getHeaders(),
                        $content
                    );
                }
            }
        }

        return [
            ObjectSerializer::deserialize($content, $dataType, []),
            $response->getStatusCode(),
            $response->getHeaders()
        ];
    }

    private function responseWithinRangeCode(
        string $rangeCode,
        int $statusCode
    ): bool {
        $left = (int) ($rangeCode[0].'00');
        $right = (int) ($rangeCode[0].'99');

        return $statusCode >= $left && $statusCode <= $right;
    }
}
