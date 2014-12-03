<?php

class User
{
    /**
     * 修改账号
     */
    public function change()
    {
        //显示可使用沙箱模式的帐号
        $cache   = Common::getCache();
        $sandbox = $cache->get("sandbox_ustrs");
        //后台登录玩家
        $admin_key = 'for_admin';


        //gohome
        if ($_POST['uid'] > 0 && isset($_POST["gohome"])) {
            $uid       = intval($_POST['uid']);
            $sharding  = Common::getSharding($uid);
            $shard_id  = $sharding['ustr'];
            $cache     = Common::getCache();
            $admin_uid = $cache->set('admin_uid', $uid);

            Common::loadModel("CommonModel");

            $flash_vars = CommonModel::getValue('flash_vars');

            $flash_vars = eval("return $flash_vars;");

            $data               = array();
            $data['sessionKey'] = $shard_id;

            $flash_vars_str = http_build_query(array_merge($data, $flash_vars));
        }

        //沙箱模式帐号
        if ($_POST['sandboxuid'] && isset($_POST["add"])) {
            $sandboxuid = stripslashes($_POST['sandboxuid']);
            $sandbox    = eval("return $sandboxuid;");
            $cache->set('sandbox_ustrs', $sandbox);
        }

        if ($_POST['uid'] > 0 && (isset($_POST["show"]) || isset($_POST["edit"]))) {
            $uid = intval($_POST['uid']);
            $db  = Common::getDB($uid);
            //获取玩家信息
            $table = 'user_' . Common::computeTableId($uid);
            $sql   = "select * from $table where uid='$uid'";
            $row   = $db->fetchRow($sql);
        }

        if ($_POST['uid'] > 0 && $_POST["reset_hidden"] == 1) {
            $uid = intval($_POST ['uid']);
            if ($_POST ['reset'] == 1) {
                $table    = array();
                $table [] = "user_" . Common::computeTableId($uid);
                $table [] = "items_" . Common::computeTableId($uid);
                $table [] = "levels_" . Common::computeTableId($uid);
                $table [] = "activity_" . Common::computeTableId($uid);

                $db = Common::getDB($uid);
                foreach ($table as $t) {
                    $sql = "delete from `{$t}` where uid='{$uid}'";
                    echo $db->query($sql);
                }
                $table = "friends_" . Common::computeTableId($uid);
                $sql   = "delete from `{$table}` where fid='{$uid}'";
                echo $db->query($sql);
            }

            $cache    = Common::getCache();
            $sharding = $cache->get("{$uid}_sharding");
            $ustr     = $sharding ['ustr'];

            $mycache    = array();
            $mycache [] = "{$ustr}_ustr";
            $mycache [] = "{$uid}_sharding";
            $mycache [] = "{$uid}_user";
            $mycache [] = "{$uid}_levels";
            $mycache [] = "{$uid}_items";
            $mycache [] = "{$uid}_pyFriends";
            $mycache [] = "{$uid}_qzFriends";
            $mycache [] = "{$uid}_activity";


            foreach ($mycache as $k) {
                echo $cache->delete($k);
            }

            $msg = 'Reset success: ' . $uid;
        }

        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }

    /**
     * 修改账号信息
     */
    public function userChange()
    {
        //查看
        if ($_POST['uid'] && $_POST['show']) {
            $uid = intval($_POST['uid']);
            Common::loadModel('UserModel');
            $userModel = new UserModel($uid);
            $info      = $userModel->info;

        }
        //修改
        if ($_POST['uid'] && $_POST['change']) {
            $uid  = intval($_POST['uid']);
            $info = stripslashes($_POST["info"]);
            $info = eval("return $info;");

            Common::loadModel('UserModel');
            $userModel                     = new UserModel($uid);
            $userModel->info               = $info;
            $userModel->info['updatetime'] = $_SERVER['REQUEST_TIME'];
            $userModel->destroy();
            $msg = "Success";
        }
        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }


