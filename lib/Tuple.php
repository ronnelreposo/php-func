<?php
 /**
 * Represents a pair of data.
 * @author R. R.
 */
 final class Tuple
 {
 	private $_fst;
 	private $_snd;
 	private function __construct ($fst, $snd) { $this->_fst = $fst; $this->_snd = $snd; }
 	static final function Create ($fst, $snd) { return new Tuple ($fst, $snd); }
 	final function fst () {
 		if ($this->_fst == null) { throw new \Exception("First element doesn't exist."); }
 		return $this->_fst;
 	}
 	final function snd () {
 		if ($this->_snd == null) { throw new \Exception("Second element doesn't exist."); }
 		return $this->_snd;
 	}
 }
 $tuple = function ($fst) : callable {
 	return function ($snd) use ($fst) : Tuple {
 		return Tuple::Create($fst, $snd);
 	};
 };
 $fst = function (Tuple $tuple) { return $tuple->fst(); };
 $snd = function (Tuple $tuple) { return $tuple->snd(); };
?>