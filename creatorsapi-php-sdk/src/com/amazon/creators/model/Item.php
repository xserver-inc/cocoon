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
 * Item Class Doc Comment
 *
 * @description Container for item information such as ASIN, Detail Page URL and other attributes. It also includes containers for various item related resources like Images, ItemInfo, etc.
 * @implements \ArrayAccess<string, mixed>
 */
class Item implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Item';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'asin' => 'string',
        'browseNodeInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\BrowseNodeInfo',
        'customerReviews' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\CustomerReviews',
        'detailPageURL' => 'string',
        'images' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\Images',
        'itemInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\ItemInfo',
        'offersV2' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OffersV2',
        'parentASIN' => 'string',
        'score' => 'float',
        'variationAttributes' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\VariationAttribute[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'asin' => null,
        'browseNodeInfo' => null,
        'customerReviews' => null,
        'detailPageURL' => null,
        'images' => null,
        'itemInfo' => null,
        'offersV2' => null,
        'parentASIN' => null,
        'score' => 'double',
        'variationAttributes' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'asin' => false,
        'browseNodeInfo' => false,
        'customerReviews' => false,
        'detailPageURL' => false,
        'images' => false,
        'itemInfo' => false,
        'offersV2' => false,
        'parentASIN' => false,
        'score' => false,
        'variationAttributes' => false
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
        'asin' => 'asin',
        'browseNodeInfo' => 'browseNodeInfo',
        'customerReviews' => 'customerReviews',
        'detailPageURL' => 'detailPageURL',
        'images' => 'images',
        'itemInfo' => 'itemInfo',
        'offersV2' => 'offersV2',
        'parentASIN' => 'parentASIN',
        'score' => 'score',
        'variationAttributes' => 'variationAttributes'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'asin' => 'setAsin',
        'browseNodeInfo' => 'setBrowseNodeInfo',
        'customerReviews' => 'setCustomerReviews',
        'detailPageURL' => 'setDetailPageURL',
        'images' => 'setImages',
        'itemInfo' => 'setItemInfo',
        'offersV2' => 'setOffersV2',
        'parentASIN' => 'setParentASIN',
        'score' => 'setScore',
        'variationAttributes' => 'setVariationAttributes'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'asin' => 'getAsin',
        'browseNodeInfo' => 'getBrowseNodeInfo',
        'customerReviews' => 'getCustomerReviews',
        'detailPageURL' => 'getDetailPageURL',
        'images' => 'getImages',
        'itemInfo' => 'getItemInfo',
        'offersV2' => 'getOffersV2',
        'parentASIN' => 'getParentASIN',
        'score' => 'getScore',
        'variationAttributes' => 'getVariationAttributes'
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
        $this->setIfExists('asin', $data ?? [], null);
        $this->setIfExists('browseNodeInfo', $data ?? [], null);
        $this->setIfExists('customerReviews', $data ?? [], null);
        $this->setIfExists('detailPageURL', $data ?? [], null);
        $this->setIfExists('images', $data ?? [], null);
        $this->setIfExists('itemInfo', $data ?? [], null);
        $this->setIfExists('offersV2', $data ?? [], null);
        $this->setIfExists('parentASIN', $data ?? [], null);
        $this->setIfExists('score', $data ?? [], null);
        $this->setIfExists('variationAttributes', $data ?? [], null);
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
     * Gets asin
     *
     * @return string|null
     */
    public function getAsin()
    {
        return $this->container['asin'];
    }

    /**
     * Sets asin
     *
     * @param string|null $asin asin
     *
     * @return self
     */
    public function setAsin($asin)
    {
        if (is_null($asin)) {
            throw new \InvalidArgumentException('non-nullable asin cannot be null');
        }
        $this->container['asin'] = $asin;

        return $this;
    }

    /**
     * Gets browseNodeInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\BrowseNodeInfo|null
     */
    public function getBrowseNodeInfo()
    {
        return $this->container['browseNodeInfo'];
    }

    /**
     * Sets browseNodeInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\BrowseNodeInfo|null $browseNodeInfo browseNodeInfo
     *
     * @return self
     */
    public function setBrowseNodeInfo($browseNodeInfo)
    {
        if (is_null($browseNodeInfo)) {
            throw new \InvalidArgumentException('non-nullable browseNodeInfo cannot be null');
        }
        $this->container['browseNodeInfo'] = $browseNodeInfo;

        return $this;
    }

    /**
     * Gets customerReviews
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\CustomerReviews|null
     */
    public function getCustomerReviews()
    {
        return $this->container['customerReviews'];
    }

    /**
     * Sets customerReviews
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\CustomerReviews|null $customerReviews customerReviews
     *
     * @return self
     */
    public function setCustomerReviews($customerReviews)
    {
        if (is_null($customerReviews)) {
            throw new \InvalidArgumentException('non-nullable customerReviews cannot be null');
        }
        $this->container['customerReviews'] = $customerReviews;

        return $this;
    }

    /**
     * Gets detailPageURL
     *
     * @return string|null
     */
    public function getDetailPageURL()
    {
        return $this->container['detailPageURL'];
    }

    /**
     * Sets detailPageURL
     *
     * @param string|null $detailPageURL detailPageURL
     *
     * @return self
     */
    public function setDetailPageURL($detailPageURL)
    {
        if (is_null($detailPageURL)) {
            throw new \InvalidArgumentException('non-nullable detailPageURL cannot be null');
        }
        $this->container['detailPageURL'] = $detailPageURL;

        return $this;
    }

    /**
     * Gets images
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Images|null
     */
    public function getImages()
    {
        return $this->container['images'];
    }

    /**
     * Sets images
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Images|null $images images
     *
     * @return self
     */
    public function setImages($images)
    {
        if (is_null($images)) {
            throw new \InvalidArgumentException('non-nullable images cannot be null');
        }
        $this->container['images'] = $images;

        return $this;
    }

    /**
     * Gets itemInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ItemInfo|null
     */
    public function getItemInfo()
    {
        return $this->container['itemInfo'];
    }

    /**
     * Sets itemInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\ItemInfo|null $itemInfo itemInfo
     *
     * @return self
     */
    public function setItemInfo($itemInfo)
    {
        if (is_null($itemInfo)) {
            throw new \InvalidArgumentException('non-nullable itemInfo cannot be null');
        }
        $this->container['itemInfo'] = $itemInfo;

        return $this;
    }

    /**
     * Gets offersV2
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OffersV2|null
     */
    public function getOffersV2()
    {
        return $this->container['offersV2'];
    }

    /**
     * Sets offersV2
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OffersV2|null $offersV2 offersV2
     *
     * @return self
     */
    public function setOffersV2($offersV2)
    {
        if (is_null($offersV2)) {
            throw new \InvalidArgumentException('non-nullable offersV2 cannot be null');
        }
        $this->container['offersV2'] = $offersV2;

        return $this;
    }

    /**
     * Gets parentASIN
     *
     * @return string|null
     */
    public function getParentASIN()
    {
        return $this->container['parentASIN'];
    }

    /**
     * Sets parentASIN
     *
     * @param string|null $parentASIN parentASIN
     *
     * @return self
     */
    public function setParentASIN($parentASIN)
    {
        if (is_null($parentASIN)) {
            throw new \InvalidArgumentException('non-nullable parentASIN cannot be null');
        }
        $this->container['parentASIN'] = $parentASIN;

        return $this;
    }

    /**
     * Gets score
     *
     * @return float|null
     */
    public function getScore()
    {
        return $this->container['score'];
    }

    /**
     * Sets score
     *
     * @param float|null $score score
     *
     * @return self
     */
    public function setScore($score)
    {
        if (is_null($score)) {
            throw new \InvalidArgumentException('non-nullable score cannot be null');
        }
        $this->container['score'] = $score;

        return $this;
    }

    /**
     * Gets variationAttributes
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\VariationAttribute[]|null
     */
    public function getVariationAttributes()
    {
        return $this->container['variationAttributes'];
    }

    /**
     * Sets variationAttributes
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\VariationAttribute[]|null $variationAttributes List of offer listing associated with a product.
     *
     * @return self
     */
    public function setVariationAttributes($variationAttributes)
    {
        if (is_null($variationAttributes)) {
            throw new \InvalidArgumentException('non-nullable variationAttributes cannot be null');
        }
        $this->container['variationAttributes'] = $variationAttributes;

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


