<?php
namespace Hawk\Plugins\HPageEditor;

class ManagerController extends Controller{
    /**
     * Display the management page
     */
    public function index(){
        return NoSidebarTab::make(array(
            'page' => $this->listPages(),
            'tabId' => 'h-page-editor-manager',
            'title' => Lang::get($this->_plugin . '.pages-list-title'),
            'icon' => 'file-text',
        ));
    }

    /**
     * Display the list of pages
     */
    public function listPages(){
        $list = new ItemList(array(
            'id' => 'h-page-editor-pages-list',
            'action' => App::router()->getUri('h-page-editor-list-pages'),
            'model' => 'Page',
            'lineClass' => function($line){
                return $line->active ? '' : 'bg-warning';
            },
            'controls' => array(
                array(
                    'icon' => 'plus',
                    'class' => 'btn-success',
                    'label' => Lang::get($this->_plugin . '.new-page-button'),
                    'href' => App::router()->getUri('h-page-editor-edit-page', array('pageId' => 0))
                )
            ),

            'fields' => array(
                'name' => array(
                    'label' => Lang::get($this->_plugin . '.pages-list-name-label'),
                    'href' => function($value, $field, $line){
                        return App::router()->getUri('h-page-editor-edit-page', array('pageId' => $line->id));
                    }
                ),

                'uri' => array(
                    'label' => Lang::get($this->_plugin . '.pages-list-uri-label'),
                    'display' => function($value, $field, $line){
                        return $line->active ?
                            '<a href="' . $line->getUri() . '" target="newtab" >' . $line->getUri() . '</a>' :
                            $line->getUri();
                    }
                ),

                'author' => array(
                    'label' => Lang::get($this->_plugin . '.pages-list-author-label'),
                    'display' => function($value){
                        return User::getById($value)->getDisplayName();
                    },
                    'search' => false,
                ),

                'createTime' => array(
                    'label' => Lang::get($this->_plugin . '.pages-list-create-time-label'),
                    'display' => function($value){
                        return date(Lang::get('main.time-format'), $value);
                    },
                    'search' => false,
                ),

                'updateTime' => array(
                    'label' => Lang::get($this->_plugin . '.pages-list-update-time-label'),
                    'display' => function($value){
                        return date(Lang::get('main.time-format'), $value);
                    },
                    'search' => false,
                ),

                'active' => array(
                    'hidden' => true,
                )
            )
        ));

        return $list->display();
    }

