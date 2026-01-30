<?php
namespace App\Http\Controllers;

use App\Repositories\TokenRepository;

class AuthController extends Controller
{
    protected $tokenRepo;

    public function __construct(TokenRepository $tokenRepo)
    {
        $this->tokenRepo = $tokenRepo;
    }

    public function issueToken()
    {
        $token = $this->tokenRepo->createToken();
        return response()->json(['token' => $token]);
    }
}
