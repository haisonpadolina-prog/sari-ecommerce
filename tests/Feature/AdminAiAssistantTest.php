<?php

use App\Events\SellerRealtimeAlert;
use App\Jobs\DispatchAdminSellerRealtime;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Finance\CodRemittanceBatch;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\SellerReturnRequest;
use App\Models\Platform\AdminAiAction;
use App\Models\Platform\AdminAiThread;
use App\Models\Platform\PlatformAnnouncement;
use App\Models\Platform\PlatformComplaint;
use App\Services\Admin\Ai\AdminAiActionService;
use App\Services\Admin\Ai\AdminAiDataService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['app.key' => 'base64:'.base64_encode(str_repeat('t', 32)), 'admin_ai.enabled' => true,
        'admin_ai.api_key' => 'test-only-key', 'admin_ai.model' => 'gemini-3.5-flash-lite', 'sari.assistant.api_key' => null, 'admin_ai.allow_actions' => true]);
    Http::preventStrayRequests();
    $this->admin = AdminAccount::query()->create(['name' => 'Test Admin', 'email' => 'ai-admin@example.test', 'password' => bcrypt('testing-only'), 'role' => 'super_admin']);
    $this->session = ['is_admin' => true, 'admin_account_id' => $this->admin->id];
});

it('rejects unauthenticated and stale administrator sessions', function () {
    $this->getJson('/admin/ai/status')->assertUnauthorized();
    $this->withSession(['is_admin' => true, 'admin_account_id' => 999999])->getJson('/admin/ai/status')->assertForbidden();
});

it('returns configuration state without revealing the API key', function () {
    $response = $this->withSession($this->session)->getJson('/admin/ai/status')->assertOk()
        ->assertJson(['configured' => true, 'actions_enabled' => true])->assertDontSee('test-only-key');
    expect($response->headers->get('Cache-Control'))->toContain('no-store')->toContain('private');
});

it('reports missing configuration without contacting the provider', function () {
    config(['admin_ai.api_key' => null]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Kumusta?', 'language' => 'Tagalog'])->assertStatus(503);
    Http::assertNothingSent();
});

it('validates empty input and unsupported language', function () {
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => '', 'language' => 'fake'])->assertUnprocessable();
    Http::assertNothingSent();
});

it('supports Tagalog and encrypts server-owned conversation history', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Kumusta! Ano ang maitutulong ko?'))]);
    $response = $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Kumusta?', 'language' => 'Tagalog'])->assertOk()->assertJsonPath('message', 'Kumusta! Ano ang maitutulong ko?');
    $thread = AdminAiThread::query()->findOrFail($response->json('thread_id'));
    expect($thread->getRawOriginal('messages'))->not->toContain('Kumusta');
    Http::assertSent(fn ($r) => str_contains($r['systemInstruction']['parts'][0]['text'], 'Preferred language: Tagalog') && $r->hasHeader('x-goog-api-key', 'test-only-key'));
});

it('prevents another admin from reading or deleting a thread', function () {
    $thread = AdminAiThread::query()->create(['id' => (string) Str::uuid(), 'admin_account_id' => $this->admin->id, 'messages' => []]);
    $other = AdminAccount::query()->create(['email' => 'other@example.test', 'password' => bcrypt('test')]);
    $this->withSession(['is_admin' => true, 'admin_account_id' => $other->id])
        ->deleteJson('/admin/ai/threads/'.$thread->id)->assertNotFound();
});

it('executes read tool calls against actual records without exposing hidden fields', function () {
    SellerAccount::query()->create(['email' => 'secret-email@example.test', 'store_name' => 'Actual Store', 'password' => 'never-export-this']);
    Http::fakeSequence()->push(['candidates' => [['content' => ['role' => 'model', 'parts' => [[
        'functionCall' => ['id' => 'call_1', 'name' => 'read_data', 'args' => ['operation' => 'accounts', 'filters' => ['role' => 'seller']]],
        'thoughtSignature' => 'opaque-gemini-signature',
    ]]]]]])->push(sariAdminGeminiText('Nakita ko ang Actual Store.'));
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Show sellers', 'language' => 'Taglish'])
        ->assertOk()->assertJsonPath('artifacts.0.rows.0.store', 'Actual Store')
        ->assertDontSee('secret-email@example.test')->assertDontSee('never-export-this');
});

