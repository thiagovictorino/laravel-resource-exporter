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
   * @param array $resources
   * @return array Columns name as array
   */
  protected function getColumnsName(iterable $resources): array {
    $columns = [];
    foreach ($resources as $resource) {
      foreach ($resource as $row) {
        if(!in_array($row['column'], $columns)) {
          $columns[] = $row['column'];
        }
      }
    }

    return $columns;
  }

  /**
   * Get the column values of a object
   * @param array $resource
   * @return array Columns name as array
   */
  protected function getColumnsValue(array $resource): array {
    $columns = [];
    foreach ($resource as $row) {
      $columns[] = $row['value'];
    }
    return $columns;
  }

  /**
   * @param $dataSource
   * @param string $prefix
   * @return array
   */
  protected function getResourceValues($dataSource, $prefix = "")
  {

    if (is_object($dataSource)) {
      return $this->getResourceValuesFromObject($dataSource, $prefix);
    }

    if (is_array($dataSource)) {
      return $this->getResourceValuesFromArray($dataSource, $prefix);
    }

    return [];
  }

  /**
   * @param \stdClass $dataSource
   * @param string $prefix
   * @return array
   */
  protected function getResourceValuesFromObject(\stdClass $dataSource, string $prefix) {
    $attributes = array_keys(get_object_vars($dataSource));
    $columns = [];

    foreach ($attributes as $attribute) {
      $value = $dataSource->$attribute;
      if(is_object($value) || is_array($value)){
        $columns = array_merge($columns, $this->getResourceValues($value, $prefix.$attribute.'.'));
        continue;
      }
      $columns[] = [
          'column' => $prefix.$attribute,
          'value' => $value
      ];
    }
    return $columns;
  }

  /**
   * @param array $dataSource
   * @param string $prefix
   * @return array
   */
  protected function getResourceValuesFromArray(array $dataSource, string $prefix) {
    $columns = [];
    foreach ($dataSource as $index => $value) {
      if(is_object($value) || is_array($value)){
        $columns = array_merge($columns, $this->getResourceValues($value, $prefix.$index.'.'));
        continue;
      }
      $columns[] = [
        'column' => $prefix.$index,
        'value' => $value
      ];
    }
    return $columns;
  }

}