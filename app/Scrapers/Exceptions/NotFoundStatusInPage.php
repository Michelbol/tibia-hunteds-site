<?php

namespace App\Scrapers\Exceptions;

use PHPHtmlParser\Dom\HtmlNode;

class NotFoundStatusInPage extends \Exception {

    private readonly HtmlNode $html;
    public function __construct(HtmlNode $node) {
        $this->html = $node;
        parent::__construct();
    }

    public function getHtmlNode(): HtmlNode {
        return $this->html;
    }
}
