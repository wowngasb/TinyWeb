<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $tool_title ?></title>
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/dpl.css" rel="stylesheet">
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/bui.css" rel="stylesheet">
    <link href="http://g.tbcdn.cn/fi/bui/css/layout-min.css" rel="stylesheet">
    <link href="http://help.aodianyun.com/css/tomorrow.min.css" rel="stylesheet">
    <script src="http://g.tbcdn.cn/fi/bui/jquery-1.8.1.min.js"></script>
    <script src="http://g.alicdn.com/bui/seajs/2.3.0/sea.js"></script>
    <script src="http://g.alicdn.com/bui/bui/1.1.21/config.js"></script>
    <script src="http://help.aodianyun.com/js/highlight.min.js"></script>
    <script src="http://help.aodianyun.com/js/highlight.min.js"></script>
    <script src="http://cdn.bootcss.com/json2/20150503/json2.min.js"></script>
</head>
<body>
<div class="demo-content">
    <h2 class="tip-title">常用脚本：</h2>
    <div class="control-group">
        <a class="button button-success array_btn" target="new" href="<?=\TinyWeb\Request::urlTo(['Deploy', 'runCrontab', 'develop'], ['script'=>'PerMinuteTask.php'])?>">每分钟任务</a>
        <a class="button button-success array_btn" target="new" href="<?=\TinyWeb\Request::urlTo(['Deploy', 'runCrontab', 'develop'], ['script'=>'PerDayTask.php'])?>">每日任务</a>
        <a class="button button-success array_btn" target="new" href="<?=\TinyWeb\Request::urlTo(['Deploy', 'buildApiModJs', 'develop'], ['dev_debug'=>1,])?>">编译API</a>
        <a class="button button-success array_btn" target="new" href="<?=\TinyWeb\Request::urlTo(['Deploy', 'phpInfo', 'develop'])?>">phpInfo</a>
        <a class="button button-success array_btn" target="new" href="<?= SYSTEM_HOST . 'index.php' ?>">首页</a>
    </div>
</div>
<div class="demo-content">
    <h2 class="tip-title">API调试工具</h2>
    <form id="J_Form" action="" class="form-horizontal">
        <div class="control-group">
            <label class="control-label">hook：</label>
            <div class="controls bui-form-group-input" data-type="custom1">
                <input type="text" id="hook_id">（以此hook_id身份执行API）
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">选择API：</label>
            <div class="controls bui-form-group-select" data-type="custom1">
                <select id="api_class" name="g" class="input-normal" value="">
                    <option>请选择</option>
                </select>&nbsp;&nbsp;
                <select id="api_method" name="h" class="input-normal">
                    <option>请选择</option>
                </select>
            </div>
        </div>
    </form>
    <form id="API_Form" action="" class="form-horizontal">
        <h2 class="tip-title">参数列表</h2>
        <div class="row">
            <div class="actions-bar span10 api-div1">
                <div class="form-actions" id="api_ajax_form">
                </div>
                <div class="form-actions offset3">
                    <button id="api_ajax_btn" type="button" class="button button-primary">提交</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="reset" class="button">重置</button>
                </div>
            </div>
            <div class="actions-bar span14 api-div2">
                <pre id="api-pre"></pre>
            </div>
        </div>
    </form>
    <div class="log well" id="status_log"></div>
