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
 * GetBrowseNodesResource Class Doc Comment
 *
 * @description Resources for GetBrowseNodes operation which specify the values to return in the API response.
 */
class GetBrowseNodesResource
{
    /**
     * Possible values of this enum
     */
    public const ANCESTOR = 'browseNodes.ancestor';

    public const CHILDREN = 'browseNodes.children';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::ANCESTOR,
            self::CHILDREN
        ];
    }
}


