<?php

namespace App\Http\Controllers;

use App\Mail\Raffle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RaffleController extends Controller
{


    private function doRaffle(&$users)
    {
        $fault = false;
        $usersToRaffle = $users;
        foreach ($users as $key => &$item) {

            /** Removing current user */
            $array_without_me = $usersToRaffle;
            unset($array_without_me[$key]);

            /** Checking if the user get himself */
            if (count($array_without_me) > 0) {
                $key_rand = array_rand($array_without_me);
                $friend = $usersToRaffle[$key_rand]["name"];
                $item["friend"] = $friend;

                /** Sending email and saving */
                Mail::to($item["email"])->send(new Raffle($item));
                $user = User::find($item["_id"]);
                $user->friend = $friend;
                $user->save();

                unset($usersToRaffle[$key_rand]);
            } else {
                $fault = true;
            }
        }
        return $fault ? NULL : $users;
    }

    private function raffle(&$users)
    {
        $checking = NULL;
        /** If the raffle has a fault try again */
        while ($checking === NULL) {
            $checking = $this->doRaffle($users);
        }
        return $users;
    }

    /**
     * Creating the Raffle from Users
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->toArray();
        if ($users !== NULL && count($users) > 0) {
            $this->raffle($users);
            return response()->json(["success" => true]);
        }
        return response()->json(["success" => false, "message" => "Não há usuários para sorteio"]);
    }
}
