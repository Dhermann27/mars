@extends('layouts.app')

@section('title')
    Distribution Lists
@endsection

@section('content')
    <div class="container">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/distlist') }}">
            @include('includes.flash')

            @include('includes.formgroup', ['type' => 'select',
                'label' => 'List Grouping', 'attribs' => ['name' => 'groupby'],
                'list' => [['id' => 'id', 'name' => 'Campers (Unique emails, duplicate snailmail addresses)'],
                            ['id' => 'family_id', 'name' => 'Families (Unique snailmail addresses, one member\'s name and email)']], 'option' => 'name'])


            @include('includes.formgroup', ['type' => 'select',
                'label' => 'Base Camper List', 'attribs' => ['name' => 'campers'],
                'list' => [['id' => 'all', 'name' => 'All campers'],
                            ['id' => 'reg', 'name' => 'All registered campers'],
                            ['id' => 'unp', 'name' => 'All registered campers with unpaid deposits'],
                            ['id' => 'uns', 'name' => 'All registered families with children missing medical responses'],
                            ['id' => 'oneyear', 'name' => 'All campers from last year'],
                            ['id' => 'lost', 'name' => 'All campers from last year who have not registered for this year'],
                            ['id' => 'loster', 'name' => 'All campers from last 3 years who have not registered for this year'],
                            ['id' => 'threeyears', 'name' => 'All campers from the last 3 years']], 'option' => 'name'])

            @include('includes.formgroup', ['type' => 'select',
                'label' => 'Restrict to campers with email addresses?', 'attribs' => ['name' => 'email'],
                'list' => [['id' => '1', 'name' => 'Show campers with email addresses only'],
                            ['id' => '0', 'name' => 'Show all campers']], 'option' => 'name'])

            @include('includes.formgroup', ['type' => 'select',
                'label' => 'Restrict to campers that want snail mail?', 'attribs' => ['name' => 'ecomm'],
                'list' => [['id' => '-1', 'name' => 'Show all campers'],
                            ['id' => '1', 'name' => 'Show campers that do not want snail mail'],
                            ['id' => '0', 'name' => 'Show campers that want to receive snail mail']],
                             'option' => 'name'])


            @include('includes.formgroup', ['type' => 'select',
                'label' => 'Restrict to Campers with current snailmail addresses?', 'attribs' => ['name' => 'current'],
                'list' => [['id' => '1', 'name' => 'Yes'],
                            ['id' => '0', 'name' => 'No, even show obselete addresses']], 'option' => 'name'])

            <div class="form-group row">
                <label for="programs" class="col-md-4 control-label">Programs (all off by default)</label>
                <div class="col-md-6 btn-group" data-toggle="buttons">
                    @foreach($programs as $program)
                        <label class="btn btn-default {{ old('program-' . $program->id, $request->input('program-' . $program->id)) == 'on' ? 'active' : '' }}">
                            <input type="checkbox" name="program-{{ $program->id }}" data-toggle="switch"
                                {{ old('program-' . $program->id, $request->input('program-' . $program->id)) == 'on' ? ' checked="checked"' : '' }}/>
                            {{ $program->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            @include('includes.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Download Data']])
        </form>
    </div>
@endsection
