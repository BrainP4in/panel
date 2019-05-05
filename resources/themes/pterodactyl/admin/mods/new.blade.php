{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Packs &rarr; New
@endsection

@section('content-header')
    <h1>New Pack<small>Create a new pack on the system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.mods') }}">Mods</a></li>
        <li class="active">New</li>
    </ol>
@endsection

@section('content')

<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom nav-tabs-floating">
            <ul class="nav nav-tabs">
                <li id="nav_manual" class="active"><a href="#manual" aria-controls="manual" role="tab" data-toggle="tab">Manual Mod</a></li>
                <li><a href="#steam" aria-controls="steam" role="tab" data-toggle="tab">Steam Workshop</a></li>
            </ul>
        </div>
    </div>
</div>


<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="manual">
        <form action="{{ route('admin.mods.new') }}" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Mod Details</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="pName" class="form-label">Name</label>
                                <input name="name" type="text" id="pName" class="form-control" value="{{ old('name') }}" />
                                <p class="text-muted small">A short but descriptive name of what this mod is. For example, <code>SouceMod</code> which is a Counter Strike Mod.</p>
                            </div>
                            <div class="form-group">
                                <label for="pEggId" class="form-label">Associated Egg</label>
                                <select id="pEggId" name="egg_id" class="form-control">
                                    @foreach($nests as $nest)
                                        <optgroup label="{{ $nest->name }}">
                                            @foreach($nest->eggs as $egg)
                                                <option value="{{ $egg->id }}">{{ $egg->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <p class="text-muted small">The option that this Mod is associated with. Only servers that are assigned this option will be able to access this pack.</p>
                            </div>
                            <div class="form-group">
                                <div class="checkbox checkbox-primary no-margin-bottom">
                                    <input id="pSelectable" name="comprehensive" type="checkbox" value="1" checked/>
                                    <label for="pSelectable">
                                        Comprehensive
                                    </label>
                                </div>
                                <p class="text-muted small">Check this box if the Mod is installable on the complete Nest and not only on the selected Egg.</p>
                            </div>
                            <div class="form-group hidden">
                                <input name="steam_id" type="text" id="pSteamID" class="form-control" value="{{ old('steam_id') }}" />
                                <input name="steam_username" type="text" id="pSteamUsername" class="form-control" value="{{ old('steam_username') }}" />
                                <input name="steam_password" type="text" id="pSteamPassword" class="form-control" value="{{ old('steam_password') }}" />


                                <label for="pSteamID" class="form-label">SteamID</label>
                                <input name="steam_id" type="text" id="pSteamID" class="form-control" value="{{ old('steam_id') }}" />
                                <p class="text-muted small"><code>WIP</code> .</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Mod Configuration</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group no-margin-bottom">

                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Install Script</h3>
                                    </div>
 
                                    <div class="box-body no-padding">
                                        <div id="install_script"style="height:300px"></div>
                                    </div>

                                    <div class="box-body">
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label">Script Container</label>
                                                <input type="text" name="install_script_container" class="form-control" value="" />
                                                <p class="text-muted small">Docker container to use when running this script for the server. If empty, the server uses it's default container.</p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="control-label">Script Entrypoint Command</label>
                                                <input type="text" name="install_script_entry" class="form-control" value="" />
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
                            <button class="btn btn-sm btn-success pull-right" type="submit">Create Mod</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    
    <div role="tabpanel" class="tab-pane" id="steam">
        <form action="{{ route('admin.mods.new') }}" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Mod Details</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="pName" class="form-label">Name</label>
                                <input name="name" type="text" id="pName" class="form-control" value="{{ old('name') }}" />
                                <p class="text-muted small">A short but descriptive name of what this mod is. For example, <code>SouceMod</code> which is a Counter Strike Mod.</p>
                            </div>
                            <div class="form-group">
                                <label for="pEggId" class="form-label">Associated Egg</label>
                                <select id="pEggId" name="egg_id" class="form-control">
                                    @foreach($nests as $nest)
                                        <optgroup label="{{ $nest->name }}">
                                            @foreach($nest->eggs as $egg)
                                                <option value="{{ $egg->id }}">{{ $egg->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <p class="text-muted small">The option that this Mod is associated with. Only servers that are assigned this option will be able to access this pack.</p>
                            </div>
                            <div class="form-group">
                                <div class="checkbox checkbox-primary no-margin-bottom">
                                    <input id="pSelectable" name="comprehensive" type="checkbox" value="1" checked/>
                                    <label for="pSelectable">
                                        Comprehensive
                                    </label>
                                </div>
                                <p class="text-muted small">Check this box if the Mod is installable on the complete Nest and not only on the selected Egg.</p>
                            </div>
                            <div class="form-group">
                                <label for="pSteamID" class="form-label">SteamID</label>
                                <input name="steam_id" type="text" id="pSteamID" class="form-control" value="{{ old('steam_id') }}" />
                                <p class="text-muted small">Steam ID of the Game you want to add mods for, <code>WIP</code> for CSGO.</p>
                            </div>

                            <div class="form-group">
                                <label for="pSteamID" class="form-label">Steam Username</label>
                                <input name="steam_username" type="text" id="pSteamUsername" class="form-control" value="{{ old('steam_username') }}" />
                                <p class="text-muted small">Username of the Steam Account to install workshop mods.</p>
                            </div>

                            <div class="form-group">
                                <label for="pSteamID" class="form-label">Steam Password</label>
                                <input name="steam_password" type="text" id="pSteamPassword" class="form-control" value="{{ old('steam_password') }}" />
                                <p class="text-muted small">Password of the Steam Account with Steam Guard disabled.</p>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Mod Configuration</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group no-margin-bottom">

                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Install Script</h3>
                                    </div>
 
                                    <div class="box-body no-padding">
                                        <div id="install_script"style="height:300px"></div>
                                    </div>

                                    <div class="box-body">
                                        <div class="row">
                                            <div class="form-group col-sm-6">
                                                <label class="control-label">Script Container</label>
                                                <input type="text" name="install_script_container" class="form-control" value="" />
                                                <p class="text-muted small">Docker container to use when running this script for the server. If empty, the server uses it's default container.</p>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="control-label">Script Entrypoint Command</label>
                                                <input type="text" name="install_script_entry" class="form-control" value="" />
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
                            <button class="btn btn-sm btn-success pull-right" type="submit">Create Mod</button>
                        </div>
                    </div>
                </div>
            </div>
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
            //console.log(e);
            $('textarea[name="install_script"]').val(InstallEditor.getValue());
        });
    });
    </script>

@endsection
