<?php


namespace Victorino\ResourceExporter\Exporters;

/**
 * Class ExporterAbstract
 * @package Victorino\ResourceExporter\Exporters
 */
abstract class ExporterAbstract implements ExporterInterface
{
  /**
   * Get the column names of a object
   * @param \stdClass $data
   * @return array Columns name as array
   */
  protected function getColumnsName(\stdClass $data)
  {
    return array_keys(get_object_vars($data));
  }

  /**
   * Get the column values of the object
   * @param \stdClass $data
   * @return void
   */
  protected function getColumnsValue(\stdClass $data)
  {
    return $this->sanitalize(array_values(get_object_vars($data)));

  }

  /**
   * Sanitalize data to prevent inside array
   * @param $data
   */
  protected function sanitalize($data)
  {
    $sanitalizedData = [];
    foreach ($data as $item) {
      if (is_array($item)) {
        $sanitalizedData[] = json_encode($item);
        continue;
      }

      $sanitalizedData[] = $item;
    }
    return $sanitalizedData;
  }

}