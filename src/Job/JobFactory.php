<?php

namespace Website\Job;

use Twig\Environment;

class JobFactory
{
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function create(string $type, array $options): JobInterface
    {
        switch ($type) {
            case "template":
                return new TemplatePageJob($this->twig, $options);
            default:
                throw new \InvalidArgumentException("Unknown page type: $type");
        }
    }
}
