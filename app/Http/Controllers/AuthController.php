<?php


namespace App\Http\Controllers;


use App\Helper\CustomController;
use App\Helper\ValidationRules;
use App\Models\Admin;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends CustomController
{
    public function __construct()
    {
        parent::__construct();
    }

    //sign in member
    public function sing_in_member()
    {
        try {
            $email = $this->postField('email');
            $password = $this->postField('password');
            $role = 'member';
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

    //sign up member
    public function sign_up_member()
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($this->request->all(), ValidationRules::REGISTER_RULE);
            $errors_response = $validator->errors()->toArray();
            $specialists = json_decode($this->postField('specialists'));
            $validator_specialists_fails = false;
            if ($specialists === null || !is_array($specialists)) {
                $validator_specialists_fails = true;
            }
            if ($validator->fails() || $validator_specialists_fails) {
                if ($validator_specialists_fails) {
                    $errors_response['specialists'] = ['invalid specialist format'];
                }
                return $this->jsonBadRequestResponse('invalid request', $errors_response);
            }
            $user_data = [
                'username' => $this->postField('username'),
                'email' => $this->postField('email'),
                'password' => $this->postField('password') !== null ? Hash::make($this->postField('password')) : null,
                'roles' => ['member'],
            ];
            $user = User::create($user_data);
            $member_data = [
                'user_id' => $user->id,
                'name' => $this->postField('name'),
                'phone' => $this->postField('phone')
            ];
            Member::create($member_data);
            $user->specialists()->attach($specialists);
            $access_token = $this->generateTokenById($user->id, 'member');
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
