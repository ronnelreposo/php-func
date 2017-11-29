<?php
	/**
	*	Represents on creating an Optional Values.
	*	@author R.R.
	*/
	final class Option
	{
		private $_value;
		private function __construct() { }
		/**
		*	Creates a Some Optional value.
		*	@param $value: mixed.
		*	@return this object.
		*/
		static final function Some ($value) : Option { $this->_value = $value; return $this; }
		/**
		*	Creates a None value.
		*	@return this object.
		*/
		static final function None () : Option { return $this; }
		/**
		*	Determines if the this Option has value.
		*	@return bool.
		*/
		final function hasValue () : bool { return $this->value == null; }
		/**
		*	Gets the value of this Option.
		*	@return value: mixed.
		*	Exception: 'The value is not available.'.
		*/
		final function value () {
			if ($this->hasValue()) { throw new Exception('The Value is not available.'); }
			return $this->_value;
		}
	}
?>