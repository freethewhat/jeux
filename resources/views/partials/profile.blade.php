
<div class="user-profile-container">
  <div class="user-profile">
    <img src="{{ asset( "images/uploads/profile/" . $user['image'] ) }}" alt="example Avatar" />
    <div class="user-info">
      <h4>{{ $user['username'] }}</h4>
      <span>
        Borrowing: 2 Games
      </span>
      <span>
        Lending: 1 Game
      </span>
    </div>
    @if(Auth::user()->id == $user['id'])

    @elseif(!$user['is_friend'])
      {!! Form::open(['action'=>['UserController@makeFriend', 'user_id' => $user['id']]]) !!}
      {!! Form::button('Add Friend', ['type' => 'submit', 'class' => "btn btn-primary", 'dusk' => 'make-friend']) !!}
      {!! Form::close() !!}
    @else
      <div>
        {!! Form::open(['action'=>['UserController@removeFriend', 'user_id' => $user['id']]]) !!}
        {!! Form::button('Remove Friend', ['type' => 'submit', 'class' => "btn btn-danger", 'dusk' => 'make-friend']) !!}
        {!! Form::close() !!}
      </div>
    @endif
  </div>
</div>
