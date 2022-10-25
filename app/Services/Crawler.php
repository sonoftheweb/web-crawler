<?php

namespace App\Services;

use App\Exceptions\CrawlerFailedException;
use CurlHandle;
use DOMDocument;

class Crawler
{
	private false|CurlHandle $curl;
	private string $url;
	private string $baseUrl;
	private int $pages;
	private array $pagesToBeParsed = [];
	private array $crawlerPages = [];
	private array $pageLoadInfo = [];
	
	public function __construct()
	{
		$this->curl = curl_init(); // init curl
	}
	
	/**
	 * @param string $url
	 * @return void
	 */
	public function setUrl(string $url): void
	{
		$parsedUrl = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
		$this->baseUrl = trim($parsedUrl, '/');
		$this->url = $url;
		
		if (count($this->pagesToBeParsed) < 1) {
			$this->pagesToBeParsed[] = $this->buildUrl();
		}
	}
	
	/**
	 * @param int $count
	 * @return void
	 */
	public function setPagesCount(int $count): void
	{
		$this->pages = $count;
	}
	
	/**
	 * Begin crawl
	 * @return void
	 * @throws CrawlerFailedException
	 */
	public function crawl(): void
	{
		for ($i = 1; $i <= $this->pages; $i++) {
			$html = $this->getPageHtml();
			
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			$links = $this->getLinks($doc);
			$externalLinks = $this->getExternalLinks($doc);
			
			$this->crawlerPages[] = [
				'html' => $doc,
				'href' => $this->pagesToBeParsed[0],
				'links' => $links,
				'external_links' => $externalLinks,
				'load_time' => $this->pageLoadInfo['total_time'],
				'http_code' => $this->pageLoadInfo['http_code'],
			];
			
			array_shift($this->pagesToBeParsed);
			if (count($links) > 0) {
				array_push($this->pagesToBeParsed, ...array_slice(array_column($links, 'href'), 0, $this->pages));
			}
			
			sleep(1);
		}
	}
	
	/**
	 * @return array
	 */
	public function pagesCrawled(): array
	{
		return $this->crawlerPages;
	}
	
	/**
	 * @return array
	 */
	public function uniqueImages(): array
	{
		return array_unique($this->allImages());
	}
	
	/**
	 * @param bool $external
	 * @return array
	 */
	public function uniqueLinks(bool $external = false): array
	{
		return array_unique($this->allLinks($external));
	}
	
	/**
	 * @return float|int
	 */
	public function averageLoadTime(): float|int
	{
		$loadTimes = array_column($this->crawlerPages, 'load_time');
		$loadTimes = array_filter($loadTimes); /// remove possible empty values
		if (count($loadTimes)) {
			return array_sum($loadTimes)/count($loadTimes);
		}
		return 0;
	}
	
	/**
	 * @param string[] $tags
	 * @return float|int
	 */
	public function averageWordCount(array $tags = ['main', 'header', 'footer']): float|int
	{
		$textNodeContent = '';
		$pageCount = count($this->pagesCrawled());
		$pagesDoc = array_column($this->crawlerPages, 'html');
		foreach ($pagesDoc as $page) {
			foreach ($tags as $tag) {
				$element = $page->getElementsByTagName($tag);
				$textNodeContent .= $element->item(0)->textContent;
			}
		}
		$textNodeContent = str_replace(["\r", "\n", "\t"], " ", $textNodeContent);
		return str_word_count($textNodeContent)/$pageCount;
	}
	
	/**
	 * @return float|int
	 */
	public function averageTitleLength(): float|int
	{
		$pagesDoc = array_column($this->crawlerPages, 'html');
		$strLength = 0;
		$pageCount = count($this->pagesCrawled());
		foreach ($pagesDoc as $page) {
			$titles = $page->getElementsByTagName('h3');
			foreach ($titles as $title) {
				$strLength += strlen(trim(preg_replace("/[\r\n]+/", " ", $title->nodeValue)));
			}
		}
		return $strLength / $pageCount;
	}
	
