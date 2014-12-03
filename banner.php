<?php 
define( "ROOT_DIR" , dirname( __FILE__ ) . '' );
include ROOT_DIR.'/config.php';
define( "CONFIG_DIR" , ROOT_DIR . "/config/" );
define( "MOD_DIR" , ROOT_DIR ."/model" );
define( "CON_DIR" , ROOT_DIR ."/controller/".SNS );
define( "CACHE_DIR" , ROOT_DIR ."/cache/" );
define( "TPL_DIR" , ROOT_DIR ."/tpl/" . SNS );
define( "LIB_DIR" , ROOT_DIR ."/lib" );

require LIB_DIR .'/Core.php';
require LIB_DIR .'/MemcachedClass.php';
require CON_DIR .'/Api.php';
Common::loadModel("CommonModel");

$cache = Common::getCache();
$type = $_GET['type'];
$key = "gm_banner";
$banner = $cache->get($key);
$bannerlist = array();
foreach($banner as $v)
{
	$bannerlist[$v['type']][] = $v;
}

if(empty($banner))exit;
$flash_vars = CommonModel::getValue('flash_vars');
$flash_vars = eval("return $flash_vars;");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
* {margin:0;padding:0;}
body { font-size:12px;}
#pic1 a {color:#333;}
#ad_text a {color:#FFF}
#ad_text a {color:#FFF}
a:link { text-decoration: none;}
ul {list-style:none;}
</style>
<script type="text/javascript" src="<?php echo $flash_vars['staticPath'];?>ms/js/jquery.js"></script>
</head>
<body>


<?php 
if($_GET['type']==1 || empty($_GET['type']))
{
?>
<div id="pic1">
</div>
	<script type="text/javascript" src="<?php echo $flash_vars['staticPath'];?>ms/js/imagechange.js"></script>
	<script>
	var data = <?php echo json_encode($bannerlist[1]);?>;
		
	$(document).ready(function(){
		$('#pic1').d_imagechange({
			data:data,
			btn:true,
			width:950,
			height:150,
			playTime:5000,
			bgHeight:20,
			bg:false,
			title:false,
			desc:false,
			href:false
		});
	});
	</script>
	
<?php 	
}
?>

<?php 
if($_GET['type']==2)
{
?>
<div id="ad_text" style="color:#FFF">
</div>
<script>
	var notic_text = <?php echo json_encode($bannerlist[2]);?>;
	
	var text_count = <?php echo count($bannerlist[2])?>;
	var text_id = 0;
	var iScrollAmount = 1;
	scroll_tip();
	function scroll_tip() 
	{
		if (iScrollAmount > 0)
		{
			document.getElementById('ad_text').innerHTML = '<a target="_blank" href="'+notic_text[text_id]['href']+'">'+notic_text[text_id]['title']+'</a>';
			text_id++;
			text_id%=text_count;
		}
		window.setTimeout( "scroll_tip()", 5000 );
	}
	
</script>
<?php 
}
?>

</body>

</html>
