<script type="text/javascript"> 
var _appid = "<?php echo $this->config['api']['appId'];?>";
var _serverPath = "<?php echo $this->flash_vars['serverPath'];?>";
var _ustr = "<?php echo $this->sharding['ustr'];?>";
</script>

<script type="text/javascript" charset="utf-8" src="http://fusion.qq.com/fusion_loader?appid=<?php echo $this->config['api']['appId'];?>&platform=<?php echo $sns;?>"></script>
<script type="text/javascript" src="<?php echo $this->flash_vars['CDN'];?>/ms/js/jquery.js"></script>

<script type="text/javascript">   


//分享
    function feed(obj)
    {
    	fusion2.dialog.sendStory({
            title:obj.title,
            msg:obj.msg,
            img:'<?php echo $this->flash_vars['CDN'];?>/ms/images/feed.png'
            });
    }

    <?php 
	    //$cache = Common::getCache();
		//if(in_array($this->sharding['ustr'], $cache->get('sandbox_ustrs')))
		//{
	?>
	var paystr = true;
	
	<?php 		
        //}
       // else
        //{
    ?>
		//var paystr = false;
    <?php
		//}
    ?>
    
    //支付
    function pay(item_id,id)
    {
    	//alert("暂未开放");
   	 if (id == undefined)  
	  {      
    		id = -1;   
	  }  
    	
		jQuery.ajax({
			url:"?act=qqPay&item_id="+item_id,
			dataType:"json",
			type:"GET",
			success:function(data){
				if(typeof(data.err_num)=='undefined')	{
					fusion2.dialog.buy({param : data.res.url,sandbox : paystr,context : item_id , 
						onSuccess : function (opt) { 
							ff = {};
							movieName = "flashPlayer";
							if (navigator.appName.indexOf("Microsoft") != -1) {
								ff = window[movieName];
							}else {
								ff =  document[movieName];
							}
							if(parseInt(id)>=0)
							{
								var obj = {'id':id,'result':{'status':'ok'},'error':''}
								ff.externalInterfaceRpcReceive(obj);
							}
							else
							{	
								ff.buyOver(opt.context);
							}

						},
						onCancel : function(opt){
							if(parseInt(id)>=0)
							{
								ff = {};
								movieName = "flashPlayer";
								if (navigator.appName.indexOf("Microsoft") != -1) {
									ff = window[movieName];
								}else {
									ff =  document[movieName];
								}
								var obj = {'id':id,'result':{'status':'ok'},'error':'cancel'}
								//ff.externalInterfaceRpcReceive(obj);
							}
							else
							{	
								alert("购买取消");
							}

						}
					
					});
				}
				else{
					if(parseInt(id)>=0)
					{
						ff = {};
						movieName = "flashPlayer";
						if (navigator.appName.indexOf("Microsoft") != -1) {
							ff = window[movieName];
						}else {
							ff =  document[movieName];
						}
						var obj = {'id':id,'result':{'status':'ok'},'error':'cancel'}
						ff.externalInterfaceRpcReceive(obj);
					}
					else
					{	
						alert("登录已超时，请重新登录。");
					}
					
				}
			},
			error:function(){
				alert("对不起，请求服务器错误，请稍后再试。");
			}
		});	
		
    }
	function pay_callback(obj) {
		ff = {};
		movieName = "flashPlayer";
		if (navigator.appName.indexOf("Microsoft") != -1) {
			ff = window[movieName];
		}else {
			ff =  document[movieName];
		}
		
		ff.buyOver(obj.context);	
	}

	function receiveitem(obj)
	{
		jQuery.ajax({
			url:"?act=receive",
			dataType:"json",
			type:"POST",
			data:obj,
			success:function(data){
				
				if(typeof(data.err_num)=='undefined')	{
					var result = {'id':obj.id,'result':data.result}
					receiveback(result);
				}
				else{
					var result = {'id':obj.id,'error':data.err_num}
					//receiveback(result)
				}
			},
			error:function(){
			}
		});	
		
	}

	function receive(obj)
	{
		jQuery.ajax({
			url:"?act=receive",
			dataType:"json",
			type:"POST",
			data:obj,
			success:function(data){
				
				if(typeof(data.err_num)=='undefined')	{
					var result = {'id':obj.id,'result':data.result}
					receiveback(result);
				}
				else{
					var result = {'id':obj.id,'error':data.err_num}
					//receiveback(result)
				}
			},
			error:function(){
			}
		});	
		
	}
	
	function receiveback(result)
	{
		ff = {};
		movieName = "flashPlayer";
		if (navigator.appName.indexOf("Microsoft") != -1) {
			ff = window[movieName];
		}else {
			ff =  document[movieName];
		}
		ff.externalInterfaceRpcReceive(result);
	}
	//邀请
	function invite(){
		
		fusion2.dialog.invite({
			onSuccess : invite_callback
		});
	}
	function invite_callback(opt)
	{
		jQuery.ajax({
			url:"?act=inviteOne&id="+opt.invitees,
			dataType:"json",
			type:"GET",
			success:function(data){				
			},
			error:function(){
			}
		});
	}

	 //Q点充值
	function recharge()
	{
		fusion2.dialog.recharge
		({
		//可选。对话框关闭时的回调方法。
		onClose : function () { }

		});
	}
	function reload()
	{
	    window.location.reload();
	}

