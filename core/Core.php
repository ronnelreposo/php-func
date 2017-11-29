<?php
 /**
 * Applies an input x to function f.
 * forward x f = f (x).
 */
	$forward = function ($x) {
		return function (callable $f) use ($x) {
			return $f($x);
		};
	};
 /**
 * Applies an input x to function f,
 * then the result will be applied to function g.
 * compose f g x = g (f (x)).
 */
	$compose = function (callable $f) {
		return function (callable $g) use ($f) {
			return function ($x) use ($f, $g) {
				return $g($f($x));
			};
		};
	};
?>