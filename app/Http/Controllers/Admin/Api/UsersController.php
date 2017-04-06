<?php

namespace App\Http\Controllers\Admin\Api;

use App\Entities\Role;
use App\Entities\User;
use App\Http\Requests\Request;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Transformers\RoleTransformer;
use App\Transformers\UserTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Hash;
use Auth;

class UsersController extends ApiController
{
    /**
     * 当前登录的用户信息
     * @return \Dingo\Api\Http\Response
     */
    public function me()
    {
        return $this->response->item(Auth::user(), new UserTransformer());
    }

    /**
     * 用户列表
     * @return \Dingo\Api\Http\Response
     */
    public function lists()
    {
        $users = User::with('roles')->withSimpleSearch()
            ->withSort()
            ->recent()
            ->paginate($this->perPage());
        return $this->response->paginator($users, new UserTransformer())
            ->setMeta(User::getAllowSortFieldsMeta() + User::getAllowSearchFieldsMeta());
    }

    /**
     * 显示指定用户信息
     * @param User $user
     * @return \Dingo\Api\Http\Response
     */
    public function show(User $user)
    {
        return $this->response->item($user, new UserTransformer());
    }

    /**
     * 删除指定用户
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy($id)
    {
        if (!User::destroy(intval($id))) {
            //todo 国际化
            throw new NotFoundHttpException('该用户不存在');
        }
        return $this->response->noContent();
    }

    /**
     * 更新指定用户
     * @param User $user
     * @param UserUpdateRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function update(User $user, UserUpdateRequest $request)
    {
        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $request->performUpdate($user);
        if(!empty($data['role_ids'])){
            $roleIds = Role::findOrFail($data['role_ids'])->pluck('id');
            $user->save($roleIds);
        }
        return $this->response->noContent();
    }

    /**
     * 创建用户
     * @param UserCreateRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->all();
        if(empty($data['password'])){
            unset($data['password']);
        }else{
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);
        if(!empty($data['role_ids'])){
            $roleIds = Role::findOrFail($data['role_ids'])->pluck('id');
            $user->roles()->sync($roleIds);
        }
        return $this->response->noContent();
    }

    /**
     * 获取当前用户的角色
     * @param User $user
     * @return \Dingo\Api\Http\Response
     */
    public function roles(User $user)
    {
        return $this->response->collection($user->roles, new RoleTransformer());
    }

    /**
     * 批量移动
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function moveUsers2Roles(Request $request)
    {
        $this->validate($request, [
            'user_ids' => 'int_array',
            'role_ids' => 'int_array',
        ]);
        $userIds = $request->get('user_ids');
        $roleIds = $request->get('role_ids');
        User::moveUsers2Roles($userIds, $roleIds);
        return $this->response->noContent();
    }
}