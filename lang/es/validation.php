<?php
return [
    'accepted' => 'El campo :attribute debe ser aceptado.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'alpha' => 'El campo :attribute sólo puede contener letras.',
    'alpha_dash' => 'El campo :attribute sólo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute sólo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'exists' => 'El campo :attribute seleccionado es inválido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado es inválido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor a :max.',
        'file' => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo con formato: :values.',
    'min' => [
        'numeric' => 'El tamaño de :attribute debe ser de al menos :min.',
        'file' => 'El tamaño de :attribute debe ser de al menos :min kilobytes.',
        'string' => 'El campo :attribute debe contener al menos :min caracteres.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'not_in' => 'El campo :attribute seleccionado es inválido.',
    'numeric' => 'El campo :attribute debe ser numérico.',
    'password' => 'La contraseña es incorrecta.',
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser una cadena de caracteres.',
    'unique' => 'El campo :attribute ya ha sido registrado.',
    'url' => 'El formato de :attribute es inválido.',
    
    'custom' => [
        'password' => [
            'required' => 'La contraseña es obligatoria.',
            'min' => 'La contraseña debe tener al menos 6 caracteres.',
            'confirmed' => 'Las contraseñas no coinciden.',
        ],
        'email' => [
            'required' => 'El correo es obligatorio.',
            'email' => 'El correo debe ser válido.',
        ]
    ],
    
    'attributes' => [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'title' => 'título',
        'body' => 'descripción',
        'scheduled_start_date' => 'fecha de salida',
        'scheduled_end_date' => 'fecha de regreso',
    ],
];
