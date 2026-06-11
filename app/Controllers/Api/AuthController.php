<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Libraries\JwtLibrary;
use App\Transformers\AuthTransformer;

class AuthController extends BaseApiController
{
    protected UserModel $userModel;
    protected AuthTransformer $authTransformer;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authTransformer = new AuthTransformer();
    }

    public function register()
    {
        $rules = $this->userModel->getValidationRules();
        if (!$this->validate($rules)) {
            return $this->respondWithError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [
            $this->request->getVar('name'),
            $this->request->getVar('email'),
            $this->request->getVar('password')
        ];

        $userId = $this->userModel->insert([
            'name' => $data[0],
            'email' => $data[1],
            'password' => $data[2]
        ]);

        if (!$userId) {
            return $this->respondWithError('Failed to create user', 500);
        }

        $user = $this->userModel->find($userId);

        return $this->respondWithSuccess($this->authTransformer->transform($user), 'User registered successfully', 201);
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithError('Validation failed', 422, $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $this->request->getVar('email'))->first();

        if (!$user || !password_verify($this->request->getVar('password'), $user['password'])) {
            return $this->respondWithError('Invalid Credentials', 401);
        }

        try {
            $token = JwtLibrary::generateToken([
                'user_id' => $user['id'],
                'email' => $user['email']
            ]);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to generate token', 500, $e->getMessage());
        }

        return $this->respondWithSuccess(['user' => $this->authTransformer->transform($user), 'token' => $token], 'Login successful');
    }
}
