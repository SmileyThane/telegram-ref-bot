@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Labels') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form method="POST" action="{{ route('update-labels') }}">
                            @csrf
                            @foreach($labels as $label)
                                <div class="row mb-3">
                                    <label for="{{$label->label}}" class="col-3 col-form-label text-md-end">{{$label->label}}</label>
                                    <div class="col-9">
                                        <input id="{{$label->label}}" class="form-control" name="labels[{{$label->label}}]" value="{{$label->alias}}">
                                    </div>
                                </div>
                            @endforeach
                                <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Save') }}
                                        </button>
                                    </div>
                                </div>
                        </form>
                </div>
            </div>
            <div class="mb-3">
            </div>
            <div class="card">
                <div class="card-header">{{ __('Users') }}</div>

                <div class="card-body">

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Telegram id</th>
                            <th scope="col">Score</th>
                            <th scope="col">Username</th>
                            <th scope="col">Card details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <th scope="row">{{$user->id}}</th>
                            <td>{{$user->telegram_id}}</td>
                            <td>{{$user->username}}</td>
                            <td>{{$user->card_details}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mb-3">
            </div>
            <div class="card">
                <div class="card-header">{{ __('Content links') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('update-links') }}">
                        @csrf
                        @foreach($links as $link)
                            <div  class="row mb-3">
                                <div class="col-12">
                                    <input class="form-control" name="links[]" value="{{$link->link}}">
                                </div>
                            </div>
                        @endforeach
                        <div id="links-form">
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="button" onclick="createLink()" class="btn btn-secondary">
                                    {{ __('Add') }}
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mb-3">
            </div>
            <div class="card">
                <div class="card-header">{{ __('Referrers') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('update-referrers') }}">
                        @csrf
                        @foreach($referrers as $referrer)
                            <div class="row mb-3">
                                <label class="col-3 col-form-label text-md-end">{{$referrer->name}}</label>
                                <div class="col-9">
                                    <input type="hidden" name="referrers_name[{{$label->id}}]" value="{{$referrer->name}}">
                                    <input id="referrers_{{$label->id}}" class="form-control" name="referrers[{{$referrer->id}}]" value="{{$referrer->link}}">
                                </div>
                            </div>
                        @endforeach
                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function createLink()
    {
        const input = document.createElement('input');
        input.classList.add('form-control');
        input.name = 'links[]'
        const div = document.createElement('div');
        div.classList.add('col-12');
        div.appendChild(input)
        const divParent = document.createElement('div');
        divParent.classList.add('row', 'mb-3');
        divParent.appendChild(div)

        let form = document.getElementById('links-form')
        form.appendChild(divParent)
    }
</script>