    /**
     * Edit a page
     */
    public function edit(){
        $param = array(
            'id' => 'h-page-editor-page-form',
            'model' => 'Page',
            'reference' => array('id' => $this->pageId),
            'fieldsets' => array(
                'global' => array(
                    new HtmlInput(array(
                        'name' => 'description',
                        'value' =>
                            '<div class="alert alert-info">' .
                                Icon::make(array(
                                    'icon' => 'exclamation-circle'
                                )) .
                                Lang::get($this->_plugin . '.page-form-description') .
                            '</div>',
                    )),

                    new TextInput(array(
                        'name' => 'name',
                        'required' => true,
                        'maxlength' => 128,
                        'label' => Lang::get($this->_plugin . '.page-form-name-label'),
                        'attributes' => array(
                            'ko-value' => 'name'
                        )
                    )),

                    new TextInput(array(
                        'name' => 'title',
                        'required' => true,
                        'mawkength' => 4096,
                        'label' => Lang::get($this->_plugin . '.page-form-title-label'),
                        'attributes' => array(
                            'ko-value' => 'title'
                        )
                    )),

                    new TextInput(array(
                        'name' => 'uri',
                        'required' => true,
                        'unique' => true,
                        'label' => Lang::get($this->_plugin . '.page-form-uri-label', array(
                            'rooturl' => App::conf()->get('rooturl') . '/' . Option::get($this->_plugin . '.url-prefix') . '/'
                        )),
                        'labelWidth' => 'auto',
                        'size' => '50',
                        'attributes' => array(
                            'ko-value' => 'uri'
                        )
                    )),

                    new CheckboxInput(array(
                        'name' => 'active',
                        'label' => Lang::get($this->_plugin . '.page-form-active-label')
                    )),

                    new WysiwygInput(array(
                        'name' => 'content',
                        'label' => lang::get($this->_plugin . '.page-form-content-label'),
                        'attributes' => array(
                            'ko-value' => 'content'
                        )
                    )),

                    new HiddenInput(array(
                        'name' => 'author',
                        'default' => App::session()->getUser()->id
                    )),

                    new HiddenInput(array(
                        'name' => 'createTime',
                        'default' => time(),
                    )),

                    new HiddenInput(array(
                        'name' => 'updateTime',
                        'value' => time(),
                    )),
                ),

                'submits' => array(
                    new SubmitInput(array(
                        'name' => 'valid',
                        'value' => Lang::get('main.valid-button')
                    )),

                    new ButtonInput(array(
                        'name' => 'preview',
                        'value' => Lang::get($this->_plugin . '.page-form-preview-button'),
                        'attributes' => array(
                            'ko-click' => 'preview'
                         ),
                        'icon' => 'eye',
                        'class' => 'btn-primary',
                    )),

                    new DeleteInput(array(
                        'name' => 'delete',
                        'value' => Lang::get('main.delete-button'),
                        'notDisplayed' => !$this->pageId,
                    )),

                    new ButtonInput(array(
                        'name' => 'cancel',
                        'value' => Lang::get('main.cancel-button'),
                        'onclick' => 'app.load(app.getUri("h-page-editor-manage-pages"));'
                    ))
                )
            ),
        );

        $form = new Form($param);

        if(!$form->submitted()){
            $this->addJavaScript($this->getPlugin()->getJsUrl('manager.js'));

            return NoSidebarTab::make(array(
                'tabId' => 'h-page-editor-manager',
                'icon' => 'file-text',
                'title' => Lang::get($this->_plugin . '.page-form-title'),
                'page' => $form
            ));
        }
        else{
            if($form->submitted() == "delete"){
                try{
                    $form->delete(Form::NO_EXIT);

                    $item = MenuItem::getByName($this->_plugin . '.page-' . $this->pageId);
                    if($item){
                        $item->delete();
                    }

                    foreach(Language::getAll() as $language){
                        $language->removeTranslations(array(
                            $this->_plugin => array(
                                'page-' . $this->pageId . '-title',
                                'page-' . $this->pageId . '-uri',
                            )
                        ));
                    }

                    return $form->response(Form::STATUS_SUCCESS, Lang::get($this->_plugin . '.delete-page-success'));
                }
                catch(Exception $e){
                    return $form->response(Form::STATUS_ERROR, Lang::get($this->_plugin . '.delete-page-error'));
                }
            }
            else{
                if($form->check()){
                    try{
                        $pageId = $form->register(Form::NO_EXIT);

                        foreach(array('title', 'uri') as $field){
                            $regex = '#\{([a-z]{2})\}(.*?)\{/\\1\}#';
                            if(!preg_match($regex, $form->getData($field))){
                                $form->setData(array(  //set
                                    $field => '{' . Lang::DEFAULT_LANGUAGE . '}' . $form->getData($field) . '{/' . Lang::DEFAULT_LANGUAGE . '}'
                                ));
                            }

                            preg_match_all('#\{([a-z]{2})\}(.*?)\{/\\1\}#', $form->getData($field), $matches, PREG_SET_ORDER);
                            foreach($matches as $match){
                                Language::getByTag($match[1])->saveTranslations(array(
                                    $this->_plugin => array(
                                        'page-' . $pageId . '-' . $field => $match[2]
                                    )
                                ));
                            }
                        }

                        $item = MenuItem::getByName($this->_plugin . '.page-' . $pageId);
                        if($form->getData('active')){
                            if(!$item){
                                MenuItem::add(array(
                                    'plugin' => $this->_plugin,
                                    'name' => 'page-' . $pageId,
                                    'action' => 'h-page-editor-page-' . $pageId,
                                    'labelKey' => $this->_plugin . '.page-' . $pageId . '-title',
                                    'active' => 0
                                ));
                            }
                        }
                        else{
                            if($item){
                                $item->delete();
                            }
                        }

                        return $form->response(Form::STATUS_SUCCESS, Lang::get($this->_plugin . '.register-page-success'));
                    }
                    catch(Exception $e){
                        return $form->response(Form::STATUS_ERROR, Lang::get($this->_plugin . '.register-page-error'));
                    }
                }
            }
        }
    }


    /**
     * Preview the result of a page
     */
    public function preview(){
        $body = App::request()->getBody();
        $content = $body['content'];

        $page = View::makeFromString($content);
        return NoSidebarTab::make(array(
            'page' => $page,
            'tabTitle' => Lang::get($this->_plugin . '.page-preview-title', array('page' => $body['name'])),
            'title' => '',
            'icon' => 'eye'
        ));
    }
}