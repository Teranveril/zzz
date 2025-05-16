<?php

namespace Tests\Feature; // ZMIEŃ Z "Feature" NA "Tests\Feature"
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserEmailTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_create_user_with_multiple_emails(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson('/api/users', [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'phone_number' => '123-456-789',
            'emails' => ['jan@example.com', 'jan.kowalski@firma.pl']
        ]);

        $response->dump(); // Wyświetli odpowiedź serwera
        $response->assertStatus(201);
    }
}
