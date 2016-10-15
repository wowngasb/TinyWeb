
//模块化方案，本项目选中CommonJS方案(同样支持异步加载哈)
fis.hook('module', {
  mode: 'commonJs'
});

fis.match('**', {
    release: false
});

fis.match('/index.php', {
    release: '/index.php'
});

fis.match('/htaccess', {
    release: '/.htaccess'
});

//资源配置表
fis.match('/map.json',{
    release: '/tpl/map.json'
});

fis.match("/static/**",{
    release: '/$0'
});

fis.match("/widget/**",{
    release: '/static/$0'
});

fis.match("/{views,widget}/**.php",{
    isHtmlLike : true,
    release: '/tpl/$0'
});

fis.match("*/views/**.php",{
    isHtmlLike : true,
    release: '/tpl/$0'
});

//开启同名依赖
fis.match('*.{js,php,css}', {
  useSameNameRequire: true
});

