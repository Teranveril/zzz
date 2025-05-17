<?php

namespace Tests\Feature;

use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\EmailAddress;
use Illuminate\Support\Facades\Log;
use Mockery;

class UserEmailTest extends TestCase
{
    use RefreshDatabase;

    //LOG
    public function test_create_user()
    {
        $payload = [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'phone_number' => '123456789'
        ];

        $response = $this->postJson('/users', $payload);

        $response->assertStatus(201);
    }


    public function test_get_users_list()
    {
        User::factory()->count(3)->create();
        $response = $this->getJson('/users');
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_get_single_user()
    {
        $user = User::factory()->create(['first_name' => 'Adam']);
        $response = $this->getJson("/users/{$user->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Adam']);
    }

    public function test_update_user()
    {
        $user = User::factory()->create(['first_name' => 'Ela']);
        $payload = ['first_name' => 'Elżbieta', 'last_name' => 'Nowak', 'phone_number' => '987654321'];
        $response = $this->putJson("/users/{$user->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Elżbieta']);

        $this->assertDatabaseHas('users', ['first_name' => 'Elżbieta', 'last_name' => 'Nowak']);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson("/users/{$user->id}");
        $response->assertStatus(204);

        $this->assertDatabaseCount('users', 0);
    }

    //SMTP
// tests/Feature/UserEmailTest.php
    public function test_welcome_mail_is_sent_to_all_addresses(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        EmailAddress::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // Ważne: odśwież model użytkownika po utworzeniu emaili
        $user->load('emails');

        $this->postJson("/users/{$user->id}/welcome")
            ->assertOk();

        Mail::assertSent(WelcomeUserMail::class, 3);

        foreach ($user->emails as $email) {
            Mail::assertSent(WelcomeUserMail::class, function ($mail) use ($user, $email) {
                return $mail->hasTo($email->email) && $mail->user->is($user);
            });
        }
    }
}
