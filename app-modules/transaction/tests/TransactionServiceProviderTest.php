<?php

namespace Modules\Transaction\Tests;

use Tests\TestCase;
use SebastianBergmann\Environment\Console;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionServiceProviderTest extends TestCase
{
    /**
     * @test
     */
	public function can_do_assert_as_admin()
	{
        $response = $this->get('/api/v1/users');
        $response->assertStatus(200);
    }
}
