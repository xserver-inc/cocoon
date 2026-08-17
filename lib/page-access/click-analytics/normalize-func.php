<?php //クリック解析URL正規化
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'cocoon_click_normalize_path' ) ):
function cocoon_click_normalize_path($path){
  $path = preg_replace('#/+#', '/', (string) $path);
  $segments = explode('/', $path);
  $output = array();
  foreach ($segments as $segment) {
    if ($segment === '' || $segment === '.') continue;
    if ($segment === '..') {
      array_pop($output);
    } else {
      $output[] = $segment;
    }
  }
  return '/' . implode('/', $output) . (substr($path, -1) === '/' && $output ? '/' : '');
}
endif;

if ( !function_exists( 'cocoon_click_make_absolute_url' ) ):
function cocoon_click_make_absolute_url($url, $source_url){
  if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) return $url;
  $base = wp_parse_url($source_url);
  if (empty($base['host'])) return '';
  $scheme = isset($base['scheme']) ? $base['scheme'] : 'https';
  $origin = $scheme . '://' . $base['host'] . (isset($base['port']) ? ':' . (int) $base['port'] : '');
  if (strpos($url, '//') === 0) return $scheme . ':' . $url;
  if (strpos($url, '/') === 0) return $origin . $url;
  if (strpos($url, '?') === 0) return $origin . (isset($base['path']) ? $base['path'] : '/') . $url;
  $base_path = isset($base['path']) ? $base['path'] : '/';
  $directory = preg_replace('#/[^/]*$#', '/', $base_path);
  return $origin . $directory . $url;
}
endif;

if ( !function_exists( 'cocoon_click_site_hosts' ) ):
function cocoon_click_site_hosts(){
  $hosts = array();
  foreach (array(home_url('/'), site_url('/')) as $url) {
    $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));
    if (!$host) continue;
    $hosts[] = rtrim($host, '.');
    $hosts[] = strpos($host, 'www.') === 0 ? substr($host, 4) : 'www.' . $host;
  }
  return array_values(array_unique($hosts));
}
endif;

if ( !function_exists( 'cocoon_click_is_internal_host' ) ):
function cocoon_click_is_internal_host($host, $site_hosts = null){
  $host = strtolower(rtrim((string) $host, '.'));
  $site_hosts = is_array($site_hosts) ? $site_hosts : cocoon_click_site_hosts();
  return in_array($host, array_map('strtolower', $site_hosts), true);
}
endif;

if ( !function_exists( 'cocoon_click_query_pairs' ) ):
function cocoon_click_query_pairs($query){
  $tracking = array('gclid', 'dclid', 'fbclid', 'msclkid', 'yclid', 'gbraid', 'wbraid', 'ttclid', 'twclid', 'igshid', 'li_fat_id', '_ga', 'mc_cid', 'mc_eid');
  $sensitive = array('token', 'auth', 'key', 'password', 'pass', 'session', 'sid', 'nonce', 'signature', 'secret', 'credential', 'jwt', 'code', 'email', 'mail');
  $pairs = array();
  foreach (array_slice(explode('&', (string) $query), 0, 20) as $pair) {
    if ($pair === '') continue;
    $parts = explode('=', $pair, 2);
    $key = strtolower(substr(rawurldecode($parts[0]), 0, 64));
    $sensitive_key = preg_match('/(^|[_-])(token|email|mail|nonce|password|pass|auth|session|signature|secret|credential|jwt|code|sid|key)([_-]|$)/', $key);
    if ($key === '' || strpos($key, 'utm_') === 0 || in_array($key, $tracking, true) || in_array($key, $sensitive, true) || $sensitive_key) continue;
    $pairs[$key] = isset($parts[1]) ? substr(rawurldecode($parts[1]), 0, 256) : '';
  }
  ksort($pairs);
  return $pairs;
}
endif;

if ( !function_exists( 'cocoon_click_is_excluded_destination' ) ):
function cocoon_click_is_excluded_destination($host, $url){
  $host = strtolower((string) $host);
  foreach (get_click_analytics_excluded_domains() as $excluded) {
    $excluded = ltrim(strtolower($excluded), '.');
    if ($host === $excluded || substr($host, -strlen('.' . $excluded)) === '.' . $excluded) return true;
  }
  foreach (get_click_analytics_excluded_urls() as $needle) {
    if ($needle !== '' && strpos($url, $needle) !== false) return true;
  }
  return false;
}
endif;