it('prepares but never executes an announcement through chat', function () {
    Http::fakeSequence()->push(['candidates' => [['content' => ['role' => 'model', 'parts' => [[
        'functionCall' => ['id' => 'call_1', 'name' => 'prepare_action', 'args' => ['type' => 'announcement', 'title' => 'Test notice', 'message' => 'Review this notice', 'audiences' => ['seller'], 'publish' => true]],
        'thoughtSignature' => 'opaque-gemini-signature',
    ]]]]]])->push(sariAdminGeminiText('Please review the preview.'));
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Prepare announcement', 'language' => 'English'])
        ->assertOk()->assertJsonPath('artifacts.0.proposal.type', 'announcement');
    expect(PlatformAnnouncement::query()->count())->toBe(0);
    expect(AdminAiAction::query()->first()->status)->toBe('pending');
});

it('requires confirmation and executes each proposal once', function () {
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'announcement', 'title' => 'Notice', 'message' => 'Hello sellers', 'audiences' => ['seller'], 'publish' => true]);
    $url = '/admin/ai/actions/'.$proposal['id'];
    $this->withSession($this->session)->postJson($url, [])->assertUnprocessable();
    $this->postJson($url, ['confirm' => true])->assertOk();
    $this->postJson($url, ['confirm' => true])->assertOk();
    expect(PlatformAnnouncement::query()->count())->toBe(1);
    expect(AdminAiAction::query()->first()->status)->toBe('executed');
    $this->assertDatabaseHas('admin_user_activities', ['action' => 'ai_confirmed_action']);
});

it('rejects expired and cross-admin proposals', function () {
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'announcement', 'title' => 'Notice', 'message' => 'Hello sellers', 'audiences' => ['seller']]);
    $other = AdminAccount::query()->create(['email' => 'second@example.test', 'password' => bcrypt('test'), 'role' => 'super_admin']);
    $this->withSession(['is_admin' => true, 'admin_account_id' => $other->id])->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertNotFound();
    AdminAiAction::query()->find($proposal['id'])->update(['expires_at' => now()->subMinute()]);
    $this->withSession($this->session)->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertStatus(409);
});

it('rejects stale previews and leaves the record unchanged', function () {
    $seller = SellerAccount::query()->create(['email' => 'stale@example.test', 'store_name' => 'Original', 'account_status' => 'active']);
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'block_account', 'ids' => [$seller->id], 'role' => 'seller', 'reason' => 'Confirmed policy breach']);
    $seller->update(['store_name' => 'Changed']);
    $this->withSession($this->session)->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertStatus(409);
    expect($seller->fresh()->account_status)->toBe('active');
});

it('does not allow ordinary administrators to execute AI changes', function () {
    $this->admin->update(['role' => 'admin']);
    expect(fn () => app(AdminAiActionService::class)->prepare($this->admin->fresh(), ['type' => 'announcement']))
        ->toThrow(HttpException::class);
});

it('rejects unsupported destructive actions and bulk selections above ten', function () {
    expect(fn () => app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'refund']))->toThrow(ValidationException::class);
    expect(fn () => app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'ban_seller', 'ids' => range(1, 11), 'reason' => 'Confirmed breach']))->toThrow(ValidationException::class);
});

it('returns bounded real low-stock product cards with protected image links', function () {
    $seller = SellerAccount::query()->create(['email' => 'products@example.test', 'store_name' => 'Inventory']);
    SellerProduct::query()->create(['seller_account_id' => $seller->id, 'name' => 'Low inventory', 'category' => 'Fashion', 'price' => 100, 'stock' => 2, 'low_stock_threshold' => 5]);
    $data = app(AdminAiDataService::class)->run('products', ['low_stock' => true]);
    expect($data['total'])->toBe(1)->and($data['rows'][0]['image_url'])->toContain('/admin/');
});

it('excludes pending and cancelled orders from delivered GMV', function () {
    $seller = SellerAccount::query()->create(['email' => 'gmv@example.test']);
    sariFinanceOrder($seller, 'delivered', 100);
    sariFinanceOrder($seller, 'cancelled', 900);
    $today = now('Asia/Manila')->format('Y-m-d');
    $data = app(AdminAiDataService::class)->run('analytics', ['from' => $today, 'to' => $today, 'metric' => 'gmv', 'group' => 'day']);
    expect((float) $data['total'])->toBe(180.0);
});

it('does not expose provider error bodies or credentials', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'sensitive-provider-body']], 401)]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Hello', 'language' => 'English'])
        ->assertStatus(502)->assertDontSee('sensitive-provider-body')->assertDontSee('test-only-key');
});

