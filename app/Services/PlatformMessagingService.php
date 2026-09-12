<?php

namespace App\Services;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\PlatformComplaint;
use App\Models\PlatformConversation;
use App\Models\PlatformConversationParticipant;
use App\Models\PlatformMessage;
use App\Models\SellerAccount;
use App\Models\SocialAccount;
use App\Support\CurrentMessagingActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlatformMessagingService
{
    public const ROLES = [
        'admin',
        'buyer',
        'social_buyer',
        'seller',
        'rider',
        'logistics',
    ];

    /**
     * Start-chat policy.
     *
     * Replying inside an already-authorized conversation remains allowed for
     * all active participants. This is intentional for operational cases such
     * as a Rider contacting a Buyer during delivery.
     *
     * Admin is deliberately excluded from every non-admin start-chat list.
     * A normal user must first submit a PlatformComplaint/report, then use the
     * report-support thread.
     */
    private const START_CHAT_MATRIX = [
        'buyer' => [
            'buyer',
            'social_buyer',
            'seller',
        ],
        'social_buyer' => [
            'buyer',
            'social_buyer',
            'seller',
        ],
        'seller' => [
            'buyer',
            'social_buyer',
            'seller',
            'logistics',
        ],
        'logistics' => [
            'buyer',
            'social_buyer',
            'seller',
            'logistics',
            'rider',
        ],
        'rider' => [
            'buyer',
            'social_buyer',
            'seller',
            'logistics',
        ],
        'admin' => [
            'buyer',
            'social_buyer',
            'seller',
            'rider',
            'logistics',
        ],
    ];

    /**
     * @return array<string,mixed>
     */
    public function me(Request $request): array
    {
        return $this->publicActor(
            CurrentMessagingActor::resolve($request)
        );
    }

    /**
     * Returns legitimate start-chat contacts plus report/support context.
     *
     * @return array{
     *     actor:array<string,mixed>,
     *     contacts:Collection<int,array<string,mixed>>,
     *     reports:Collection<int,array<string,mixed>>
     * }
     */
    public function connections(Request $request): array
    {
        $actor = CurrentMessagingActor::resolve($request);

        return [
            'actor' => $this->publicActor($actor),
            'contacts' => $this->contactsForActor($actor),
            'reports' => $this->reportsForActor($actor),
        ];
    }

    /**
     * @return Collection<int,array<string,mixed>>
     */
    public function inbox(Request $request): Collection
    {
        $actor = CurrentMessagingActor::resolve($request);

        $conversations = PlatformConversation::query()
            ->with(['participants', 'latestMessage'])
            ->where('status', 'active')
            ->whereHas(
                'participants',
                fn (Builder $query) => $query
                    ->where('participant_role', $actor['role'])
                    ->where('participant_id', $actor['id'])
                    ->whereNull('left_at')
            )
            ->orderByRaw('last_message_at IS NULL')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return $conversations->map(
            fn (PlatformConversation $conversation): array =>
                $this->conversationSummary(
                    $conversation,
                    $actor
                )
        );
    }

    public function openDirect(
        Request $request,
        string $targetRole,
        int $targetId
    ): PlatformConversation {
        $actor = CurrentMessagingActor::resolve($request);
        $targetRole = $this->normalizeRole($targetRole);

        if (
            $actor['role'] === $targetRole
            && (int) $actor['id'] === $targetId
        ) {
            throw ValidationException::withMessages([
                'target_id' => 'You cannot start a conversation with yourself.',
            ]);
        }

        $target = $this->resolveIdentity(
            $targetRole,
            $targetId
        );

        $this->authorizeDirectStart(
            $actor,
            $target
        );

        $identityKeys = [
            $actor['role'] . ':' . $actor['id'],
            $target['role'] . ':' . $target['id'],
        ];
        sort($identityKeys);

        $dedupeKey = 'direct:' . implode('|', $identityKeys);

        return DB::transaction(function () use (
            $actor,
            $target,
            $dedupeKey
        ): PlatformConversation {
            $conversation = PlatformConversation::query()
                ->firstOrCreate(
                    ['dedupe_key' => $dedupeKey],
                    [
                        'uuid' => (string) Str::uuid(),
                        'conversation_type' => 'direct',
                        'context_type' => null,
                        'context_id' => null,
                        'subject' =>
                            $actor['name'] . ' ↔ ' . $target['name'],
                        'status' => 'active',
                        'created_by_role' => $actor['role'],
                        'created_by_id' => $actor['id'],
                    ]
                );

            $this->ensureParticipant(
                $conversation,
                $actor['role'],
                $actor['id']
            );

            $this->ensureParticipant(
                $conversation,
                $target['role'],
                $target['id']
            );

            return $conversation->fresh([
                'participants',
                'latestMessage',
            ]);
        });
    }

    /**
     * Creates/opens a report-gated Admin support thread.
     *
     * Non-admin users may only open a support conversation for a report that
     * belongs to their own current account. Admins may open any report.
     */
    public function openReportConversation(
        Request $request,
        PlatformComplaint $complaint
    ): PlatformConversation {
        $actor = CurrentMessagingActor::resolve($request);
        $reporter = $this->resolveComplaintReporter($complaint);

        if ($actor['role'] !== 'admin') {
            abort_unless(
                $actor['role'] === $reporter['role']
                    && (int) $actor['id'] === (int) $reporter['id'],
                403,
                'You can only open Admin support for a report submitted by your own account.'
            );
        }

        $admins = AdminAccount::query()
            ->orderBy('id')
            ->get();

        abort_if(
            $admins->isEmpty(),
            422,
            'No SARI Administrator account is currently available for this report.'
        );

        return DB::transaction(function () use (
            $actor,
            $reporter,
            $complaint,
            $admins
        ): PlatformConversation {
            $conversation = PlatformConversation::query()
                ->firstOrCreate(
                    [
                        'dedupe_key' =>
                            'report-support:platform-complaint:' .
                            $complaint->id,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'conversation_type' => 'report_support',
                        'context_type' => 'platform_complaint',
                        'context_id' => $complaint->id,
                        'subject' =>
                            'Report #' . $complaint->id .
                            ' · ' . $complaint->subject,
                        'status' => 'active',
                        'created_by_role' => $actor['role'],
                        'created_by_id' => $actor['id'],
                    ]
                );

            $this->ensureParticipant(
                $conversation,
                $reporter['role'],
                $reporter['id']
            );

            foreach ($admins as $admin) {
                $this->ensureParticipant(
                    $conversation,
                    'admin',
                    (int) $admin->id
                );
            }

            return $conversation->fresh([
                'participants',
                'latestMessage',
            ]);
        });
    }

    /**
     * @return array<string,mixed>
     */
    public function detail(
        Request $request,
        PlatformConversation $conversation
    ): array {
        $actor = CurrentMessagingActor::resolve($request);

        $participant = $this->assertParticipant(
            $conversation,
            $actor
        );

        $messages = PlatformMessage::query()
            ->where(
                'platform_conversation_id',
                $conversation->id
            )
            ->whereNull('deleted_at')
            ->latest('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return [
            'conversation' => $this->conversationSummary(
                $conversation->fresh([
                    'participants',
                    'latestMessage',
                ]),
                $actor
            ),
            'participants' => $conversation
                ->participants()
                ->whereNull('left_at')
                ->orderBy('id')
                ->get()
                ->map(
                    fn (PlatformConversationParticipant $row): array =>
                        $this->participantPayload($row)
                )
                ->values(),
            'messages' => $messages->map(
                fn (PlatformMessage $message): array =>
                    $this->messagePayload($message)
            ),
            'read_state' => [
                'last_read_message_id' =>
                    $participant->last_read_message_id,
            ],
        ];
    }

    public function send(
        Request $request,
        PlatformConversation $conversation,
        string $body
    ): PlatformMessage {
        $actor = CurrentMessagingActor::resolve($request);

        $this->assertParticipant(
            $conversation,
            $actor
        );

        abort_if(
            $conversation->status !== 'active',
            422,
            'This conversation is not active.'
        );

        $this->assertActorCanSend($actor);

        $body = trim($body);

        if ($body === '') {
            throw ValidationException::withMessages([
                'body' => 'A message is required.',
            ]);
        }

        return DB::transaction(function () use (
            $conversation,
            $actor,
            $body
        ): PlatformMessage {
            $message = PlatformMessage::query()->create([
                'platform_conversation_id' => $conversation->id,
                'sender_role' => $actor['role'],
                'sender_id' => $actor['id'],
                'message_type' => 'text',
                'body' => $body,
            ]);

            $conversation->forceFill([
                'last_message_at' => $message->created_at,
            ])->save();

            PlatformConversationParticipant::query()
                ->where(
                    'platform_conversation_id',
                    $conversation->id
                )
                ->where('participant_role', $actor['role'])
                ->where('participant_id', $actor['id'])
                ->whereNull('left_at')
                ->update([
                    'last_read_message_id' => $message->id,
                    'updated_at' => now(),
                ]);

            return $message->fresh();
        });
    }

    public function markRead(
        Request $request,
        PlatformConversation $conversation
    ): ?int {
        $actor = CurrentMessagingActor::resolve($request);

        $participant = $this->assertParticipant(
            $conversation,
            $actor
        );

        $lastMessageId = PlatformMessage::query()
            ->where(
                'platform_conversation_id',
                $conversation->id
            )
            ->whereNull('deleted_at')
            ->max('id');

        $participant->forceFill([
            'last_read_message_id' => $lastMessageId,
        ])->save();

        return $lastMessageId
            ? (int) $lastMessageId
            : null;
    }

    /**
     * @return array<string,mixed>
     */
    public function messagePayload(
        PlatformMessage $message
    ): array {
        return [
            'id' => $message->id,
            'conversation_id' =>
                $message->platform_conversation_id,
            'sender_role' => $message->sender_role,
            'sender_id' => $message->sender_id,
            'sender' => $this->identityLabel(
                $message->sender_role,
                (int) $message->sender_id
            ),
            'message_type' => $message->message_type,
            'body' => $message->body,
            'attachment' => $message->attachment_path
                ? [
                    'path' => $message->attachment_path,
                    'name' => $message->attachment_name,
                    'mime' => $message->attachment_mime,
                    'size' => $message->attachment_size,
                ]
                : null,
            'metadata' => $message->metadata,
            'edited_at' =>
                $message->edited_at?->toIso8601String(),
            'created_at' =>
                $message->created_at?->toIso8601String(),
        ];
    }

    /**
     * @param array<string,mixed> $actor
     * @param array<string,mixed> $target
     */
    private function authorizeDirectStart(
        array $actor,
        array $target
    ): void {
        $allowed = self::START_CHAT_MATRIX[
            $actor['role']
        ] ?? [];

        if (
            $actor['role'] !== 'admin'
            && $target['role'] === 'admin'
        ) {
            abort(
                403,
                'Direct Admin chat is report-gated. Submit a report first, then open the support thread for that report.'
            );
        }

        abort_unless(
            in_array(
                $target['role'],
                $allowed,
                true
            ),
            403,
            'Your account role cannot start a direct chat with this role.'
        );

        $this->assertTargetAvailable($target);

        // Rider <-> Logistics direct chat is limited to the Rider's linked
        // Logistics provider, preserving the existing SARI relationship.
        if (
            $actor['role'] === 'rider'
            && $target['role'] === 'logistics'
        ) {
            /** @var CourierAccount $rider */
            $rider = $actor['model'];

            abort_unless(
                (int) $rider->logistics_account_id
                    === (int) $target['id'],
                403,
                'A Rider can only start Logistics chat with the Rider’s linked provider.'
            );
        }

        if (
            $actor['role'] === 'logistics'
            && $target['role'] === 'rider'
        ) {
            /** @var CourierAccount $rider */
            $rider = $target['model'];

            abort_unless(
                (int) $rider->logistics_account_id
                    === (int) $actor['id'],
                403,
                'Logistics can only start Rider chat with a Rider linked to that provider.'
            );
        }
    }

    /**
     * @param array<string,mixed> $target
     */
    private function assertTargetAvailable(
        array $target
    ): void {
        if (
            in_array(
                $target['role'],
                ['admin', 'social_buyer'],
                true
            )
        ) {
            return;
        }

        $allowedStatuses = $target['role'] === 'seller'
            ? ['active', 'warning']
            : ['active'];

        abort_unless(
            in_array(
                $target['status'],
                $allowedStatuses,
                true
            ),
            403,
            'The selected account is not currently available for new direct conversations.'
        );
    }

    /**
     * @param array<string,mixed> $actor
     */
    private function assertActorCanSend(
        array $actor
    ): void {
        if (
            in_array(
                $actor['role'],
                ['admin', 'social_buyer'],
                true
            )
        ) {
            return;
        }

        $allowedStatuses = $actor['role'] === 'seller'
            ? ['active', 'warning']
            : ['active'];

        abort_unless(
            in_array(
                $actor['status'],
                $allowedStatuses,
                true
            ),
            403,
            'This account is not currently allowed to send messages.'
        );
    }

    /**
     * @param array<string,mixed> $actor
     */
    private function assertParticipant(
        PlatformConversation $conversation,
        array $actor
    ): PlatformConversationParticipant {
        $participant = PlatformConversationParticipant::query()
            ->where(
                'platform_conversation_id',
                $conversation->id
            )
            ->where('participant_role', $actor['role'])
            ->where('participant_id', $actor['id'])
            ->whereNull('left_at')
            ->first();

        abort_unless(
            $participant,
            403,
            'You are not a participant in this conversation.'
        );

        return $participant;
    }

    private function ensureParticipant(
        PlatformConversation $conversation,
        string $role,
        int $id
    ): PlatformConversationParticipant {
        $participant = PlatformConversationParticipant::query()
            ->firstOrCreate(
                [
                    'platform_conversation_id' =>
                        $conversation->id,
                    'participant_role' => $role,
                    'participant_id' => $id,
                ],
                [
                    'joined_at' => now(),
                ]
            );

        if ($participant->left_at) {
            $participant->forceFill([
                'left_at' => null,
                'joined_at' => now(),
            ])->save();
        }

        return $participant;
    }

    /**
     * @param array<string,mixed> $actor
     * @return Collection<int,array<string,mixed>>
     */
    private function contactsForActor(
        array $actor
    ): Collection {
        if ($actor['role'] === 'admin') {
            return $this->adminContacts();
        }

        $contacts = collect();

        if (
            in_array(
                $actor['role'],
                ['buyer', 'social_buyer'],
                true
            )
        ) {
            $contacts = $contacts
                ->concat($this->activeBuyerContacts($actor))
                ->concat($this->activeSellerContacts());
        }

        if ($actor['role'] === 'seller') {
            $contacts = $contacts
                ->concat($this->activeBuyerContacts($actor))
                ->concat($this->activeSellerContacts($actor))
                ->concat($this->activeLogisticsContacts());
        }

        if ($actor['role'] === 'logistics') {
            $contacts = $contacts
                ->concat($this->activeBuyerContacts($actor))
                ->concat($this->activeSellerContacts())
                ->concat($this->activeLogisticsContacts($actor))
                ->concat(
                    $this->linkedRiderContacts(
                        (int) $actor['id']
                    )
                );
        }

        if ($actor['role'] === 'rider') {
            $contacts = $contacts
                ->concat($this->activeBuyerContacts($actor))
                ->concat($this->activeSellerContacts())
                ->concat(
                    $this->riderLinkedLogisticsContact(
                        $actor
                    )
                );
        }

        return $contacts
            ->unique(
                fn (array $row): string =>
                    $row['role'] . ':' . $row['id']
            )
            ->values();
    }

    /**
     * @return Collection<int,array<string,mixed>>
     */
    private function adminContacts(): Collection
    {
        return collect()
            ->concat(
                BuyerAccount::query()
                    ->orderBy('first_name')
                    ->get()
                    ->map(
                        fn (BuyerAccount $account): array =>
                            $this->contactPayload(
                                'buyer',
                                $account->id,
                                trim(
                                    $account->first_name .
                                    ' ' .
                                    $account->last_name
                                ),
                                $account->email
                            )
                    )
            )
            ->concat(
                SocialAccount::query()
                    ->orderBy('name')
                    ->get()
                    ->map(
                        fn (SocialAccount $account): array =>
                            $this->contactPayload(
                                'social_buyer',
                                $account->id,
                                $account->name ?: 'Social Buyer',
                                $account->email
                            )
                    )
            )
            ->concat(
                SellerAccount::query()
                    ->orderBy('store_name')
                    ->get()
                    ->map(
                        fn (SellerAccount $account): array =>
                            $this->contactPayload(
                                'seller',
                                $account->id,
                                $account->store_name
                                    ?: $account->email,
                                $account->email
                            )
                    )
            )
            ->concat(
                CourierAccount::query()
                    ->orderBy('first_name')
                    ->get()
                    ->map(
                        fn (CourierAccount $account): array =>
                            $this->contactPayload(
                                'rider',
                                $account->id,
                                trim(
                                    $account->first_name .
                                    ' ' .
                                    $account->last_name
                                ),
                                $account->email
                            )
                    )
            )
            ->concat(
                LogisticsAccount::query()
                    ->orderBy('first_name')
                    ->get()
                    ->map(
                        fn (LogisticsAccount $account): array =>
                            $this->contactPayload(
                                'logistics',
                                $account->id,
                                $this->logisticsName($account),
                                $account->email
                            )
                    )
            )
            ->values();
    }

    /**
     * @param array<string,mixed>|null $actor
     * @return Collection<int,array<string,mixed>>
     */
    private function activeBuyerContacts(
        ?array $actor = null
    ): Collection {
        $buyers = BuyerAccount::query()
            ->where('account_status', 'active')
            ->orderBy('first_name')
            ->get()
            ->map(
                fn (BuyerAccount $account): array =>
                    $this->contactPayload(
                        'buyer',
                        $account->id,
                        trim(
                            $account->first_name .
                            ' ' .
                            $account->last_name
                        ),
                        $account->email
                    )
            );

        $socialBuyers = SocialAccount::query()
            ->orderBy('name')
            ->get()
            ->map(
                fn (SocialAccount $account): array =>
                    $this->contactPayload(
                        'social_buyer',
                        $account->id,
                        $account->name ?: 'Social Buyer',
                        $account->email
                    )
            );

        return $buyers
            ->concat($socialBuyers)
            ->reject(
                fn (array $row): bool =>
                    $actor
                    && $row['role'] === $actor['role']
                    && (int) $row['id'] === (int) $actor['id']
            )
            ->values();
    }

    /**
     * @param array<string,mixed>|null $actor
     * @return Collection<int,array<string,mixed>>
     */
    private function activeSellerContacts(
        ?array $actor = null
    ): Collection {
        return SellerAccount::query()
            ->whereIn(
                'account_status',
                ['active', 'warning']
            )
            ->orderBy('store_name')
            ->get()
            ->map(
                fn (SellerAccount $account): array =>
                    $this->contactPayload(
                        'seller',
                        $account->id,
                        $account->store_name ?: $account->email,
                        $account->email
                    )
            )
            ->reject(
                fn (array $row): bool =>
                    $actor
                    && $row['role'] === $actor['role']
                    && (int) $row['id'] === (int) $actor['id']
            )
            ->values();
    }

    /**
     * @param array<string,mixed>|null $actor
     * @return Collection<int,array<string,mixed>>
     */
    private function activeLogisticsContacts(
        ?array $actor = null
    ): Collection {
        return LogisticsAccount::query()
            ->where('account_status', 'active')
            ->orderBy('first_name')
            ->get()
            ->map(
                fn (LogisticsAccount $account): array =>
                    $this->contactPayload(
                        'logistics',
                        $account->id,
                        $this->logisticsName($account),
                        $account->email
                    )
            )
            ->reject(
                fn (array $row): bool =>
                    $actor
                    && $row['role'] === $actor['role']
                    && (int) $row['id'] === (int) $actor['id']
            )
            ->values();
    }

    /**
     * @return Collection<int,array<string,mixed>>
     */
    private function linkedRiderContacts(
        int $logisticsId
    ): Collection {
        return CourierAccount::query()
            ->where(
                'logistics_account_id',
                $logisticsId
            )
            ->where('account_status', 'active')
            ->orderBy('first_name')
            ->get()
            ->map(
                fn (CourierAccount $account): array =>
                    $this->contactPayload(
                        'rider',
                        $account->id,
                        trim(
                            $account->first_name .
                            ' ' .
                            $account->last_name
                        ),
                        $account->email
                    )
            )
            ->values();
    }

    /**
     * @param array<string,mixed> $rider
     * @return Collection<int,array<string,mixed>>
     */
    private function riderLinkedLogisticsContact(
        array $rider
    ): Collection {
        /** @var CourierAccount $model */
        $model = $rider['model'];

        if (!$model->logistics_account_id) {
            return collect();
        }

        $logistics = LogisticsAccount::query()
            ->whereKey($model->logistics_account_id)
            ->where('account_status', 'active')
            ->first();

        if (!$logistics) {
            return collect();
        }

        return collect([
            $this->contactPayload(
                'logistics',
                $logistics->id,
                $this->logisticsName($logistics),
                $logistics->email
            ),
        ]);
    }

    /**
     * @param array<string,mixed> $actor
     * @return Collection<int,array<string,mixed>>
     */
    private function reportsForActor(
        array $actor
    ): Collection {
        $query = PlatformComplaint::query()
            ->latest('id');

        if ($actor['role'] !== 'admin') {
            $reporterRole = $actor['role'] === 'social_buyer'
                ? 'buyer'
                : $actor['role'];

            $identifier = match ($actor['role']) {
                'seller' => (string) $actor['id'],
                default => (string) ($actor['email'] ?? ''),
            };

            $query
                ->where('reporter_role', $reporterRole)
                ->where(
                    'reporter_identifier',
                    $identifier
                );
        }

        return $query
            ->limit(100)
            ->get()
            ->map(function (PlatformComplaint $complaint): array {
                return [
                    'id' => $complaint->id,
                    'subject' => $complaint->subject,
                    'status' => $complaint->status,
                    'reporter_role' =>
                        $complaint->reporter_role,
                    'support_conversation_uuid' =>
                        PlatformConversation::query()
                            ->where(
                                'dedupe_key',
                                'report-support:platform-complaint:' .
                                    $complaint->id
                            )
                            ->value('uuid'),
                    'created_at' =>
                        $complaint->created_at?->toIso8601String(),
                ];
            })
            ->values();
    }

    /**
     * @return array<string,mixed>
     */
    private function resolveComplaintReporter(
        PlatformComplaint $complaint
    ): array {
        $role = strtolower(
            trim((string) $complaint->reporter_role)
        );
        $identifier = trim(
            (string) $complaint->reporter_identifier
        );

        abort_if(
            $identifier === '',
            422,
            'This report does not contain a reporter identifier.'
        );

        if ($role === 'buyer') {
            $buyer = BuyerAccount::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [strtolower($identifier)]
                )
                ->first();

            if ($buyer) {
                return $this->identityFromModel(
                    'buyer',
                    $buyer
                );
            }

            $social = SocialAccount::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [strtolower($identifier)]
                )
                ->first();

            abort_unless(
                $social,
                422,
                'The Buyer account that submitted this report could not be resolved.'
            );

            return $this->identityFromModel(
                'social_buyer',
                $social
            );
        }

        if ($role === 'seller') {
            $seller = ctype_digit($identifier)
                ? SellerAccount::query()->find(
                    (int) $identifier
                )
                : SellerAccount::query()
                    ->whereRaw(
                        'LOWER(email) = ?',
                        [strtolower($identifier)]
                    )
                    ->first();

            abort_unless(
                $seller,
                422,
                'The Seller account that submitted this report could not be resolved.'
            );

            return $this->identityFromModel(
                'seller',
                $seller
            );
        }

        if (in_array($role, ['rider', 'courier'], true)) {
            $rider = ctype_digit($identifier)
                ? CourierAccount::query()->find(
                    (int) $identifier
                )
                : CourierAccount::query()
                    ->whereRaw(
                        'LOWER(email) = ?',
                        [strtolower($identifier)]
                    )
                    ->first();

            abort_unless(
                $rider,
                422,
                'The Rider account that submitted this report could not be resolved.'
            );

            return $this->identityFromModel(
                'rider',
                $rider
            );
        }

        if ($role === 'logistics') {
            $logistics = ctype_digit($identifier)
                ? LogisticsAccount::query()->find(
                    (int) $identifier
                )
                : LogisticsAccount::query()
                    ->whereRaw(
                        'LOWER(email) = ?',
                        [strtolower($identifier)]
                    )
                    ->first();

            abort_unless(
                $logistics,
                422,
                'The Logistics account that submitted this report could not be resolved.'
            );

            return $this->identityFromModel(
                'logistics',
                $logistics
            );
        }

        abort(
            422,
            'Unsupported report owner role.'
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function resolveIdentity(
        string $role,
        int $id
    ): array {
        $model = match ($role) {
            'admin' => AdminAccount::query()->findOrFail($id),
            'buyer' => BuyerAccount::query()->findOrFail($id),
            'social_buyer' => SocialAccount::query()->findOrFail($id),
            'seller' => SellerAccount::query()->findOrFail($id),
            'rider' => CourierAccount::query()->findOrFail($id),
            'logistics' => LogisticsAccount::query()->findOrFail($id),
            default => abort(404),
        };

        return $this->identityFromModel(
            $role,
            $model
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function identityFromModel(
        string $role,
        object $model
    ): array {
        $name = match ($role) {
            'admin' =>
                $model->name ?: 'SARI Administrator',
            'buyer' => trim(
                $model->first_name . ' ' .
                $model->last_name
            ),
            'social_buyer' =>
                $model->name ?: 'Social Buyer',
            'seller' =>
                $model->store_name ?: $model->email,
            'rider' => trim(
                $model->first_name . ' ' .
                $model->last_name
            ),
            'logistics' =>
                $this->logisticsName($model),
            default =>
                ucfirst($role) . ' #' . $model->getKey(),
        };

        $status = match ($role) {
            'admin', 'social_buyer' => 'active',
            default => strtolower(
                (string) (
                    $model->account_status
                    ?: 'active'
                )
            ),
        };

        return [
            'role' => $role,
            'id' => (int) $model->getKey(),
            'name' => $name ?: ucfirst($role) . ' #' . $model->getKey(),
            'email' => $model->email ?? null,
            'status' => $status,
            'model' => $model,
        ];
    }

    private function identityLabel(
        string $role,
        int $id
    ): string {
        try {
            return $this->resolveIdentity(
                $this->normalizeRole($role),
                $id
            )['name'];
        } catch (\Throwable) {
            return ucfirst(
                str_replace('_', ' ', $role)
            ) . ' #' . $id;
        }
    }

    private function logisticsName(
        LogisticsAccount $account
    ): string {
        return method_exists(
            $account,
            'displayName'
        )
            ? $account->displayName()
            : (
                $account->business_name
                ?: trim(
                    $account->first_name .
                    ' ' .
                    $account->last_name
                )
                ?: 'SARI Logistics'
            );
    }

    /**
     * @param array<string,mixed> $actor
     * @return array<string,mixed>
     */
    private function publicActor(
        array $actor
    ): array {
        return [
            'role' => $actor['role'],
            'id' => $actor['id'],
            'name' => $actor['name'],
            'email' => $actor['email'],
            'status' => $actor['status'],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function contactPayload(
        string $role,
        int $id,
        string $name,
        ?string $email
    ): array {
        return [
            'role' => $role,
            'id' => $id,
            'name' => $name ?: ucfirst($role) . ' #' . $id,
            'email' => $email,
        ];
    }

    /**
     * @param array<string,mixed> $actor
     * @return array<string,mixed>
     */
    private function conversationSummary(
        PlatformConversation $conversation,
        array $actor
    ): array {
        $ownParticipant = $conversation->participants
            ->first(
                fn (PlatformConversationParticipant $row): bool =>
                    $row->participant_role === $actor['role']
                    && (int) $row->participant_id === (int) $actor['id']
                    && !$row->left_at
            );

        $lastMessage = $conversation->latestMessage;
        $unreadCount = 0;

        if ($ownParticipant && $lastMessage) {
            $lastReadId =
                (int) ($ownParticipant->last_read_message_id ?? 0);

            $unreadCount = PlatformMessage::query()
                ->where(
                    'platform_conversation_id',
                    $conversation->id
                )
                ->where('id', '>', $lastReadId)
                ->whereNull('deleted_at')
                ->where(function (Builder $query) use ($actor): void {
                    $query
                        ->where(
                            'sender_role',
                            '!=',
                            $actor['role']
                        )
                        ->orWhere(
                            'sender_id',
                            '!=',
                            $actor['id']
                        );
                })
                ->count();
        }

        return [
            'id' => $conversation->id,
            'uuid' => $conversation->uuid,
            'type' => $conversation->conversation_type,
            'context' => [
                'type' => $conversation->context_type,
                'id' => $conversation->context_id,
            ],
            'subject' => $conversation->subject,
            'status' => $conversation->status,
            'participants' => $conversation->participants
                ->whereNull('left_at')
                ->map(
                    fn (PlatformConversationParticipant $row): array =>
                        $this->participantPayload($row)
                )
                ->values(),
            'last_message' => $lastMessage
                ? $this->messagePayload($lastMessage)
                : null,
            'unread_count' => $unreadCount,
            'last_message_at' =>
                $conversation->last_message_at?->toIso8601String(),
            'created_at' =>
                $conversation->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function participantPayload(
        PlatformConversationParticipant $participant
    ): array {
        return [
            'role' => $participant->participant_role,
            'id' => $participant->participant_id,
            'label' => $this->identityLabel(
                $participant->participant_role,
                (int) $participant->participant_id
            ),
            'joined_at' =>
                $participant->joined_at?->toIso8601String(),
            'last_read_message_id' =>
                $participant->last_read_message_id,
        ];
    }

    private function normalizeRole(
        string $role
    ): string {
        $normalized = match (
            strtolower(trim($role))
        ) {
            'courier' => 'rider',
            'buyer_social', 'social' => 'social_buyer',
            default => strtolower(trim($role)),
        };

        if (!in_array($normalized, self::ROLES, true)) {
            throw ValidationException::withMessages([
                'target_role' => 'Unsupported messaging role.',
            ]);
        }

        return $normalized;
    }
}
