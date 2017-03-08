<?php
namespace app\api\GraphQL\Vlss\Type;

use app\api\GraphQL\Vlss\Data\App;
use app\api\GraphQL\Vlss\Data\SceneGroup;
use app\api\GraphQL\Vlss\Data\SceneTemplate;

use GraphQL\Type\Definition\ObjectType;

class AppType extends ObjectType
{
    public function __construct()
    {
        $config = [
        ];
        parent::__construct($config);
    }

    public function activeGroup(App $app)
    {
        return SceneGroup::getItem($app->active_group_id);
    }

    public function activeTemplate(App $app)
    {
        return SceneTemplate::getItem($app->active_template_id);
    }
}
