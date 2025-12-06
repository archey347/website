<?php

namespace Website;

use Website\Job\JobFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Builder
{
    private $jobDir;
    private $outDir;
    private Environment $twig;

    function __construct($jobDir = "jobs", $outDir = "out")
    {
        $this->jobDir = $jobDir;
        $this->outDir = $outDir;
        $this->twig = new Environment(new FilesystemLoader("templates"));
    }

    function run(): void
    {
        $jobs = $this->getJobs($this->jobDir);

        $callback = new BuilderJobCallback($this->outDir);

        foreach ($jobs as $job) {
            $job->run($callback);
        }
    }

    function resetOutDir(): void
    {
        $outDir = $this->outDir;
        $cmd = "rm -rf {$outDir}";
        shell_exec($cmd);
        mkdir($this->outDir);
    }

    function getJobs(string $dir): array
    {
        $jobFactory = new JobFactory($this->twig);

        $jobs = [];
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                $jobs = array_merge($jobs, $this->getJobs($path));
                continue;
            }

            $jobConfig = \yaml_parse_file($path);
            $jobs[] = $jobFactory->create(
                $jobConfig["type"],
                $jobConfig["options"],
            );
        }

        return $jobs;
    }
}
