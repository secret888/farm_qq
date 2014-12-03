<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );

class db  {
    protected $db;
    /**
     * 数据源
     * mysql的结构
     * "mysql:host=localhost;dbname=test";
     * @var string
     */
    protected $dns;
    protected $host;
    protected $dbname;
    protected $user;
    protected $pass;
    protected $dbms = 'mysql';

    public function __construct($config)
    {
        try {
            $this->host = $config['host'];
            $this->dbname = $config['name'];
            $this->dns = $this->dbms.':host='.$this->host.';dbname='.$this->dbname;
            $this->user = $config['user'];
            $this->pass = $config['pass'];

            $this->db = new PDO($this->dsn,$this->user,$this->pass);

        } catch (Exception $e) {
            die("error: " . $e->__tostring() . "<br/>");
        }
    }

    /**
     * 可用于直接执行 插入，删除 更新语句
     * @param $sql
     * @return int
     */
    public function query($sql)
    {
        try{
            return $this->db->exec($this->setstring($sql));
        }catch (Exception $e){
            die("error: " . $e->__tostring() . "<br/>");
        }
    }

    public function fetchRow($sql)
    {
        try {
            $result = $this->db->query($this->setstring($sql));
            $result->setFetchMode(PDO::FETCH_ASSOC);
            return $result->fetchAll();
        }catch (Exception $e){
            die("error: " . $e->__tostring() . "<br/>");
        }
    }

    /**
     * 用于防注入
     * @param $sql
     * @return mixed
     */
    private final function setstring($sql)
    {
//        echo "我要处理一下$sql";
        return $sql;
    }
}