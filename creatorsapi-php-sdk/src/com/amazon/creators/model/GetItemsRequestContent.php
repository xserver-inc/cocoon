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
 * GetItemsRequestContent Class Doc Comment
 * @implements \ArrayAccess<string, mixed>
 */
class GetItemsRequestContent implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'GetItemsRequestContent';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'partnerTag' => 'string',
        'itemIds' => 'string[]',
        'condition' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\Condition',
        'currencyOfPreference' => 'string',
        'languagesOfPreference' => 'string[]',
        'properties' => 'array<string,string>',
        'resources' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource[]'
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
        'itemIds' => null,
        'condition' => null,
        'currencyOfPreference' => null,
        'languagesOfPreference' => null,
        'properties' => null,
        'resources' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'partnerTag' => false,
        'itemIds' => false,
        'condition' => false,
        'currencyOfPreference' => false,
        'languagesOfPreference' => false,
        'properties' => false,
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
        'itemIds' => 'itemIds',
        'condition' => 'condition',
        'currencyOfPreference' => 'currencyOfPreference',
        'languagesOfPreference' => 'languagesOfPreference',
        'properties' => 'properties',
        'resources' => 'resources'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'partnerTag' => 'setPartnerTag',
        'itemIds' => 'setItemIds',
        'condition' => 'setCondition',
        'currencyOfPreference' => 'setCurrencyOfPreference',
        'languagesOfPreference' => 'setLanguagesOfPreference',
        'properties' => 'setProperties',
        'resources' => 'setResources'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'partnerTag' => 'getPartnerTag',
        'itemIds' => 'getItemIds',
        'condition' => 'getCondition',
        'currencyOfPreference' => 'getCurrencyOfPreference',
        'languagesOfPreference' => 'getLanguagesOfPreference',
        'properties' => 'getProperties',
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
        $this->setIfExists('itemIds', $data ?? [], null);
        $this->setIfExists('condition', $data ?? [], null);
        $this->setIfExists('currencyOfPreference', $data ?? [], null);
        $this->setIfExists('languagesOfPreference', $data ?? [], null);
        $this->setIfExists('properties', $data ?? [], null);
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

        if ($this->container['itemIds'] === null) {
            $invalidProperties[] = "'itemIds' can't be null";
        }
        if ((count($this->container['itemIds']) > 10)) {
            $invalidProperties[] = "invalid value for 'itemIds', number of items must be less than or equal to 10.";
        }

        if ((count($this->container['itemIds']) < 1)) {
            $invalidProperties[] = "invalid value for 'itemIds', number of items must be greater than or equal to 1.";
        }

        if (!is_null($this->container['currencyOfPreference']) && (mb_strlen($this->container['currencyOfPreference']) > 100)) {
            $invalidProperties[] = "invalid value for 'currencyOfPreference', the character length must be smaller than or equal to 100.";
        }

        if (!is_null($this->container['currencyOfPreference']) && !preg_match("/.*\\S.*/", $this->container['currencyOfPreference'])) {
            $invalidProperties[] = "invalid value for 'currencyOfPreference', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['languagesOfPreference']) && (count($this->container['languagesOfPreference']) > 1)) {
            $invalidProperties[] = "invalid value for 'languagesOfPreference', number of items must be less than or equal to 1.";
        }

        if (!is_null($this->container['properties']) && (count($this->container['properties']) > 2)) {
            $invalidProperties[] = "invalid value for 'properties', number of items must be less than or equal to 2.";
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
     * @param string $partnerTag An alphanumeric token that uniquely identifies a partner. If the value of PartnerType is Associates, enter your Store Id or tracking ID.
     *
     * @return self
     */
    public function setPartnerTag($partnerTag)
    {
        if (is_null($partnerTag)) {
            throw new \InvalidArgumentException('non-nullable partnerTag cannot be null');
        }
        if ((mb_strlen($partnerTag) > 64)) {
            throw new \InvalidArgumentException('invalid length for $partnerTag when calling GetItemsRequestContent., must be smaller than or equal to 64.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($partnerTag)))) {
            throw new \InvalidArgumentException("invalid value for \$partnerTag when calling GetItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['partnerTag'] = $partnerTag;

        return $this;
    }

    /**
     * Gets itemIds
     *
     * @return string[]
     */
    public function getItemIds()
    {
        return $this->container['itemIds'];
    }

    /**
     * Sets itemIds
     *
     * @param string[] $itemIds itemIds
     *
     * @return self
     */
    public function setItemIds($itemIds)
    {
        if (is_null($itemIds)) {
            throw new \InvalidArgumentException('non-nullable itemIds cannot be null');
        }

        if ((count($itemIds) > 10)) {
            throw new \InvalidArgumentException('invalid value for $itemIds when calling GetItemsRequestContent., number of items must be less than or equal to 10.');
        }
        if ((count($itemIds) < 1)) {
            throw new \InvalidArgumentException('invalid length for $itemIds when calling GetItemsRequestContent., number of items must be greater than or equal to 1.');
        }
        $this->container['itemIds'] = $itemIds;

        return $this;
    }

    /**
     * Gets condition
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Condition|null
     */
    public function getCondition()
    {
        return $this->container['condition'];
    }

    /**
     * Sets condition
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Condition|null $condition condition
     *
     * @return self
     */
    public function setCondition($condition)
    {
        if (is_null($condition)) {
            throw new \InvalidArgumentException('non-nullable condition cannot be null');
        }
        $this->container['condition'] = $condition;

        return $this;
    }

    /**
     * Gets currencyOfPreference
     *
     * @return string|null
     */
    public function getCurrencyOfPreference()
    {
        return $this->container['currencyOfPreference'];
    }

    /**
     * Sets currencyOfPreference
     *
     * @param string|null $currencyOfPreference Currency of preference in which the prices information should be returned in response. By default the prices are returned in the default currency of the marketplace. Expected currency code format is the ISO 4217 currency code (i.e. USD, EUR etc.).
     *
     * @return self
     */
    public function setCurrencyOfPreference($currencyOfPreference)
    {
        if (is_null($currencyOfPreference)) {
            throw new \InvalidArgumentException('non-nullable currencyOfPreference cannot be null');
        }
        if ((mb_strlen($currencyOfPreference) > 100)) {
            throw new \InvalidArgumentException('invalid length for $currencyOfPreference when calling GetItemsRequestContent., must be smaller than or equal to 100.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($currencyOfPreference)))) {
            throw new \InvalidArgumentException("invalid value for \$currencyOfPreference when calling GetItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['currencyOfPreference'] = $currencyOfPreference;

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
     * @param string[]|null $languagesOfPreference Languages in order of preference in which the item information should be returned in response. By default the item information is returned in the default language of the marketplace.
     *
     * @return self
     */
    public function setLanguagesOfPreference($languagesOfPreference)
    {
        if (is_null($languagesOfPreference)) {
            throw new \InvalidArgumentException('non-nullable languagesOfPreference cannot be null');
        }

        if ((count($languagesOfPreference) > 1)) {
            throw new \InvalidArgumentException('invalid value for $languagesOfPreference when calling GetItemsRequestContent., number of items must be less than or equal to 1.');
        }
        $this->container['languagesOfPreference'] = $languagesOfPreference;

        return $this;
    }

    /**
     * Gets properties
     *
     * @return array<string,string>|null
     */
    public function getProperties()
    {
        return $this->container['properties'];
    }

    /**
     * Sets properties
     *
     * @param array<string,string>|null $properties Reserved parameter for specifying key-value pairs. This is a flexible mechanism for passing additional context or metadata to the API.
     *
     * @return self
     */
    public function setProperties($properties)
    {
        if (is_null($properties)) {
            throw new \InvalidArgumentException('non-nullable properties cannot be null');
        }

        if ((count($properties) > 2)) {
            throw new \InvalidArgumentException('invalid value for $properties when calling GetItemsRequestContent., number of items must be less than or equal to 2.');
        }
        $this->container['properties'] = $properties;

        return $this;
    }

    /**
     * Gets resources
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource[]|null
     */
    public function getResources()
    {
        return $this->container['resources'];
    }

    /**
     * Sets resources
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\GetItemsResource[]|null $resources resources
     *
     * @return self
     */
    public function setResources($resources)
    {
        if (is_null($resources)) {
            throw new \InvalidArgumentException('non-nullable resources cannot be null');
        }

        if ((count($resources) > 100)) {
            throw new \InvalidArgumentException('invalid value for $resources when calling GetItemsRequestContent., number of items must be less than or equal to 100.');
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


