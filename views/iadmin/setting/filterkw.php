<style type="text/css">
.help-block {color: red; display: inline-block; padding-left: 20px;}
</style>
                <div class="span9" id="content">
                      <!-- morris stacked chart -->
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">设置要过滤的关键词</div>
                                <div class="muted pull-right" onclick="javascript: window.history.go(-1);" style="cursor: pointer;">返回</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <?php
                                    $form = \yii\widgets\ActiveForm::begin([
                                        'id' => 'filterForm',
                                        'options' => [
                                            'class' => 'form-horizontal',      
                                        ],
                                    ]);

                                    ?>
                                     <form class="form-horizontal">
                                      <fieldset>
                                        <legend>添加关键词</legend>
                                        <!--<?= $form->errorSummary($model); ?>-->

                                        <?= $form->field($model, 'values',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{hint}{error}</div></div>',
                                        ])->textarea(['class' => 'input-xlarge focused', 'rows' => 5,'cols' => 20])->hint('关键词或关键字用英文逗号,分隔')->label('关键词<span class="required">*</span>') ?>

                                        <div class="form-actions">
                                        <?= \yii\helpers\Html::hiddenInput('WSetting[keys]', 'filter_keywords') ;?>
                                        <?= \yii\helpers\Html::submitButton('提交', ['class'=> 'btn btn-primary']) ;?>

                                        </div>
                                      </fieldset>
                                    <?php \yii\widgets\ActiveForm::end(); ?>

                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>

                    </div>
		    </div>
                     <!-- /validation -->




                </div>
            </div>

        <link href="/static/iadmin/vendors/datepicker.css" rel="stylesheet" media="screen">
        <link href="/static/iadmin/vendors/uniform.default.css" rel="stylesheet" media="screen">
        <link href="/static/iadmin/vendors/chosen.min.css" rel="stylesheet" media="screen">

        <link href="/static/iadmin/vendors/wysiwyg/bootstrap-wysihtml5.css" rel="stylesheet" media="screen">

    