<?php
namespace Hawk\Plugins\HPageEditor;

class PageController extends Controller{

	/**
	 * Display a page
	 */
	public function display(){
		$page = App::router()->getCurrentRoute()->page;

        $content = View::makeFromString($page->content);
        return NoSidebarTab::make(array(
            'page' => $content,
            'tabTitle' => View::makeFromString($page->name),
            'title' => $page->title,
            'icon' => ''
        ));
	}
}

