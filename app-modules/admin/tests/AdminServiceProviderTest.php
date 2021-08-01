<?php

namespace Modules\Transaction\Tests;

use Tests\TestCase;
use SebastianBergmann\Environment\Console;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminServiceProviderTest extends TestCase
{
    /**
     * @test
     */
	public function can_see_all_users()
	{
        $response = $this->get('/api/v1/users');
        $response->assertStatus(200);
    }

    /**
     * @test
    */
      public function can_admin_view_all_transactions()
      {
        $response = $this->get('/api/v1/transactions');

        $response->assertStatus(200);

      }
}
