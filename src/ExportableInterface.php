<?php

namespace DonkeyWorks\Roast;

/**
 * Interface allowing customization of how a result data object is fed into the serializer
 *
 * @author sbouw
 */
interface ExportableInterface
{
    /**
     * Export a result data object to valid result data
     *
     * @return string|null|array|object|int|float|bool
     */
    public function export();
}
