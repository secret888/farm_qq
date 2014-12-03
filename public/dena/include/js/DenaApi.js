var _viewer_id = "";
var userinfo_params={};
var all_fuserinfo =new Array();
var all_fuserinfo_params=new Array();

function init(params)
{
	var _URL_ = config._Base_URL+'../index.php';
	checkAuth(_URL_,'gameBody');
}
function checkAuth(_auth_url,id){
	var req = opensocial.newDataRequest();
	var viewer_params = {};
	var viewer_friends_params = {};
	viewer_params[opensocial.DataRequest.PeopleRequestFields.PROFILE_DETAILS] = [opensocial.Person.Field.THUMBNAIL_URL, opensocial.Person.Field.GENDER];
	viewer_friends_params[opensocial.IdSpec.Field.USER_ID] = opensocial.IdSpec.PersonId.VIEWER;
	viewer_friends_params[opensocial.IdSpec.Field.GROUP_ID] = opensocial.IdSpec.GroupId.FRIENDS;
	viewer_friends_params[opensocial.DataRequest.PeopleRequestFields.FILTER ] = opensocial.Person.Field.HAS_APP;
	var viewer_friends_idspec = opensocial.newIdSpec(viewer_friends_params);
	req.add(req.newFetchPeopleRequest(viewer_friends_idspec, {"max":1000}), 'friends');    
	req.add(req.newFetchPersonRequest(opensocial.IdSpec.PersonId.VIEWER, viewer_params), 'viewer');
	req.send(function(dataResponse) {
		if (dataResponse.hadError()) {
			top.location = "http://sb.yahoo-mbga.jp/game/" + config._App_ID;
			return;
		}else{
			var viewer = dataResponse.get('viewer').getData();
			_viewer_id = getNumUid(viewer.getId());
			var _name = viewer.getDisplayName();
			var _thumbnail_url= viewer.getField(opensocial.Person.Field.THUMBNAIL_URL);
			var _gender = viewer.getField(opensocial.Person.Field.GENDER);
			var _sGender = _gender ? _gender.getDisplayValue() : 'unknown';
			userinfo_params['u_id']=_viewer_id;
			userinfo_params['name']=_name;
			userinfo_params['thumbnail_url']=_thumbnail_url;
			userinfo_params['gender']=_sGender;
			var viewerFriends = dataResponse.get('friends').getData();
		    viewerFriends.each(function(friend) {
		    	all_fuserinfo['fu_id']=getNumUid(friend.getId());
		    	all_fuserinfo['f_name']=friend.getDisplayName();
		    	all_fuserinfo['f_thumbnail_url']=friend.getField(opensocial.Person.Field.THUMBNAIL_URL);
		    	all_fuserinfo['f_gender']=friend.getField(opensocial.Person.Field.GENDER);
		    	all_fuserinfo['HAS_APP']=friend.getField(opensocial.Person.Field.HAS_APP);
		    	all_fuserinfo_params.push(all_fuserinfo);
		    	all_fuserinfo=[];
		      }); 
			 
			 var fuids = '';
		     for(var i=0;i<all_fuserinfo_params.length;i++)
		     {
				 fuids += all_fuserinfo_params[i]['fu_id'];
				 fuids += '-';
				
		     };
			
			userinfo_params['fuids'] = fuids;
			
		    var _callback = function(response){
		    	//response为flash_vars
		    	loadFlash(response.data);
			};
			makePOSTRequest(_auth_url, userinfo_params, _callback);
		}
	});
}
function loadFlash(data){
	var flashvar;
	flashvar = 'dena=1' ;
	for (items in data){
		flashvar +='&'+items+'='+data[items];
		}
	var params = {
		width:  "950",
		height: "650",
		wmode: "opaque",
		allowFullScreen: "true",
		id: "flashPlayer",
		name:"flashPlayer",
		allowScriptAccess :"always",
		flashvars:flashvar
	};
	gadgets.flash.embedFlash(
			data.mainpath+data.flashName,
		document.getElementById("gameBody"),
		6,
		params
	);
	
	
	
	

	 $('#gameBottom').html("UID: "+_viewer_id);
	 //setUserId(data.uid);
}
function makePOSTRequest(url, post_data, callback) {
	var params = {};
	var _callback = callback || function() {};
	params[gadgets.io.RequestParameters.METHOD] = gadgets.io.MethodType.POST;
	params[gadgets.io.RequestParameters.POST_DATA]= gadgets.io.encodeValues(post_data);
	//params[gadgets.io.RequestParameters.AUTHORIZATION] = gadgets.io.AuthorizationType.NONE;
	//params[gadgets.io.RequestParameters.CONTENT_TYPE] = gadgets.io.ContentType.TEXT;
	params[gadgets.io.RequestParameters.AUTHORIZATION] = gadgets.io.AuthorizationType.SIGNED;
	params[gadgets.io.RequestParameters.CONTENT_TYPE] = gadgets.io.ContentType.JSON;
	gadgets.io.makeRequest(url, _callback, params);
}


function invite(params) {
	opensocial.requestShareApp("VIEWER_FRIENDS", null, function(response) {
		var recipientIds = response.getData()["recipientIds"];
		if(recipientIds.length <= 0){
			return;
		}
		var backurl = config._Base_URL + "../index.php?act=inviteOne&invnum=" + recipientIds.length;
		makePOSTRequest(
			backurl, 
			{'u_id':_viewer_id,'ids':recipientIds},
			function(response){
				//
			}
		);
	});
}