it('executes existing account workflows with the previewed reason', function () {
    $buyer = SocialAccount::query()->create(['provider' => 'google', 'provider_user_id' => 'ai-test-1', 'email' => 'social@example.test', 'name' => 'Test Buyer', 'account_status' => 'active']);
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'block_account', 'ids' => [$buyer->id], 'role' => 'social_buyer', 'reason' => 'Confirmed policy breach']);
    $this->withSession($this->session)->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertOk();
    expect($buyer->fresh()->account_status)->toBe('blocked');
});

it('cancels proposals without writing announcements', function () {
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'announcement', 'title' => 'Notice', 'message' => 'Hello', 'audiences' => ['buyer']]);
    $this->withSession($this->session)->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => false])->assertOk();
    $this->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertStatus(409);
    expect(PlatformAnnouncement::query()->count())->toBe(0);
});

it('rejects unknown filters rather than silently ignoring the requested scope', function () {
    expect(fn () => app(AdminAiDataService::class)->run('products', ['unsupported' => true]))->toThrow(InvalidArgumentException::class);
});

it('cleans abandoned history while retaining executed action evidence', function () {
    $thread = AdminAiThread::query()->create(['id' => (string) Str::uuid(), 'admin_account_id' => $this->admin->id, 'messages' => []]);
    $thread->timestamps = false;
    $thread->updated_at = now()->subDays(40);
    $thread->save();
    $this->artisan('admin-ai:maintenance')->assertSuccessful();
    expect(AdminAiThread::query()->find($thread->id))->toBeNull();
});

it('approves a product using the existing moderation workflow and queues committed realtime work', function () {
    Queue::fake();
    $seller = SellerAccount::query()->create(['email' => 'approve@example.test', 'store_name' => 'Review Store', 'account_status' => 'active', 'store_status' => 'open']);
    $product = SellerProduct::query()->create(['seller_account_id' => $seller->id, 'name' => 'Review item', 'category' => 'Fashion', 'price' => 100, 'stock' => 4, 'moderation_status' => 'pending']);
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'approve_product', 'ids' => [$product->id]]);
    $this->withSession($this->session)->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertOk();
    expect($product->fresh()->moderation_status)->toBe('approved');
    Queue::assertPushed(DispatchAdminSellerRealtime::class, fn ($job) => $job->afterCommit === true && $job->sellerId === $seller->id);
});

it('dispatches the existing seller event from the restored realtime job', function () {
    Event::fake([SellerRealtimeAlert::class]);
    $seller = SellerAccount::query()->create(['email' => 'event@example.test', 'store_name' => 'Event Store']);
    $job = new DispatchAdminSellerRealtime($seller->id, 'alert', ['message' => 'Confirmed message']);
    $job->handle();
    Event::assertDispatched(SellerRealtimeAlert::class, fn ($event) => $event->message === 'Confirmed message');
});

function sariAdminGeminiText(string $text): array
{
    return ['candidates' => [['content' => ['role' => 'model', 'parts' => [['text' => $text]]], 'finishReason' => 'STOP']]];
}

it('reuses the Buyer Gemini configuration without an OpenAI key', function () {
    config(['admin_ai.api_key' => null, 'admin_ai.model' => null,
        'sari.assistant.api_key' => 'existing-gemini-key', 'sari.assistant.model' => 'gemini-3.5-flash-lite']);
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Handa na ako.'))]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Kumusta', 'language' => 'Tagalog'])
        ->assertOk()->assertJsonPath('message', 'Handa na ako.');
    Http::assertSent(fn ($r) => str_contains($r->url(), '/models/gemini-3.5-flash-lite:generateContent') && $r->hasHeader('x-goog-api-key', 'existing-gemini-key'));
});

it('preserves Gemini thought signatures and matching function response IDs', function () {
    Http::fakeSequence()->push(['candidates' => [['content' => ['role' => 'model', 'parts' => [[
        'functionCall' => ['id' => 'signed-call', 'name' => 'read_data', 'args' => ['operation' => 'guide', 'filters' => []]],
        'thoughtSignature' => 'exact-opaque-signature',
    ]]]]]])->push(sariAdminGeminiText('Narito ang guide.'));
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Show guide', 'language' => 'Tagalog'])->assertOk();
    Http::assertSent(fn ($r) => ($r['contents'][1]['parts'][0]['thoughtSignature'] ?? '') === 'exact-opaque-signature'
        && ($r['contents'][2]['parts'][0]['functionResponse']['id'] ?? '') === 'signed-call');
});

