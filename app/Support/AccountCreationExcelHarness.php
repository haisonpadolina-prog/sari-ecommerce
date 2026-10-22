<?php

namespace Tests\Support;

use App\Jobs\SendRegistrationDecisionEmail;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Registration\RegistrationApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AccountCreationExcelHarness
{
    public static function run(TestCase $test, string $id): void
    {
        if (str_starts_with($id, 'TC-BUY-')) {
            self::buyer($test, $id);
            return;
        }
        if (str_starts_with($id, 'TC-SEL-')) {
            self::seller($test, $id);
            return;
        }
        if (str_starts_with($id, 'TC-LOG-')) {
            self::logistics($test, $id);
            return;
        }
        if (str_starts_with($id, 'TC-RID-')) {
            self::rider($test, $id);
            return;
        }

        $test->fail("Unknown Excel black-box test case {$id}.");
    }

    private static function buyer(TestCase $test, string $id): void
    {
        $route = route('register.buyer.submit');
        $n = (int) substr($id, -3);

        switch ($n) {
            case 1:
                $email = 'juanmiguel.delacruz@example.com';
                $test->post($route, self::buyerWorkbookPayload())
                    ->assertSessionDoesntHaveErrors();
                $app = RegistrationApplication::query()->where('email', $email)->first();
                $test->assertNotNull($app, 'Registration must be persisted before OTP verification.');
                $test->assertSame('unverified', $app->status);
                $buyer = BuyerAccount::query()->where('email', $email)->first();
                $test->assertNotNull($buyer, 'Buyer account must exist in Unverified state before OTP verification.');
                $test->assertSame('unverified', $buyer->account_status);
                $test->assertNotNull(session('registration_email_otp.hash'));
                return;

            case 2:
                $test->post($route, [])->assertSessionHasErrors([
                    'first_name','last_name','email','contact_no','birthday','password','terms',
                ]);
                $test->assertDatabaseCount('buyer_accounts', 0);
                return;

            case 3:
                foreach (['first_name','last_name','email','contact_no','birthday','password','password_confirmation'] as $field) {
                    $payload = self::buyerIsolationPayload();
                    $payload[$field] = '';
                    if ($field === 'password') $payload['password_confirmation'] = '';
                    $test->post($route, $payload)->assertSessionHasErrors($field);
                }
                return;

            case 4:
                foreach ([['first_name','Juan99'], ['last_name','Dela Cruz@#']] as [$field,$value]) {
                    $payload = self::buyerIsolationPayload();
                    $payload[$field] = $value;
                    $test->post($route, $payload)->assertSessionHasErrors($field);
                }
                return;

            case 5:
                $payload = self::buyerIsolationPayload();
                $payload['first_name'] = 'Iñigo Rafael';
                $payload['last_name'] = "Muñoz-D'Angelo";
                $test->post($route, $payload)->assertSessionDoesntHaveErrors(['first_name','last_name']);
                return;

            case 6:
                foreach ([1 => true, 2 => false, 50 => false, 51 => true] as $length => $shouldFail) {
                    $payload = self::buyerIsolationPayload();
                    $payload['first_name'] = str_repeat('A', $length);
                    $response = $test->post($route, $payload);
                    $shouldFail ? $response->assertSessionHasErrors('first_name') : $response->assertSessionDoesntHaveErrors('first_name');
                }
                return;

            case 7:
                $payload = self::buyerIsolationPayload();
                $payload['birthday'] = now()->subYears(18)->addDay()->toDateString();
                $test->post($route, $payload)->assertSessionHasErrors('birthday');
                $payload['birthday'] = now()->subYears(18)->toDateString();
                $test->post($route, $payload)->assertSessionDoesntHaveErrors('birthday');
                $payload['birthday'] = now()->subYears(18)->subDay()->toDateString();
                $test->post($route, $payload)->assertSessionDoesntHaveErrors('birthday');
                return;

            case 8:
                $payload = self::buyerIsolationPayload();
                $payload['birthday'] = now()->addDay()->toDateString();
                $test->post($route, $payload)->assertSessionHasErrors('birthday');
                return;

            case 9:
                foreach (['juanmiguel.delacruzexample.com','juanmiguel.delacruz@','@example.com','juan@@example.com'] as $email) {
                    $payload = self::buyerIsolationPayload();
                    $payload['email'] = $email;
                    $test->post($route, $payload)->assertSessionHasErrors('email');
                }
                return;

            case 10:
                self::createBuyer(['email' => 'juanmiguel.delacruz@example.com']);
                $payload = self::buyerIsolationPayload();
                $payload['email'] = 'juanmiguel.delacruz@example.com';
                self::withVerifiedOtp($test, 'buyer', $payload['email'])
                    ->post($route, $payload)->assertSessionHasErrors('email');
                return;

            case 11:
                self::createBuyer(['email' => 'juanmiguel.delacruz@example.com']);
                $payload = self::buyerIsolationPayload();
                $payload['email'] = 'JUANMIGUEL.DELACRUZ@EXAMPLE.COM';
                self::withVerifiedOtp($test, 'buyer', strtolower($payload['email']))
                    ->post($route, $payload)->assertSessionHasErrors('email');
                return;

            case 12:
                $payload = self::buyerIsolationPayload('buyer.trim@example.com');
                $payload['email'] = '  buyer.trim@example.com  ';
                self::withVerifiedOtp($test, 'buyer', 'buyer.trim@example.com')
                    ->post($route, $payload)->assertSessionDoesntHaveErrors('email');
                $test->assertDatabaseHas('buyer_accounts', ['email' => 'buyer.trim@example.com']);
                return;

            case 13:
                foreach (['0917ABC4567','0917-123-456','12345678901','639171234567'] as $mobile) {
                    $payload = self::buyerIsolationPayload();
                    $payload['contact_no'] = $mobile;
                    $test->post($route, $payload)->assertSessionHasErrors('contact_no');
                }
                return;

            case 14:
                foreach (['0917123456' => true,'09170000001' => false,'091712345678' => true] as $mobile => $shouldFail) {
                    $payload = self::buyerIsolationPayload();
                    $payload['contact_no'] = $mobile;
                    $response = $test->post($route, $payload);
                    $shouldFail ? $response->assertSessionHasErrors('contact_no') : $response->assertSessionDoesntHaveErrors('contact_no');
                }
                return;

            case 15:
                self::createBuyer(['email' => 'otherbuyer@example.test', 'contact_no' => '09171234567']);
                $payload = self::buyerIsolationPayload();
                $payload['contact_no'] = '09171234567';
                self::withVerifiedOtp($test, 'buyer', $payload['email'])
                    ->post($route, $payload)->assertSessionHasErrors('contact_no');
                return;

            case 16:
                self::createBuyer(['email' => 'otherbuyer@example.test', 'contact_no' => '09171234567']);
                $payload = self::buyerIsolationPayload();
                $payload['contact_no'] = '+639171234567';
                self::withVerifiedOtp($test, 'buyer', $payload['email'])
                    ->post($route, $payload)->assertSessionHasErrors('contact_no');
                return;

            case 17:
                foreach (['Buy@126' => true, 'Buyer@26' => false] as $password => $shouldFail) {
                    $payload = self::buyerIsolationPayload();
                    $payload['password'] = $payload['password_confirmation'] = $password;
                    $response = $test->post($route, $payload);
                    $shouldFail ? $response->assertSessionHasErrors('password') : $response->assertSessionDoesntHaveErrors('password');
                }
                return;

            case 18:
                foreach (['mamimili@2026','MAMIMILI@2026','Mamimili@abcd','Mamimili2026'] as $password) {
                    $payload = self::buyerIsolationPayload();
                    $payload['password'] = $payload['password_confirmation'] = $password;
                    $test->post($route, $payload)->assertSessionHasErrors('password');
                }
                return;

            case 19:
                $payload = self::buyerIsolationPayload();
                $payload['password_confirmation'] = 'Mamimili@2027';
                $test->post($route, $payload)->assertSessionHasErrors('password');
                return;

            case 20:
                $test->get(route('register.buyer'))
                    ->assertOk()
                    ->assertSee('id="password"', false)
                    ->assertSee('type="password"', false)
                    ->assertSee('id="passwordConfirmation"', false);
                return;

            case 21:
                $payload = self::buyerIsolationPayload();
                unset($payload['terms']);
                $test->post($route, $payload)->assertSessionHasErrors('terms');
                return;

            case 22:
                $payload = self::buyerWorkbookPayload('anamarie.reyes@example.com','09173456789','Ana Marie','Reyes','2001-11-02','Suki#2026Ana');
                for ($i = 0; $i < 3; $i++) {
                    $test->post($route, $payload);
                }
                $test->assertSame(1, RegistrationApplication::query()->where('email', 'anamarie.reyes@example.com')->count());
                $test->assertSame(1, BuyerAccount::query()->where('email', 'anamarie.reyes@example.com')->count());
                return;

            case 23:
                [$app,$buyer] = self::createUnverifiedBuyer();
                self::withOtpState($test, 'buyer', $app->email, '482913')
                    ->post($route, ['_registration_action'=>'otp_verify','role'=>'buyer','email'=>$app->email,'otp'=>'482913'])
                    ->assertSuccessful();
                $test->assertSame('active', $buyer->fresh()->account_status);
                return;

            case 24:
                [$app,$buyer] = self::createUnverifiedBuyer();
                self::withOtpState($test, 'buyer', $app->email, '482913')
                    ->postJson($route, ['_registration_action'=>'otp_verify','role'=>'buyer','email'=>$app->email,'otp'=>'000000'])
                    ->assertStatus(422);
                $test->assertSame('unverified', $buyer->fresh()->account_status);
                return;

            case 25:
                [$app,$buyer] = self::createUnverifiedBuyer();
                foreach (['48291','4829137','48A91B',''] as $otp) {
                    self::withOtpState($test, 'buyer', $app->email, '482913')
                        ->postJson($route, ['_registration_action'=>'otp_verify','role'=>'buyer','email'=>$app->email,'otp'=>$otp])
                        ->assertStatus(422);
                }
                $test->assertSame('unverified', $buyer->fresh()->account_status);
                return;

            case 26:
                [$app,$buyer] = self::createUnverifiedBuyer();
                self::withOtpState($test, 'buyer', $app->email, '482913', now()->subSecond()->timestamp)
                    ->postJson($route, ['_registration_action'=>'otp_verify','role'=>'buyer','email'=>$app->email,'otp'=>'482913'])
                    ->assertStatus(422);
                $test->assertSame('unverified', $buyer->fresh()->account_status);
                return;

            case 27:
                [$app,$buyer] = self::createUnverifiedBuyer();
                $first = self::withOtpState($test, 'buyer', $app->email, '482913', now()->addMinutes(5)->timestamp, now()->subMinute()->timestamp)
                    ->postJson($route, ['_registration_action'=>'otp_send','role'=>'buyer','email'=>$app->email]);
                $first->assertSuccessful();
                $secondCode = (string) $first->json('debug_code');
                self::withOtpState($test, 'buyer', $app->email, $secondCode)
                    ->postJson($route, ['_registration_action'=>'otp_verify','role'=>'buyer','email'=>$app->email,'otp'=>'482913'])
                    ->assertStatus(422);
                return;

            case 28:
                [, $buyer] = self::createUnverifiedBuyer();
                $response = $test->post(route('login.submit'), ['email'=>$buyer->email,'password'=>'Mamimili@2026']);
                $test->assertFalse((bool) session('is_buyer'));
                $response->assertSessionHasErrors('email');
                return;

            case 29:
                $buyer = self::createBuyer(['email'=>'juanmiguel.delacruz@example.com','password'=>Hash::make('Mamimili@2026'),'first_name'=>'Juan Miguel','last_name'=>'Dela Cruz','account_status'=>'active']);
                $test->post(route('login.submit'), ['email'=>$buyer->email,'password'=>'Mamimili@2026'])
                    ->assertRedirect(route('buyer.dashboard'));
                $test->assertTrue((bool) session('is_buyer'));
                return;

            case 30:
                $buyer = self::createBuyer(['account_status'=>'active']);
                $session = ['is_buyer'=>true,'buyer_account_id'=>$buyer->id,'buyer_email'=>$buyer->email];
                foreach (['seller.dashboard','logistics.dashboard','courier.dashboard','admin.dashboard'] as $otherRoute) {
                    self::assertDenied($test->withSession($session)->get(route($otherRoute)));
                }
                return;
        }
    }

    private static function seller(TestCase $test, string $id): void
    {
        $route = route('register.seller.submit');
        $n = (int) substr($id, -3);

        switch ($n) {
            case 1:
                $payload = self::sellerWorkbookPayload();
                $test->post($route, $payload)->assertSessionDoesntHaveErrors();
                $app = RegistrationApplication::query()->where('email', $payload['email'])->first();
                $test->assertNotNull($app);
                $test->assertSame('unverified', $app->status);
                $test->assertNotNull(session('registration_email_otp.hash'));
                return;
            case 2:
                $test->post($route, [])->assertSessionHasErrors(['first_name','last_name','email','contact_no','business_name','line_of_business','seller_type','tin','postal_code','id_document','password','terms']);
                return;
            case 3:
                foreach (['first_name','last_name','email','contact_no','business_name','line_of_business','seller_type','tin','province_code','municipality_code','barangay_code','street_address','postal_code','password','password_confirmation'] as $field) {
                    $payload = self::sellerIsolationPayload();
                    $payload[$field] = '';
                    if ($field === 'password') $payload['password_confirmation'] = '';
                    $test->post($route, $payload)->assertSessionHasErrors($field);
                }
                return;
            case 4:
                foreach ([['first_name','Maria99'],['last_name','Santos@#']] as [$field,$value]) {
                    $payload = self::sellerIsolationPayload(); $payload[$field]=$value;
                    $test->post($route,$payload)->assertSessionHasErrors($field);
                }
                return;
            case 5:
                $payload=self::sellerIsolationPayload(); $payload['first_name']='Iñigo Rafael'; $payload['last_name']="Muñoz-D'Angelo";
                $test->post($route,$payload)->assertSessionDoesntHaveErrors(['first_name','last_name']);
                return;
            case 6:
                foreach ([1=>true,2=>false,50=>false,51=>true] as $length=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['first_name']=str_repeat('A',$length); $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('first_name') : $r->assertSessionDoesntHaveErrors('first_name');
                }
                return;
            case 7:
                foreach (['mariaclara.santosexample.com','mariaclara.santos@','@example.com','maria@@example.com'] as $email) {
                    $payload=self::sellerIsolationPayload(); $payload['email']=$email; $test->post($route,$payload)->assertSessionHasErrors('email');
                }
                return;
            case 8:
                self::createSeller(['email'=>'mariaclara.santos@example.com']);
                $payload=self::sellerIsolationPayload(); $payload['email']='mariaclara.santos@example.com';
                self::withVerifiedOtp($test,'seller',$payload['email'])->post($route,$payload)->assertSessionHasErrors('email');
                return;
            case 9:
                self::createSeller(['email'=>'mariaclara.santos@example.com']);
                $payload=self::sellerIsolationPayload(); $payload['email']='MARIACLARA.SANTOS@EXAMPLE.COM';
                self::withVerifiedOtp($test,'seller',strtolower($payload['email']))->post($route,$payload)->assertSessionHasErrors('email');
                return;
            case 10:
                $payload=self::sellerIsolationPayload('seller.trim@example.com'); $payload['email']='  seller.trim@example.com  ';
                self::withVerifiedOtp($test,'seller','seller.trim@example.com')->post($route,$payload)->assertSessionDoesntHaveErrors('email');
                $test->assertDatabaseHas('registration_applications',['email'=>'seller.trim@example.com']);
                return;
            case 11:
                foreach (['0917ABC4567','0917-123-456','12345678901','639171234567'] as $mobile) {
                    $payload=self::sellerIsolationPayload(); $payload['contact_no']=$mobile; $test->post($route,$payload)->assertSessionHasErrors('contact_no');
                }
                return;
            case 12:
                foreach (['0917123456'=>true,'09170000001'=>false,'091712345678'=>true] as $mobile=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['contact_no']=$mobile; $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('contact_no') : $r->assertSessionDoesntHaveErrors('contact_no');
                }
                return;
            case 13:
                self::createBuyer(['email'=>'crossrole@example.test','contact_no'=>'09189876543']);
                $payload=self::sellerIsolationPayload(); $payload['contact_no']='09189876543';
                self::withVerifiedOtp($test,'seller',$payload['email'])->post($route,$payload)->assertSessionHasErrors('contact_no');
                return;
            case 14:
                self::createBuyer(['email'=>'crossrole@example.test','contact_no'=>'09189876543']);
                $payload=self::sellerIsolationPayload(); $payload['contact_no']='+639189876543';
                self::withVerifiedOtp($test,'seller',$payload['email'])->post($route,$payload)->assertSessionHasErrors('contact_no');
                return;
            case 15:
                foreach (['Sell@26'=>true,'Seller@2'=>false] as $password=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['password']=$payload['password_confirmation']=$password; $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('password') : $r->assertSessionDoesntHaveErrors('password');
                }
                return;
            case 16:
                foreach (['tindahan@2026','TINDAHAN@2026','Tindahan@abcd','Tindahan2026'] as $password) {
                    $payload=self::sellerIsolationPayload(); $payload['password']=$payload['password_confirmation']=$password; $test->post($route,$payload)->assertSessionHasErrors('password');
                }
                return;
            case 17:
                $payload=self::sellerIsolationPayload(); $payload['password_confirmation']='Tindahan@2027'; $test->post($route,$payload)->assertSessionHasErrors('password');
                return;
            case 18:
                $test->get(route('register.seller'))->assertOk()->assertSee('id="password"',false)->assertSee('type="password"',false);
                return;
            case 19:
                $payload=self::sellerIsolationPayload(); unset($payload['terms']); $test->post($route,$payload)->assertSessionHasErrors('terms');
                return;
            case 20:
                $payload=self::sellerWorkbookPayload('roberto.villanueva@example.com','09194567890','Roberto','Villanueva','RV Gadget Hub','corporation','456-789-012-000','CS202012346');
                for($i=0;$i<3;$i++) $test->post($route,$payload);
                $test->assertSame(1,RegistrationApplication::query()->where('email',$payload['email'])->count());
                return;
            case 21:
                self::createSeller(['store_name'=>'Maria Clara Handicrafts']);
                $payload=self::sellerIsolationPayload(); $payload['business_name']='Maria Clara Handicrafts';
                self::withVerifiedOtp($test,'seller',$payload['email'])->post($route,$payload)->assertSessionHasErrors('business_name');
                return;
            case 22:
                foreach ([2=>true,3=>false,30=>false,31=>true] as $length=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['business_name']=str_repeat('A',$length); $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('business_name') : $r->assertSessionDoesntHaveErrors('business_name');
                }
                return;
            case 23:
                foreach (['individual','sole_proprietorship','corporation'] as $type) {
                    $payload=self::sellerIsolationPayload(null,$type,true);
                    $response=$test->post($route,$payload);
                    if($type==='individual') $response->assertSessionDoesntHaveErrors(['id_document','business_permit','bir_document','sec_document']);
                    if($type==='sole_proprietorship') $response->assertSessionDoesntHaveErrors(['id_document','business_permit','bir_document'])->assertSessionDoesntHaveErrors('sec_document');
                    if($type==='corporation') $response->assertSessionDoesntHaveErrors(['id_document','bir_document','sec_document'])->assertSessionDoesntHaveErrors('business_permit');
                }
                return;
            case 24:
                $payload=self::sellerIsolationPayload(null,'corporation',true); unset($payload['sec_document']);
                $test->post($route,$payload)->assertSessionHasErrors('sec_document');
                return;
            case 25:
                foreach (['123-456-789-00'=>true,'345-678-901-000'=>false,'123-456-789-0000'=>true,'123456789000'=>true] as $tin=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['tin']=$tin; $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('tin') : $r->assertSessionDoesntHaveErrors('tin');
                }
                return;
            case 26:
                self::createLogistics(['email'=>'tin-owner@example.test','tin'=>'123-456-789-000']);
                $payload=self::sellerIsolationPayload(); $payload['tin']='123-456-789-000';
                self::withVerifiedOtp($test,'seller',$payload['email'])->post($route,$payload)->assertSessionHasErrors('tin');
                return;
            case 27:
                $test->get(route('register.seller'))->assertOk()
                    ->assertSee('name="region_code"',false)
                    ->assertSee('name="province_code"',false)
                    ->assertSee('name="municipality_code"',false)
                    ->assertSee('name="barangay_code"',false);
                return;
            case 28:
                foreach (['160'=>true,'1121'=>false,'16030'=>true,'16A3'=>true] as $postal=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['postal_code']=$postal; $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('postal_code') : $r->assertSessionDoesntHaveErrors('postal_code');
                }
                return;
            case 29:
                foreach ([['x.jpg','image/jpeg'],['x.png','image/png'],['x.pdf','application/pdf']] as [$name,$mime]) {
                    $payload=self::sellerIsolationPayload(); $payload['id_document']=self::file($name,200,$mime); $test->post($route,$payload)->assertSessionDoesntHaveErrors('id_document');
                }
                return;
            case 30:
                foreach ([['x.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document'],['x.gif','image/gif'],['x.exe','application/octet-stream']] as [$name,$mime]) {
                    $payload=self::sellerIsolationPayload(); $payload['id_document']=self::file($name,200,$mime); $test->post($route,$payload)->assertSessionHasErrors('id_document');
                }
                return;
            case 31:
                foreach ([5018=>false,5120=>false,5222=>true] as $size=>$fail) {
                    $payload=self::sellerIsolationPayload(); $payload['id_document']=self::file('size.jpg',$size,'image/jpeg'); $r=$test->post($route,$payload);
                    $fail ? $r->assertSessionHasErrors('id_document') : $r->assertSessionDoesntHaveErrors('id_document');
                }
                return;
            case 32:
                $app=self::createUnverifiedApplication('seller');
                self::withOtpState($test,'seller',$app->email,'482913')->postJson($route,['_registration_action'=>'otp_verify','role'=>'seller','email'=>$app->email,'otp'=>'482913'])->assertSuccessful();
                $test->assertSame('pending',$app->fresh()->status);
                return;
            case 33:
                $app=self::createUnverifiedApplication('seller');
                self::withOtpState($test,'seller',$app->email,'482913')->postJson($route,['_registration_action'=>'otp_verify','role'=>'seller','email'=>$app->email,'otp'=>'000000'])->assertStatus(422);
                $test->assertSame('unverified',$app->fresh()->status);
                return;
            case 34:
                $app=self::createUnverifiedApplication('seller');
                foreach(['48291','4829137','48A91B',''] as $otp) self::withOtpState($test,'seller',$app->email,'482913')->postJson($route,['_registration_action'=>'otp_verify','role'=>'seller','email'=>$app->email,'otp'=>$otp])->assertStatus(422);
                return;
            case 35:
                $app=self::createUnverifiedApplication('seller');
                self::withOtpState($test,'seller',$app->email,'482913',now()->subSecond()->timestamp)->postJson($route,['_registration_action'=>'otp_verify','role'=>'seller','email'=>$app->email,'otp'=>'482913'])->assertStatus(422);
                $test->assertSame('unverified',$app->fresh()->status);
                return;
            case 36:
                $app=self::createUnverifiedApplication('seller');
                $r=self::withOtpState($test,'seller',$app->email,'482913',now()->addMinutes(5)->timestamp,now()->subMinute()->timestamp)->postJson($route,['_registration_action'=>'otp_send','role'=>'seller','email'=>$app->email]);
                $r->assertSuccessful();
                return;
            case 37:
                $app=self::createUnverifiedApplication('seller',['password'=>Hash::make('Tindahan@2026')]);
                $test->post(route('login.submit'),['email'=>$app->email,'password'=>'Tindahan@2026'])->assertSessionHasErrors('email');
                $test->assertFalse((bool)session('is_seller'));
                return;
            case 38:
                $app=self::createPendingApplication('seller',['email'=>'mariaclara.santos@example.com','password'=>Hash::make('Tindahan@2026')]);
                $test->post(route('login.submit'),['email'=>$app->email,'password'=>'Tindahan@2026'])->assertSessionHasErrors('email');
                $test->assertFalse((bool)session('is_seller'));
                return;
            case 39:
                $app=self::createPendingApplication('seller',['email'=>'mariaclara.santos@example.com']);
                Storage::disk('local')->put($app->id_path,'test');
                $test->withSession(self::adminSession())->get(route('admin.registrations'))->assertOk()->assertSee($app->email);
                $test->withSession(self::adminSession())->get(route('admin.registrations.document',[$app,'id']))->assertOk();
                return;
            case 40:
                Bus::fake();
                $app=self::createPendingApplication('seller',['email'=>'mariaclara.santos@example.com','password'=>Hash::make('Tindahan@2026')]);
                $test->withSession(self::adminSession())->post(route('admin.registrations.approve',$app),[])->assertRedirect();
                $test->assertSame('approved',$app->fresh()->status);
                $test->assertDatabaseHas('seller_accounts',['email'=>$app->email,'registration_status'=>'approved','account_status'=>'active']);
                Bus::assertDispatched(SendRegistrationDecisionEmail::class);
                return;
            case 41:
                Bus::fake();
                $app=self::createPendingApplication('seller');
                $reason='DTI certificate is blurred and the business name cannot be read.';
                $test->withSession(self::adminSession())->post(route('admin.registrations.reject',$app),['admin_note'=>$reason])->assertRedirect();
                $test->assertSame('rejected',$app->fresh()->status); $test->assertSame($reason,$app->fresh()->admin_note);
                Bus::assertDispatched(SendRegistrationDecisionEmail::class);
                return;
            case 42:
                $app=self::createRejectedApplication('seller',['email'=>'mariaclara.santos@example.com','password'=>Hash::make('Tindahan@2026')]);
                $test->post(route('login.submit'),['email'=>$app->email,'password'=>'Tindahan@2026'])->assertRedirect(route('register.seller'));
                $payload=self::sellerIsolationPayload($app->email,'sole_proprietorship',true); $payload['business_permit']=self::file('dti_certificate_clear.pdf',1000,'application/pdf');
                $test->post($route,$payload)->assertRedirect(route('registration.pending'));
                $test->assertSame('pending',$app->fresh()->status);
                return;
            case 43:
                self::assertDenied($test->withSession(['is_seller'=>true,'seller_account_id'=>self::createSeller()->id])->get(route('admin.registrations')));
                return;
            case 44:
                $seller=self::createSeller(['account_status'=>'active','registration_status'=>'approved']);
                $session=['is_seller'=>true,'seller_account_id'=>$seller->id];
                foreach(['admin.dashboard','logistics.dashboard','courier.dashboard'] as $other) self::assertDenied($test->withSession($session)->get(route($other)));
                return;
        }
    }

    private static function logistics(TestCase $test, string $id): void
    {
        $route = route('register.logistics.submit');
        $n=(int)substr($id,-3);
        switch($n){
            case 1:
                $payload=self::logisticsWorkbookPayload(); $test->post($route,$payload)->assertSessionDoesntHaveErrors();
                $app=RegistrationApplication::query()->where('email',$payload['email'])->first(); $test->assertNotNull($app); $test->assertSame('unverified',$app->status); $test->assertNotNull(session('registration_email_otp.hash')); return;
            case 2:
                $test->post($route,[])->assertSessionHasErrors(['business_name','sec_registration_no','tin','province_code','municipality_code','barangay_code','street_address','postal_code','first_name','last_name','position','email','contact_no','service_areas','id_document','business_permit','bir_document','sec_document','password','terms']); return;
            case 3:
                foreach(['business_name','sec_registration_no','tin','province_code','municipality_code','barangay_code','street_address','postal_code','first_name','last_name','position','email','contact_no','service_areas','password','password_confirmation'] as $field){$p=self::logisticsIsolationPayload();$p[$field]='';if($field==='password')$p['password_confirmation']='';$test->post($route,$p)->assertSessionHasErrors($field);}return;
            case 4:
                foreach([['first_name','Carlos7'],['last_name','Dela Cruz@#']] as[$f,$v]){$p=self::logisticsIsolationPayload();$p[$f]=$v;$test->post($route,$p)->assertSessionHasErrors($f);}return;
            case 5:
                $p=self::logisticsIsolationPayload();$p['first_name']='Iñigo Rafael';$p['last_name']="Muñoz-D'Angelo";$test->post($route,$p)->assertSessionDoesntHaveErrors(['first_name','last_name']);return;
            case 6:
                foreach([1=>true,2=>false,50=>false,51=>true]as$l=>$fail){$p=self::logisticsIsolationPayload();$p['first_name']=str_repeat('A',$l);$r=$test->post($route,$p);$fail?$r->assertSessionHasErrors('first_name'):$r->assertSessionDoesntHaveErrors('first_name');}return;
            case 7:
                foreach(['operations.bilishatidexample.com','operations.bilishatid@','@example.com','ops@@example.com']as$e){$p=self::logisticsIsolationPayload();$p['email']=$e;$test->post($route,$p)->assertSessionHasErrors('email');}return;
            case 8:
                self::createLogistics(['email'=>'operations@bilishatid.example.com']);$p=self::logisticsIsolationPayload();$p['email']='operations@bilishatid.example.com';self::withVerifiedOtp($test,'logistics',$p['email'])->post($route,$p)->assertSessionHasErrors('email');return;
            case 9:
                self::createLogistics(['email'=>'operations@bilishatid.example.com']);$p=self::logisticsIsolationPayload();$p['email']='OPERATIONS@BILISHATID.EXAMPLE.COM';self::withVerifiedOtp($test,'logistics',strtolower($p['email']))->post($route,$p)->assertSessionHasErrors('email');return;
            case 10:
                $p=self::logisticsIsolationPayload('logistics.test01@example.com');$p['email']='  logistics.test01@example.com  ';self::withVerifiedOtp($test,'logistics','logistics.test01@example.com')->post($route,$p)->assertSessionDoesntHaveErrors('email');$test->assertDatabaseHas('registration_applications',['email'=>'logistics.test01@example.com']);return;
            case 11:
                foreach(['0917ABC4567','0917-123-456','12345678901','639285551234']as$m){$p=self::logisticsIsolationPayload();$p['contact_no']=$m;$test->post($route,$p)->assertSessionHasErrors('contact_no');}return;
            case 12:
                foreach(['0917123456'=>true,'09170000001'=>false,'091712345678'=>true]as$m=>$fail){$p=self::logisticsIsolationPayload();$p['contact_no']=$m;$r=$test->post($route,$p);$fail?$r->assertSessionHasErrors('contact_no'):$r->assertSessionDoesntHaveErrors('contact_no');}return;
            case 13:
                self::createBuyer(['email'=>'cross@example.test','contact_no'=>'09285551234']);$p=self::logisticsIsolationPayload();$p['contact_no']='09285551234';self::withVerifiedOtp($test,'logistics',$p['email'])->post($route,$p)->assertSessionHasErrors('contact_no');return;
            case 14:
                self::createBuyer(['email'=>'cross@example.test','contact_no'=>'09285551234']);$p=self::logisticsIsolationPayload();$p['contact_no']='+639285551234';self::withVerifiedOtp($test,'logistics',$p['email'])->post($route,$p)->assertSessionHasErrors('contact_no');return;
            case 15:
                foreach(['Hati@26'=>true,'Hatid@26'=>false]as$pw=>$fail){$p=self::logisticsIsolationPayload();$p['password']=$p['password_confirmation']=$pw;$r=$test->post($route,$p);$fail?$r->assertSessionHasErrors('password'):$r->assertSessionDoesntHaveErrors('password');}return;
            case 16:
                foreach(['hatid@2026','HATID@2026','Hatid@abcd','Hatid2026']as$pw){$p=self::logisticsIsolationPayload();$p['password']=$p['password_confirmation']=$pw;$test->post($route,$p)->assertSessionHasErrors('password');}return;
            case 17:
                $p=self::logisticsIsolationPayload();$p['password_confirmation']='Hatid@2027';$test->post($route,$p)->assertSessionHasErrors('password');return;
            case 18:
                $test->get(route('register.logistics'))->assertOk()->assertSee('id="password"',false)->assertSee('type="password"',false);return;
            case 19:
                $p=self::logisticsIsolationPayload();unset($p['terms']);$test->post($route,$p)->assertSessionHasErrors('terms');return;
            case 20:
                $p=self::logisticsWorkbookPayload('admin@agilacargo.example.com','09396667890','Agila Cargo Movers Corp.','CS201998765','567-890-123-000');for($i=0;$i<3;$i++)$test->post($route,$p);$test->assertSame(1,RegistrationApplication::query()->where('email',$p['email'])->count());return;
            case 21:
                self::createLogistics(['business_name'=>'Bilis Hatid Express Inc.']);$p=self::logisticsIsolationPayload();$p['business_name']='Bilis Hatid Express Inc.';self::withVerifiedOtp($test,'logistics',$p['email'])->post($route,$p)->assertSessionHasErrors('business_name');return;
            case 22:
                foreach(['CS20201234','202012345CS','CS2020-12345','12345678901']as$sec){$p=self::logisticsIsolationPayload();$p['sec_registration_no']=$sec;$test->post($route,$p)->assertSessionHasErrors('sec_registration_no');}return;
            case 23:
                self::createLogistics(['sec_registration_no'=>'CS202012345']);$p=self::logisticsIsolationPayload();$p['sec_registration_no']='CS202012345';self::withVerifiedOtp($test,'logistics',$p['email'])->post($route,$p)->assertSessionHasErrors('sec_registration_no');return;
            case 24:
                $page=$test->get(route('register.logistics'))->assertOk();$page->assertSee('name="service_areas[]"',false)->assertSee('multiple',false);
                $p=self::logisticsIsolationPayload();$p['service_areas']=['NCR','Region III (Central Luzon)','Region IV-A (CALABARZON)'];$test->post($route,$p)->assertSessionDoesntHaveErrors('service_areas');return;
            case 25:
                foreach(['123-456-789-00'=>true,'345-678-901-000'=>false,'123-456-789-0000'=>true,'123456789000'=>true]as$tin=>$fail){$p=self::logisticsIsolationPayload();$p['tin']=$tin;$r=$test->post($route,$p);$fail?$r->assertSessionHasErrors('tin'):$r->assertSessionDoesntHaveErrors('tin');}return;
            case 26:
                self::createSeller(['email'=>'tin-seller@example.test','tin'=>'234-567-890-000']);$p=self::logisticsIsolationPayload();$p['tin']='234-567-890-000';self::withVerifiedOtp($test,'logistics',$p['email'])->post($route,$p)->assertSessionHasErrors('tin');return;
            case 27:
                $test->get(route('register.logistics'))->assertOk()->assertSee('name="region_code"',false)->assertSee('name="province_code"',false)->assertSee('name="municipality_code"',false)->assertSee('name="barangay_code"',false);return;
            case 28:
                foreach(['160'=>true,'1603'=>false,'16030'=>true,'16A3'=>true]as$pc=>$fail){$p=self::logisticsIsolationPayload();$p['postal_code']=$pc;$r=$test->post($route,$p);$fail?$r->assertSessionHasErrors('postal_code'):$r->assertSessionDoesntHaveErrors('postal_code');}return;
            case 29:
                foreach([['x.jpg','image/jpeg'],['x.png','image/png'],['x.pdf','application/pdf']]as[$name,$mime]){$p=self::logisticsIsolationPayload();$p['sec_document']=self::file($name,200,$mime);$test->post($route,$p)->assertSessionDoesntHaveErrors('sec_document');}return;
            case 30:
                foreach([['x.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document'],['x.gif','image/gif'],['x.exe','application/octet-stream']]as[$name,$mime]){$p=self::logisticsIsolationPayload();$p['sec_document']=self::file($name,200,$mime);$test->post($route,$p)->assertSessionHasErrors('sec_document');}return;
            case 31:
                foreach([5018=>false,5120=>false,5222=>true]as$size=>$fail){$p=self::logisticsIsolationPayload();$p['sec_document']=self::file('size.pdf',$size,'application/pdf');$r=$test->post($route,$p);$fail?$r->assertSessionHasErrors('sec_document'):$r->assertSessionDoesntHaveErrors('sec_document');}return;
            case 32:
                $app=self::createUnverifiedApplication('logistics');self::withOtpState($test,'logistics',$app->email,'482913')->postJson($route,['_registration_action'=>'otp_verify','role'=>'logistics','email'=>$app->email,'otp'=>'482913'])->assertSuccessful();$test->assertSame('pending',$app->fresh()->status);return;
            case 33:
                $app=self::createUnverifiedApplication('logistics');self::withOtpState($test,'logistics',$app->email,'482913')->postJson($route,['_registration_action'=>'otp_verify','role'=>'logistics','email'=>$app->email,'otp'=>'000000'])->assertStatus(422);$test->assertSame('unverified',$app->fresh()->status);return;
            case 34:
                $app=self::createUnverifiedApplication('logistics');foreach(['48291','4829137','48A91B','']as$otp)self::withOtpState($test,'logistics',$app->email,'482913')->postJson($route,['_registration_action'=>'otp_verify','role'=>'logistics','email'=>$app->email,'otp'=>$otp])->assertStatus(422);return;
            case 35:
                $app=self::createUnverifiedApplication('logistics');self::withOtpState($test,'logistics',$app->email,'482913',now()->subSecond()->timestamp)->postJson($route,['_registration_action'=>'otp_verify','role'=>'logistics','email'=>$app->email,'otp'=>'482913'])->assertStatus(422);$test->assertSame('unverified',$app->fresh()->status);return;
            case 36:
                $app=self::createUnverifiedApplication('logistics');self::withOtpState($test,'logistics',$app->email,'482913',now()->addMinutes(5)->timestamp,now()->subMinute()->timestamp)->postJson($route,['_registration_action'=>'otp_send','role'=>'logistics','email'=>$app->email])->assertSuccessful();return;
            case 37:
                $app=self::createUnverifiedApplication('logistics',['password'=>Hash::make('Hatid@2026')]);$test->post(route('login.submit'),['email'=>$app->email,'password'=>'Hatid@2026'])->assertSessionHasErrors('email');$test->assertFalse((bool)session('is_logistics'));return;
            case 38:
                $app=self::createPendingApplication('logistics',['email'=>'operations@bilishatid.example.com','password'=>Hash::make('Hatid@2026')]);$test->post(route('login.submit'),['email'=>$app->email,'password'=>'Hatid@2026'])->assertSessionHasErrors('email');$test->assertFalse((bool)session('is_logistics'));return;
            case 39:
                $app=self::createPendingApplication('logistics',['email'=>'operations@bilishatid.example.com']);Storage::disk('local')->put($app->id_path,'test');$test->withSession(self::adminSession())->get(route('admin.registrations'))->assertOk()->assertSee($app->email);$test->withSession(self::adminSession())->get(route('admin.registrations.document',[$app,'id']))->assertOk();return;
            case 40:
                Bus::fake();$app=self::createPendingApplication('logistics',['email'=>'operations@bilishatid.example.com','password'=>Hash::make('Hatid@2026')]);$test->withSession(self::adminSession())->post(route('admin.registrations.approve',$app),[])->assertRedirect();$test->assertSame('approved',$app->fresh()->status);$test->assertDatabaseHas('logistics_accounts',['email'=>$app->email,'account_status'=>'active']);Bus::assertDispatched(SendRegistrationDecisionEmail::class);return;
            case 41:
                Bus::fake();$app=self::createPendingApplication('logistics');$reason="Mayor's permit expired on 12/31/2025.";$test->withSession(self::adminSession())->post(route('admin.registrations.reject',$app),['admin_note'=>$reason])->assertRedirect();$test->assertSame('rejected',$app->fresh()->status);$test->assertSame($reason,$app->fresh()->admin_note);Bus::assertDispatched(SendRegistrationDecisionEmail::class);return;
            case 42:
                $app=self::createRejectedApplication('logistics',['email'=>'operations@bilishatid.example.com','password'=>Hash::make('Hatid@2026')]);$test->post(route('login.submit'),['email'=>$app->email,'password'=>'Hatid@2026'])->assertRedirect(route('register.logistics'));$p=self::logisticsIsolationPayload($app->email);$p['business_permit']=self::file('mayors_permit_2026.pdf',1200,'application/pdf');$test->post($route,$p)->assertRedirect(route('registration.pending'));$test->assertSame('pending',$app->fresh()->status);return;
            case 43:
                self::assertDenied($test->withSession(['is_logistics'=>true,'logistics_account_id'=>self::createLogistics()->id])->get(route('admin.registrations')));return;
            case 44:
                $log=self::createLogistics(['account_status'=>'active']);$session=['is_logistics'=>true,'logistics_account_id'=>$log->id,'logistics_email'=>$log->email];foreach(['admin.dashboard','seller.dashboard','courier.dashboard']as$other)self::assertDenied($test->withSession($session)->get(route($other)));return;
        }
    }

    private static function rider(TestCase $test, string $id): void
    {
        $n=(int)substr($id,-3);
        switch($n){
            case 1:
                $log=self::createLogistics(['email'=>'operations@bilishatid.example.com','business_name'=>'Bilis Hatid Express Inc.','account_status'=>'active']);$p=self::riderPayload();$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionDoesntHaveErrors();$test->assertDatabaseHas('courier_accounts',['email'=>$p['email'],'logistics_account_id'=>$log->id,'account_status'=>'pending_activation']);return;
            case 2:
                $log=self::createLogistics(['account_status'=>'active']);$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),[])->assertSessionHasErrors(['first_name','last_name','birthday','email','contact_no','license_number','license_expiry_date','vehicle_type','plate_number','driver_license_document','orcr_document']);return;
            case 3:
                $log=self::createLogistics(['account_status'=>'active']);foreach(['first_name','last_name','birthday','email','contact_no','license_number','license_expiry_date','vehicle_type','plate_number']as$f){$p=self::riderPayload();$p[$f]='';$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors($f);}return;
            case 4:
                $log=self::createLogistics(['account_status'=>'active']);foreach([['first_name','Pedro Jose99'],['last_name','Dela Cruz@#']]as[$f,$v]){$p=self::riderPayload();$p[$f]=$v;$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors($f);}return;
            case 5:
                $log=self::createLogistics(['account_status'=>'active']);foreach([1=>true,2=>false,50=>false,51=>true]as$l=>$fail){$p=self::riderPayload();$p['first_name']=str_repeat('A',$l);$r=$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p);$fail?$r->assertSessionHasErrors('first_name'):$r->assertSessionDoesntHaveErrors('first_name');}return;
            case 6:
                $log=self::createLogistics(['account_status'=>'active']);foreach([now()->subYears(18)->addDay()->toDateString()=>true,now()->subYears(18)->toDateString()=>false,now()->subYears(18)->subDay()->toDateString()=>false]as$dob=>$fail){$p=self::riderPayload();$p['birthday']=$dob;$r=$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p);$fail?$r->assertSessionHasErrors('birthday'):$r->assertSessionDoesntHaveErrors('birthday');}return;
            case 7:
                $log=self::createLogistics(['account_status'=>'active']);foreach(['pedrojose.garciaexample.com','pedrojose.garcia@','@example.com','pedro@@example.com']as$e){$p=self::riderPayload();$p['email']=$e;$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('email');}return;
            case 8:
                $log=self::createLogistics(['account_status'=>'active']);self::createBuyer(['email'=>'juanmiguel.delacruz@example.com']);$p=self::riderPayload();$p['email']='juanmiguel.delacruz@example.com';$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('email');return;
            case 9:
                $log=self::createLogistics(['account_status'=>'active']);foreach(['0917ABC4567','0917-123-456','12345678901','639451239876']as$m){$p=self::riderPayload();$p['contact_no']=$m;$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('contact_no');}return;
            case 10:
                $log=self::createLogistics(['account_status'=>'active']);foreach(['0917123456'=>true,'09170000001'=>false,'091712345678'=>true]as$m=>$fail){$p=self::riderPayload();$p['contact_no']=$m;$r=$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p);$fail?$r->assertSessionHasErrors('contact_no'):$r->assertSessionDoesntHaveErrors('contact_no');}return;
            case 11:
                $log=self::createLogistics(['account_status'=>'active']);self::createBuyer(['email'=>'mobile-owner@example.test','contact_no'=>'09171234567']);$p=self::riderPayload();$p['contact_no']='09171234567';$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('contact_no');return;
            case 12:
                $log=self::createLogistics(['account_status'=>'active']);foreach(['N01-23-45678','N01-23-4567890','101-23-456789','N0A-23-456789']as$lic){$p=self::riderPayload();$p['license_number']=$lic;$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('license_number');}return;
            case 13:
                $log=self::createLogistics(['account_status'=>'active']);self::createCourier($log,['license_number'=>'N01-23-456789']);$p=self::riderPayload();$p['license_number']='N01-23-456789';$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('license_number');return;
            case 14:
                $log=self::createLogistics(['account_status'=>'active']);foreach([now()->subDay()->toDateString()=>true,now()->toDateString()=>false,now()->addDay()->toDateString()=>false]as$d=>$fail){$p=self::riderPayload();$p['license_expiry_date']=$d;$r=$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p);$fail?$r->assertSessionHasErrors('license_expiry_date'):$r->assertSessionDoesntHaveErrors('license_expiry_date');}return;
            case 15:
                $log=self::createLogistics(['account_status'=>'active']);self::createCourier($log,['plate_number'=>'123ABC']);$p=self::riderPayload();$p['plate_number']='123ABC';$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('plate_number');return;
            case 16:
                $log=self::createLogistics(['account_status'=>'active']);foreach([['x.jpg','image/jpeg'],['x.png','image/png'],['x.pdf','application/pdf']]as[$name,$mime]){$p=self::riderPayload();$p['driver_license_document']=self::file($name,200,$mime);$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionDoesntHaveErrors('driver_license_document');}return;
            case 17:
                $log=self::createLogistics(['account_status'=>'active']);foreach([['x.docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document'],['x.gif','image/gif'],['x.exe','application/octet-stream']]as[$name,$mime]){$p=self::riderPayload();$p['driver_license_document']=self::file($name,200,$mime);$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p)->assertSessionHasErrors('driver_license_document');}return;
            case 18:
                $log=self::createLogistics(['account_status'=>'active']);foreach([5018=>false,5120=>false,5222=>true]as$size=>$fail){$p=self::riderPayload();$p['driver_license_document']=self::file('size.jpg',$size,'image/jpeg');$r=$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p);$fail?$r->assertSessionHasErrors('driver_license_document'):$r->assertSessionDoesntHaveErrors('driver_license_document');}return;
            case 19:
                $log=self::createLogistics(['account_status'=>'active']);$p=self::riderPayload('markanthony.bautista@example.com','09662345678','D05-19-123456','NBC1234');for($i=0;$i<3;$i++)$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.store'),$p);$test->assertSame(1,CourierAccount::query()->where('email',$p['email'])->count());return;
            case 20:
                [$rider,$token]=self::pendingRider();$test->post(route('rider.activation.activate',$token),['password'=>'Rider@2026','password_confirmation'=>'Rider@2026'])->assertRedirect(route('login'));$test->assertSame('active',$rider->fresh()->account_status);$test->assertTrue(Hash::check('Rider@2026',$rider->fresh()->password));return;
            case 21:
                foreach(['Ride@26','rider@2026','RIDER@2026','Rider@abcd','Rider2026']as$pw){[$rider,$token]=self::pendingRider();$test->post(route('rider.activation.activate',$token),['password'=>$pw,'password_confirmation'=>$pw])->assertSessionHasErrors('password');$test->assertSame('pending_activation',$rider->fresh()->account_status);}return;
            case 22:
                [$rider,$token]=self::pendingRider();$test->post(route('rider.activation.activate',$token),['password'=>'Rider@2026','password_confirmation'=>'Rider@2027'])->assertSessionHasErrors('password');$test->assertSame('pending_activation',$rider->fresh()->account_status);return;
            case 23:
                [$rider,$token]=self::pendingRider(['activation_expires_at'=>now()->subMinute()]);$test->get(route('rider.activation.show',$token))->assertStatus(410);$test->assertSame('pending_activation',$rider->fresh()->account_status);return;
            case 24:
                [$rider,$firstToken,$log]=self::pendingRider(returnLogistics:true);$test->withSession(self::logisticsSession($log))->post(route('logistics.rider-management.resend-activation',$rider))->assertSessionHas('success');$test->get(route('rider.activation.show',$firstToken))->assertNotFound();$test->assertNotSame(hash('sha256',$firstToken),$rider->fresh()->activation_token_hash);return;
            case 25:
                [$rider,$token]=self::pendingRider();$test->post(route('rider.activation.activate',$token),['password'=>'Rider@2026','password_confirmation'=>'Rider@2026']);$test->get(route('rider.activation.show',$token))->assertNotFound();return;
            case 26:
                [$rider]=self::pendingRider();$test->post(route('login.submit'),['email'=>$rider->email,'password'=>'Rider@2026'])->assertSessionHasErrors('email');$test->assertFalse((bool)session('is_courier'));return;
            case 27:
                [$rider,$token]=self::pendingRider();$test->post(route('rider.activation.activate',$token),['password'=>'Rider@2026','password_confirmation'=>'Rider@2026']);$test->post(route('login.submit'),['email'=>$rider->email,'password'=>'Rider@2026'])->assertRedirect(route('courier.dashboard'));$test->assertTrue((bool)session('is_courier'));return;
            case 28:
                $a=self::createLogistics(['email'=>'operations@bilishatid.example.com','business_name'=>'Bilis Hatid Express Inc.','account_status'=>'active']);$b=self::createLogistics(['email'=>'admin@agilacargo.example.com','business_name'=>'Agila Cargo Movers Corp.','account_status'=>'active']);$r=self::createCourier($a,['email'=>'pedrojose.garcia@example.com']);$test->withSession(self::logisticsSession($a))->get(route('logistics.rider-management'))->assertOk()->assertSee($r->email);$test->withSession(self::logisticsSession($b))->get(route('logistics.rider-management'))->assertOk()->assertDontSee($r->email);return;
            case 29:
                $test->get(route('rider.logistics.index'))->assertRedirect(route('login'));$test->get(route('register'))->assertOk()->assertDontSee('data-role-option="rider"',false);return;
            case 30:
                $buyer=self::createBuyer();self::assertDenied($test->withSession(['is_buyer'=>true,'buyer_account_id'=>$buyer->id])->get(route('logistics.rider-management')));$seller=self::createSeller();self::assertDenied($test->withSession(['is_seller'=>true,'seller_account_id'=>$seller->id])->get(route('logistics.rider-management')));return;
            case 31:
                $log=self::createLogistics(['account_status'=>'pending']);self::assertDenied($test->withSession(self::logisticsSession($log))->get(route('logistics.rider-management')));return;
            case 32:
                $log=self::createLogistics(['account_status'=>'active']);$r=self::createCourier($log,['account_status'=>'active']);$session=['is_courier'=>true,'courier_account_id'=>$r->id,'courier_email'=>$r->email];foreach(['logistics.dashboard','seller.dashboard','admin.dashboard']as$other)self::assertDenied($test->withSession($session)->get(route($other)));return;
        }
    }

    private static function buyerWorkbookPayload(string $email='juanmiguel.delacruz@example.com', string $mobile='09171234567', string $first='Juan Miguel', string $last='Dela Cruz', string $birthday='1998-05-14', string $password='Mamimili@2026'): array
    {
        return ['role'=>'buyer','first_name'=>$first,'last_name'=>$last,'email'=>$email,'contact_no'=>$mobile,'birthday'=>$birthday,'password'=>$password,'password_confirmation'=>$password,'terms'=>'1'];
    }

    private static function buyerIsolationPayload(?string $email=null): array
    {
        return array_merge(self::buyerWorkbookPayload($email ?? 'buyer.test01@example.com','09170000001'),[
            'sex'=>'Male','province_code'=>'130000000','province_name'=>'Metro Manila','municipality_code'=>'137404000','municipality_name'=>'Quezon City','barangay_code'=>'137404001','barangay_name'=>'Commonwealth','street_address'=>'Blk 12 Lot 8, Sampaguita St.','id_document'=>self::file('govt_id_front.jpg',100,'image/jpeg'),
        ]);
    }

    private static function sellerWorkbookPayload(string $email='mariaclara.santos@example.com', string $mobile='09189876543', string $first='Maria Clara', string $last='Santos', string $shop='Maria Clara Handicrafts', string $type='sole_proprietorship', string $tin='123-456-789-000', ?string $sec=null): array
    {
        $p=['role'=>'seller','first_name'=>$first,'last_name'=>$last,'email'=>$email,'contact_no'=>$mobile,'business_name'=>$shop,'line_of_business'=>'Home & Living','seller_type'=>$type,'tin'=>$tin,'province_code'=>'130000000','province_name'=>'Metro Manila','municipality_code'=>'137404000','municipality_name'=>'Quezon City','barangay_code'=>'137404001','barangay_name'=>'Commonwealth','street_address'=>'Blk 12 Lot 8, Sampaguita St.','postal_code'=>'1121','id_document'=>self::file('govt_id_front.jpg',1200,'image/jpeg'),'password'=>'Tindahan@2026','password_confirmation'=>'Tindahan@2026','terms'=>'1'];
        if($type==='sole_proprietorship'){$p['business_permit']=self::file('dti_certificate.pdf',850,'application/pdf');$p['bir_document']=self::file('bir_2303.pdf',1100,'application/pdf');}
        if($type==='corporation'){$p['sec_registration_no']=$sec??'CS202012345';$p['sec_document']=self::file('sec_certificate.pdf',1000,'application/pdf');$p['bir_document']=self::file('bir_2303.pdf',1100,'application/pdf');}
        return $p;
    }

    private static function sellerIsolationPayload(?string $email=null,string $type='sole_proprietorship',bool $workbookOnly=false):array
    {
        $p=self::sellerWorkbookPayload($email??'seller.test01@example.com','09170000002','Maria Clara','Santos','Unique Shop '.$type,$type,'123-456-789-111',$type==='corporation'?'CS202012346':null);
        if(!$workbookOnly){$p['sex']='Female';$p['birthday']='1990-01-01';}
        return $p;
    }

    private static function logisticsWorkbookPayload(string $email='operations@bilishatid.example.com',string $mobile='09285551234',string $company='Bilis Hatid Express Inc.',string $sec='CS202012345',string $tin='234-567-890-000'):array
    {
        return ['role'=>'logistics','business_name'=>$company,'sec_registration_no'=>$sec,'tin'=>$tin,'province_code'=>'130000000','province_name'=>'Metro Manila','municipality_code'=>'137403000','municipality_name'=>'Pasig City','barangay_code'=>'137403001','barangay_name'=>'Kapitolyo','street_address'=>'Unit 5B, 88 West Capitol Drive','postal_code'=>'1603','first_name'=>'Carlos Antonio','last_name'=>'Mendoza','position'=>'Operations Manager','email'=>$email,'contact_no'=>$mobile,'service_areas'=>'NCR; Region IV-A (CALABARZON)','sec_document'=>self::file('sec_certificate.pdf',1000,'application/pdf'),'bir_document'=>self::file('bir_2303.pdf',1100,'application/pdf'),'business_permit'=>self::file('mayors_permit.pdf',1300,'application/pdf'),'id_document'=>self::file('govt_id_front.jpg',1200,'image/jpeg'),'password'=>'Hatid@2026','password_confirmation'=>'Hatid@2026','terms'=>'1'];
    }

    private static function logisticsIsolationPayload(?string $email=null):array
    {
        return array_merge(self::logisticsWorkbookPayload($email??'logistics.test01@example.com','09170000003','Bilis Hatid Test '.substr(md5($email??'x'),0,6),'CS'.substr('000000000'.abs(crc32($email??'x')),-9),'234-567-890-111'),['sex'=>'Male','birthday'=>'1990-01-01','profile_image'=>self::file('profile.jpg',100,'image/jpeg')]);
    }

    private static function riderPayload(string $email='pedrojose.garcia@example.com',string $mobile='09451239876',string $license='N01-23-456789',string $plate='123ABC'):array
    {
        return ['first_name'=>'Pedro Jose','last_name'=>'Garcia','birthday'=>'1995-03-22','email'=>$email,'contact_no'=>$mobile,'license_number'=>$license,'license_expiry_date'=>'2030-03-22','vehicle_type'=>'Motorcycle','plate_number'=>$plate,'driver_license_document'=>self::file('drivers_license.jpg',1500,'image/jpeg'),'orcr_document'=>self::file('or_cr.pdf',900,'application/pdf')];
    }

    private static function file(string $name,int $kilobytes,string $mime):UploadedFile{return UploadedFile::fake()->create($name,$kilobytes,$mime);}

    private static function withVerifiedOtp(TestCase $test,string $role,string $email):TestCase{return $test->withSession(['registration_email_otp'=>['email'=>strtolower(trim($email)),'purpose'=>$role,'verified_at'=>now()->timestamp]]);}

    private static function withOtpState(TestCase $test,string $role,string $email,string $code,int|null $expiresAt=null,int|null $lastSentAt=null):TestCase
    {
        return $test->withSession(['registration_email_otp'=>['email'=>strtolower(trim($email)),'purpose'=>$role,'hash'=>hash_hmac('sha256',$code,(string)config('app.key')),'expires_at'=>$expiresAt??now()->addMinutes(5)->timestamp,'last_sent_at'=>$lastSentAt??now()->timestamp,'attempts'=>0,'verified_at'=>null]]);
    }

    private static function createBuyer(array $override=[]):BuyerAccount
    {
        static $i=0;$i++;
        return BuyerAccount::query()->create(array_merge(['registration_application_id'=>null,'last_name'=>'Buyer','first_name'=>'Test','middle_initial'=>null,'sex'=>'Male','email'=>'buyer'.$i.'@example.test','contact_no'=>'0918'.str_pad((string)$i,7,'0',STR_PAD_LEFT),'birthday'=>'1990-01-01','age'=>36,'province_code'=>'130000000','province_name'=>'Metro Manila','municipality_code'=>'137404000','municipality_name'=>'Quezon City','barangay_code'=>'137404001','barangay_name'=>'Commonwealth','street_address'=>'Test Address','password'=>Hash::make('Mamimili@2026'),'id_path'=>null,'account_status'=>'active','approved_at'=>now()],$override));
    }

    private static function createSeller(array $override=[]):SellerAccount
    {
        static $i=0;$i++;
        return SellerAccount::query()->create(array_merge(['email'=>'seller'.$i.'@example.test','store_name'=>'Seller Store '.$i,'password'=>Hash::make('Tindahan@2026'),'registration_status'=>'approved','account_status'=>'active'], $override));
    }

    private static function createLogistics(array $override=[]):LogisticsAccount
    {
        static $i=0;$i++;
        return LogisticsAccount::query()->create(array_merge(['registration_application_id'=>null,'last_name'=>'Mendoza','first_name'=>'Carlos','middle_initial'=>null,'sex'=>'Male','email'=>'logistics'.$i.'@example.test','contact_no'=>'0928'.str_pad((string)$i,7,'0',STR_PAD_LEFT),'birthday'=>'1990-01-01','age'=>36,'province_code'=>'130000000','province_name'=>'Metro Manila','municipality_code'=>'137403000','municipality_name'=>'Pasig City','barangay_code'=>'137403001','barangay_name'=>'Kapitolyo','street_address'=>'88 West Capitol Drive','password'=>Hash::make('Hatid@2026'),'id_path'=>null,'account_status'=>'active','approved_at'=>now(),'business_name'=>'Logistics '.$i],$override));
    }

    private static function createCourier(LogisticsAccount $logistics,array $override=[]):CourierAccount
    {
        static $i=0;$i++;
        return CourierAccount::query()->create(array_merge(['registration_application_id'=>null,'logistics_account_id'=>$logistics->id,'last_name'=>'Garcia','first_name'=>'Pedro','middle_initial'=>null,'sex'=>'N/A','email'=>'rider'.$i.'@example.test','contact_no'=>'0945'.str_pad((string)$i,7,'0',STR_PAD_LEFT),'birthday'=>'1995-03-22','age'=>31,'province_code'=>$logistics->province_code,'province_name'=>$logistics->province_name,'municipality_code'=>$logistics->municipality_code,'municipality_name'=>$logistics->municipality_name,'barangay_code'=>$logistics->barangay_code,'barangay_name'=>$logistics->barangay_name,'street_address'=>$logistics->street_address,'password'=>Hash::make('Rider@2026'),'vehicle_type'=>'Motorcycle','plate_number'=>'P'.str_pad((string)$i,5,'0',STR_PAD_LEFT),'orcr_path'=>'rider/orcr.pdf','id_path'=>'rider/license.jpg','license_number'=>'N01-'.str_pad((string)($i%99),2,'0',STR_PAD_LEFT).'-'.str_pad((string)$i,6,'0',STR_PAD_LEFT),'license_expiry_date'=>now()->addYear()->toDateString(),'account_status'=>'active','availability_status'=>'offline','approved_at'=>now()],$override));
    }

    private static function createUnverifiedBuyer():array
    {
        $app=self::createUnverifiedApplication('buyer',['email'=>'juanmiguel.delacruz@example.com','password'=>Hash::make('Mamimili@2026')]);
        $buyer=self::createBuyer(['registration_application_id'=>$app->id,'email'=>$app->email,'password'=>$app->password,'account_status'=>'unverified','approved_at'=>null]);
        return [$app,$buyer];
    }

    private static function createUnverifiedApplication(string $role,array $override=[]):RegistrationApplication{return self::createApplication($role,'unverified',$override);}
    private static function createPendingApplication(string $role,array $override=[]):RegistrationApplication{return self::createApplication($role,'pending',$override);}
    private static function createRejectedApplication(string $role,array $override=[]):RegistrationApplication{return self::createApplication($role,'rejected',array_merge(['admin_note'=>'Please correct the submitted document.','rejected_at'=>now(),'reviewed_at'=>now()],$override));}

    private static function createApplication(string $role,string $status,array $override=[]):RegistrationApplication
    {
        static $i=0;$i++;
        $seller=$role==='seller';$log=$role==='logistics';
        $data=['role'=>$role,'logistics_account_id'=>null,'last_name'=>$log?'Mendoza':($seller?'Santos':'Dela Cruz'),'first_name'=>$log?'Carlos Antonio':($seller?'Maria Clara':'Juan Miguel'),'middle_initial'=>null,'sex'=>'Male','email'=>$role.$i.'@example.test','contact_no'=>'0917'.str_pad((string)$i,7,'0',STR_PAD_LEFT),'birthday'=>'1990-01-01','age'=>36,'province_code'=>'130000000','province_name'=>'Metro Manila','municipality_code'=>'137404000','municipality_name'=>'Quezon City','barangay_code'=>'137404001','barangay_name'=>'Commonwealth','street_address'=>'Test Address','postal_code'=>($seller||$log)?'1121':null,'password'=>Hash::make($seller?'Tindahan@2026':($log?'Hatid@2026':'Mamimili@2026')),'business_name'=>$seller?'Maria Clara Handicrafts':($log?'Bilis Hatid Express Inc.':null),'seller_type'=>$seller?'sole_proprietorship':null,'tin'=>$seller?'123-456-789-000':($log?'234-567-890-000':null),'sec_registration_no'=>$log?'CS202012345':null,'line_of_business'=>$seller?'Home & Living':null,'position'=>$log?'Operations Manager':null,'service_areas'=>$log?'NCR':null,'vehicle_type'=>null,'plate_number'=>null,'id_path'=>'registration/id.jpg','business_permit_path'=>($seller||$log)?'registration/permit.pdf':null,'bir_path'=>($seller||$log)?'registration/bir.pdf':null,'sec_path'=>$log?'registration/sec.pdf':null,'orcr_path'=>null,'status'=>$status,'admin_note'=>null,'reviewed_at'=>null,'approved_at'=>null,'rejected_at'=>null];
        return RegistrationApplication::query()->create(array_merge($data,$override));
    }

    private static function pendingRider(array $override=[],bool $returnLogistics=false):array
    {
        $log=self::createLogistics(['account_status'=>'active']);$token=str_repeat(chr(97+(random_int(0,20))),64);$r=self::createCourier($log,array_merge(['email'=>'pending'.uniqid().'@example.test','account_status'=>'pending_activation','password'=>Hash::make('unusable-secret'),'activation_token_hash'=>hash('sha256',$token),'activation_expires_at'=>now()->addHours(24),'activated_at'=>null],$override));return $returnLogistics?[$r,$token,$log]:[$r,$token];
    }

    private static function adminSession():array
    {
        $admin=AdminAccount::query()->firstOrCreate(['email'=>'admin.blackbox@example.test'],['name'=>'Black Box Admin','password'=>Hash::make('Admin@2026')]);return ['is_admin'=>true,'admin_account_id'=>$admin->id];
    }
    private static function logisticsSession(LogisticsAccount $log):array{return ['is_logistics'=>true,'logistics_account_id'=>$log->id,'logistics_email'=>$log->email,'logistics_name'=>$log->business_name?:'Logistics'];}

    private static function assertDenied($response):void
    {
        if(!in_array($response->status(),[302,401,403],true)){
            throw new \PHPUnit\Framework\AssertionFailedError('Expected access to be denied with 302, 401, or 403; received '.$response->status().'.');
        }
    }
}
