
<div>
    @foreach ($values as $key => $value)
        <label>
            {{ Form::radio($name, $key) }}
            <span>{{ $value }}</span>
        </label>
    @endforeach
</div>
