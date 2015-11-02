<?php

// abort the default pattern
if($config->page_type === 'article' && Route::is($config->index->slug . '/(:any)')) {
    Shield::abort('404-article');
}

// pattern: `http://localhost/article/2015/04/slug`
Route::accept($config->index->slug . '/(:num)/(:num)/(:any)' . $cp_config['extension'], function($year = "", $month = "", $slug = "") use($config) {
    if($path = Get::articlePath($slug)) {
        $s = explode('_', File::N($path));
        $s = explode('-', $s[0]);
        if(
            (string) $year !== (string) $s[0] ||
            (string) $month !== (string) $s[1]
        ) {
            Shield::abort('404-article');
        }
    } else {
        Shield::abort('404-article');
    }
    Route::execute($config->index->slug . '/(:any)', array($slug));
}, 1);

// from: `http://localhost/article/slug`
// to: `http://localhost/article/2015/04/slug`
function do_custom_permalink($url) {
    global $config, $cp_config;
    if($path = Get::articlePath(File::B($url))) {
        list($time, $kind, $slug) = explode('_', File::N($path), 3);
        $time = explode('-', $time);
        return $config->url . '/' . $config->index->slug . '/' . $time[0] . '/' . $time[1] . '/' . $slug . $cp_config['extension'];
    }
    return $url;
}

// fix page types
if(Route::is($config->index->slug . '/(:num)/(:num)/(:any)' . $cp_config['extension'])) {
    $config->page_type = Get::articlePath(File::N($config->url_path)) ? 'article' : 'page';
    Config::set('page_type', $config->page_type);
}