<?php
// vim: set et ts=4 sw=4 sts=4 ai cindent: */

namespace BeyondModPhp;

interface UserRepo {
    public function loadByName($name);
}
