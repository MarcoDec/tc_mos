<?php

namespace App\Twig;

use App\Twig\Exception\ViteEntryNotFoundException;
use App\Twig\Exception\ViteMissingManifest;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ViteExtension extends AbstractExtension {
    public function __construct(private string $manifest, private string $outDir) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array {
        return [
            (new ViteFunction('vite_link', [$this, 'link']))->get(),
            (new ViteFunction('vite_script', [$this, 'script']))->get()
        ];
    }

    public function link(Environment $env, string $asset): string {
        $entries = $this->getEntry($asset);
        if (isset($entries['imports'])) {
            $entries['imports'] = collect($entries['imports'])
                ->map(static fn (string $import): string => removeStart($import, '_'))
                ->values()
                ->all();
        }
        return $env->render('vite/link.html.twig', ['entries' => $entries, 'out_dir' => $this->outDir]);
    }

    public function script(Environment $env, string $asset): string {
        return $env->render('vite/script.html.twig', ['entry' => $this->getEntry($asset)['file'], 'out_dir' => $this->outDir]);
    }

    /**
     * @return mixed[]
     */
    private function getEntry(string $entry): array {
        $name = str_replace('.', '/', $entry);
        foreach ($this->getManifestData() as $key => $data) {
            if (str_contains($key, $name) && isset($data['isEntry']) && $data['isEntry']) {
                return $data;
            }
        }
        throw new ViteEntryNotFoundException($entry);
    }

    /**
     * @return mixed[]
     */
    private function getManifestData(): array {
        $content = file_get_contents($this->manifest);
        if (empty($content)) {
            throw new ViteMissingManifest();
        }
        return json_decode($content, true);
    }
}
