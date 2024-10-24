<?php

namespace App\Filesystem;

use App\Collection;
use PHPUnit\Exception;
use Stringable;

final class Directory implements Stringable {
    /** @var Collection<int, string> */
    private readonly Collection $files;

    public function __construct(private readonly string $path) {
        if (!file_exists($path)) {
            mkdir($path,0777,false);
        }
        $this->files = Collection::collect(scandir($path) ?: [])
            ->filter(static fn (string $file): bool => !in_array($file, ['.', '..', '.gitignore']));
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
