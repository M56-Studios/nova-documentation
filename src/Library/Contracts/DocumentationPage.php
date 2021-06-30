<?php

namespace Dniccum\NovaDocumentation\Library\Contracts;

class DocumentationPage
{
    /**
     * @var
     */
    public $title;

    /**
     * @var string
     */
    public $route;

    /**
     * @var
     */
    public $file;

    /**
     * @var bool
     */
    public $isHome = false;

    /**
     * @var
     */
    public $pageTitle;

    /**
     * @var string
     */
    public $content;

    /**
     * @var null|int
     */
    public $order;

    public function __construct($file, string $route, PageContent $content, bool $isHome = false)
    {
        $this->file = $file;
        $this->title = config('novadocumentation.title', 'Documentation');

        $this->content = $this->replaceLinks($content->content);
        $this->isHome = $isHome;

        if ($content->path) {
            $this->route = $content->path;
        } else {
            $this->route = $route;
        }
        if ($content->title) {
            $this->pageTitle = $content->title;
        } else {
            $this->pageTitle = $this->getPageTitle($file);
        }
        if ($content->order) {
            $this->order = $content->order;
        }
    }

    /**
     * @param string $htmlContent
     * @return string
     */
    private function replaceLinks(string $htmlContent): string
    {
        $regex = "/<a.+href=['|\"](?!http|https|mailto|\/)([^\"\']*)['|\"].*>(.+)<\/a>/i";
        $output = preg_replace($regex, '<a href="' . config('nova.path', '/nova') . '/documentation/\1">\2</a>', $htmlContent);
        $output = preg_replace("/(\.md|\.text|\.mdown|\.mkdn|\.mkd|\.mdwn|\.mdtxt|\.Rmd|\.mdtext)/i", '"', $output);

        return $output;
    }

    /**
     * Returns the title of the page
     * @param string $filePath
     * @return string
     */
    private function getPageTitle(string $filePath): string
    {
        $lines = file($filePath);
        $title = '';

        foreach ($lines as $line) {
            if (strpos($line, '# ') === 0) {
                $title = substr($line, 2);
            }
            break;
        }

        if (strlen($title) === 0) {
            $title = !empty($lines[0]) ? $lines[0] : 'Page Title';
        }

        return $title;
    }
}