if ( !function_exists( 'cocoon_click_host_matches' ) ):
function cocoon_click_host_matches($host, $domains){
  $host = strtolower(rtrim((string) $host, '.'));
  foreach ($domains as $domain) {
    $domain = strtolower(ltrim((string) $domain, '.'));
    if ($host === $domain || ($domain !== '' && substr($host, -strlen('.' . $domain)) === '.' . $domain)) return true;
  }
  return false;
}
endif;

if ( !function_exists( 'cocoon_click_classify_external_destination' ) ):
function cocoon_click_classify_external_destination($type, $host, $attributes = array()){
  if ($type !== 'external') return $type;
  $hint = isset($attributes['classification_hint']) ? sanitize_key($attributes['classification_hint']) : '';
  if (in_array($hint, array('official', 'reference'), true)) return $hint;
  $social_hosts = array('x.com', 'twitter.com', 'facebook.com', 'instagram.com', 'youtube.com', 'youtu.be', 'tiktok.com', 'linkedin.com', 'pinterest.com', 'line.me');
  if (cocoon_click_host_matches($host, $social_hosts)) return 'social';
  $official_hosts = array('who.int', 'un.org', 'europa.eu');
  if (cocoon_click_host_matches($host, $official_hosts) || preg_match('/(?:^|\.)(?:go|lg)\.jp$/', $host) || preg_match('/(?:^|\.)gov(?:\.[a-z]{2})?$/', $host)) return 'official';
  $reference_hosts = array('doi.org', 'arxiv.org', 'wikipedia.org', 'ci.nii.ac.jp', 'jstage.jst.go.jp', 'pubmed.ncbi.nlm.nih.gov', 'scholar.google.com');
  if (cocoon_click_host_matches($host, $reference_hosts) || preg_match('/(?:^|\.)ac\.jp$/', $host) || preg_match('/(?:^|\.)edu(?:\.[a-z]{2})?$/', $host)) return 'reference';
  return $type;
}
endif;

if ( !function_exists( 'cocoon_click_normalize_destination' ) ):
function cocoon_click_normalize_destination($raw_url, $source_url, $attributes = array()){
  $raw_url = trim(html_entity_decode((string) $raw_url, ENT_QUOTES, 'UTF-8'));
  if ($raw_url === '' || strlen($raw_url) > 2048) return false;
  if ($raw_url[0] === '#') {
    $fragment = substr(preg_replace('/[^A-Za-z0-9_:\-.]/', '', $raw_url), 0, 191);
    return array('canonical' => $fragment, 'display' => $fragment, 'host' => '', 'type' => 'anchor', 'target_post_id' => 0);
  }
  $scheme = strtolower((string) wp_parse_url($raw_url, PHP_URL_SCHEME));
  if (in_array($scheme, array('javascript', 'data', 'blob', 'file'), true)) return false;
  if (in_array($scheme, array('mailto', 'tel', 'sms'), true)) {
    return array('canonical' => $scheme . ':' . cocoon_click_hmac($raw_url), 'display' => $scheme . ':', 'host' => '', 'type' => $scheme, 'target_post_id' => 0);
  }
  $absolute = cocoon_click_make_absolute_url($raw_url, $source_url);
  $parts = wp_parse_url($absolute);
  if (!$parts || empty($parts['host'])) return false;
  $source_parts = wp_parse_url($source_url);
  $destination_scheme = strtolower(isset($parts['scheme']) ? $parts['scheme'] : 'https');
  $source_scheme = strtolower(isset($source_parts['scheme']) ? $source_parts['scheme'] : 'https');
  $destination_port = isset($parts['port']) ? (int) $parts['port'] : ($destination_scheme === 'http' ? 80 : 443);
  $source_port = isset($source_parts['port']) ? (int) $source_parts['port'] : ($source_scheme === 'http' ? 80 : 443);
  $same_page_fragment = !empty($parts['fragment']) && !empty($source_parts['host'])
    && $destination_scheme === $source_scheme && $destination_port === $source_port
    && strtolower((string) $parts['host']) === strtolower((string) $source_parts['host'])
    && cocoon_click_normalize_path(isset($parts['path']) ? $parts['path'] : '/') === cocoon_click_normalize_path(isset($source_parts['path']) ? $source_parts['path'] : '/')
    && (string) (isset($parts['query']) ? $parts['query'] : '') === (string) (isset($source_parts['query']) ? $source_parts['query'] : '');
  if ($same_page_fragment) {
    $fragment = '#' . substr(preg_replace('/[^A-Za-z0-9_:\-.]/', '', $parts['fragment']), 0, 190);
    return array('canonical' => $fragment, 'display' => $fragment, 'host' => '', 'type' => 'anchor', 'target_post_id' => 0);
  }
  $scheme = strtolower(isset($parts['scheme']) ? $parts['scheme'] : 'https');
  if (!in_array($scheme, array('http', 'https'), true)) return false;
  $host = strtolower(rtrim($parts['host'], '.'));
  $port = isset($parts['port']) ? (int) $parts['port'] : 0;
  $port_text = ($port && !(($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443))) ? ':' . $port : '';
  $path = cocoon_click_normalize_path(isset($parts['path']) ? $parts['path'] : '/');
  $pairs = cocoon_click_query_pairs(isset($parts['query']) ? $parts['query'] : '');
  $canonical_query = '';
  if ($pairs) $canonical_query = '?' . http_build_query($pairs, '', '&', PHP_QUERY_RFC3986);
  $canonical = $scheme . '://' . $host . $port_text . $path . $canonical_query;
  $allowlist = get_click_analytics_query_allowlist();
  $display_pairs = array();
  foreach ($pairs as $key => $value) {
    $display_pairs[] = isset($allowlist[$host]) && in_array($key, $allowlist[$host], true)
      ? rawurlencode($key) . '=' . rawurlencode($value)
      : rawurlencode($key);
  }
  $display = $scheme . '://' . $host . $port_text . $path . ($display_pairs ? '?' . implode('&', $display_pairs) : '');
  if (cocoon_click_is_excluded_destination($host, $display)) return false;
  $type = cocoon_click_is_internal_host($host) ? 'internal' : 'external';
  $path_lower = strtolower($path);
  if (!empty($attributes['download']) || preg_match('/\.(zip|pdf|docx?|xlsx?|pptx?|csv|epub|mp3|mp4|mov|webm)$/', $path_lower)) $type = 'download';
  if ($type === 'external' && !empty($attributes['is_affiliate'])) $type = 'affiliate';
  $type = cocoon_click_classify_external_destination($type, $host, $attributes);
  $type = apply_filters('cocoon_click_analytics_classify_destination', $type, $canonical, $attributes);
  return array('canonical' => $canonical, 'display' => $display, 'host' => $host, 'type' => sanitize_key($type), 'target_post_id' => 0);
}
endif;

