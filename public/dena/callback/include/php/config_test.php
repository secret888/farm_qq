<?php 
$config = Common::getConfig();
define('_Platform', "dena");
define('_RestApi_Debug', true);
define('_SunvyApi_Debug', false);
define('_App_ID', $config['api']['appId']);
define('_Consumer_Key', $config['api']['apiKey']);
define('_Consumer_Secret', $config['api']['Secret']);
define('_Sunvy_mediaId', "");
define('_Sunvy_mediaKey', "");
define('_Sunvy_mediaSecret', "");
