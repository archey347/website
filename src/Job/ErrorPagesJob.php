<?php

namespace Website\Job;

use Twig\Environment;

class ErrorPagesJob implements JobInterface
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
        $errors = $this->options["errors"];
        foreach ($errors as $error) {
            $path = $this->options["destination"] . $error . ".html";
            $content = $this->twig->render($this->options["template"], [
                "error" => $error,
            ]);
            $cb->AddPage($path, $content);
        }
    }
}
