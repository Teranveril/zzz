<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUserWithEmails($request->validated());
        return response()->json($user, 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->updateUserWithEmails($user, $request->validated());
        return response()->json($user);
    }

    public function sendWelcomeEmails(User $user): JsonResponse
    {
        $this->userService->sendWelcomeEmails($user);
        return response()->json(['message' => 'Wiadomości wysłane']);
    }

    // ... pozostałe metody
}
