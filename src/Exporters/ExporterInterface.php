<?php

namespace Victorino\ResourceExporter\Exporters;

interface ExporterInterface
{
    public function export(iterable $data);
}
