<?php

namespace Tests\Feature;

use App\Events\PlatformMessageReactionUpdated;
use App\Events\PlatformMessageSent;
use App\Jobs\SendSariAdminAssistantReply;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Messaging\PlatformConversation;
use App\Models\Messaging\PlatformConversationParticipant;
use App\Models\Messaging\PlatformMessage;
use App\Services\Messaging\SariAdminAssistantService;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SellerSupportMessagingReliabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_support_send_uses_worker_free_deferred_assistant_connection(): void
    {
        config([
            'sari.assistant.enabled' => true,
            'sari.assistant.queue_connection' => 'deferred',
            'sari.assistant.delay_seconds' => 0,
        ]);

        Bus::fake();
        Event::fake([PlatformMessageSent::class]);

        $admin = $this->admin();
        $seller = $this->seller();

        $support = $this->withSession($this->sellerSession($seller))
            ->postJson(route('messaging.api.support'))
            ->assertSuccessful();

        $uuid = $support->json('conversation.uuid');

        $this->withSession($this->sellerSession($seller))
            ->postJson(route('messaging.api.send', ['conversation' => $uuid]), [
                'body' => 'How do I add a product?',
            ])
            ->assertCreated()
            ->assertJsonPath('message.sender_role', 'seller');

        Bus::assertDispatched(SendSariAdminAssistantReply::class, function ($job): bool {
            return $job->connection === 'deferred'
                && $job->waitSeconds === 0
                && $job->triggerMessageId > 0;
        });
    }

    public function test_message_and_reaction_broadcasts_are_queued_and_rescuable(): void
    {
        $this->assertTrue(is_a(PlatformMessageSent::class, ShouldBroadcast::class, true));
        $this->assertFalse(is_a(PlatformMessageSent::class, ShouldBroadcastNow::class, true));
        $this->assertTrue(is_a(PlatformMessageSent::class, ShouldRescue::class, true));

        $this->assertTrue(is_a(PlatformMessageReactionUpdated::class, ShouldBroadcast::class, true));
        $this->assertFalse(is_a(PlatformMessageReactionUpdated::class, ShouldBroadcastNow::class, true));
        $this->assertTrue(is_a(PlatformMessageReactionUpdated::class, ShouldRescue::class, true));
    }

    public function test_assistant_has_a_useful_local_fallback_when_provider_configuration_is_missing(): void
    {
        config([
            'sari.assistant.enabled' => true,
            'sari.assistant.api_key' => null,
            'sari.assistant.model' => null,
        ]);

        Event::fake([PlatformMessageSent::class]);

        $admin = $this->admin();
        $seller = $this->seller();

        $conversation = PlatformConversation::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'conversation_type' => 'admin_support',
            'context_type' => 'account_support',
            'context_id' => $seller->id,
            'dedupe_key' => 'assistant-test:' . $seller->id,
            'subject' => 'SARI Support',
            'status' => 'active',
            'created_by_role' => 'seller',
            'created_by_id' => $seller->id,
        ]);

        PlatformConversationParticipant::query()->create([
            'platform_conversation_id' => $conversation->id,
            'participant_role' => 'seller',
            'participant_id' => $seller->id,
            'realtime_token' => \Illuminate\Support\Str::random(48),
            'joined_at' => now(),
        ]);

        PlatformConversationParticipant::query()->create([
            'platform_conversation_id' => $conversation->id,
            'participant_role' => 'admin',
            'participant_id' => $admin->id,
            'realtime_token' => \Illuminate\Support\Str::random(48),
            'joined_at' => now(),
        ]);

        $trigger = PlatformMessage::query()->create([
            'platform_conversation_id' => $conversation->id,
            'sender_role' => 'seller',
            'sender_id' => $seller->id,
            'message_type' => 'text',
            'body' => 'How do I add a new product?',
        ]);

        (new SendSariAdminAssistantReply($trigger->id, 0))
            ->handle(app(SariAdminAssistantService::class));

        $reply = PlatformMessage::query()
            ->where('platform_conversation_id', $conversation->id)
            ->where('sender_role', 'admin')
            ->where('id', '>', $trigger->id)
            ->firstOrFail();

        $this->assertTrue((bool) data_get($reply->metadata, 'ai_assistant'));
        $this->assertStringContainsString('Product Management', (string) $reply->body);
        $this->assertStringContainsString('Add New Product', (string) $reply->body);
    }

    private function baseSession(): array
    {
        return [
            'is_admin' => false,
            'admin_account_id' => null,
            'is_buyer' => false,
            'buyer_account_id' => null,
            'buyer_social_account_id' => null,
            'buyer_email' => null,
            'is_seller' => false,
            'seller_account_id' => null,
            'is_courier' => false,
            'courier_account_id' => null,
            'courier_email' => null,
            'is_logistics' => false,
            'logistics_account_id' => null,
            'logistics_email' => null,
        ];
    }

    private function sellerSession(SellerAccount $seller): array
    {
        return array_merge($this->baseSession(), [
            'is_seller' => true,
            'seller_account_id' => $seller->id,
        ]);
    }

    private function admin(): AdminAccount
    {
        return AdminAccount::query()->create([
            'name' => 'Messaging Admin',
            'email' => 'messaging-admin-' . uniqid() . '@example.test',
            'password' => Hash::make('password'),
        ]);
    }

    private function seller(): SellerAccount
    {
        return SellerAccount::query()->create([
            'email' => 'seller-' . uniqid() . '@example.test',
            'store_name' => 'Messaging Test Store ' . uniqid(),
            'warning_count' => 0,
            'account_status' => 'active',
        ]);
    }
}
