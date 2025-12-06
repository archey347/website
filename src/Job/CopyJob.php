<?php

namespace Website\Job;

class CopyJob implements JobInterface
{
    protected array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function run(JobCallbackInterface $cb): void
    {
        $src = $this->options["src"];
        $dest = $this->options["dest"];

        $this->copyDir($src, $dest, $cb);
    }

    public function copyDir(string $src, string $dest, JobCallbackInterface $cb)
    {
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }
            $srcFile = $src . DIRECTORY_SEPARATOR . $file;
            $destFile = $dest . DIRECTORY_SEPARATOR . $file;
            if (is_dir($srcFile)) {
                $this->copyDir($srcFile, $destFile, $cb);
                continue;
            }

            $content = file_get_contents($srcFile);
            $cb->AddPage($destFile, $content);
        }
    }
}
