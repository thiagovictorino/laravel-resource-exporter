<?php

namespace Victorino\ResourceExporter\Exporters;

use Illuminate\Support\Facades\Storage;
use Victorino\ResourceExporter\Exceptions\UrlParserException;
use Victorino\ResourceExporter\Url\Builder;
use Victorino\ResourceExporter\Url\Parser;

class Exporter
{
    /**
     * @var Parser
     */
    protected $urlParser;

    public function __construct(Parser $parser)
    {
        $this->urlParser = $parser;
    }

    /**
     * Export to CSV and save in file.
     * @param Builder $builder
     * @param string|null $name The name of file
     * @return string Name of the file saved
     * @throws UrlParserException
     */
    public function getCSV(Builder $builder, ?string $name)
    {
        $result = $this->urlParser->load($builder);
        /**
         * @var $engine CommaSeparatedValues
         */
        $engine = resolve(CommaSeparatedValues::class);

        $fileContent = $engine->export($result);
        $fileName = $name ?? $this->generateRandomName().'.csv';
        Storage::disk(config('resource-exporter.disk'))->put(
      $fileName,
      $fileContent);

        return $fileName;
    }

    /**
     * Generate a random name to file.
     * @return string
     */
    protected function generateRandomName(): string
    {
        return md5(uniqid(rand(), true));
    }
}
