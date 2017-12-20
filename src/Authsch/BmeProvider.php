<?php

namespace Sztyup\Authsch;

class BmeProvider extends SchProvider
{
    protected function getAuthUrl($state)
    {
        $target = $this->buildAuthUrlFromBase("https://auth.sch.bme.hu/site/login/provider/bme", $state);

        return "https://auth.sch.bme.hu/Shibboleth.sso/Login?target=" . urlencode($target);
    }
}
