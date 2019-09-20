<?php
namespace Viewer;

final class Compose
{

    /**
     * 
     */
    private $sections = [];

    public function __construct()
    {



    }

    /**
     * 
     */
    public function add(?string $content, string $priority = '000'): void
    {

        if (!is_null($content)) {

            $priority = preg_match('/^[a-z\d]{3}$/', $priority) ? $priority : '000';
            $this->sections[$priority][] = $content;

        }

    }

    /**
     * 
     */
    public function build()
    {

        ksort($this->sections, SORT_STRING);
        foreach ($this->sections as &$section) {

            $section = is_array($section) ? implode(PHP_EOL, $section) : $section;

        }

        $content = implode(PHP_EOL, $this->sections);
        echo $content;

    }

    /**
     * 
     */
    private function sorting() {

    }

}