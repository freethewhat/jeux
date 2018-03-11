<?php
//TODO::put notifications stuff in here!

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Inventory;

use Illuminate\Notifications\Notification;
use Illuminate\Http\Request;
use App\Notifications\FriendRequest;
use App\Notifications\BorrowRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
          return view('user', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
      /*
        The show method gets a users profile and return a list of games owned
        and borrowed by that user.

        TODO determine what happens if user does not exist
        TODO determine if game is borrowed by profile user
        TODO determine profile user borrowed game count
        TODO determine profile user lended game count
        TODO determine profile user total owned games
      */

      // Get Authenticated user information
      $auth_user = Auth::User();

      // Get Auth user friends
      $auth_friends = $auth_user->friends;

      //create easily searchable array of friend IDs
      $auth_friends_ids = array();
      for($i = 0; $i < sizeof($auth_friends); $i++) {
          array_push($auth_friends_ids, $auth_friends[$i]->id);
      }

      // Set Authenticated user information
      $data['authUser'] = [
        'id'=>$auth_user->id,
        'image'=>$auth_user->image,
        'username'=>$auth_user->username,
        'email'=>$auth_user->email,
        'name'=>$auth_user->name,
      ];

      // Get user profile information with user inventory
      $userProfile = User::with('inventory')->where('username', $username)->get();

      // determine if user is a friends
      $is_friend = (array_search($userProfile[0]->id, $auth_friends_ids) ? true : false);

      $userInventory = $userProfile[0]->inventory;

      // Set user profile information
      $data['userProfile'] = [
        'id'=>$userProfile[0]->id,
        'image'=>$userProfile[0]->image,
        'username'=>$userProfile[0]->username,
        'email'=>$userProfile[0]->email,
        'name'=>$userProfile[0]->name,
        'is_friend'=>$is_friend,
      ];

      $data['games'] = [];
      for($i = 0; $i < sizeof($userInventory); $i++) {

        // Get game information
        $gameQuery = Game::with('inventory')->where('id', $userInventory[$i]->game_id)->get();
        $gameInventory = $gameQuery[0]->inventory;

        // Get Owner information
        $owner = [];
        $own_game = false;

        for($j = 0; $j < sizeof($gameInventory); $j++){
          $user = User::find($gameInventory[$j]->owner_id);

          // set if authed user owns game
          if($auth_user->id == $user->id) {
            $own_game = true;
          }

          // search if game owner is a friend
          $is_friend = array_search($user->id, $auth_friends_ids);

          // if game owner is a freind add them to the owners (you can only borrow from friends)
          if($is_friend) {
            $user =[
              'id'=>$user->id,
              'username'=>$user->username,
              'image'=>$user->image,
            ];

            // Add user to the owner array
            array_push($owner, $user);
          }
        }

        // Set game information
        $game = [
          'game_id'=>$gameQuery[0]->id,
          'own_game'=>$own_game,
          'name'=>$gameQuery[0]->name,
          'image'=>$gameQuery[0]->image,
          'year'=>$gameQuery[0]->year,
          'min_player'=>$gameQuery[0]->min_player,
          'max_player'=>$gameQuery[0]->max_player,
          'min_age'=>$gameQuery[0]->min_age,
          'min_play'=>$gameQuery[0]->min_play,
          'max_play'=>$gameQuery[0]->max_play,
          'description'=>$gameQuery[0]->description,
          'instructions'=>$gameQuery[0]->instructions,
          'borrower_id'=>$userInventory[$i]->borrower_id,
          'owner'=>$owner,
        ];

        // Add game to response data
        array_push($data['games'], $game);
      }

      // return $data for the view
       return view('user', ['data' => $data]);
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
      $user = User::find($id);

      // that the old information is staying with the information.
      $user->name = $request->name;
      $user->image = $request->image;
      $user->username = $request->username;
      $user->email = $request->email;

      $user->save();
    }

    public function makeFriend($id)
    {
      $friend = User::find($id);
      Auth::user()->friends()->attach($friend);
      $friend->notify(new FriendRequest(Auth::user()));
    }

    public function borrowGame($inventory_id)
    {
      $borrower = Auth::user();
      $owner_id = Inventory::find($inventory_id)->owner_id;
      $owner = User::find($owner_id);
      $game_id = Inventory::find($inventory_id)->game_id;
      $game = Game::find($game_id);
      $owner->notify(new BorrowRequest($borrower, $game));

    }

    public function notificationRead($notification_id)
    {

      $user = Auth::user();
      $notification = $user->unreadNotifications->find($notification_id);
      $notification->markAsRead();

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

      $user->delete();
    }


    public function showSettings() {
        return view('settings');
    }
}
