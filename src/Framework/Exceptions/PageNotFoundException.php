<?php
/**
 * This file contains the Framework/Exceptions/PageNotFoundException.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * Section Name: Exceptions
 * File Name: PageNotFoundException.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
namespace Framework\Exceptions;

use DomainException;

/**
 * Class PageNotFoundException
 *
 * This class represents an exception that is thrown when a requested page is not found.
 * It extends the built-in DomainException class.
 */
class PageNotFoundException extends DomainException
{
}