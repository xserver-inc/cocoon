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
 * ItemInfo Class Doc Comment
 *
 * @description Container for ItemInfo high level resource which is a collection of large number of attributes describing a product.
 * @implements \ArrayAccess<string, mixed>
 */
class ItemInfo implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'ItemInfo';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'byLineInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ByLineInfo',
        'classifications' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\Classifications',
        'contentInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ContentInfo',
        'contentRating' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ContentRating',
        'externalIds' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ExternalIds',
        'features' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\MultiValuedAttribute',
        'manufactureInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ManufactureInfo',
        'productInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ProductInfo',
        'technicalInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\TechnicalInfo',
        'title' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute',
        'tradeInInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\TradeInInfo'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'byLineInfo' => null,
        'classifications' => null,
        'contentInfo' => null,
        'contentRating' => null,
        'externalIds' => null,
        'features' => null,
        'manufactureInfo' => null,
        'productInfo' => null,
        'technicalInfo' => null,
        'title' => null,
        'tradeInInfo' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'byLineInfo' => false,
        'classifications' => false,
        'contentInfo' => false,
        'contentRating' => false,
        'externalIds' => false,
        'features' => false,
        'manufactureInfo' => false,
        'productInfo' => false,
        'technicalInfo' => false,
        'title' => false,
        'tradeInInfo' => false
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
        'byLineInfo' => 'byLineInfo',
        'classifications' => 'classifications',
        'contentInfo' => 'contentInfo',
        'contentRating' => 'contentRating',
        'externalIds' => 'externalIds',
        'features' => 'features',
        'manufactureInfo' => 'manufactureInfo',
        'productInfo' => 'productInfo',
        'technicalInfo' => 'technicalInfo',
        'title' => 'title',
        'tradeInInfo' => 'tradeInInfo'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'byLineInfo' => 'setByLineInfo',
        'classifications' => 'setClassifications',
        'contentInfo' => 'setContentInfo',
        'contentRating' => 'setContentRating',
        'externalIds' => 'setExternalIds',
        'features' => 'setFeatures',
        'manufactureInfo' => 'setManufactureInfo',
        'productInfo' => 'setProductInfo',
        'technicalInfo' => 'setTechnicalInfo',
        'title' => 'setTitle',
        'tradeInInfo' => 'setTradeInInfo'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'byLineInfo' => 'getByLineInfo',
        'classifications' => 'getClassifications',
        'contentInfo' => 'getContentInfo',
        'contentRating' => 'getContentRating',
        'externalIds' => 'getExternalIds',
        'features' => 'getFeatures',
        'manufactureInfo' => 'getManufactureInfo',
        'productInfo' => 'getProductInfo',
        'technicalInfo' => 'getTechnicalInfo',
        'title' => 'getTitle',
        'tradeInInfo' => 'getTradeInInfo'
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
        $this->setIfExists('byLineInfo', $data ?? [], null);
        $this->setIfExists('classifications', $data ?? [], null);
        $this->setIfExists('contentInfo', $data ?? [], null);
        $this->setIfExists('contentRating', $data ?? [], null);
        $this->setIfExists('externalIds', $data ?? [], null);
        $this->setIfExists('features', $data ?? [], null);
        $this->setIfExists('manufactureInfo', $data ?? [], null);
        $this->setIfExists('productInfo', $data ?? [], null);
        $this->setIfExists('technicalInfo', $data ?? [], null);
        $this->setIfExists('title', $data ?? [], null);
        $this->setIfExists('tradeInInfo', $data ?? [], null);
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
     * Gets byLineInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ByLineInfo|null
     */
    public function getByLineInfo()
    {
        return $this->container['byLineInfo'];
    }

    /**
     * Sets byLineInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ByLineInfo|null $byLineInfo byLineInfo
     *
     * @return self
     */
    public function setByLineInfo($byLineInfo)
    {
        if (is_null($byLineInfo)) {
            throw new \InvalidArgumentException('non-nullable byLineInfo cannot be null');
        }
        $this->container['byLineInfo'] = $byLineInfo;

        return $this;
    }

    /**
     * Gets classifications
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Classifications|null
     */
    public function getClassifications()
    {
        return $this->container['classifications'];
    }

    /**
     * Sets classifications
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Classifications|null $classifications classifications
     *
     * @return self
     */
    public function setClassifications($classifications)
    {
        if (is_null($classifications)) {
            throw new \InvalidArgumentException('non-nullable classifications cannot be null');
        }
        $this->container['classifications'] = $classifications;

        return $this;
    }

    /**
     * Gets contentInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ContentInfo|null
     */
    public function getContentInfo()
    {
        return $this->container['contentInfo'];
    }

    /**
     * Sets contentInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ContentInfo|null $contentInfo contentInfo
     *
     * @return self
     */
    public function setContentInfo($contentInfo)
    {
        if (is_null($contentInfo)) {
            throw new \InvalidArgumentException('non-nullable contentInfo cannot be null');
        }
        $this->container['contentInfo'] = $contentInfo;

        return $this;
    }

    /**
     * Gets contentRating
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ContentRating|null
     */
    public function getContentRating()
    {
        return $this->container['contentRating'];
    }

    /**
     * Sets contentRating
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ContentRating|null $contentRating contentRating
     *
     * @return self
     */
    public function setContentRating($contentRating)
    {
        if (is_null($contentRating)) {
            throw new \InvalidArgumentException('non-nullable contentRating cannot be null');
        }
        $this->container['contentRating'] = $contentRating;

        return $this;
    }

    /**
     * Gets externalIds
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ExternalIds|null
     */
    public function getExternalIds()
    {
        return $this->container['externalIds'];
    }

    /**
     * Sets externalIds
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ExternalIds|null $externalIds externalIds
     *
     * @return self
     */
    public function setExternalIds($externalIds)
    {
        if (is_null($externalIds)) {
            throw new \InvalidArgumentException('non-nullable externalIds cannot be null');
        }
        $this->container['externalIds'] = $externalIds;

        return $this;
    }

    /**
     * Gets features
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\MultiValuedAttribute|null
     */
    public function getFeatures()
    {
        return $this->container['features'];
    }

    /**
     * Sets features
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\MultiValuedAttribute|null $features features
     *
     * @return self
     */
    public function setFeatures($features)
    {
        if (is_null($features)) {
            throw new \InvalidArgumentException('non-nullable features cannot be null');
        }
        $this->container['features'] = $features;

        return $this;
    }

    /**
     * Gets manufactureInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ManufactureInfo|null
     */
    public function getManufactureInfo()
    {
        return $this->container['manufactureInfo'];
    }

    /**
     * Sets manufactureInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ManufactureInfo|null $manufactureInfo manufactureInfo
     *
     * @return self
     */
    public function setManufactureInfo($manufactureInfo)
    {
        if (is_null($manufactureInfo)) {
            throw new \InvalidArgumentException('non-nullable manufactureInfo cannot be null');
        }
        $this->container['manufactureInfo'] = $manufactureInfo;

        return $this;
    }

    /**
     * Gets productInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ProductInfo|null
     */
    public function getProductInfo()
    {
        return $this->container['productInfo'];
    }

    /**
     * Sets productInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ProductInfo|null $productInfo productInfo
     *
     * @return self
     */
    public function setProductInfo($productInfo)
    {
        if (is_null($productInfo)) {
            throw new \InvalidArgumentException('non-nullable productInfo cannot be null');
        }
        $this->container['productInfo'] = $productInfo;

        return $this;
    }

    /**
     * Gets technicalInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\TechnicalInfo|null
     */
    public function getTechnicalInfo()
    {
        return $this->container['technicalInfo'];
    }

    /**
     * Sets technicalInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\TechnicalInfo|null $technicalInfo technicalInfo
     *
     * @return self
     */
    public function setTechnicalInfo($technicalInfo)
    {
        if (is_null($technicalInfo)) {
            throw new \InvalidArgumentException('non-nullable technicalInfo cannot be null');
        }
        $this->container['technicalInfo'] = $technicalInfo;

        return $this;
    }

    /**
     * Gets title
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null
     */
    public function getTitle()
    {
        return $this->container['title'];
    }

    /**
     * Sets title
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SingleStringValuedAttribute|null $title title
     *
     * @return self
     */
    public function setTitle($title)
    {
        if (is_null($title)) {
            throw new \InvalidArgumentException('non-nullable title cannot be null');
        }
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * Gets tradeInInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\TradeInInfo|null
     */
    public function getTradeInInfo()
    {
        return $this->container['tradeInInfo'];
    }

    /**
     * Sets tradeInInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\TradeInInfo|null $tradeInInfo tradeInInfo
     *
     * @return self
     */
    public function setTradeInInfo($tradeInInfo)
    {
        if (is_null($tradeInInfo)) {
            throw new \InvalidArgumentException('non-nullable tradeInInfo cannot be null');
        }
        $this->container['tradeInInfo'] = $tradeInInfo;

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


