<?php

namespace Orchestra\Html\Macros;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\Paginator;
use Orchestra\Html\HtmlBuilder;
use Orchestra\Support\Str;

class Title
{
    /**
     * The HTML builder implementation.
     *
     * @var \Orchestra\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Site name.
     *
     * @var string
     */
    protected $site;

    /**
     * Text formats.
     *
     * @var array
     */
    protected $formats = [
        'site' => '{site.name} (Page {page.number})',
        'page' => '{page.title} &mdash; {site.name}',
    ];

    /**
     * Construct a new title builder.
     *
     * @param string  $site
     */
    public function __construct(HtmlBuilder $html, $site, array $formats = [])
    {
        $this->html = $html;
        $this->site = $site;
        $this->formats = $formats;
    }

    /**
     * Set text formats.
     */
    public function setFormat(array $formats = []): void
    {
        $this->formats = \array_filter($formats);
    }

    /**
     * Create the title.
     */
    public function title(string $title = null): Htmlable
    {
        $page = Paginator::resolveCurrentPage();

        $data = [
            'site' => ['name' => $this->site],
            'page' => ['title' => $title, 'number' => $page],
        ];

        $data['site']['name'] = $this->getHtmlTitleFormatForSite($data);

        $output = $this->getHtmlTitleFormatForPage($data);

        return $this->html->create('title', trim($output));
    }

    /**
     * Get HTML::title() format for site.
     *
     * @return mixed
     */
    protected function getHtmlTitleFormatForSite(array $data)
    {
        if ((int) $data['page']['number'] < 2) {
            return $data['site']['name'];
        }

        return Str::translate($this->formats['site'], $data);
    }

    /**
     * Get HTML::title() format for page.
     *
     * @return mixed
     */
    protected function getHtmlTitleFormatForPage(array $data)
    {
        if (empty($data['page']['title'])) {
            return $data['site']['name'];
        }

        return Str::translate($this->formats['page'], $data);
    }
}
