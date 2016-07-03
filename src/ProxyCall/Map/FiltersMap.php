<?php
namespace ProxyCall\Map;


class FiltersMap
{
	private $nextPriority = 0;
	
	
	/** @var FiltersSet */
	private $setByType;
	
	/** @var FiltersSet */
	private $setByName;
	
	/** @var FiltersMap */
	private $genericFilter = null;
	
	/**
	 * @param ParameterFilter $filter
	 * @throws \Exception
	 */
	private function setGenericFilter(ParameterFilter $filter)
	{
		if ($this->genericFilter)
			throw new \Exception('Only one global filter can be defined');
		
		$this->genericFilter = $filter;
	}
	
	/**
	 * @param ParameterFilter $filter
	 */
	private function setFilterByType(ParameterFilter $filter)
	{
		if ($filter->PropertyName)
		{
			$this->setByName->add(strtolower($filter->PropertyName), $filter);
		}
		
		if ($filter->PropertyType)
		{
			$this->setByType->add($filter->PropertyType, $filter);
		}
	}
		
	
	
	public function __construct() 
	{
		$this->setByName = new FiltersSet();
		$this->setByType = new FiltersSet();
	}
	
	
	/**
	 * @param ParameterFilter $filter
	 * @throws \Exception
	 */
	public function addFilter(ParameterFilter $filter)
	{
		if (!$filter->PropertyName && !$filter->PropertyType)
		{
			$this->setGenericFilter($filter);
			return;
		}
		
		$filter->Priority = $this->nextPriority++;
		
		$this->setFilterByType($filter);
	}
	
	/**
	 * @param \ReflectionParameter $parameter
	 * @return FiltersMap
	 */
	public function getFilter(\ReflectionParameter $parameter)
	{
		$filter = $this->setByName->findMatch($parameter->getType(), $this->nextPriority, $parameter);
		
		$filter = $this->setByName->findMatch(
			strtolower($parameter->getName()),
			$filter ? $filter->Priority : $this->nextPriority, 
			$parameter);
		
		return $filter ?: $this->genericFilter;
	}
}