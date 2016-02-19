<?php

class MenuTest extends PHPUnit_Framework_TestCase
{


	function test_it_removes_empty_lines()
	{
			$menu = new Typesaucer\MenuTabber\Menu;
			$original = '

					About us, /about-us, action
						Contact Us

					Packages, #, null
				';
			$expected = '					About us, /about-us, action
						Contact Us
					Packages, #, null';

			$this->assertEquals(
				$menu->removeEmptylines($original),
				$expected
			);
	}

	function test_it_splits_each_line_into_an_array_item()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = '

				About us, /about-us, action
					Contact Us

				Packages, #, null
			';

		$this->assertArrayHasKey(2, $menu->explodeString($original));

	}

	function test_it_separates_each_item_into_a_tab_count_and_the_details()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = '
				About us, /about-us, action
					Contact Us
				Packages, #, null
			';

		$this->assertContains(
			[4,'Packages, #, null'],
			$menu->countTabsArray($original)
		);

	}

	function test_it_formats_the_link_item_into_a_html_anchor_tag()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = 'About Us, /about-us, action';
		$expected = '<a href="/about-us" class="action">About Us</a>';

		$this->assertEquals(
			$menu->formatAnchorTag($original),
			$expected
			);
	}

	function test_it_formats_the_link_item_into_a_html_anchor_tag_with_no_href()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = 'About Us';
		$expected = '<a href="/about-us">About Us</a>';

		$this->assertEquals(
			$menu->formatAnchorTag($original),
			$expected
			);
	}

	function test_it_formats_the_link_item_into_a_html_anchor_tag_with_null()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = 'About Us, #, null';
		$expected = '<a href="#" class="null">About Us</a>';

		$this->assertEquals(
			$menu->formatAnchorTag($original),
			$expected
			);
	}

	function test_it_iterates_through_list_of_links_and_formats_based_on_tab_count()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = '
			About Us, /about-us, action
				Contact Us
			Packages, #, null';

		$expected = '<ul><li><a href="/about-us" class="action">About Us</a><ul><li><a href="/contact-us">Contact Us</a></li></ul></li><li><a href="#" class="null">Packages</a></li></ul>';

		$this->assertEquals(
			$menu->formatList($original),
			$expected
		);

	}

	function test_it_closes_all_lists_that_it_opens()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = '
			About Us, /about-us, action
				Contact Us
					Thusly
			Packages, #, null';

		$expected = '<ul><li><a href="/about-us" class="action">About Us</a><ul><li><a href="/contact-us">Contact Us</a><ul><li><a href="/thusly">Thusly</a></li></ul></li></ul></li><li><a href="#" class="null">Packages</a></li></ul>';

			$this->assertEquals(
				$menu->formatList($original),
				$expected
				);
	}

	function test_it_closes_all_lists_that_it_opens_with_hanging_items()
	{
		$menu = new Typesaucer\MenuTabber\Menu;

		$original = '
			About Us, /about-us, action
				Contact Us
					Thusly
					Packages, #, null';

		$expected = '<ul><li><a href="/about-us" class="action">About Us</a><ul><li><a href="/contact-us">Contact Us</a><ul><li><a href="/thusly">Thusly</a></li><li><a href="#" class="null">Packages</a></li></ul></li></ul></li></ul>';

		$this->assertEquals(
			$menu->formatList($original),
			$expected
			);
	}


    // function it_throws_an_exception_if_list_close_tags_are_less_than_list_open_tags()
    // {
    // 	$menu = new Typesaucer\MenuTabber\Menu;


    // 	try {
	   //  	$menu->countTags('<ul><ul></ul>');
    // 	} catch (\Exception $e) {
    // 	    $this->assertEquals($e->getCode(), 100);
    // 	    return;
    // 	}

    // 	$this->fail();
    // }


 //    function it_throws_an_exception_if_menu_goes_up_more_than_one_tab()
 //    {
 //    	$original = '
 //    		About Us, /about-us, action
 //    				Contact Us';

 //    	$this->shouldThrow('\Exception')->duringFormatList($original);
 //    }

}