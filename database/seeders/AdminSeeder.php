<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'email' => 'adminG13@gudang.com',
            'password' => Hash::make('adminG13Bisa##'),
        ]);
    }
}
