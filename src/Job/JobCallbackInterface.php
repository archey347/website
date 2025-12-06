<?php

namespace Website\Job;

interface JobCallbackInterface
{
    public function AddPage(string $path, string $content): void;
}
