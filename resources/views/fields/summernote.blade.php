<!-- summernote editor -->
@php
    // make sure that the options array is defined
    // and at the very least, dialogsInBody is true;
    // that's needed for modals to show above the overlay in Bootstrap 4
    $field['options'] = array_merge([
                    'toolbar' => [
                        // [groupName, [list of button]]
                        ['style', ['style']],
                        ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['audio', 'link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ],
                    'codemirror' => [
                        'theme' => 'monokai',
                        'indentWithTabs' => true,
                        'indentUnit' => 1,
                        'lineNumbers' => true
                    ]
                ],  $field['options'] ?? []);

    $field['options'] = array_merge(['dialogsInBody' => true, 'tooltip' => false], $field['options'] ?? []);
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')
<textarea
    name="{{ $field['name'] }}"
    data-init-function="bpFieldInitSummernoteElement"
    data-options="{{ json_encode($field['options']) }}"
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control summernote'])
        >{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}</textarea>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- include summernote css-->
        <link href="{{ asset('packages/summernote/dist/summernote-bs4.css') }}" rel="stylesheet" type="text/css"/>
        <style type="text/css">
            .note-editor.note-frame .note-status-output, .note-editor.note-airframe .note-status-output {
                height: auto;
            }
        </style>
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- include codemirror (codemirror.css, codemirror.js, xml.js, formatting.js) -->
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css">
        <link rel="stylesheet" type="text/css"
              href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.css">
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
        <script type="text/javascript"
                src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.js"></script>
        <!-- include summernote js-->
        {{-- <script src="{{ asset('packages/summernote/dist/summernote.min.js') }}"></script> --}}
        <script src="{{ asset('packages/summernote/dist/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('js/summernote-video-attributes.js') }}"></script>
        <script>
            function bpFieldInitSummernoteElement(element) {
                element.summernote(element.data('options'));
            }
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
