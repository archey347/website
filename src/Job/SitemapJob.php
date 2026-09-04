<?php

namespace Website\Job;

use Twig\Environment;

class SitemapJob implements JobInterface
{
    protected Environment $twig;
    protected array $options;

    public function __construct(Environment $twig, array $options)
    {
        $this->twig = $twig;
        $this->options = $options;
    }

    public function run(JobCallbackInterface $cb): void
    {
        $urls = [];
        foreach ($cb->getPages() as $path) {
            $url = $this->toUrl($path);
            if ($url === null) {
                continue;
            }

            $urls[] = $url;
        }

        sort($urls);

        $content = $this->twig->render($this->options["template"], [
            "base_url" => rtrim($this->options["site"]["base_url"], "/"),
            "urls" => $urls,
        ]);

        $cb->AddPage($this->options["path"], $content);
    }

    private function toUrl(string $path): ?string
    {
        $path = str_replace(DIRECTORY_SEPARATOR, "/", $path);
        $path = "/" . ltrim(preg_replace("#/+#", "/", $path), "/");

        if (!str_ends_with($path, ".html")) {
            return null;
        }

        foreach ($this->options["exclude"] ?? [] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return null;
            }
        }

        // Pages live at <name>/index.html but are served without the
        // trailing slash, the root being the one exception.
        if (str_ends_with($path, "/index.html")) {
            $path = substr($path, 0, -strlen("/index.html"));
            return $path === "" ? "/" : $path;
        }

        return $path;
    }
}
