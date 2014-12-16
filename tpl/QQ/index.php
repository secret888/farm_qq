<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $this->flash_vars['game_name']?></title>
    <style>
        * {
            margin: 0px;
            padding: 0px;
        }
        #flashMain {

            background:  url('<?php echo $this->flash_vars['CDN'];?>/ms/images/loading.png') no-repeat  center;
        }

        #notice {
            background:none repeat scroll 0 0 #EFFFEF;
            border:1px solid #669966;
            color:#446F25;
            display:block;
            font-size:13px;
            line-height:140%;
            margin:5px 5px 5px 0px;
            padding:5px;
            text-align:center;
            width:748px;
        }
        #notice a {text-decoration:none;color:#446F25;}
        #notice a:hover {text-decoration:underline;color:#F00;}

        .fm_help {
            line-height:20px;
            -moz-border-radius: 5px 5px 5px 5px;

            display: none;
            margin: 0 0 4px;
            padding: 4px 8px 4px;
            width: 740px;
            font-size:13px;
            color:#cccccc;
        }
        .fm_helper span {
            margin: 0 5px;
        }
        .fm_helper a {
            color: #666666;
        }
        .fm_uid {
            float: right;
        }

        .recognize .list_pic li {
            overflow:hidden;
            float:left;
            margin-left:12px;
        }
        a {
            text-decoration:none;
        }
        .name {
            width:62px;
            overflow:hidden;
            text-align:center;
        }
        .emlink {
            color:#077D01 !important;
        }
        #add_friend{
            -moz-border-radius: 5px 5px 5px 5px;
            border: 2px solid #E5E5E5;
            padding: 4px 8px 4px;
            width: 740px;
            font-size:12px;
            float:left;
        }
    </style>
</head>

<body >

<?php include "header.php"; ?>
<div id="flashMain" style="height:650px;">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="970" height="650" id="flashPlayer">
        <param name="movie" value="<?php echo $this->flash_vars['mainpath'].$this->flash_vars['flashName'];?>" />
        <param name="flashvars" value="<?php echo $flashvars;?>" />
        <param name="menu" value="false" />
        <param name="allowFullScreen" value="true" />
        <param name="allowscriptaccess" value="always" />
        <param name="wmode" value="transparent" />
        <param name="bgcolor" value="#93eafe" />
        <embed name="flashPlayer" src="<?php echo $this->flash_vars['mainpath'].$this->flash_vars['flashName'];?>" flashvars="<?php echo $flashvars;?>" menu="false" allowFullScreen="true" bgcolor="#93eafe"  wmode="transparent"  allowscriptaccess="always" type="application/x-shockwave-flash" width="970" height="650"></embed>
    </object>
</div>
<div style="clear: both"></div>
<div  style="height:50px;margin-top:10px;padding:5px 0 0 10px;font-size:13px;border:1px solid #cccccc;">
    <div  style="">
        <div  style="height:25px;">
            <p >
            <span  style="font-weight: bold;" >
				编号：<?php echo $this->sharding["ustr"];?>
			</span> 
			<span  style="padding-left:30px;">
			官方交友讨论群：322736468
			</span>
            </p>
        </div>
        <div  style="height:25px;">
		<span >此应用由“<?php echo $this->flash_vars['company_name']?>”提供。
		遇到问题，请联系<a href="http://<?php echo $this->config['api']['appId'];?>.kf.ieodopen.qq.com/" target='_blank'><u>在线客服</u></a>
		</span>
		<span  style="padding-left:30px;">
		</span>
        </div>

    </div>
</div>



<script>

    fusion2.canvas.setHeight
    ({
        height : 1000
    });

</script>
</body>
</html>	
