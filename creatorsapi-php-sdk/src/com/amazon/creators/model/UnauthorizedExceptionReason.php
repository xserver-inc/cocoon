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


namespace Amazon\CreatorsAPI\v1\com\amazon\creators\model;
use \Amazon\CreatorsAPI\v1\ObjectSerializer;

/**
 * UnauthorizedExceptionReason Class Doc Comment
 *
 * @description Possible reasons for authentication failure
 */
class UnauthorizedExceptionReason
{
    /**
     * Possible values of this enum
     */
    public const TOKEN_EXPIRED = 'TokenExpired';

    public const INVALID_TOKEN = 'InvalidToken';

    public const INVALID_ISSUER = 'InvalidIssuer';

    public const MISSING_CLAIM = 'MissingClaim';

    public const MISSING_KEY_ID = 'MissingKeyId';

    public const UNSUPPORTED_CLIENT = 'UnsupportedClient';

    public const INVALID_CLIENT = 'InvalidClient';

    public const MISSING_CREDENTIAL = 'MissingCredential';

    public const OTHER = 'Other';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::TOKEN_EXPIRED,
            self::INVALID_TOKEN,
            self::INVALID_ISSUER,
            self::MISSING_CLAIM,
            self::MISSING_KEY_ID,
            self::UNSUPPORTED_CLIENT,
            self::INVALID_CLIENT,
            self::MISSING_CREDENTIAL,
            self::OTHER
        ];
    }
}


