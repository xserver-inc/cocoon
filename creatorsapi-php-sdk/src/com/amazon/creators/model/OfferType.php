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
 * OfferType Class Doc Comment
 *
 * @description The OfferType is an optional parameter that indicates the type of offer. We only use this for special callouts. Standard Offers will not have a particular type.
 */
class OfferType
{
    /**
     * Possible values of this enum
     */
    public const HAUL = 'HAUL';

    public const SUBSCRIBE_AND_SAVE = 'SUBSCRIBE_AND_SAVE';

    public const LIGHTNING_DEAL = 'LIGHTNING_DEAL';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::HAUL,
            self::SUBSCRIBE_AND_SAVE,
            self::LIGHTNING_DEAL
        ];
    }
}


