<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    /** @return array<string, string|null> */
    public function all(): array;

    /** @param array<string, string|null> $values */
    public function putMany(array $values): void;
}
