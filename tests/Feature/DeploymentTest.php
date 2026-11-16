<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    use \Tests\Concerns\SetsEnv;

    protected function tearDown(): void
    {
        $this->restoreEnv();
        parent::tearDown();
    }


    public function test_public_disk_switches_to_s3_and_uses_no_per_file_acls(): void
    {
        foreach (['PUBLIC_DISK_DRIVER' => 's3', 'AWS_ACCESS_KEY_ID' => 'k', 'AWS_SECRET_ACCESS_KEY' => 's', 'AWS_BUCKET' => 'nonstop',
            'AWS_URL' => 'https://cdn.example.com', 'AWS_ENDPOINT' => 'https://acct.r2.cloudflarestorage.com'] as $k => $v) {
            $this->setEnv($k, $v);
        }
        $this->refreshApplication();

        $this->assertSame('s3', config('filesystems.disks.public.driver'));
        $this->assertSame('https://cdn.example.com/site/a.webp', Storage::disk('public')->url('site/a.webp'));
        $this->assertArrayNotHasKey('visibility', config('filesystems.disks.public'));
    }

    public function test_forwarded_https_headers_from_the_host_proxy_are_trusted(): void
    {
        Route::get('/_proxy-check', fn () => response()->json(['url' => url('/x'), 'secure' => request()->isSecure()]));

        $this->withServerVariables(['HTTP_X_FORWARDED_PROTO' => 'https', 'HTTP_X_FORWARDED_HOST' => 'cms.example.com', 'HTTP_X_FORWARDED_PORT' => '443'])
            ->getJson('/_proxy-check')->assertOk()->assertJsonPath('secure', true)->assertJsonPath('url', 'https://cms.example.com/x');
    }

    public function test_admin_seeding_never_resets_an_existing_password(): void
    {
        $this->setEnv('ADMIN_EMAIL', 'boss@example.com');
        $this->setEnv('ADMIN_PASSWORD', 'first-password-123');
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'boss@example.com')->first();
        $user->update(['password' => 'changed-by-the-admin-456']);

        $this->setEnv('ADMIN_PASSWORD', 'second-password-789');
        $this->seed(DatabaseSeeder::class); // a redeploy

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('changed-by-the-admin-456', $user->fresh()->password));
        $this->assertSame(1, User::where('email', 'boss@example.com')->count());
    }
}