it('discards legacy provider history when switching to Gemini', function () {
    $thread = AdminAiThread::query()->create(['id' => (string) Str::uuid(), 'admin_account_id' => $this->admin->id,
        'messages' => [[['role' => 'user', 'content' => 'old-provider-content']]]]);
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('New Gemini chat.'))]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['thread_id' => $thread->id, 'message' => 'Hello', 'language' => 'English'])->assertOk();
    Http::assertSent(fn ($r) => count($r['contents']) === 1 && ! str_contains(json_encode($r['contents']), 'old-provider-content'));
});

it('reports Gemini quota and invalid model errors without leaking provider bodies', function () {
    Http::fakeSequence()->push(['error' => ['message' => 'private raw provider error']], 429)
        ->push(['error' => ['message' => 'private raw provider error']], 404);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Hello', 'language' => 'English'])
        ->assertStatus(502)->assertSee('quota')->assertDontSee('private raw provider error');
    $this->postJson('/admin/ai/chat', ['message' => 'Hello again', 'language' => 'English'])
        ->assertStatus(502)->assertSee('gemini-3.5-flash-lite')->assertDontSee('private raw provider error');
});

it('makes explicit English override a Tagalog message and prior language', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Here is your summary.'))]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Ipakita ang buod', 'language' => 'English'])->assertOk();
    Http::assertSent(fn ($r) => str_contains($r['systemInstruction']['parts'][0]['text'], 'Reply in English only')
        && str_contains($r['systemInstruction']['parts'][0]['text'], 'overrides the message language'));
});

it('makes explicit Tagalog override an English message', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Narito ang buod.'))]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Show the summary', 'language' => 'Tagalog'])->assertOk();
    Http::assertSent(fn ($r) => str_contains($r['systemInstruction']['parts'][0]['text'], 'Sumagot sa Tagalog lamang'));
});

it('instructs Auto to follow the latest message independently on each turn', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Here is your summary.'))]);
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Show the summary', 'language' => 'auto'])->assertOk();
    Http::assertSent(fn ($r) => str_contains($r['systemInstruction']['parts'][0]['text'], 'Match the language of the LATEST user message')
        && str_contains($r['systemInstruction']['parts'][0]['text'], 'An English message requires an English answer'));
});

it('translates display messages without offering tools or creating admin proposals', function () {
    $translated = ['messages' => [['id' => 7, 'text' => 'Ipakita ang order #123 na may halagang PHP 500.']]];
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText(json_encode($translated)))]);
    $this->withSession($this->session)->postJson('/admin/ai/translate', ['language' => 'Tagalog', 'messages' => [['id' => 7, 'text' => 'Show order #123 worth PHP 500.']]])
        ->assertOk()->assertJsonPath('messages.0.id', 7)->assertJsonPath('messages.0.text', $translated['messages'][0]['text']);
    Http::assertSent(fn ($r) => ! isset($r['tools']) && $r['generationConfig']['responseMimeType'] === 'application/json'
        && str_contains($r['systemInstruction']['parts'][0]['text'], 'translation-only'));
    expect(AdminAiAction::query()->count())->toBe(0);
    expect(AdminAiThread::query()->count())->toBe(0);
});

it('protects and bounds the translation endpoint', function () {
    $this->postJson('/admin/ai/translate', ['language' => 'English', 'messages' => [['id' => 1, 'text' => 'Hello']]])->assertUnauthorized();
    $this->withSession($this->session)->postJson('/admin/ai/translate', ['language' => 'auto', 'messages' => []])->assertUnprocessable();
    $this->postJson('/admin/ai/translate', ['language' => 'English', 'messages' => array_map(fn ($i) => ['id' => $i, 'text' => 'Hello'], range(1, 9))])->assertUnprocessable();
    Http::assertNothingSent();
});

it('rejects malformed translation IDs and provider response bodies', function () {
    Http::fakeSequence()->push(sariAdminGeminiText(json_encode(['messages' => [['id' => 999, 'text' => 'Maling ID']]])))
        ->push(['error' => ['message' => 'private Gemini diagnostic']], 403);
    $payload = ['language' => 'Tagalog', 'messages' => [['id' => 1, 'text' => 'Hello']]];
    $this->withSession($this->session)->postJson('/admin/ai/translate', $payload)->assertStatus(502);
    $this->postJson('/admin/ai/translate', $payload)->assertStatus(502)->assertDontSee('private Gemini diagnostic')->assertDontSee('test-only-key');
});

