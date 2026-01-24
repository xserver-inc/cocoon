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
 * SearchItemsResource Class Doc Comment
 */
class SearchItemsResource
{
    /**
     * Possible values of this enum
     */
    public const BROWSE_NODE_INFO_BROWSE_NODES = 'browseNodeInfo.browseNodes';

    public const BROWSE_NODE_INFO_BROWSE_NODES_ANCESTOR = 'browseNodeInfo.browseNodes.ancestor';

    public const BROWSE_NODE_INFO_BROWSE_NODES_SALES_RANK = 'browseNodeInfo.browseNodes.salesRank';

    public const BROWSE_NODE_INFO_WEBSITE_SALES_RANK = 'browseNodeInfo.websiteSalesRank';

    public const CUSTOMER_REVIEWS_COUNT = 'customerReviews.count';

    public const CUSTOMER_REVIEWS_STAR_RATING = 'customerReviews.starRating';

    public const IMAGES_PRIMARY_SMALL = 'images.primary.small';

    public const IMAGES_PRIMARY_MEDIUM = 'images.primary.medium';

    public const IMAGES_PRIMARY_LARGE = 'images.primary.large';

    public const IMAGES_PRIMARY_HIGH_RES = 'images.primary.highRes';

    public const IMAGES_VARIANTS_SMALL = 'images.variants.small';

    public const IMAGES_VARIANTS_MEDIUM = 'images.variants.medium';

    public const IMAGES_VARIANTS_LARGE = 'images.variants.large';

    public const IMAGES_VARIANTS_HIGH_RES = 'images.variants.highRes';

    public const ITEM_INFO_BY_LINE_INFO = 'itemInfo.byLineInfo';

    public const ITEM_INFO_CONTENT_INFO = 'itemInfo.contentInfo';

    public const ITEM_INFO_CONTENT_RATING = 'itemInfo.contentRating';

    public const ITEM_INFO_CLASSIFICATIONS = 'itemInfo.classifications';

    public const ITEM_INFO_EXTERNAL_IDS = 'itemInfo.externalIds';

    public const ITEM_INFO_FEATURES = 'itemInfo.features';

    public const ITEM_INFO_MANUFACTURE_INFO = 'itemInfo.manufactureInfo';

    public const ITEM_INFO_PRODUCT_INFO = 'itemInfo.productInfo';

    public const ITEM_INFO_TECHNICAL_INFO = 'itemInfo.technicalInfo';

    public const ITEM_INFO_TITLE = 'itemInfo.title';

    public const ITEM_INFO_TRADE_IN_INFO = 'itemInfo.tradeInInfo';

    public const OFFERS_V2_LISTINGS_AVAILABILITY = 'offersV2.listings.availability';

    public const OFFERS_V2_LISTINGS_CONDITION = 'offersV2.listings.condition';

    public const OFFERS_V2_LISTINGS_DEAL_DETAILS = 'offersV2.listings.dealDetails';

    public const OFFERS_V2_LISTINGS_IS_BUY_BOX_WINNER = 'offersV2.listings.isBuyBoxWinner';

    public const OFFERS_V2_LISTINGS_LOYALTY_POINTS = 'offersV2.listings.loyaltyPoints';

    public const OFFERS_V2_LISTINGS_MERCHANT_INFO = 'offersV2.listings.merchantInfo';

    public const OFFERS_V2_LISTINGS_PRICE = 'offersV2.listings.price';

    public const OFFERS_V2_LISTINGS_TYPE = 'offersV2.listings.type';

    public const PARENT_ASIN = 'parentASIN';

    public const SEARCH_REFINEMENTS = 'searchRefinements';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::BROWSE_NODE_INFO_BROWSE_NODES,
            self::BROWSE_NODE_INFO_BROWSE_NODES_ANCESTOR,
            self::BROWSE_NODE_INFO_BROWSE_NODES_SALES_RANK,
            self::BROWSE_NODE_INFO_WEBSITE_SALES_RANK,
            self::CUSTOMER_REVIEWS_COUNT,
            self::CUSTOMER_REVIEWS_STAR_RATING,
            self::IMAGES_PRIMARY_SMALL,
            self::IMAGES_PRIMARY_MEDIUM,
            self::IMAGES_PRIMARY_LARGE,
            self::IMAGES_PRIMARY_HIGH_RES,
            self::IMAGES_VARIANTS_SMALL,
            self::IMAGES_VARIANTS_MEDIUM,
            self::IMAGES_VARIANTS_LARGE,
            self::IMAGES_VARIANTS_HIGH_RES,
            self::ITEM_INFO_BY_LINE_INFO,
            self::ITEM_INFO_CONTENT_INFO,
            self::ITEM_INFO_CONTENT_RATING,
            self::ITEM_INFO_CLASSIFICATIONS,
            self::ITEM_INFO_EXTERNAL_IDS,
            self::ITEM_INFO_FEATURES,
            self::ITEM_INFO_MANUFACTURE_INFO,
            self::ITEM_INFO_PRODUCT_INFO,
            self::ITEM_INFO_TECHNICAL_INFO,
            self::ITEM_INFO_TITLE,
            self::ITEM_INFO_TRADE_IN_INFO,
            self::OFFERS_V2_LISTINGS_AVAILABILITY,
            self::OFFERS_V2_LISTINGS_CONDITION,
            self::OFFERS_V2_LISTINGS_DEAL_DETAILS,
            self::OFFERS_V2_LISTINGS_IS_BUY_BOX_WINNER,
            self::OFFERS_V2_LISTINGS_LOYALTY_POINTS,
            self::OFFERS_V2_LISTINGS_MERCHANT_INFO,
            self::OFFERS_V2_LISTINGS_PRICE,
            self::OFFERS_V2_LISTINGS_TYPE,
            self::PARENT_ASIN,
            self::SEARCH_REFINEMENTS
        ];
    }
}


