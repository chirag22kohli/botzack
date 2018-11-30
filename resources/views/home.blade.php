@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
<iframe src="https://engine.4dsply.com/Pixel/IFrame?cid=37918" frameborder="0" width="1" height="1"></iframe>
                    <iframe
                        allow="microphone;"
                        width="350"
                        height="430"
                        src="https://console.dialogflow.com/api-client/demo/embedded/22d0bf19-c20b-4405-8fb7-fcc199b6405d">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
