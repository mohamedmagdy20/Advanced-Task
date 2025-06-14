<?php  
namespace App\Services;

use App\Interfaces\AuthInterface;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthInterface
{
    protected $model;
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function register($data)
    {
        $user = $this->model->create($data);
        $user['token'] = $user->createToken('user Token')->plainTextToken;
        return $user;
    }

    public function login($data)
    {
        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'data' => ['The provided credentials are incorrect.']
            ]);
        }
        $user = User::where('email', $data['email'])->firstOrFail();
        $user->tokens()->delete();
        $user['token'] = $user->createToken('user Token')->plainTextToken;
        return $user;
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return true;

    }
}