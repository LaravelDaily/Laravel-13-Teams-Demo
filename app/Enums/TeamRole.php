<?php

namespace App\Enums;

enum TeamRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';
    case Member = 'member';

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * Get all the permissions for this role.
     *
     * @return array<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => [
                'team:update',
                'team:delete',
                'member:add',
                'member:update',
                'member:remove',
                'invitation:create',
                'invitation:cancel',
            ],
            self::Admin => [
                'team:update',
                'invitation:create',
                'invitation:cancel',
            ],
            self::Editor => [
                'invitation:create',
            ],
            self::Viewer => [],
            self::Member => [],
        };
    }

    /**
     * Determine if the role has the given permission.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    /**
     * Get the hierarchy level for this role.
     * Higher numbers indicate higher privileges.
     */
    public function level(): int
    {
        return match ($this) {
            self::Owner => 50,
            self::Admin => 40,
            self::Editor => 30,
            self::Member => 20,
            self::Viewer => 10,
        };
    }

    /**
     * Check if this role is at least as privileged as another role.
     */
    public function isAtLeast(TeamRole $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * Get the roles that can be assigned to team members (excludes Owner).
     *
     * @return array<array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        return collect(self::cases())
            ->filter(fn (self $role) => $role !== self::Owner)
            ->map(fn (self $role) => ['value' => $role->value, 'label' => $role->label()])
            ->values()
            ->toArray();
    }
}
