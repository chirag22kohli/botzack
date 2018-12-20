<div class="form-group{{ $errors->has('profile_id') ? 'has-error' : ''}}">

    {!! Form::hidden('profile_id', app('request')->input('profile'), ('' == 'required') ? ['class' => 'form-control', 'required' => 'required'] : ['class' => 'form-control']) !!}
    {!! $errors->first('profile_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('service_id') ? 'has-error' : ''}}">
    {!! Form::label('service_id', 'Services (You may select multiple services)', ['class' => 'control-label']) !!}
    <select name = "service_id[]" class= "form-control" id = "allservices" multiple="multiple">
        <?php foreach ($services as $service) { ?>

            <option value = "<?= $service->id ?>"><?= $service->name ?></option>
        <?php } ?>
            
            
        <?php foreach ($profileServices as $service) { ?>
            <option value = "<?= $service->serviceName->id ?>" selected><?= $service->serviceName->name ?></option>

        <?php }
        ?>

    </select>
</div>




<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

<script>
    $(document).ready(function () {
        $('#allservices').select2();
    });
</script>