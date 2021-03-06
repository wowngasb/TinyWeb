
//模块化方案，本项目选中CommonJS方案(同样支持异步加载哈)
fis.hook('module', {
  mode: 'commonjs'
});

fis.hook('commonjs', {
    extList: ['.js', '.jsx', '.es', '.ts', '.tsx']
})

fis.unhook('components'); // fis3 自带的不是 npm 所以，先禁用它
fis.hook('node_modules'); // 启用 node_modules 组件支持。

fis.match('/{node_modules}/**.js', {
    isMod: true,
    useSameNameRequire: true
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
fis.match('/robots.txt', {
    release: '/robots.txt'
});

//资源配置表	
fis.match('/map.json',{
    release: '/tpl/map.json'
});

fis.match("/static/**",{
    release: '/$0'
});

fis.match("/static/apiMod/**",{
    isMod : true
});

fis.match("/widget/**",{
    isMod : true,
    release: '/static/$0'
});

fis.match("/{views,widget}/**.php",{
    isMod : true,
    isHtmlLike : true,
    url: '$&', //此处注意，php加载的时候已带tpl目录，所以路径得修正
    release: '/tpl/$&'
});

fis.match("*/views/**.php",{
    isMod : true,
    isHtmlLike : true,
    url: '$&', //此处注意，php加载的时候已带tpl目录，所以路径得修正
    release: '/tpl/$&'
});

//开启组件同名依赖
fis.match('*.{html,js,php}', {
  useSameNameRequire: true
});

fis.match('*', {
  deploy: fis.plugin('local-deliver', {
    to: './../public'
  })
})

/*
fis.media('product').match('*', {
    deploy: fis.plugin('http-push', {
        receiver: 'http://product.org/fis-receiver.php?media=product&token=product_key)',
        to: '/usr/local/tengine/html' // 注意这个是指的是测试机器的路径，而非本地机器
    })
});

fis.media('product').match('*.{js, css, png, jpg, gif, svg}', {
    domain: 'http://cdn.product.com',
});
*/

fis.media('product').match('*.js', {
    // fis-optimizer-uglify-js 插件进行压缩，已内置
    optimizer: fis.plugin('uglify-js')
});

fis.media('product').match('*.css', {
    // fis-optimizer-clean-css 插件进行压缩，已内置
    optimizer: fis.plugin('clean-css')
});

fis.media('product').match('*.png', {
    // fis-optimizer-png-compressor 插件进行压缩，已内置
    optimizer: fis.plugin('png-compressor')
});

fis.media('product').match('*.{js, css, png, jpg, gif, svg}', {
    useHash: true
});

