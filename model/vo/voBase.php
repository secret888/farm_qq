<?php
class voBase
{
	public $_info;

	public function __construct()
	{
		if( !$this->_info )
		{
			$cache = Common::getCache();
			$this->_info = $cache->get($this->_key);
			
			if($this->_info == false)
			{
				throw new Exception("xml init error");
			}
		}

	}

	public function getByKey($pid)
	{
		return $this->_info[$pid];
	}
}