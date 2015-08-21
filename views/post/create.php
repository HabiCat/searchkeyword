<section>

	<div class="post-content">
		<?php 
            $form = \yii\widgets\ActiveForm::begin([
                'id' => 'postCreateForm',
            ]);
		?>

        <?= $form->field($model, 'subject',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused', 'id' => 'subject'])->label('标题<span class="required">*</span>') ?>
        <?= $form->field($model, 'url',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused', 'id' => 'url'])->label('链接<span class="required">*</span>') ?>
        <?= $form->field($model, 'keywords',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textInput(['class' => 'input-xlarge focused', 'id' => 'keywords'])->label('关键词<span class="required">*</span>') ?>
        <?= $form->field($model, 'description',[
            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
        ])->textarea(['class' => 'input-xlarge focused', 'id' => 'description'])->label('描述') ?>
        <?= \yii\helpers\Html::button('提交', ['class'=> 'btn btn-primary', 'id' => 'postBtn']) ;?>

		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
<script src="/static/iadmin/vendors/jquery-1.9.1.min.js"></script>
    <script type="text/javascript">
    $(function () {
        $('#postBtn').on('click', function() {
            $.post('index.php?r=post/create', $('#postCreateForm').serialize(), function(msg) {
                var jsonStr = $.parseJSON(msg);
                if(jsonStr['status'] == 1) {
                    alert(jsonStr.msg);
                    window.location.reload();
                } else {
                    for(var i in jsonStr['msg']) {
                        $('#' + i).next().html(jsonStr['msg'][i][0]);
                    }                  
                }
            });
        });
    });
    </script>
</section>