function sendMessage(params){
	var _callback = params.callback || function(){};
	var _params = {}

	_params[opensocial.Message.Field.TITLE] = params.title;
	var msg = opensocial.newMessage(params.body, _params);
	opensocial.requestSendMessage(params.recipient, msg, _callback);
}
function makeGETRequest(url, callback) {
	var params = {};
	var _callback = callback || function() {};

	params[gadgets.io.RequestParameters.METHOD] = gadgets.io.MethodType.GET;

	gadgets.io.makeRequest(url, _callback, params);
}


function showDialogBuy(params,onSuccess){
	//从后端获取支付相关信息
	var _URL_ = config._Base_URL+'../index.php?act=denaPay';
	var post_data_1 = {};
	post_data_1["item_id"] = params.item_id;
	post_data_1["u_id"] = _viewer_id;
	
	makePOSTRequest(
			_URL_,
			post_data_1,
			function(res){
					var data = res.data; 
					var itemParams = {};
					itemParams[opensocial.BillingItem.Field.SKU_ID] = data.skuid;
					itemParams[opensocial.BillingItem.Field.PRICE]  = data.price;
					itemParams[opensocial.BillingItem.Field.COUNT]  = 1;
					itemParams[mbga.BillingItem.Field.NAME]         = data.title;
					itemParams[opensocial.BillingItem.Field.DESCRIPTION] = data.content;
					itemParams[mbga.BillingItem.Field.IMAGE_URL]    = data.pic;
					var item = opensocial.newBillingItem(itemParams);

					var payparams = {};
					payparams[opensocial.Payment.Field.ITEMS]  = [item];
					payparams[opensocial.Payment.Field.AMOUNT] = data.price;

					var payment = opensocial.newPayment(payparams);
					opensocial.requestPayment(payment, function(responseItem) {
						if (responseItem.hadError()) {
							ff = {};
							movieName = "flashPlayer";
							if (navigator.appName.indexOf("Microsoft") != -1) {
								ff = window[movieName];
							}else {
								ff =  document[movieName];
							}
							var obj = {'id':params.id,'result':{'status':'ok'},'error':'cancel'}
							ff.externalInterfaceRpcReceive(obj);
							// alert('fail');
							return false;
						} else {
							var url=config._Base_URL+'callback/payhandler.php';
							var payment = responseItem.getData();
							var orderId = payment.getField(opensocial.Payment.Field.ORDER_ID);
							var post_data = {};
							post_data["orderid"] = orderId;
							post_data["u_id"] = _viewer_id;
							var _callback = function(response){
								ff = {};
								movieName = "flashPlayer";
								if (navigator.appName.indexOf("Microsoft") != -1) {
									ff = window[movieName];
								}else {
									ff =  document[movieName];
								}
								if(params.id<0)
								{
									ff.buyOver(params.item_id);
								}
								else
								{
									var obj = {'id':params.id,'result':{'status':'ok'},'error':''}
									ff.externalInterfaceRpcReceive(obj);
								}
								//alert('add_payment success!');
								onSuccess();
							};
							makePOSTRequest(url, post_data, _callback);
						}
					});
				
			}
		);
}
function getNumUid(uid){
	if(isNaN(uid)){ ary=uid.split(":"); if(ary.length==2){uid=ary[1];}}
	return uid;
}

function sendShare(params){
	if (typeof(params) == undefined || params == null) {
		return;
	}
	var feed_params = {};
	feed_params[opensocial.Activity.Field.TITLE] = params.title;
	feed_params[opensocial.Activity.Field.BODY] = params.msg ;
	if (typeof(params.picturl) != undefined && params.picturl != null && params.picturl != "") {
		var mediaItem = opensocial.newMediaItem("image/gif" , params.picturl);
		feed_params[opensocial.Activity.Field.MEDIA_ITEMS]=[mediaItem];
	}
	var activity = opensocial.newActivity(feed_params);
	opensocial.requestCreateActivity(activity, opensocial.CreateActivityPriority.HIGH,
	function(response) {
		if (response.hadError()) {
			alert(response.getErrorCode());
		}
		else {
			// alert('send feed success!');
		}
	});
}
init(config);

/////////////////////////////////////////////////////////////////
//没有用到的接口

function loadTest(params)
{
	loadHtml(params._URL_+'?uid='+_viewer_id+'&t=' + (new Date()).getTime(), {}, "gameBody");
	alert(_viewer_id);
	gadgets.window.adjustHeight(3000);
}



function loadHtml(url, post_data, id) {
	if (typeof(post_data)==undefined ||  post_data==null)
	var data = {}
	var params = {};
	//params[gadgets.io.RequestParameters.METHOD] = gadgets.io.MethodType.POST;
	//params[gadgets.io.RequestParameters.POST_DATA] = gadgets.io.encodeValues(post_data);
	params[gadgets.io.RequestParameters.CONTENT_TYPE] = gadgets.io.ContentType.TEXT;
	params[gadgets.io.RequestParameters.AUTHORIZATION] = gadgets.io.AuthorizationType.NONE;
	gadgets.io.makeRequest(url, function(response) {
		var data = response.data;
		jQuery("#" + id).html(data);
	}, params);
}
