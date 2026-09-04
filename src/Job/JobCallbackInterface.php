<?php

namespace Website\Job;

interface JobCallbackInterface
{
    public function AddPage(string $path, string $content): void;

    /**
     * The paths of every page added so far.
     */
    public function getPages(): array;
}
