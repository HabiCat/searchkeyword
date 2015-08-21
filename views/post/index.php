<section>
	<style type="text/css">
		ul, li { list-style: none; }
		.pager li { float: left; padding: 5px;}
		.pager .active { }
		.pager .active a {font-size: 22px; color: red;}
		.autoSearch { min-height: 24px; }
		.autoSearch ul, li { padding: 0; margin: 0;}
	</style>
	<div class="search">
		<?php 
            $form = \yii\widgets\ActiveForm::begin([
                'id' => 'postSearchForm',
                // 'method' => 'get',
                // 'action' => \Yii::$app->urlManager->createUrl('post/index'),
            ]);
		?>
		
        <?= $form->field($model, 'searchName',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused', 'id' => 'searchInput','value' => $searchName])->label('关键字<span class="required">*</span>') ?>

        <div class="autoSearch" id="autoSearch"></div>

		<?= \yii\helpers\Html::submitButton('搜索', ['class'=> 'btn btn-primary']) ;?>

		<?= \yii\helpers\Html::hiddenInput('page', 1) ;?>

		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
	<ul>
		<?php foreach($data as $value): ?>
			<li>
				<span><?= $value['id'] ?></span>&nbsp;&nbsp;<a href="<?= \Yii::$app->urlManager->createUrl(['post/jump-url', 'url' => $value['url_code']]) ?>"><?= $value['excerpts'][0] ?></a>
				&nbsp;&nbsp;<span><?= $value['excerpts'][1] ?></span>
				&nbsp;&nbsp;<a href="<?= \Yii::$app->urlManager->createUrl(['post/update', 'id' => $value['id']]) ?>">编辑</a>
				&nbsp;&nbsp;<a class="delBtn" data-id="<?= $value['id'] ?>" href="#">删除</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="row">
		<div class="pager" id="pager">
		<?= \yii\widgets\LinkPager::widget([
		        'pagination' => $pager,
		        'prevPageLabel' => '上一页',
		        'nextPageLabel' => '下一页',
		    ]);
		?>
		</div>
	</div>
	<script src="/static/iadmin/vendors/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">
	$(function () {
		$('#pager').children().find('a').on('click', function () {
			$("input[name='page']").prop('value', parseInt($(this).attr('data-page')) + 1);
			$('#postSearchForm').submit();
			return false;
		});

		$('.delBtn').on('click', function () {
			$.post('index.php?r=post/delete', {id:$(this).attr('data-id')}, function (msg) {
                var jsonStr = $.parseJSON(msg);
                if(jsonStr['status'] == 1) {
                    alert(jsonStr.msg);
                    window.location.reload();
                } else {
					alert(jsonStr.msg);               
                }
			});

			return false;
		});

		$('#autoSearch').css({'width':$('#searchInput').width()+4});
		$('#searchInput').keyup(function () {
			$('#autoSearch').html('<ul><li>111111</li><li>222222</li></ul>');
		});

		// $('body').click(function () {
		// 	$('#autoSearch').css({'display': 'none'});
		// });
	}); 
	</script>
</section>