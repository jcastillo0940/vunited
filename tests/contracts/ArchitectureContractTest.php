<?php

namespace Tests\Contracts;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ArchitectureContractTest extends TestCase
{
    public static function requiredPathProvider(): array
    {
        return collect([
            'web/backend', 'web/frontend',
            'ticketing/backend', 'ticketing/frontend',
            'store/backend', 'store/frontend',
            'payments/backend',
            'shared/api-contracts', 'shared/ui', 'shared/config',
            'infrastructure/apache', 'infrastructure/systemd',
            'infrastructure/mysql', 'infrastructure/redis',
            'infrastructure/observability', 'infrastructure/backups',
            'infrastructure/scripts',
            'tests/contracts', 'tests/e2e', 'tests/performance', 'tests/security',
            'docs/architecture', 'docs/runbooks', 'docs/decisions',
        ])->mapWithKeys(fn (string $path): array => [$path => [$path]])->all();
    }

    #[DataProvider('requiredPathProvider')]
    public function test_required_monorepo_path_exists(string $path): void
    {
        $this->assertDirectoryExists(base_path($path));
    }

    public function test_store_contract_is_read_only_and_uses_minor_units(): void
    {
        $contract = file_get_contents(base_path('shared/api-contracts/store-v1.openapi.yaml'));

        $this->assertIsString($contract);
        $this->assertStringNotContainsString('/orders:', $contract);
        $this->assertStringContainsString('price_minor:', $contract);
        $this->assertStringNotContainsString('price: { type: string', $contract);
    }
}
