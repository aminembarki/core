<?php namespace Flarum\Web\Actions;

use Illuminate\Http\Request;
use Session;
use Auth;
use Cookie;
use Config;
use View;

class IndexAction extends Action
{
    public function handle(Request $request, $params = [])
    {
        $config = [
            'modulePrefix' => 'flarum',
            'environment' => 'production',
            'baseURL' => '/',
            'apiURL' => '/api',
            'locationType' => 'hash',
            'EmberENV' => [],
            'APP' => []
        ];
        $data = [];
        $session = [];
        $alert = Session::get('alert');

        if (($user = $this->actor->getUser()) && $user->exists) {
            $session = [
                'userId' => $user->id,
                'token' => Cookie::get('flarum_remember')
            ];

            $response = $this->callAction('Flarum\Api\Actions\Users\ShowAction', ['id' => $user->id]);

            $data['users'] = [$response->getData()->users];
            $data['groups'] = $response->getData()->linked->groups;
        }


        return View::make('flarum.web::ember')
            ->with('title', Config::get('flarum::forum_title', 'Flarum Demo Forum'))
            ->with('styles', app('flarum.web.assetManager')->styles())
            ->with('scripts', app('flarum.web.assetManager')->scripts())
            ->with('config', $config)
            ->with('content', '')
            ->with('data', $data)
            ->with('session', $session)
            ->with('alert', $alert);
    }
}
