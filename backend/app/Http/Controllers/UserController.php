<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(["success" => true, "users" => $users]);
    }

    private function validateUser($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors(), 401);
        }
        return $request->all();
    }

    private function saveUser($user, $input)
    {
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->save();
        return response()->json(['success' => true, 'user' => $user]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $this->validateUser($request);
            $result = $this->saveUser(new User, $input);
            return $result;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user !== NULL)
            return response()->json(["success" => true, "user" => $user]);

        return  response()->json(["success" => false, "message" => "Nenhum Registro Encontrado"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $this->validateUser($request);
            $user = User::find($id);
            if ($user) {
                $result = $this->saveUser($user, $input);
                return $result;
            }
            return  response()->json(["success" => false, "message" => "Usuário inexistente"]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return  response()->json(["success" => true]);
        }
        return  response()->json(["success" => false, "message" => "Usuário inexistente"]);
    }
}
