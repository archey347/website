<?php

namespace Website\Job;

use Twig\Environment;
use Website\BlogLoader;

class BlogJob implements JobInterface
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
        $this->renderBlogs($cb, $blogs);
        $this->renderIndex($cb, $blogs);
    }

    public function renderBlogs(JobCallbackInterface $cb, array $blogs): void
    {
        $base_url = $this->options["base_url"];

        foreach ($blogs as $name => $blog) {
            $metadata = $blog->getYAML();
            $url = $base_url . "/posts/" . $name . ".html";

            $content = $this->twig->render("blog/post.html.twig", [
                "blog" => [
                    "title" => $metadata["title"],
                    "tags" => $metadata["tags"],
                    "published_at" => $metadata["published_at"],
                    "content" => $blog->getContent(),
                ],
            ]);

            $cb->AddPage($url, $content);
        }
    }

    public function renderIndex(JobCallbackInterface $cb, array $blogs)
    {
        $listing = [];
        $base_url = $this->options["base_url"];

        foreach ($blogs as $name => $blog) {
            $metadata = $blog->getYAML();
            $url = $base_url . "/posts/" . $name . ".html";

            $listing[] = [
                "title" => $metadata["title"],
                "url" => $url,
                "tags" => $metadata["tags"],
                "published_at" => strtotime($metadata["published_at"]),
            ];
        }

        usort(
            $listing,
            fn($a, $b) => $b["published_at"] <=> $a["published_at"],
        );

        $content = $this->twig->render("blog/index.html.twig", [
            "blogs" => $listing,
        ]);

        $cb->AddPage($base_url . "/index.html", $content);
    }
}
