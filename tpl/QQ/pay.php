<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xn="http://www.renren.com/2009/xnml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>充值精石 - 史前传说</title>
</head>

<body>
<?php include "header.php"; ?>
<link type="text/css" rel="stylesheet" href="<?php echo $this->flash_vars['staticPath'];?>ms/css/core.css" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->flash_vars['staticPath'];?>ms/css/payment2.css" />

<div id="content">
<div class="user-info">
    <a class="avatar"><img src="<?php echo $User->info['face'];?>" width="50" height="50" alt="<?php echo $User->info['name'];?>" /></a>
    <h2><?php echo $User->info['name'];?></h2>
    <p><label><span id="spanFBalance"><?php echo $User->info['premium'];?></span></label></p>
</div>
<div class="pay-form">
<h2>选择你要兑换的面值（黄钻全场8折优惠）</h2>
<div id="message" class="notice none"></div>
	<ul class="pay-type clearfix">
	<li  class="fcoin-100">
        <div title="充值50个精石">充值50个精石</div>
        <p><input type="button" class="btn-red" value="兑换" onclick="pay('CASH1');" /></p>
		<p class="exchange-rate">50Q点=50精石</p>
	</li>
	<li  class="fcoin-200">
	    <div  title="充值100个精石">充值100个精石</div>
		<p><input type="button" class="btn-red" value="兑换" onclick="pay('CASH2');" /></p>
		<p class="exchange-rate">100Q点=100精石</p>
    </li>

	<li  class="fcoin-500">
	    <div  title="充值200个精石">充值200个精石</div>
		<p><input type="button" class="btn-red" value="兑换" onclick="pay('CASH3');" /></p>
		<p class="exchange-rate">190Q点=200精石</p>
    </li>

    <li class="fcoin-1000">
        <div title="充值500个精石">充值500个精石</div>
        <p><input type="button" class="btn-red" value="兑换" onclick="pay('CASH4');" /></p>
		<p class="exchange-rate">460Q点=500精石</p>
	</li>
    <li class="fcoin-5000">
        <div title="充值1000个精石">充值1000个精石</div>
        <p><input type="hidden" name="ac"  id="ac"  value="exchange" />
    	<input type="button" class="btn-red" value="兑换" onclick="pay('CASH5');" /></p>
		<p class="exchange-rate">900Q点1000精石</p>
	</li>
	</ul>   
   </div>
  <!--支付平台推荐游戏板块开始-->
   <div class="pay-list">

   		<div class="list-title"></div>
   </div>   
  <!--支付平台推荐游戏板块结束-->
</div>
</body>
</html>
</body>
</html>	