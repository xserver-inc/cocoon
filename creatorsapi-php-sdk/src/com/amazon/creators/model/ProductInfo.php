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
 * ProductInfo Class Doc Comment
 *
 * @description Container for set of attributes that describes non-technical aspects of the product.
 * @implements \ArrayAccess<string, mixed>
 */
class ProductInfo implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'ProductInfo';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'color' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute',
        'isAdultProduct' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleBooleanValuedAttribute',
        'itemDimensions' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\DimensionBasedAttribute',
        'releaseDate' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute',
        'size' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute',
        'unitCount' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleIntegerValuedAttribute'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'color' => null,
        'isAdultProduct' => null,
        'itemDimensions' => null,
        'releaseDate' => null,
        'size' => null,
        'unitCount' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'color' => false,
        'isAdultProduct' => false,
        'itemDimensions' => false,
        'releaseDate' => false,
        'size' => false,
        'unitCount' => false
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
        'color' => 'color',
        'isAdultProduct' => 'isAdultProduct',
        'itemDimensions' => 'itemDimensions',
        'releaseDate' => 'releaseDate',
        'size' => 'size',
        'unitCount' => 'unitCount'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'color' => 'setColor',
        'isAdultProduct' => 'setIsAdultProduct',
        'itemDimensions' => 'setItemDimensions',
        'releaseDate' => 'setReleaseDate',
        'size' => 'setSize',
        'unitCount' => 'setUnitCount'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'color' => 'getColor',
        'isAdultProduct' => 'getIsAdultProduct',
        'itemDimensions' => 'getItemDimensions',
        'releaseDate' => 'getReleaseDate',
        'size' => 'getSize',
        'unitCount' => 'getUnitCount'
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
        $this->setIfExists('color', $data ?? [], null);
        $this->setIfExists('isAdultProduct', $data ?? [], null);
        $this->setIfExists('itemDimensions', $data ?? [], null);
        $this->setIfExists('releaseDate', $data ?? [], null);
        $this->setIfExists('size', $data ?? [], null);
        $this->setIfExists('unitCount', $data ?? [], null);
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
     * Gets color
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null
     */
    public function getColor()
    {
        return $this->container['color'];
    }

    /**
     * Sets color
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null $color color
     *
     * @return self
     */
    public function setColor($color)
    {
        if (is_null($color)) {
            throw new \InvalidArgumentException('non-nullable color cannot be null');
        }
        $this->container['color'] = $color;

        return $this;
    }

    /**
     * Gets isAdultProduct
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleBooleanValuedAttribute|null
     */
    public function getIsAdultProduct()
    {
        return $this->container['isAdultProduct'];
    }

    /**
     * Sets isAdultProduct
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleBooleanValuedAttribute|null $isAdultProduct isAdultProduct
     *
     * @return self
     */
    public function setIsAdultProduct($isAdultProduct)
    {
        if (is_null($isAdultProduct)) {
            throw new \InvalidArgumentException('non-nullable isAdultProduct cannot be null');
        }
        $this->container['isAdultProduct'] = $isAdultProduct;

        return $this;
    }

    /**
     * Gets itemDimensions
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\DimensionBasedAttribute|null
     */
    public function getItemDimensions()
    {
        return $this->container['itemDimensions'];
    }

    /**
     * Sets itemDimensions
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\DimensionBasedAttribute|null $itemDimensions itemDimensions
     *
     * @return self
     */
    public function setItemDimensions($itemDimensions)
    {
        if (is_null($itemDimensions)) {
            throw new \InvalidArgumentException('non-nullable itemDimensions cannot be null');
        }
        $this->container['itemDimensions'] = $itemDimensions;

        return $this;
    }

    /**
     * Gets releaseDate
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null
     */
    public function getReleaseDate()
    {
        return $this->container['releaseDate'];
    }

    /**
     * Sets releaseDate
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null $releaseDate releaseDate
     *
     * @return self
     */
    public function setReleaseDate($releaseDate)
    {
        if (is_null($releaseDate)) {
            throw new \InvalidArgumentException('non-nullable releaseDate cannot be null');
        }
        $this->container['releaseDate'] = $releaseDate;

        return $this;
    }

    /**
     * Gets size
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null
     */
    public function getSize()
    {
        return $this->container['size'];
    }

    /**
     * Sets size
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null $size size
     *
     * @return self
     */
    public function setSize($size)
    {
        if (is_null($size)) {
            throw new \InvalidArgumentException('non-nullable size cannot be null');
        }
        $this->container['size'] = $size;

        return $this;
    }

    /**
     * Gets unitCount
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleIntegerValuedAttribute|null
     */
    public function getUnitCount()
    {
        return $this->container['unitCount'];
    }

    /**
     * Sets unitCount
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleIntegerValuedAttribute|null $unitCount unitCount
     *
     * @return self
     */
    public function setUnitCount($unitCount)
    {
        if (is_null($unitCount)) {
            throw new \InvalidArgumentException('non-nullable unitCount cannot be null');
        }
        $this->container['unitCount'] = $unitCount;

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


