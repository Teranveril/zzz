<?php

namespace App\Services;

use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * Pobierz wszystkich użytkowników.
     */
    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('emails')->get();
    }

    /**
     * Pobierz jednego użytkownika (wymaga parametru User dzięki route model binding).
     */
    public function getUser(User $user)
    {
        return $user->load('emails');
    }

    /**
     * Utwórz nowego użytkownika (dane z validacji request).
     * Opcjonalnie tworzy także powiązane adresy email.
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
        ]);

        if (!empty($data['emails']) && is_array($data['emails'])) {
            $emails = array_map(fn($address) => ['email' => $address], $data['emails']);
            $user->emails()->createMany($emails);
        }

        return $user;
    }

    /**
     * Zaktualizuj istniejącego użytkownika.
     * (Opcjonalnie można też obsłużyć aktualizację adresów email tutaj.)
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
        ]);
        return $user;
    }

    /**
     * Usuń użytkownika.
     */
    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    /**
     * Wyślij (zaloguj) wiadomość powitalną do wszystkich emaili użytkownika.
     * W rzeczywistości używamy Log::info zamiast prawdziwego wysyłania.
     */
    public function sendWelcomeEmails(User $user): void
    {
        $message = "Witamy użytkownika {$user->first_name} {$user->last_name}";
//        foreach ($user->emails as $email) {
//            Log::info("{$message} na adresie {$email->email}");
//            Mail::to($email->email)->queue(new WelcomeUserMail($user));
//        }

        $user->load('emails');
        foreach ($user->emails as $address) {
            Mail::to($address->email)->send(
                new WelcomeUserMail($user)
            );
        }
    }
}
