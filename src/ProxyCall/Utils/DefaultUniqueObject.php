<?php
namespace ProxyCall\Utils;


class DefaultUniqueObject
{
	private static $instance = null;
	
	private final function __construct() {}
	private final function __wakeup() {}
	private final function __clone() {}
	
	
	/**
	 * @return DefaultUniqueObject
	 */
	public final static function get()
	{
		if (!self::$instance)
			self::$instance = new self();
		
		return self::$instance; 
	}
	
	/**
	 * @param mixed $obj
	 * @return bool
	 */
	public static function isMe($obj)
	{
		return self::$instance === $obj;
	}
}