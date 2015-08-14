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

                                   </div>
                                    <form action="<?php echo Yii::$app->urlManager->createUrl('iadmin/admin/delete-dict') ?>" name="form2" method="post">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="all" id="all" value="" />&nbsp;&nbsp;ID</th>
                                                <th>文件名/目录名</th>
                                                <th>文件大小</th>
                                                <th>文件类型</th>
                                                <th>文件创建时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody id="datalist">
                                            <?php foreach($data as $key => $value): ?>
                                            <tr class="odd gradeX">
                                                <td><input type="checkbox" name="dir[]" value="<?php echo $key ?>" />&nbsp;&nbsp;<?php echo $key ?></td>
                                                <td><?php echo $key ?></td>
                                                <td>--</td>
                                                <td>目录</td>
                                                <td>--</td>
                                                <td class="center">
                                                    <a href="<?php echo Yii::$app->urlManager->createUrl(['iadmin/tools/delete-dict', 'dir' => $key]) ?>">删除</a>
                                                </td>
                                            </tr>
                                            <?php foreach($value as $k => $val): ?>
                                            <tr class="odd gradeX">
                                                <td><input type="checkbox" name="file[]" value="<?php echo $val['filename'] ?>" />&nbsp;&nbsp;<?php echo $k + 1 ?></td>
                                                <td><?php echo $val['filename'] ?></td>
                                                <td><?php echo $val['filesize'] ?></td>
                                                <td class="center"><?php echo $val['filetype'] ?></td>
                                                <td class="center"><?php echo $val['filetime'] ?></td>
                                                <td class="center">
                                                    <a href="<?php echo Yii::$app->urlManager->createUrl(['iadmin/tools/delete-dict', 'file' => $val['filename']]) ?>">删除</a>
                                                </td>
                                            </tr>                                            	
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- 用户列表 -->
                                    <script type="text/html" id="uploadDictRowTemplate">
                                        {{each templateData as item i}}
                                            <tr class="odd gradeX">
                                                <td><input type="checkbox" name="dir[]" value="{{i}}" />&nbsp;&nbsp;{{i}}</td>
                                                <td>{{i}}</td>
                                                <td>--</td>
                                                <td>目录</td>
                                                <td>--</td>
                                                <td class="center">
                                                    <a href="index.php?iadmin/tools/delete-dict&dir={{i}}">删除</a>
                                                </td>
                                            </tr>
	                                        {{each item as elem n}}
	                                        <tr class="odd gradeX">
	                                            <td><input type="checkbox" name="file[]" value="{{elem.filname}}" />&nbsp;&nbsp;{{n + 1}}</td>
	                                            <td>{{elem.filename}}</td>
	                                            <td>{{elem.filesize}}</td>
	                                            <td class="center">{{elem.filetype}}</td>
	                                            <td class="center">{{elem.filetime}}</td>
	                                            <td class="center">
	                                                <a href="index.php?r=iadmin/admin/delete-dic&file={{elem.filename}}">删除</a>
	                                            </td>
	                                        </tr>   
	                                        {{/each}}                                         
                                        {{/each}}
                                    </script>
                                    
                                    <div class="row">
                                      <div class="btn-group" id="uploadArea">
                                         <a href="javascript:void(0);"><button class="btn btn-success" id="pickFilesBtn">添加文件<i class="icon-plus icon-white"></i></button></a>
                                         <a href="javascript:void(0);"><button class="btn btn-success" id="uploadFilesBtn">上传文件<i class="icon-plus icon-white"></i></button></a>
                                      	 <div id="console" style="color: red;"></div>
                                      </div>
                                      <div id="fileList"></div>
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
		<script type="text/javascript" src="/static/js/plupload/plupload.full.min.js"></script>
        <script type="text/javascript">
            function getAdminListTemplate(jsonStr) {
                var uploadDictRowHtml = template('uploadDictRowTemplate',{'templateData':jsonStr['data']});
                $('#datalist').html(uploadDictRowHtml);
                $('#pager').html(jsonStr['pager']);               
            }

            /** 分页ajax  */
            $('.pagination').children().find('a').on('click', function () {

                if(($(this).attr('data-page') + 1)) {
                    $.post('index.php?r=iadmin/tools/upload-dict', {'page': parseInt($(this).attr('data-page')) + 1}, function (jsonStr) {
                        var jsonStr = $.parseJSON(jsonStr);
                        getAdminListTemplate(jsonStr);
                    });
                }
                return false;
            });

            $('#all').on('click', function() {
                if($(this).prop('checked')) {
                    $('input[type="checkbox"]:not(input[name="all"])').prop('checked', true);  
                } else {
                    $('input[type="checkbox"]:not(input[name="all"])').prop('checked', false);  
                }
            });

            var uploader = new plupload.Uploader({
				runtimes : 'html5,flash,silverlight,html4',
				browse_button : 'pickFilesBtn', // you can pass an id...
				container: document.getElementById('uploadArea'), // ... or DOM Element itself
				url : 'index.php?r=iadmin/tools/uploaded',
				flash_swf_url : '/static/js/plupload/Moxie.swf',
				silverlight_xap_url : '/static/js/plupload/Moxie.xap',
				
				filters : {
					max_file_size : '10mb',
					mime_types: [
						{title : "搜狗词典", extensions : "scel"}
					]
				},

				init: {
					PostInit: function() {
						document.getElementById('fileList').innerHTML = '';

						document.getElementById('uploadFilesBtn').onclick = function() {
							uploader.start();
							return false;
						};
					},

					FilesAdded: function(up, files) {
						plupload.each(files, function(file) {
							document.getElementById('fileList').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
						});
					},

					UploadProgress: function(up, file) {
						document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
					},

					Error: function(up, err) {
						document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
					}
				}
			});

			uploader.init();
        </script>