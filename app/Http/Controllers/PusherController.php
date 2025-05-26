<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PusherController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        return view('index');
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function broadcast(Request $request): Factory|View|Application
    {
//        if(broadcast(new PusherBroadcast($request->get('message')))->toOthers()) {
        if(broadcast(new PusherBroadcast('hello world !'))->toOthers()) {
            $data = [
                'title'    => "{}'s reviews",
                'template' => 'review.list',
            ];
            return view('with_login_common', ['message' => $request->get('message'), 'data' => $data]);
        } else {
            dd(broadcast(new PusherBroadcast($request->get('message')))->toOthers());
        }


    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function receive(Request $request): Factory|View|Application
    {
        $data = [
            'title'    => "{}'s reviews",
            'template' => 'review.list',
        ];
        return view('with_login_common', ['message' => $request->get('message'), 'data' => $data]);
    }
}
