<?php
require(dirname(__DIR__) . "/config/config.php");

$table_data = load_json(__DIR__ . '/table.json');  //读取数据库描述文件

$out_dir = __DIR__ . '/GraphQL/';
if (!is_dir($out_dir)) {
    mkdir($out_dir, 777);
}
$enum_dir = $out_dir . 'Enum/';
if (!is_dir($enum_dir)) {
    mkdir($enum_dir, 777);
}
$table_dir = $out_dir . 'Type/';
if (!is_dir($table_dir)) {
    mkdir($table_dir, 777);
}
//新建临时目录

$author = 'Administrator';
$date = date('Y-m-d H:i:s');
$file_header = <<<EOT
<?php
/**
 * Created by table_graphQL.
 * User: {$author}
 * Date: {$date}
 */
EOT;


$namespace = 'app\api\GraphQL';  //初始变量设置
$table_class_dict = _table_class_dict($table_data);
$state_list = _table_filter($table_data, function ($table) {
    foreach ($table['columns'] as $column) {
        if ($column['name'] == 'state') {
            return true;
        }
    }
    return false;
});
$state_enum_dict = _table_class_dict($state_list, 'StateEnum');


$_types_file = build_types($file_header, $namespace, $table_class_dict, $state_enum_dict);
file_put_contents($out_dir . 'Types.php', $_types_file);


foreach ($state_enum_dict as $class_name => $table) {
    $description = "{$table['table_name']} 数据表 state 字段 表示状态.";
    $_enum_file = build_enum_types($file_header, $namespace . '\Enum', $class_name, $table, "'{$description}'");
    file_put_contents($enum_dir . "{$class_name}.php", $_enum_file);
}

foreach ($table_class_dict as $class_name => $table) {
    $description = "{$table['table_name']} 数据表 {$table['doc']}.";
    $_table_file = build_table_types($file_header, $namespace . '\Type', $class_name, $table, "'{$description}'");
    file_put_contents($table_dir . "{$class_name}.php", $_table_file);
}

function build_table_types($file_header, $namespace, $class_name, array $table, $description = "''")
{
    $primary_col = _col_find($table, function ($col) {
        return isset($col['primary_key']) && $col['primary_key'] === true;
    });
    $var_str = '$config';
    $var_orm = '$_orm_config';
    $col_list = build_table_cols($table);
    $php_str = <<<EOT
{$file_header}
namespace {$namespace};

use app\api\GraphQL\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

use TinyWeb\Application;
use TinyWeb\Func;
use TinyWeb\OrmQuery\OrmConfig;
use TinyWeb\Traits\OrmTrait;


/**
 * Class {$class_name}
 * @package {$namespace}
 */
class {$class_name} extends ObjectType
{
    use OrmTrait;

    public function __construct()
    {
        {$var_str} = [
            'description' => {$description},
            'fields' => []
        ];
EOT;
    foreach ($col_list as $item) {
        $php_str .= <<<EOT

        {$var_str}['fields']['{$item['key']}'] = [
            'type' => {$item['type']},
            'description' => {$item['description']},
        ];
EOT;

    }
    $php_str .= <<<EOT

        {$var_str}['resolveField'] =
EOT;
    $php_str .= <<<'EOT'
 function($value, $args, $context, ResolveInfo $info) {
            if (method_exists($this, $info->fieldName)) {
                return $this->{$info->fieldName}($value, $args, $context, $info);
            } else {
                return $value->{$info->fieldName};
            }
        };
EOT;
    $php_str .= <<<EOT

        parent::__construct({$var_str});
    }

    /**
     * 使用这个特性的子类必须 实现这个方法 返回特定格式的数组 表示数据表的配置
     * @return OrmConfig
     */
    protected static function getOrmConfig()
    {
        if (is_null(static::{$var_orm})) {
            static::{$var_orm} = new OrmConfig(Application::getInstance()->getEnv('ENV_MYSQL_DB'), Func::class2table(__METHOD__), '{$primary_col['name']}', 300, 5000);
        }
        return static::{$var_orm};
    }
}
EOT;
    return $php_str;
}

function build_table_cols(array $table)
{
    $out = [];
    $table['columns'] = !empty($table['columns']) ? $table['columns'] : [];
    foreach ($table['columns'] as $col) {
        $description = "{$table['table_name']} 数据表 {$col['name']} 字段 {$col['doc']}";
        $out[] = [
            'key' => strval($col['name']),
            'type' => build_col_type($col),
            'description' => "'{$description}'",
        ];
    }
    return $out;
}

function build_col_type(array $col)
{
    if (isset($col['primary_key']) && $col['primary_key'] === true) {
        return 'Types::nonNull(Types::id())';
    } else if (_cmp($col['type'], 'TEXT') || _starts_with($col['type'], 'VARCHAR')) {
        return !empty($col['nullable']) ? 'Types::nonNull(Types::string())' : 'Types::string()';
    } else if (_cmp($col['type'], 'SMALLINT') || _cmp($col['type'], 'INTEGER') || _cmp($col['type'], 'BIGINT')) {
        return !empty($col['nullable']) ? 'Types::nonNull(Types::int())' : 'Types::int()';
    } else if(_cmp($col['type'], 'DATETIME')){
        return !empty($col['nullable']) ? 'Types::nonNull(Types::string())' : 'Types::string()';
    } else {
    return !empty($col['nullable']) ? 'Types::nonNull(Types::string())' : 'Types::string()';
}
}

function _cmp($str1, $str2)
{
    return strtolower(trim($str1)) === strtolower(trim($str2));
}

