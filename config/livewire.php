<?php
return [
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'components.layouts.app',
    'lazy_placeholder' => null,
    'temporary_file_upload' => [
        'disk' => 'public',
        'rules' => ['required', 'file', 'max:10240'],
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => ['png', 'gif', 'bmp', 'svg', 'jpg', 'jpeg', 'mpga', 'webp'],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],
    'render_on_redirect' => false,
    'legacy_model_binding' => false,
    'inject_assets' => true,
    'navigate' => ['show_progress_bar' => true, 'progress_bar_color' => '#2299dd'],
    'inject_morph_markers' => true,
    'pagination_theme' => 'tailwind',
];
