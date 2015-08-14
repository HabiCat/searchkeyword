<?php
//use yii\web\Session;

?>
<!DOCTYPE html>
<html class="no-js">   
    <head>
        <title>Admin Home Page</title>
        <script src="/static/iadmin/vendors/jquery-1.9.1.min.js"></script>
        <!-- Bootstrap -->
        <link href="/static/iadmin/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="/static/iadmin/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="/static/iadmin/vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
        <link href="/static/iadmin/assets/styles.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="/static/iadmin/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <script src="/static/js/artTemplate.js" type="text/javascript"></script>
    </head>
<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
            </a>
            <a class="brand" href="#">Admin Panel</a>
            <div class="nav-collapse collapse">
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> <?php echo \Yii::$app->session['accountName'] ?> <i class="caret"></i>

                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a tabindex="-1" href="#">个人资料</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a tabindex="-1" href="<?= \Yii::$app->urlManager->createUrl(['iadmin/access/logout']) ?>">退出</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav">
                    <li class="active">
                        <a href="#">Dashboard</a>
                    </li>
                    <?php 
                        
                        if(isset($this->context->_menus) && !empty($this->context->_menus)) {
                            foreach($this->context->_menus as $menu) {
                        
                    ?>
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $menu['menu_title'] ?> <b class="caret"></b>

                        </a>
                        <ul class="dropdown-menu">
                            <?php 
                                if(isset($menu['childs']) && !empty($menu['childs'])) {
                                    foreach($menu['childs'] as $value) { 
                            ?>
                            <li>
                                <a tabindex="-1" href="<?php echo Yii::$app->urlManager->createUrl($value['menu_url']) ?>"><?php echo $value['menu_title'] ?></a>
                            </li>
                            <?php } 
                                } else {
                            ?>
                            <li>无权查看</li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php }
                        }
                     ?>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3" id="sidebar">
            <ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
                <li class="active">
                    <a href="index.html"><i class="icon-chevron-right"></i> Dashboard</a>
                </li>
                <li>
                    <a href="calendar.html"><i class="icon-chevron-right"></i> Calendar</a>
                </li>
                <li>
                    <a href="stats.html"><i class="icon-chevron-right"></i> Statistics (Charts)</a>
                </li>
                <li>
                    <a href="form.html"><i class="icon-chevron-right"></i> Forms</a>
                </li>
                <li>
                    <a href="tables.html"><i class="icon-chevron-right"></i> Tables</a>
                </li>
                <li>
                    <a href="buttons.html"><i class="icon-chevron-right"></i> Buttons & Icons</a>
                </li>
                <li>
                    <a href="editors.html"><i class="icon-chevron-right"></i> WYSIWYG Editors</a>
                </li>
                <li>
                    <a href="interface.html"><i class="icon-chevron-right"></i> UI & Interface</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-success pull-right">731</span> Orders</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-success pull-right">812</span> Invoices</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-info pull-right">27</span> Clients</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-info pull-right">1,234</span> Users</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-info pull-right">2,221</span> Messages</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-info pull-right">11</span> Reports</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-important pull-right">83</span> Errors</a>
                </li>
                <li>
                    <a href="#"><span class="badge badge-warning pull-right">4,231</span> Logs</a>
                </li>
            </ul>
        </div>

        <?= $content ?>


        <hr>
        <footer>
            <p>&copy; Vincent Gabriel 2013</p>
        </footer>
    </div>
    <!--/.fluid-container-->
    <script src="/static/iadmin/bootstrap/js/bootstrap.min.js"></script>
    <script src="/static/iadmin/vendors/easypiechart/jquery.easy-pie-chart.js"></script>
    <script src="/static/iadmin/assets/scripts.js"></script>
    <script>
    $(function() {
        // Easy pie charts
        $('.chart').easyPieChart({animate: 1000});
    });
    </script>
</body>

</html>
