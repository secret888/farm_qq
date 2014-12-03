<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );
class MemcachedClass
{
	protected $link;

	protected $expire = 172800;

	public function __construct( $config )
	{
	    $servers = array();
	    foreach ($config as $value)
	    {
	        $servers[] = array($value['host'],$value['port'],$value['weight']);
	    }
    	    
	    if(defined("LOCALHOST") && LOCALHOST)
	    {
    	    $this->link = new Memcache();
    	    $this->link->connect($servers[0][0],$servers[0][1]); 
	    }else {
    	    $this->link = new Memcached();
    	    $this->link->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT); 
    	    $this->link->setOption(Memcached::OPT_COMPRESSION, false);
    	    $this->link->addServers($servers);
	    }
	}

	public function set( $key , $value , $expire = 0 )
	{
		$expire = ( $expire > 0 ) ? $expire : $this->expire;
		return $this->link->set( $key , $value ,  time()+$expire );
	}

	public function add( $key , $value , $expire = 0 )
	{
		$expire = ( $expire > 0 ) ? $expire : $this->expire;
		return $this->link->add( $key , $value , time()+$expire );
	}

	public function replace( $key , $value , $expire = 0 )
	{
		$expire = ( $expire > 0 ) ? $expire : $this->expire;
		return $this->link->replace( $key , $value , time()+$expire );
	}

	public function get( $key )
	{
		return $this->link->get( $key );
	}

	public function increment( $key , $value)
	{
		return $this->link->increment( $key , $value );
	}

	public function delete( $key , $time_out = 0 )
	{
		return $this->link->delete( $key , $time_out );
	}
	
	public function flush()
	{
		return $this->link->flush();
	}
}
?>
