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
 * Condition Class Doc Comment
 *
 * @description The condition parameter filters offers by condition type.
 */
class Condition
{
    /**
     * Possible values of this enum
     */
    public const ANY = 'Any';

    public const _NEW = 'New';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::ANY,
            self::_NEW
        ];
    }
}


