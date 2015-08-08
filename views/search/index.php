<<!DOCTYPE html>
<html>
<head>
	<title>测试</title>
	<script type="text/javascript" src="/static/iadmin/vendors/jquery-1.9.1.min.js"></script>
</head>

<body>

</body>

<script type="text/javascript">
$(function () {
	$.get("http://suggest.taobao.com/sug?code=utf-8&q=切糕&callback=dachie","",function(data){
　　　　var list = eval(data);//eval必须
　　　　console.table(list);
},"text");
});
</script>
</html>