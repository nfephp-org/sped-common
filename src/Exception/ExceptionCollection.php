<?php

namespace NFePHP\Common\Exception;

use IteratorAggregate;
use Countable;
use Traversable;

class ExceptionCollection extends \Exception implements ExceptionInterface, IteratorAggregate, Countable
{
    /**
     * @var array
     */
    protected $exceptions = [];
    /**
     * @var string
     * stan: Property NFePHP\Common\Exception\ExceptionCollection::$shortMessage is never read, only written.
     */
    // private $shortMessage;

    /**
     * Constructor
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        // $this->shortMessage = $message;
    }

    /**
     * Set all of the exceptions
     * @param array $exceptions Array of exceptions
     * @return ExceptionCollection
     */
    public function setExceptions(array $exceptions)
    {
        $this->exceptions = array();
        foreach ($exceptions as $exception) {
            $this->add($exception);
        }
        return $this;
    }

    /**
     * Add exceptions to the collection
     * @param \Exception $exception Exception to add
     * @return \NFePHP\Common\Exception\ExceptionCollection
     */
    public function add(\Exception $exception)
    {
        $this->exceptions[] = $exception;
        $this->message = $this->__toString();
        return $this;
    }

    /**
     * Get the total number of request exceptions
     * @return int
     */
    public function count(): int
    {
        return count($this->exceptions);
    }

    /**
     * Allows array-like iteration over the request exceptions
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->exceptions);
    }

    /**
     * Get the first exception in the collection
     * @return \Exception
     */
    public function getFirst()
    {
        return $this->exceptions ? $this->exceptions[0] : null;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString()
    {
        $messages = array_map(function (\Exception $exception) {
            return $exception->getMessage();
        }, $this->exceptions);
        return implode(PHP_EOL, $messages);
    }
}
