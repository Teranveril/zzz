<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * GET /api/users
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }

    /**
     * GET /api/users/{user}
     * Implicit model binding: {user} => User $user
     */
    public function show(User $user)
    {
        $userWithEmails = $this->userService->getUser($user);
        return response()->json($userWithEmails);
    }

    /**
     * POST /api/users
     */
    public function store(Request $request)
    {
        // Walidacja danych
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'phone_number'      => 'required|string',
            'emails'     => 'sometimes|array',
            'emails.*'   => 'email',
        ]);

        $user = $this->userService->createUser($data);
        return response()->json($user, 201);
    }

    /**
     * PUT /api/users/{user}
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'phone_number'      => 'required|string',
            // pomijamy emails przy aktualizacji dla prostoty
        ]);

        $updated = $this->userService->updateUser($user, $data);
        return response()->json($updated);
    }

    /**
     * DELETE /api/users/{user}
     */
    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);
        return response()->json(null, 204);
    }

    /**
     * POST /api/users/{user}/welcome
     * Endpoint logujący wiadomość powitalną na wszystkie adresy email użytkownika.
     */
    public function sendWelcomeEmails(User $user)
    {
        $user->load('emails');
        $this->userService->sendWelcomeEmails($user);
        return response()->json(['message' => 'Wiadomości zostały zalogowane']);
    }
}
