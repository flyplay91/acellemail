<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'O :attribute deve ser aceite.',
    'active_url' => 'O :attribute não é um URL correcto.',
    'after' => 'O :attribute deve ser datado depois de :date.',
    'alpha' => 'O :attribute deve só conter letras.',
    'alpha_dash' => 'O :attribute deve só conter letras, números e traços.',
    'alpha_num' => 'O :attribute deve só conter letras e números.',
    'array' => 'O :attribute deve ser uma matriz.',
    'before' => 'O :attribute deve ser uma data anterior a :date.',
    'between' => [
        'numeric' => 'O :attribute deve ser entre :min e :max.',
        'file' => 'O :attribute deve ser entre :min e :max kilobytes.',
        'string' => 'O :attribute deve ser entre :min e :max caracteres.',
        'array' => 'O :attribute deve ser entre :min e :max unidades.',
    ],
    'boolean' => 'O :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'O :attribute confirmação não corresponde .',
    'date' => 'O :attribute não tem uma data válida.',
    'date_format' => 'O :attribute não corresponde ao formato :format.',
    'different' => 'O :attribute e :other devem ser diferentes.',
    'digits' => 'O :attribute deve ter :digits digitos.',
    'digits_between' => 'O :attribute deve ter entre :min e :max digitos.',
    'distinct' => 'O :attribute tem valores duplicados.',
    'email' => 'Endereço de Email inválido.',
    'exists' => 'A seleção :attribute é inválida.',
    'filled' => 'O :attribute é de preenchimento obrigatório.',
    'image' => 'O :attribute deve ser uma imagem.',
    'in' => 'A seleção :attribute é inválida.',
    'in_array' => 'O :attribute não existe em :other.',
    'integer' => 'O :attribute deve ser um número inteiro.',
    'ip' => 'O :attribute deve ser um endereço de IP válido.',
    'json' => 'O :attribute deve ter JSON string válido.',
    'max' => [
        'numeric' => 'O :attribute pode não ser maior que :max.',
        'file' => 'O :attribute pode não ser maior que :max kilobytes.',
        'string' => 'O :attribute pode não ser maior que :max caracteres.',
        'array' => 'O :attribute pode não ser maior que :max unidades.',
    ],
    'mimes' => 'O :attribute deve ser um ficheiro do tipo: :values.',
    'min' => [
        'numeric' => 'O :attribute deve ser pelo menos :min.',
        'file' => 'O :attribute deve ser pelo menos :min kilobytes.',
        'string' => 'O :attribute deve ser pelo menos :min caracteres.',
        'array' => 'O :attribute deve ser pelo menos :min unidades.',
    ],
    'not_in' => 'A seleção :attribute é inválida.',
    'numeric' => 'O :attribute deve ser um número.',
    'present' => 'O :attribute deve estar presente.',
    'regex' => 'O formato :attribute é inválido.',
    'required' => 'O campo :attribute é de preenchimento obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_unless' => 'O campo :attribute é de preenchimento obrigatório a não ser que :other é entre :values.',
    'required_with' => 'O campo :attribute é de preenchimento obrigatório quando :values é preenchido.',
    'required_with_all' => 'O campo :attribute é de preenchimento obrigatório quando :values é preenchido.',
    'required_without' => 'O campo :attribute é de preenchimento obrigatório quando :values não é preenchido.',
    'required_without_all' => 'O campo :attribute é de preenchimento obrigatório quando nenhum dos :values está preenchido.',
    'same' => 'O campo :attribute e :other deve ser igual.',
    'size' => [
        'numeric' => 'O campo :attribute deve ter :size.',
        'file' => 'O campo :attribute deve ter :size kilobytes.',
        'string' => 'O campo :attribute deve ter :size characters.',
        'array' => 'O campo :attribute deve conter :size unidades.',
    ],
    'string' => 'O campo :attribute deve ser só letras.',
    'timezone' => 'O campo :attribute deve ser uma zona válida.',
    'unique' => 'Este nome já foi ocupado.',
    'url' => 'O formato :attribute é invalido.',
    'substring' => 'A tag :tag não foi encontrada em :attribute.',
    'license' => 'A licença não é válida.',
    'license_error' => ':error',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'miss_main_field_tag' => [
            'required' => 'Falta o preenchimento do campo de Email',
        ],
        'conflict_field_tags' => [
            'required' => 'As Tags não podem ser repetidas',
        ],
        'segment_conditions_empty' => [
            'required' => 'A lista de condições é de preenchimento obrigatório',
        ],
        'mysql_connection' => [
            'required' => 'Falha na conexão ao servidor MySQL',
        ],
        'database_not_empty' => [
            'required' => 'A base dados não está vazia',
        ],
        'promo_code_not_valid' => [
            'required' => 'Códido de promoção invalido',
        ],
        'smtp_valid' => [
            'required' => 'Falha na conexão de servidor SMTP',
        ],
        'yaml_parse_error' => [
            'required' => 'Não é possível analisar. Verifique a sintaxe',
        ],
        'file_not_found' => [
            'required' => 'Ficheiro não encontrado.',
        ],
        'not_zip_archive' => [
            'required' => 'Não é um ficheiro ZIP.',
        ],
        'zip_archive_unvalid' => [
            'required' => 'Erro de leitura.',
        ],
        'custom_criteria_empty' => [
            'required' => 'Os critérios personalizados não podem estar vazios',
        ],
        'php_bin_path_invalid' => [
            'required' => 'Executavel PHP inválido. Verifique novamente.',
        ],
        'can_not_empty_database' => [
            'required' => 'Não é possível DROP em certas tabelas, limpe a sua base de dados manualmente e tente novamente',
        ],
        'recaptcha_invalid' => [
            'required' => 'reCAPTCHA inválido.',
        ],
        'payment_method_not_valid' => [
            'required' => 'Algo deu errado na configuração no método de pagamento. Por favor verifique novamente.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'options' => [
            'limit_value' => 'valor limite',
            'limit_base' => 'Limite base',
            'limit_unit' => 'Limite tempo',
        ],
        'quota_value' => 'Limite de envio',
        'quota_base' => 'Tempo base',
        'quota_unit' => 'Unidade tempo',
        'lists_segments' => [
            '0' => [
                'mail_list_uid' => 'Lista'
            ],
            '1' => [
                'mail_list_uid' => 'Lista'
            ],
            '2' => [
                'mail_list_uid' => 'Lista'
            ],
            '3' => [
                'mail_list_uid' => 'Lista'
            ],
            '4' => [
                'mail_list_uid' => 'Lista'
            ],
            '5' => [
                'mail_list_uid' => 'Lista'
            ],
            '6' => [
                'mail_list_uid' => 'Lista'
            ],
            '7' => [
                'mail_list_uid' => 'Lista'
            ],
            '8' => [
                'mail_list_uid' => 'Lista'
            ],
            '9' => [
                'mail_list_uid' => 'Lista'
            ],
        ],
    ],

];
