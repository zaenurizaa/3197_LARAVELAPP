<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FreeEventCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_event_bypasses_midtrans_and_creates_successful_transaction_and_decrements_stock(): void
    {
        $tenant = Tenant::create([
            'name' => 'Organizer Test',
            'slug' => 'organizer-test',
        ]);

        $category = Category::create([
            'name' => 'Webinar',
            'slug' => 'webinar',
        ]);

        $event = Event::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'title'       => 'Acara Webinar Gratis',
            'price'       => 0,
            'stock'       => 10,
            'is_free'     => true,
            'date'        => now()->addDays(5)->toDateTimeString(),
            'location'    => 'Online Zoom',
        ]);

        $response = $this->postJson(route('checkout.store', $event->id), [
            'event_id'       => $event->id,
            'customer_name'  => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $responseData = $response->json();
        $this->assertStringContainsString('/success/FREE-', $responseData['redirect_url']);

        // Assert database transaction
        $this->assertDatabaseHas('transactions', [
            'event_id'       => $event->id,
            'customer_name'  => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'total_price'    => 0,
            'status'         => 'success',
            'is_reserved'    => false,
        ]);

        // Assert stock decremented from 10 to 9
        $event->refresh();
        $this->assertEquals(9, $event->stock);
    }
}