it('allows six chat requests without double counting then returns a retry time', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Ready.'))]);
    for ($i = 0; $i < 6; $i++) {
        $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Hello', 'language' => 'English'])->assertOk();
    }
    $response = $this->postJson('/admin/ai/chat', ['message' => 'One more', 'language' => 'English'])
        ->assertStatus(429)->assertJsonPath('code', 'admin_ai_rate_limited')->assertHeader('Retry-After');
    expect($response->json('retry_after'))->toBeGreaterThan(0);
    Http::assertSentCount(6);
});

it('keeps status and translation traffic out of the chat counter', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText(json_encode(['messages' => [['id' => 1, 'text' => 'Hello']]])))]);
    for ($i = 0; $i < 20; $i++) {
        $this->withSession($this->session)->getJson('/admin/ai/status')->assertOk();
    }
    for ($i = 0; $i < 6; $i++) {
        $this->postJson('/admin/ai/translate', ['language' => 'English', 'messages' => [['id' => 1, 'text' => 'Kumusta']]])->assertOk();
    }
    $this->postJson('/admin/ai/translate', ['language' => 'English', 'messages' => [['id' => 1, 'text' => 'Kumusta']]])->assertStatus(429);
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Graph ready.'))]);
    $this->postJson('/admin/ai/chat', ['message' => 'Graph daily GMV', 'language' => 'English'])->assertOk();
});

it('does not share chat limits between administrators on the same IP', function () {
    Http::fake(['generativelanguage.googleapis.com/*' => Http::response(sariAdminGeminiText('Ready.'))]);
    for ($i = 0; $i < 6; $i++) {
        $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Hello', 'language' => 'English'])->assertOk();
    }
    $other = AdminAccount::query()->create(['name' => 'Other Admin', 'email' => 'rate-other@example.test', 'password' => bcrypt('test'), 'role' => 'super_admin']);
    $this->withSession(['is_admin' => true, 'admin_account_id' => $other->id])->postJson('/admin/ai/chat', ['message' => 'Hello', 'language' => 'English'])->assertOk();
});

it('returns seven zero-filled Manila dates when a GMV period has no deliveries', function () {
    $data = app(AdminAiDataService::class)->run('analytics', ['from' => '2026-11-05', 'to' => '2026-11-11', 'metric' => 'gmv', 'group' => 'day']);
    expect($data['rows'])->toHaveCount(7);
    expect(array_column($data['rows'], 'label'))->toBe(['2026-11-05', '2026-11-06', '2026-11-07', '2026-11-08', '2026-11-09', '2026-11-10', '2026-11-11']);
    expect(array_sum(array_column($data['rows'], 'value')))->toBe(0.0);
    expect($data['matching_records'])->toBe(0)->and($data['chart']['date_basis'])->toBe('delivered_at');
});

it('counts GMV by delivery date even when the order was created in an earlier month', function () {
    $seller = SellerAccount::query()->create(['email' => 'delivery-basis@example.test']);
    $order = sariFinanceOrder($seller, 'delivered', 100, CarbonImmutable::parse('2026-11-06 04:00:00', 'UTC'));
    $order->timestamps = false;
    $order->forceFill(['created_at' => '2026-10-01 00:00:00'])->save();
    $data = app(AdminAiDataService::class)->run('analytics', ['from' => '2026-11-05', 'to' => '2026-11-11', 'metric' => 'gmv', 'group' => 'day']);
    expect($data['total'])->toBe(180.0)->and($data['matching_records'])->toBe(1);
    expect(collect($data['rows'])->firstWhere('label', '2026-11-06')['value'])->toBe(180.0);
});

it('groups late UTC deliveries into the next Manila day', function () {
    $seller = SellerAccount::query()->create(['email' => 'manila-chart@example.test']);
    sariFinanceOrder($seller, 'delivered', 100, CarbonImmutable::parse('2026-11-04 16:30:00', 'UTC'));
    $data = app(AdminAiDataService::class)->run('analytics', ['from' => '2026-11-05', 'to' => '2026-11-05', 'metric' => 'gmv', 'group' => 'day']);
    expect($data['rows'])->toBe([['label' => '2026-11-05', 'value' => 180.0]]);
    expect($data['chart']['timezone'])->toBe('Asia/Manila');
});

