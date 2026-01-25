<?php

namespace Website\Job;

use Twig\Environment;
use Mni\FrontYAML\Parser;

class BlogJob implements JobInterface
{
    private array $options;
    private Environment $twig;
    private Parser $parser;

    public function __construct(Environment $twig, array $options)
    {
        $this->twig = $twig;
        $this->options = $options;
        $this->parser = new Parser();
    }

    public function run(JobCallbackInterface $cb): void
    {
        $blogs = $this->loadBlogs();
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
            fn($a, $b) => $a["published_at"] <=> $b["published_at"],
        );

        $content = $this->twig->render("blog/index.html.twig", [
            "blogs" => $listing,
        ]);

        $cb->AddPage($base_url . "/index.html", $content);
    }

    public function loadBlogs()
    {
        $dir = $this->options["src_path"];
        $file = scandir($dir);
        $blogs = [];
        foreach ($file as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $blog = $this->parseBlog($dir . DIRECTORY_SEPARATOR . $file);

            $name = basename($file, ".md");
            $blogs[$name] = $blog;
        }

        return $blogs;
    }

    public function parseBlog($file)
    {
        $content = file_get_contents($file);
        $parsed = $this->parser->parse($content);
        return $parsed;
    }
}
