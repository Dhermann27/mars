@extends('layouts.app')

@section('title')
    User Role Assignment
@endsection

@section('content')
    <div class="container">
        <form id="roles" class="form-horizontal" role="form" method="POST" action="{{ route('admin.roles.store') }}">
            @include('includes.flash')

            <ul id="nav-tab-roles" class="nav nav-tabs" role="tablist">
                @foreach($roles as $role)
                    <li class="nav-item{{ $loop->first ? ' pl-5' : '  ml-2' }}">
                        <a class="nav-link{{ $loop->first ? ' active' : '' }}" data-toggle="tab"
                           href="#tab-{{ $role['id'] }}" role="tab">
                            {{ $role['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div id="nav-tab-rolesContent" class="tab-content p-3">
                @foreach($roles as $role)
                    <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $role['id'] }}"
                         role="tabpanel">
                        <p>&nbsp;</p>
                        <h4>{{ $role['name'] }}</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Controls</th>
                                <th>Delete?</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users[$role['id']] as $user)
                                @if($user->camper)
                                    <tr>
                                        <td>{{ $user->camper->lastname }}, {{ $user->camper->firstname }}</td>
                                        <td>{{ $user->camper->email }}</td>
                                        <td>
                                            @include('components.admin.controls', ['id' => $user->id])
                                        </td>
                                        <td>
                                            @include('components.admin.delete', ['id' => $user->id])
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                @endforeach
            </div>

            <div class="form-group row @error('usertype') has-danger @enderror">
                <label for="usertype" class="col-md-4 col-form-label text-md-end">User Type</label>

                <div class="col-md-6">
                    <select id="usertype" name="usertype"
                            class="form-control @error('usertype') is-invalid @enderror">
                        @foreach($roles as $role)
                            <option value="{{ $role['id'] }}" @selected(old('usertype') == $role['id'])>
                                {{ $role['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('includes.formgroup', ['type' => 'select', 'class' => ' camperlist',
            'label' => 'Camper', 'attribs' => ['name' => 'camper_id'], 'list' => []])

            @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
        </form>
    </div>
@endsection
