function load_jsapi(params)
{
	//document.write("<script src='http://app.sunvy.jp/banner/scripts/jquery.js'><\/script>"); 
	if(params._Platform=='dena'){
		if(params._RestApi_Debug){
    		document.write("<script src='"+params._JS_Base_URL+"js/DenaApi_test.js?v="+(new Date()).getTime()+"'><\/script>"); 
		}else {
    		document.write("<script src='"+params._JS_Base_URL+"js/DenaApi.js?v="+(new Date()).getTime()+"'><\/script>"); 
		}
	}
}