<?php
namespace App\Services;

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserService
{
public function createUserWithEmails(array $data): User
{
return DB::transaction(function () use ($data) {
$user = User::create([
'name' => $data['name'],
'email' => $data['email'],
'password' => bcrypt($data['password']),
'phone_number' => $data['phone_number']
]);

$this->createEmails($user, $data['emails']);

return $user->load('emailAddresses');
});
}

public function updateUserWithEmails(User $user, array $data): User
{
return DB::transaction(function () use ($user, $data) {
$user->update([
'name' => $data['name'] ?? $user->name,
'email' => $data['email'] ?? $user->email,
'phone_number' => $data['phone_number'] ?? $user->phone_number
]);

if (isset($data['emails'])) {
$user->emailAddresses()->delete();
$this->createEmails($user, $data['emails']);
}

return $user->fresh()->load('emailAddresses');
});
}

public function sendWelcomeEmails(User $user): void
{
$user->emailAddresses->each(function ($email) use ($user) {
Mail::to($email->email)->send(new WelcomeMail($user));
});
}

private function createEmails(User $user, array $emails): void
{
collect($emails)->each(function ($email) use ($user) {
$user->emailAddresses()->create(['email' => $email]);
});
}
}
