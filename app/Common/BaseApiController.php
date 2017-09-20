<?php
/**
 * Created by ZhaoJia.
 * Email: youngzhaojia@qq.com
 * Date: 2017/9/20  19:01
 */
namespace App\Common;

use App\Http\Controllers\Controller;
use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Routing\Helpers;

/**
 * Class BaseApiController
 * @package App\Common
 */
class BaseApiController extends Controller
{
    use Helpers;

    protected function errorBadRequest($validator)
    {
        // github like error messages
        // if you don't like this you can use code bellow
        //
        //throw new ValidationHttpException($validator->errors());

        $result = [];
        $messages = $validator->errors()->toArray();

        if ($messages) {
            foreach ($messages as $field => $errors) {
                foreach ($errors as $error) {
                    $result[] = [
                        'field' => $field,
                        'code' => $error,
                    ];
                }
            }
        }

        throw new ValidationHttpException($result);
    }
}