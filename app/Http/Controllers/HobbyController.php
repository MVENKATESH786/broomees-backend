<?php
namespace App\Http\Controllers;

use App\Repositories\HobbyRepository;
use App\Repositories\UserRepository;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HobbyController extends Controller
{
    protected $hobbyRepo;
    protected $userRepo;
    protected $reputationService;

    public function __construct(HobbyRepository $hobbyRepo, UserRepository $userRepo, ReputationService $reputationService)
    {
        $this->hobbyRepo = $hobbyRepo;
        $this->userRepo = $userRepo;
        $this->reputationService = $reputationService;
    }

    public function store(Request $request, $id)
    {
        $validated = $request->validate(['name' => 'required|string']);

        DB::transaction(function () use ($id, $validated) {
            $user = $this->userRepo->find($id);
            $hobby = $this->hobbyRepo->findByName($validated['name']);
            $this->hobbyRepo->attachToUser($user, $hobby);

            // Recalculate
            $this->reputationService->calculateAndSave($user);
            // In a full production system, we would also recalculate all friends of this user
            // because shared hobbies score changed for them too.
        });

        return response()->json(['message' => 'Hobby added'], 201);
    }
}
