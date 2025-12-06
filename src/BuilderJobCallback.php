<?php

namespace Website;

use Website\Job\JobCallbackInterface;

class BuilderJobCallback implements JobCallbackInterface
{
    private $outDir;

    public function __construct(string $outDir)
    {
        $this->outDir = $outDir;
    }

    public function AddPage(string $path, string $content): void
    {
        $filePath = $this->outDir . DIRECTORY_SEPARATOR . $path;
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir(dirname($filePath), 0777, true);
        }
        file_put_contents($filePath, $content);
    }
}
