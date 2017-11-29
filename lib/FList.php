<?php
	/**
	* Represents an Immutable List.
	* @author ronnelreposo
	*/
	final class FList {
  private $_hd;
  private $_tl;
  private function __construct ($hd = null, FList $tl = null) { $this->_hd = $hd; $this->_tl = $tl; }
  /**
  * FList factory constructor
  * @param $hd - 'a: The head of the list
  * @param $tl - FList: The tail of the list
  * @return new FList with $hd and $tl
  */
  static final function cons ($hd, FList $tl) : FList { return new FList($hd, $tl); }
  /**
  * Creates an Enpty FList
  * @return FList: new FList (empty)
  */
  static final function empty () : FList { return new FList(null, null); }
  /**
  * Represents the head of Flist
  * @return 'a: head of FList
  */
  final function hd () { return $this->_hd; }
  /**
  * Represents the tail of the list
  * @return FList: tail of FList
  */
  final function tl () : FList { if ($this->_tl == null) { throw new Exception('Tail is empty.'); } return $this->_tl; }
  /**
  * Tests if this FList is empty
  * @return bool: true if empty or else false
  */
  final function isEmpty () : bool { return $this->_hd == null && $this->_tl == null; }
	}

 /**
 * Represents a curried construction function for FList
 * @param a':$x The head of the FList
 * @return callable:f->$xs The function that accepts $xs as its tail for FList
 */
	$cons = function ($x) : callable {
		return function (FList $xs) use ($x) : FList {
			return FList::cons($x, $xs);
		};
	};

 /**
 * Retrieves the head of FList
 * @param FList:$xs The given FList
 * @return a': The of the FList
 */
	$hd = function (FList $xs) { return $xs->hd(); };

 /**
 * Retrieves the tail of FList
 * @param FList:$xs The given FList
 * @return FList: The tail of the FList
 */
	$tl = function (FList $xs) { return $xs->tl(); };

 /**
 * Iterates through the list using action function
 * @param callable:$action - The action function to be applied in each item
 * @return callable:f->$xs - function accepting list ($xs)
 */
	$iter = function (callable $action) : callable {
		return function (FList $xs) use ($action) {
			return ($f = function (callable $action) use (&$f) {
				return function (FList $xs) use (&$f, $action) {
					if ($xs->isEmpty()) { return; }
					$action ($xs->hd());
					$f ($action) ($xs->tl());
				};
			})($action) ($xs);
		};
	};

 /**
 * Creates a list with a single item
 * @param a':$x - The single item
 * @return new FList with $x as its head
 */
	$single = function ($x) : FList { return FList::cons($x, FList::empty()); };

 /**
 * Creates a new list which is the reverse order of the given list
 * @param FList:$xs The given list
 * @return FList
 */
	$reverse = function (FList $xs) {
		return ($f = function (FList $acc_xs) use (&$f) : callable {
			return function (FList $xs) use (&$f, $acc_xs) : FList {
				if ($xs->isEmpty()) { return FList::empty(); } 
				if ($xs->tl()->isEmpty()) { return FList::cons($xs->hd(), $acc_xs); }
				$acc_prime = FList::cons($xs->hd(), $acc_xs);
				return $f ($acc_prime) ($xs->tl());
			};
		})(FList::empty()) ($xs);
	};

 /**
 * Projects each element of a list to a new form of list using a function ($projection)
 * @param callable:$projection - The given function to project an element
 * @return callable:f->$xs - function accepting list ($xs)
 */
	$map = function (callable $projection) use ($reverse) : callable {
		return function (FList $xs) use ($projection, $reverse) : FList {
			return ($f = function (callable $projection) use (&$f, $reverse) : callable {
				return function (FList $xs) use ($projection, &$f, $reverse) : callable {
					return function (FList $acc_xs) use ($xs, $projection, &$f, $reverse) : FList {
      if ($xs->isEmpty()) { return $reverse($acc_xs); }
      $projected = $projection($xs->hd());
      $acc_prime = FList::cons($projected, $acc_xs);
      return $f ($projection) ($xs->tl()) ($acc_prime);
					};
				};
			})($projection) ($xs) (FList::empty());
		};
	};

 /**
 * Builds a new list for each element matching the function $predicate
 * @param callable:$predicate - The filter function
 * @return callable:f->$xs - A function that accepts the list $xs
 */
	$filter = function (callable $predicate) use ($reverse) : callable {
		return function (FList $xs) use ($predicate, $reverse) : FList {
			return ($f = function (callable $predicate) use (&$f) : callable {
				return function (FList $xs) use (&$f, $predicate) : callable {
					return function (FList $acc_xs) use (&$f, $predicate, $xs) : FList {
      if ( $xs->isEmpty() ) { return $acc_xs; }
      if ( $predicate ($xs->hd()) ) {
       $acc_prime = FList::cons($xs->hd(), $acc_xs);
       return $f ($predicate) ($xs->tl()) ($acc_prime);
      }
      return $f ($predicate) ($xs->tl()) ($acc_xs);
					};
				};
			})($predicate) ($reverse($xs)) (FList::empty());
		};
	};

	$foldr = function (callable $folder) use ($reverse) : callable {
		return function (FList $xs) use ($reverse, $folder) : callable {
			return function ($init) use ($reverse, $folder, $xs) {
				return ($f = function (callable $folder) use (&$f) : callable {
					return function (FList $xs) use (&$f, $folder) {
						return function ($init) use (&$f, $folder, $xs) {
       if ( $xs->isEmpty() ) { return $init; }
       $acc_prime = $folder ($xs->hd()) ($init);
       return $f ($folder) ($xs->tl()) ($acc_prime);
						};
					};
				})($folder) ($reverse($xs)) ($init);
			};
		};
	};

	$foldl = function (callable $folder) : callable {
		return function ($init) use ($folder) : callable {
			return function (FList $xs) use ($folder, $init) {
				return ($f = function (callable $folder) use (&$f) : callable {
					return function ($init) use (&$f, $folder) : callable {
						return function ($xs) use (&$f, $folder, $init) {
       if ( $xs->isEmpty() ) { return $init; }
       $acc_prime = $folder ($init) ($xs->hd());
       return $f ($folder) ($acc_prime) ($xs->tl());
						};
					};
				})($folder) ($init) ($xs);
			};
		};
	};

	$concat = function (FList $xs) use ($foldr, $cons) : callable {
		return function (FList $ys) use ($foldr, $cons, $xs) : FList {
			if ( $xs->isEmpty() && $ys->isEmpty() ) { return FList::empty(); }
			if ( $xs->isEmpty() ) { return $ys; }
			if ( $ys->isEmpty() ) { return $xs; }
			return $foldr ($cons) ($xs) ($ys);
		};
	};

	$length = function (FList $xs) use ($foldr) : int {
		return $foldr(function ($_) : callable {
   return function ($a) : int { return $a + 1; }; 
  })($xs) (0);
	};

	$reduce = function (callable $reduction) use ($foldl) : callable {
		return function (FList $xs) use ($foldl, $reduction) {
			if ($xs->isEmpty()) { throw new \Exception('Reducing a list requires a non-empty list.'); }
			return $foldl ($reduction) ($xs->hd()) ($xs->tl());
		};
	};

	$map2 = function (callable $projection) use ($reverse): callable {
  return ($f = function (FList $acc_xs) use (&$f, $reverse) : callable {
   return function (callable $projection) use (&$f, $reverse, $acc_xs) : callable {
    return function (FList $xs) use (&$f, $reverse, $acc_xs, $projection) : callable {
     return function (FList $ys) use (&$f, $reverse, $acc_xs, $projection, $xs) : FList {
      if ( $xs->isEmpty() || $ys->isEmpty() ) { return $reverse($acc_xs); }
      $projected = $projection ($xs->hd()) ($ys->hd());
      $acc_prime = FList::cons($projected, $acc_xs);
      return $f ($acc_prime) ($projection) ($xs->tl()) ($ys->tl());
     };
    };
   };
  })(FList::empty()) ($projection);
 };

 $pair = $map2($tuple);

?>