	/**
	 * Set the URL to reach with a GET HTTP request.
	 * Get the data returned by the cURL request as a string.
	 * Make the cURL request follow eventual redirects, and reach the final page of interest,
	 * Execute the cURL request and get the HTML of the page as a string.
	 * @return bool|string
	 * @throws CrawlerFailedException
	 */
	private function getPageHtml(): bool|string
	{
		curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($this->curl, CURLOPT_URL, $this->pagesToBeParsed[0]);
		curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($this->curl, CURLOPT_FAILONERROR, true);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
		$html = curl_exec($this->curl);
		
		if (!$html) {
			throw new CrawlerFailedException('failed to get page');
		}
		
		$this->pageLoadInfo = curl_getinfo($this->curl);
		$this->closeCurl();
		
		return $html;
	}
	
	/**
	 * close curl to release resource
	 * @return void
	 */
	private function closeCurl(): void
	{
		curl_close($this->curl);
	}
	
	/**
	 * @param string|null $url
	 * @return string
	 */
	private function buildUrl(string|null $url = null): string
	{
		$useUrl = $this->url;
		if ($url) {
			$useUrl = $url;
		}
		return (str_contains($useUrl, $this->baseUrl)) ? $useUrl : $this->baseUrl . $useUrl;
	}
	
	/**
	 * checks if the href string is internal
	 * @param string $href
	 * @return bool
	 */
	private function isNotInternal(string $href): bool
	{
		return match (true) {
			str_starts_with($href, 'http') && !str_starts_with($href, $this->baseUrl) => true,
			default => false,
		};
	}
	
	/**
	 * @param DOMDocument $doc
	 * @return array
	 */
	private function getLinks(DOMDocument $doc): array
	{
		$links = [];
		$linksArr = $doc->getElementsByTagName("a");
		foreach ($linksArr as $item) {
			$href = $item->getAttribute("href");
			
			if (str_starts_with($href, '#')) continue; // if this is a hash link, ignore
			if ($this->isNotInternal($href)) continue;
			if ($this->baseUrl . $href === $this->pagesToBeParsed[0]) continue; // if this link is same as the currently crawled page, ignore
			if (
				in_array($this->baseUrl . $href, $this->pagesToBeParsed) ||
				in_array($this->baseUrl . $href, array_column($this->crawlerPages, 'href'))
			) continue;
			
			$text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
			$links[] = [
				'href' => $this->buildUrl($href),
				'text' => $text
			];
		}
		return $links;
	}
	
	/**
	 * @param DOMDocument $doc
	 * @return array
	 */
	private function getExternalLinks(DOMDocument $doc): array
	{
		$links = [];
		$linksArr = $doc->getElementsByTagName("a");
		
		foreach ($linksArr as $item) {
			$href = $item->getAttribute("href");
			if (
				str_starts_with($href, '/') ||
				str_starts_with($href, '#') ||
				str_starts_with($href, $this->baseUrl) ||
				!str_starts_with($href, 'http')
			) continue;
			
			$text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
			$links[] = [
				'href' => $href,
				'text' => $text
			];
		}
		return $links;
	}
	
	/**
	 * @return array
	 */
	public function allImages(): array
	{
		$images = [];
		foreach ($this->crawlerPages as $doc) {
			$docImages = $doc['html']->getElementsByTagName('img');
			$docImagesSource = $doc['html']->getElementsByTagName('source');
			foreach ($docImages as $docImage) {
				if (str_starts_with($docImage->getAttribute('src'), 'data') || $docImage->getAttribute('src') == '') // omit svg images
					continue;
				$imageString = explode('?', $docImage->getAttribute('src'));
				$images[] = $imageString[0];
			}
			foreach ($docImagesSource as $docImageSource) {
				if (!$docImageSource->hasAttribute('data-srcset') || $docImageSource->hasAttribute('data-srcset') == '')
					continue;
				$imageString = explode('?', $docImageSource->getAttribute('data-srcset'));
				$images[] = $imageString[0];
			}
		}
		return $images;
	}
	
	/**
	 * @param bool $external
	 * @return array
	 */
	public function allLinks(bool $external = false): array
	{
		if ($external) {
			$links = array_column($this->crawlerPages, 'external_links');
		} else {
			$links = array_column($this->crawlerPages, 'links');
		}
		
		$links = array_merge(...$links);
		
		return array_column($links, 'href');
	}
}