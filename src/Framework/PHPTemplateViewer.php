<?php
/**
 * This file contains the src/Framework/PHPTemplateViewer.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: PHPTemplateViewer.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Class PHPTemplateViewer
 *
 * Implements the TemplateViewerInterface and provides a method to render PHP templates.
 *
 * @noinspection PhpUnused
 */
class PHPTemplateViewer implements TemplateViewerInterface
{
    /**
     * Renders a template with provided data.
     *
     * @param string $template The path to the template file.
     * @param array $data The associative array of data to be passed to the template (default: empty array).
     *
     * @return string The rendered template as a string.
     */
    public function render(string $template, array $data = []): string {
        extract(array: $data, flags: EXTR_SKIP);
        ob_start();
        require dirname(path: __DIR__, levels: 1) . "/Views/$template";
        return ob_get_clean();
    }
}