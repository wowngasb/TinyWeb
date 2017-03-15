<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15 0015
 * Time: 17:57
 */

namespace app\api\GraphQL\Union\Hsms;


use app\api\GraphQL\Type\Hsms\VlssHsmsLogoType;
use app\api\GraphQL\Type\Hsms\VlssHsmsSubtitleType;
use app\api\GraphQL\Type\Hsms\VlssHsmsTrailerType;
use app\Bootstrap;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Union\AbstractUnionType;

class VlssHsmsSceneConfigUnion  extends AbstractUnionType
{

    /**
     * @param $object object from resolve function
     *
     * @return AbstractType
     */
    public function resolveType($object)
    {
        Bootstrap::_D((array)$object, 'resolveType');
        if( stripos($object['scene_type'], 'hsms-trailer') !== false ){
            return new VlssHsmsTrailerType();
        } else if( stripos($object['scene_type'], 'hsms-subtitle') !== false ){
            return new VlssHsmsSubtitleType();
        } else if( stripos($object['scene_type'], 'hsms-logo') !== false ){
            return new VlssHsmsLogoType();
        }
        return null;
    }

    /**
     * @return AbstractObjectType[]|AbstractScalarType[]
     */
    public function getTypes()
    {
        return [new VlssHsmsTrailerType(), new VlssHsmsSubtitleType(), new VlssHsmsLogoType()];
    }
}