it('keeps order-count analytics on the actual order creation date', function () {
    $seller = SellerAccount::query()->create(['email' => 'order-count-chart@example.test']);
    $order = sariFinanceOrder($seller, 'delivered', 100, CarbonImmutable::parse('2026-11-10 00:00:00', 'UTC'));
    $order->timestamps = false;
    $order->forceFill(['created_at' => '2026-11-05 02:00:00'])->save();
    $data = app(AdminAiDataService::class)->run('analytics', ['from' => '2026-11-05', 'to' => '2026-11-06', 'metric' => 'orders', 'group' => 'day']);
    expect($data['total'])->toBe(1.0)->and($data['chart']['date_basis'])->toBe('created_at');
    expect($data['rows'])->toBe([['label' => '2026-11-05', 'value' => 1.0], ['label' => '2026-11-06', 'value' => 0.0]]);
});

it('reports undated delivered records without fabricating a delivery date', function () {
    $seller = SellerAccount::query()->create(['email' => 'undated-chart@example.test']);
    $order = sariFinanceOrder($seller, 'delivered', 100);
    $order->forceFill(['delivered_at' => null])->save();
    $data = app(AdminAiDataService::class)->run('analytics', ['from' => '2026-11-05', 'to' => '2026-11-11', 'metric' => 'gmv', 'group' => 'day']);
    expect($data['total'])->toBe(0.0)->and($data['matching_records'])->toBe(0);
    expect($data['chart']['undated_delivered_records'])->toBe(1);
});

it('provides permission-scoped action center without Gemini', function () {
    $seller = SellerAccount::query()->create(['email' => 'center@example.test', 'store_name' => 'Center']);
    SellerProduct::query()->create(['seller_account_id' => $seller->id, 'name' => 'Pending', 'category' => 'Fashion', 'price' => 100, 'stock' => 4, 'moderation_status' => 'pending']);
    PlatformComplaint::query()->create(['reporter_role' => 'buyer', 'reporter_identifier' => 'do-not-export', 'subject' => 'Issue', 'description' => 'Details', 'status' => 'open']);
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'action_center', 'filters' => []])
        ->assertOk()->assertJsonPath('rows.0.value', 1);
    $this->admin->forceFill(['role' => 'admin', 'ai_role' => 'product_reviewer'])->save();
    $response = $this->postJson('/admin/ai/read', ['operation' => 'action_center', 'filters' => []])->assertOk();
    expect(array_column($response->json('rows'), 'id'))->toBe(['products']);
    $this->postJson('/admin/ai/read', ['operation' => 'complaints', 'filters' => []])->assertForbidden();
    $this->postJson('/admin/ai/read', ['operation' => 'audit', 'filters' => []])->assertForbidden();
    Http::assertNothingSent();
});

it('assigns AI roles only by super admin confirmation and clears old context', function () {
    $target = AdminAccount::query()->create(['name' => 'Reviewer', 'email' => 'reviewer@example.test', 'password' => bcrypt('test'), 'role' => 'admin']);
    $thread = AdminAiThread::query()->create(['id' => (string) Str::uuid(), 'admin_account_id' => $target->id, 'messages' => [['private old context']]]);
    $proposal = app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'assign_ai_role', 'ids' => [$target->id], 'ai_role' => 'product_reviewer']);
    expect($target->fresh()->ai_role)->toBeNull();
    $this->withSession($this->session)->postJson('/admin/ai/actions/'.$proposal['id'], ['confirm' => true])->assertOk();
    expect($target->fresh()->ai_role)->toBe('product_reviewer');
    expect($thread->fresh()->messages)->toBe([]);
    $this->withSession(['is_admin' => true, 'admin_account_id' => $target->id])->postJson('/admin/ai/prepare', ['type' => 'assign_ai_role', 'ids' => [$target->id], 'ai_role' => 'finance'])->assertForbidden();
});

it('allows reviewer bulk previews and rejects invalid review states atomically', function () {
    Queue::fake();
    Event::fake();
    $seller = SellerAccount::query()->create(['email' => 'bulk@example.test', 'store_name' => 'Bulk', 'account_status' => 'active']);
    $products = collect(['pending', 'flagged'])->map(fn ($status) => SellerProduct::query()->create(['seller_account_id' => $seller->id, 'name' => 'Bulk '.$status, 'category' => 'Fashion', 'price' => 100, 'stock' => 4, 'moderation_status' => $status]));
    $this->admin->forceFill(['role' => 'admin', 'ai_role' => 'product_reviewer'])->save();
    $ids = $products->pluck('id')->all();
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'products', 'filters' => ['status' => 'review']])->assertOk()->assertJsonPath('total', 2);
    $preview = $this->postJson('/admin/ai/prepare', ['type' => 'approve_product', 'ids' => $ids])->assertOk()->json('proposal.id');
    expect($products[0]->fresh()->moderation_status)->toBe('pending');
    $products[1]->update(['moderation_status' => 'approved']);
    $this->postJson('/admin/ai/actions/'.$preview, ['confirm' => true])->assertStatus(409);
    expect($products[0]->fresh()->moderation_status)->toBe('pending');
    $action = AdminAiAction::query()->findOrFail($preview);
    expect($action->status)->toBe('failed');
    expect($action->result['ok'])->toBeFalse();
    $this->postJson('/admin/ai/prepare', ['type' => 'reject_product', 'ids' => [$ids[0]], 'reason' => '  '])->assertUnprocessable();
    $this->postJson('/admin/ai/prepare', ['type' => 'announcement', 'title' => 'No', 'message' => 'No', 'audiences' => ['seller']])->assertForbidden();
});

