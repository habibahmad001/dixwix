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
        return view('chating.index');
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function broadcast(Request $request): Factory|View|Application
    {
//        $pusher = new Pusher\Pusher("3f2e5ae628ca52fd09af", "7f1f128b5eaeb0b383c8", "1659970", array('cluster' => 'ap2'));
//        $pusher->trigger('my-channel', 'my-event', array('message' => 'hello world 111'));
//        if(broadcast(new PusherBroadcast($request->get('message')))->toOthers()) {
        if(broadcast(new PusherBroadcast('hello world !'))->toOthers()) {
            return view('chating.broadcast', ['message' => $request->get('message')]);
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
        return view('chating.receive', ['message' => $request->get('message')]);
    }
}
