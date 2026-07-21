<?php

namespace App\Domain\Squad\Models;

use Database\Factories\StaffMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'role',
    'category',
    'photo_path',
    'biography',
    'is_active',
    'sort_order',
])]
class StaffMember extends Model
{
    /** @use HasFactory<StaffMemberFactory> */
    use HasFactory;

    protected static function newFactory(): StaffMemberFactory
    {
        return StaffMemberFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
