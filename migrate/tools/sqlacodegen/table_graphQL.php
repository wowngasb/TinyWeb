<?php

echo build_types(load_json(__DIR__ . '/table.json'));

function load_json($file)
{
    $file_str = file_get_contents($file);
    return !empty($file_str) ? json_decode($file_str, true) : '';
}
function _build_use($namespace, $table_data){
    $use_str = "\n";
    foreach ($table_data as $table) {
        $use_str .= "use {$namespace}\\" . "{$table['name']}Type\n";
    }
    return $use_str;
}

function build_types($table_data){
    $author = 'Administrator';
    $date = date('Y-m-d H:i:s');
    $namespace = 'app\api\GraphQL';
    $use_str = _build_use($namespace, $table_data);
    $php_str = <<<TYPEOTHER
<?php
/**
 * Created by table_graphQL.
 * User: {$author}
 * Date: {$date}
 */
namespace {$namespace};
{$use_str}
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * Class Types
 *
 * Acts as a registry and factory for your types.
 *
 * As simplistic as possible for the sake of clarity of this example.
 * Your own may be more dynamic (or even code-generated).
 *
 * @package GraphQL\Examples\Blog
 */
class Types
{
TYPEOTHER;

    foreach ($table_data as $item) {
        
    }

    $php_str .=  <<<'EOT'

    // Let's add internal types as well for consistent experience

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