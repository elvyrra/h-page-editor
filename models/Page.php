<?php

namespace Hawk\Plugins\HPageEditor;

/**
 * This class describes the data Page behavior.
 *
 * @package PageEditor
 */
class Page extends Model{
    /**
     * The table containing page data
     *
     * @var string
     */
    public static $tablename = 'PageEditorPage';

    /**
     * Primary Column for the table
     *
     * @var string
     */ 
    public static $primaryColumn = 'id';

    /**
     * The table fields
     *
     * @var array
     */
    protected static $fields = array(
        'id' => array(
            'type' => 'INT(11)',
            'auto_increment' => true
        ),

        'name' => array(
            'type' => 'VARCHAR(128)',
            'NOT NULL' => true,
            'DEFAULT' => "",
        ),

        'title' => array(
            'type' => 'VARCHAR(4096)',
            'NOT NULL' => true,
            'DEFAULT' => "",
        ),

        'uri' => array(
            'type' => 'VARCHAR(256)',
            'NOT NULL' => true,
            'DEFAULT' => "",
        ),

        'content' => array(
            'type' => 'MEDIUMTEXT'
        ),

        'active' => array(
            'type' => 'TINYINT(1)',
            'NOT NULL' => true,
            'DEFAULT' => 0,
        ),

        'author' => array(
            'type' => 'INT(11)'
        ),

        'createTime' => array(
            'type' => 'INT(11)',
        ),

        'updateTime' => array(
            'type' => 'INT(11)'
        ),
    );

    /**
     * The table constraints
     */
    protected static $constraints = array(
        'name' => array(
            'type' => 'unique',
            'fields' => array(
                'name',
            ),
        ),
    );

    /**
     * URI PREFIX for user pages
     *
     * @var object
     */
    const URI_PREFIX = 'user-pages';

    /**
     * Get view directory
     *
     * @return path
     */
    public function getViewDir(){
  		return USERFILES_PLUGINS_DIR . 'page-editor/pages';
  	}

    /**
     * Get uri for this page
     *
     * @return uri
     */
    public function getUri(){
        return '/' . Option::get('page-editor.url-prefix') . '/' . Lang::get('page-editor.page-' . $this->id . '-uri');
    }

    /**
     * Get page by this name
     *
     * @return page
     */
    public static function getByName($name){
    	return self::getByExample(new DBExample(array('name' => $name)));
    }
}