it('returns real seller cohort metrics with Manila dates', function () {
    Event::fake();
    $seller = SellerAccount::query()->create(['email' => 'metrics@example.test', 'store_name' => 'Metrics']);
    foreach (['delivered', 'cancelled'] as $index => $status) {
        MarketplaceOrder::query()->create(['order_number' => 'MET-'.$index, 'seller_account_id' => $seller->id, 'buyer_name' => 'Buyer', 'buyer_address' => 'Test', 'status' => $status, 'total' => 120, 'delivered_at' => $status === 'delivered' ? '2026-11-11 01:00:00' : null])->forceFill(['created_at' => '2026-11-10 01:00:00'])->save();
    }
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'seller_performance', 'filters' => ['from' => '2026-11-10', 'to' => '2026-11-11']])->assertOk()
        ->assertJsonPath('rows.0.orders', 2)->assertJsonPath('rows.0.cancellation_rate_percent', 50)->assertJsonPath('rows.0.average_fulfillment_hours', 24)->assertJsonPath('chart.rows.0.value', 120);
    $this->postJson('/admin/ai/read', ['operation' => 'seller_performance', 'filters' => []])->assertUnprocessable();
});

it('does not invent complaint links and keeps history owner scoped', function () {
    PlatformComplaint::query()->create(['reporter_role' => 'buyer', 'reporter_identifier' => 'private-identity', 'subject' => 'Issue', 'description' => 'Details', 'status' => 'open']);
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'complaints', 'filters' => []])->assertOk()->assertDontSee('private-identity')->assertJsonPath('rows.0.subject', 'Issue');
    app(AdminAiActionService::class)->prepare($this->admin, ['type' => 'announcement', 'title' => 'Notice', 'message' => 'Hello', 'audiences' => ['seller']]);
    $other = AdminAccount::query()->create(['email' => 'history@example.test', 'password' => bcrypt('test'), 'role' => 'admin']);
    $this->withSession(['is_admin' => true, 'admin_account_id' => $other->id])->postJson('/admin/ai/read', ['operation' => 'action_history', 'filters' => []])->assertOk()->assertJsonPath('total', 0);
    $this->postJson('/admin/ai/read', ['operation' => 'admin_permissions', 'filters' => []])->assertForbidden();
});

it('executes new tools through Gemini while enforcing finance read boundaries', function () {
    $this->admin->forceFill(['role' => 'admin', 'ai_role' => 'finance'])->save();
    Http::fakeSequence()->push(['candidates' => [['content' => ['role' => 'model', 'parts' => [[
        'functionCall' => ['name' => 'read_data', 'args' => ['operation' => 'cod_remittances', 'filters' => []]],
    ]]]]]])->push(sariAdminGeminiText('No matching batches.'));
    $this->withSession($this->session)->postJson('/admin/ai/chat', ['message' => 'Check COD', 'language' => 'English'])->assertOk()->assertJsonPath('artifacts.0.source', 'cod_remittances')->assertJsonPath('artifacts.0.total', 0);
    $this->postJson('/admin/ai/read', ['operation' => 'orders', 'filters' => []])->assertForbidden();
    Http::assertSent(fn ($r) => ! in_array('prepare_action', array_column($r['tools'][0]['functionDeclarations'], 'name')));
});

