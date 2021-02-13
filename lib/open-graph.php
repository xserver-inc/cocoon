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
          //'sslverify' => false,
          //'redirection' => 10,
          'cocoon' => true,
          'user-agent' => $_SERVER['HTTP_USER_AGENT'],
        );
        $res = wp_remote_get( $URI, $args );
        $response_code = wp_remote_retrieve_response_code( $res );
        //echo('<pre>');
        // _v($res);
        // _v($response_code);
        //echo('</pre>');
        if (!is_wp_error( $res ) && $response_code === 200) {
          $response = $res['body'];
        } else if (!is_admin()) {
          $response = wp_filesystem_get_contents($URI, true);

          if (!$response) {
            $response = get_http_content($URI);
          }
        }
        //var_dump($response);
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
    // if (get_option('WPLANG') == 'ja') {
    //   mb_language("Japanese");
    // }
    $HTML = @mb_convert_encoding($HTML,'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
    //$HTML = mb_convert_encoding($HTML,'HTML-ENTITIES', 'ASCII, JIS, UTF-8');
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

    //Amazonページかどうか
    if (is_amazon_site_page($URI)
      //(includes_string($URI, '//amzn.to/') || includes_string($URI, '//www.amazon.co'))
    //  || (includes_string($HTML, '//images-fe.ssl-images-amazon.com')
    //  && includes_string($HTML, '//m.media-amazon.com'))
    ) {
      $image_url = null;
      //Amazonページなら画像取得
      if (includes_string($HTML, 'id="landingImage"')) {
        //通常商品ページ用
        if (preg_match('|https://images-na.ssl-images-amazon.com/images/I/\d[^&"]+?_S[A-Z]\d{3}(,\d{3})?_\.jpg|i', $HTML, $m)) {
          if (isset($m[0])) {
            //_v($m[0]);
            $image_url = $m[0];
          }
        } else {
          //https://images-na.ssl-images-amazon.com/images/I/41b9TQppZJL._AC_.jp
          //https://images-na.ssl-images-amazon.com/images/I/81OcexSf0SL._AC_UX625_.jpg
          if (preg_match('/"(https:\/\/images-na\.ssl-images-amazon\.com\/images\/I\/[^&"]+?\._AC_(U[XY][^&"]+?)?\.jpg)"/i', $HTML, $m)) {
            //var_dump($m[1]);
            if (isset($m[1])) {
              //_v($m[0]);
              $image_url = $m[1];
            }
          }
        }
      } else if (includes_string($HTML, 'id="imgBlkFront"')) {
        //書籍ページ用
        //https://images-fe.ssl-images-amazon.com/images/I/51aV7NaxG4L.jpg
        $res = preg_match('/id="imgBlkFront" data-a-dynamic-image="\{&quot;(https:\/\/images-(fe|na).ssl-images-amazon.com\/images\/I\/.+?\.jpg)&quot;:/i', $HTML, $m);
        if ($res && isset($m[1])) {
          $image_url = $m[1];
        }
      } else if (includes_string($HTML, 'id="MusicCartToastContainer"')) {
        //Amazon Music
        //https://m.media-amazon.com/images/I/61+mhXhVhfL._SS500_.jpg
        //https://images-na.ssl-images-amazon.com/images/I/41AFHM036KL._AC_.jpg
        $res = preg_match('/<img.+?src="(https:\/\/m.media-amazon.com\/images\/I\/.+?)">/i', $HTML, $m);
        if ($res && isset($m[1])) {
          $image_url = $m[1];
        }
      } else if (includes_string($HTML, 'id="ebooksImgBlkFront"')) {
        //Amazon Kindle
        //https://m.media-amazon.com/images/I/51tY7U5mUHL.jpg
        $res = preg_match('/"(https:\/\/m.media-amazon\.com\/images\/I\/[^&"]+?\.jpg)"/i', $HTML, $m);
        if ($res && isset($m[1])) {
          $image_url = $m[1];
        }
      }
      $page->_values['image'] = $image_url;
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
	public function rewind() { reset($this->_values); $this->_position = 0; }
	public function current() { return current($this->_values); }
	public function key() { return key($this->_values); }
	public function next() { next($this->_values); ++$this->_position; }
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
