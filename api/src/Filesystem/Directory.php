<?php

declare(strict_types=1);

namespace App\Filesystem;

use App\Collection;
use Stringable;

class Directory implements Stringable {
    /** @var Collection<int, string> */
    private readonly Collection $files;

    public function __construct(private readonly string $path) {
        $this->files = (new Collection(scandir($path) ?: []))
            ->filter(static fn (string $file): bool => in_array($file, ['.', '..', '.gitignore'], true) === false);
    }

    public function __toString(): string {
        return $this->path;
    }

    public function startsWith(string $start): ?string {
        return empty($file = $this->files->startsWith($start)) ? null : "$this->path/$file";
    }
}
