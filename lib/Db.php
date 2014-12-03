<?php
defined( 'IN_INU' ) or exit( 'Access Denied' );

class db  {
    protected $db;
    /**
     * 数据源
     * mysql的结构
     * $dsn = "mysql:host=localhost;dbname=test";
     * @var string
     */
    protected $dsn;
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
            $this->dsn = $this->dbms.':host='.$this->host.';dbname='.$this->dbname;
            $this->user = $config['user'];
            $this->pass = $config['passwd'];

            $this->db = new PDO($this->dsn,$this->user,$this->pass);
            $this->db->setAttribute(PDO::ATTR_CASE,PDO::CASE_LOWER);
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

    public function fetchTest($sql)
    {
        try{
            $sql = "select * from user_000";
            $rs =  $this->db->query($sql);
            return $rs;
        }catch (Exception $e){
            var_dump($e);
        }
    }
    /**
     * 获取第一行数据
     * @param $sql
     * @return array
     */
    public function fetchRow($sql)
    {
        try {
            $result = $this->execute($sql);
            $result->setFetchMode(PDO::FETCH_ASSOC);

            return $result->fetch();
        }catch (Exception $e){
            die("error: " . $e->__tostring() . "<br/>");
        }
    }

    /**
     * 获取所有记录
     */
    public function fetchArray($sql)
    {
        try {
            $result = $this->execute($sql);
            $result->setFetchMode(PDO::FETCH_ASSOC);

            return $result->fetchAll();
        }catch (Exception $e){
            die("error: " . $e->__tostring() . "<br/>");
        }
    }

    /**
     * 获取第一条记录的第一个字段
     */
    public function fetchCol($sql)
    {
        try {
            $result = $this->execute($sql);
            return $result->fetchColumn();
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

    /**
     * 执行SQL文件
     */
    private function execute($sql)
    {
        $result = $this->db->prepare($this->setstring($sql));
        $result->execute();
        return $result;
    }

}