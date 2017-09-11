@if (strpos($operator, 'verification') !== false)
    <div class="row">
        <div class="col-md-6 operator-col">
            @include('helpers.form_control', [
                'type' => 'select',
                'name' => 'conditions['.$index.'][operator]',
                'label' => '',
                'value' => (isset($operator) ? $operator : ''),
                'options' => Acelle\Model\Segment::verificationOperators()
            ])
        </div>
        <div class="col-md-6 value-col">
            @include('helpers.form_control', [
                'type' => 'select',
                'name' => 'conditions['.$index.'][value]',
                'label' => '',
                'value' => (isset($value) ? $value : ''),
                'options' => Acelle\Model\EmailVerification::resultSelectOptions()
            ])
        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-6 operator-col">
            @include('helpers.form_control', [
                'type' => 'select',
                'name' => 'conditions['.$index.'][operator]',
                'label' => '',
                'value' => (isset($operator) ? $operator : ''),
                'options' => Acelle\Model\Segment::operators()
            ])
        </div>
        <div class="col-md-6 value-col">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'conditions['.$index.'][value]',
                'label' => '',
                'value' => (isset($value) ? $value : '')
            ])
        </div>
    </div>
@endif
