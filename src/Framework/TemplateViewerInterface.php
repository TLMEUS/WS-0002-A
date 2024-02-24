<?php
/**
 * This file contains the src/Framework/TemplateViewerInterface.php interface for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: TemplateViewerInterface.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
namespace Framework;

/**
 * Interface TemplateViewerInterface
 *
 * This interface defines the methods for a template viewer class.
 *
 * @package Application\Templates
 */
interface TemplateViewerInterface {
    public function render(string $template, array $data = []): string;    
}