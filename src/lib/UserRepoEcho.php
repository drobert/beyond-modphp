<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent: */

namespace BeyondModPhp;

class UserRepoEcho implements UserRepo {
    private static $singleton;

    private function __construct() { }

    public static function getInstance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new UserRepoEcho();
        }
        return self::$singleton;
    }

    public function loadByName($name) {
        return $name;
    }
}
