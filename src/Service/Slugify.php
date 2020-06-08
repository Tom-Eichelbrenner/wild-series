<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input): string
    {
        $replace = ['?','!',"'",'.'];
        $e = ['é','è','ê'];
        $a = ['à','â'];
        $i = ['î'];
        $o = ['ô'];
        $u = ['ù','û'];
        $slug = str_replace(' ', '-', $input);
        $slug = str_replace($replace, '',$slug);
        $slug = str_replace($e, 'e',$slug);
        $slug = str_replace($i, 'i',$slug);
        $slug = str_replace($o, 'o',$slug);
        $slug = str_replace($u, 'u',$slug);
        $slug = str_replace('--', '-',$slug);
        $slug = trim($slug);
        $slug = strtolower($slug);
        return $slug;
    }
}