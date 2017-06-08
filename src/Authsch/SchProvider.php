<?php

namespace Sztyup\Authsch;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class SchProvider extends AbstractProvider
{
    protected $scopes = [
        'basic',
        'displayName',
        'sn',
        'givenName',
        'mail',
        'eduPersonEntitlement',
        'bmeunitscope',
        'linkedAccounts',
        'mobile'
    ];

    protected $scopeSeparator = ' ';

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://auth.sch.bme.hu/site/login', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://auth.sch.bme.hu/oauth2/token';
    }

    protected function getTokenFields($code)
    {
        return array_add(
            parent::getTokenFields($code), 'grant_type', 'authorization_code'
        );
    }
    protected function getUserByToken($token)
    {
        $userUrl = 'https://auth.sch.bme.hu/api/profile?access_token=' . $token;

        $response = $this->getHttpClient()->get($userUrl);

        $user = json_decode($response->getBody(), true);

        return $user;
    }

    protected function mapUserToObject(array $user)
    {
        $result = new User();

        $result->name = $user["displayName"];
        $result->email = $user["mail"];
        $result->provider_user_id = $user["internal_id"];

        if(isset($user["linkedAccounts"])) {
            if(isset($user["linkedAccounts"]["schacc"])) {
                $result->schacc = $user["linkedAccounts"]["schacc"];
            }

            if(isset($user["linkedAccounts"]["bme"])) {
                $result->bme_id = $user["linkedAccounts"]["bme"];
                $arr = explode("@", $result->bme_id);
                $result->bme_id = $arr[0];
            }
        }

        if(isset($user["roomNumber"])) {
            $result->dormitory = $user["roomNumber"]["dormitory"];
            $result->room_number = $user["roomNumber"]["roomNumber"];
        }

        if(isset($user["niifPersonOrgID"])) {
            $result->neptun = $user["niifPersonOrgID"];
        }

        if(isset($user["mobile"])) {
            $result->phone = $user["mobile"];
        }

        if(isset($user["eduPersonEntitlement"])) {
            $result->circles = $user["eduPersonEntitlement"];
        }

        if(isset($user["bmeunitscope"])) {
            if(in_array("BME_VIK_NEWBIE", $user["bmeunitscope"])) {
                $result->bme_status = 4;
            }
            elseif(in_array("BME_VIK_ACTIVE", $user["bmeunitscope"])) {
                $result->bme_status = 3;
            }
            elseif(in_array("BME_VIK", $user["bmeunitscope"])) {
                $result->bme_status = 2;
            }
            elseif(in_array("BME", $user["bmeunitscope"])) {
                $result->bme_status = 1;
            }
            else {
                $result->bme_status = 0;
            }
        }


        return $result;
    }
}
