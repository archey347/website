<?php

namespace Website\Job;

use Twig\Environment;

class JobFactory
{
    protected Environment $twig;
    protected array $site;

    public function __construct(Environment $twig, array $site)
    {
        $this->twig = $twig;
        $this->site = $site;
    }

    public function create(string $type, array $options): JobInterface
    {
        $options["site"] = $this->site;

        switch ($type) {
            case "template":
                return new TemplatePageJob($this->twig, $options);
            case "copy":
                return new CopyJob($options);
            case "blog":
                return new BlogJob($this->twig, $options);
            case "error_pages":
                return new ErrorPagesJob($this->twig, $options);
            case "rss":
                return new RssJob($this->twig, $options);
            case "redirects":
                return new RedirectsJob($this->twig, $options);
            case "sitemap":
                return new SitemapJob($this->twig, $options);
            default:
                throw new \InvalidArgumentException("Unknown page type: $type");
        }
    }
}
