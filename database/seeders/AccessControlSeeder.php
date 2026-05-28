<?php

namespace Database\Seeders;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'admin_users.view' => 'View admin users',
            'admin_users.manage' => 'Manage admin users',
            'roles.view' => 'View roles',
            'roles.manage' => 'Manage roles',
            'settings.view' => 'View settings',
            'settings.update' => 'Update settings',
            'menus.view' => 'View menus',
            'menus.manage' => 'Manage menus',
            'pages.view' => 'View pages',
            'pages.manage' => 'Manage pages',
            'news.view' => 'View news',
            'news.manage' => 'Manage news',
            'product_categories.view' => 'View product categories',
            'product_categories.manage' => 'Manage product categories',
            'products.view' => 'View products',
            'products.manage' => 'Manage products',
            'store_orders.view' => 'View store orders',
            'match_events.view' => 'View match events',
            'match_events.manage' => 'Manage match events',
            'ticket_zones.view' => 'View ticket zones',
            'ticket_zones.manage' => 'Manage ticket zones',
            'media.upload' => 'Upload media',
            'audit.view' => 'View audit log',
            'payment_settings.view' => 'View payment settings',
            'payment_settings.update' => 'Update payment settings',
            'membership_plans.view' => 'View membership plans',
            'membership_plans.manage' => 'Manage membership plans',
            'membership_orders.view' => 'View membership orders',
            'payments.view' => 'View payments',
            'payment_events.view' => 'View payment events',
            'ticket_orders.view'      => 'View ticket orders',
            'issued_tickets.view'     => 'View issued tickets',
            'issued_tickets.validate' => 'Validate issued tickets',
            'players.view'            => 'View players',
            'players.manage'          => 'Manage players',
            'staff.view'              => 'View staff members',
            'staff.manage'            => 'Manage staff members',
            'sponsors.view'           => 'View sponsors',
            'sponsors.manage'         => 'Manage sponsors',
            'board_members.view'      => 'View board members',
            'board_members.manage'    => 'Manage board members',
            'fanfest.view'            => 'View FanFest events',
            'fanfest.manage'          => 'Manage FanFest events',
            'expeditions.view'        => 'View expeditions/bus trips',
            'expeditions.manage'      => 'Manage expeditions/bus trips',
            'stadium.view'            => 'View stadium',
            'stadium.manage'          => 'Manage stadium',
            'clubs.view'              => 'View clubs',
            'clubs.manage'            => 'Manage clubs',
            'match_goals.manage'      => 'Manage match goals',
            'standings.view'          => 'View standings',
            'standings.manage'        => 'Manage standings',
        ];

        foreach ($permissions as $name => $label) {
            Permission::query()->updateOrCreate(
                ['name' => $name],
                ['label' => $label],
            );
        }

        $superadmin = Role::query()->updateOrCreate(
            ['name' => 'superadmin'],
            ['label' => 'Superadmin'],
        );

        $superadmin->permissions()->sync(Permission::query()->pluck('id'));
    }
}
