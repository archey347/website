<?php

namespace Website\Job;

use Twig\Environment;
use Website\BlogLoader;

class RssJob implements JobInterface
{
    private array $options;
    private Environment $twig;
    private BlogLoader $blogLoader;

    public function __construct(Environment $twig, array $options)
    {
        $this->twig = $twig;
        $this->options = $options;
        $this->blogLoader = new BlogLoader();
    }

    public function run(JobCallbackInterface $cb): void
    {
        $blogs = $this->blogLoader->loadBlogs($this->options["src_path"]);
        $this->renderFeed($cb, $blogs);
    }

    public function renderFeed(JobCallbackInterface $cb, array $blogs): void
    {
        $items = [];
        $baseUrl = $this->options["base_url"] ?? "";
        $blogBaseUrl = $this->options["blog_base_url"] ?? "/blog";

        foreach ($blogs as $name => $blog) {
            $metadata = $blog->getYAML();
            $url = $blogBaseUrl . "/posts/" . $name . ".html";

            $items[] = [
                "title" => $metadata["title"],
                "author" => $metadata["author"] ?? "",
                "url" => $url,
                "published_at" => $metadata["published_at"],
                "content" => $blog->getContent(),
            ];
        }

        usort($items, fn($a, $b) => $b["published_at"] <=> $a["published_at"]);

        $content = $this->twig->render("blog/feed.xml.twig", [
            "items" => $items,
            "base_url" => $baseUrl,
            "title" => $this->options["title"] ?? "Blog",
            "description" => $this->options["description"] ?? "",
        ]);

        $cb->AddPage($this->options["output_path"] ?? "/feed.xml", $content);
    }
}
