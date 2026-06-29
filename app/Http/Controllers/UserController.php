<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserGroup;
use App\Services\UserService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules;


class UserController extends Controller
{

    private $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->fetchUsuer($filtre);
        $user_groupes = UserGroup::orderBy('name')->get()->map(function ($f) {
            return [
                'value' => $f->id,
                'label' => $f->name
            ];
        });
        // Map Group_user
        $userGroupNames = $user_groupes->pluck('label', 'value')->all();


        if (isset($output->data)) {
            foreach ($output->data as $user) {

                if (isset($user->user_group_id) && array_key_exists($user->user_group_id, $userGroupNames)) {
                    $user->user_group_name = $userGroupNames[$user->user_group_id];
                } else {
                    $user->user_group_name = null; // Or a default like 'Unknown'
                }
            }
        } else {
            // If $output is not a pagination object, assume it's a collection/array of users
             foreach ($output as $user) {
                if (isset($user->user_group_id) && array_key_exists($user->user_group_id, $userGroupNames)) {
                    $user->user_group_name = $userGroupNames[$user->user_group_id];
                } else {
                    $user->user_group_name = null;
                }
            }
        }
        return Inertia::render("User/Index", [
            'data' =>$output ,
            'user_groupes' => $user_groupes,
            "filters" => [
                "search" => $request->search
            ]
        ]);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'tel' => 'nullable|string|max:255',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'img' => 'nullable|file|mimes:jpeg,png,jpg',
            'user_group_id' => 'required|exists:user_groups,id',
        ]);

        if (!array_key_exists('password', $data) || empty($data['password'])) {
            $data['password'] = Hash::make('password'); // Mot de passe par défaut
        } else {
            $data['password'] = Hash::make($data['password']); // Hacher le mot de passe fourni
        }
        $data['email_verified_at'] = date("Y-m-d H:i:s");

        $this->service->create($data);

        return back()->with("message.success", "Enrégistrement effectuer");
    }

    public function update(Request $request,int $userID)
    {

        $user = User::find($userID);
        $data=$request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|lowercase|email|max:255|unique:users,email,{$user->id}",
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'user_group_id' => 'required|exists:user_groups,id',
            'img' => 'nullable|file|mimes:jpeg,png,jpg',
        ]);
        if($request->password) $data["password"] = Hash::make($request->password);

        $this->service->update($user, $data);

        return back()->with("message.success", "Enrégistrement effectuer");
    }

    public function destroy(int $userID)
    {
        $utilisateur= User::find($userID);
        if(User::query()->count() <= 1 || $utilisateur->is_you)
            return back()->with("message.error", "Impossible de supprimer cet utilisateur");

            $output = $this->service->delete($utilisateur);
            if (!empty($output['error'])) {
                return back()->with('message.error', $output['message']);
            }
            return back()->with('message.success', $output['message']);
        }
}
