<?php
class Base{
	public $config;
	public $uid;
	public $params;
	
    public function __construct()
    {
    	$this->config = Common::getConfig();
    	
    }
    
    public function error($msg)
    {
        //exit('{"result":"error","error":"'.$msg.'","description":""}');
        return array(
        		'jsonrpc'=>2.0,
        		'id'=>$msg['id'],
        		'error'=>array(
        				'code'=>$msg['code']
        				)
        		);
    }
    
    public function wrap($data=array())
    {
        return array("response_code"=>0 , "data"=>$data);
    }

    /**
     * 构造统一的返回格式
     * @param $result
     * @return array
     */
    protected function response($result='')
    {
        $res = array(
            'jsonrpc' => 2.0,
            'id'      => $this->params[0],
        );
        if($result!==null){
            $res['result'] = $result;
        }
        return $res;
    }
}