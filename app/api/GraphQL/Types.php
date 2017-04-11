<?php
/**
 * Created by table_graphQL.
 * User: Administrator
 * Date: 2017-04-12 01:16:27
 */
namespace app\api\GraphQL;

//import table classes
use app\api\GraphQL\Type\BasicUser;
use app\api\GraphQL\Type\RbacPermission;
use app\api\GraphQL\Type\RbacRole;
use app\api\GraphQL\Type\RbacRolePermission;
use app\api\GraphQL\Type\RbacUserRole;
use app\api\GraphQL\Type\RecordConsoleLogin;
use app\api\GraphQL\Type\VlssApp;
use app\api\GraphQL\Type\VlssSceneGroup;
use app\api\GraphQL\Type\VlssSceneItem;
use app\api\GraphQL\Type\VlssSceneTemplate;

//import state enum classes
use app\api\GraphQL\Enum\BasicUserStateEnum;
use app\api\GraphQL\Enum\RbacRolePermissionStateEnum;
use app\api\GraphQL\Enum\VlssAppStateEnum;
use app\api\GraphQL\Enum\VlssSceneGroupStateEnum;
use app\api\GraphQL\Enum\VlssSceneItemStateEnum;
use app\api\GraphQL\Enum\VlssSceneTemplateStateEnum;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

/**
 * Class Types
 *
 * Acts as a registry and factory for types.
 *
 * @package app\api\GraphQL
 */
class Types
{

    ####################################
    ##########  table types  ##########
    ####################################

    private static $_mBasicUser = null;

    /**
     * @return BasicUser
     */
    public static function BasicUser()
    {
        return self::$_mBasicUser ?: (self::$_mBasicUser = new BasicUser());
    }

    private static $_mRbacPermission = null;

    /**
     * @return RbacPermission
     */
    public static function RbacPermission()
    {
        return self::$_mRbacPermission ?: (self::$_mRbacPermission = new RbacPermission());
    }

    private static $_mRbacRole = null;

    /**
     * @return RbacRole
     */
    public static function RbacRole()
    {
        return self::$_mRbacRole ?: (self::$_mRbacRole = new RbacRole());
    }

    private static $_mRbacRolePermission = null;

    /**
     * @return RbacRolePermission
     */
    public static function RbacRolePermission()
    {
        return self::$_mRbacRolePermission ?: (self::$_mRbacRolePermission = new RbacRolePermission());
    }

    private static $_mRbacUserRole = null;

    /**
     * @return RbacUserRole
     */
    public static function RbacUserRole()
    {
        return self::$_mRbacUserRole ?: (self::$_mRbacUserRole = new RbacUserRole());
    }

    private static $_mRecordConsoleLogin = null;

    /**
     * @return RecordConsoleLogin
     */
    public static function RecordConsoleLogin()
    {
        return self::$_mRecordConsoleLogin ?: (self::$_mRecordConsoleLogin = new RecordConsoleLogin());
    }

    private static $_mVlssApp = null;

    /**
     * @return VlssApp
     */
    public static function VlssApp()
    {
        return self::$_mVlssApp ?: (self::$_mVlssApp = new VlssApp());
    }

    private static $_mVlssSceneGroup = null;

    /**
     * @return VlssSceneGroup
     */
    public static function VlssSceneGroup()
    {
        return self::$_mVlssSceneGroup ?: (self::$_mVlssSceneGroup = new VlssSceneGroup());
    }

    private static $_mVlssSceneItem = null;

    /**
     * @return VlssSceneItem
     */
    public static function VlssSceneItem()
    {
        return self::$_mVlssSceneItem ?: (self::$_mVlssSceneItem = new VlssSceneItem());
    }

    private static $_mVlssSceneTemplate = null;

    /**
     * @return VlssSceneTemplate
     */
    public static function VlssSceneTemplate()
    {
        return self::$_mVlssSceneTemplate ?: (self::$_mVlssSceneTemplate = new VlssSceneTemplate());
    }

    ####################################
    ######### state enum types #########
    ####################################

    private static $_mBasicUserStateEnum = null;

    /**
     * @return BasicUserStateEnum
     */
    public static function BasicUserStateEnum()
    {
        return self::$_mBasicUserStateEnum ?: (self::$_mBasicUserStateEnum = new BasicUserStateEnum());
    }

    private static $_mRbacRolePermissionStateEnum = null;

    /**
     * @return RbacRolePermissionStateEnum
     */
    public static function RbacRolePermissionStateEnum()
    {
        return self::$_mRbacRolePermissionStateEnum ?: (self::$_mRbacRolePermissionStateEnum = new RbacRolePermissionStateEnum());
    }

    private static $_mVlssAppStateEnum = null;

    /**
     * @return VlssAppStateEnum
     */
    public static function VlssAppStateEnum()
    {
        return self::$_mVlssAppStateEnum ?: (self::$_mVlssAppStateEnum = new VlssAppStateEnum());
    }

    private static $_mVlssSceneGroupStateEnum = null;

    /**
     * @return VlssSceneGroupStateEnum
     */
    public static function VlssSceneGroupStateEnum()
    {
        return self::$_mVlssSceneGroupStateEnum ?: (self::$_mVlssSceneGroupStateEnum = new VlssSceneGroupStateEnum());
    }

    private static $_mVlssSceneItemStateEnum = null;

    /**
     * @return VlssSceneItemStateEnum
     */
    public static function VlssSceneItemStateEnum()
    {
        return self::$_mVlssSceneItemStateEnum ?: (self::$_mVlssSceneItemStateEnum = new VlssSceneItemStateEnum());
    }

    private static $_mVlssSceneTemplateStateEnum = null;

    /**
     * @return VlssSceneTemplateStateEnum
     */
    public static function VlssSceneTemplateStateEnum()
    {
        return self::$_mVlssSceneTemplateStateEnum ?: (self::$_mVlssSceneTemplateStateEnum = new VlssSceneTemplateStateEnum());
    }

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