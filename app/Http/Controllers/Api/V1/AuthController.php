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