<?php

namespace Sztyup\Authsch;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class SchProvider extends AbstractProvider
{
    protected $scopeSeparator = ' ';

    /** @var  Dispatcher */
    private $dispatcher;

    public function __construct(Request $request, UrlGenerator $router, Dispatcher $dispatcher, array $config)
    {
        if (in_array('niifPersonOrgID', $config['scopes']) && app()->isLocal()) {
            Arr::forget($config['scopes'], 'niifPersonOrgID');
        }

        $this->setScopes(array_merge(['basic'], $config['scopes']));

        $this->dispatcher = $dispatcher;

        parent::__construct(
            $request,
            $config['driver']['client_id'],
            $config['driver']['client_secret'],
            $this->getRedirectRoute($config['driver']['redirect'], $router)
        );
    }

    protected function getRedirectRoute($config, UrlGenerator $router)
    {
        if (is_array($config)) {
            return $router->route($config[0], $config[1]);
        } else {
            return $router->route($config);
        }
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
            parent::getTokenFields($code),
            'grant_type',
            'authorization_code'
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
        $result = [];

        $mapping = [
            'displayName' => 'name',
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

        foreach ($mapping as $from => $to) {
            if (array_key_exists($from, $user)) {
                $result[$to] = $user[$from];
            }
        }

        if (isset($user['linkedAccounts'])) {
            if (isset($user['linkedAccounts']['schacc'])) {
                $result['schacc'] = $user['linkedAccounts']['schacc'];
            }

            if (isset($user['linkedAccounts']['bme'])) {
                $arr = explode('@', $user['linkedAccounts']['bme']);
                $result['bme_id'] = $arr[0];
            }
        }

        if (isset($user['roomNumber'])) {
            $result['dormitory'] = $user['roomNumber']['dormitory'];
            $result['room_number'] = $user['roomNumber']['roomNumber'];
        }

        if (isset($user['bmeunitscope'])) {
            if (in_array('BME_VIK_NEWBIE', $user['bmeunitscope'])) {
                $result['bme_status'] = SchUser::BME_STATUS_NEWBIE;
            } elseif (in_array('BME_VIK_ACTIVE', $user['bmeunitscope'])) {
                $result['bme_status'] = SchUser::BME_STATUS_VIK_ACTIVE;
            } elseif (in_array('BME_VIK', $user['bmeunitscope'])) {
                $result['bme_status'] = SchUser::BME_STATUS_VIK_PASSIVE;
            } elseif (in_array('BME', $user['bmeunitscope'])) {
                $result['bme_status'] = SchUser::BME_STATUS_BME;
            } else {
                $result['bme_status'] = SchUser::BME_STATUS_NONE;
            }
        } else {
            $result['bme_status'] = SchUser::BME_STATUS_NONE;
        }

        $return = new User();

        $return->id = $user['internal_id'];
        $return->name = $result['name'];
        $return->email = $return['email'];
        $return->setRaw($result);

        return $return;
    }
}
