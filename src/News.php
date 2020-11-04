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
     * Get Month
     * */
    public function getMonth($code, $lang = 'az')
    {
        $items['az'] = [
            'yanvar' => '01',
            'fevral' => '02',
            'mart' => '03',
            'aprel' => '04',
            'may' => '05',
            'iyun' => '06',
            'iyul' => '07',
            'avqust' => '08',
            'sentyabr' => '09',
            'oktyabr' => '10',
            'noyabr' => '11',
            'dekabr' => '12',
        ];
        $items['az_min'] = [
            'yan' => '01',
            'fev' => '02',
            'mar' => '03',
            'apr' => '04',
            'may' => '05',
            'iyun' => '06',
            'iyul' => '07',
            'avq' => '08',
            'sen' => '09',
            'okt' => '10',
            'noy' => '11',
            'dek' => '12',
        ];
        $items['en'] = [
            'january' => '01',
            'february' => '02',
            'march' => '03',
            'april' => '04',
            'may' => '05',
            'june' => '06',
            'july' => '07',
            'august' => '08',
            'september' => '09',
            'october' => '10',
            'november' => '11',
            'december' => '12',
        ];
        $items['ru'] = [
            'январь' => '01',
            'февраль' => '02',
            'март' => '03',
            'апрель' => '04',
            'май' => '05',
            'июнь' => '06',
            'июль' => '07',
            'август' => '08',
            'сентябрь' => '09',
            'октябрь' => '10',
            'ноябрь' => '11',
            'декабрь' => '12',
        ];
        $code = strtolower($code);
        $result = '';
        if (@$items[$lang][$code])
            $result = $items[$lang][$code];
        else if (@$items[$lang . '_min'][$code])
            $result = $items[$lang . '_min'][$code];
        return $result;
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
            $i = 0;
            foreach ($links as $item):
                try {
                    $link = ($item) ? 'https://apa.az' . $item->href : null;
                    if ($link && $i < $this->limit):
                        $html = new Dom();
                        $html = $html->loadFromUrl($link);
                        $page = $html->find('.news-block');
                        /*
                         * Title
                         * */
                        $title = $page->find('h2');
                        $title = ($title) ? strip_tags($title->innerHTML) : null;
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
                        $dateExplode = explode(' ', $date);
                        $date = $dateExplode[2] . '-' . $this->getMonth($dateExplode[1]) . '-' . $dateExplode[0] . ' ' . $dateExplode[3] . ':00';
                        /*
                         * Text
                         * */
                        $text = $page->find('.category-text');
                        $text = ($text) ? $text->innerHtml : null;
                        /*
                         * Video
                         * */
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
                            'source' => 'apa.az',
                            'source_url' => $link
                        ];
                        $i++;
                    endif;
                } catch (\Exception $e) {
                    $result[] = [];
                }
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
            $i = 0;
            foreach ($links as $link):
                try {
                    $link = ($link) ? 'https://oxu.az' . $link->href : null;
                    if ($link && $i < $this->limit):
                        $html = new Dom();
                        $html = $html->loadFromUrl($link);
                        /*
                         * Title
                         * */
                        $title = $html->find('.news-inner h1');
                        $title = ($title) ? strip_tags($title->innerHTML) : null;
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
                        $dateExplode = explode(' ', $date);
                        $date = $dateExplode[2] . '-' . $this->getMonth($dateExplode[1]) . '-' . ($dateExplode[0] < 10 ? '0' . $dateExplode[0] : $dateExplode[0]) . ' ' . $dateExplode[3] . ':00';
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
                        /*
                         * Video
                         * */
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
                            'source' => 'oxu.az',
                            'source_url' => $link
                        ];
                        $i++;
                    endif;
                } catch (\Exception $e) {
                    $result[] = [];
                }
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
            $i = 0;
            foreach ($links as $item):
                try {
                    $link = @$item['url'] ? 'https://report.az' . $item['url'] : null;
                    if ($link && $i < $this->limit):
                        $html = new Dom();
                        $html = $html->loadFromUrl($link);
                        /*
                         * Title
                         * */
                        $title = $html->find('.selected-news .news-title');
                        $title = ($title) ? strip_tags($title->innerHTML) : null;
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
                        $date = $date ? str_replace([', '], null, rtrim($date, ' ')) : null;
                        $dateExplode = explode(' ', $date);
                        $date = $dateExplode[2] . '-' . $this->getMonth($dateExplode[1]) . '-' . ($dateExplode[0] < 10 ? '0' . $dateExplode[0] : $dateExplode[0]) . ' ' . $dateExplode[3] . ':00';
                        /*
                         * Text
                         * */
                        $text = $html->find('.selected-news .editor-body');
                        $text = ($text) ? $text->innerHTML : null;
                        /*
                         * Video
                         * */
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
                            'source' => 'report.az',
                            'source_url' => $link
                        ];
                        $i++;
                    endif;
                } catch (\Exception $e) {
                    $result[] = [];
                }
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
            $i = 0;
            foreach ($links as $link):
                try {
                    $link = ($link) ? $link->href : null;
                    if ($link && $i < $this->limit):
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
                        $dateExplode = explode(' ', $date);
                        $date = $dateExplode[2] . '-' . $this->getMonth($dateExplode[1]) . '-' . ($dateExplode[0] < 10 ? '0' . $dateExplode[0] : $dateExplode[0]) . ' ' . $dateExplode[3] . ':00';
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
                            'source' => 'mod.gov.az',
                            'source_url' => $link
                        ];
                        $i++;
                    endif;
                } catch (\Exception $e) {
                    return $result[] = [];
                }
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Sport 24 Az
     * */
    public function getSport24Az()
    {
        try {
            $dom = new Dom();
            $dom->loadFromUrl('http://sport24.az/');
            $links = $dom->find('.media-list .media .media-body');
            $links = ($links) ? $links : [];
            $result = [];
            $i = 0;
            foreach ($links as $link):
                try {
                    $link = ($link) ? $link->href : null;
                    if ($link && $i < $this->limit):
                        $link = 'http://sport24.az/' . $link;
                        $html = new Dom();
                        $html = $html->loadFromUrl($link);
                        /*
                         * Title
                         * */
                        $title = $html->find('.news-main .one-news-main .one-news-heading');
                        $title = ($title) ? strip_tags($title->innerHTML) : null;
                        /*
                         * Image
                         * */
                        $image = $html->find('.one-news-image img');
                        $image = ($image) ? 'http://sport24.az' . $image->src : null;
                        /*
                         * Date
                         * */
                        $date = $html->find('.one-news-info .one-news-date');
                        $date = ($date) ? $date->text . ':00' : null;
                        /*
                         * Text
                         * */
                        $text = $html->find('.one-news-main .one-news-text');
                        $text = ($text) ? $text->innerHtml : null;
                        /*
                         * Video
                         * */
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
                            'source' => 'sport24.az',
                            'source_url' => $link
                        ];
                        $i++;
                    endif;
                } catch (\Exception $e) {
                    $result[] = [];
                }
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * SportLine Az
     * */
    public function getSportLineAz()
    {
        try {
            $dom = new Dom();
            $dom->loadFromUrl('https://sportline.az/');
            $links = $dom->find('.article-content .entry-title a');
            $links = ($links) ? $links : [];
            $result = [];
            $i = 0;
            foreach ($links as $link):
                try {
                    $link = ($link) ? $link->href : null;
                    if ($link && $i < $this->limit):
                        $link = 'https://sportline.az' . $link;
                        $html = new Dom();
                        $html = $html->loadFromUrl($link);
                        /*
                         * Title
                         * */
                        $title = $html->find('.article-content .entry-title');
                        $title = ($title) ? strip_tags($title->innerHTML) : null;
                        /*
                         * Image
                         * */
                        $image = $html->find('.featured-image img');
                        $image = ($image) ? 'https://sportline.az' . $image->src : null;
                        /*
                         * Date
                         * */
                        $dateHtml = $html->find('.below-entry-meta .entry-date');
                        $dateHtml = ($dateHtml) ? $dateHtml->text : null;
                        $dateExplode = explode(' ', $dateHtml);
                        $time = $dateExplode[0];
                        $date = $dateExplode[3] . '-' . $this->getMonth($dateExplode[2]) . '-' . $dateExplode[1];
                        $date = $date . ' ' . $time . ':00';
                        /*
                         * Text
                         * */
                        $text = $html->find('.article-content .entry-content');
                        $text = ($text) ? $text->innerHTML : null;
                        /*
                         * Video
                         * */
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
                            'source' => 'sport24.az',
                            'source_url' => $link
                        ];
                        $i++;
                    endif;
                } catch (\Exception $e) {
                    $result[] = [];
                }
            endforeach;
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
