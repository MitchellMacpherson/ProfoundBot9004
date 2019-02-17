<?php
declare(strict_types=1);

namespace App\Services\Configuration\Interfaces;

interface ConfigHelperInterface
{
    /**
     * Get a configuration value or section based on the specified period-separated key.
     *
     * @param   string $key Dot-separated syntax, where first part is always the 'filename'
     *                      parts beyond that represent array keys.
     *
     * @return  mixed
     */
    public function get(string $key);
}
