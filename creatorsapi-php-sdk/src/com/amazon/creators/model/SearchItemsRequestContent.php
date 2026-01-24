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
 * SearchItemsRequestContent Class Doc Comment
 *
 * @description The request object for the search items operation. It contains the request parameters for the search items operation.
 * @implements \ArrayAccess<string, mixed>
 */
class SearchItemsRequestContent implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'SearchItemsRequestContent';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'actor' => 'string',
        'artist' => 'string',
        'author' => 'string',
        'availability' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\Availability',
        'brand' => 'string',
        'browseNodeId' => 'string',
        'condition' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\Condition',
        'currencyOfPreference' => 'string',
        'deliveryFlags' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\DeliveryFlag[]',
        'itemCount' => 'float',
        'itemPage' => 'float',
        'keywords' => 'string',
        'languagesOfPreference' => 'string[]',
        'maxPrice' => 'float',
        'minPrice' => 'float',
        'minReviewsRating' => 'float',
        'minSavingPercent' => 'float',
        'partnerTag' => 'string',
        'properties' => 'array<string,string>',
        'resources' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResource[]',
        'searchIndex' => 'string',
        'sortBy' => '\Amazon\CreatorsAPI\v1\com\amazon\creators\model\SortBy',
        'title' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'actor' => null,
        'artist' => null,
        'author' => null,
        'availability' => null,
        'brand' => null,
        'browseNodeId' => null,
        'condition' => null,
        'currencyOfPreference' => null,
        'deliveryFlags' => null,
        'itemCount' => null,
        'itemPage' => null,
        'keywords' => null,
        'languagesOfPreference' => null,
        'maxPrice' => null,
        'minPrice' => null,
        'minReviewsRating' => null,
        'minSavingPercent' => null,
        'partnerTag' => null,
        'properties' => null,
        'resources' => null,
        'searchIndex' => null,
        'sortBy' => null,
        'title' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'actor' => false,
        'artist' => false,
        'author' => false,
        'availability' => false,
        'brand' => false,
        'browseNodeId' => false,
        'condition' => false,
        'currencyOfPreference' => false,
        'deliveryFlags' => false,
        'itemCount' => false,
        'itemPage' => false,
        'keywords' => false,
        'languagesOfPreference' => false,
        'maxPrice' => false,
        'minPrice' => false,
        'minReviewsRating' => false,
        'minSavingPercent' => false,
        'partnerTag' => false,
        'properties' => false,
        'resources' => false,
        'searchIndex' => false,
        'sortBy' => false,
        'title' => false
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
        'actor' => 'actor',
        'artist' => 'artist',
        'author' => 'author',
        'availability' => 'availability',
        'brand' => 'brand',
        'browseNodeId' => 'browseNodeId',
        'condition' => 'condition',
        'currencyOfPreference' => 'currencyOfPreference',
        'deliveryFlags' => 'deliveryFlags',
        'itemCount' => 'itemCount',
        'itemPage' => 'itemPage',
        'keywords' => 'keywords',
        'languagesOfPreference' => 'languagesOfPreference',
        'maxPrice' => 'maxPrice',
        'minPrice' => 'minPrice',
        'minReviewsRating' => 'minReviewsRating',
        'minSavingPercent' => 'minSavingPercent',
        'partnerTag' => 'partnerTag',
        'properties' => 'properties',
        'resources' => 'resources',
        'searchIndex' => 'searchIndex',
        'sortBy' => 'sortBy',
        'title' => 'title'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'actor' => 'setActor',
        'artist' => 'setArtist',
        'author' => 'setAuthor',
        'availability' => 'setAvailability',
        'brand' => 'setBrand',
        'browseNodeId' => 'setBrowseNodeId',
        'condition' => 'setCondition',
        'currencyOfPreference' => 'setCurrencyOfPreference',
        'deliveryFlags' => 'setDeliveryFlags',
        'itemCount' => 'setItemCount',
        'itemPage' => 'setItemPage',
        'keywords' => 'setKeywords',
        'languagesOfPreference' => 'setLanguagesOfPreference',
        'maxPrice' => 'setMaxPrice',
        'minPrice' => 'setMinPrice',
        'minReviewsRating' => 'setMinReviewsRating',
        'minSavingPercent' => 'setMinSavingPercent',
        'partnerTag' => 'setPartnerTag',
        'properties' => 'setProperties',
        'resources' => 'setResources',
        'searchIndex' => 'setSearchIndex',
        'sortBy' => 'setSortBy',
        'title' => 'setTitle'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'actor' => 'getActor',
        'artist' => 'getArtist',
        'author' => 'getAuthor',
        'availability' => 'getAvailability',
        'brand' => 'getBrand',
        'browseNodeId' => 'getBrowseNodeId',
        'condition' => 'getCondition',
        'currencyOfPreference' => 'getCurrencyOfPreference',
        'deliveryFlags' => 'getDeliveryFlags',
        'itemCount' => 'getItemCount',
        'itemPage' => 'getItemPage',
        'keywords' => 'getKeywords',
        'languagesOfPreference' => 'getLanguagesOfPreference',
        'maxPrice' => 'getMaxPrice',
        'minPrice' => 'getMinPrice',
        'minReviewsRating' => 'getMinReviewsRating',
        'minSavingPercent' => 'getMinSavingPercent',
        'partnerTag' => 'getPartnerTag',
        'properties' => 'getProperties',
        'resources' => 'getResources',
        'searchIndex' => 'getSearchIndex',
        'sortBy' => 'getSortBy',
        'title' => 'getTitle'
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
        $this->setIfExists('actor', $data ?? [], null);
        $this->setIfExists('artist', $data ?? [], null);
        $this->setIfExists('author', $data ?? [], null);
        $this->setIfExists('availability', $data ?? [], null);
        $this->setIfExists('brand', $data ?? [], null);
        $this->setIfExists('browseNodeId', $data ?? [], null);
        $this->setIfExists('condition', $data ?? [], null);
        $this->setIfExists('currencyOfPreference', $data ?? [], null);
        $this->setIfExists('deliveryFlags', $data ?? [], null);
        $this->setIfExists('itemCount', $data ?? [], null);
        $this->setIfExists('itemPage', $data ?? [], null);
        $this->setIfExists('keywords', $data ?? [], null);
        $this->setIfExists('languagesOfPreference', $data ?? [], null);
        $this->setIfExists('maxPrice', $data ?? [], null);
        $this->setIfExists('minPrice', $data ?? [], null);
        $this->setIfExists('minReviewsRating', $data ?? [], null);
        $this->setIfExists('minSavingPercent', $data ?? [], null);
        $this->setIfExists('partnerTag', $data ?? [], null);
        $this->setIfExists('properties', $data ?? [], null);
        $this->setIfExists('resources', $data ?? [], null);
        $this->setIfExists('searchIndex', $data ?? [], null);
        $this->setIfExists('sortBy', $data ?? [], null);
        $this->setIfExists('title', $data ?? [], null);
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

        if (!is_null($this->container['actor']) && (mb_strlen($this->container['actor']) > 1000)) {
            $invalidProperties[] = "invalid value for 'actor', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['actor']) && !preg_match("/.*\\S.*/", $this->container['actor'])) {
            $invalidProperties[] = "invalid value for 'actor', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['artist']) && (mb_strlen($this->container['artist']) > 1000)) {
            $invalidProperties[] = "invalid value for 'artist', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['artist']) && !preg_match("/.*\\S.*/", $this->container['artist'])) {
            $invalidProperties[] = "invalid value for 'artist', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['author']) && (mb_strlen($this->container['author']) > 1000)) {
            $invalidProperties[] = "invalid value for 'author', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['author']) && !preg_match("/.*\\S.*/", $this->container['author'])) {
            $invalidProperties[] = "invalid value for 'author', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['brand']) && (mb_strlen($this->container['brand']) > 1000)) {
            $invalidProperties[] = "invalid value for 'brand', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['brand']) && !preg_match("/.*\\S.*/", $this->container['brand'])) {
            $invalidProperties[] = "invalid value for 'brand', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['browseNodeId']) && (mb_strlen($this->container['browseNodeId']) > 19)) {
            $invalidProperties[] = "invalid value for 'browseNodeId', the character length must be smaller than or equal to 19.";
        }

        if (!is_null($this->container['browseNodeId']) && !preg_match("/^[1-9][0-9]*$/", $this->container['browseNodeId'])) {
            $invalidProperties[] = "invalid value for 'browseNodeId', must be conform to the pattern /^[1-9][0-9]*$/.";
        }

        if (!is_null($this->container['currencyOfPreference']) && (mb_strlen($this->container['currencyOfPreference']) > 100)) {
            $invalidProperties[] = "invalid value for 'currencyOfPreference', the character length must be smaller than or equal to 100.";
        }

        if (!is_null($this->container['currencyOfPreference']) && !preg_match("/.*\\S.*/", $this->container['currencyOfPreference'])) {
            $invalidProperties[] = "invalid value for 'currencyOfPreference', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['deliveryFlags']) && (count($this->container['deliveryFlags']) > 100)) {
            $invalidProperties[] = "invalid value for 'deliveryFlags', number of items must be less than or equal to 100.";
        }

        if (!is_null($this->container['itemCount']) && ($this->container['itemCount'] > 100)) {
            $invalidProperties[] = "invalid value for 'itemCount', must be smaller than or equal to 100.";
        }

        if (!is_null($this->container['itemCount']) && ($this->container['itemCount'] < 1)) {
            $invalidProperties[] = "invalid value for 'itemCount', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['itemPage']) && ($this->container['itemPage'] > 10)) {
            $invalidProperties[] = "invalid value for 'itemPage', must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['itemPage']) && ($this->container['itemPage'] < 1)) {
            $invalidProperties[] = "invalid value for 'itemPage', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['keywords']) && (mb_strlen($this->container['keywords']) > 1000)) {
            $invalidProperties[] = "invalid value for 'keywords', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['keywords']) && !preg_match("/.*\\S.*/", $this->container['keywords'])) {
            $invalidProperties[] = "invalid value for 'keywords', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['languagesOfPreference']) && (count($this->container['languagesOfPreference']) > 1)) {
            $invalidProperties[] = "invalid value for 'languagesOfPreference', number of items must be less than or equal to 1.";
        }

        if (!is_null($this->container['maxPrice']) && ($this->container['maxPrice'] < 1)) {
            $invalidProperties[] = "invalid value for 'maxPrice', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['minPrice']) && ($this->container['minPrice'] < 1)) {
            $invalidProperties[] = "invalid value for 'minPrice', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['minReviewsRating']) && ($this->container['minReviewsRating'] > 4)) {
            $invalidProperties[] = "invalid value for 'minReviewsRating', must be smaller than or equal to 4.";
        }

        if (!is_null($this->container['minReviewsRating']) && ($this->container['minReviewsRating'] < 1)) {
            $invalidProperties[] = "invalid value for 'minReviewsRating', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['minSavingPercent']) && ($this->container['minSavingPercent'] > 99)) {
            $invalidProperties[] = "invalid value for 'minSavingPercent', must be smaller than or equal to 99.";
        }

        if (!is_null($this->container['minSavingPercent']) && ($this->container['minSavingPercent'] < 1)) {
            $invalidProperties[] = "invalid value for 'minSavingPercent', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['partnerTag']) && (mb_strlen($this->container['partnerTag']) > 64)) {
            $invalidProperties[] = "invalid value for 'partnerTag', the character length must be smaller than or equal to 64.";
        }

        if (!is_null($this->container['partnerTag']) && !preg_match("/.*\\S.*/", $this->container['partnerTag'])) {
            $invalidProperties[] = "invalid value for 'partnerTag', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['properties']) && (count($this->container['properties']) > 2)) {
            $invalidProperties[] = "invalid value for 'properties', number of items must be less than or equal to 2.";
        }

        if (!is_null($this->container['resources']) && (count($this->container['resources']) > 100)) {
            $invalidProperties[] = "invalid value for 'resources', number of items must be less than or equal to 100.";
        }

        if (!is_null($this->container['searchIndex']) && (mb_strlen($this->container['searchIndex']) > 1000)) {
            $invalidProperties[] = "invalid value for 'searchIndex', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['searchIndex']) && !preg_match("/.*\\S.*/", $this->container['searchIndex'])) {
            $invalidProperties[] = "invalid value for 'searchIndex', must be conform to the pattern /.*\\S.*/.";
        }

        if (!is_null($this->container['title']) && (mb_strlen($this->container['title']) > 1000)) {
            $invalidProperties[] = "invalid value for 'title', the character length must be smaller than or equal to 1000.";
        }

        if (!is_null($this->container['title']) && !preg_match("/.*\\S.*/", $this->container['title'])) {
            $invalidProperties[] = "invalid value for 'title', must be conform to the pattern /.*\\S.*/.";
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
     * Gets actor
     *
     * @return string|null
     */
    public function getActor()
    {
        return $this->container['actor'];
    }

    /**
     * Sets actor
     *
     * @param string|null $actor Actor name associated with the item. You can enter all or part of the name.
     *
     * @return self
     */
    public function setActor($actor)
    {
        if (is_null($actor)) {
            throw new \InvalidArgumentException('non-nullable actor cannot be null');
        }
        if ((mb_strlen($actor) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $actor when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($actor)))) {
            throw new \InvalidArgumentException("invalid value for \$actor when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['actor'] = $actor;

        return $this;
    }

    /**
     * Gets artist
     *
     * @return string|null
     */
    public function getArtist()
    {
        return $this->container['artist'];
    }

    /**
     * Sets artist
     *
     * @param string|null $artist Artist name associated with the item. You can enter all or part of the name.
     *
     * @return self
     */
    public function setArtist($artist)
    {
        if (is_null($artist)) {
            throw new \InvalidArgumentException('non-nullable artist cannot be null');
        }
        if ((mb_strlen($artist) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $artist when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($artist)))) {
            throw new \InvalidArgumentException("invalid value for \$artist when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['artist'] = $artist;

        return $this;
    }

    /**
     * Gets author
     *
     * @return string|null
     */
    public function getAuthor()
    {
        return $this->container['author'];
    }

    /**
     * Sets author
     *
     * @param string|null $author Author name associated with the item. You can enter all or part of the name.
     *
     * @return self
     */
    public function setAuthor($author)
    {
        if (is_null($author)) {
            throw new \InvalidArgumentException('non-nullable author cannot be null');
        }
        if ((mb_strlen($author) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $author when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($author)))) {
            throw new \InvalidArgumentException("invalid value for \$author when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['author'] = $author;

        return $this;
    }

    /**
     * Gets availability
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Availability|null
     */
    public function getAvailability()
    {
        return $this->container['availability'];
    }

    /**
     * Sets availability
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\Availability|null $availability availability
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
     * Gets brand
     *
     * @return string|null
     */
    public function getBrand()
    {
        return $this->container['brand'];
    }

    /**
     * Sets brand
     *
     * @param string|null $brand Brand name associated with the item. You can enter all or part of the name.
     *
     * @return self
     */
    public function setBrand($brand)
    {
        if (is_null($brand)) {
            throw new \InvalidArgumentException('non-nullable brand cannot be null');
        }
        if ((mb_strlen($brand) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $brand when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($brand)))) {
            throw new \InvalidArgumentException("invalid value for \$brand when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['brand'] = $brand;

        return $this;
    }

    /**
     * Gets browseNodeId
     *
     * @return string|null
     */
    public function getBrowseNodeId()
    {
        return $this->container['browseNodeId'];
    }

    /**
     * Sets browseNodeId
     *
     * @param string|null $browseNodeId A unique ID assigned by Amazon that identifies a product category/sub-category. The BrowseNodeId is a positive Long having max value upto Long.MAX_VALUE i.e. 9223372036854775807 (inclusive).
     *
     * @return self
     */
    public function setBrowseNodeId($browseNodeId)
    {
        if (is_null($browseNodeId)) {
            throw new \InvalidArgumentException('non-nullable browseNodeId cannot be null');
        }
        if ((mb_strlen($browseNodeId) > 19)) {
            throw new \InvalidArgumentException('invalid length for $browseNodeId when calling SearchItemsRequestContent., must be smaller than or equal to 19.');
        }
        if ((!preg_match("/^[1-9][0-9]*$/", ObjectSerializer::toString($browseNodeId)))) {
            throw new \InvalidArgumentException("invalid value for \$browseNodeId when calling SearchItemsRequestContent., must conform to the pattern /^[1-9][0-9]*$/.");
        }

        $this->container['browseNodeId'] = $browseNodeId;

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
            throw new \InvalidArgumentException('invalid length for $currencyOfPreference when calling SearchItemsRequestContent., must be smaller than or equal to 100.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($currencyOfPreference)))) {
            throw new \InvalidArgumentException("invalid value for \$currencyOfPreference when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['currencyOfPreference'] = $currencyOfPreference;

        return $this;
    }

    /**
     * Gets deliveryFlags
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\DeliveryFlag[]|null
     */
    public function getDeliveryFlags()
    {
        return $this->container['deliveryFlags'];
    }

    /**
     * Sets deliveryFlags
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\DeliveryFlag[]|null $deliveryFlags List of DeliveryFlag which denotes a certain delivery program.
     *
     * @return self
     */
    public function setDeliveryFlags($deliveryFlags)
    {
        if (is_null($deliveryFlags)) {
            throw new \InvalidArgumentException('non-nullable deliveryFlags cannot be null');
        }

        if ((count($deliveryFlags) > 100)) {
            throw new \InvalidArgumentException('invalid value for $deliveryFlags when calling SearchItemsRequestContent., number of items must be less than or equal to 100.');
        }
        $this->container['deliveryFlags'] = $deliveryFlags;

        return $this;
    }

    /**
     * Gets itemCount
     *
     * @return float|null
     */
    public function getItemCount()
    {
        return $this->container['itemCount'];
    }

    /**
     * Sets itemCount
     *
     * @param float|null $itemCount The number of items desired in SearchItems response.
     *
     * @return self
     */
    public function setItemCount($itemCount)
    {
        if (is_null($itemCount)) {
            throw new \InvalidArgumentException('non-nullable itemCount cannot be null');
        }

        if (($itemCount > 100)) {
            throw new \InvalidArgumentException('invalid value for $itemCount when calling SearchItemsRequestContent., must be smaller than or equal to 100.');
        }
        if (($itemCount < 1)) {
            throw new \InvalidArgumentException('invalid value for $itemCount when calling SearchItemsRequestContent., must be bigger than or equal to 1.');
        }

        $this->container['itemCount'] = $itemCount;

        return $this;
    }

    /**
     * Gets itemPage
     *
     * @return float|null
     */
    public function getItemPage()
    {
        return $this->container['itemPage'];
    }

    /**
     * Sets itemPage
     *
     * @param float|null $itemPage The specific page of items to be returned from the available Search Results.
     *
     * @return self
     */
    public function setItemPage($itemPage)
    {
        if (is_null($itemPage)) {
            throw new \InvalidArgumentException('non-nullable itemPage cannot be null');
        }

        if (($itemPage > 10)) {
            throw new \InvalidArgumentException('invalid value for $itemPage when calling SearchItemsRequestContent., must be smaller than or equal to 10.');
        }
        if (($itemPage < 1)) {
            throw new \InvalidArgumentException('invalid value for $itemPage when calling SearchItemsRequestContent., must be bigger than or equal to 1.');
        }

        $this->container['itemPage'] = $itemPage;

        return $this;
    }

    /**
     * Gets keywords
     *
     * @return string|null
     */
    public function getKeywords()
    {
        return $this->container['keywords'];
    }

    /**
     * Sets keywords
     *
     * @param string|null $keywords A word or phrase that describes an item i.e. the search query.
     *
     * @return self
     */
    public function setKeywords($keywords)
    {
        if (is_null($keywords)) {
            throw new \InvalidArgumentException('non-nullable keywords cannot be null');
        }
        if ((mb_strlen($keywords) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $keywords when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($keywords)))) {
            throw new \InvalidArgumentException("invalid value for \$keywords when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['keywords'] = $keywords;

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
            throw new \InvalidArgumentException('invalid value for $languagesOfPreference when calling SearchItemsRequestContent., number of items must be less than or equal to 1.');
        }
        $this->container['languagesOfPreference'] = $languagesOfPreference;

        return $this;
    }

    /**
     * Gets maxPrice
     *
     * @return float|null
     */
    public function getMaxPrice()
    {
        return $this->container['maxPrice'];
    }

    /**
     * Sets maxPrice
     *
     * @param float|null $maxPrice The MaxPrice parameter filters search results to items with at least one offer price below the specified value.
     *
     * @return self
     */
    public function setMaxPrice($maxPrice)
    {
        if (is_null($maxPrice)) {
            throw new \InvalidArgumentException('non-nullable maxPrice cannot be null');
        }

        if (($maxPrice < 1)) {
            throw new \InvalidArgumentException('invalid value for $maxPrice when calling SearchItemsRequestContent., must be bigger than or equal to 1.');
        }

        $this->container['maxPrice'] = $maxPrice;

        return $this;
    }

    /**
     * Gets minPrice
     *
     * @return float|null
     */
    public function getMinPrice()
    {
        return $this->container['minPrice'];
    }

    /**
     * Sets minPrice
     *
     * @param float|null $minPrice The MinPrice parameter filters search results to items with at least one offer price above the specified value.
     *
     * @return self
     */
    public function setMinPrice($minPrice)
    {
        if (is_null($minPrice)) {
            throw new \InvalidArgumentException('non-nullable minPrice cannot be null');
        }

        if (($minPrice < 1)) {
            throw new \InvalidArgumentException('invalid value for $minPrice when calling SearchItemsRequestContent., must be bigger than or equal to 1.');
        }

        $this->container['minPrice'] = $minPrice;

        return $this;
    }

    /**
     * Gets minReviewsRating
     *
     * @return float|null
     */
    public function getMinReviewsRating()
    {
        return $this->container['minReviewsRating'];
    }

    /**
     * Sets minReviewsRating
     *
     * @param float|null $minReviewsRating The MinReviewsRating parameter filters search results to items with customer review ratings above specified value.
     *
     * @return self
     */
    public function setMinReviewsRating($minReviewsRating)
    {
        if (is_null($minReviewsRating)) {
            throw new \InvalidArgumentException('non-nullable minReviewsRating cannot be null');
        }

        if (($minReviewsRating > 4)) {
            throw new \InvalidArgumentException('invalid value for $minReviewsRating when calling SearchItemsRequestContent., must be smaller than or equal to 4.');
        }
        if (($minReviewsRating < 1)) {
            throw new \InvalidArgumentException('invalid value for $minReviewsRating when calling SearchItemsRequestContent., must be bigger than or equal to 1.');
        }

        $this->container['minReviewsRating'] = $minReviewsRating;

        return $this;
    }

    /**
     * Gets minSavingPercent
     *
     * @return float|null
     */
    public function getMinSavingPercent()
    {
        return $this->container['minSavingPercent'];
    }

    /**
     * Sets minSavingPercent
     *
     * @param float|null $minSavingPercent The MinSavingPercent parameter filters search results to items with at least one offer having saving percentage above the specified value.
     *
     * @return self
     */
    public function setMinSavingPercent($minSavingPercent)
    {
        if (is_null($minSavingPercent)) {
            throw new \InvalidArgumentException('non-nullable minSavingPercent cannot be null');
        }

        if (($minSavingPercent > 99)) {
            throw new \InvalidArgumentException('invalid value for $minSavingPercent when calling SearchItemsRequestContent., must be smaller than or equal to 99.');
        }
        if (($minSavingPercent < 1)) {
            throw new \InvalidArgumentException('invalid value for $minSavingPercent when calling SearchItemsRequestContent., must be bigger than or equal to 1.');
        }

        $this->container['minSavingPercent'] = $minSavingPercent;

        return $this;
    }

    /**
     * Gets partnerTag
     *
     * @return string|null
     */
    public function getPartnerTag()
    {
        return $this->container['partnerTag'];
    }

    /**
     * Sets partnerTag
     *
     * @param string|null $partnerTag An alphanumeric token that uniquely identifies a partner. If the value of PartnerType is Associates, enter your Store Id or tracking ID.
     *
     * @return self
     */
    public function setPartnerTag($partnerTag)
    {
        if (is_null($partnerTag)) {
            throw new \InvalidArgumentException('non-nullable partnerTag cannot be null');
        }
        if ((mb_strlen($partnerTag) > 64)) {
            throw new \InvalidArgumentException('invalid length for $partnerTag when calling SearchItemsRequestContent., must be smaller than or equal to 64.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($partnerTag)))) {
            throw new \InvalidArgumentException("invalid value for \$partnerTag when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['partnerTag'] = $partnerTag;

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
            throw new \InvalidArgumentException('invalid value for $properties when calling SearchItemsRequestContent., number of items must be less than or equal to 2.');
        }
        $this->container['properties'] = $properties;

        return $this;
    }

    /**
     * Gets resources
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResource[]|null
     */
    public function getResources()
    {
        return $this->container['resources'];
    }

    /**
     * Sets resources
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SearchItemsResource[]|null $resources List of resources for SearchItems operation which specify the values to return.
     *
     * @return self
     */
    public function setResources($resources)
    {
        if (is_null($resources)) {
            throw new \InvalidArgumentException('non-nullable resources cannot be null');
        }

        if ((count($resources) > 100)) {
            throw new \InvalidArgumentException('invalid value for $resources when calling SearchItemsRequestContent., number of items must be less than or equal to 100.');
        }
        $this->container['resources'] = $resources;

        return $this;
    }

    /**
     * Gets searchIndex
     *
     * @return string|null
     */
    public function getSearchIndex()
    {
        return $this->container['searchIndex'];
    }

    /**
     * Sets searchIndex
     *
     * @param string|null $searchIndex Indicates the product category to search. SearchIndex values differ by marketplace.
     *
     * @return self
     */
    public function setSearchIndex($searchIndex)
    {
        if (is_null($searchIndex)) {
            throw new \InvalidArgumentException('non-nullable searchIndex cannot be null');
        }
        if ((mb_strlen($searchIndex) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $searchIndex when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($searchIndex)))) {
            throw new \InvalidArgumentException("invalid value for \$searchIndex when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['searchIndex'] = $searchIndex;

        return $this;
    }

    /**
     * Gets sortBy
     *
     * @return \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SortBy|null
     */
    public function getSortBy()
    {
        return $this->container['sortBy'];
    }

    /**
     * Sets sortBy
     *
     * @param \Amazon\CreatorsAPI\v1\com\amazon\creators\model\SortBy|null $sortBy sortBy
     *
     * @return self
     */
    public function setSortBy($sortBy)
    {
        if (is_null($sortBy)) {
            throw new \InvalidArgumentException('non-nullable sortBy cannot be null');
        }
        $this->container['sortBy'] = $sortBy;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->container['title'];
    }

    /**
     * Sets title
     *
     * @param string|null $title Title associated with the item.
     *
     * @return self
     */
    public function setTitle($title)
    {
        if (is_null($title)) {
            throw new \InvalidArgumentException('non-nullable title cannot be null');
        }
        if ((mb_strlen($title) > 1000)) {
            throw new \InvalidArgumentException('invalid length for $title when calling SearchItemsRequestContent., must be smaller than or equal to 1000.');
        }
        if ((!preg_match("/.*\\S.*/", ObjectSerializer::toString($title)))) {
            throw new \InvalidArgumentException("invalid value for \$title when calling SearchItemsRequestContent., must conform to the pattern /.*\\S.*/.");
        }

        $this->container['title'] = $title;

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


