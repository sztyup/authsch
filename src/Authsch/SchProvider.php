<?php

namespace Sztyup\Authsch;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Sztyup\Authsch\Exceptions\AuthschException;
use Sztyup\Authsch\Exceptions\NoEmailException;

class SchProvider extends AbstractProvider
{
    protected $scopeSeparator = ' ';

    /** @var string */
    protected $base;

    public function __construct(Request $request, UrlGenerator $router, array $config, bool $local)
    {
        if ($local && in_array('niifPersonOrgID', $config['scopes'], true)) {
            Arr::forget($config['scopes'], array_search('niifPersonOrgID', $config['scopes'], true));
        }

        $this->setScopes(array_merge(['basic'], $config['scopes']));

        $this->base = $config['driver']['base'];

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
        return $this->buildAuthUrlFromBase($this->base . '/site/login', $state);
    }

    protected function getTokenUrl()
    {
        return $this->base . '/oauth2/token';
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
        $userUrl = $this->base . '/api/profile?access_token=' . $token;

        $response = $this->getHttpClient()->get($userUrl);

        return json_decode($response->getBody(), true);
    }
    
    public function forceRefresh($token)
    {
        $userUrl = $this->base . '/api/profile/resync?access_token=' . $token;

        $this->getHttpClient()->get($userUrl);
    }

    /**
     * @param string $code
     *
     * @return array
     * @throws AuthschException
     */
    public function getAccessTokenResponse($code)
    {
        if (empty($code)) {
            throw new AuthschException($this->request->query->get('error_description'));
        }

        return parent::getAccessTokenResponse($code);
    }

    /**
     * @param array $user
     *
     * @return User
     * @throws NoEmailException
     */
    protected function mapUserToObject(array $user): User
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

            if (isset($user['linkedAccounts']['vir'])) {
                $result['vir'] = $user['linkedAccounts']['vir'];
            }

            if (isset($user['linkedAccounts']['virUid'])) {
                $result['virUid'] = $user['linkedAccounts']['virUid'];
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
            if (in_array('BME_VIK_NEWBIE', $user['bmeunitscope'], true)) {
                $result['bme_status'] = SchUser::BME_STATUS_NEWBIE;
            } elseif (in_array('BME_VIK_ACTIVE', $user['bmeunitscope'], true)) {
                $result['bme_status'] = SchUser::BME_STATUS_VIK_ACTIVE;
            } elseif (in_array('BME_VIK', $user['bmeunitscope'], true)) {
                $result['bme_status'] = SchUser::BME_STATUS_VIK_PASSIVE;
            } elseif (in_array('BME', $user['bmeunitscope'], true)) {
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
        $return->email = $result['email'];
        $return->setRaw($result);

        if (empty($return->email)) {
            throw new NoEmailException($return);
        }

        return $return;
    }
}
