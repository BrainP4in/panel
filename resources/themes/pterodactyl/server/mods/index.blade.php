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
                            <th class="text-center">Status</th>
                            <th class="text-center">                                
                                <a class="btn btn-xs btn-default" href="#"><i class="fa fa-refresh"></i></a>
                                <a class="btn btn-xs btn-default" href="#"><i class="fa fa-download"></i></a>
                            </th>
                        </tr>
                        @foreach ($installed as $mod)
                             <tr>
                                <td>
                                    @if( $hasVariable($mod, 'steamModId') )
                                        <i class="fa fa-steam"></i>
                                    @endif

                                    {{ $getCustomName($mod) }}
                                </td>
                                <td class="text-center">
                                    @if(! $mod->mod)
                                        <span class="label bg-maroon">Outdated</span>
                                    @else
                                        <span class="label label-success">Installed</span>
                                    @endif
                                </td>
                                <td class="text-center">                            
                                    <a href="#/delete/{{ $mod->id }}" data-action="delete" data-id="{{ $mod->id }}">
                                        <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                    </a>
                                    <a href="#/update/{{ $mod->id }}" data-action="update" data-id="{{ $mod->id }}">
                                        <button class="btn btn-xs btn-info"><i class="fa fa-refresh"></i></button>
                                    </a>
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
                            <th class="text-center">Status</th>
                            <th class="text-center"></th>
                        </tr>
                        @foreach ($mods as $mod)
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
                                <a class="btn btn-xs btn-default" href="{{ route('server.mods.install', [ 'server' => $server->uuidShort, 'mods' => $mod->id] ) }}"><i class="fa fa-download"></i></a> {{--  data-mod-game="{{$mod->steam_id}}" data-mod-id="{{$mod->id}}" --}}
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




@endsection


@section('footer-scripts')
    @parent

    {!! Theme::js('js/frontend/server.socket.js') !!}

    <script>
    $(document).ready(function () {
        $('[data-action="delete"]').click(function (event) {
            event.preventDefault();
            var self = $(this);
            swal({
                type: 'warning',
                title: 'Delete Mod',
                text: 'This will immediately remove this mod from this server.',
                showCancelButton: true,
                showConfirmButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function () {
                $.ajax({
                    method: 'DELETE',
                    url: "mods/" + self.data('id'),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                    }
                }).done(function () {
                    self.parent().parent().slideUp();

                    swal({
                        type: 'success',
                        title: '',
                        text: 'Mod was successfully deleted.'
                    });
                }).fail(function (jqXHR) {
                    console.error(jqXHR);
                    var error = 'An error occurred while trying to process this request.';
                    if (typeof jqXHR.responseJSON !== 'undefined' && typeof jqXHR.responseJSON.error !== 'undefined') {
                        error = jqXHR.responseJSON.error;
                    }
                    swal({
                        type: 'error',
                        title: 'Whoops!',
                        text: error
                    });
                });
            });
        });
    });
    </script>
@endsection