function _starts_with($haystack, $needle)
{
    return _cmp(substr($haystack, 0, strlen($needle)), $haystack);
}

function _ends_with($haystack, $needle)
{
    return _cmp(substr($haystack, -strlen($needle)), $haystack);
}

function build_enum_types($file_header, $namespace, $class_name, array $table, $description = "''")
{
    $col = _col_find($table, function ($col) {
        return $col['name'] == 'state';
    });
    $doc_str = !empty($col['doc']) ? $col['doc'] : '0@UNDEFINED#未定义';
    $enum_list = _split_enum($doc_str, ';', '@', '#');
    $var_str = '$config';
    $php_str = <<<EOT
{$file_header}
namespace {$namespace};

use GraphQL\Type\Definition\EnumType;

/**
 * Class {$class_name}
 * @package {$namespace}
 */
class {$class_name} extends EnumType
{

    public function __construct()
    {
        {$var_str} = [
            'description' => {$description},
            'values' => []
        ];
EOT;
    foreach ($enum_list as $item) {
        $php_str .= <<<EOT

        {$var_str}['values']['{$item['key']}'] = ['value' => {$item['value']}, 'description' => {$item['description']}];
EOT;
    }
    $php_str .= <<<EOT

        parent::__construct({$var_str});
    }

}
EOT;
    return $php_str;
}

function _split_enum($doc_str, $split = ';', $v_tag = '@', $d_tag = '#')
{
    $out = [];
    $enum_list = explode($split, $doc_str);
    \app\Bootstrap::_D($enum_list, 'dawd');
    foreach ($enum_list as $enum) {
        $value = trim(explode($v_tag, $enum)[0]);
        $key = trim(explode($d_tag, substr($enum, strpos($enum, $v_tag) + 1))[0]);
        $description = substr($enum, strpos($enum, $d_tag) + 1);
        $out[] = [
            'key' => strval($key),
            'value' => is_numeric($value) ? $value : "'{$value}'",
            'description' => "'{$description}'",
        ];
    }
    return $out;
}

function load_json($file)
{
    $file_str = file_get_contents($file);
    return !empty($file_str) ? json_decode($file_str, true) : '';
}

function _table_class_name($table_name)
{
    $table_list = explode('_', $table_name);
    $out = '';
    foreach ($table_list as $item) {
        $out .= ucfirst($item);
    }
    return $out;
}

function _table_class_dict(array $table_data, $ext = '')
{
    $class_list = [];
    foreach ($table_data as $table) {
        $key = _table_class_name($table['table_name']) . $ext;
        $class_list[$key] = $table;
    }
    return $class_list;
}

function _table_filter(array $table_data, callable $filter)
{
    $list = [];
    foreach ($table_data as $table) {
        if ($filter($table)) {
            $list[] = $table;
        }
    }
    return $list;
}

function _col_find(array $table, callable $filter)
{
    $table['columns'] = !empty($table['columns']) ? $table['columns'] : [];
    foreach ($table['columns'] as $col) {
        if ($filter($col)) {
            return $col;
        }
    }
    return [];
}

function build_use($namespace, $cate, array $table_class_dict)
{
    $use_str = "";
    foreach ($table_class_dict as $class_name => $table) {
        $use_str .= "use {$namespace}\\{$cate}\\" . "{$class_name};\n";
    }
    return $use_str;
}

function build_types($file_header, $namespace, array $table_class_dict, array $state_enum_dict)
{
    $use_table = build_use($namespace, 'Type', $table_class_dict);
    $use_state_enum = build_use($namespace, 'Enum', $state_enum_dict);
    $var_name = '$_mQuery';
    $php_str = <<<EOT
{$file_header}
namespace {$namespace};

//import table classes
{$use_table}
//import state enum classes
{$use_state_enum}
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * Class Types
 *
 * Acts as a registry and factory for types.
 *
 * @package {$namespace}
 */
class Types
{

    ####################################
    ########  root query type  #########
    ####################################

    private static {$var_name} = null;
    /**
     * @return QueryType
     */
    public static function query()
    {
        return self::{$var_name} ?: (self::{$var_name} = new QueryType());
    }

    ####################################
    ##########  table types  ##########
    ####################################

EOT;

    foreach ($table_class_dict as $class_name => $table) {
        $var_name = '$_m' . $class_name;
        $php_str .= <<<EOT

    private static {$var_name} = null;

    /**
     * @return {$class_name}
     */
    public static function {$class_name}()
    {
        return self::{$var_name} ?: (self::{$var_name} = new {$class_name}());
    }

EOT;
    }

    $php_str .= <<<EOT

    ####################################
    ######### state enum types #########
    ####################################

EOT;


    foreach ($state_enum_dict as $class_name => $table) {
        $var_name = '$_m' . $class_name;
        $php_str .= <<<EOT

    private static {$var_name} = null;

    /**
     * @return {$class_name}
     */
    public static function {$class_name}()
    {
        return self::{$var_name} ?: (self::{$var_name} = new {$class_name}());
    }

EOT;
    }

    $php_str .= <<<'EOT'

    ####################################
    ########## internal types ##########
    ####################################

    public static function boolean()
    {
        return Type::boolean();
    }

    /**
     * @return \GraphQL\Type\Definition\FloatType
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     */
    public static function id()
    {
        return Type::id();
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param Type $type
     * @return ListOfType
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    /**
     * @param Type $type
     * @return NonNull
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
}
EOT;
    return $php_str;
}