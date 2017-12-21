<?php

namespace Sztyup\Authsch;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;
use Sztyup\Authsch\Events\AuthSchLogin;
use Sztyup\Authsch\Model\SchAccount;

class SchProvider extends AbstractProvider
{
    protected $scopeSeparator = ' ';

    /** @var  Dispatcher */
    private $dispatcher;

    public function __construct(Request $request, UrlGenerator $router, Dispatcher $dispatcher, array $config)
    {
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

    public function user()
    {
        /** @var SchUser $user */
        $user = parent::user();

        $shacc = $this->shaccFromUser($user);

        $this->dispatcher->dispatch(new AuthSchLogin($user, $shacc));

        return $shacc;
    }

    protected function shaccFromUser(SchUser $user)
    {
        $matchFields = [
            'provider_user_id',
            'schacc',
            'neptun',
            'bme_id'
        ];

        foreach ($matchFields as $field) {
            if (!$user->hasField($field)) {
                continue;
            }
            if ($user->getField($field) == null || empty($user->getField($field))) {
                continue;
            }

            $shacc = SchAccount::where($field, $user->getField($field))->first();

            if ($shacc) {
                return $shacc;
            }
        }

        return SchAccount::create($user->toArray());
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
        $result = new SchUser($user['internal_id']);

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
                $result->setField($to, $user[$from]);
            }
        }

        if (isset($user['linkedAccounts'])) {
            if (isset($user['linkedAccounts']['schacc'])) {
                $result->setField('schacc', $user['linkedAccounts']['schacc']);
            }

            if (isset($user['linkedAccounts']['bme'])) {
                $arr = explode('@', $user['linkedAccounts']['bme']);
                $result->setField('bme_id', $arr[0]);
            }
        }

        if (isset($user['roomNumber'])) {
            $result->setField('dormitory', $user['roomNumber']['dormitory']);
            $result->setField('room_number', $user['roomNumber']['roomNumber']);
        }

        if (isset($user['bmeunitscope'])) {
            if (in_array('BME_VIK_NEWBIE', $user['bmeunitscope'])) {
                $result->setField('bme_status', SchUser::BME_STATUS_NEWBIE);
            } elseif (in_array('BME_VIK_ACTIVE', $user['bmeunitscope'])) {
                $result->setField('bme_status', SchUser::BME_STATUS_VIK_ACTIVE);
            } elseif (in_array('BME_VIK', $user['bmeunitscope'])) {
                $result->setField('bme_status', SchUser::BME_STATUS_VIK_PASSIVE);
            } elseif (in_array('BME', $user['bmeunitscope'])) {
                $result->setField('bme_status', SchUser::BME_STATUS_BME);
            } else {
                $result->setField('bme_status', SchUser::BME_STATUS_NONE);
            }
        }

        return $result;
    }
}
