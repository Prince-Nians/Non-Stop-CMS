<?php

namespace Tests\Concerns;

/** Sets environment variables for one test and puts the ORIGINAL values back afterwards (not just unsets them). */
trait SetsEnv
{
    private array $originalEnv = [];

    protected function setEnv(string $key, ?string $value): void
    {
        if (! array_key_exists($key, $this->originalEnv)) {
            $this->originalEnv[$key] = ['getenv' => getenv($key), 'env' => $_ENV[$key] ?? null, 'server' => $_SERVER[$key] ?? null];
        }
        if ($value === null) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        } else {
            putenv("$key=$value");
            $_ENV[$key] = $_SERVER[$key] = $value;
        }
    }

    protected function restoreEnv(): void
    {
        foreach ($this->originalEnv as $key => $o) {
            $o['getenv'] === false ? putenv($key) : putenv("$key={$o['getenv']}");
            $o['env'] === null ? ($_ENV[$key] = null) : ($_ENV[$key] = $o['env']);
            $o['server'] === null ? ($_SERVER[$key] = null) : ($_SERVER[$key] = $o['server']);
            if ($o['env'] === null) { unset($_ENV[$key]); }
            if ($o['server'] === null) { unset($_SERVER[$key]); }
        }
        $this->originalEnv = [];
    }
}