it('handles empty monitors and rejects unsupported operation filters', function () {
    foreach (['logistics_exceptions', 'returns', 'cod_remittances', 'action_history'] as $operation) {
        $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => $operation, 'filters' => []])->assertOk()->assertJsonPath('total', 0);
    }
    foreach (['branch', 'admin'] as $stage) {
        $this->postJson('/admin/ai/read', ['operation' => 'cod_remittances', 'filters' => ['stage' => $stage]])->assertOk()->assertJsonPath('total', 0);
    }
    $this->postJson('/admin/ai/read', ['operation' => 'complaints', 'filters' => ['seller_id' => 1]])->assertUnprocessable();
    $this->postJson('/admin/ai/read', ['operation' => 'cod_remittances', 'filters' => ['stage' => 'admin', 'courier_id' => 1]])->assertUnprocessable();
});

it('reports stale and hub exceptions from recorded timestamps and parcel state', function () {
    Event::fake();
    $seller = SellerAccount::query()->create(['email' => 'stale-monitor@example.test']);
    $order = MarketplaceOrder::query()->create(['order_number' => 'ST-MON', 'seller_account_id' => $seller->id, 'buyer_name' => 'Buyer', 'buyer_address' => 'Test', 'status' => 'in_transit']);
    $order->forceFill(['updated_at' => now()->subHours(72)])->save();
    LogisticsParcel::query()->create(['marketplace_order_id' => $order->id, 'status' => 'received', 'received_at' => now()->subHours(72)]);
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'logistics_exceptions', 'filters' => ['kind' => 'hub']])->assertOk()->assertJsonPath('total', 1)->assertJsonPath('rows.0.id', $order->id);
    $order->update(['status' => 'delivered']);
    $this->postJson('/admin/ai/read', ['operation' => 'logistics_exceptions', 'filters' => ['kind' => 'hub']])->assertOk()->assertJsonPath('total', 0);
});

it('reports return details and seller response without implying transferred funds', function () {
    Event::fake();
    $seller = SellerAccount::query()->create(['email' => 'return-monitor@example.test']);
    $order = MarketplaceOrder::query()->create(['order_number' => 'RET-MON', 'seller_account_id' => $seller->id, 'buyer_name' => 'Buyer', 'buyer_address' => 'Test', 'status' => 'delivered', 'total' => 100]);
    $return = SellerReturnRequest::query()->create(['marketplace_order_id' => $order->id, 'seller_account_id' => $seller->id, 'reason' => 'Wrong item', 'details' => 'Different color', 'requested_amount' => 100, 'status' => 'requested', 'seller_response' => 'Under review']);
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'returns', 'filters' => ['id' => $return->id]])->assertOk()->assertJsonPath('rows.0.order_id', $order->id)->assertJsonPath('rows.0.seller_response', 'Under review')->assertJsonPath('rows.0.refunded_at', null);
});

it('compares recorded COD amounts without adding overlapping custody stages', function () {
    $logistics = LogisticsAccount::query()->create(['first_name' => 'Test', 'last_name' => 'Provider', 'sex' => 'Male', 'email' => 'cash-provider@example.test', 'contact_no' => '09123456789', 'birthday' => '2000-01-01', 'age' => 26, 'province_code' => '04', 'province_name' => 'Laguna', 'municipality_code' => '1', 'municipality_name' => 'Test', 'barangay_code' => '1', 'barangay_name' => 'Test', 'street_address' => 'Test', 'password' => bcrypt('test')]);
    $rider = CourierAccount::query()->create(['first_name' => 'Test', 'last_name' => 'Rider', 'sex' => 'Male', 'email' => 'cash-rider@example.test', 'contact_no' => '09123456789', 'birthday' => '2000-01-01', 'age' => 26, 'province_code' => '04', 'province_name' => 'Laguna', 'municipality_code' => '1', 'municipality_name' => 'Test', 'barangay_code' => '1', 'barangay_name' => 'Test', 'street_address' => 'Test', 'password' => bcrypt('test'), 'vehicle_type' => 'Motorcycle', 'plate_number' => 'TEST123']);
    CodRemittanceBatch::query()->create(['logistics_account_id' => $logistics->id, 'courier_account_id' => $rider->id, 'batch_code' => 'COD-TEST', 'expected_amount' => 1000, 'submitted_amount' => 1000, 'confirmed_amount' => 900, 'difference_amount' => -100, 'currency' => 'PHP', 'status' => 'discrepancy', 'submitted_at' => '2026-11-10 01:00:00']);
    $this->withSession($this->session)->postJson('/admin/ai/read', ['operation' => 'cod_remittances', 'filters' => ['from' => '2026-11-10', 'to' => '2026-11-10', 'courier_id' => $rider->id]])->assertOk()->assertJsonPath('total', 1)->assertJsonPath('rows.0.unconfirmed_amount', 100)->assertJsonPath('rows.0.stage', 'rider');
});
