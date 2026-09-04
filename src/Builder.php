<?php

namespace Website;

use Website\Job\FinalJobInterface;
use Website\Job\JobFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Builder
{
    private $jobDir;
    private $outDir;
    private array $site;
    private Environment $twig;

    function __construct(
        $jobDir = "jobs",
        $outDir = "out",
        $siteFile = "site.yaml",
    ) {
        $this->jobDir = $jobDir;
        $this->outDir = $outDir;
        $this->site = $this->loadSite($siteFile);
        $this->twig = new Environment(new FilesystemLoader("templates"));

        // So templates can reach it as {{ site.base_url }} without every job
        // having to pass it through.
        $this->twig->addGlobal("site", $this->site);
    }

    /**
     * Settings that belong to the site as a whole rather than to one job, so
     * that things like the base url are defined in exactly one place.
     */
    function loadSite(string $file): array
    {
        $site = \yaml_parse_file($file);
        if (!is_array($site)) {
            throw new \RuntimeException("Unable to read site config: $file");
        }

        foreach ($site as $key => $value) {
            $override = getenv("SITE_" . strtoupper($key));
            if ($override !== false && $override !== "") {
                $site[$key] = $override;
            }
        }

        return $site;
    }

    function run(): void
    {
        $this->resetOutDir();
        $jobs = $this->getJobs($this->jobDir);

        $callback = new BuilderJobCallback($this->outDir);

        // Some jobs (the sitemap) need to see what everything else produced,
        // so hold those back until the rest have run.
        $finalJobs = [];

        foreach ($jobs as $job) {
            if ($job instanceof FinalJobInterface) {
                $finalJobs[] = $job;
                continue;
            }

            $job->run($callback);
        }

        foreach ($finalJobs as $job) {
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
        $jobFactory = new JobFactory($this->twig, $this->site);

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
