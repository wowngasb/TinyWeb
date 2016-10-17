<html>
<head>
<title><?= $title ?>-123</title>
<?=\TinyWeb\Plugin\Fis::framework('static/js/mod.js'); ?>
<?=\TinyWeb\Plugin\Fis::placeholder('css');?>
<?=\TinyWeb\Plugin\Fis::import('static/js/jquery-1.8.1.min.js'); ?>
</head>
<body>
<h1><?= $beforeAction ?>-[AUTO]</h1>

<p><?= $a1 ?></p>

<p><?= $a2 ?></p>

<p><?= $a3 ?></p>

<p><a href="<?= \TinyWeb\Request::urlTo(['index', 'index', 'admin'], ['name' => 'abc', 'id' => 123,]) ?>">跳转连接测试</a></p>
<p><a href="<?= \TinyWeb\Request::urlTo(['index', 'index', 'develop']) ?>">开发工具</a></p>
<form action="" method="POST">
    args_name：<input type="text" placeholder="" id="args_name">
    args_name：<input type="text" placeholder="" id="args_id">
    <button type="button" id="btn-test">测试</button>
</form>
</body>
<?=TinyWeb\Plugin\Fis::scriptStart()?>
<script type="text/javascript">
$(function(){
    $("#btn-test").click(function(){
        var args_name = $('#args_name').val(),
            args_id = $('#args_id').val(),
            AdminTest = require('static/apiMod/AdminTest');

        AdminTest.testApiFirst({name: args_name, id: args_id}, function (data){
            console.log('ok');
        }, function(data){
            console.log('error');
        });
    });
});
</script>
<?=\TinyWeb\Plugin\Fis::scriptEnd()?>
<?=\TinyWeb\Plugin\Fis::placeholder('js')?>
</html>