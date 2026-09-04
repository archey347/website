<?php

namespace Website\Job;

use Twig\Environment;

class RedirectsJob implements JobInterface
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
        $redirects = $this->loadRedirects($this->options["source"]);

        $content = $this->twig->render($this->options["template"], [
            "redirects" => $redirects,
        ]);

        $cb->AddPage($this->options["path"], $content);
    }

    /**
     * Redirects are stored in a server agnostic json file, so that the format
     * they get rendered into (currently an .htaccess) can be swapped out
     * without having to touch the list itself.
     */
    public function loadRedirects(string $file): array
    {
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new \RuntimeException("Unable to read redirects: $file");
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !is_array($data["redirects"] ?? null)) {
            throw new \RuntimeException(
                "Redirects file needs a \"redirects\" list: $file",
            );
        }

        $redirects = [];
        foreach ($data["redirects"] as $redirect) {
            if (empty($redirect["from"]) || empty($redirect["to"])) {
                throw new \RuntimeException(
                    "Every redirect needs a \"from\" and a \"to\": $file",
                );
            }

            $redirects[] = [
                "from" => $redirect["from"],
                "to" => $redirect["to"],
                "status" => $redirect["status"] ?? 301,
                // Match the whole path, so that a redirect for /blog doesn't
                // also swallow /blog/posts/something.
                "pattern" => "^" . preg_quote($redirect["from"], "#") . "$",
            ];
        }

        return $redirects;
    }
}
