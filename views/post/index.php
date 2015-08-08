<section>
	<style type="text/css">
		ul, li { list-style: none; }
		.pager li { float: left; }
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
        ])->textInput(['class' => 'input-xlarge focused', 'value' => $searchName])->label('关键字<span class="required">*</span>') ?>
		<?= \yii\helpers\Html::submitButton('搜索', ['class'=> 'btn btn-primary']) ;?>

		<?= \yii\helpers\Html::hiddenInput('page', 1) ;?>

		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
	<ul>
		<?php foreach($data as $value): ?>
			<li><span><?= $value['_source']['id'] ?></span>&nbsp;&nbsp;<a href="<?= $value['_source']['url_code'] ?>"><?= $value['_source']['subject'] ?></a></li>
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
	}); 
	</script>
</section>