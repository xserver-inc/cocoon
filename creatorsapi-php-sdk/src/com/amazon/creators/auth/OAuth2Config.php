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


namespace Amazon\CreatorsAPI\v1\com\amazon\creators\auth;

/**
 * OAuth2 Configuration class
 */
class OAuth2Config
{
    /**
     * OAuth2 client ID
     *
     * @var string
     */
    private $clientId;

    /**
     * OAuth2 client secret
     *
     * @var string
     */
    private $clientSecret;

    /**
     * credential version
     *
     * @var string
     */
    private $version;

    /**
     * Authentication endpoint
     *
     * @var string|null
     */
    private $authEndpoint;

    /**
     * Grant type for OAuth2
     *
     * @var string
     */
    private $grantType = 'client_credentials';

    /**
     * OAuth2 scope
     *
     * @var string
     */
    private $scope = 'creatorsapi/default';

    /**
     * Constructor
     *
     * @param string $clientId OAuth2 client ID
     * @param string $clientSecret OAuth2 client secret
     * @param string $version credential version
     * @param string|null $authEndpoint Authentication endpoint
     */
    public function __construct($clientId, $clientSecret, $version, $authEndpoint)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->version = $version;
        $this->authEndpoint = $authEndpoint;
    }

    /**
     * Gets the OAuth2 client ID
     *
     * @return string OAuth2 client ID
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Gets the OAuth2 client secret
     *
     * @return string OAuth2 client secret
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Gets the credential version
     *
     * @return string credential version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Gets the OAuth2 grant type
     *
     * @return string Grant type
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * Gets the OAuth2 scope
     *
     * @return string OAuth2 scope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Gets the appropriate OAuth2 token endpoint based on the credential version
     *
     * @return string Token endpoint URL
     * @throws \InvalidArgumentException If the version is not supported
     */
    public function getTokenEndpoint()
    {
        // Custom authEndpoint used for testing
        if ($this->authEndpoint !== null && trim($this->authEndpoint) !== '') {
            return $this->authEndpoint;
        }
        
        switch ($this->version) {
            case "2.1":
                return "https://creatorsapi.auth.us-east-1.amazoncognito.com/oauth2/token";
            case "2.2":
                return "https://creatorsapi.auth.eu-south-2.amazoncognito.com/oauth2/token";
            case "2.3":
                return "https://creatorsapi.auth.us-west-2.amazoncognito.com/oauth2/token";
            default:
                throw new \InvalidArgumentException("Unsupported version: {$this->version}. Supported versions are: 2.1, 2.2, 2.3");
        }
    }

    /**
     * Sets the OAuth2 scope
     *
     * @param string $scope OAuth2 scope
     * @return $this
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Sets the grant type
     *
     * @param string $grantType Grant type
     * @return $this
     */
    public function setGrantType($grantType)
    {
        $this->grantType = $grantType;
        return $this;
    }
}
