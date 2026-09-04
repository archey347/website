<?php

namespace Website;

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

        foreach ($this->sortJobs($jobs) as $job) {
            $job->run($callback);
        }
    }

    /**
     * Orders jobs so that everything a job lists in depends_on has run before
     * it does, letting a job like the sitemap rely on the pages it describes
     * already existing.
     */
    function sortJobs(array $jobs): array
    {
        $sorted = [];
        $state = [];

        foreach (array_keys($jobs) as $name) {
            $this->sortJob($name, $jobs, $state, $sorted, []);
        }

        return $sorted;
    }

    function sortJob(
        string $name,
        array $jobs,
        array &$state,
        array &$sorted,
        array $trail,
    ): void {
        if (($state[$name] ?? null) === "sorted") {
            return;
        }

        if (($state[$name] ?? null) === "sorting") {
            throw new \RuntimeException(
                "Jobs depend on each other in a loop: " .
                    implode(" -> ", [...$trail, $name]),
            );
        }

        $state[$name] = "sorting";

        foreach ($jobs[$name]["depends_on"] as $dependency) {
            if (!isset($jobs[$dependency])) {
                throw new \RuntimeException(
                    "Job \"$name\" depends on \"$dependency\", which does not exist",
                );
            }

            $this->sortJob(
                $dependency,
                $jobs,
                $state,
                $sorted,
                [...$trail, $name],
            );
        }

        $state[$name] = "sorted";
        $sorted[] = $jobs[$name]["job"];
    }

    function resetOutDir(): void
    {
        $outDir = $this->outDir;
        $cmd = "rm -rf {$outDir}";
        shell_exec($cmd);
        mkdir($this->outDir);
    }

    /**
     * Jobs are keyed by their file name relative to the job directory, which
     * is what depends_on refers to.
     */
    function getJobs(string $dir, string $prefix = ""): array
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
                $jobs += $this->getJobs($path, $prefix . $file . "/");
                continue;
            }

            $jobConfig = \yaml_parse_file($path);
            $jobs[$prefix . $file] = [
                "job" => $jobFactory->create(
                    $jobConfig["type"],
                    $jobConfig["options"],
                ),
                "depends_on" => $jobConfig["depends_on"] ?? [],
            ];
        }

        return $jobs;
    }
}
