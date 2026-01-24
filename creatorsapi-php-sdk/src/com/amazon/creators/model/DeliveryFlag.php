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
 * DeliveryFlag Class Doc Comment
 */
class DeliveryFlag
{
    /**
     * Possible values of this enum
     */
    public const AMAZON_GLOBAL = 'AmazonGlobal';

    public const FREE_SHIPPING = 'FreeShipping';

    public const FULFILLED_BY_AMAZON = 'FulfilledByAmazon';

    public const PRIME = 'Prime';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::AMAZON_GLOBAL,
            self::FREE_SHIPPING,
            self::FULFILLED_BY_AMAZON,
            self::PRIME
        ];
    }
}