    /**
     * 修改道具数量
     */
    public function itemsChange()
    {
        //查看
        if ($_POST['uid'] && $_POST['show']) {
            $uid = $_POST['uid'];
            Common::loadModel('ItemModel');
            $ItemModel = new ItemModel($uid);
            $info      = $ItemModel->info;
        }

        //编辑
        if ($_POST['uid'] && $_POST['change']) {
            $uid = $_POST['uid'];

            $info = stripslashes($_POST['info']);
            $info = eval("return $info;");
            if (is_array($info)) {
                Common::loadModel('ItemModel');
                $ItemModel       = new ItemModel($uid);
                $ItemModel->info = $info;
                $ItemModel->destroy();
                $msg = "Success";
            }
        }
        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }

    /**
     * 修改战斗日志
     */
    public function activityChange()
    {
        //查看
        if ($_POST['uid'] && $_POST['show']) {
            $uid = $_POST['uid'];
            Common::loadModel('ActivityModel');
            $ActivityModel = new ActivityModel($uid);
            $info          = $ActivityModel->info;
        }

        //编辑
        if ($_POST['uid'] && $_POST['change']) {
            $uid = $_POST['uid'];

            $info = stripslashes($_POST['info']);
            $info = eval("return $info;");
            if (is_array($info)) {
                Common::loadModel('ActivityModel');
                $ActivityModel       = new ActivityModel($uid);
                $ActivityModel->info = $info;
                $ActivityModel->destroy();
                $msg = "Success";
            }
        }
        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }


    /**
     * 关卡
     *array (
     * 'uid' => '10001',
     * 'id' => '1',
     * 'score' => '125',
     * 'stars' => '3',
     * 'time' => '1369395921',
     * )
     */
    public function levelsChange()
    {


        //查看
        if ($_POST['uid'] && $_POST['show']) {
            $uid = intval($_POST['uid']);
            Common::loadModel('LevelModel');
            $LevelModel = new LevelModel($uid);
            $list       = array_keys($LevelModel->info);
            $topid      = array_pop($list);
            $id         = intval($_POST['id']);
            $info       = $LevelModel->info[$id];
        }
        //改加
        if ($_POST['uid'] && $_POST['change']) {
            $uid  = intval($_POST['uid']);
            $id   = intval($_POST['id']);
            $info = stripslashes($_POST["info"]);
            $info = eval("return $info;");
            Common::loadModel('LevelModel');
            $LevelModel = new LevelModel($uid);
            if ($LevelModel->info[$id] && $info) {
                $LevelModel->info[$id] = $info;
            }
            if (empty($LevelModel->info[$id]) && $info) {
                $updata          = array();
                $updata['id']    = $info['id'];
                $updata['score'] = $info['score'];
                $updata['stars'] = $info['stars'];
                $updata['time']  = $_SERVER['REQUEST_TIME'];
                $LevelModel->add($updata);
            }
            $LevelModel->destroy();
            $list  = array_keys($LevelModel->info);
            $topid = array_pop($list);
            $msg   = "Success";
        }
        //删除
        if ($_POST['uid'] && $_POST['delete']) {
            $uid = intval($_POST['uid']);
            $id  = intval($_POST['id']);
            Common::loadModel('LevelModel');
            $LevelModel = new LevelModel($uid);
            if ($LevelModel->info[$id]) {
                unset($LevelModel->info[$id]);
                $LevelModel->destroy();
                $db    = Common::getDB($uid);
                $table = 'levels_' . Common::computeTableId($uid);
                $sql   = "delete from {$table} where uid={$uid},and id={$id}";
                $db->query($sql);

                $msg = "Success";
            }
            $list  = array_keys($LevelModel->info);
            $topid = array_pop($list);
        }
        //跳关
        if ($_POST['uid'] && $_POST['skip']) {
            $uid = intval($_POST['uid']);
            Common::loadModel('LevelModel');
            $LevelModel = new LevelModel($uid);
            $list       = array_keys($LevelModel->info);
            $topid      = array_pop($list);
            $info       = $LevelModel->info[$topid];
            $topid += 1;
            $info['id'] = $topid;
            $LevelModel->add($info);
            $LevelModel->destroy();
        }

        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }

