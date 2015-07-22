<link href="/static/iadmin/assets/DT_bootstrap.css" rel="stylesheet" media="screen">
<style type="text/css">
    .row { margin-left: 0; }
    .pagination { float: right;}
    .pagination li { float: left; list-style: none; padding-left: 5px;}
</style>
                <!--/span-->
                <div class="span9" id="content">
                     <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">后台用户管理列表</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                   <div class="table-toolbar">
                                      <div class="btn-group">
                                         <a href="<?php echo Yii::$app->urlManager->createUrl('iadmin/admin/create') ?>"><button class="btn btn-success">添加用户<i class="icon-plus icon-white"></i></button></a>
                                      </div>
                                   </div>
                                    <div class="row">
                                        <div class="span6">
                                            <div id="example2_length" class="dataTables_length">
                                                <label><select size="1" name="pageSize" id="pageSize" aria-controls="example2">
                                                    <option value="10" selected="selected">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                    </select> records per page</label>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="dataTables_filter" id="example2_filter">
                                                <label style="line-height: 36px; display: block;">搜索用户名: <input type="text" name="searchName" id="searchName" aria-controls="example2">
                                                <button class="btn btn-success" id="searchBtn">搜索</button>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="<?php echo Yii::$app->urlManager->createUrl('iadmin/admin/delete') ?>" name="form2" method="post">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="all" id="all" value="" />&nbsp;&nbsp;ID</th>
                                                <th>用户名</th>
                                                <th>所属管理组</th>
                                                <th>最后登录时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="datalist">
                                            <?php foreach($datalist as $key => $value): ?>
                                            <tr class="odd gradeX">
                                                <td><input type="checkbox" name="ids[]" value="<?php echo $value['id'] ?>" />&nbsp;&nbsp;<?php echo $value['id'] ?></td>
                                                <td><?php echo $value['username'] ?></td>
                                                <td><?php echo $value['group_name'] ?></td>
                                                <td class="center"><?php echo $value['last_login_time'] ?></td>
                                                <td class="center">
                                                    <a href="<?php echo Yii::$app->urlManager->createUrl(['iadmin/admin/edit', 'id' => $value['id']]) ?>">编辑</a>
                                                    <a href="<?php echo Yii::$app->urlManager->createUrl(['iadmin/admin/delete', 'id' => $value['id']]) ?>">删除</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="searchNameHidden" id="searchNameHidden" value="" />
                                    <input type="hidden" name="pageSizeHidden" id="pageSizeHidden" value="" />
                                    
                                    <div class="row">
                                        <div class="btn-group">
                                             <a href="#"><?= \yii\helpers\Html::submitButton('批量删除', ['class'=> 'btn btn-success']) ;?></a>
                                        </div>
                                        <div class="pager" id="pager">
                                        <?php 
                                            echo \yii\widgets\LinkPager::widget([
                                                'pagination' => $pager,
                                                'prevPageLabel' => '上一页',
                                                'nextPageLabel' => '下一页',
                                            ]);
                                        ?>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
                </div>
            </div>
        
        <script src="/static/iadmin/assets/DT_bootstrap.js"></script>
        <script type="text/javascript">
        $(function () {
            $('#all').on('click', function() {
                if($(this).prop('checked')) {
                    $('input[type="checkbox"]:not(input[name="all"])').prop('checked', true);  
                } else {
                    $('input[type="checkbox"]:not(input[name="all"])').prop('checked', false);  
                }
            });
            
            /** 分页ajax  */
            $('.pagination').children().find('a').on('click', function () {
                if(($(this).attr('data-page') + 1)) {
                    $.post('index.php?r=iadmin/admin/index', {'search_name': $('#searchNameHidden').val(), 'page': parseInt($(this).attr('data-page')) + 1}, function (jsonStr) {
                        var jsonStr = $.parseJSON(jsonStr);

                        $('#datalist').html(jsonStr['datalist']);
                        $('#pager').html(jsonStr['pager']);
                    });
                }
                return false;
            });

            /** 搜索ajax **/
            $('#searchBtn').on('click', function () {
                $('#searchNameHidden').val($('#searchName').val());
                if($('#searchName').val()) {
                    $.post('index.php?r=iadmin/admin/index', {'searchName': $('#searchName').val(), 'pageSize': $('#pageSizeHidden').val()}, function (jsonStr) {
                        var jsonStr = $.parseJSON(jsonStr);

                        $('#datalist').html(jsonStr['datalist']);
                        $('#pager').html(jsonStr['pager']);
                    });
                } else {
                    alert('请输入用户名');
                }
                return false;
            });

            $('#pageSize').on('change', function () {
                if($(this).val()) {  
                    $.post('index.php?r=iadmin/admin/index', {'searchName': $('#searchNameHidden').val(), 'pageSize': $(this).val(), 'page': $('.active').find('a').attr('data-page')}, function (jsonStr) {
                        var jsonStr = $.parseJSON(jsonStr);

                        $('#datalist').html(jsonStr['datalist']);
                        $('#pager').html(jsonStr['pager']);
                    });                
                }
            });
        });
        </script>