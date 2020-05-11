<?php

namespace Victorino\ResourceExporter\Exporters;

/**
 * Class CommaSeparatedValues
 * @package Victorino\ResourceExporter\Exporters
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
    $flatted_resources = $this->flattenData($resources);
    $columns = $this->getColumnsName($flatted_resources);

    $fh = fopen('php://output', 'w');

    ob_start();

    /**
     * CSV columns name
     */
    if (is_array($columns)) {
      fputcsv($fh, $columns);
    }

    /**
     * CSV values
     */
    foreach ($flatted_resources as $row) {
      fputcsv($fh, (array)$this->getColumnsValue($row));
    }

    return ob_get_clean();
  }

  /**
   * Flatten the resource data by going deeply into results
   * @param iterable $resources
   * @return \Illuminate\Support\Collection
   */
  protected function flattenData(iterable $resources)
  {
    $flatted = collect();
    if (empty($resources)) {
      return $flatted;
    }
    foreach ($resources as $item) {
      $flatted->push($this->getResourceValues($item));
    }
    return $flatted;
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