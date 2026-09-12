<?php

namespace Tests\Feature;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\PlatformComplaint;
use App\Models\PlatformConversation;
use App\Models\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UniversalMessagingBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_start_direct_chat_with_every_user_role(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();
        $seller = $this->seller();
        $logistics = $this->logistics();
        $rider = $this->rider($logistics);

        foreach ([
            ['buyer', $buyer->id],
            ['seller', $seller->id],
            ['rider', $rider->id],
            ['logistics', $logistics->id],
        ] as [$role, $id]) {
            $this->withSession(
                $this->adminSession($admin)
            )->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => $role,
                    'target_id' => $id,
                ]
            )
                ->assertSuccessful()
                ->assertJsonPath(
                    'conversation.type',
                    'direct'
                );
        }

        $this->assertSame(
            4,
            PlatformConversation::query()
                ->where('conversation_type', 'direct')
                ->count()
        );
    }

    public function test_buyer_can_start_buyer_and_seller_chat_but_not_admin_logistics_or_rider(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();
        $otherBuyer = $this->buyer();
        $seller = $this->seller();
        $logistics = $this->logistics();
        $rider = $this->rider($logistics);

        $session = $this->buyerSession($buyer);

        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'buyer',
                    'target_id' => $otherBuyer->id,
                ]
            )
            ->assertSuccessful();

        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'seller',
                    'target_id' => $seller->id,
                ]
            )
            ->assertSuccessful();

        foreach ([
            ['admin', $admin->id],
            ['logistics', $logistics->id],
            ['rider', $rider->id],
        ] as [$role, $id]) {
            $this->withSession($session)
                ->postJson(
                    route('messaging.api.direct'),
                    [
                        'target_role' => $role,
                        'target_id' => $id,
                    ]
                )
                ->assertForbidden();
        }
    }

    public function test_seller_can_start_buyer_seller_and_logistics_but_admin_is_report_gated(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();
        $seller = $this->seller();
        $otherSeller = $this->seller();
        $logistics = $this->logistics();
        $rider = $this->rider($logistics);

        $session = $this->sellerSession($seller);

        foreach ([
            ['buyer', $buyer->id],
            ['seller', $otherSeller->id],
            ['logistics', $logistics->id],
        ] as [$role, $id]) {
            $this->withSession($session)
                ->postJson(
                    route('messaging.api.direct'),
                    [
                        'target_role' => $role,
                        'target_id' => $id,
                    ]
                )
                ->assertSuccessful();
        }

        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'admin',
                    'target_id' => $admin->id,
                ]
            )
            ->assertForbidden();

        // Seller cannot initiate Rider chat under the requested matrix.
        // Rider may initiate a Seller conversation and Seller may reply.
        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'rider',
                    'target_id' => $rider->id,
                ]
            )
            ->assertForbidden();
    }

    public function test_rider_can_start_buyer_seller_and_only_linked_logistics_but_not_admin(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();
        $seller = $this->seller();
        $logistics = $this->logistics();
        $otherLogistics = $this->logistics(
            'other-logistics@example.test'
        );
        $rider = $this->rider($logistics);

        $session = $this->riderSession($rider);

        foreach ([
            ['buyer', $buyer->id],
            ['seller', $seller->id],
            ['logistics', $logistics->id],
        ] as [$role, $id]) {
            $this->withSession($session)
                ->postJson(
                    route('messaging.api.direct'),
                    [
                        'target_role' => $role,
                        'target_id' => $id,
                    ]
                )
                ->assertSuccessful();
        }

        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'logistics',
                    'target_id' => $otherLogistics->id,
                ]
            )
            ->assertForbidden();

        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'admin',
                    'target_id' => $admin->id,
                ]
            )
            ->assertForbidden();
    }

    public function test_logistics_can_start_buyer_seller_logistics_and_linked_rider_only(): void
    {
        $buyer = $this->buyer();
        $seller = $this->seller();
        $logistics = $this->logistics();
        $otherLogistics = $this->logistics(
            'other-logistics@example.test'
        );

        $linkedRider = $this->rider(
            $logistics,
            'linked-rider@example.test'
        );

        $otherRider = $this->rider(
            $otherLogistics,
            'other-rider@example.test'
        );

        $session = $this->logisticsSession($logistics);

        foreach ([
            ['buyer', $buyer->id],
            ['seller', $seller->id],
            ['logistics', $otherLogistics->id],
            ['rider', $linkedRider->id],
        ] as [$role, $id]) {
            $this->withSession($session)
                ->postJson(
                    route('messaging.api.direct'),
                    [
                        'target_role' => $role,
                        'target_id' => $id,
                    ]
                )
                ->assertSuccessful();
        }

        $this->withSession($session)
            ->postJson(
                route('messaging.api.direct'),
                [
                    'target_role' => 'rider',
                    'target_id' => $otherRider->id,
                ]
            )
            ->assertForbidden();
    }

    public function test_non_admin_cannot_direct_chat_admin_but_own_report_opens_admin_support(): void
    {
        $admin = $this->admin();
        $buyer = $this->buyer();
        $otherBuyer = $this->buyer();

        $complaint = PlatformComplaint::query()->create([
            'reporter_role' => 'buyer',
            'reporter_identifier' => $buyer->email,
            'subject' => 'Order issue',
            'description' =>
                'I need administrator assistance with this order issue.',
            'status' => 'open',
        ]);

        $this->withSession(
            $this->buyerSession($buyer)
        )->postJson(
            route('messaging.api.direct'),
            [
                'target_role' => 'admin',
                'target_id' => $admin->id,
            ]
        )->assertForbidden();

        $created = $this->withSession(
            $this->buyerSession($buyer)
        )->postJson(
            route(
                'messaging.api.report-support',
                $complaint
            )
        )
            ->assertSuccessful()
            ->assertJsonPath(
                'conversation.type',
                'report_support'
            )
            ->assertJsonPath(
                'conversation.context.type',
                'platform_complaint'
            )
            ->assertJsonPath(
                'conversation.context.id',
                $complaint->id
            );

        $conversation = PlatformConversation::query()
            ->where(
                'uuid',
                $created->json('conversation.uuid')
            )
            ->firstOrFail();

        $this->assertDatabaseHas(
            'platform_conversation_participants',
            [
                'platform_conversation_id' =>
                    $conversation->id,
                'participant_role' => 'buyer',
                'participant_id' => $buyer->id,
                'left_at' => null,
            ]
        );

        $this->assertDatabaseHas(
            'platform_conversation_participants',
            [
                'platform_conversation_id' =>
                    $conversation->id,
                'participant_role' => 'admin',
                'participant_id' => $admin->id,
                'left_at' => null,
            ]
        );

        $this->withSession(
            $this->buyerSession($otherBuyer)
        )->postJson(
            route(
                'messaging.api.report-support',
                $complaint
            )
        )->assertForbidden();
    }

    public function test_admin_can_open_report_support_for_reporter(): void
    {
        $admin = $this->admin();
        $seller = $this->seller();

        $complaint = PlatformComplaint::query()->create([
            'reporter_role' => 'seller',
            'reporter_identifier' => (string) $seller->id,
            'subject' => 'Seller support request',
            'description' =>
                'Seller submitted a formal report before Admin messaging.',
            'status' => 'open',
        ]);

        $this->withSession(
            $this->adminSession($admin)
        )->postJson(
            route(
                'messaging.api.report-support',
                $complaint
            )
        )
            ->assertSuccessful()
            ->assertJsonPath(
                'conversation.type',
                'report_support'
            );
    }

    public function test_sender_identity_is_always_taken_from_session_and_cannot_be_spoofed(): void
    {
        $buyer = $this->buyer();
        $seller = $this->seller();

        $created = $this->withSession(
            $this->buyerSession($buyer)
        )->postJson(
            route('messaging.api.direct'),
            [
                'target_role' => 'seller',
                'target_id' => $seller->id,
            ]
        )->assertSuccessful();

        $uuid = $created->json(
            'conversation.uuid'
        );

        $this->withSession(
            $this->buyerSession($buyer)
        )->postJson(
            route(
                'messaging.api.send',
                ['conversation' => $uuid]
            ),
            [
                'body' => 'Hello seller.',
                'sender_role' => 'admin',
                'sender_id' => 999999,
            ]
        )
            ->assertCreated()
            ->assertJsonPath(
                'message.sender_role',
                'buyer'
            )
            ->assertJsonPath(
                'message.sender_id',
                $buyer->id
            );

        $this->assertDatabaseHas(
            'platform_messages',
            [
                'sender_role' => 'buyer',
                'sender_id' => $buyer->id,
                'body' => 'Hello seller.',
            ]
        );
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

    private function adminSession(
        AdminAccount $admin
    ): array {
        return array_merge(
            $this->baseSession(),
            [
                'is_admin' => true,
                'admin_account_id' => $admin->id,
            ]
        );
    }

    private function buyerSession(
        BuyerAccount $buyer
    ): array {
        return array_merge(
            $this->baseSession(),
            [
                'is_buyer' => true,
                'buyer_account_id' => $buyer->id,
                'buyer_email' => $buyer->email,
            ]
        );
    }

    private function sellerSession(
        SellerAccount $seller
    ): array {
        return array_merge(
            $this->baseSession(),
            [
                'is_seller' => true,
                'seller_account_id' => $seller->id,
            ]
        );
    }

    private function riderSession(
        CourierAccount $rider
    ): array {
        return array_merge(
            $this->baseSession(),
            [
                'is_courier' => true,
                'courier_account_id' => $rider->id,
                'courier_email' => $rider->email,
            ]
        );
    }

    private function logisticsSession(
        LogisticsAccount $logistics
    ): array {
        return array_merge(
            $this->baseSession(),
            [
                'is_logistics' => true,
                'logistics_account_id' => $logistics->id,
                'logistics_email' => $logistics->email,
            ]
        );
    }

    private function admin(): AdminAccount
    {
        return AdminAccount::query()->create([
            'name' => 'Messaging Admin',
            'email' =>
                'messaging-admin-' . uniqid() . '@example.test',
            'password' => Hash::make('password'),
        ]);
    }

    private function buyer(): BuyerAccount
    {
        return BuyerAccount::query()->create([
            'registration_application_id' => null,
            'last_name' => 'Buyer',
            'first_name' => 'Test',
            'middle_initial' => null,
            'sex' => 'N/A',
            'email' =>
                'buyer-' . uniqid() . '@example.test',
            'contact_no' => '09111111111',
            'birthday' => '2000-01-01',
            'age' => 26,
            'province_code' => 'TEST',
            'province_name' => 'Test Province',
            'municipality_code' => 'TEST',
            'municipality_name' => 'Test City',
            'barangay_code' => 'TEST',
            'barangay_name' => 'Test Barangay',
            'street_address' => 'Test Buyer Address',
            'password' => Hash::make('password'),
            'id_path' => null,
            'account_status' => 'active',
            'approved_at' => now(),
        ]);
    }

    private function seller(): SellerAccount
    {
        return SellerAccount::query()->create([
            'email' =>
                'seller-' . uniqid() . '@example.test',
            'store_name' =>
                'Messaging Test Store ' . uniqid(),
            'warning_count' => 0,
            'account_status' => 'active',
        ]);
    }

    private function logistics(
        ?string $email = null
    ): LogisticsAccount {
        return LogisticsAccount::query()->create([
            'registration_application_id' => null,
            'last_name' => 'Provider',
            'first_name' => 'Test',
            'middle_initial' => null,
            'sex' => 'N/A',
            'email' =>
                $email
                ?: 'logistics-' . uniqid() . '@example.test',
            'contact_no' => '09222222222',
            'birthday' => '2000-01-01',
            'age' => 26,
            'province_code' => 'TEST',
            'province_name' => 'Test Province',
            'municipality_code' => 'TEST',
            'municipality_name' => 'Test City',
            'barangay_code' => 'TEST',
            'barangay_name' => 'Test Barangay',
            'street_address' => 'Test Logistics Address',
            'business_name' =>
                'Messaging Logistics ' . uniqid(),
            'password' => Hash::make('password'),
            'id_path' => null,
            'business_permit_path' => null,
            'account_status' => 'active',
            'approved_at' => now(),
        ]);
    }

    private function rider(
        LogisticsAccount $logistics,
        ?string $email = null
    ): CourierAccount {
        return CourierAccount::query()->create([
            'registration_application_id' => null,
            'logistics_account_id' =>
                $logistics->id,
            'last_name' => 'Rider',
            'first_name' => 'Test',
            'middle_initial' => null,
            'sex' => 'N/A',
            'email' =>
                $email
                ?: 'rider-' . uniqid() . '@example.test',
            'contact_no' => '09333333333',
            'birthday' => '2000-01-01',
            'age' => 26,
            'province_code' => 'TEST',
            'province_name' => 'Test Province',
            'municipality_code' => 'TEST',
            'municipality_name' => 'Test City',
            'barangay_code' => 'TEST',
            'barangay_name' => 'Test Barangay',
            'street_address' => 'Test Rider Address',
            'password' => Hash::make('password'),
            'vehicle_type' => 'Motorcycle',
            'plate_number' =>
                'TEST-' . random_int(100, 999),
            'orcr_path' => null,
            'id_path' => null,
            'account_status' => 'active',
            'approved_at' => now(),
            'rating' => 5.00,
            'availability_status' => 'online',
        ]);
    }
}
