<?php
/*
  Copyright 2010 Scott MacVicar

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

	Original can be found at https://github.com/scottmac/opengraph/blob/master/OpenGraph.php

*/

/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

class OpenGraphGetter implements Iterator
{
  /**
   * There are base schema's based on type, this is just
   * a map so that the schema can be obtained
   *
   */
	public static $TYPES = array(
		'activity' => array('activity', 'sport'),
		'business' => array('bar', 'company', 'cafe', 'hotel', 'restaurant'),
		'group' => array('cause', 'sports_league', 'sports_team'),
		'organization' => array('band', 'government', 'non_profit', 'school', 'university'),
		'person' => array('actor', 'athlete', 'author', 'director', 'musician', 'politician', 'public_figure'),
		'place' => array('city', 'country', 'landmark', 'state_province'),
		'product' => array('album', 'book', 'drink', 'food', 'game', 'movie', 'product', 'song', 'tv_show'),
		'website' => array('blog', 'website'),
	);

  /**
   * Holds all the Open Graph values we've parsed from a page
   *
   */
	private $_values = array();

  /**
   * Fetches a URI and parses it for Open Graph data, returns
   * false on error.
   *
   * @param $URI    URI to page to parse for Open Graph data
   * @return OpenGraphGetter
   */
	static public function fetch($URI) {
        global $wp_version;

        $args = array(
          'headers' => array(
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ja,ja-JP;q=0.9,und;q=0.8,en;q=0.7,zh-CN;q=0.6,zh;q=0.5',
            'Cache-Control' => 'no-cache',
          ),
          'cocoon' => true,
          'user-agent' => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '',//×
          // 楽天OGP情報取得テスト
          // 'user-agent' => 'WordPress/'.get_bloginfo('version').'; '.get_the_site_domain(),//○
          // 'user-agent' => 'Mozilla/5.0',//○
          // 'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0',//○
          // 'user-agent' => 'WordPress/'.get_bloginfo('version').'; '.home_url(),//×
        );
        // _v($args);
        if (is_amazon_site_page($URI)) {
          $args['user-agent'] = 'Twitterbot/1.0';
        }
        if (is_rakuten_site_page($URI)) {
          //通常のユーザーエージェントだと楽天でOGP情報が取得できないため
          $args['user-agent'] = 'WordPress/'.get_bloginfo('version').'; '.get_the_site_domain();
        } else {
          unset($args['headers']);
        }

        $res = wp_remote_get( $URI, $args );
        $response_code = wp_remote_retrieve_response_code( $res );

        if (!is_wp_error( $res ) && $response_code === 200) {
          $response = $res['body'];
        } else if (!is_admin()) {
          $response = wp_filesystem_get_contents($URI, true);

          if (!$response) {
            $response = get_http_content($URI);
          }
        }
        if (!empty($response)) {
            return self::_parse($response, $URI);
        } else {
            return false;
        }
	}

