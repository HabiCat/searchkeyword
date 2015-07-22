
        <link href="/static/iadmin/assets/DT_bootstrap.css" rel="stylesheet" media="screen">
        <style type="text/css">
        .tdlen { min-width: 30%;}
        label { vertical-align: middle;}
        input { vertical-align: middle;}
        </style>
                <div class="span9" id="content">

                    <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">权限列表</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                <form action="<?php echo Yii::$app->urlManager->createUrl('iadmin/auth/edit-power') ?>" method="post" name="powerForm">
  									<table class="table">
						              <thead>
						                <tr>
						                  <th>后台所有栏目权限<span style="color: red;">(用户组：<?= $groupName ?>)</span></th>
						                </tr>
						              </thead>
						              <tbody>
                                        
                                        <?php if(!empty($menus)): ?>
                                            <?php foreach($menus as $p): ?>
        						                <tr>
        						                  <td colspan="2"><label clas><input type="checkbox" <?php echo in_array($p['id'], $adminGroupPower) ? 'checked="checked"' : ''; ?> name="Power[<?php echo $p['menu_acl'] ?>]" value="<?php echo $p['id'] ?>" /><?php echo $p['menu_title'] ?></td>
        						                </tr>
                                                <?php if(isset($p['submenu'])): ?>
                                                    <?php foreach($p['submenu'] as $s): ?>
                                                    <tr>
                                                        <td class="tdlen"><label style="padding-left: 20px;"><input type="checkbox" <?php echo in_array($s['id'], $adminGroupPower) ? 'checked="checked"' : ''; ?> name="Power[<?php echo $s['menu_acl'] ?>]" value="<?php echo $s['id'] ?>" /><?php echo $s['menu_title'] ?></label></td>                                               
                                                        
                                                    <?php if(isset($s['ops'])): ?>
                                                        <?php foreach($s['ops'] as $t): ?>
                                                        <td><label><input type="checkbox" <?php echo in_array($t['id'], $adminGroupPower) ? 'checked="checked"' : ''; ?> name="Power[<?php echo $t['menu_acl'] ?>]" value="<?php echo $t['id'] ?>" /><?php echo $t['menu_title'] ?></label></td>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?> 

                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif;?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        
						              </tbody>
						            </table>
                                    <?= \yii\helpers\Html::hiddenInput('id', $id) ;?>
                                    <?= \yii\helpers\Html::submitButton('提交', ['class'=> 'btn btn-success']) ;?>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                </div>
            </div>
