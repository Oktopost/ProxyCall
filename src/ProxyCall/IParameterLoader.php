<?php
namespace ProxyCall;


interface IParameterLoader
{
	/**
	 * @param \ReflectionParameter $parameter
	 * @param mixed $default
	 * @return mixed
	 */
	public function load(\ReflectionParameter $parameter, $default);
}