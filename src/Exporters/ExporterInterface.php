<?php


namespace thiagovictorino\ResourceExporter\Exporters;


interface ExporterInterface
{
  public function export(iterable $data);
}