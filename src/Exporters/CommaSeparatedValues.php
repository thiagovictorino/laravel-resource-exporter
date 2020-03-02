<?php

namespace thiagovictorino\ResourceExporter\Exporters;

/**
 * Class CommaSeparatedValues
 * @package thiagovictorino\ResourceExporter\Exporters
 */
class CommaSeparatedValues extends ExporterAbstract
{
  /**
   * Export data to CSV and return the content
   * @param iterable $collection
   * @return false|string content of file
   */
  public function export(iterable $collection)
  {
    $resources = $this->normalizeData($collection);
    $columns = $this->getColumnsName($resources->first());
    // Open the output stream
    $fh = fopen('php://output', 'w');

    // Start output buffering (to capture stream contents)
    ob_start();

    // CSV Header
    if (is_array($columns)) {
      fputcsv($fh, $columns);
    }

    // CSV Data
    foreach ($resources as $row) {
      fputcsv($fh, $this->getColumnsValue($row));
    }

    // Get the contents of the output buffer
    return ob_get_clean();
  }

  /**
   * Normalize data to get everything as a one page
   * @param iterable $collection
   * @return \Illuminate\Support\Collection
   */
  protected function normalizeData(iterable $collection)
  {

    $resources = collect();

    if (empty($collection)) {
      return $resources;
    }

    foreach ($collection as $page => $items) {
      foreach ($items as $item) {
        $resources->push($item);
      }
    }

    return $resources;
  }
}