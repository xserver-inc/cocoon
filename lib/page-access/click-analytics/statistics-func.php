<?php //クリック解析統計関数
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'cocoon_click_format_percent' ) ):
function cocoon_click_format_percent($value){
  return $value === null ? '—' : number_format_i18n((float) $value * 100, 1) . '%';
}
endif;

if ( !function_exists( 'cocoon_click_effective_sample_size' ) ):
function cocoon_click_effective_sample_size($sum_weight, $sum_weight_squared){
  $sum_weight = (float) $sum_weight;
  $sum_weight_squared = (float) $sum_weight_squared;
  if ($sum_weight <= 0 || $sum_weight_squared <= 0) return 0.0;
  // 初心者向け: 重みがばらつくほど実質的な標本数が小さくなるように補正します。
  return ($sum_weight * $sum_weight) / $sum_weight_squared;
}
endif;

if ( !function_exists( 'cocoon_click_wilson_interval' ) ):
function cocoon_click_wilson_interval($weighted_clicks, $weighted_impressions, $sum_weight_squared, $z = 1.96){
  $impressions = (float) $weighted_impressions;
  if ($impressions <= 0) return array('rate' => null, 'lower' => null, 'upper' => null, 'effective_n' => 0.0);
  $rate = min(1.0, max(0.0, (float) $weighted_clicks / $impressions));
  $effective_n = cocoon_click_effective_sample_size($impressions, $sum_weight_squared);
  if ($effective_n <= 0) return array('rate' => $rate, 'lower' => null, 'upper' => null, 'effective_n' => 0.0);
  // 初心者向け: 少数データの100%を過大評価しないWilson信頼区間を計算します。
  $z2 = $z * $z;
  $denominator = 1 + ($z2 / $effective_n);
  $center = ($rate + ($z2 / (2 * $effective_n))) / $denominator;
  $margin = ($z * sqrt(($rate * (1 - $rate) / $effective_n) + ($z2 / (4 * $effective_n * $effective_n)))) / $denominator;
  return array(
    'rate' => $rate,
    'lower' => max(0.0, $center - $margin),
    'upper' => min(1.0, $center + $margin),
    'effective_n' => $effective_n,
  );
}
endif;

if ( !function_exists( 'cocoon_click_coordinate_bin' ) ):
function cocoon_click_coordinate_bin($basis_points, $bins){
  $basis_points = max(0, min(10000, (int) $basis_points));
  $bins = max(1, (int) $bins);
  return min($bins - 1, (int) floor(($basis_points / 10000) * $bins));
}
endif;

if ( !function_exists( 'cocoon_click_sanitize_device' ) ):
function cocoon_click_sanitize_device($device){
  return in_array($device, array('mobile', 'tablet', 'desktop'), true) ? $device : 'desktop';
}
endif;

if ( !function_exists( 'cocoon_click_data_is_sufficient' ) ):
function cocoon_click_data_is_sufficient($effective_n, $sampled_clicks){
  return (float) $effective_n >= 100 && (int) $sampled_clicks >= 10;
}
endif;

if ( !function_exists( 'cocoon_click_data_sufficiency_reasons' ) ):
function cocoon_click_data_sufficiency_reasons($effective_n, $sampled_clicks){
  $reasons = array();
  if ((float) $effective_n < 100) $reasons[] = 'effective_sample_size';
  if ((int) $sampled_clicks < 10) $reasons[] = 'sampled_clicks';
  return $reasons;
}
endif;

if ( !function_exists( 'cocoon_click_detect_ctr_anomaly' ) ):
function cocoon_click_detect_ctr_anomaly($current, $previous){
  if (empty($current['data_sufficient']) || empty($previous['data_sufficient'])) return 'insufficient';
  if ($current['ctr_lower'] !== null && $previous['ctr_upper'] !== null && $current['ctr_lower'] > $previous['ctr_upper']) return 'high';
  if ($current['ctr_upper'] !== null && $previous['ctr_lower'] !== null && $current['ctr_upper'] < $previous['ctr_lower']) return 'low';
  return 'stable';
}
endif;

if ( !function_exists( 'cocoon_click_sampling_weight' ) ):
function cocoon_click_sampling_weight($sampling_rate){
  $sampling_rate = max(1, min(100, (int) $sampling_rate));
  // 初心者向け: 20%抽出なら1件を5件相当として戻す逆確率重みを計算します。
  return (int) round(100 / $sampling_rate);
}
endif;

if ( !function_exists( 'cocoon_click_retention_cutoff' ) ):
function cocoon_click_retention_cutoff($today, $retention_days){
  $retention_days = max(1, (int) $retention_days);
  return gmdate('Y-m-d', strtotime($today . ' -' . ($retention_days - 1) . ' days'));
}
endif;
