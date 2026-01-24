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
 * ValidationExceptionReason Class Doc Comment
 *
 * @description Possible reasons a request failed validation
 */
class ValidationExceptionReason
{
    /**
     * Possible values of this enum
     */
    public const UNKNOWN_OPERATION = 'UnknownOperation';

    public const CANNOT_PARSE = 'CannotParse';

    public const FIELD_VALIDATION_FAILED = 'FieldValidationFailed';

    public const INVALID_ASSOCIATE = 'InvalidAssociate';

    public const INVALID_PARTNER_TAG = 'InvalidPartnerTag';

    public const OTHER = 'Other';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::UNKNOWN_OPERATION,
            self::CANNOT_PARSE,
            self::FIELD_VALIDATION_FAILED,
            self::INVALID_ASSOCIATE,
            self::INVALID_PARTNER_TAG,
            self::OTHER
        ];
    }
}