if ( !function_exists( 'cocoon_click_build_link_identity' ) ):
function cocoon_click_build_link_identity($source_post_id, $destination, $event){
  $area = isset($event['area']) ? sanitize_key($event['area']) : 'other';
  $allowed_areas = array('content', 'toc', 'blogcard', 'cta', 'related', 'header', 'navi', 'sidebar', 'footer', 'mobile_menu', 'other');
  if (!in_array($area, $allowed_areas, true)) $area = 'other';
  $heading_label = isset($event['heading']) ? sanitize_text_field($event['heading']) : '';
  $heading_label = function_exists('mb_substr') ? mb_substr($heading_label, 0, 191) : substr($heading_label, 0, 191);
  $heading_key = $heading_label === '' ? '' : cocoon_click_hmac('heading|' . $heading_label);
  $occurrence = isset($event['occurrence']) ? max(0, min(10000, (int) $event['occurrence'])) : 0;
  $element_type = isset($event['element_type']) ? sanitize_key($event['element_type']) : 'text';
  if (!in_array($element_type, array('text', 'button', 'image', 'blogcard'), true)) $element_type = 'text';
  $anchor = isset($event['label']) ? sanitize_text_field($event['label']) : '';
  $anchor = function_exists('mb_substr') ? mb_substr($anchor, 0, 191) : substr($anchor, 0, 191);
  $slot_material = implode('|', array((int) $source_post_id, $area, $heading_key, $occurrence, $element_type));
  $slot_key = cocoon_click_hmac('slot|' . $slot_material);
  $destination_key = cocoon_click_hmac('destination|' . $destination['canonical']);
  $link_key = cocoon_click_hmac('link|' . $slot_material . '|' . $destination_key . '|' . cocoon_click_hmac('label|' . $anchor));
  return array(
    'link_key' => $link_key,
    'slot_key' => $slot_key,
    'destination_key' => $destination_key,
    'area' => $area,
    'heading_key' => $heading_key,
    'heading_label' => $heading_label,
    'occurrence' => $occurrence,
    'element_type' => $element_type,
    'anchor_text' => $anchor,
  );
}
endif;
