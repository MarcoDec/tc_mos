<?php

namespace App\Filesystem;

use Tightenco\Collect\Support\Collection;

final class Directory {
    /** @var Collection<int, string> */
    private Collection $files;

    public function __construct(private string $path) {
        $this->files = collect(scandir($path) ?: [])->filter(static fn (string $file): bool => !in_array($file, ['.', '..', '.gitignore']));
    }

    public function __toString(): string {
        return $this->path;
    }

    public function firstStartsWith(string $start): ?string {
        return !empty($first = $this->files->first(static fn (string $file): bool => str_starts_with($file, $start)))
            ? "$this->path/$first"
            : null;
    }
}
