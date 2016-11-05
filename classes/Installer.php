<?php
/**
 * Installer.class.php
 */

namespace Hawk\Plugins\HPageEditor;

/**
 * This class describes the behavio of the installer for the plugin page-editor
 */
class Installer extends PluginInstaller{
    const PLUGIN_NAME = 'h-page-editor';
    /**
     * Install the plugin. This method is called on plugin installation, after the plugin has been inserted in the database
     */
    public function install(){
        Page::createTable();

        Permission::add($this->_plugin . '.manage-pages', 0, 0);
    }

    /**
     * Uninstall the plugin. This method is called on plugin uninstallation, after it has been removed from the database
     */
    public function uninstall(){
        Page::dropTable();

        $permissions = Permission::getPluginPermissions($this->_plugin);
        foreach($permissions as $permission){
            $permission->delete();
        }
    }

    /**
     * Activate the plugin. This method is called when the plugin is activated, just after the activation in the database
     */
    public function activate(){
        MenuItem::add(array(
            'plugin' => $this->_plugin,
            'name' => 'h-page-editor-manage',
            'labelKey' => $this->_plugin . '.manage-pages-menu-title',
            'action' => 'h-page-editor-manage-pages',
            'parentId' => MenuItem::ADMIN_ITEM_ID,
            'icon' => 'pagelines'
        ));

        Option::set($this->_plugin . '.url-prefix', Page::URI_PREFIX);
    }

    /**
     * Deactivate the plugin. This method is called when the plugin is deactivated, just after the deactivation in the database
     */
    public function deactivate(){
        $menus = MenuItem::getPluginMenuItems($this->_plugin);
        foreach($menus as $menu){
            $menu->delete();
        }
    }

    /**
     * Configure the plugin. This method contains a page that display the plugin configuration. To treat the submission of the configuration
     * you'll have to create another method, and make a route which action is this method. Uncomment the following function only if your plugin if
     * configurable.
     */
    public function settings(){
        $form = new Form(array(
            'id' => 'h-page-editor-settings-form',
            'inputs' => array(
                new TextInput(array(
                    'name' => 'url-prefix',
                    'required' => true,
                    'default' => Option::get($this->_plugin . '.url-prefix'),
                    'label' => Lang::get($this->_plugin . '.settings-url-prefix-label'),
                    'pattern' => '/^\w[\w\/\-]+\w$/'
                )),

                new SubmitInput(array(
                    'name' => 'valid',
                    'value' => Lang::get('main.valid-button'),
                    'nl' => true
                )),
            ),
            'onsuccess' => 'app.dialog("close")'
        ));

        if(!$form->submitted()) {
            return Dialogbox::make(array(
                'title' => Lang::get($this->_plugin . '.settings-title'),
                'icon' => 'cogs',
                'page' => $form
            ));
        }
        else {
            if($form->check()) {
                Option::set($this->_plugin . '.url-prefix', $form->getData('url-prefix'));

                return $form->response(Form::STATUS_SUCCESS);
            }
        }
    }
}
