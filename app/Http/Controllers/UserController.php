<?php
namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;

class UserController extends Controller
{
    protected $userRepo;
    protected $userService;

    public function __construct(UserRepository $userRepo, UserService $userService)
    {
        $this->userRepo = $userRepo;
        $this->userService = $userService;
    }

    public function index()
    {
        return response()->json($this->userRepo->paginate());
    }

    public function show($id)
    {
        return response()->json($this->userRepo->find($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username',
            'age' => 'required|integer',
        ]);
        $user = $this->userRepo->create($validated);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        // Enforce Optimistic Locking input
        $validated = $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'age' => 'sometimes|integer',
            'version' => 'required|integer'
        ]);

        try {
            $user = $this->userRepo->updateWithLock($id, $validated, $validated['version']);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);
            return response()->json(['message' => 'User deleted'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function systemMetrics()
    {
        $avg = User::avg('reputation_score');
        return response()->json(['average_reputation' => $avg]);
    }
}