</script>

<?php
if(strpos($this->flash_vars['forum'],"http") === false)
{
	$forum = "";
}else{
	$forum = 'target="_blank" href="'.$this->flash_vars['forum'].'"';
}

?>


<style>
body,dl,dt,dd,ul,ol,li,pre,form,fieldset,input,p,blockquote,th,td{margin:0;padding:0;}
.qz_div2 {margin-left:3px;background-position:left bottom;background-repeat:repeat-x;height:27px;padding:0;position:relative;}
.qz_div2 ul {float:left;border-bottom:1px solid #CCCCCC;width:760px;}
.qz_div2 ul li {margin-right:5px;float:left;list-style:none;}
.qz_div2 ul li a {position:relative;float:left;padding:3px 8px 3px;text-decoration:none !important;background:none repeat scroll 0 0 #E6F4BF;font-size:12px;color:#5C5C5C;border-top:1px solid #AFC67A;border-left:1px solid #AFC67A;border-right:1px solid #AFC67A;}
.qz_div2 a {border-color:#C4DA87;}
body{}
#menu
{
	background: url("<?php echo $this->flash_vars['CDN'];?>/ms/images/menu/menu_bg1.png") repeat scroll 0 0 transparent;
    height: 93px;
    width: 960px;
    margin:0 auto;
}
.nai {
    margin-left: 140px;
    padding-top:10px;
    height:50px;
}
.nai ul {
    list-style: none outside none;
}
.nai li {
    float: left;
    margin-left: 1px;
}
.nai li a {
}
.nai li a img{
	border:none;
}
.nai li a:hover {
    padding-top: 5px;
}
#menunotice
{
 	height: 20px;
    overflow: hidden;
    margin-left:150px;
    padding: 0;
    color:#000;
}
#daily
{
	background: url("<?php echo $this->flash_vars['CDN'];?>/ms/images/menu/daily1.png") repeat scroll 0 0 transparent;
    height: 221px;
    margin: 0 auto;
    width: 960px;
}

</style>
<div style="font-size:12px;padding:0px 0px 8px 10px;color:red;margin:0 auto;width:760px;">
请点击上方评分给《<?php echo $this->flash_vars['game_name']?>》打10分哦，您的5星评价是我们前进的动力，谢谢您的支持
</div>
<div style="width: 970px;margin:0 auto;">
	<a style=" height: 80px; display: block; " href="javascript:void(0)" onclick="invite();"  >
	<img src="<?php echo $this->flash_vars['CDN'];?>/ms/images/<?php echo $this->flash_vars['banner'];?>" alt="邀请送礼" title="邀请送礼" />
	</a>
</div>
<div style="clear: both"></div>
<?php 
	$urldata = $_GET;
	$playurl = "?openid=".$urldata['openid'];
	unset($urldata['openid']);
	foreach($urldata as $k=>$v)
	{
		$playurl .="&".$k."=".$v;
	}
	
?>
<div style="background: none repeat scroll 0pt 0pt transparent; text-align: center; height: 23px; margin: 5px 0pt 2px;">
    <div style="position: relative; width: 970px; " class="qz_div1">
	<div class="qz_div2">
            <ul>
                <li>
                    <a href="<?php echo $playurl;?>"><?php echo $this->flash_vars['game_name']?></a>
                </li>
                <li>
                    <a style="color: red; font-weight: bold;" href="javascript:;" onclick="invite();">邀请好友</a>
                </li>

                <li>
                    <a target="_blank" href="<?php echo $this->flash_vars['forum']?>">游戏论坛</a>
				</li>

                  <li>
                    <a onclick="recharge();return false;" href="javascript:void(0);" title="Q点充值">Q点充值</a>
                </li>
            </ul>
        <?php
        ?>
        <a href="public/tools/farm_admin.php" target="_blank">后台</a>
        <?php

        ?>
        </div>
    </div>
</div>

