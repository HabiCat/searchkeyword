<style type="text/css">
.help-block {color: red; display: inline-block; padding-left: 20px;}
#wmenu-type label { display: inline;}
</style>
                <div class="span9" id="content">
                      <!-- morris stacked chart -->
                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">添加后台菜单项</div>
                                <div class="muted pull-right" onclick="javascript: window.history.go(-1);" style="cursor: pointer;">返回</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                    <?php
                                    $form = \yii\widgets\ActiveForm::begin([
                                        'id' => 'authCreateForm',
                                        'options' => [
                                            'class' => 'form-horizontal',      
                                        ],
                                    ]);

                                    ?>
                                     <form class="form-horizontal">
                                      <fieldset>
                                        <legend>添加菜单项(权限有关)</legend>
                                        <!--<?= $form->errorSummary($model); ?>-->

                                        <?= $form->field($model, 'menu_title',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->textInput(['class' => 'input-xlarge focused'])->label('菜单名<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'menu_url',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->textInput(['class' => 'input-xlarge focused'])->label('菜单URL<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'type',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->radioList(['0' => '普通菜单', '1' => '操作项'])->label('菜单类型<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'pid',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->dropDownList($groupList, ['prompt'=>'请选择', 'class' => 'selectError'])->label('上级菜单<span class="required">*</span>') ?>

                                        <?= $form->field($model, 'sort',[
                                            'template' => '<div class="control-group">{label}<div class="controls">{input}{error}</div></div>',
                                        ])->textInput(['class' => 'input-xlarge focused' , 'value' => 0])->label('排序<span class="required">*</span>') ?>
                                        
                                        <div class="form-actions">
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

    