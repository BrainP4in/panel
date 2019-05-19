{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.master')

@section('title')
    @lang('server.mods.add.header')
@endsection

@section('content-header')
    <h1>Install<small>Install {{ $mod->name }} Mod on {{ $server->name }}.</small></h1>
    <ol class="breadcrumb">
    <li><a href="{{ route('index') }}">@lang('strings.home')</a></li>
    <li><a href="{{ route('server.index', $server->uuidShort) }}">{{ $server->name }}</a></li>
    <li><a href="{{ route('index') }}">Mods</a></li>
    <li class="active">{{ $mod->name }}</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('server.mods.new', $server->uuidShort) }}" method="POST">
    // user_viewable":0,"user_editable":0
    // User::USER_LEVEL_ADMIN
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Mod Configuration</h3>
                </div>


                <input type="hidden" id="modId" name="modId" class="form-control" value="{{$mod->id}}" />

                

                @foreach ($variables as $variable)


                    @if ($variable->input_type == "TEXT")          
                        <div class="form-group col-sm-6">
                                <label for="var_ref_{{$variable->id}}" class="control-label">@if (strpos($variable->rules, 'required') !== false) <span class="label label-danger">Required</span> @endif {{$variable->name}}</label>
                                <input type="text" id="var_ref_{{$variable->id}}" autocomplete="off" name="environment[{{$variable->env_variable}}]" class="form-control" value="{{$variable->default_value}}" />
                                <p class="text-muted small">{{$variable->description}}<br />
                                <strong>Access in Startup:</strong> <code>{{$variable->env_variable}}</code><br />
                                <strong>Validation Rules:</strong> <code>{{$variable->rules}}</code></small></p>
                        </div>
                    @elseif ($variable->input_type == "STEAMWORKSHOPID")

                        <div class="form-group col-sm-6">
                            <label for="var_ref_{{$variable->id}}" class="control-label"><span class="label label-danger">Required</span> Steam ID</label>
                            <div class="input-group">
                                <input id="steamModId" type="search" class="form-control" id="var_ref_{{$variable->id}}" name="environment[{{$variable->env_variable}}]" value="{{$variable->default_value}}">
                                <span class="input-group-btn">
                                    <button onclick="checkSteamModId()" class="btn btn-primary" type="button">
                                        <span class="fa fa-search" aria-hidden="true"></span> Check
                                    </button>
                                </span>
                            </div>

                            <div id="steamModPreview" class="info-box bg-blue hidden" style="margin-top: 10px;">
                                <span class="info-box-icon" style="width:160px;">
                                    <img id="steamModImg" src="" style="margin-top: -7px;border-top-left-radius: 2px;border-bottom-left-radius: 2px;"/>
                                </span>
                                <div class="info-box-content number-info-box-content" style="margin-left: 160px;">
                                    <span id="steamModTitle" class="info-box-number"></span>
                                </div>
                            </div>
                        </div>





                    @elseif ($variable->input_type == "HIDDEN")
                        <input type="hidden" id="var_ref_{{$variable->id}}" autocomplete="off" name="environment[{{$variable->env_variable}}]" class="form-control" value="{{$variable->default_value}}" />
                    @else
                        <div class="form-group col-sm-6">
                                <label for="var_ref_{{$variable->id}}" class="control-label"> <span class="label label-danger">UNDEFINED INPUT TYPE</span> @if (strpos($variable->rules, 'required') !== false) <span class="label label-danger">Required</span> @endif {{$variable->name}}</label>
                                <input type="text" id="var_ref_{{$variable->id}}" autocomplete="off" name="environment[{{$variable->env_variable}}]" class="form-control" value="{{$variable->default_value}}" />
                                <p class="text-muted small">{{$variable->description}}<br />
                                <strong>Access in Startup:</strong> <code>{{$variable->env_variable}}</code><br />
                                <strong>Validation Rules:</strong> <code>{{$variable->rules}}</code></small></p>
                        </div>

                    @endif




                @endforeach


                <div class="box-footer">
                    {!! csrf_field() !!}
                    <input type="submit" class="btn btn-success pull-right" value="Install Mod" />
                </div>
            </div>
        </div>
    </div>
</form>



@endsection


@section('footer-scripts')
    @parent

    {!! Theme::js('js/frontend/server.socket.js') !!}

    <script>
        function checkSteamModId(event) {
            var formData = new FormData();
            formData.append('itemcount', 1);
            formData.append('publishedfileids[0]', $('#steamModId').val());

            $.ajax({
                type: "GET",
                url: "steamWorkshop/" + $('#steamModId').val(),
                cache: false,
                processData: false,
                success: function(data){
                    data = JSON.parse(data);
                    var mod = data.response.publishedfiledetails[0];

                    $('#steamModImg').attr('src',mod.preview_url);
                    $("#steamModTitle").html(mod.title);
                    //$("#steamModDescription").html(mod.description);
                    $("#steamModPreview").removeClass("hidden");
                    $('input[name*=NAME]').val(mod.title.replace(" ", "_"));
                }
            });
        }

    </script>
@endsection