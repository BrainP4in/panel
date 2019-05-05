{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    List Mods
@endsection

@section('content-header')
    <h1>Mods<small>All Mods available on the system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Mods</li>
    </ol>
@endsection

@section('content')
                        {{$mods}} 


<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Mods List</h3>
                <div class="box-tools">
                    <form action="{{ route('admin.mods') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input type="text" name="query" class="form-control pull-right" style="width:30%;" value="{{ request()->input('query') }}" placeholder="Search Mods">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.mods.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Egg</th>
                            <th>Name</th>
                            <th>Include Nest</th>
                        </tr>
                        @foreach ($mods as $mod)

                        {{$mod}} 
                             <tr>
                                <td class="middle" data-toggle="tooltip" data-placement="right" title="{{ $mod->id }}"><code>{{ $mod->id }}</code></td>
                                <td class="middle"><a href="{{ route('admin.mods.view', $mod->id) }}"> $mod->egg_id->name </a></td>
                                <td class="middle"><code>{{ $mod->name }}</code></td>
                                <td class="middle"><code>{{ $mod->comprehensive }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($mods->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $mods->appends(['query' => Request::input('query')])->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
