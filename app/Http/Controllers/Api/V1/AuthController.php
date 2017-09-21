<?php
/**
 * Created by ZhaoJia.
 * Email: youngzhaojia@qq.com
 * Date: 2017/9/20  19:00
 */

namespace App\Http\Controllers\Api\V1;

use App\Common\BaseApiController;
use App\Http\Controllers\Api\Transformers\UserTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api\V1
 */
class AuthController extends BaseApiController
{
    /**
     * @api {post} auth/login 登陆
     * @apiDescription 登陆返回 token
     * @apiGroup Auth
     * @apiPermission none
     * @apiParam {Email} email     邮箱
     * @apiParam {String} password  密码
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 Created
     *     {
     *          "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9kZXYucGx1dG8ubWUvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwOTM2NzIsImV4cCI6MTUwNDY5ODQ3MiwibmJmIjoxNTA0MDkzNjcyLCJqdGkiOiJWd3RYVTZ6SXg2R29JbFpTIn0.3vSW33vTPBkxk7eR_54PUk9f76lD-qTOQyFZLyNda94",
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401
     *     {
     *       "error": "账号或密码错误"
     *     }
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => '账号或密码错误'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => '不能创建token'], 500);
        }

        // all good so return the token
        return $this->response->array(compact('token'));
    }

    /**
     * @api {post} auth/register 注册
     * @apiDescription 注册返回 token
     * @apiGroup Auth
     * @apiPermission none
     * @apiParam {Email} email     邮箱
     * @apiParam {String} name     用户名
     * @apiParam {Password} password  密码
     * @apiParam {Password} password_confirmation  重复密码
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 Created
     *     {
     *          "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9kZXYucGx1dG8ubWUvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE1MDQwOTM2NzIsImV4cCI6MTUwNDY5ODQ3MiwibmJmIjoxNTA0MDkzNjcyLCJqdGkiOiJWd3RYVTZ6SXg2R29JbFpTIn0.3vSW33vTPBkxk7eR_54PUk9f76lD-qTOQyFZLyNda94",
     *     }
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422
     *     {
     *       "error": "两次输入的密码不一致"
     *     }
     */
    public function register(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ];
        $user = User::create($attributes);

        $token = JWTAuth::fromUser($user);
        return $this->response->array(compact('token'));
    }

    public function logout()
    {
        JWTAuth::refresh();
        return $this->response->noContent();
    }

    public function detail()
    {
        $user = $this->user();
        return $this->response->item($user, new UserTransformer());
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string|max:50',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $user = $this->user();
        $attributes = array_filter($request->only('name'));
        if ($attributes) {
            $user->update($attributes);
        }
        return $this->response->item($user, new UserTransformer());
    }

    public function update_password(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $user = $this->user();
        $user->update([
            'password' => bcrypt($data['password']),
        ]);

        JWTAuth::refresh();
        return $this->response->noContent();
    }
}