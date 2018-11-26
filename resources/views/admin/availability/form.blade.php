<div class="form-group{{ $errors->has('profile_id') ? 'has-error' : ''}}">
    {!! Form::label('profile_id', 'Profile Id', ['class' => 'control-label']) !!}
    {!! Form::number('profile_id', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('profile_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('start_date') ? 'has-error' : ''}}">
    {!! Form::label('start_date', 'Start Date', ['class' => 'control-label']) !!}
    {!! Form::date('start_date', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('start_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('end_date') ? 'has-error' : ''}}">
    {!! Form::label('end_date', 'End Date', ['class' => 'control-label']) !!}
    {!! Form::date('end_date', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('end_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('start_time') ? 'has-error' : ''}}">
    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label']) !!}
    {!! Form::input('time', 'start_time', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('start_time', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('end_time') ? 'has-error' : ''}}">
    {!! Form::label('end_time', 'End Time', ['class' => 'control-label']) !!}
    {!! Form::input('time', 'end_time', null, ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('end_time', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
