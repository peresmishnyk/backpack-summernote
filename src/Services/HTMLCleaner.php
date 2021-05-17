<?php


namespace Peresmishnyk\BackpackSummernote\Services;


use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class HTMLCleaner
{
    public $buffer;
    public $parsed_rules;
    public $level;
    public $config;

    const DEFAULT_RULES = 'video,source,strong,b,u,i,br,p[class],span[class|style],a[href|target],h1,h2,h3,h4,h5,h6,img[src|style|width|height|data-filename],hr,code,blockquote,ul,ol,li,iframe,font[color],table,tr,td,th,thead,colgroup,col,tfoot,tbody,strike,sup,sub';

    public function __construct($config){
        $this->config = $config;
    }

    public function clear($content, $rules=null)

    {
        $this->level = 0;
        $this->buffer = '';
        $this->parsed_rules = collect(explode(',', $rules ?? $this->config['default_rules']))
            ->map(function ($el) {
                $el = trim($el);
                preg_match_all('/^(?<tag>\w+)(\[(?<attrs>[^\]]+))?/u', $el, $parts);
                if ($parts['attrs'][0] != "") {
                    $attrs = collect(explode('|', $parts['attrs'][0]))->map('trim')->toArray();
                } else {
                    $attrs = true;
                }
                return [
                    'tag' => $parts['tag'][0],
                    'attrs' => $attrs
                ];
            })->pluck('attrs', 'tag');


        // Creates a new XML parser and returns a resource handle referencing it to be used by the other XML functions.
        $parser = xml_parser_create();

        xml_set_element_handler($parser, [$this, "startElements"], [$this, "endElements"]);
        xml_set_character_data_handler($parser, [$this, "characterData"]);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, true);

        // All options : http://tidy.sourceforge.net/docs/quickref.html
        $options = [
            'output-xhtml' => true,
            'clean' => false,
            'show-body-only' => true,
        ];

        //$content = html_entity_decode($content);

        $content = tidy_repair_string($content, $options);
        $content = str_replace('&nbsp;', " ", $content);
        //$content = html_entity_decode($content);
//        dd($content);

        xml_parse($parser, '<xml>' . $content . '</xml>');

        return Str::after(Str::beforeLast($this->buffer, '</xml>'), '<xml>');

    }

    // Called to this function when tags are opened
    public function startElements($parser, $name, $attrs)
    {
        $name = mb_strtolower($name);
        if (isset($this->parsed_rules[$name])) {
            $this->level++;
            $this->buffer .= str_repeat("\t", $this->level - 1) . '<' . mb_strtolower($name);

            // Save images if a base64 was sent, store it in the db
            if ($name == 'img') {
                if (isset($attrs['SRC']) && Str::startsWith($attrs['SRC'], 'data:image')) {

                    $image_type = Str::after(Str::before($attrs['SRC'], ';'), '/');
                    $image_type = in_array($image_type, ['jpg','jpeg','png']) ? $image_type : 'png';

                    // 0. Make the image
                    $image = Image::make($attrs['SRC'])->encode($image_type, 90);

                    // 1. Generate a filename.
                    $filename = md5($attrs['SRC']) . '.' . $image_type;

                    $disk = $this->config['images']['disk'];
                    $path = rtrim($this->config['images']['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                    // 2. Store the image on disk.
                    \Storage::disk($disk)
                        ->put($path . $filename, $image->stream());

                    // 4. Save the public path to the database
                    // but first, remove "public/" from the path, since we're pointing to it
                    // from the root folder; that way, what gets saved in the db
                    // is the public URL (everything that comes after the domain name)
                    // $public_destination_path = Str::replaceFirst('public/', '/img/_articles/');

                    $uri_prefix = rtrim($this->config['images']['url'], '/') . '/';
                    $attrs['SRC'] = $uri_prefix . $filename;
                }
            }

            foreach ($attrs as $k => $v) {
                if ($this->parsed_rules[$name] === true || in_array(mb_strtolower($k), $this->parsed_rules[$name])) {
                    $this->buffer .= ' ' . mb_strtolower($k) . '="' . $v . '"';
                }
            }
            $this->buffer .= '>' . "\n";
        }
    }

    // Called to this function when tags are closed
    public function endElements($parser, $name)
    {
        $name = mb_strtolower($name);
        if (isset($this->parsed_rules[$name])) {
            if (!in_array($name, ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'])) {
                $this->buffer .= "\n" . str_repeat("\t", $this->level - 1) . '</' . mb_strtolower($name) . '>';
            }
            $this->level--;
        }
    }

    // Called on the text between the start and end of the tags
    public function characterData($parser, $data)
    {
        if (!empty($data)) {
            $this->buffer .= str_repeat("\t", $this->level) . $data;
        }
    }
}

