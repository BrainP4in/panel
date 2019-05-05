{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Mods &rarr; View &rarr; {{ $mod->name }}
@endsection

@section('content-header')
    <h1>{{ $mod->name }}<small>GAME</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.mods') }}">Mods</a></li>
        <li class="active">{{ $mod->name }}</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('admin.mods.view', $mod->id) }}" method="POST">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Mod Details</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="pName" class="form-label">Name</label>
                        <input name="name" type="text" id="pName" class="form-control" value="{{ $mod->name }}" />
                        <p class="text-muted small">A short but descriptive name of what this mod is. For example, <code>SouceMod</code> which is a Counter Strike Mod.</p>
                    </div>
                    <div class="form-group">
                        <label for="pEggId" class="form-label">Associated Egg</label>
                        <select id="pEggId" name="egg_id" class="form-control">
                            @foreach($nests as $nest)
                                <optgroup label="{{ $nest->name }}">
                                    @foreach($nest->eggs as $egg)
                                        <option value="{{ $egg->id }}" {{ $mod->egg_id !== $egg->id ?: 'selected' }}>{{ $egg->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-muted small">The option that this Mod is associated with. Only servers that are assigned this option will be able to access this pack.</p>
                    </div> 
                    <div class="form-group">
                        <div class="checkbox checkbox-primary no-margin-bottom">
                            <input id="pSelectable" name="comprehensive" type="checkbox" value="1" {{ ! $mod->comprehensive ?: 'checked' }}/>
                            <label for="pSelectable">
                                Comprehensive
                            </label>
                        </div>
                        <p class="text-muted small">Check this box if the Mod is installable on the complete Nest and not only on the selected Egg.</p>
                    </div>
                    <div class="form-group">
                        <label for="pSteamID" class="form-label">SteamID</label>
                        <input name="steam_id" type="text" id="pSteamID" class="form-control" value="{{ $mod->steam_id }}" />
                        <p class="text-muted small"><code>WIP</code> .</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Pack Configuration</h3>
                </div>
                <div class="box-body">
                    <div class="form-group no-margin-bottom">

                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Install Script</h3>
                            </div>

                            <div class="box-body no-padding">
                                <div id="install_script"style="height:300px">{{ $mod->install_script }}</div>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Script Container</label>
                                        <input type="text" name="install_script_container" class="form-control" value="{{ $mod->install_script_container }}" />
                                        <p class="text-muted small">Docker container to use when running this script for the server. If empty, the server uses it's default container.</p>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Script Entrypoint Command</label>
                                        <input type="text" name="install_script_entry" class="form-control" value="{{ $mod->install_script_entry }}" />
                                        <p class="text-muted small">The entrypoint command to use for this script.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                {!! csrf_field() !!}
                                <textarea name="install_script" class="hidden"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer with-border">
                    {!! csrf_field() !!}
                    <button name="_method" value="PATCH" class="btn btn-sm btn-primary pull-right" type="submit">Save</button>
                    <button name="_method" value="DELETE" class="btn btn-sm btn-danger pull-left muted muted-hover" type="submit"><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Servers Using This Mod</h3>
            </div>
            <div class="box-body no-padding table-responsive">
                <table class="table table-hover">
                    <tr>
                        <th>ID</th>
                        <th>Server Name</th>
                        <th>Node</th>
                        <th>Owner</th>
                    </tr>
                    foreach($mod->servers as $server)
                        <tr>
                            <td><code> $server->uuidShort </code></td>
                            <td><a href=" route('admin.servers.view', $server->id) "> $server->name </a></td>
                            <td><a href=" route('admin.nodes.view', $server->node->id) "> $server->node->name </a></td>
                            <td><a href=" route('admin.users.view', $server->user->id) "> $server->user->email </a></td>
                        </tr>
                    endforeach
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-6 col-md-5 col-md-offset-7 col-xs-offset-6">
        <form action="{{ route('admin.mods.view.export', $mod->id) }}" method="POST">
            {!! csrf_field() !!}
            <button type="submit" class="btn btn-sm btn-success pull-right">Export</button>
        </form>
        <form action="{{ route('admin.mods.view.export', ['id' => $mod->id, 'files' => 'with-files']) }}" method="POST">
            {!! csrf_field() !!}
            <button type="submit" class="btn btn-sm pull-right muted muted-hover" style="margin-right:10px;">Export with Archive</button>
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('vendor/ace/ace.js') !!}
    {!! Theme::js('vendor/ace/ext-modelist.js') !!}
    <script>
    $(document).ready(function () {

        const InstallEditor = ace.edit('install_script');
        const Modelist = ace.require('ace/ext/modelist')

        InstallEditor.setTheme('ace/theme/chrome');
        InstallEditor.getSession().setMode('ace/mode/sh');
        InstallEditor.getSession().setUseWrapMode(true);
        InstallEditor.setShowPrintMargin(false);


        $('form').on('submit', function (e) {
            $('textarea[name="install_script"]').val(InstallEditor.getValue());
        });
    });
    </script>

@endsection

