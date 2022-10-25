<?php

namespace App\Http\Controllers;

use App\Exceptions\CrawlerFailedException;
use App\Services\Crawler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
	/**
	 * @throws CrawlerFailedException
	 */
	public function store(Request $request, Crawler $crawler): JsonResponse
	{
		$validatedData = $request->validate([
			'url' => ['required', 'url'],
			'count' => ['required', 'numeric'],
		]);
		
		$crawler->setUrl($validatedData['url']);
		$crawler->setPagesCount($validatedData['count']);
		$crawler->crawl();
		
		return response()->json([
			'pages' => $crawler->pagesCrawled(),
			'unique_images' => count($crawler->uniqueImages()),
			'unique_links' => count($crawler->uniqueLinks()),
			'unique_external_links' => count($crawler->uniqueLinks(true)),
			'average_load_times' => $crawler->averageLoadTime(),
			'average_word_count' => $crawler->averageWordCount(),
			'average_title_length' => $crawler->averageTitleLength(),
		]);
    }
}
