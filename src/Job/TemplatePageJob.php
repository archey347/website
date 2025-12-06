<?php

namespace Website\Job;

use Twig\Environment;

class TemplatePageJob implements JobInterface
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
        $path = $this->options["path"];
        $content = $this->twig->render($this->options["template"]);
        $cb->AddPage($path, $content);
    }
}
