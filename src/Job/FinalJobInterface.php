<?php

namespace Website\Job;

/**
 * A job that needs to see everything the other jobs produced, so it gets run
 * once they are all done.
 */
interface FinalJobInterface extends JobInterface
{
}
