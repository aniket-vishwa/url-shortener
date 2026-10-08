<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::insert(
            "INSERT INTO users (name, email, password, role, company_id, created_at, updated_at)
             VALUES (?, ?, ?, ?, NULL, datetime('now'), datetime('now'))",
            ['Super Admin', 'superadmin@example.com', Hash::make('password123'), 'SuperAdmin']
        );
    }
}