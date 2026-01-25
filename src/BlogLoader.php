<?php

namespace Website;

use Mni\FrontYAML\Parser;

class BlogLoader
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function loadBlogs(string $dir): array
    {
        $files = scandir($dir);
        $blogs = [];

        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $blog = $this->parseBlog($dir . DIRECTORY_SEPARATOR . $file);
            $name = basename($file, ".md");
            $blogs[$name] = $blog;
        }

        return $blogs;
    }

    public function parseBlog(string $file)
    {
        $content = file_get_contents($file);
        return $this->parser->parse($content);
    }
}
