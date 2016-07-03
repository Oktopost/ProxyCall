<?php
namespace ProxyCall\Map;


use ProxyCall\IParameterLoader;
use ProxyCall\Utils\DefaultUniqueObject;

use Objection\LiteSetup;
use Objection\LiteObject;
use Objection\Enum\AccessRestriction;


/**
 * @property string|null 		$PropertyName
 * @property string|null 		$PropertyType
 * @property bool				$IsCaseSensitive
 * @property int				$Priority
 * @property IParameterLoader 	$Loader
 */
class ParameterFilter extends LiteObject
{
	/**
	 * @param string $name
	 * @return string
	 */
	private function getNameForComparison($name)
	{
		return $this->IsCaseSensitive ? $name : strtolower($name);
	}
	
	/**
	 * @param \ReflectionParameter $parameter
	 * @return bool
	 */
	private function compareTypes(\ReflectionParameter $parameter)
	{
		return $this->PropertyType && $parameter->getClass()->getName() == $this->PropertyType;
	}
	
	/**
	 * @param \ReflectionParameter $parameter
	 * @return bool
	 */
	private function compareNames(\ReflectionParameter $parameter)
	{
		if ($this->IsCaseSensitive)
		{
			return $this->PropertyName == $parameter->getName();
		}
		else
		{
			return strtolower($this->PropertyName) == strtolower($parameter->getName());
		}
	}
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'PropertyName'		=> LiteSetup::createString(null),
			'PropertyType'		=> LiteSetup::createString(null),
			'IsCaseSensitive'	=> LiteSetup::createBool(true),
			'Priority'			=> LiteSetup::createInt(-1, AccessRestriction::NO_SET),
			'Loader'			=> LiteSetup::createInstanceOf(IParameterLoader::class)
		];
	}
	
	
	/**
	 * @param \ReflectionParameter $parameter
	 * @return bool
	 */
	public function isMatching(\ReflectionParameter $parameter)
	{
		if ($this->PropertyType && !$this->compareTypes($parameter))
		{
			return false;
		}
		else if ($this->PropertyName && !$this->compareNames($parameter))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 * @throws \Exception
	 */
	public function loadValue(\ReflectionParameter $parameter)
	{
		if ($parameter->isOptional())
		{
			return $this->Loader->load($parameter, $parameter->getDefaultValue());
		}
		
		$value = $this->Loader->load($parameter, DefaultUniqueObject::get());
		
		if (DefaultUniqueObject::isMe($value))
		{
			throw new \Exception("Failed to load parameter value for {$parameter->getName()}");
		}
		
		return $value;
	}
}