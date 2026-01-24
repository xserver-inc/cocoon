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
 * OfferListingV2 Class Doc Comment
 *
 * @description Specifies about various offer listings associated with the product.
 * @implements \ArrayAccess<string, mixed>
 */
class OfferListingV2 implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'OfferListingV2';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'availability' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferAvailabilityV2',
        'condition' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferConditionV2',
        'dealDetails' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\DealDetails',
        'isBuyBoxWinner' => 'bool',
        'loyaltyPoints' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferLoyaltyPointsV2',
        'merchantInfo' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferMerchantInfoV2',
        'price' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferPriceV2',
        'type' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferType',
        'violatesMAP' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'availability' => null,
        'condition' => null,
        'dealDetails' => null,
        'isBuyBoxWinner' => null,
        'loyaltyPoints' => null,
        'merchantInfo' => null,
        'price' => null,
        'type' => null,
        'violatesMAP' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'availability' => false,
        'condition' => false,
        'dealDetails' => false,
        'isBuyBoxWinner' => false,
        'loyaltyPoints' => false,
        'merchantInfo' => false,
        'price' => false,
        'type' => false,
        'violatesMAP' => false
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
        'availability' => 'availability',
        'condition' => 'condition',
        'dealDetails' => 'dealDetails',
        'isBuyBoxWinner' => 'isBuyBoxWinner',
        'loyaltyPoints' => 'loyaltyPoints',
        'merchantInfo' => 'merchantInfo',
        'price' => 'price',
        'type' => 'type',
        'violatesMAP' => 'violatesMAP'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'availability' => 'setAvailability',
        'condition' => 'setCondition',
        'dealDetails' => 'setDealDetails',
        'isBuyBoxWinner' => 'setIsBuyBoxWinner',
        'loyaltyPoints' => 'setLoyaltyPoints',
        'merchantInfo' => 'setMerchantInfo',
        'price' => 'setPrice',
        'type' => 'setType',
        'violatesMAP' => 'setViolatesMAP'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'availability' => 'getAvailability',
        'condition' => 'getCondition',
        'dealDetails' => 'getDealDetails',
        'isBuyBoxWinner' => 'getIsBuyBoxWinner',
        'loyaltyPoints' => 'getLoyaltyPoints',
        'merchantInfo' => 'getMerchantInfo',
        'price' => 'getPrice',
        'type' => 'getType',
        'violatesMAP' => 'getViolatesMAP'
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
        $this->setIfExists('availability', $data ?? [], null);
        $this->setIfExists('condition', $data ?? [], null);
        $this->setIfExists('dealDetails', $data ?? [], null);
        $this->setIfExists('isBuyBoxWinner', $data ?? [], null);
        $this->setIfExists('loyaltyPoints', $data ?? [], null);
        $this->setIfExists('merchantInfo', $data ?? [], null);
        $this->setIfExists('price', $data ?? [], null);
        $this->setIfExists('type', $data ?? [], null);
        $this->setIfExists('violatesMAP', $data ?? [], null);
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
     * Gets availability
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferAvailabilityV2|null
     */
    public function getAvailability()
    {
        return $this->container['availability'];
    }

    /**
     * Sets availability
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferAvailabilityV2|null $availability availability
     *
     * @return self
     */
    public function setAvailability($availability)
    {
        if (is_null($availability)) {
            throw new \InvalidArgumentException('non-nullable availability cannot be null');
        }
        $this->container['availability'] = $availability;

        return $this;
    }

    /**
     * Gets condition
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferConditionV2|null
     */
    public function getCondition()
    {
        return $this->container['condition'];
    }

    /**
     * Sets condition
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferConditionV2|null $condition condition
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
     * Gets dealDetails
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\DealDetails|null
     */
    public function getDealDetails()
    {
        return $this->container['dealDetails'];
    }

    /**
     * Sets dealDetails
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\DealDetails|null $dealDetails dealDetails
     *
     * @return self
     */
    public function setDealDetails($dealDetails)
    {
        if (is_null($dealDetails)) {
            throw new \InvalidArgumentException('non-nullable dealDetails cannot be null');
        }
        $this->container['dealDetails'] = $dealDetails;

        return $this;
    }

    /**
     * Gets isBuyBoxWinner
     *
     * @return bool|null
     */
    public function getIsBuyBoxWinner()
    {
        return $this->container['isBuyBoxWinner'];
    }

    /**
     * Sets isBuyBoxWinner
     *
     * @param bool|null $isBuyBoxWinner isBuyBoxWinner
     *
     * @return self
     */
    public function setIsBuyBoxWinner($isBuyBoxWinner)
    {
        if (is_null($isBuyBoxWinner)) {
            throw new \InvalidArgumentException('non-nullable isBuyBoxWinner cannot be null');
        }
        $this->container['isBuyBoxWinner'] = $isBuyBoxWinner;

        return $this;
    }

    /**
     * Gets loyaltyPoints
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferLoyaltyPointsV2|null
     */
    public function getLoyaltyPoints()
    {
        return $this->container['loyaltyPoints'];
    }

    /**
     * Sets loyaltyPoints
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferLoyaltyPointsV2|null $loyaltyPoints loyaltyPoints
     *
     * @return self
     */
    public function setLoyaltyPoints($loyaltyPoints)
    {
        if (is_null($loyaltyPoints)) {
            throw new \InvalidArgumentException('non-nullable loyaltyPoints cannot be null');
        }
        $this->container['loyaltyPoints'] = $loyaltyPoints;

        return $this;
    }

    /**
     * Gets merchantInfo
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferMerchantInfoV2|null
     */
    public function getMerchantInfo()
    {
        return $this->container['merchantInfo'];
    }

    /**
     * Sets merchantInfo
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferMerchantInfoV2|null $merchantInfo merchantInfo
     *
     * @return self
     */
    public function setMerchantInfo($merchantInfo)
    {
        if (is_null($merchantInfo)) {
            throw new \InvalidArgumentException('non-nullable merchantInfo cannot be null');
        }
        $this->container['merchantInfo'] = $merchantInfo;

        return $this;
    }

    /**
     * Gets price
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferPriceV2|null
     */
    public function getPrice()
    {
        return $this->container['price'];
    }

    /**
     * Sets price
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferPriceV2|null $price price
     *
     * @return self
     */
    public function setPrice($price)
    {
        if (is_null($price)) {
            throw new \InvalidArgumentException('non-nullable price cannot be null');
        }
        $this->container['price'] = $price;

        return $this;
    }

    /**
     * Gets type
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferType|null
     */
    public function getType()
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\OfferType|null $type type
     *
     * @return self
     */
    public function setType($type)
    {
        if (is_null($type)) {
            throw new \InvalidArgumentException('non-nullable type cannot be null');
        }
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets violatesMAP
     *
     * @return bool|null
     */
    public function getViolatesMAP()
    {
        return $this->container['violatesMAP'];
    }

    /**
     * Sets violatesMAP
     *
     * @param bool|null $violatesMAP violatesMAP
     *
     * @return self
     */
    public function setViolatesMAP($violatesMAP)
    {
        if (is_null($violatesMAP)) {
            throw new \InvalidArgumentException('non-nullable violatesMAP cannot be null');
        }
        $this->container['violatesMAP'] = $violatesMAP;

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


