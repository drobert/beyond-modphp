<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent: */

namespace BeyondModPhp;

use Psr\Log\LoggerInterface;

class UserRepoEventStream extends UserRepoWrapper {

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(UserRepo $delegate, LoggerInterface $logger) {
        parent::__construct($delegate);
        $this->logger = $logger;
    }

    public function loadByName($name) {
        $start = microtime(true);
        $retval = parent::loadByName($name);
        $total = microtime(true) - $start;

        $msg = sprintf("$name\t%.6f usec", $total);

        // to illustrate multipart zmq multipart message routing, names shorter
        // than 4 characters are ERRORs, others are INFOs
        if (strlen($name) >= 3) {
            $this->logger->info($msg);
        } else {
            $this->logger->error($msg);
        }
        return $retval;
    }
}
