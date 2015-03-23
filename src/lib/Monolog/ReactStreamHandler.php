<?php
// vim: set ts=4 sw=4 sts=4 ai cindent:

// see: https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/StreamHandler.php

namespace BeyondModPhp\Monolog;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use React\Stream\WritableStreamInterface;

class ReactStreamHandler extends AbstractProcessingHandler
{
    protected $stream;
    protected $url;
    private $errorMessage;

    /**
     * @param WriteableStreamInterface $stream
     * @param integer         $level          The minimum logging level at which this handler will be triggered
     * @param Boolean         $bubble         Whether the messages that are handled can bubble up the stack or not
     *
     * @throws \InvalidArgumentException If stream is not a resource or string
     */
    public function __construct(WritableStreamInterface $stream, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
	if (!isset($stream) || !$stream->isWritable()) {
	    throw new \InvalidArgumentException('Given write stream is not set or is not writeable');
	}
	$this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
	if (isset($this->stream)) {
	    $this->stream->close();
	}
        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
	$this->stream->write((string) $record['formatted']);
    }
}