</div>
<!-- script start -->
<script type="text/javascript">
BUI.use('bui/form', function (Form) {
    //定义级联下拉框的类型
    BUI.Form.Group.Select.addType('custom1', {
        url: "<?=\TinyWeb\Request::urlTo(['syslog', 'getmethodlist', 'develop'])?>",
        root: {
            id: '0',
            children: <?=$json_api_list?>
        }
    });
    var form = new Form.Form({
        srcNode: '#J_Form'
    }).render();

    $('#api_method').change(function () {
        var cls = $('#api_class').val(),
            method = $('#api_method').val();
        var api_url = "<?=\TinyWeb\Request::urlTo(['syslog', 'getparamlist', 'develop'])?>";
        $.ajax({
            type: "GET",
            url: api_url,
            data: {cls: cls, method: method},
            dataType: "json",
            success: function (data) {
                $('#api_ajax_form').html('');
                $('#api-pre').html('');
                if ( data.error && data.errno!=0 ) {
                    console.log(data.error);
                    return;
                }
                $('#api-pre').html(data.Note);
                for (var idx in data.Args) {
                    var item = data.Args[idx];
                    var must_tag = item.is_optional ? '' : '<s>*</s>';
                    var array_style = item.is_array ? '' : 'style="display:none;"';
                    item.name = item.is_array ? item.name + '[ ]' : item.name;
                    var html = '<div class="control-group"><label class="control-label">' + must_tag + item.name + '：</label><div class="controls"><input name="' + item.name + '" type="text" value="' + item.optional + '" class="input-normal" data-rules="{required : true}"><span ' + array_style + ' class="args_btn"><span class="x-icon x-icon-success array_btn">+</span><span class="x-icon x-icon-error array_btn">×</span></span></div></div>';
                    $('#api_ajax_form').append(html);
                }
                setTimeout(array_btn_set, 200);
            }
        });
    });

    $('#api_ajax_btn').on('click', function () {
        var cls = $('#api_class').val(),
            method = $('#api_method').val();
        var api_url = '/api/' + cls + '/' + method;
        var json_data = $("#API_Form").serializeJson();
        var hook_id = $('#hook_id').val();
        if (hook_id) {
            json_data.hook_id = hook_id;
        }
        for (var key in json_data) {
            var item = json_data[key];
            if (item == 'null') {
                delete json_data[key];
            }
        }
        var start_time = new Date().getTime();
        if (typeof CSRF_TOKEN != "undefined" && CSRF_TOKEN) {
            json_data.csrf = CSRF_TOKEN;
        }
        $.ajax({
            type: "POST",
            url: api_url,
            data: json_data,
            dataType: "json",
            success: function (data) {
                var use_time = Math.round((new Date().getTime() - start_time));
                if (data.errno == 0 || !data.error) {
                    api_log(cls, method, 'INFO', use_time, json_data, data);
                } else {
                    api_log(cls, method, 'ERROR', use_time, json_data, data);
                }
                var json_code = "<pre><code>" + JSON.stringify(data, null, 4) + "</code></pre>";
                $('#status_log').html(json_code);
                setTimeout(hljs_code, 200);
            }
        });
    });
});

function api_log(cls, func, tag, use_time, args, data) {
    delete args.csrf;
    var _log_func_dict = (typeof console != "undefined" && typeof console.info == "function" && typeof console.warn == "function") ? {
        INFO: console.info.bind(console),
        ERROR: console.warn.bind(console)
    } : {};
    ;
    var f = _log_func_dict[tag];
    f && f(formatDateNow(), '[' + tag + '] ' + cls + '.' + func + '(' + use_time + 'ms)', 'args:', args, 'data:', data);
}

function formatDateNow() {
    var now = new Date(new Date().getTime());
    var year = now.getFullYear();
    var month = now.getMonth() + 1;
    var date = now.getDate();
    var hour = now.getHours();
    var minute = now.getMinutes();
    if (minute < 10) {
        minute = '0' + minute.toString();
    }
    var seconds = now.getSeconds()
    if (seconds < 10) {
        seconds = '0' + seconds.toString();
    }
    return year + "-" + month + "-" + date + " " + hour + ":" + minute + ":" + seconds;
}

function hljs_code() {
    $('pre code').each(function (i, block) {
        hljs.highlightBlock(block);
    });
}

function array_btn_set() {
    $('.x-icon-success').on('click', function () {
        var item = $(this).closest('.control-group');
        item.after(item.clone(true));
    });

    $('.x-icon-error').on('click', function () {
        var item = $(this).closest('.control-group');
        item.remove();
    });
}

(function ($) {
    $.fn.serializeJson = function () {
        var serializeObj = {};
        var array = this.serializeArray();
        var str = this.serialize();
        $(array).each(function () {
            if (serializeObj[this.name]) {
                if ($.isArray(serializeObj[this.name])) {
                    serializeObj[this.name].push(this.value);
                } else {
                    serializeObj[this.name] = [serializeObj[this.name], this.value];
                }
            } else {
                serializeObj[this.name] = this.value;
            }
        });
        return serializeObj;
    };
})(jQuery);
</script>
<!-- script end -->
</body>
</html>