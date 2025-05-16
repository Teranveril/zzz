<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\EmailAddress;
use Illuminate\Support\Facades\Log;
use Mockery;

class UserEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user()
    {
        $payload = [
            'first_name' => 'Jan',
            'last_name'  => 'Kowalski',
            'phone'      => '123456789',
            'emails'     => ['jan@example.com', 'kowalski@example.com'],
        ];

        $response = $this->postJson('/api/users', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment(['first_name' => 'Jan', 'last_name' => 'Kowalski']);

        // Sprawdź, czy utworzono wpis w bazie i powiązane emaile
        $this->assertDatabaseHas('users', ['first_name' => 'Jan']);
        $this->assertDatabaseHas('emails', ['email' => 'jan@example.com']);
        $this->assertDatabaseHas('emails', ['email' => 'kowalski@example.com']);
    }

    public function test_get_users_list()
    {
        User::factory()->count(3)->create();
        $response = $this->getJson('/api/users');
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_get_single_user()
    {
        $user = User::factory()->create(['first_name' => 'Adam']);
        $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Adam']);
    }

    public function test_update_user()
    {
        $user = User::factory()->create(['first_name' => 'Ela']);
        $payload = ['first_name' => 'Elżbieta', 'last_name' => 'Nowak', 'phone' => '987654321'];
        $response = $this->putJson("/api/users/{$user->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Elżbieta']);

        $this->assertDatabaseHas('users', ['first_name' => 'Elżbieta', 'last_name' => 'Nowak']);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson("/api/users/{$user->id}");
        $response->assertStatus(204);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_send_welcome_emails_logs_messages()
    {
        $user = User::factory()->create(['first_name' => 'Marek', 'last_name' => 'Nowak']);
        // Dodajemy dwa emaile do użytkownika
        EmailAddress::factory()->count(2)->create(['user_id' => $user->id]);

        // Mockujemy fasadę Log, żeby przechwycić wywołania
        Log::shouldReceive('info')->times(2)->with(
            Mockery::on(function ($msg) use ($user) {
                // Sprawdź, czy wiadomość zawiera imię i nazwisko użytkownika
                return str_contains($msg, "Witamy użytkownika {$user->first_name} {$user->last_name}");
            })
        );

        $response = $this->postJson("/api/users/{$user->id}/welcome");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Wiadomości zostały zalogowane']);
    }
}
