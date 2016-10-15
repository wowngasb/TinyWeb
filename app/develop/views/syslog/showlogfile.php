<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$tool_title?></title>
    <script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
    <style type="text/css">
        .log_pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-left: 20px;
            margin-right: 10px;
            line-height: 20px;
        }
        .log_idx{
            padding:3px 3px 3px 3px;color:white;background:#887ddd;
            border-radius: 4px;
        }
        .log_green{
            padding:3px;color:white;background:#8cc540;
            border-radius: 4px;
        }
        .log_blue{
            padding:3px;color:white;background:#157FCC;
            border-radius: 4px;
        }
        .log_yellow{
            padding:3px;color:white;background:#ff8345;
            border-radius: 4px;
        }
        .log_red{
            padding:3px;color:white;background:red;
            border-radius: 4px;
        }
        .log_black{
            padding:3px;color:white;background:black;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<pre class="log_pre" id="log_code"></pre>
<script type="text/javascript">
var START_TIME = new Date().getTime();
var SCROLL_TO = '<?=$scroll_to?>';
var FILE_STR = <?=json_encode( htmlspecialchars($file_str) )?>;

$(function() {
    console.info('before split:', Math.round( (new Date().getTime() - START_TIME) ) , 'ms');
    var str_array = FILE_STR.split("\n");
    console.info('after split:', Math.round( (new Date().getTime() - START_TIME) ) , 'ms');
    var idx = 0;
    var tmp_str = '';
    for (var i = 0; i < str_array.length; i++) {
        idx = i + 1;
        tmp_str = str_array[i];
        if (tmp_str.indexOf('[DEBUG]') >= 0) {
            str_array[i] = '<b class="log_idx">' + idx + '</b>&nbsp;' + tmp_str.replace('[DEBUG]', '[<b class="log_green">DEBUG</b>]');
        } else if (tmp_str.indexOf('[INFO]') >= 0) {
            str_array[i] = '<b class="log_idx">' + idx + '</b>&nbsp;' + tmp_str.replace('[INFO]', '[<b class="log_blue">INFO</b>]');
        } else if (tmp_str.indexOf('[WARN]') >= 0) {
            str_array[i] = '<b class="log_idx">' + idx + '</b>&nbsp;' + tmp_str.replace('[WARN]', '[<b class="log_yellow">WARN</b>]');
        } else if (tmp_str.indexOf('[ERROR]') >= 0) {
            str_array[i] = '<b class="log_idx">' + idx + '</b>&nbsp;' + tmp_str.replace('[ERROR]', '[<b class="log_red">ERROR</b>]');
        } else if (tmp_str.indexOf('[FATAL]') >= 0) {
            str_array[i] = '<b class="log_idx">' + idx + '</b>&nbsp;' + tmp_str.replace('[FATAL]', '[<b class="log_black">FATAL</b>]');
        } else {
            str_array[i] = tmp_str;
        }
    }
    console.info('after for:', Math.round( (new Date().getTime() - START_TIME) ) , 'ms');
    $('#log_code').html( str_array.join("\n") );
    console.info('add html:', Math.round( (new Date().getTime() - START_TIME) ) , 'ms');
    if (SCROLL_TO == 'end') {
        var h = $(document).height() - $(window).height();
        $(document).scrollTop(h);
    }
});
</script>
</body>
</html>