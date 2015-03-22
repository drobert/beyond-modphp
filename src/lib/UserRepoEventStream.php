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
        $this->logger->info("$name\t$total"); // event, name and load time
        return $retval;
    }
}
