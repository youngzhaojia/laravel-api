# My Laravel Api Template

### Laravel5.4＋JWT＋dingo/API 构建的 RESTful Api
用于个人以及公司项目快速开发的基础项目

## 说明
* Laravel 5.4
* [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
* [dingo/api](https://github.com/dingo/api)
* 跨域 [barryvdh/laravel-cors](https://github.com/barryvdh/laravel-cors) 
* api文档 [apidoc](https://github.com/apidoc/apidoc) 

#### 本项目也可以用于laravel初学者学习

## 截图

![register](https://github.com/youngzhaojia/laravel-api/raw/master/public/images/register.png)
![login](https://github.com/youngzhaojia/laravel-api/raw/master/public/images/login.png)
![detail](https://github.com/youngzhaojia/laravel-api/raw/master/public/images/detail.png)
![apidoc](https://github.com/youngzhaojia/laravel-api/raw/master/public/images/apidoc.png)

## 安装
- git clone 到本地
- 执行 `composer install`,创建好数据库
- 配置 `.env` 中数据库连接信息,没有.env请复制.env.example命名为.env
- 执行 `php artisan key:generate`
- 执行 `php artisan migrate`

## 接口
* `POST`   localhost/api/auth/register
* `POST`   localhost/api/auth/login
* `GET`    localhost/api/auth
* `PATCH`  localhost/api/auth
* `DELETE` localhost/api/auth

## apidoc
```
安装 cnpm install apidoc -g

生成文档 apidoc -i app/Http/Controllers/Api/V1 -o docs/api
```

### apidoc.json示例
```
{
  "name" : "api_v1",
  "version": "1.0.0",
  "title": "pluto",
  "description": "young api docs"
}
```
