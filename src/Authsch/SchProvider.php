<?php

namespace Sztyup\Authsch;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;

class SchProvider extends AbstractProvider
{
    protected $scopeSeparator = ' ';

    public function __construct(Request $request, UrlGenerator $router, array $config)
    {
        $this->setScopes($config['scopes']);

        parent::__construct($request, $config['client_id'], $config['client_secret'], $router->route($config['redirect']));
    }

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
        $result = new SchUser();

        $mapping = [
            'displayName' => 'displayName',
            'sn' => 'lastName',
            'givenName' => 'firstName',
            'mail' => 'email',
            'niifPersonOrgID' => 'neptun',
            'mobile' => 'mobile',
            'eduPersonEntitlement' => 'circles',
            'entrants' => 'entrants',
            'niifEduPersonAttendedCourse' => 'courses',
            'admembership' => 'admembership'
        ];

        foreach($mapping as $from => $to) {
            if(in_array($from, $user)) {
                $result->$to = $user[$from];
            }
        }

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

        if(isset($user["bmeunitscope"])) {
            if(in_array("BME_VIK_NEWBIE", $user["bmeunitscope"])) {
                $result->bme_status = SchUser::BME_STATUS_NEWBIE;
            }
            elseif(in_array("BME_VIK_ACTIVE", $user["bmeunitscope"])) {
                $result->bme_status = SchUser::BME_STATUS_VIK_ACTIVE;
            }
            elseif(in_array("BME_VIK", $user["bmeunitscope"])) {
                $result->bme_status = SchUser::BME_STATUS_VIK_PASSIVE;
            }
            elseif(in_array("BME", $user["bmeunitscope"])) {
                $result->bme_status = SchUser::BME_STATUS_BME;
            }
            else {
                $result->bme_status = SchUser::BME_STATUS_NONE;
            }
        }

        return $result;
    }
}
