<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\CsvBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Export initialized event.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ExportedEvent extends Event
{
    protected $content;

    /**
     * @param string $content Csv data
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * Get the csv data.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the csv data.
     *
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
