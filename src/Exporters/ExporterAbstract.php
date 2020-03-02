<?php


namespace thiagovictorino\ResourceExporter\Exporters;

/**
 * Class ExporterAbstract
 * @package thiagovictorino\ResourceExporter\Exporters
 */
abstract class ExporterAbstract implements ExporterInterface
{
  /**
   * Get the column names of a object
   * @param \stdClass $data
   * @return array Columns name as array
   */
  protected function getColumnsName(\stdClass $data) {
    return array_keys(get_object_vars($data));
  }

  /**
   * Get the column values of the object
   * @param \stdClass $data
   * @return array Values as array
   */
  protected function getColumnsValue(\stdClass $data) {
    return array_values(get_object_vars($data));
  }
}