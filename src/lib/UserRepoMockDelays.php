<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent: */

namespace BeyondModPhp;

use Psr\Log\LoggerInterface;

class UserRepoMockDelays implements UserRepo {
    /** @var LoggerInterface $logger log implementation */
    private $logger;
    private $boundMin;
    private $boundMax;

    /**
      * @param int $min lower bound of random pause in microseconds
      * @param int $max upper bound of random pause in microseconds
      */
    public function __construct($min, $max, LoggerInterface $logger) {
        $this->boundMin = $min;
        $this->boundMax = $max;
        $this->logger = $logger;
    }

    public function loadByName($name) {
        $delay = rand($this->boundMin,$this->boundMax);
        $this->logger->info('Delaying user load', array('user', $name, 'delay (msec)', $delay/1000));
        usleep($delay);
        $this->logger->info('User loading complete', array('user', $name));
        return $name;
    }
}
