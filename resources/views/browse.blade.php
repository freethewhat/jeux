@extends("layouts.two-column")

@section("pageheading")
  <h2 class="lead">Browse</h2>
@endsection

@section("left-column")
  @component('partials.index', ['games' => $games, 'browse' => $browse])
    oops
  @endcomponent
@endsection

  @section("right-column")
  @component('partials.filter')
  @endcomponent
@endsection
