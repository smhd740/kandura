<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();



        // User Management Permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'activate users']);
        Permission::create(['name' => 'deactivate users']);

        // Address Management Permissions
        Permission::create(['name' => 'view addresses']);
        Permission::create(['name' => 'create addresses']);
        Permission::create(['name' => 'edit addresses']);
        Permission::create(['name' => 'delete addresses']);

        // Measurement Management Permissions
        Permission::create(['name' => 'view measurements']);
        Permission::create(['name' => 'create measurements']);
        Permission::create(['name' => 'edit measurements']);
        Permission::create(['name' => 'delete measurements']);

        // Design Management Permissions
        Permission::create(['name' => 'view designs']);
        Permission::create(['name' => 'create designs']);
        Permission::create(['name' => 'edit designs']);
        Permission::create(['name' => 'delete designs']);

        // Design Options Management Permissions
        Permission::create(['name' => 'view design-options']);
        Permission::create(['name' => 'create design-options']);
        Permission::create(['name' => 'edit design-options']);
        Permission::create(['name' => 'delete design-options']);

        // Order Management Permissions
        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'edit orders']);
        Permission::create(['name' => 'delete orders']);
        Permission::create(['name' => 'cancel orders']);
        Permission::create(['name' => 'update order status']);

        // Wallet Management Permissions
        Permission::create(['name' => 'view wallets']);
        Permission::create(['name' => 'add wallet balance']);
        Permission::create(['name' => 'deduct wallet balance']);
        Permission::create(['name' => 'view wallet transactions']);

        // Coupon Management Permissions
        Permission::create(['name' => 'view coupons']);
        Permission::create(['name' => 'create coupons']);
        Permission::create(['name' => 'edit coupons']);
        Permission::create(['name' => 'delete coupons']);

        // Invoice Management Permissions
        Permission::create(['name' => 'view invoices']);
        Permission::create(['name' => 'generate invoices']);

        // Review Management Permissions
        Permission::create(['name' => 'view reviews']);
        Permission::create(['name' => 'approve reviews']);
        Permission::create(['name' => 'reject reviews']);
        Permission::create(['name' => 'delete reviews']);

        // Notification Management Permissions
        Permission::create(['name' => 'send notifications']);
        Permission::create(['name' => 'view all notifications']);


        // Guest Role
        $guestRole = Role::create(['name' => 'guest']);
        $guestRole->givePermissionTo([
            'view designs',
            'view design-options',
        ]);

        // User Role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            // Own addresses
            'view addresses',
            'create addresses',
            'edit addresses',
            'delete addresses',

            // Own measurements
            'view measurements',
            'create measurements',
            'edit measurements',
            'delete measurements',

            // Own designs
            'view designs',
            'create designs',
            'edit designs',
            'delete designs',

            // View design options
            'view design-options',

            // Own orders
            'view orders',
            'create orders',
            'cancel orders',

            // Own wallet
            'view wallets',
            'view wallet transactions',

            // Invoices
            'view invoices',
        ]);

        // Admin Role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            // Users
            'view users',
            'activate users',
            'deactivate users',

            // All addresses
            'view addresses',
            'delete addresses',

            // Design Options Management
            'view design-options',
            'create design-options',
            'edit design-options',
            'delete design-options',

            // Orders Management
            'view orders',
            'edit orders',
            'update order status',

            // Wallet Management
            'view wallets',
            'add wallet balance',
            'deduct wallet balance',
            'view wallet transactions',

            // Coupons
            'view coupons',
            'create coupons',
            'edit coupons',
            'delete coupons',

            // Invoices
            'view invoices',
            'generate invoices',

            // Reviews
            'view reviews',
            'approve reviews',
            'reject reviews',

            // Notifications
            'send notifications',
        ]);

        // Super Admin Role
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());



        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kandura.com',
            'phone' => '+966500000000',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@kandura.com',
            'phone' => '+966500000001',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Regular User
        $user = User::create([
            'name' => 'Ahmed Mohammed',
            'email' => 'user@kandura.com',
            'phone' => '+966500000002',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        $this->command->info('✅ Roles and Permissions created successfully!');
        $this->command->info('📧 Super Admin: superadmin@kandura.com / password');
        $this->command->info('📧 Admin: admin@kandura.com / password');
        $this->command->info('📧 User: user@kandura.com / password');
    }
}
