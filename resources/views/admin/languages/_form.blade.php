                    <div class="row">
                        <div class="col-md-4">
                            @include('helpers.form_control', [
                                'type' => 'text',
                                'class' => '',
                                'name' => 'name',
                                'value' => $language->name,
                                'help_class' => 'language',
                                'rules' => $language->rules()
                            ])
                        </div>
                        <div class="col-md-4">
                            @include('helpers.form_control', [
                                'type' => 'select',
                                'class' => '',
                                'name' => 'code',
                                'value' => $language->code,
                                'options' => \Acelle\Model\Language::languageCodes(),
                                'help_class' => 'language',
                                'rules' => $language->rules()
                            ])
                        </div>
                    </div>

                    <hr >
                    <div class="text-left">
                        <button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
                        <a href="{{ action('Admin\LanguageController@index') }}" type="button" class="btn bg-grey">
                            <i class="icon-cross2"></i> {{ trans('messages.cancel') }}
                        </a>
                    </div>
