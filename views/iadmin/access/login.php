<!DOCTYPE html>
<html>
  <head>
    <title>后台登陆</title>
    <!-- Bootstrap -->
    <link href="/static/iadmin/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/static/iadmin/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="/static/iadmin/assets/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="/static/iadmin/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  </head>
  <body id="login">
    <div class="container">
    <?php
      $form = \yii\widgets\ActiveForm::begin([
          'id' => 'loginForm',
          'options' => [
              'class' => 'form-signin',      
          ],
      ]);

    ?>
        <h2 class="form-signin-heading">后台登陆</h2>
        <input type="text"  name="WAdmin[username]" id="username" class="input-block-level" placeholder="用户名">
        <input type="password" name="WAdmin[password]" id="password"  class="input-block-level" placeholder="密码">
        <?= $form->field($model, 'verifycode')->widget(\yii\captcha\Captcha::className(), [
            'options' => ['placeholder'=>'验证码'],
            'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
        ])->label('') ?>
        <label class="checkbox">
          <input type="checkbox" name="WAdmin[reme]" value="1"> 记住我
        </label>
        <button class="btn btn-large btn-primary" id="loginBtn" type="button">登陆</button>
    <?php \yii\widgets\ActiveForm::end(); ?>
    </div> <!-- /container -->
    <script src="/static/iadmin/vendors/jquery-1.9.1.min.js"></script>
    <script src="/static/iadmin/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(function () {
      $('#loginBtn').on('click', function () {
          $.post('<?= \Yii::$app->urlManager->createUrl("iadmin/access/login") ?>', $('#loginForm').serialize(), function (jsonStr) {
              // alert(jsonStr);
              var jsonStr = $.parseJSON(jsonStr);

              if(jsonStr['status'] < 0) {
                alert(jsonStr['msg']);
              } else {
                alert(jsonStr['msg']);
                window.location.href = '<?= \Yii::$app->urlManager->createUrl("iadmin/admin/index") ?>';
              }
          });
      });

      $('#wadmin-verifycode-image').on('click', function () {
          $(this).prop('src', '/web/index.php?r=site/captcha&v=' + Math.random());
      });
    });
    </script>
  </body>
</html>