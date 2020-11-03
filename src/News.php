<?php

namespace Azeroglu\Packagist;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

require "vendor/autoload.php";

set_time_limit(0);

class News
{
    private $limit;
    private $format;

    public function __construct()
    {
        $this->limit = 3;
        $this->format = 'html';
    }

    /*
     * Set Limit
     * */
    public function setLimit($val)
    {
        $this->limit = $val;
        return $this;
    }

    /*
     * Set Format
     * */
    public function setFormat($val)
    {
        $this->format = $val;
        return $this;
    }

    /*
     * Apa.az
     * */
    public function getApaAz()
    {
        try {
            $dom = new Dom();
            $dom->loadFromUrl('https://apa.az/');
            $links = $dom->find('.newsletter-body .module-flash');
            $links = ($links) ? $links : [];
            $result = [];
            foreach ($links as $item):
                $link = ($item) ? 'https://apa.az' . $item->href : null;
                if ($link):
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
                        'title' => $this->format == 'html' ? $title : strip_tags($title),
                        'image' => $image,
                        'date' => $date,
                        'text' => $this->format == 'html' ? $text : strip_tags($text),
                        'source' => 'apa.az'
                    ];
                endif;
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Oux.az
     * */
    public function getOxuAz()
    {
        try {
            $dom = new Dom();
            $dom->loadFromUrl('https://oxu.az/');
            $links = $dom->find('.news-list .news-i .news-i-inner');
            $links = ($links) ? $links : [];
            $result = [];
            foreach ($links as $link):
                $link = ($link) ? 'https://oxu.az' . $link->href : null;
                if ($link):
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
                        'title' => $this->format == 'html' ? $title : strip_tags($title),
                        'image' => $image,
                        'date' => $date,
                        'text' => $this->format == 'html' ? $text : strip_tags($text),
                        'source' => 'oxu.az'
                    ];
                endif;
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Report.az
     * */
    public function getReportAz()
    {
        try {
            $client = new Client([
                'base_uri' => 'https://report.az'
            ]);
            $client = $client->request('GET', '/news-feed');
            $links = json_decode($client->getBody(), true);
            $links = $links['posts'];
            $result = [];
            foreach ($links as $item):
                $link = @$item['url'] ? 'https://report.az' . $item['url'] : null;
                if ($link):
                    $html = new Dom();
                    $html = $html->loadFromUrl('https://report.az/dagliq-qarabag-munaqishesi/prezidentin-komekcisi-ermenistanin-bmt-konvensiyasini-pozdugunu-aciqlayib/');
                    /*
                     * Title
                     * */
                    $title = $html->find('.selected-news .news-title');
                    $title = ($title) ? $title->text : null;
                    /*
                     * Image
                     * */
                    $image = $html->find('.news-cover .image img');
                    $image = ($image) ? $image->src : null;
                    /*
                     * Date
                     * */
                    $dateHtml = $html->find('.category-date .news-date span');
                    $date = '';
                    foreach ($dateHtml as $item):
                        $date .= $item->text . ' ';
                    endforeach;
                    $date = $date ? rtrim($date, ' ') : null;
                    /*
                     * Text
                     * */
                    $text = $html->find('.selected-news .editor-body');
                    $text = ($text) ? $text->innerHTML : null;

                    $result[] = [
                        'title' => $this->format == 'html' ? $title : strip_tags($title),
                        'image' => $image,
                        'date' => $date,
                        'text' => $this->format == 'html' ? $text : strip_tags($text),
                        'source' => 'report.az'
                    ];
                    $result[] = $title;
                endif;
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Mod Gov Az
     * */
    public function getModGovAz()
    {
        try {
            $dom = new Dom();
            $dom->loadFromUrl('https://mod.gov.az/az/xeberler-791/');
            $links = $dom->find('.n_items .news_item');
            $links = ($links) ? $links : [];
            $result = [];
            foreach ($links as $link):
                $link = ($link) ? $link->href : null;
                if ($link):
                    $html = new Dom();
                    $html = $html->loadFromUrl($link);
                    /*
                     * Title
                     * */
                    $title = $html->find('.n_title h2');
                    $title = ($title) ? strip_tags($title->innerHTML) : null;
                    /*
                     * Image
                     * */
                    $image = $html->find('.n_top .n_tp img');
                    $image = ($image) ? $image->src : null;
                    /*
                     * Date
                     * */
                    $date = $html->find('.n_title span');
                    $date = ($date) ? $date->text : null;
                    /*
                     * Text
                     * */
                    $text = $html->find('.vi_in_desc');
                    $text = ($text) ? $text->innerHTML : null;

                    $video = '';
                    if (strpos($title, 'VİDEO') !== false):
                        $video = new Dom();
                        $video = $video->loadStr($text);
                        $video = $video->find('.video-container video source');
                        $video = ($video) ? $video->src : null;
                    endif;

                    $result[] = [
                        'title' => $this->format == 'html' ? $title : strip_tags($title),
                        'image' => $image,
                        'date' => $date,
                        'text' => $this->format == 'html' ? $text : strip_tags($text),
                        'video' => $video,
                        'source' => 'mod.gov.az'
                    ];
                endif;
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
