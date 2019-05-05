{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.master')

@section('title')
    @lang('server.files.add.header')
@endsection

@section('content-header')
    <h1>Mods<small>All Mods available on the system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Mods</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
    

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Installed</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        @foreach ($installed as $mod)
                             <tr>
                                <td>
                                    @if($mod->steam_name)
                                    <i class="fa fa-steam"></i> {{$mod->steam_name}} <code>{{$mod->steam_id}}</code>
                                    @else
                                    {{$mod->mod->name}}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(! $mod->mod)
                                        <span class="label bg-maroon">Not Installed</span>
                                    @else
                                        <span class="label label-success">Installed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-xs btn-danger" href="{{ route('server.console', $mod->id) }}"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- @if ($mods->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $mods->appends(['query' => Request::input('query')])->render() !!}</div>
                </div>
            @endif --}}
        </div>


        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Available</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        @foreach ($mods as $mod)
                        {{$mod}}
                        <tr>
                                <td>{{ $mod->name }}</td>
                                <td class="text-center">
                                    @if(! $mod->installed)
                                        <span class="label bg-maroon">Not Installed</span>
                                    @else
                                        <span class="label label-success">Installed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(! $mod->steam_id)
                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#installModal" data-mod-name="{{$mod->name}}" data-mod-id="{{$mod->id}}"><i class="fa fa-download"></i></a>
                                    @else
                                    <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#installSteamModal" data-mod-game="{{$mod->steam_id}}" data-mod-id="{{$mod->id}}"><i class="fa fa-download"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- @if ($mods->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $mods->appends(['query' => Request::input('query')])->render() !!}</div>
                </div>
            @endif --}}
        </div>
    </div>
</div>






<!-- Modal -->
<div class="modal fade" id="installModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('server.mods.new', $server_id) }}" method="POST" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Install Mod: <span id="modName"></span></h4>

                    <input type="text" name="modId">

                </div>
                <div class="modal-body">
                    <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-default" data-dismiss="modal">Install</button>
                </div>
            </form>

        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="installSteamModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('server.mods.new',$server_id) }}" method="POST" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Install Steam Mod</h4>

                    <input type="text" name="modId">
                    <input type="text" name="workshopModName">
                    
                </div>
                <div class="modal-body">
                    <img id="modImg" src="" alt="" >
                    <p class="center" id="modName">No Mod found.</p>
                </div>
                <div class="modal-footer">
                    <input type="text" name="workshopModId">
                    {!! csrf_field() !!}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default">Install</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection


@section('footer-scripts')
    @parent

    {!! Theme::js('js/frontend/server.socket.js') !!}
    {!! Theme::js('vendor/async/async.min.js') !!}
    {!! Theme::js('vendor/lodash/lodash.js') !!}
    {!! Theme::js('vendor/siofu/client.min.js') !!}
    @if(App::environment('production'))
        {!! Theme::js('js/frontend/files/filemanager.min.js?updated-cancel-buttons') !!}
    @else
        {!! Theme::js('js/frontend/files/src/index.js') !!}
        {!! Theme::js('js/frontend/files/src/contextmenu.js') !!}
        {!! Theme::js('js/frontend/files/src/actions.js') !!}
    @endif
    {!! Theme::js('js/frontend/files/upload.js') !!}

    <script>
    $(document).ready(function () {
            //$('form').submit(false);


        $('#installModal').on('show.bs.modal', function(e) {

            // ModID
            var ModId = $(e.relatedTarget).data('mod-id');
            $(e.currentTarget).find('input[name="modId"]').val(ModId);
            // ModName
            var ModName = $(e.relatedTarget).data('mod-name');
            $(e.currentTarget).find('#modName').html(ModName);
        });


        $('#installSteamModal').on('show.bs.modal', function(e) {

            // ModID
            var ModId = $(e.relatedTarget).data('mod-id');
            $(e.currentTarget).find('input[name="modId"]').val(ModId);

            var ModGame = $(e.relatedTarget).data('mod-game');
            console.log(ModGame)
            
        });


        $('input[name="workshopModId"]').on('keyup', function(e) {
            if (e.keyCode == 13) {
                /*$.ajax({
                    type: 'POST',
                    url: 'https://api.steampowered.com/ISteamRemoteStorage/GetPublishedFileDetails/v1/?key=9C3517C68EB8713E1ED83B880DCD4AFF',
                    data: JSON.stringify({"itemcount":1, "publishedfileids[0]": $(e.currentTarget).find('input[name="workshopModId"]').val() }), // or JSON.stringify ({name: 'jonas'}),
                    success: function(data) { alert('data: ' + data); },
                    contentType: "application/json",
                    crossDomain: true,
                    dataType: 'json'
                });
                */
            }
        });


    });
    </script>

@endsection


