<!-- resources/views/components/form-field.blade.php -->
@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => true, 'options' => []])

<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>
    <div class="col-md-6">
        @if(empty($options))
            <input id="{{ $name }}" type="{{ $type }}" class="form-control @error($name) is-invalid @enderror" name="{{ $name }}" value="{{ old($name, $value) }}" @if($required) required @endif>
        @else
            <select id="{{ $name }}" class="form-control @error($name) is-invalid @enderror" name="{{ $name }}" @if($required) required @endif>
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @if(old($name, $value) == $optionValue) selected @endif>{{ $optionLabel }}</option>
                @endforeach
            </select>
        @endif
        @error($name)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
