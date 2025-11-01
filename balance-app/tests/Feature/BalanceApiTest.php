<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BalanceApiTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_user_to_deposit_money()

    {
        $user = User::factory()->create(['balance' => 0]);

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 100.50,
            'comment' => 'Пополнение',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Успешное пополнение']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => 100.50,
        ]);
    }

    /** @test */
    public function it_prevents_withdraw_if_insufficient_funds()
    {
        $user = User::factory()->create(['balance' => 50]);

        $response = $this->postJson('/api/withdraw', [
            'user_id' => $user->id,
            'amount' => 100,
        ]);

        $response->assertStatus(409)
            ->assertJson(['error' => 'Недостаточно средств']);
    }

    /** @test */
    public function it_returns_user_balance()
    {
        $user = User::factory()->create(['balance' => 500]);

        $response = $this->getJson("/api/balance/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'balance' => 500,
            ]);
    }
}