  /**
   * Parses HTML and extracts Open Graph data, this assumes
   * the document is at least well formed.
   *
   * @param $HTML    HTML to parse
   * @return OpenGraphGetter
   */
	static private function _parse($HTML, $URI = null) {
		$old_libxml_error = libxml_use_internal_errors(true);

		$doc = new DOMDocument();
    // //UTF-8ページの文字化け問題
    // //対処法1：http://qiita.com/kobake@github/items/3c5d09f9584a8786339d
    // //対処法2：http://nplll.com/archives/2011/06/_domdocumentloadhtml.php
    // $HTML = @mb_convert_encoding($HTML, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');

    // 上記の方法で新しいPHPの仕様で非推奨になったため、以下の方法を選択
    // //対処法3：https://wp-cocoon.com/community/postid/77632/
    $HTML = mb_convert_encoding($HTML, 'UTF-8', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    $HTML = mb_encode_numericentity($HTML, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
    if (!$HTML) {
      return false;
    }
		$doc->loadHTML($HTML);

    //タイトルタグからタイトル情報を取得
    preg_match( "/<title>(.*?)<\/title>/i", $HTML, $matches);
    $title = $matches ? $matches[1] : null;
    //メタディスクリプションタグからタイトル情報を取得
    preg_match( '{<meta name="description" content="(.*?)".*?>}i', $HTML, $matches);
    $description = $matches ? $matches[1] : null;
    if (!$description) {
      preg_match( "{<meta name='description' content='(.*?)'.*?>}i", $HTML, $matches);
      $description = $matches ? $matches[1] : null;
    }

		libxml_use_internal_errors($old_libxml_error);

		$tags = $doc->getElementsByTagName('meta');

		if ((!$tags || $tags->length === 0) && !$title) {
			return false;
		}

		$page = new self();
    $page->_values['title'] = $title;

		$nonOgDescription = null;

		foreach ($tags AS $tag) {
			if ($tag->hasAttribute('property') &&
			    strpos($tag->getAttribute('property'), 'og:') === 0) {
				$key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
				$page->_values[$key] = $tag->getAttribute('content');
			}

			//Added this if loop to retrieve description values from sites like the New York Times who have malformed it.
			if ($tag ->hasAttribute('value') && $tag->hasAttribute('property') &&
			    strpos($tag->getAttribute('property'), 'og:') === 0) {
				$key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
				$page->_values[$key] = $tag->getAttribute('value');
			}
			//Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
			if ($tag->hasAttribute('name') && $tag->getAttribute('name') === 'description') {
                $nonOgDescription = $tag->getAttribute('content');
            }

		}

		//Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
		if (!isset($page->_values['title'])) {
            $titles = $doc->getElementsByTagName('title');
            if ($titles->length > 0) {
                $page->_values['title'] = $titles->item(0)->textContent;
            }

        }
        if (!isset($page->_values['description']) && $nonOgDescription) {
            $page->_values['description'] = $nonOgDescription;
        }

        //Fallback to use image_src if ogp::image isn't set.
        if (!isset($page->values['image'])) {
            $domxpath = new DOMXPath($doc);
            $elements = $domxpath->query("//link[@rel='image_src']");

            if ($elements->length > 0) {
                $domattr = $elements->item(0)->attributes->getNamedItem('href');
                if ($domattr) {
                    if (is_amazon_site_page($URI)) {
                      $domattr->value = preg_replace('/[^\.]+?\.jpg$/', '.jpg', $domattr->value);
                    }
                    $page->_values['image'] = $domattr->value;
                    $page->_values['image_src'] = $domattr->value;
                }
            }
        }

		if (empty($page->_values)) { return false; }

    //og:titleが空文字だったとき
    $page_title = null;
    if (isset($page->_values['title'])) {
      $page_title = trim($page->_values['title']);
    }
    if (empty($page_title)) {
      $page->_values['title'] = $title;
    }
    //og:descriptionが空文字だったとき
    $page_description = null;
    if (isset($page->_values['description'])) {
      $page_description = trim($page->_values['description']);
    }
    if (empty($page_description)) {
      $page->_values['description'] = $description;
    }

		return $page;
	}

  /**
   * Helper method to access attributes directly
   * Example:
   * $graph->title
   *
   * @param $key    Key to fetch from the lookup
   */
	public function __get($key) {
		if (array_key_exists($key, $this->_values)) {
			return $this->_values[$key];
		}

		if ($key === 'schema') {
			foreach (self::$TYPES AS $schema => $types) {
				if (array_search($this->_values['type'], $types)) {
					return $schema;
				}
			}
		}
	}

  /**
   * Return all the keys found on the page
   *
   * @return array
   */
	public function keys() {
		return array_keys($this->_values);
	}

  /**
   * Helper method to check an attribute exists
   *
   * @param $key
   */
	public function __isset($key) {
		return array_key_exists($key, $this->_values);
	}

  /**
   * Will return true if the page has location data embedded
   *
   * @return boolean Check if the page has location data
   */
	public function hasLocation() {
		if (array_key_exists('latitude', $this->_values) && array_key_exists('longitude', $this->_values)) {
			return true;
		}

		$address_keys = array('street_address', 'locality', 'region', 'postal_code', 'country_name');
		$valid_address = true;
		foreach ($address_keys AS $key) {
			$valid_address = ($valid_address && array_key_exists($key, $this->_values));
		}
		return $valid_address;
	}

  /**
   * Iterator code
   */
	private $_position = 0;
  #[\ReturnTypeWillChange]
	public function rewind() { reset($this->_values); $this->_position = 0; }
  #[\ReturnTypeWillChange]
	public function current() { return current($this->_values); }
  #[\ReturnTypeWillChange]
	public function key() { return key($this->_values); }
  #[\ReturnTypeWillChange]
	public function next() { next($this->_values); ++$this->_position; }
  #[\ReturnTypeWillChange]
	public function valid() { return $this->_position < sizeof($this->_values); }
}

//curlのバージョンチェック
if ( !function_exists( 'is_nss_curl' ) ):
function is_nss_curl() {
  if (function_exists('curl_version')) {
    $info = curl_version();
    return $info['version'] === '7.19.7';
  }
}
endif;

//curl_setoptで暗号化スイートを設定
//http://blog.tojiru.net/article/437364535.html
//https://github.com/hirak/prestissimo/pull/69/files
if ( !function_exists( 'set_ecc_cipher_suites' ) ):
function set_ecc_cipher_suites($handle, $r) {
  if (isset($r['cocoon'])) {
    $cipher_list = array(
      "rsa_3des_sha",
      "rsa_des_sha",
      "rsa_null_md5",
      "rsa_null_sha",
      "rsa_rc2_40_md5",
      "rsa_rc4_128_md5",
      "rsa_rc4_128_sha",
      "rsa_rc4_40_md5",
      "fips_des_sha",
      "fips_3des_sha",
      "rsa_des_56_sha",
      "rsa_rc4_56_sha",
      "rsa_aes_128_sha",
      "rsa_aes_256_sha",
      "rsa_aes_128_gcm_sha_256",
      "dhe_rsa_aes_128_gcm_sha_256",
      "ecdh_ecdsa_null_sha",
      "ecdh_ecdsa_rc4_128_sha",
      "ecdh_ecdsa_3des_sha",
      "ecdh_ecdsa_aes_128_sha",
      "ecdh_ecdsa_aes_256_sha",
      "ecdhe_ecdsa_null_sha",
      "ecdhe_ecdsa_rc4_128_sha",
      "ecdhe_ecdsa_3des_sha",
      "ecdhe_ecdsa_aes_128_sha",
      "ecdhe_ecdsa_aes_256_sha",
      "ecdh_rsa_null_sha",
      "ecdh_rsa_128_sha",
      "ecdh_rsa_3des_sha",
      "ecdh_rsa_aes_128_sha",
      "ecdh_rsa_aes_256_sha",
      "echde_rsa_null",
      "ecdhe_rsa_rc4_128_sha",
      "ecdhe_rsa_3des_sha",
      "ecdhe_rsa_aes_128_sha",
      "ecdhe_rsa_aes_256_sha",
      "ecdhe_ecdsa_aes_128_gcm_sha_256",
      "ecdhe_rsa_aes_128_gcm_sha_256",
    );
    curl_setopt($handle, CURLOPT_SSL_CIPHER_LIST, implode(',', $cipher_list));
  }
}
endif;
if (is_nss_curl()) {
  add_action('http_api_curl', 'set_ecc_cipher_suites', 10, 2);
}
