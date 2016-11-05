<?php

namespace Hawk\Plugins\HPageEditor;


App::router()->prefix('/h-page-editor', function(){
    App::router()->auth(App::session()->isAllowed('h-page-editor.manage-pages'), function(){
        /**
         * Manage pages
         */
        App::router()->get('h-page-editor-manage-pages', '/admin/page-editor', array('action' => 'ManagerController.index'));

        App::router()->get('h-page-editor-list-pages', '/admin/page-editor/pages', array('action' => 'ManagerController.listPages'));

        App::router()->any('h-page-editor-edit-page', '/admin/page-editor/pages/{pageId}', array('where' => array('pageId' => '\d+'), 'action' => 'ManagerController.edit'));

        App::router()->post('h-page-editor-preview-page', '/admin/page-editor/pages/preview', array('action' => 'ManagerController.preview'));
    });
});

App::router()->prefix('/' . Option::get('h-page-editor.url-prefix') . '/', function(){
    // Display pages
    $pages = Page::getListByExample(new DBExample(array('active' => 1)), 'id');

    foreach($pages as $page){
        App::router()->get('h-page-editor-page-' . $page->id, Lang::get('h-page-editor.page-' . $page->id . '-uri'), array(
            'action' => 'PageController.display',
            'page' => $page,
            'auth' => $page->active
        ));
    }

    Event::on('after-routing', function($route){
        if($route->getName() === 'index'){
            $id = str_replace('h-page-editor-page-', '', Option::get('main.home-page-item'));
            if($id && $pages[$id]){
                $event->setData('route', App::router()->getRoutes()['h-page-editor-page-' . $pages[$id]->id]);
            }
        }
    });
});
