<html>
<head>
    <title><?= $title ?></title>
</head>
<body>
<h1><?= $beforeAction ?></h1>

<p>id:<?= $id ?></p>
<p>name:<?= $name ?></p>
<p>age:<?= $age ?></p>

<p><?= $a1 ?></p>

<p><?= $a2 ?></p>

<p><?= $a3 ?></p>

<p><?= $_SERVER["SERVER_PORT"] ?></p>

<p>cofig</p>
<pre><?= var_dump($config) ?></pre>

<p>constants:</p>
<pre><?= var_dump(get_defined_constants(1)['user']) ?></pre>
<p><a href="<?=\TinyWeb\Request::urlTo($request, ['index', 'index', ''], ['name'=>'abc', 'id'=>123, ])?>">跳转连接测试</a></p>
</body>
</html>