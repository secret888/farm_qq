<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="<?php echo $this->flash_vars['serverPath'];?>QQ/jquery.js"></script>
</head>
<title>免费送礼-机甲之战</title>

<style type="text/css" charset="utf-8">
.giftformsubmit{
	background-color:#3b5998;
	border-color:#d9dfea #0e1f5b #0e1f5b #d9dfea;
	border-style:solid;
	border-width:1px;
	color:white;
	font-size:12px;
	font-weight:bold;
	margin:1px 5px;
	padding:3px 10px;
	text-decoration:none;
	cursor:pointer;
}
.inputbutton,.inputsubmit{
	border-style:solid;
	border-width:1px;
	border-color:#d9dfea #0e1f5b #0e1f5b #d9dfea;
	background-color:#3b5998;
	color:#fff;
	padding:2px 15px 3px 15px;
	text-align:center;
	cursor:pointer;
	*width:1;
	*overflow:visible;
	*padding:2px 15px;
}
.inputaux{
	background:#f0f0f0;
	border-color:#e7e7e7 #666 #666 #e7e7e7;
	color:#000
}
.gift_name {
   color: #000;
	margin: 10px auto 5px;
   font-size: 9pt;
   font-weight:bold;
}
.gift_img{
	padding:20px 0 0 0;
	text-align:center;
	height: 85px;
}
.giftLocked .gift_name { color: #000; }

.gift_action {
	margin: 0 auto;
	color: red;
	font-weight: bold;
	font-size: 8pt;
}
.main_gift_cont
{
	margin: 0;
}
.main_gift_cont ul{
	list-style-type:none;
	padding-left:10px;
}
.main_gift_cont li {
   float: left;
   text-align: center;
   width: 143px;
   height: 166px;
   background: #FFFFFF url(<?php echo $this->flash_vars['staticPath'];?>ms/images/freegift/gifts_police.png) center center no-repeat;
}


.main_gift_cont li.energy_gift { background-image: url(<?php echo $this->flash_vars['staticPath'];?>ms/images/freegift/gifts_item_green.png); }
.main_gift_cont li.permit_gift { background-image: url(<?php echo $this->flash_vars['staticPath'];?>ms/images/freegift/gifts_item_orange.png); }
.main_gift_cont li.lock_gift { background-image: url(<?php echo $this->flash_vars['staticPath'];?>ms/images/freegift/gifts_item_gray2.png); }

a.inputaux{padding: 5px 15px;}

.energy_gift {
}
.proceedSkip {
   float: right;
   margin: 8px 50px 8px 0;
}
.getgiftbutton{
	border-color:#d9dfea #0e1f5b #0e1f5b #d9dfea;
	border-width:1px;
	border-style:solid;
	background-color:#3B5998;
	margin:1px 5px;
	padding:3px 10px;
	cursor:pointer;
	color:#fff;
	font-weight:bold;
}
</style>
<script type="text/javascript" charset="utf-8" src="http://fusion.qq.com/fusion_loader?appid=<?php echo $this->config["api"]["appId"];?>&platform=qzone"></script>

<script type="text/javascript">
/*
* 发出礼物
*/
jQuery(function() {
$(".sendGiftbutton").click(function() {
    var itemstr = $(":checked").attr("value");
    if(itemstr == undefined){
        alert("请选择您要赠送的礼物!");
    }else{
		var item = itemstr.split("::");
		var stat = 123;
		var title = item[1];
		var desc = item[1];
		var imgurl = item[2];
		var context = item[0];
		var msg = '';
		$("#loginimg").show();
		$("#freegiftform").hide();
		sendGift(stat,title,desc,msg,imgurl,context,callback='');
    }
});
});

function sendGift(stat,title,desc,msg,imgurl,context){
	fusion2.dialog.sendRequest({
		appid : '<?php echo $this->config["api"]["appId"];?>',
		type : 'freegift',
		stat : stat,// 必须 。这里请传入请求的标识，由应用自定义，用于请求事件发送和点击量的统计。如:'action_id'.
		title : title,//必须 。gift的名称 如: '扩地证'.
		desc : desc,//如:'用于扩大土地面积'.
		msg : msg,//必须 。送礼的默认赠言，可由用户修改 如 : '送你一个扩地证，赶快扩大城市面积吧！'
		img : imgurl,//图片的URL，建议为应用的图标或者与请求相关的主题图片。要求存放在APP域名下或腾讯CDN，规格为65*65px 
		context : context,//可选。透传参数，用于onSuccess和onCancel回调时传入的参数，以识别请求
		callback : callback,//必须 。这里请传入处理某一条请求时的回调URL。(文档中说暂时没用)
		onSuccess : function (opt) {
			var ids = opt.receiver.join(",");
			var data = "act=sendfreegift&context=" + opt.context + "&ids=" + ids;
			var parent_url = window.location.href;
			var url = parent_url.replace('act=freegift','act=sendfreegift');
			$.ajax({
				async: false,
				type: "POST",
				url:url,
				data: data,
				timeout: 5000,
				success: function(msg){
					alert('您的礼物已经发出!');
					returngame();
				}
			});	// end ajax
		},
		onCancel : function (opt) {
			//回到游戏
			returngame();
		},
		onClose : function () {
			//回到游戏
			returngame();
		}
	});
}

/*
* 进页面时，显示/隐藏收到礼物的列表
*/
$("body").ready(function() {
    if(0 == <?php echo $count;?>){
        $("#acceive_gift").hide();
        $("#gift_list").css('height','504px');
    }else{
        $("#acceive_gift").show();
        $("#gift_list").css('height','336px');
    }
});

/*
* 领取完礼物时，显示/隐藏收到礼物的列表
*/
jQuery(function() {
    $("#send_freegift").click(function() {
		var data = "";
		var parent_url = window.location.href;
		var url = parent_url.replace('act=freegift','act=receiveFreegift');
        $.ajax({
            async: false,
            type: "GET",
            url:url,
            data: data,
            timeout: 5000,
            success: function(msg){
				$("#acceive_gift").hide();//隐藏收到的礼物列表
		        $("#gift_list").css('height','504px');//加高送礼列表
                alert('您已经成功接收到好友的礼物！请重新进游戏查收！');
            }
        }); // end ajax
    });
});

/*
* 回到游戏
*/
function returngame(){
	window.parent.$("#QzoneFreeGift").hide();
}
/*
* 取消
*/
jQuery(function() {
$(".cancelbutton").click(function() {
	returngame();
});
});
/*
* 进游戏取消
*/
jQuery(function() {
$("#loginimg").click(function() {
    returngame();
});
});

</script>
<div style="width: 760px;height:650px;border:0;">
  <img src="<?php echo $this->flash_vars['staticPath'];?>ms/images/freegift/login1.jpg" id="loginimg" style="display:none;cursor:pointer;"/>
  <form action="" method="post" id="freegiftform">
	<input name="giftRecipient" value="" type="hidden">
	<input name="ref" value="" type="hidden">
	<div class="proceedSkip">
	  <input value="跳过" class="cancelbutton inputbutton inputaux giftformsubmit" type="button" />
	  <input value="继续发送 &gt;&gt;" class="sendGiftbutton giftformsubmit " type="button" />
	</div>
	<div style="clear: both;"></div>
	
	<div class="main_gift_cont" id="acceive_gift">
		<div style="margin:0 0 0 10px;"><span>您有<span style="color:#F00;font-weight:bold"><?php echo count($data);?></span>份礼物</span><input id="send_freegift" value="一键收取礼物" class="getgiftbutton" type="button"></div>
		<ul id="acceive_gift_list" style="margin:5px 0px;border:solid #999 1px; height:168px; overflow:auto; overflow-x:none;">
		<?php
		foreach($data as $row){
			echo "<li class='energy_gift'>
			<div class='gift_img'>
			<label for=''><img src='{$row['gpic']}' class='giftImg'></label>
			</div>
			<div class='gift_name'><span>".$row['gname']."</span></div>
			<div class='gift_action' style='font-size:9pt;width:128px; color:#000;'>
			来自<span style='color:#ff0'>".$row['name']."</span>的礼物
			</div>
			</li>";
		}
		?>
		</ul>
	</div>
	<div>
	<div style="clear: both;"></div>
	<div class="main_gift_cont">
	  <ul class="items" id="gift_list" style="height:504px;overflow:auto;overflow-x:none;">
	<?php
	foreach($vo_free_gift as $value){
		if($value["level"] <= $User->info["level"]){
		echo "
		<li class='permit_gift'>
		  <div class='gift_img'>
			<label for='radiopermits{$value['id']}'> <img src='{$value['pic']}' class='giftImg'></label>
		  </div>
		  <div class='gift_name'><span>{$value['name']}</span></div>
		  <div class='gift_action'>
			<input id='radiopermits{$value['id']}' name='gift' value='{$value['id']}::{$value['name']}::{$value['pic']}' type='radio'/>
		  </div>
		</li>
		";
		}else{
			echo "
        <li class='lock_gift'>
          <div class='gift_img'>
            <label for='radiopermits{$value['id']}'> <img src='{$value['pic']}' class='giftImg'></label>
          </div>
          <div class='gift_name'><span>{$value['name']}</span></div>
          <div class='gift_action' style='font-size:9pt;'>
					在第<span style='font-size:10pt;color:#ff0'>&nbsp;{$value['level']}&nbsp;</span>级解锁
          </div>
        </li>
        ";
		}
	}
	?>
	  </ul>
	</div>
	<div style="clear: both;"></div>
	<div class="proceedSkip">
	  <input value="跳过" class="cancelbutton inputbutton inputaux giftformsubmit" type="button">
	  <input value="继续发送 &gt;&gt;" class="sendGiftbutton giftformsubmit " type="button">
	</div>
	<br style="clear: both;">
  </form>
</div>

</html>
