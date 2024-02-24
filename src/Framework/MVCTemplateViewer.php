<?php
/**
 * This file contains the src/Framework/MVCTemplateViewer.php class for project WS-0000-A.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: MVCTemplateViewer.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

/**
 * Class MVCTemplateViewer
 *
 * This class implements the TemplateViewerInterface and provides a method to render a template with given data.
 */
class MVCTemplateViewer implements TemplateViewerInterface {

    /**
     * Renders the given template with the provided data and returns the rendered result as a string.
     *
     * @param string $template The path to the template file relative to the views directory.
     * @param array $data Optional. The data to be passed into the template. Default is an empty array.
     *
     * @return string The rendered template as a string.
     */
    public function render(string $template, array $data = []): string {
        $views_dir = dirname(__DIR__) . "/App/Views/";
        $code = file_get_contents($views_dir . $template);
        if (preg_match('#^{% extends "(?<template>.*)" %}#', $code, $matches) === 1) {
            $base = file_get_contents($views_dir . $matches["template"]);
            $blocks = $this->getBlocks($code);
            $code = $this->replaceYields($base, $blocks);
        }
        $code = $this->loadIncludes($views_dir, $code);
        $code = $this->replaceVariables($code);
        $code = $this->replacePHP($code);
        extract(array: $data, flags: EXTR_SKIP);
        ob_start();
        eval("?>$code");
        return ob_get_clean();
    }

    /**
     * Replaces variables in the given code with their corresponding values.
     *
     * @param string $code The code to replace variables in.
     *
     * @return string The code with variables replaced as a string.
     */
    private function replaceVariables(string $code): string {
        return preg_replace(pattern: "#{{\s*(\S+)\s*}}#",
            replacement: "<?= htmlspecialchars(\$$1 ?? '') ?>",
            subject: $code);
    }

    /**
     * Replaces PHP code blocks in the given code with evaluated PHP code.
     *
     * @param string $code The code to be processed.
     *
     * @return string The code with the PHP code blocks replaced.
     */
    private function replacePHP(string $code): string {
        return preg_replace(pattern: "#{%\s*(.+)\s*%}#", replacement: "<?php $1 ?>", subject: $code);
    }

    /**
     * Extracts the content of all block tags in the given code and returns them as an associative array.
     *
     * @param string $code The code to search for block tags.
     *
     * @return array An associative array where the keys are the names of the blocks and the values
     * are the content of the blocks.
     */
    private function getBlocks(string $code): array {
        preg_match_all(pattern: "#{% block (?<name>\w+) %}(?<content>.*?){% endblock %}#s",
            subject: $code, matches: $matches,
            flags: PREG_SET_ORDER);
        $blocks = [];
        foreach ($matches as $match) {
            $blocks[$match["name"]] = $match["content"];
        }
        return $blocks;
    }

    /**
     * Replaces yield placeholders with corresponding blocks in the given code.
     *
     * @param string $code The code containing yield placeholders.
     * @param array $blocks An associative array of block names and their corresponding contents.
     *
     * @return string The code with the yield placeholders replaced by their respective blocks.
     */
    private function replaceYields(string $code, array $blocks): string {
        preg_match_all(pattern: "#{% yield (?<name>\w+) %}#",
            subject: $code,
            matches: $matches,
            flags: PREG_SET_ORDER);
        foreach ($matches as $match) {
            $name = $match["name"];
            $block = $blocks[$name];
            $code = preg_replace(pattern: "#{% yield $name %}#", replacement: $block, subject: $code);
        }
        return $code;
    }

    /**
     * Loads included templates and replaces include placeholders with their contents in the given code.
     *
     * @param string $dir The directory path where the templates are located.
     * @param string $code The code containing include placeholders.
     *
     * @return string The code with the include placeholders replaced by the contents of the included templates.
     */
    private function loadIncludes(string $dir, string $code): string {
        preg_match_all(pattern: '#{% include "(?<template>.*?)" %}#',
            subject: $code,
            matches: $matches,
            flags: PREG_SET_ORDER);
        foreach ($matches as $match) {
            $template = $match["template"];
            $contents = file_get_contents(filename: $dir . $template);
            $code = preg_replace(pattern: "#{% include \"$template\" %}#", replacement: $contents, subject: $code);
        }
        return $code;
    }
}