<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Models\Admin;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        try {
            $email = $this->postField('email');
            $password = $this->postField('password');
            $role = $this->postField('role');
            if (!$this->check_role($role)) {
                return $this->jsonBadRequestResponse('invalid role format');
            }
            $user = User::with($role)
                ->whereJsonContains('roles', $role)
                ->where('email', '=', $email)
                ->first();
            if (!$user) {
                return $this->jsonNotFoundResponse('user not found!');
            }

            if (!Hash::check($password, $user->password)) {
                return $this->jsonBadRequestResponse('password did not match');
            }
            $access_token = $this->generateTokenById($user->id, $role);
            return $this->jsonSuccessResponse('success', [
                'access_token' => $access_token
            ]);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse('internal server error : ' . $e->getMessage());
        }
    }

    public function register()
    {
        DB::beginTransaction();
        try {
            $role = $this->postField('role');
            if (!$this->check_role($role)) {
                return $this->jsonBadRequestResponse('invalid role format');
            }
            $user_data = [
                'username' => $this->postField('username'),
                'email' => $this->postField('email'),
                'password' => $this->postField('password') !== null ? Hash::make($this->postField('password')) : null,
                'roles' => $role === 'admin' ? ['member'] : ['admin'],
            ];
            $user = User::create($user_data);
            if ($role === 'admin') {
                $admin_data = [
                    'user_id' => $user->id,
                    'name' => $this->postField('name')
                ];
                Admin::create($admin_data);
            } else {
                $member_data = [
                    'user_id' => $user->id,
                    'name' => $this->postField('name'),
                    'phone' => $this->postField('phone')
                ];
                Member::create($member_data);
            }

            $access_token = $this->generateTokenById($user->id, $role);
            DB::commit();
            return $this->jsonSuccessResponse('success', [
                'access_token' => $access_token
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($e->getMessage());
        }
    }
}
