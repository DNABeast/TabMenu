<?php

namespace Typesaucer\MenuTabber;

use Illuminate\Support\Facades\Request;

// use App\Exceptions\ListDoesntClose;

class Menu
{
	public function build($string){
		if (Request::segment(1)=='admin') {
			$prefix = Request::segment(1);
		} else {
			$prefix = null;
		}
		$result = $this->formatList($string, $prefix = null);
		$this->countTags($result);
		return $result;
	}

	public function removeEmptylines($string)
	{
		$string = preg_replace('/\n^\t*$/m', '', $string);
		$string = preg_replace('/^\n/', '', $string);
		return $string;
	}

	public function explodeString($string){
		$string = $this->removeEmptylines($string);
		return explode(PHP_EOL, $string);
	}

	public function countTabsArray($string)
	{
		$array = $this->explodeString($string);

		$array = array_map(function($item){
			$tabCount = substr_count($item, '	');
			$linkData = str_replace('	', '', $item);
			return [$tabCount, $linkData];
		}, $array);

		return $array;
	}

	public function formatAnchorTag($string, $prefix = null)
	{
		$array = explode(',', $string);
		$array = array_map(function($item){
			return trim($item, ' ');
		}, $array);

		$array = $this->createSlugLink($array, $prefix);
		$class = $this->AddClassToLink($array);

		return '<a href="'.$array[1].'"'.$class.'>'.$array[0].'</a>';
	}

	public function createSlugLink($linkArray, $prefix = null){
		if (!isset($linkArray[1])){
			$linkArray[1] = '/'.str_slug($linkArray[0]);
		}
		if (isset($prefix)){
			$linkArray[1] = '/'.$prefix.$linkArray[1];
		}
		return $linkArray;
	}

	public function addClassToLink($linkArray){
			if (isset($linkArray[2])){
				$class=' class="'.$linkArray[2].'"';
			} else {
				$class = '';
			}
		return $class;
	}

	public function formatList($string, $prefix = null){
		$array = $this->countTabsArray($string);

		$listHTML = '';
		$startTab = $array[0][0];
		$counter = $startTab-1;
		foreach ($array as $line){
			$diff = $line[0]-$counter;
			if ($diff==1){
				$listHTML .= "<ul>";
			}
			if ($diff>1){
				throw new \Exception('List Items should only indent one tab');
			}
			if ($diff<0){
				for($i=0; $i>$diff; $i--){
					$listHTML .= "</li></ul>";
				}
				$listHTML .= "</li>";
			}
			if ($diff==0){
				$listHTML .= "</li>";
			}

			$listHTML .= "<li>".$this->formatAnchorTag($line[1], $prefix);

			$counter = $line[0];
		}
		while($counter>$startTab){
			$listHTML .= "</li></ul>";
			$counter--;
		}

		$listHTML .= "</li></ul>";

		return $listHTML;

	}

	public function countTags($string)
	{
		$countOpen = substr_count($string, '<ul>');
		$countClose = substr_count($string, '</ul>');

		if ($countOpen!=$countClose) {
			throw new \Exception('Menu list didn\'t close properly');
		}

	}




}