    public function showCache()
    {
        //查看缓存
        if (!empty ($_POST ['key']) && $_POST["submit"] == "show") {
            $key   = $_POST ['key'];
            $cache = & Common::getCache();
            $data  = $cache->get($key);
        }

        //修改缓存
        if (!empty ($_GET ['key']) && (!empty ($_GET ['k']) || !empty ($_GET ['v'])) && $_GET["submit"] == "submit") {
            $key = trim($_GET ['key']);
            $k   = trim($_GET ['k']);
            $v   = trim($_GET ['v']);

            $mccache = & Common::getCache();
            $data    = $mccache->get($key);

            if (is_array($data)) {
                $data[$k] = $v;
            } else {
                $data = $v;
            }
            $data['updatetime'] = $_SERVER['REQUEST_TIME'];

            $mccache->set($key, $data);
            header("Location: ?mod={$_GET['mod']}&act={$_GET['act']}");
        }

        //删除缓存
        if (!empty ($_POST ['key']) && $_POST["submit"] == "delete") {
            $key   = $_POST ['key'];
            $key   = trim($key);
            $cache = & Common::getCache();
            $cache->delete($key);
            $msg = 'Delete success: ' . $key;
        }
        //导出缓存信息
        if (!empty($_POST['key']) && $_POST['submit'] == 'export') {
            $cache    = Common::getCache();
            $_key     = $_POST['key'];
            $data     = $cache->get($_key);
            $path     = CONFIG_ADM_DIR . "/data/";
            $filename = $path . $_key . "_" . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.log';
            file_put_contents($filename, var_export($data, true));
            $msg = 'export success: ' . $_key;
        }

        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }


    /**
     * 查找vz中ustring对应的uid
     */
    public function findUid()
    {
        if ($_POST ['ustring']) {
            $ustring = trim($_POST['ustring']);
            $db      = Common::getDbName();
            $ustring = trim($_POST ['ustring']);
            $sql     = "select * from gm_sharding where ustr = '{$ustring}'";
            $row     = $db->fetchRow($sql);
        }
        if ($_POST ['uid']) {
            $uid = intval(trim($_POST['uid']));
            $db  = Common::getDbName();
            $sql = "select * from gm_sharding where uid = '{$uid}'";
            $row = $db->fetchRow($sql);
        }
        if ($_POST ['uname']) {
            $uname = trim($_POST ['uname']);
            $data  = array();

            $Sharding = Common::getConfig("Sharding");
            $config   = Common::getConfig();
            foreach ($Sharding as $dbName) {
                $db = Common::getDbName($dbName);
                for ($i = 0; $i < $config['param']['table_div']; $i++) {
                    $table = "user_" . str_pad($i, $config['param']['table_bit'], '0', STR_PAD_LEFT);
                    $sql   = "select uid,name from $table where name like '{$uname}%'";
                    $info  = $db->fetchArray($sql);
                    if (!empty ($info) && is_array($info)) {
                        $data = array_merge($data, $info);
                    }
                }
            }
        }
        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }


    /**
     * 留存
     */
    public function keep()
    {
        $page    = max(1, intval($_GET['page']));
        $perPage = 30;
        $offset  = ($page - 1) * $perPage;
        $limit   = "limit " . $offset . "," . $perPage;

        $db   = Common::getDbName();
        $sql  = "select * from `gm_keep` order by id desc $limit";
        $data = $db->fetchArray($sql);

        include TPL_DIR . str_replace('controller', '', strtolower(__CLASS__)) . '/' . __FUNCTION__ . '.php';
    }

}
