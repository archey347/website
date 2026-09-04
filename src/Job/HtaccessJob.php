<?php

namespace Website\Job;

use Twig\Environment;

/**
 * Renders the .htaccess. The redirect list it pulls in is kept as server
 * agnostic json, so pointing the site at something that isn't apache means a
 * new job and template rather than a rewritten list.
 */
class HtaccessJob implements JobInterface
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
