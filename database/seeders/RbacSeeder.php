<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(config('rbac.permissions'));
        $roles = collect(config('rbac.roles'));
        $rolePermissions = collect(config('rbac.role_permissions'));

        $permissions->each(function (string $permission): void {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        });

        $roles->each(function (string $roleName) use ($permissions, $rolePermissions): void {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $mapped = $this->expandPermissionPatterns($rolePermissions->get($roleName, []), $permissions);
            $role->syncPermissions($mapped);
        });

        collect(config('rbac.demo_users'))->each(function (array $demoUser): void {
            $user = User::updateOrCreate(
                ['email' => $demoUser['email']],
                [
                    'name' => $demoUser['name'],
                    'password' => 'password',
                ]
            );

            $user->syncRoles([$demoUser['role']]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<int, string>  $patterns
     * @return array<int, string>
     */
    private function expandPermissionPatterns(array $patterns, Collection $allPermissions): array
    {
        if (in_array('*', $patterns, true)) {
            return $allPermissions->all();
        }

        return collect($patterns)
            ->flatMap(function (string $pattern) use ($allPermissions): array {
                if (! str_contains($pattern, '*')) {
                    return [$pattern];
                }

                return $allPermissions
                    ->filter(fn (string $permission): bool => Str::is($pattern, $permission))
                    ->values()
                    ->all();
            })
            ->unique()
            ->values()
            ->all();
    }
}
