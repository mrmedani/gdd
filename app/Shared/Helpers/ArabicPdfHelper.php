<?php

namespace App\Shared\Helpers;

use ArPHP\I18N\Arabic;

class ArabicPdfHelper
{
    /**
     * Processes HTML content to shape and reverse Arabic text nodes,
     * making them compatible with DomPDF's rendering engine.
     *
     * @param string $html
     * @return string
     */
    public static function processHtml(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        $arabic = new Arabic();
        
        $dom = new \DOMDocument();
        // Silence potential HTML parsing warnings/errors
        libxml_use_internal_errors(true);
        // Load with UTF-8 XML declaration so DOMDocument reads UTF-8 correctly
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        self::processNode($dom, $arabic);
        
        $outputHtml = $dom->saveHTML();
        
        // Remove the prepended XML declaration if present
        $outputHtml = preg_replace('/<\?xml[^>]*\?>/i', '', $outputHtml);
        
        return $outputHtml;
    }
    
    /**
     * Recursively traverses DOM nodes and shapes Arabic text in text nodes.
     *
     * @param \DOMNode $node
     * @param Arabic $arabic
     */
    private static function processNode(\DOMNode $node, Arabic $arabic): void
    {
        if ($node instanceof \DOMText) {
            $val = $node->nodeValue;
            // Check if node contains any Arabic character
            if (preg_match('/\p{Arabic}/u', $val)) {
                // Ensure we are not processing text inside style or script tags
                $parent = $node->parentNode;
                while ($parent) {
                    if (in_array(strtolower($parent->nodeName), ['style', 'script'])) {
                        return;
                    }
                    $parent = $parent->parentNode;
                }
                
                // Shape the Arabic characters using ar-php
                $node->nodeValue = $arabic->utf8Glyphs($val);
            }
            return;
        }
        
        if ($node->hasChildNodes()) {
            $children = [];
            foreach ($node->childNodes as $child) {
                $children[] = $child;
            }
            foreach ($children as $child) {
                self::processNode($child, $arabic);
            }
        }
    }
}
