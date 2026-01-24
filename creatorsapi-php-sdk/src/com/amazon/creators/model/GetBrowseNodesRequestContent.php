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

use \ArrayAccess;
use \Amazon\CreatorsAPI\v1\ObjectSerializer;

/**
 * GetBrowseNodesRequestContent Class Doc Comment
 *
 * @description Input for the GetBrowseNodes operation to retrieve browse node information.
 * @implements \ArrayAccess<string, mixed>
 */
class GetBrowseNodesRequestContent implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'GetBrowseNodesRequestContent';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'partnerTag' => 'string',
        'browseNodeIds' => 'string[]',
        'languagesOfPreference' => 'string[]',
        'resources' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResource[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'partnerTag' => null,
        'browseNodeIds' => null,
        'languagesOfPreference' => null,
        'resources' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'partnerTag' => false,
        'browseNodeIds' => false,
        'languagesOfPreference' => false,
        'resources' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'partnerTag' => 'partnerTag',
        'browseNodeIds' => 'browseNodeIds',
        'languagesOfPreference' => 'languagesOfPreference',
        'resources' => 'resources'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'partnerTag' => 'setPartnerTag',
        'browseNodeIds' => 'setBrowseNodeIds',
        'languagesOfPreference' => 'setLanguagesOfPreference',
        'resources' => 'setResources'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'partnerTag' => 'getPartnerTag',
        'browseNodeIds' => 'getBrowseNodeIds',
        'languagesOfPreference' => 'getLanguagesOfPreference',
        'resources' => 'getResources'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }


    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[]|null $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('partnerTag', $data ?? [], null);
        $this->setIfExists('browseNodeIds', $data ?? [], null);
        $this->setIfExists('languagesOfPreference', $data ?? [], null);
        $this->setIfExists('resources', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['partnerTag'] === null) {
            $invalidProperties[] = "'partnerTag' can't be null";
        }
        if ((mb_strlen($this->container['partnerTag']) > 64)) {
            $invalidProperties[] = "invalid value for 'partnerTag', the character length must be smaller than or equal to 64.";
        }

        if (!preg_match("/.*\\S.*/", $this->container['partnerTag'])) {
            $invalidProperties[] = "invalid value for 'partnerTag', must be conform to the pattern /.*\\S.*/.";
        }

        if ($this->container['browseNodeIds'] === null) {
            $invalidProperties[] = "'browseNodeIds' can't be null";
        }
        if ((count($this->container['browseNodeIds']) > 10)) {
            $invalidProperties[] = "invalid value for 'browseNodeIds', number of items must be less than or equal to 10.";
        }

        if ((count($this->container['browseNodeIds']) < 1)) {
            $invalidProperties[] = "invalid value for 'browseNodeIds', number of items must be greater than or equal to 1.";
        }

        if (!is_null($this->container['languagesOfPreference']) && (count($this->container['languagesOfPreference']) > 1)) {
            $invalidProperties[] = "invalid value for 'languagesOfPreference', number of items must be less than or equal to 1.";
        }

        if (!is_null($this->container['resources']) && (count($this->container['resources']) > 100)) {
            $invalidProperties[] = "invalid value for 'resources', number of items must be less than or equal to 100.";
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets partnerTag
     *
     * @return string
     */
    public function getPartnerTag()
    {
        return $this->container['partnerTag'];
    }

    /**
     * Sets partnerTag
     *
     * @param string $partnerTag Unique ID for a partner. Type: String (Non-Empty) Default Value: None Example: 'xyz-20'
     *
     * @return self
     */
    public function setPartnerTag($partnerTag)
    {
        if (is_null($partnerTag)) {
            throw new \InvalidArgumentException('non-nullable partnerTag cannot be null');
        }
        if ((mb_strlen($partnerTag) > 64)) {
            throw new \InvalidArgumentException('invalid length for $partnerTag when calling GetBrowseNodesRequestContent., must be smaller than or equal to 64.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($partnerTag)))) {
            throw new \InvalidArgumentException("invalid value for \$partnerTag when calling GetBrowseNodesRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['partnerTag'] = $partnerTag;

        return $this;
    }

    /**
     * Gets browseNodeIds
     *
     * @return string[]
     */
    public function getBrowseNodeIds()
    {
        return $this->container['browseNodeIds'];
    }

    /**
     * Sets browseNodeIds
     *
     * @param string[] $browseNodeIds List of BrowseNodeIds. A BrowseNodeId is a unique ID assigned by Amazon that identifies a product category/sub-category. The BrowseNodeId is a positive Long having max value upto Long.MAX_VALUE i.e. 9223372036854775807 (inclusive). Type: List of Strings (Positive Long only) (up to 10) Default Value: None Example: ['283155', '3040']
     *
     * @return self
     */
    public function setBrowseNodeIds($browseNodeIds)
    {
        if (is_null($browseNodeIds)) {
            throw new \InvalidArgumentException('non-nullable browseNodeIds cannot be null');
        }

        if ((count($browseNodeIds) > 10)) {
            throw new \InvalidArgumentException('invalid value for $browseNodeIds when calling GetBrowseNodesRequestContent., number of items must be less than or equal to 10.');
        }
        if ((count($browseNodeIds) < 1)) {
            throw new \InvalidArgumentException('invalid length for $browseNodeIds when calling GetBrowseNodesRequestContent., number of items must be greater than or equal to 1.');
        }
        $this->container['browseNodeIds'] = $browseNodeIds;

        return $this;
    }

    /**
     * Gets languagesOfPreference
     *
     * @return string[]|null
     */
    public function getLanguagesOfPreference()
    {
        return $this->container['languagesOfPreference'];
    }

    /**
     * Sets languagesOfPreference
     *
     * @param string[]|null $languagesOfPreference Languages of preference in which the information should be returned in response. By default the information is returned in the default language of the marketplace. Expected locale format is the ISO 639 language code followed by underscore followed by the ISO 3166 country code (i.e. en_US, fr_CA etc.). Currently only single language of preference is supported. Type: List of Strings (Non-Empty) Default Value: None Example: ['en_US']
     *
     * @return self
     */
    public function setLanguagesOfPreference($languagesOfPreference)
    {
        if (is_null($languagesOfPreference)) {
            throw new \InvalidArgumentException('non-nullable languagesOfPreference cannot be null');
        }

        if ((count($languagesOfPreference) > 1)) {
            throw new \InvalidArgumentException('invalid value for $languagesOfPreference when calling GetBrowseNodesRequestContent., number of items must be less than or equal to 1.');
        }
        $this->container['languagesOfPreference'] = $languagesOfPreference;

        return $this;
    }

    /**
     * Gets resources
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResource[]|null
     */
    public function getResources()
    {
        return $this->container['resources'];
    }

    /**
     * Sets resources
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetBrowseNodesResource[]|null $resources Specifies the types of values to return. You can specify multiple resources in one request. For list of valid Resources for SearchItems operation, refer Resources Parameter. Type: List of String Default Value: ItemInfo.Title
     *
     * @return self
     */
    public function setResources($resources)
    {
        if (is_null($resources)) {
            throw new \InvalidArgumentException('non-nullable resources cannot be null');
        }

        if ((count($resources) > 100)) {
            throw new \InvalidArgumentException('invalid value for $resources when calling GetBrowseNodesRequestContent., number of items must be less than or equal to 100.');
        }
        $this->container['resources'] = $resources;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


