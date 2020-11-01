<?php

namespace Azeroglu\Packagist;

use PHPHtmlParser\Dom;

require "vendor/autoload.php";

set_time_limit(0);

class News
{
    /*
     * Apa.az
     * */
    public static function apa_az($limit = 10)
    {
        $dom = new Dom();
        $dom->loadFromUrl('https://apa.az/');
        $links = $dom->find('.newsletter-body .module-flash');
        $links = collect($links)->map(function ($item) {
            return 'https://apa.az' . $item->href;
        })->take($limit);
        $result = [];
        foreach ($links as $link):
            $html = new Dom();
            $html = $html->loadFromUrl($link);
            $page = $html->find('.news-block');
            /*
             * Title
             * */
            $title = $page->find('h2');
            $title = ($title) ? $title->text : null;
            /*
             * Image
             * */
            $image = $page->find('.row .col-md-10 .img-responsive');
            $image = ($image) ? $image->src : null;
            /*
             * Date
             * */
            $dateHtml = @$page->find('.col-md-2 .general-infor .list-unstyled li');
            $clock = @($dateHtml[0]) ? $dateHtml[0]->find('.date')->text : null;
            $date = @($dateHtml[1]) ? $dateHtml[1]->find('.date')->text : null;
            $date = $clock && $date ? $date . ' ' . $clock : null;
            /*
             * Text
             * */
            $text = $page->find('.category-text');
            $text = ($text) ? $text->innerHtml : null;
            $result[] = [
                'title' => $title,
                'image' => $image,
                'date' => $date,
                'text' => $text
            ];
        endforeach;
        return $result;
    }

    /*
     * Oux.az
     * */
    public static function oxu_az($limit = 10)
    {
        $dom = new Dom();
        $dom->loadFromUrl('https://oxu.az/');
        $links = $dom->find('.news-list .news-i .news-i-inner');
        $links = collect($links)->map(function ($item) {
            return 'https://oxu.az/' . $item->href;
        })->take($limit);
        $result = [];
        foreach ($links as $link):
            $html = new Dom();
            $html = $html->loadFromUrl($link);
            /*
             * Title
             * */
            $title = $html->find('.news-inner h1');
            $title = ($title) ? $title->text : null;
            /*
             * Image
             * */
            $image = $html->find('.news .news-img');
            $image = ($image) ? $image->src : null;
            /*
             * Date
             * */
            $dateHtml = $html->find('.news .when');
            $findDate = $dateHtml->find('.when-date');
            $findDate = ($findDate) ? $findDate->find('div') : null;
            $date = '';
            foreach ($findDate as $item):
                $date .= str_replace(['&nbsp;', '&nbsp'], null, trim($item->text)) . ' ';
            endforeach;
            $date = rtrim($date, ' ');
            $clock = $dateHtml->find('.when-time');
            $clock = ($clock) ? $clock->text : null;
            $date = $date && $clock ? $date . ' ' . $clock : null;
            /*
             * Text
             * */
            $textHtml = $html->find('.news-inner');
            $div = ($textHtml) ? $textHtml->find('div') : null;
            $div ? $div->delete() : null;
            $h1 = ($textHtml) ? $textHtml->find('h1') : null;
            $h1 ? $h1->delete() : null;
            $meta = ($textHtml) ? $textHtml->find('meta') : null;
            $meta ? $meta->delete() : null;
            $text = ($textHtml) ? $textHtml->innerHTML : null;
            $text = str_replace(['<p></p>'], null, $text);
            $result[] = [
                'title' => $title,
                'image' => $image,
                'date' => $date,
                'text' => $text
            ];
        endforeach;
        return $result;
    }

    /*
     * Report.az
     * */
    public static function report_az($limit = 10)
    {
        $dom = new Dom();
        $dom->loadFromUrl('https://report.az/');
        $links = $dom->find('.latest-news .news-item .info .title');
        /*$links = collect($links)->map(function ($item) {
            return 'https://oxu.az/' . $item->href;
        })->take($limit);*/

        return $links->innerHTML;
        $result = [];
    }
}
