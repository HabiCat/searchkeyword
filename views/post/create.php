<section>
	<style type="text/css">

	</style>
	<div class="post-content">
		<?php 
            $form = \yii\widgets\ActiveForm::begin([
                'id' => 'postCreateForm',
            ]);
		?>

        <?= $form->field($model, 'subject',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused'])->label('标题<span class="required">*</span>') ?>
        <?= $form->field($model, 'url',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused'])->label('链接<span class="required">*</span>') ?>
        <?= $form->field($model, 'keywords',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused'])->label('关键词<span class="required">*</span>') ?>
        <?= $form->field($model, 'description',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textarea(['class' => 'input-xlarge focused'])->label('描述') ?>
        <?= \yii\helpers\Html::submitButton('提交', ['class'=> 'btn btn-primary']) ;?>

		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</section>