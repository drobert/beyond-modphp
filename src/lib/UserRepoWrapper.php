<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent: */

namespace BeyondModPhp;

abstract class UserRepoWrapper implements UserRepo {
    /** @var UserRepo delegate */
    protected $delegate;

    public function __construct(UserRepo $delegate) {
        $this->delegate = $delegate;
    }

    public function loadByName($name) {
        return $this->delegate->loadByName($name);
    }
}
