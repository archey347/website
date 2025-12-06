<?php

namespace Website\Job;

interface JobInterface
{
    public function run(JobCallbackInterface $callback): void;
}
