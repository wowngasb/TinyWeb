<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 13:29
 */

namespace app\api\GraphQL\Vlss;


use app\api\GraphQL\Vlss\Type\AppType;
use app\api\GraphQL\Vlss\Type\Enum\AppStateEnum;
use app\api\GraphQL\Vlss\Type\Enum\SceneGroupStateEnum;
use app\api\GraphQL\Vlss\Type\Enum\SceneTemplateStateEnum;
use app\api\GraphQL\Vlss\Type\QueryType;
use app\api\GraphQL\Vlss\Type\Scalar\EmailType;
use app\api\GraphQL\Vlss\Type\Scalar\UrlType;
use app\api\GraphQL\Vlss\Type\SceneGroupType;
use app\api\GraphQL\Vlss\Type\SceneItemType;
use app\api\GraphQL\Vlss\Type\SceneTemplateType;
use GraphQL\Examples\Blog\Type\Enum\SceneItemStateEnum;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

class Types
{

    // Object types:
    private static $app;
    private static $sceneGroup;
    private static $sceneItem;
    private static $sceneTemplate;
    private static $query;

    /**
     * @return AppType
     */
    public static function app()
    {
        return self::$app ?: (self::$app = new AppType());
    }

    /**
     * @return SceneGroupType
     */
    public static function sceneGroup()
    {
        return self::$sceneGroup ?: (self::$sceneGroup = new SceneGroupType());
    }

    /**
     * @return SceneItemType
     */
    public static function sceneItem()
    {
        return self::$sceneItem ?: (self::$sceneItem = new SceneItemType());
    }

    /**
     * @return SceneTemplateType
     */
    public static function sceneTemplate()
    {
        return self::$sceneTemplate ?: (self::$sceneTemplate = new SceneTemplateType());
    }

    /**
     * @return QueryType
     */
    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }


    // Enum types
    private static $appStateEnum;
    private static $sceneGroupStateEnum;
    private static $sceneItemStateEnum;
    private static $sceneTemplateStateEnum;

    /**
     * @return AppStateEnum
     */
    public static function appStateEnum()
    {
        return self::$appStateEnum ?: (self::$appStateEnum = new AppStateEnum());
    }

    /**
     * @return SceneGroupStateEnum
     */
    public static function sceneGroupStateEnum()
    {
        return self::$sceneGroupStateEnum ?: (self::$sceneGroupStateEnum = new SceneGroupStateEnum());
    }

    /**
     * @return SceneItemStateEnum
     */
    public static function sceneItemStateEnum()
    {
        return self::$sceneItemStateEnum ?: (self::$sceneItemStateEnum = new SceneItemStateEnum());
    }

    /**
     * @return SceneTemplateStateEnum
     */
    public static function sceneTemplateStateEnum()
    {
        return self::$sceneTemplateStateEnum ?: (self::$sceneTemplateStateEnum = new SceneTemplateStateEnum());
    }

    // Custom Scalar types:
    private static $urlType;
    private static $emailType;

    public static function email()
    {
        return self::$emailType ?: (self::$emailType = EmailType::create());
    }

    /**
     * @return UrlType
     */
    public static function url()
    {
        return self::$urlType ?: (self::$urlType = new UrlType());
    }

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

