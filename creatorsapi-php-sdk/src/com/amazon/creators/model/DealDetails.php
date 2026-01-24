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
 * DealDetails Class Doc Comment
 *
 * @description Specifies deal information about an offer.
 * @implements \ArrayAccess<string, mixed>
 */
class DealDetails implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'DealDetails';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'accessType' => 'string',
        'badge' => 'string',
        'earlyAccessDurationInMilliseconds' => 'float',
        'endTime' => 'string',
        'percentClaimed' => 'float',
        'startTime' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'accessType' => null,
        'badge' => null,
        'earlyAccessDurationInMilliseconds' => null,
        'endTime' => null,
        'percentClaimed' => null,
        'startTime' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'accessType' => false,
        'badge' => false,
        'earlyAccessDurationInMilliseconds' => false,
        'endTime' => false,
        'percentClaimed' => false,
        'startTime' => false
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
        'accessType' => 'accessType',
        'badge' => 'badge',
        'earlyAccessDurationInMilliseconds' => 'earlyAccessDurationInMilliseconds',
        'endTime' => 'endTime',
        'percentClaimed' => 'percentClaimed',
        'startTime' => 'startTime'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'accessType' => 'setAccessType',
        'badge' => 'setBadge',
        'earlyAccessDurationInMilliseconds' => 'setEarlyAccessDurationInMilliseconds',
        'endTime' => 'setEndTime',
        'percentClaimed' => 'setPercentClaimed',
        'startTime' => 'setStartTime'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'accessType' => 'getAccessType',
        'badge' => 'getBadge',
        'earlyAccessDurationInMilliseconds' => 'getEarlyAccessDurationInMilliseconds',
        'endTime' => 'getEndTime',
        'percentClaimed' => 'getPercentClaimed',
        'startTime' => 'getStartTime'
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
        $this->setIfExists('accessType', $data ?? [], null);
        $this->setIfExists('badge', $data ?? [], null);
        $this->setIfExists('earlyAccessDurationInMilliseconds', $data ?? [], null);
        $this->setIfExists('endTime', $data ?? [], null);
        $this->setIfExists('percentClaimed', $data ?? [], null);
        $this->setIfExists('startTime', $data ?? [], null);
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
     * Gets accessType
     *
     * @return string|null
     */
    public function getAccessType()
    {
        return $this->container['accessType'];
    }

    /**
     * Sets accessType
     *
     * @param string|null $accessType accessType
     *
     * @return self
     */
    public function setAccessType($accessType)
    {
        if (is_null($accessType)) {
            throw new \InvalidArgumentException('non-nullable accessType cannot be null');
        }
        $this->container['accessType'] = $accessType;

        return $this;
    }

    /**
     * Gets badge
     *
     * @return string|null
     */
    public function getBadge()
    {
        return $this->container['badge'];
    }

    /**
     * Sets badge
     *
     * @param string|null $badge badge
     *
     * @return self
     */
    public function setBadge($badge)
    {
        if (is_null($badge)) {
            throw new \InvalidArgumentException('non-nullable badge cannot be null');
        }
        $this->container['badge'] = $badge;

        return $this;
    }

    /**
     * Gets earlyAccessDurationInMilliseconds
     *
     * @return float|null
     */
    public function getEarlyAccessDurationInMilliseconds()
    {
        return $this->container['earlyAccessDurationInMilliseconds'];
    }

    /**
     * Sets earlyAccessDurationInMilliseconds
     *
     * @param float|null $earlyAccessDurationInMilliseconds earlyAccessDurationInMilliseconds
     *
     * @return self
     */
    public function setEarlyAccessDurationInMilliseconds($earlyAccessDurationInMilliseconds)
    {
        if (is_null($earlyAccessDurationInMilliseconds)) {
            throw new \InvalidArgumentException('non-nullable earlyAccessDurationInMilliseconds cannot be null');
        }
        $this->container['earlyAccessDurationInMilliseconds'] = $earlyAccessDurationInMilliseconds;

        return $this;
    }

    /**
     * Gets endTime
     *
     * @return string|null
     */
    public function getEndTime()
    {
        return $this->container['endTime'];
    }

    /**
     * Sets endTime
     *
     * @param string|null $endTime endTime
     *
     * @return self
     */
    public function setEndTime($endTime)
    {
        if (is_null($endTime)) {
            throw new \InvalidArgumentException('non-nullable endTime cannot be null');
        }
        $this->container['endTime'] = $endTime;

        return $this;
    }

    /**
     * Gets percentClaimed
     *
     * @return float|null
     */
    public function getPercentClaimed()
    {
        return $this->container['percentClaimed'];
    }

    /**
     * Sets percentClaimed
     *
     * @param float|null $percentClaimed percentClaimed
     *
     * @return self
     */
    public function setPercentClaimed($percentClaimed)
    {
        if (is_null($percentClaimed)) {
            throw new \InvalidArgumentException('non-nullable percentClaimed cannot be null');
        }
        $this->container['percentClaimed'] = $percentClaimed;

        return $this;
    }

    /**
     * Gets startTime
     *
     * @return string|null
     */
    public function getStartTime()
    {
        return $this->container['startTime'];
    }

    /**
     * Sets startTime
     *
     * @param string|null $startTime startTime
     *
     * @return self
     */
    public function setStartTime($startTime)
    {
        if (is_null($startTime)) {
            throw new \InvalidArgumentException('non-nullable startTime cannot be null');
        }
        $this->container['startTime'] = $startTime;

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


