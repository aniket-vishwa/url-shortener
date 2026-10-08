<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser($role, $companyId = null)
    {
        static $n = 0;
        $n++;

        return User::create([
            'name' => "User $n",
            'email' => "user$n@test.com",
            'password' => 'password123',
            'role' => $role,
            'company_id' => $companyId,
            'email_verified_at' => now(),
        ]);
    }

    private function makeUrl($user, $link)
    {
        return ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'original_url' => $link,
            'short_code' => substr(md5($link), 0, 6),
        ]);
    }

    public function test_admin_can_create_short_url()
    {
        $company = Company::create(['name' => 'Acme']);
        $admin = $this->makeUser('Admin', $company->id);

        $this->actingAs($admin)
            ->post('/short-urls', ['original_url' => 'https://google.com'])
            ->assertRedirect('/short-urls');

        $this->assertDatabaseHas('short_urls', ['user_id' => $admin->id]);
    }

    public function test_member_can_create_short_url()
    {
        $company = Company::create(['name' => 'Acme']);
        $member = $this->makeUser('Member', $company->id);

        $this->actingAs($member)
            ->post('/short-urls', ['original_url' => 'https://google.com'])
            ->assertRedirect('/short-urls');

        $this->assertDatabaseHas('short_urls', ['user_id' => $member->id]);
    }

    public function test_superadmin_cannot_create_short_url()
    {
        $super = $this->makeUser('SuperAdmin');

        $this->actingAs($super)
            ->post('/short-urls', ['original_url' => 'https://google.com'])
            ->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_admin_sees_only_own_company_urls()
    {
        $c1 = Company::create(['name' => 'A']);
        $c2 = Company::create(['name' => 'B']);
        $admin1 = $this->makeUser('Admin', $c1->id);
        $admin2 = $this->makeUser('Admin', $c2->id);

        $this->makeUrl($admin1, 'https://own-company.com');
        $this->makeUrl($admin2, 'https://other-company.com');

        $this->actingAs($admin1)
            ->get('/short-urls')
            ->assertSee('https://own-company.com')
            ->assertDontSee('https://other-company.com');
    }

    public function test_member_sees_only_own_urls()
    {
        $company = Company::create(['name' => 'A']);
        $member1 = $this->makeUser('Member', $company->id);
        $member2 = $this->makeUser('Member', $company->id);

        $this->makeUrl($member1, 'https://mine.com');
        $this->makeUrl($member2, 'https://someone-else.com');

        $this->actingAs($member1)
            ->get('/short-urls')
            ->assertSee('https://mine.com')
            ->assertDontSee('https://someone-else.com');
    }

    public function test_short_url_redirects_without_login()
    {
        $company = Company::create(['name' => 'A']);
        $admin = $this->makeUser('Admin', $company->id);
        $url = $this->makeUrl($admin, 'https://google.com');

        $this->get('/s/' . $url->short_code)
            ->assertRedirect('https://google.com');
    }
}