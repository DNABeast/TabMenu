<?php
namespace DNABeast\TabMenu\Test;

use DNABeast\TabMenu\TabMenu;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;

class TabMenuTest extends TestCase
{

	public function setUp()
	{
		$this->menu = new TabMenu;
		parent::setUp();
	}

	/** @test */
	function it_should_build_a_html_list(){
		$original = '

				About us, /about-us, action
					Contact Us

				Packages, #, null
			';

		$expected = '<ul><li><a href="/about-us" class="action">About us</a><ul><li><a href="/contact-us">Contact Us</a></li></ul></li><li><a href="#" class="null">Packages</a></li></ul>';


		$this->assertEquals(
			$this->menu->build($original),
			$expected
		);
	}

	function test_it_removes_empty_lines()
	{
			$original = [
'',
'					About us, /about-us, action',
'						Contact Us',
'',
'					Packages, #, null',
'				'
];

			$expected = [
'					About us, /about-us, action',
'						Contact Us',
'					Packages, #, null'
];

			$this->assertEquals(
				$this->menu->removeEmptylines($original),
				$expected
			);

	}

	function test_it_splits_each_line_into_an_array_item()
	{
		$original = '

				About us, /about-us, action
					Contact Us

				Packages, #, null
			';
		$this->assertArrayHasKey(2, $this->menu->lines($original));

	}


	/** @test */
	function it_replaces_multiple_spaces_with_tabs(){
		Config::set('tabmenu.indent', "    ");

		$expected = '
				About us, /about-us, action
					Contact Us
				Packages, #, null
			';

		$original = '
                About us, /about-us, action
                    Contact Us
                Packages, #, null
            ';


		$this->assertContains(
			$expected,
			$this->menu->indentToTabs($original)
		);

		Config::set('tabmenu.indent', "  ");

		$original = '
        About us, /about-us, action
          Contact Us
        Packages, #, null
      ';

		$this->assertContains(
			$expected,
			$this->menu->indentToTabs($original)
		);

	}

	function test_it_separates_each_item_into_a_tab_count_and_the_details()
	{
		$original = '
				About us, /about-us, action
					Contact Us
				Packages, #, null
			';

		$this->assertContains(
			[4,'Packages, #, null'],
			$this->menu->countTabsArray($original)
		);

	}

	function test_it_formats_the_link_item_into_a_html_anchor_tag()
	{
		$original = 'About Us, /about-us, action';
		$expected = '<a href="/about-us" class="action">About Us</a>';

		$this->assertEquals(
			$this->menu->formatAnchorTag($original),
			$expected
			);
	}


	function test_it_formats_the_link_item_into_a_html_anchor_tag_with_no_href()
	{
		$original = 'About Us';
		$expected = '<a href="/about-us">About Us</a>';

		$this->assertEquals(
			$this->menu->formatAnchorTag($original),
			$expected
			);
	}

	function test_it_formats_the_link_item_into_a_html_anchor_tag_with_null()
	{
		$original = 'About Us, #, null';
		$expected = '<a href="#" class="null">About Us</a>';

		$this->assertEquals(
			$this->menu->formatAnchorTag($original),
			$expected
			);
	}

	function test_it_iterates_through_list_of_links_and_formats_based_on_tab_count()
	{
		$original = '
			About Us, /about-us, action
				Contact Us
			Packages, #, null';

		$expected = '<ul><li><a href="/about-us" class="action">About Us</a><ul><li><a href="/contact-us">Contact Us</a></li></ul></li><li><a href="#" class="null">Packages</a></li></ul>';

		$this->assertEquals(
			$this->menu->formatList($original),
			$expected
		);

	}

	function test_it_closes_all_lists_that_it_opens()
	{
		$original = '
			About Us, /about-us, action
				Contact Us
					Thusly
			Packages, #, null';

		$expected = '<ul><li><a href="/about-us" class="action">About Us</a><ul><li><a href="/contact-us">Contact Us</a><ul><li><a href="/thusly">Thusly</a></li></ul></li></ul></li><li><a href="#" class="null">Packages</a></li></ul>';

			$this->assertEquals(
				$this->menu->formatList($original),
				$expected
				);
	}

	function test_it_closes_all_lists_that_it_opens_with_hanging_items()
	{
		$original = '
			About Us, /about-us, action
				Contact Us
					Thusly
					Packages, #, null';

		$expected = '<ul><li><a href="/about-us" class="action">About Us</a><ul><li><a href="/contact-us">Contact Us</a><ul><li><a href="/thusly">Thusly</a></li><li><a href="#" class="null">Packages</a></li></ul></li></ul></li></ul>';

		$this->assertEquals(
			$this->menu->formatList($original),
			$expected
			);
	}


	/** @test */
	function it_takes_a_prefix_and_adds_it_to_all_the_local_links()
	{
		$this->get('admin');

		$original = '
			About Us, /about-us, action
			Contact Us';
		$prefix = 'admin';

		$expected = '<ul><li><a href="/admin/about-us" class="action">About Us</a></li><li><a href="/admin/contact-us">Contact Us</a></li></ul>';

		$this->assertEquals(
			$this->menu->formatList($original),
			$expected
			);
	}


	/** @test */
	function it_takes_a_prefix_from_the_config_and_adds_it_to_all_the_local_links()
	{
		$this->assertEquals(
			$this->menu->checkForPrefix(),
			null
			);

		Config::set('tabmenu.prefix', "dashboard");

		$this->assertEquals(
			$this->menu->checkForPrefix(),
			null
			);

		$this->get('dashboard');

		$this->assertEquals(
			$this->menu->checkForPrefix(),
			'dashboard'
			);
	}

	/** @test */
	function the_prefix_defaults_to_admin(){

		$this->get('admin');

		$this->assertEquals(
			$this->menu->checkForPrefix(),
			'admin'
			);
	}

	/** @test */
	function it_leaves_the_outside_list_tags_off_for_manual_use()
	{

		$this->get('admin');

		$original = '
			About Us, /about-us, action
			Contact Us';

		$expected = '<li><a href="/admin/about-us" class="action">About Us</a></li><li><a href="/admin/contact-us">Contact Us</a></li>';

		$this->assertEquals(
			$this->menu->formatList($original, true),
			$expected
			);
	}

	/** @test */
	function it_throws_an_exception_if_list_close_tags_are_less_than_list_open_tags()
	{

		$this->expectException(\Exception::class);
		$this->menu->countTags('<ul><ul></ul>');

	}


	/** @test */
	function it_throws_an_exception_if_menu_goes_up_more_than_one_tab()
	{

		$this->expectException(\Exception::class);

		$original = '
			About Us, /about-us, action
					Contact Us';

		$this->menu->formatList($original, null , true);
	}

}
