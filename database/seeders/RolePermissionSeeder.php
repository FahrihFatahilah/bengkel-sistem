<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Barang
            'barang.view', 'barang.create', 'barang.edit',
            // Stok
            'stok.masuk', 'stok.keluar', 'stok.mutasi', 'stok.view',
            // Pembelian
            'pembelian.view', 'pembelian.create',
            // Pelanggan & Kendaraan
            'pelanggan.view', 'pelanggan.create', 'pelanggan.edit',
            // Work Order
            'wo.view', 'wo.create', 'wo.edit', 'wo.bayar',
            // Opname
            'opname.view', 'opname.create', 'opname.approve',
            // Laporan
            'laporan.view',
            // Master Data
            'master.view', 'master.manage',
            // User
            'user.view', 'user.manage',
            // Diskon
            'diskon.kecil', 'diskon.besar',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roles = [
            'admin' => $permissions,
            'supervisor' => [
                'barang.view', 'stok.view', 'pembelian.view',
                'pelanggan.view', 'wo.view', 'wo.edit',
                'opname.view', 'opname.approve',
                'laporan.view', 'master.view',
                'diskon.kecil', 'diskon.besar',
            ],
            'kasir' => [
                'barang.view', 'stok.view',
                'pelanggan.view', 'pelanggan.create', 'pelanggan.edit',
                'wo.view', 'wo.bayar',
                'laporan.view',
                'diskon.kecil',
            ],
            'mekanik' => [
                'barang.view', 'stok.view',
                'pelanggan.view', 'pelanggan.create',
                'wo.view', 'wo.create', 'wo.edit',
            ],
            'gudang' => [
                'barang.view', 'barang.create', 'barang.edit',
                'stok.view', 'stok.masuk', 'stok.keluar', 'stok.mutasi',
                'pembelian.view', 'pembelian.create',
                'opname.view', 'opname.create',
                'master.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        // Admin user default
        $admin = User::firstOrCreate(
            ['email' => 'admin@bengkel.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Demo users per role
        $demoUsers = [
            ['name' => 'Supervisor Demo', 'email' => 'supervisor@bengkel.com', 'role' => 'supervisor'],
            ['name' => 'Kasir Demo',      'email' => 'kasir@bengkel.com',      'role' => 'kasir'],
            ['name' => 'Mekanik Demo',    'email' => 'mekanik@bengkel.com',    'role' => 'mekanik'],
            ['name' => 'Gudang Demo',     'email' => 'gudang@bengkel.com',     'role' => 'gudang'],
        ];

        foreach ($demoUsers as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password')]
            );
            $user->assignRole($u['role']);
        }
    }
}
