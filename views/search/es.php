<section>
<script type="text/javascript" src="/static/iadmin/vendors/jquery-1.9.1.min.js"></script>
	<input type="button" id="postbtn" value="获取" />
	<script type="text/javascript">
$(function () {
	$('#postbtn').on('click', function () {
		$.post("http://localhost:9200/word/_analyze?analyzer=ik&pretty=true&text=花千骨",{},function(data){
	　　　　//var list = eval(data);//eval必须
	　　　　	console(data);
		},"text");
	});

});
	</script>
</section>