<?php

namespace App\Domain\Board\Models;

use Database\Factories\BoardMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'role',
    'group',
    'photo_path',
    'biography',
    'email',
    'sort_order',
    'is_active',
    'metadata',
])]
class BoardMember extends Model
{
    /** @use HasFactory<BoardMemberFactory> */
    use HasFactory;

    protected static function newFactory(): BoardMemberFactory
    {
        return BoardMemberFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
            'metadata'   => 'array',
        ];
    }

    public static function groupLabel(string $group): string
    {
        return match ($group) {
            'presidency'      => 'Presidencia',
            'executive_staff' => 'Staff Ejecutivo',
            'board'           => 'Junta Directiva',
            'transparency'    => 'Gobernanza',
            default           => $group,
        };
    }
}
