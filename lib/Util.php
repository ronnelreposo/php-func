<?php

 $pConcat = function ($a) : callable {
 	return function ($b) use ($a) : string {
 		return $a . $b;
 	};
 };
 $toTag = function ($tag) use ($pConcat) : callable {
 	return function ($content) use ($tag, $pConcat) : string {
 		return $pConcat($pConcat($pConcat($pConcat($pConcat($pConcat('<')($tag))('>'))($content))('</'))($tag))('>'); };
 };
 $reduceToTag = function ($tag) use ($reduce, $map, $pConcat, $toTag) : callable {
 	return function ($xs) use ($reduce, $map, $pConcat, $toTag, $tag) : string {
 		return $reduce($pConcat)($map($toTag($tag))($xs));
 	};
 };
 
?>