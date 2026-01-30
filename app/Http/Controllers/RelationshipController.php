<?php
namespace App\Http\Controllers;

use App\Repositories\RelationshipRepository;
use App\Repositories\UserRepository;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelationshipController extends Controller
{
    protected $relationRepo;
    protected $userRepo;
    protected $reputationService;

    public function __construct(RelationshipRepository $relationRepo, UserRepository $userRepo, ReputationService $reputationService)
    {
        $this->relationRepo = $relationRepo;
        $this->userRepo = $userRepo;
        $this->reputationService = $reputationService;
    }

    public function store(Request $request, $id)
    {
        $validated = $request->validate(['friend_id' => 'required|exists:users,id']);
        $friendId = $validated['friend_id'];

        if ($id === $friendId) return response()->json(['message' => 'Self-linking forbidden'], 400);
        if ($this->relationRepo->exists($id, $friendId)) return response()->json(['message' => 'Exists'], 409);

        // Transaction to ensure atomicity of relationship creation AND score recalculation
        DB::transaction(function () use ($id, $friendId) {
            $this->relationRepo->addFriend($id, $friendId);
            $this->reputationService->calculateAndSave($this->userRepo->find($id));
            $this->reputationService->calculateAndSave($this->userRepo->find($friendId));
        });

        return response()->json(['message' => 'Relationship created'], 201);
    }

    public function destroy(Request $request, $id)
    {
        $validated = $request->validate(['friend_id' => 'required|exists:users,id']);
        $friendId = $validated['friend_id'];

        DB::transaction(function () use ($id, $friendId) {
            $this->relationRepo->removeFriend($id, $friendId);
            $this->reputationService->calculateAndSave($this->userRepo->find($id));
            $this->reputationService->calculateAndSave($this->userRepo->find($friendId));
        });

        return response()->json(['message' => 'Relationship removed'], 200);
    }
}
