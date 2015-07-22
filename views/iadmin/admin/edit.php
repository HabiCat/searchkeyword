<style type="text/css">
.help-block {color: red; display: inline-block; padding-left: 20px;}
</style>
                <div class="span9" id="content">
                      <!-- morris stacked chart -->
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">修改用户</div>
                                <div class="muted pull-right" onclick="javascript: window.history.go(-1);" style="cursor: pointer;">返回</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <?php
                                    $form = \yii\widgets\ActiveForm::begin([
                                        'id' => 'adminCreateForm',
                                        'options' => [
                                            'class' => 'form-horizontal',      
                                        ],
                                    ]);

                                    ?>
                                     <form class="form-horizontal">
                                      <fieldset>
                                        <legend>修改用户信息</legend>
                                        <!--<?= $form->errorSummary($model); ?>-->

                                        <?= $form->field($model, 'username',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->textInput(['class' => 'input-xlarge focused'])->label('用户名<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'password',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->passwordInput(['class' => 'input-xlarge focused'])->label('密码<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'repassword',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->passwordInput(['class' => 'input-xlarge focused'])->label('确认密码<span class="required">*</span>') ?>
                                      
                                        <?= $form->field($model, 'group_id', [
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->dropDownList($groupList, ['prompt'=>'请选择', 'class' => 'selectError'])->label('用户组<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'email',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->textInput(['class' => 'input-xlarge focused'])->label('Email') ?>
                                        
                                        <?= $form->field($model, 'mobile',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->textInput(['class' => 'input-xlarge focused'])->label('手机号码') ?>

                                        <div class="form-actions">
                                        <?= \yii\helpers\Html::hiddenInput('WAdmin[id]', $model->id) ;?>
                                        <?= \yii\helpers\Html::submitButton('提交', ['class'=> 'btn btn-primary']) ;?>
                                        <?= \yii\helpers\Html::resetButton('取消', ['class'=> 'btn']) ;?>

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

    