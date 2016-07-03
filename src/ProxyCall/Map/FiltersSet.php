<?php
namespace ProxyCall\Map;


class FiltersSet
{
	private $set = [];
	
	
	/**
	 * @param string $key
	 * @param ParameterFilter $filter
	 */
	public function add($key, ParameterFilter $filter)
	{
		if (isset($this->set[$key]))
		{
			$this->set[$key] = $filter;
		}
		else
		{
			$this->set[$key] = [$filter];
		}
	}
	
	/**
	 * @param string $key
	 * @return ParameterFilter[]
	 */
	public function get($key)
	{
		return (isset($this->set[$key]) ? $this->set[$key] : []);
	}
	
	/**
	 * @param string $key
	 * @param int $maximumPriority
	 * @param \ReflectionParameter $parameter
	 * @return ParameterFilter|null
	 */
	public function findMatch($key, $maximumPriority, \ReflectionParameter $parameter)
	{
		$filters = $this->get($key);
		$filtersCount = count($filters);
		
		for ($i = $filtersCount - 1; $i >= 0; $i--)
		{
			if ($filters[$i]->Priority > $maximumPriority) 
				continue;
			
			if ($filters[$i]->isMatching($parameter))
				return $filters[$i];
		}
		
		return null;
	}
}