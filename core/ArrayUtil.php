<?php
 //Array Function Utilities. -----------------------------------
 final class ArrayUtil
 {
 	private function __construct() { }
 	private static final function mapf ($projection, int $i, array $acc, array $source) { if ($i > (count($source) - 1)) { return $acc; } $acc[$i] = $projection($source[$i]); return self::mapf($projection, ($i + 1), $acc, $source); }
 	private static final function mapfi ($projection_i, int $i, array $acc, array $source) {if ($i > (count($source) - 1)) { return $acc; } $acc[$i] = $projection_i($i, $source[$i]); return self::mapfi($projection_i, ($i + 1), $acc, $source); }
 	private static final function filterf ($predicate, int $i, array $acc, array $source) {if ($i > (count($source) - 1)) { return $acc; } if ($predicate($source[$i])) { $acc[$i] = $source[$i]; } return self::filterf($predicate, ($i + 1), $acc, $source); }
 	private static final function iteri ($f, int $i, array $source) {if ($i > (count($source) - 1)) { return; } $f($source[$i]); return self::iteri($f, ($i + 1), $source); }
 	static final function map ($projection, array $source) {return self::mapf($projection, 0, array(), $source); }
 	static final function mapi ($projection_i, array $source) {return self::mapfi($projection_i, 0, array(), $source); }
 	static final function filter ($predicate, array $source) {return self::filterf($predicate, 0, array(), $source); }
 	static final function foldfr ($f, int $i, $acc, array $source) {if ($i > count($source) - 1) { return $acc; } return $f($source[$i], self::foldfr($f, ($i + 1), $acc, $source)); }
  static final function foldfl ($f, int $i, $acc, array $source) {if ($i > count($source) - 1) { return $acc; } return self::foldfl($f, ($i + 1), $f($acc, $source[$i]), $source); }
 	static final function foldr ($f, $acc, array $source) { return self::foldfr($f, 0, $acc, $source); }
  static final function foldl ($f, $acc, array $source) { return self::foldfl($f, 0, $acc, $source); }
 	static final function iter ($f, array $source) { return self::iteri($f, 0, $source); }
 }

 //Array function utilitites. ----------------------------------
 $arrayToFList = function (array $source) : FList {
 	return ArrayUtil::foldr(function ($x, $acc) {return FList::cons($x, $acc); },
 	FList::empty(), $source);
 };

 /**
 * Filters the array indexed data.
 * allowing numerically index data only with the corresponding value.
 * e.g. from: $array_mixed = array(["c1"] => "data", [0] => 1, ["c2"] => "data2", [1] => 5); 
 * e.g. to: $array_filtered = array([0] => 1, [1] => 5);
 * ***special use: in mixed type array index.
 * ***note: the desired filtered array should be half size of $array_mixed size.
 * ***dangers: should be used with extreme caution. Unstable.
 */
 $filter_numi = function ($i, array $acc, array $source) use (&$filter_numi) {
 	$halfOfSize = count($source) / 2;
 	if ($i > ($halfOfSize - 1)) { return $acc; }
 	$acc[$i] = $source[$i];
 	return $filter_numi(($i + 1), $acc, $source);
 };
 /// See function filter_numi.
 $filter_num = function (array $source) use ($filter_numi) { return $filter_numi(0, array(), $source); };

 ?>