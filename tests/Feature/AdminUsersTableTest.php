<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('public');
});

function usersTableBuyer(): BuyerAccount
{
    $buyer = new BuyerAccount();
    $buyer->forceFill([
        'first_name' => 'Maria', 'last_name' => 'Santos', 'sex' => 'Female',
        'email' => 'table-buyer@example.test', 'contact_no' => '09171234567',
        'birthday' => '1998-01-01', 'age' => 28,
        'province_code' => '020900000', 'province_name' => 'Batanes',
        'municipality_code' => '020901000', 'municipality_name' => 'Basco',
        'barangay_code' => '020901002', 'barangay_name' => 'Ihubok I',
        'street_address' => 'Talangan', 'password' => Hash::make('Table@2026'),
        'account_status' => 'active', 'approved_at' => now(),
    ])->save();

    return $buyer;
}

it('shows an account reference and no fabricated sign in timestamp', function (): void {
    $buyer = usersTableBuyer();
    $this->withSession(['is_admin' => true])->get(route('admin.users'))
        ->assertOk()
        ->assertSee('BUYER-' . str_pad((string) $buyer->id, 3, '0', STR_PAD_LEFT))
        ->assertSee('No recorded sign-in')
        ->assertSee('userNextPage', false)
        ->assertSee('popovertarget=', false);
});

it('uses the latest login even when newer non login events fill the recent activity limit', function (): void {
    $buyer = usersTableBuyer();
    $loginTime = now()->subDays(3)->startOfSecond();
    $login = new AdminUserActivity();
    $login->forceFill([
        'user_role' => 'buyer', 'user_id' => $buyer->id,
        'action' => 'login', 'description' => 'Buyer signed in.',
        'created_at' => $loginTime, 'updated_at' => $loginTime,
    ])->save();
    $events = [];
    for ($i = 0; $i < 301; $i++) {
        $events[] = [
            'user_role' => 'buyer', 'user_id' => $buyer->id,
            'action' => 'profile_updated', 'description' => 'Profile changed.',
            'created_at' => now(), 'updated_at' => now(),
        ];
    }
    DB::table('admin_user_activities')->insert($events);

    $this->withSession(['is_admin' => true])->get(route('admin.users'))
        ->assertOk()->assertViewHas('accounts', function ($accounts) use ($buyer, $loginTime): bool {
            $row = $accounts->first(fn ($row) => $row['role_key'] === 'buyer' && $row['id'] === $buyer->id);

            return $row['last_sign_in_at']->equalTo($loginTime);
        });
});

it('serves the saved profile photo only to an admin session', function (): void {
    $buyer = usersTableBuyer();
    Storage::disk('public')->put('profile-images/table.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGNwaDgAAAKEAYEml6crAAAAAElFTkSuQmCC'));
    $buyer->forceFill(['profile_image_path' => 'profile-images/table.png'])->save();

    $url = route('admin.users.photo', ['buyer', $buyer->id]);
    $this->get($url)->assertForbidden();
    $this->withSession(['is_admin' => true])->get($url)->assertOk();
});

it('returns 404 for a missing photo instead of returning another file', function (): void {
    $buyer = usersTableBuyer();
    $this->withSession(['is_admin' => true])->get(route('admin.users.photo', ['buyer', $buyer->id]))
        ->assertNotFound();
});


it('passes the saved photo URL to the table and includes the separated account modal', function (): void {
    $buyer = usersTableBuyer();
    $buyer->forceFill(['profile_image_path' => 'profile-images/table.png'])->save();
    $this->withSession(['is_admin' => true])->get(route('admin.users'))
        ->assertOk()
        ->assertSee(route('admin.users.photo', ['buyer', $buyer->id]))
        ->assertSee('id="drawerPhotoState"', false)
        ->assertSee('data-close-user-modal', false);
});

it('returns 404 when the stored path no longer exists on disk', function (): void {
    $buyer = usersTableBuyer();
    $buyer->forceFill(['profile_image_path' => 'profile-images/deleted.png'])->save();
    $this->withSession(['is_admin' => true])->get(route('admin.users.photo', ['buyer', $buyer->id]))
        ->assertNotFound();
});
