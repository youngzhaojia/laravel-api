<?php
/**
 * Created by ZhaoJia.
 * Email: youngzhaojia@qq.com
 * Date: 2017/9/20  19:04
 */

namespace App\Http\Controllers\Api\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer
 * @package app\Http\Controllers\Api\Transformers
 */
class UserTransformer extends TransformerAbstract
{
    protected $authorization;

    public function transform(User $user)
    {
        return $user->attributesToArray();
    }

    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;

        return $this;
    }

    public function includeAuthorization(User $user)
    {
        if (! $this->authorization) {
            return $this->null();
        }

        return $this->item($this->authorization, new AuthorizationTransformer());
    }
}