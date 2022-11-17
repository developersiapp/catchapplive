<?php

use Illuminate\Database\Seeder;

class AdminUsersTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@catchapp.com',
            'password' => 'welcome',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
