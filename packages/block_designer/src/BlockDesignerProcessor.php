<?php namespace RamonLeenders\BlockDesigner;

defined('C5_EXECUTE') or die("Access Denied.");

use Package;
use Config;
use Concrete\Core\File\Service\File;
use Zend\Code\Generator\ValueGenerator;

class BlockDesignerProcessor
{
    protected static $packageHandle = 'block_designer';
    protected static $errors = [];
    protected static $packages = [];
    protected static $fieldTypeCounts = [];
    protected static $fieldSlugsBlacklist = ['limit', 'description', 'select', 'file', 'bid', 'bttable', 'helpers', 'btfieldsrequired', 'btinterfacewidth', 'btinterfaceheight', 'btcacheblockrecord', 'btcacheblockoutput', 'btcacheblockoutputonpost', 'btcacheblockoutputforregisteredusers', 'btcacheblockoutputlifetime', 'bthandle', 'btname', 'btexportpagecolumns', 'btexportfilecolumns', 'btexportpagetypecolumns', 'btexportpagefeedcolumns', 'btwrapperclass', 'btignorepagethemegridframeworkcontainer', 'pkg', 'asc', 'desc', 'option', 'fulltext', 'bigint', 'int', 'smallint', 'tinyint', 'bit', 'decimal', 'numeric', 'float', 'real', 'smalldatetime', 'char', 'varchar', 'nchar', 'nvarchar', 'ntext', 'binary', 'varbinary', 'column', 'condition', 'double'];
    protected static $pkgVersionsRequired = [
	    'block_designer_pro' => '1.0.2'
    ];
	protected static $variables = [];

	public static function setFieldTypeVariable($ftHandle, $key, $value){
		self::$variables[$ftHandle][$key] = $value;
	}

	public static function getFieldTypeVariable($ftHandle, $key){
		return self::$variables[$ftHandle][$key];
	}

    public function getPackageFolder($pkgHandle)
    {
        return 'packages' . DIRECTORY_SEPARATOR . $pkgHandle . DIRECTORY_SEPARATOR;
    }

    public static function getBlocksFolder()
    {
        return 'application' . DIRECTORY_SEPARATOR . 'blocks';
    }

    public static function getBlockTypeFolder($btHandle = null)
    {
        return self::getBlocksFolder() . DIRECTORY_SEPARATOR . $btHandle;
    }

    public static function getFieldTypes()
    {
        $results = [];
        $pkgBd = Package::getByHandle(self::$packageHandle);
        $pkgBdVersion = $pkgBd->getPackageVersion();
        $packages = [self::$packageHandle, 'block_designer_pro'];
        foreach ($packages as $packageHandle) {
            if (($pkg = Package::getByHandle($packageHandle)) && $pkg->isPackageInstalled() == '1') {
                self::$packages[$packageHandle] = [
	                'version' => $pkg->getPackageVersion(),
                ];
                $pkgDirectory = self::getPackageFolder($packageHandle);
                $directory = $pkgDirectory . 'src' . DIRECTORY_SEPARATOR . 'FieldType';
                $folders = self::getDirList($directory);
                if (array_key_exists($packageHandle, self::$pkgVersionsRequired)) {
                    $neededVersion = self::$pkgVersionsRequired[$packageHandle];
                    if (!version_compare($pkg->getPackageVersion(), $neededVersion, '>=')) {
                        continue;
                    }
                }
                if (!empty($folders)) {
                    foreach ($folders as $folder) {
                        $ftDirectory = $directory . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
                        $ftClassName = self::_nameSpace($folder);
                        $ftNamespace = '\\RamonLeenders\\' . self::_namespace($packageHandle) . '\\FieldType\\' . $ftClassName . '\\' . $ftClassName;
                        /* @var $ftClass \RamonLeenders\BlockDesigner\FieldType\FieldType */
                        if (class_exists($ftNamespace)) {
                            $ftClass = new $ftNamespace($ftDirectory, $packageHandle, $pkgDirectory, $ftClassName);
                            $pkgVersionRequired = $ftClass->getPkgVersionRequired();
                            $appVersionRequired = $ftClass->getAppVersionRequired();
                            if (($pkgVersionRequired === false || version_compare($pkgVersionRequired, $pkgBdVersion, '<=')) && ($appVersionRequired === false || version_compare($appVersionRequired, self::getConcreteVersionInstalled(), '<='))) {
                                $iconFile = $ftDirectory . 'icon.png';
                                $icon = file_exists($iconFile) ? $iconFile : self::getPackageFolder($packageHandle) . 'img' . DIRECTORY_SEPARATOR . 'icon.png';
                                $results[$ftClass->getHandle()] = [
	                                'icon'         => DIR_REL . DIRECTORY_SEPARATOR . $icon,
	                                'name'         => $ftClass->getFieldName(),
	                                'description'  => $ftClass->getFieldDescription(),
	                                'namespace'    => $ftNamespace,
	                                'directory'    => $ftDirectory,
	                                'pkgHandle'    => $packageHandle,
	                                'pkgDirectory' => $pkgDirectory,
	                                'class'        => $ftClass,
	                                'className'    => $ftClassName,
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $results;
    }

    private static function getConcreteVersionInstalled()
    {
        return Config::get('concrete.version_installed');
    }

    private static function _nameSpace($name)
    {
        $nameSpaced = implode('', array_map(function ($v, $k) {
            return ucfirst($v);
        }, explode('_', $name), array_keys(explode('_', $name))));
        return $nameSpaced;
    }

    private static function getDirList($d)
    {
        foreach (array_diff(scandir($d), ['.', '..']) as $f) if (is_dir($d . '/' . $f)) $l[] = $f;
        return $l;
    }

    public static function runErrors()
    {
        return self::$errors;
    }

    public static function blockHandle($blockHandle = null)
    {
        $blockHandle = strtolower(str_replace('-', '_', $blockHandle));
        return preg_replace('~_+~', '_', $blockHandle); // replacing multiple underscores with just 1
    }

    public static function getFieldSlugsBlacklist()
    {
        return self::$fieldSlugsBlacklist;
    }

    public static function getFieldPrefix($value = [])
    {
        return PHP_EOL . '    ' . (isset($value['prefix']) && trim($value['prefix']) != '' ? $value['prefix'] : null);
    }

    public static function getFieldSuffix($value = [])
    {
        return isset($value['suffix']) && trim($value['suffix']) != '' ? $value['suffix'] : null;
    }

    public static function updateFieldTypeCount($type, $repeatable = false)
    {
        if (!isset(self::$fieldTypeCounts[$type])) {
            self::$fieldTypeCounts[$type] = [
	            'normal'     => 0,
	            'repeatable' => 0,
            ];
        }
        $key = $repeatable ? 'repeatable' : 'normal';
        self::$fieldTypeCounts[$type][$key]++;
    }

    public static function getFieldTypeCount($type, $repeatable = false)
    {
        $key = $repeatable ? 'repeatable' : 'normal';
        return isset(self::$fieldTypeCounts[$type]) ? self::$fieldTypeCounts[$type][$key] : 0;
    }

    public static function run($postData = [])
    {
        $autoCss = null;
        $autoJs = null;
        $extraFunction = [];
        $viewFields = ['<?php defined("C5_EXECUTE") or die("Access Denied."); ?>'];
        $formFields = ['<?php defined("C5_EXECUTE") or die("Access Denied."); ?>'];
        $tabs = [['form-basics', 'Basics']];
        $tabsFields = ['form-basics' => []];
        $dbTables = [];
        $fieldSlugs = [];
        $copyFiles = [];
        $assets = [];
        $assetsKeys = [];
        $controllerVariablesArray = [];
        $dbFields = [
	        [
		        'name'       => 'bID',
		        'type'       => 'I',
		        'attributes' => [
			        'key'      => true,
			        'unsigned' => true,
		        ]	    
	        ],
        ];
        $fieldTypes = self::getFieldTypes();
        $blockHandle = self::blockHandle($postData['block_handle']);
        $blockHandleNamespaced = self::_nameSpace($blockHandle);
        $blockTypeFolder = self::getBlockTypeFolder($postData['block_handle']);
        $btTable = 'bt' . (isset($postData['table_prefix']) && trim($postData['table_prefix']) != '' ? $postData['table_prefix'] : null) . $blockHandleNamespaced;
        $controllerUsesArray = ['Concrete\Core\Block\BlockController', 'Core'];
        $controllerVariables = [
	        'helpers'             => [
		        'type'               => 'public',
		        'value'              => [],
		        'array_strip'        => true,
		        'array_empty_remove' => true,
	        ],
	        'btFieldsRequired'    => [
		        'type'        => 'public',
		        'value'       => [],
		        'array_strip' => true,
	        ],
	        'btExportFileColumns' => [
		        'type'               => 'protected',
		        'value'              => [],
		        'array_strip'        => true,
		        'array_empty_remove' => true,
	        ],
	        'btExportPageColumns' => [
		        'type'               => 'protected',
		        'value'              => [],
		        'array_strip'        => true,
		        'array_empty_remove' => true,
	        ],
	        'btExportTables'                          => [
		        'type'        => 'protected',
		        'value'       => [$btTable],
		        'array_strip' => true,
	        ],
	        'btTable'                                 => [
		        'type'  => 'protected',
		        'value' => $btTable,
	        ],
	        'btInterfaceWidth'                        => [
		        'type'  => 'protected',
		        'value' => (int)$postData['interface_width'],
	        ],
	        'btInterfaceHeight'                       => [
		        'type'  => 'protected',
		        'value' => (int)$postData['interface_height'],
	        ],
	        'btIgnorePageThemeGridFrameworkContainer' => [
		        'type'  => 'protected',
		        'value' => isset($postData['ignore_page_theme_grid_framework_container']) && $postData['ignore_page_theme_grid_framework_container'] == 1 ? true : false,
	        ],
	        'btCacheBlockRecord'                      => [
		        'type'  => 'protected',
		        'value' => isset($postData['cache_block_record']) && $postData['cache_block_record'] == 0 ? false : true,
	        ],
	        'btCacheBlockOutput'                      => [
		        'type'  => 'protected',
		        'value' => isset($postData['cache_block_output']) && $postData['cache_block_output'] == 0 ? false : true,
	        ],
	        'btCacheBlockOutputOnPost'                => [
		        'type'  => 'protected',
		        'value' => isset($postData['cache_block_output_on_post']) && $postData['cache_block_output_on_post'] == 0 ? false : true,
	        ],
	        'btCacheBlockOutputForRegisteredUsers'    => [
		        'type'  => 'protected',
		        'value' => isset($postData['cache_block_output_for_registered_users']) && $postData['cache_block_output_for_registered_users'] == 0 ? false : true,
	        ],
        ];
        $controllerFunctionsArray = [];
        $controllerFunctions = [
	        'getBlockTypeDescription' => [
		        'type'  => 'public',
		        'lines' => [],
	        ],
	        'getBlockTypeName'        => [
		        'type'  => 'public',
		        'lines' => [
			        'return t("' . h($postData['block_name']) . '");',
		        ],
	        ],
	        'getSearchableContent'    => [
		        'type'  => 'public',
		        'lines' => [],
	        ],
	        'on_start'                => [
		        'type'  => 'public',
		        'lines' => [],
	        ],
	        'view'                    => [
		        'type'  => 'public',
		        'lines' => [],
	        ],
	        'delete'                  => [
		        'type'  => 'public',
		        'lines' => [],
	        ],
	        'duplicate'               => [
		        'type'      => 'public',
		        'lines'     => [],
		        'variables' => ['$newBID'],
	        ],
	        'add'                     => [
		        'type'  => 'public',
		        'lines' => ['$this->addEdit();'],
	        ],
	        'edit'                    => [
		        'type'  => 'public',
		        'lines' => ['$this->addEdit();'],
	        ],
	        'addEdit'                 => [
		        'type'  => 'protected',
		        'lines' => [],
	        ],
	        'save'                    => [
		        'type'      => 'public',
		        'lines'     => [],
		        'variables' => ['$args'],
	        ],
	        'validate'                => [
		        'type'      => 'public',
		        'lines'     => [],
		        'variables' => ['$args'],
	        ],
	        'composer'                => [
		        'type'  => 'public',
		        'lines' => [],
	        ],
        ];
	    if (isset($postData['block_description']) && trim($postData['block_description']) != '') {
		    $controllerFunctions['getBlockTypeDescription']['lines'][] = 'return t("' . h($postData['block_description']) . '");';
	    }
        if (isset($postData['cache_block_output_lifetime']) && trim($postData['cache_block_output_lifetime']) != '' && $postData['cache_block_output_lifetime'] >= 0) {
            $controllerVariables['btCacheBlockOutputLifetime'] = [
	            'type'  => 'protected',
	            'value' => (int)$postData['cache_block_output_lifetime'],
            ];
        }
        if (isset($postData['default_set']) && is_string($postData['default_set']) && trim($postData['default_set']) != '') {
            $controllerVariables['btDefaultSet'] = [
	            'type'  => 'protected',
	            'value' => $postData['default_set'],
            ];
        }
        $controllerVariables['pkg'] = [
	        'type'  => 'protected',
	        'value' => false,
        ];
        foreach ($postData['fields'] as $key => $value) {
            if (isset($value['type'])) {
                if (array_key_exists($value['type'], $fieldTypes)) {
                    $fieldType = $fieldTypes[$value['type']];
                    /* @var $fieldTypeClass \RamonLeenders\BlockDesigner\FieldType\FieldType */
                    $fieldTypeClass = new $fieldType['namespace']($fieldType['directory'], $fieldType['pkgHandle'], $fieldType['pkgDirectory'], $fieldType['className']);
                    $fieldRepeating = isset($value['repeatable']) && trim($value['repeatable']) != '' && $fieldTypeClass->getCanRepeat() === true && isset($postData['fields'][$value['repeatable']]) && array_key_exists('repeatable', $fieldTypes) ? true : false;
                    if (!$fieldRepeating) {
                        $required = isset($value['required']) && is_string($value['required']) && $value['required'] == '1' ? true : false;
	                    $fieldData = array_merge($value, [
			                    'row_id'              => $key,
			                    'required'            => $required,
			                    'prefix'              => self::getFieldPrefix($value),
			                    'suffix'              => self::getFieldSuffix($value),
			                    'label'               => $value['label'],
			                    'ft_count'            => self::getFieldTypeCount($value['type']),
			                    'ft_count_repeatable' => self::getFieldTypeCount($value['type'], true),
			                    'btDirectory'         => $blockTypeFolder . DIRECTORY_SEPARATOR,
			                    'btTable'             => $btTable,
			                    'block_handle'        => $blockHandle,
		                    ]
	                    );
                        if (method_exists($fieldTypeClass, 'on_start')) {
                            $fieldTypeClass->on_start($fieldData);
                        }
                        if ($fieldTypeClass->getRequiredSlug() === true) {
                            if (isset($value['slug']) && trim($value['slug']) != '') {
                                // Being sure we have a non-existing slug for the field
                                $slug_num = 1;
                                $slug = $value['slug'];
                                while (in_array($slug, $fieldSlugs) || in_array(strtolower($slug), self::$fieldSlugsBlacklist)) {
                                    $slug = $value['slug'] . '_' . $slug_num;
                                    $slug_num++;
                                }
                                $fieldSlugs[] = $slug;
                            } else {
                                self::$errors[] = t('There was no slug found for row #%s. Please try again.', $key);
                                break;
                            }
                        } else {
                            $slug = false;
                        }
                        if ($required && $slug) {
                            $controllerVariables['btFieldsRequired']['value'][] = $slug;
                        }
                        $fieldData['slug'] = $slug;
                        $fieldTypeClass->setPostData($postData);
                        $fieldTypeClass->setData($fieldData);
                        $continue = true;
                        if (method_exists($fieldTypeClass, 'validate') && ($validateResult = $fieldTypeClass->validate()) !== true) {
                            self::$errors[] = $validateResult;
                            $continue = false;
                        }
                        if ($continue) {
                            if (method_exists($fieldTypeClass, 'getBtExportTables')) {
                                if (($result = $fieldTypeClass->getBtExportTables()) && is_array($result)) {
                                    $controllerVariables['btExportTables']['value'] = array_merge($controllerVariables['btExportTables']['value'], $result);
                                }
                            }
	                        $classMethods = [
		                        [
			                        'method'   => 'getViewContents',
			                        'variable' => 'viewFields',
		                        ],
		                        [
			                        'method'   => 'getExtraFunctionsContents',
			                        'variable' => 'extraFunction',
		                        ],
                            ];
                            foreach ($classMethods as $classMethod) {
                                if (method_exists($fieldTypeClass, $classMethod['method'])) {
                                    if ($result = $fieldTypeClass->{$classMethod['method']}()) {
                                        ${$classMethod['variable']}[] = $result;
                                    }
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getTabs')) {
                                if (($result = $fieldTypeClass->getTabs()) && is_array($result) && !empty($result)) {
                                    $tabs = array_merge($tabs, $result);
                                    foreach ($result as $v) {
                                        $tabsFields[$v[0]] = [];
                                    }
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getFormContents')) {
                                if ($result = $fieldTypeClass->getFormContents()) {
                                    $tabsKey = ($tab = $fieldTypeClass->getTabKey()) ? $tab : 'form-basics';
                                    $tabsFields[$tabsKey][] = $result;
                                }
                            }
	                        $classMethods = [
		                        [
			                        'method'   => 'getOnStartFunctionContents',
			                        'variable' => 'on_start',
		                        ],
		                        [
			                        'method'   => 'getValidateFunctionContents',
			                        'variable' => 'validate',
		                        ],
		                        [
			                        'method'   => 'getAddFunctionContents',
			                        'variable' => 'add',
		                        ],
		                        [
			                        'method'   => 'getEditFunctionContents',
			                        'variable' => 'edit',
		                        ],
		                        [
			                        'method'   => 'getAddEditFunctionContents',
			                        'variable' => 'addEdit',
		                        ],
		                        [
			                        'method'   => 'getDeleteFunctionContents',
			                        'variable' => 'delete',
		                        ],
		                        [
			                        'method'   => 'getDuplicateFunctionContents',
			                        'variable' => 'duplicate',
		                        ],
		                        [
			                        'method'   => 'getViewFunctionContents',
			                        'variable' => 'view',
		                        ],
		                        [
			                        'method'   => 'getSaveFunctionContents',
			                        'variable' => 'save',
		                        ],
		                        [
			                        'method'   => 'getSearchableContent',
			                        'variable' => 'getSearchableContent',
		                        ],
	                        ];
                            foreach ($classMethods as $classMethod) {
                                if (method_exists($fieldTypeClass, $classMethod['method'])) {
                                    if ($result = $fieldTypeClass->{$classMethod['method']}()) {
                                        if (!isset($controllerFunctions[$classMethod['variable']])) {
                                            $controllerFunctions[$classMethod['variable']] = [
	                                            'type'  => 'public',
	                                            'lines' => [],
                                            ];
                                        }
                                        $controllerFunctions[$classMethod['variable']]['lines'][] = $result;
                                    }
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getAutoJsContents')) {
                                if (($result = $fieldTypeClass->getAutoJsContents()) && trim($result) != '') {
                                    $autoJs .= $result;
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getAutoCssContents')) {
                                if (($result = $fieldTypeClass->getAutoCssContents()) && trim($result) != '') {
                                    $autoCss .= $result;
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getDbFields')) {
                                if (($result = $fieldTypeClass->getDbFields()) && is_array($result) && !empty($result)) {
                                    $dbFields = array_merge($dbFields, $result);
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getDbTables')) {
                                if (($result = $fieldTypeClass->getDbTables()) && is_array($result) && !empty($result)) {
                                    $dbTables = array_merge($dbTables, $result);
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getFieldsRequired')) {
                                if (($result = $fieldTypeClass->getFieldsRequired()) && is_array($result) && !empty($result)) {
                                    $controllerVariables['btFieldsRequired']['value'] = array_merge($controllerVariables['btFieldsRequired']['value'], $result);
                                }
                            }
	                        if (method_exists($fieldTypeClass, 'getBtExportFileColumn')) {
		                        if (($result = $fieldTypeClass->getBtExportFileColumn()) && is_array($result) && !empty($result)) {
			                        $controllerVariables['btExportFileColumns']['value'] = array_merge($controllerVariables['btExportFileColumns']['value'], $result);
		                        }
                            }
	                        if (method_exists($fieldTypeClass, 'getBtExportPageColumn')) {
		                        if (($result = $fieldTypeClass->getBtExportPageColumn()) && is_array($result) && !empty($result)) {
			                        $controllerVariables['btExportPageColumns']['value'] = array_merge($controllerVariables['btExportPageColumns']['value'], $result);
		                        }
                            }
                            if (method_exists($fieldTypeClass, 'copyFiles')) {
                                if (($result = $fieldTypeClass->copyFiles()) && is_array($result) && !empty($result)) {
                                    $copyFiles = array_merge($copyFiles, $result);
                                }
                            }
                            if (method_exists($fieldTypeClass, 'getAssets')) {
                                if (($ftAssets = $fieldTypeClass->getAssets()) && is_array($ftAssets) && !empty($ftAssets)) {
                                    foreach ($ftAssets as $ftAssetFunctionName => $ftAssetArray) {
                                        if (isset($controllerFunctions[$ftAssetFunctionName])) {
                                            if (!isset($assets[$ftAssetFunctionName])) {
                                                $assets[$ftAssetFunctionName] = [];
                                            }
                                            foreach ($ftAssetArray as $ftAssetEntriesType => $ftAssetEntries) {
                                                // We only have these 2 functionalities with assets
                                                if (in_array($ftAssetEntriesType, ['require', 'register'])) {
                                                    if (!isset($assets[$ftAssetFunctionName][$ftAssetEntriesType])) {
                                                        $assets[$ftAssetFunctionName][$ftAssetEntriesType] = [];
                                                    }
                                                    foreach ($ftAssetEntries as $ftAssetEntry) {
                                                        $ftAddAsset = false;
                                                        if (isset($ftAssetEntry['type'])) {
                                                            if (!is_string($ftAssetEntry['type']) || trim($ftAssetEntry['type']) == '' || !in_array($ftAssetEntry['type'], ['css', 'javascript'])) {
                                                                unset($ftAssetEntry['type']);
                                                            }
                                                        }
                                                        if (isset($ftAssetEntry['handle'])) {
                                                            if (!is_string($ftAssetEntry['handle']) || trim($ftAssetEntry['handle']) == '') {
                                                                unset($ftAssetEntry['handle']);
                                                            }
                                                        }
                                                        switch ($ftAssetEntriesType) {
                                                            case 'require':
                                                                if (isset($ftAssetEntry['handle'])) {
                                                                    $ftAddAsset = true;
                                                                }
                                                                break;
                                                            case 'register':
                                                                if (isset($ftAssetEntry['filename'])) {
                                                                    if (!is_string($ftAssetEntry['filename']) || trim($ftAssetEntry['filename']) == '') {
                                                                        unset($ftAssetEntry['filename']);
                                                                    }
                                                                }
                                                                if (isset($ftAssetEntry['args'])) {
                                                                    if (!is_array($ftAssetEntry['args'])) {
                                                                        unset($ftAssetEntry['args']);
                                                                    }
                                                                }
                                                                if (isset($ftAssetEntry['type'], $ftAssetEntry['handle'], $ftAssetEntry['filename'])) {
                                                                    $ftAddAsset = true;
                                                                }
                                                                break;
                                                        }
                                                        if ($ftAddAsset) {
                                                            $ftAssetEntryKey = md5($ftAssetFunctionName . $ftAssetEntriesType . json_encode($ftAssetEntry));
                                                            if (!isset($assetsKeys[$ftAssetEntryKey])) {
                                                                $assets[$ftAssetFunctionName][$ftAssetEntriesType][] = $ftAssetEntry;
                                                                $assetsKeys[$ftAssetEntryKey] = true;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (($helpers = $fieldTypeClass->getHelpers()) && is_array($helpers) && !empty($helpers)) {
                                foreach ($helpers as $helper) {
                                    if (!in_array($helper, $controllerVariables['helpers']['value'])) {
                                        $controllerVariables['helpers']['value'][] = $helper;
                                    }
                                }
                            }
                            if (($uses = $fieldTypeClass->getUses()) && is_array($uses) && !empty($uses)) {
                                foreach ($uses as $use) {
                                    if (!in_array($use, $controllerUsesArray)) {
                                        $controllerUsesArray[] = $use;
                                    }
                                }
                            }
                            self::updateFieldTypeCount($value['type']);
                        }
                    }
                } else {
                    self:: $errors[] = t("An unknown field type was posted. Please try again.");
                    break;
                }
            } else {
                unset($postData['fields'][$key]);
            }
        }
        if (!empty(self::$errors)) {
            return false;
        } else {
            if (trim($autoJs) != '') {
                if (!isset($assets['composer'])) {
                    $assets['composer'] = [];
                }
                if (!isset($assets['composer']['register'])) {
                    $assets['composer']['register'] = [];
                }
                if (!isset($assets['composer']['require'])) {
                    $assets['composer']['require'] = [];
                }
                $assets['composer']['register'][] = [
	                'type'     => 'javascript',
	                'handle'   => 'auto-js-{{blockHandle}}',
	                'filename' => 'blocks/{{blockHandle}}/auto.js',
                ];
                $assets['composer']['require'][] = [
	                'type'   => 'javascript',
	                'handle' => 'auto-js-{{blockHandle}}',
                ];
            }
            if (trim($autoCss) != '') {
                if (!isset($assets['addEdit'])) {
                    $assets['addEdit'] = [];
                }
                if (!isset($assets['addEdit']['register'])) {
                    $assets['addEdit']['register'] = [];
                }
                if (!isset($assets['addEdit']['require'])) {
                    $assets['addEdit']['require'] = [];
                }
                $assets['addEdit']['register'][] = [
	                'type'     => 'css',
	                'handle'   => 'auto-css-{{blockHandle}}',
	                'filename' => 'blocks/{{blockHandle}}/auto.css',
                ];
                $assets['addEdit']['require'][] = [
	                'type'   => 'css',
	                'handle' => 'auto-css-{{blockHandle}}',
                ];
            }
            $assetsLists = [];
            foreach ($assets as $assetFunctionName => $assetArray) {
                // First register assets, before being able to actually require assets
                $assetArray = self::sortArrayByArray($assetArray, ['register', 'require']);
                foreach ($assetArray as $assetEntriesType => $assetEntries) {
                    foreach ($assetEntries as $assetEntry) {
	                    $assetEntry['handle'] = str_replace('{{blockHandle}}', '\' . $this->btHandle . \'', $assetEntry['handle']);
	                    $assetEntry['filename'] = str_replace('{{blockHandle}}', '\' . $this->btHandle . \'', $assetEntry['filename']);
                        $assetCode = null;
                        switch ($assetEntriesType) {
                            case 'require':
                                if (isset($assetEntry['type'])) {
                                    $assetCode = '$this->requireAsset(\'' . $assetEntry['type'] . '\', \'' . $assetEntry['handle'] . '\');';
                                } else {
                                    $assetCode = '$this->requireAsset(\'' . $assetEntry['handle'] . '\');';
                                }
                                break;
                            case 'register':
                                if (!isset($assetsLists[$assetFunctionName])) {
                                    $controllerFunctions[$assetFunctionName]['lines'][] = '$al = AssetList::getInstance();';
                                    $assetsLists[$assetFunctionName] = true;
	                                $controllerUsesArray[] = 'AssetList';
                                }
                                $args = isset($assetEntry['args']) ? $assetEntry['args'] : [];
	                            $args = self::varExportArray($args, true);
                                $assetCode = '$al->register(\'' . $assetEntry['type'] . '\', \'' . $assetEntry['handle'] . '\', \'' . $assetEntry['filename'] . '\', ' . $args . ', $this->pkg);';
                                break;
                        }
                        if (trim($assetCode) != '') {
                            $controllerFunctions[$assetFunctionName]['lines'][] = str_replace(' . \'\'', '', $assetCode);
                        }
                    }
                }
            }
            $postData['packages'] = self::$packages;
            foreach ($tabs as $k => $v) {
                if (!isset($tabsFields[$v[0]]) || empty($tabsFields[$v[0]])) {
                    unset($tabs[$k]);
                    unset($tabsFields[$v[0]]);
                }
            }
            $tabs = array_filter($tabs);
            if (count($tabs) > 1) {
                $tabsArray = [];
                $tabs[key($tabs)][2] = true;
                foreach ($tabs as $tab) {
                    $tabsArray[] = self::getTabs(1) . '[\'' . $tab[0] . '-\' . $identifier_getString, t(\'' . $tab[1] . '\')' . (isset($tab[2]) && $tab[2] === true ? ', true' : null) . ']';
                }
                $formFields[] = '<?php $tabs = [' . PHP_EOL . implode(',' . PHP_EOL, $tabsArray) . PHP_EOL . '];
echo Core::make(\'helper/concrete/ui\')->tabs($tabs); ?>';
                if (!empty($tabsFields)) {
                    foreach ($tabsFields as $k => $v) {
                        $formFields[] = '<div class="ccm-tab-content" id="ccm-tab-content-' . $k . '-<?php echo $identifier_getString; ?>">
    ' . implode('', $v) . '
</div>';
                    }
                }
            } else {
                // In a case where we only use static HTML, there are no tabs fields
                if (!empty($tabsFields)) {
                    $formFields = array_merge($formFields, $tabsFields[key($tabsFields)]);
                }
            }
            // Some final lines for our functions
            $controllerFunctions['composer']['lines'][] = '$this->edit();';
            if (!empty($controllerFunctions['on_start']['lines'])) {
                array_unshift($controllerFunctions['on_start']['lines'], '$al = AssetList::getInstance();');
	            $controllerUsesArray[] = 'AssetList';
            }
            if (in_array('Database', $controllerUsesArray)) {
                if (!empty($controllerFunctions['view']['lines'])) {
                    array_unshift($controllerFunctions['view']['lines'], '$db = Database::connection();');
                }
                if (!empty($controllerFunctions['edit']['lines'])) {
                    array_unshift($controllerFunctions['edit']['lines'], '$db = Database::connection();');
                }
                if (!empty($controllerFunctions['delete']['lines'])) {
                    array_unshift($controllerFunctions['delete']['lines'], '$db = Database::connection();');
                }
                if (!empty($controllerFunctions['duplicate']['lines'])) {
                    array_unshift($controllerFunctions['duplicate']['lines'], '$db = Database::connection();');
                }
                if (!empty($controllerFunctions['save']['lines'])) {
                    array_unshift($controllerFunctions['save']['lines'], '$db = Database::connection();');
                }
            }
            if (!empty($controllerFunctions['delete']['lines'])) {
                $controllerFunctions['delete']['lines'][] = 'parent::delete();';
            }
            if (!empty($controllerFunctions['duplicate']['lines'])) {
                $controllerFunctions['duplicate']['lines'][] = 'parent::duplicate($newBID);';
            }
            if (!empty($controllerFunctions['save']['lines'])) {
                $controllerFunctions['save']['lines'][] = 'parent::save($args);';
            }
            if (!empty($controllerFunctions['validate']['lines'])) {
                array_unshift($controllerFunctions['validate']['lines'], '$e = Core::make("helper/validation/error");');
                $controllerFunctions['validate']['lines'][] = 'return $e;';
            }
            if (!empty($controllerFunctions['getSearchableContent']['lines'])) {
                array_unshift($controllerFunctions['getSearchableContent']['lines'], '$content = [];');
                $controllerFunctions['getSearchableContent']['lines'][] = 'return implode(" ", $content);';
            }
            $controllerFunctions['addEdit']['lines'][] = '$this->set(\'btFieldsRequired\', $this->btFieldsRequired);';
	        $controllerFunctions['addEdit']['lines'][] = '$this->set(\'identifier_getString\', Core::make(\'helper/validation/identifier\')->getString(18));';
	        foreach ($controllerFunctions as $key => $controllerFunction) {
                if (isset($controllerFunction['lines']) && !empty($controllerFunction['lines'])) {
                    $controllerFunctionsArray[] = $controllerFunction['type'] . ' function ' . $key . '(' . (isset($controllerFunction['variables']) && is_array($controllerFunction['variables']) && !empty($controllerFunction['variables']) ? implode(', ', $controllerFunction['variables']) : null) . ')
    {
        ' . implode(PHP_EOL . self::getTabs(2), $controllerFunction['lines']) . '
    }';
                }
            }
            if (!empty($extraFunction)) {
                $controllerFunctionsArray = array_merge($controllerFunctionsArray, $extraFunction);
            }
            // Copy block/field type related files
            $fileService = new File();
            $fileService->copyAll(self::getPackageFolder(self::$packageHandle) . 'template', $blockTypeFolder);
            foreach ($copyFiles as $copy_file) {
                if (is_array($copy_file) && isset($copy_file['source'], $copy_file['target'])) {
                    $fileService->copyAll($copy_file['source'], $copy_file['target'], isset($copy_file['mode']) ? $copy_file['mode'] : null);
                }
            }
            $dbTables = array_merge([$btTable => ['fields' => $dbFields]], $dbTables);
	        if(!empty($controllerVariables['helpers']['value']) && !in_array('form', $controllerVariables['helpers']['value'])){
		        $controllerVariables['helpers']['value'][] = 'form';
	        }
            if (count($controllerVariables['btExportTables']['value']) <= 1) {
                unset($controllerVariables['btExportTables']);
            } else {
                $controllerVariables['btExportTables']['value'] = array_unique($controllerVariables['btExportTables']['value']);
            }
            foreach ($controllerVariables as $key => $classVariable) {
	            $continueVariable = true;
	            if(isset($classVariable['array_empty_remove']) && $classVariable['array_empty_remove'] === true && empty($classVariable['value'])){
		            $continueVariable = false;
	            }
	            if($continueVariable){
		            $export = is_array($classVariable['value']) ? self::varExportArray($classVariable['value'], isset($classVariable['array_strip']) && $classVariable['array_strip'] === true) : var_export($classVariable['value'], true);
		            $controllerVariablesArray[] = $classVariable['type'] . ' $' . $key . ' = ' . $export . ';';
	            }
            }
            // Make sure we have no duplicates
	        $controllerUsesArray = array_unique($controllerUsesArray);
	        asort($controllerUsesArray);
            $controllerUsesString = !empty($controllerUsesArray) ? implode('', array_map(function ($v, $k) {
                return sprintf('use %s;' . PHP_EOL, $v);
            }, $controllerUsesArray, array_keys($controllerUsesArray))) : null;
            // Get a block image, if posted along
            if ($block_image = isset($_FILES, $_FILES["block_image"]) ? $_FILES["block_image"] : false) {
                $allowedExts = ["png"];
                $temp = explode(".", $block_image["name"]);
                $extension = end($temp);
                if (in_array($extension, $allowedExts) && $block_image["error"] <= 0) {
                    move_uploaded_file($block_image["tmp_name"], $blockTypeFolder . DIRECTORY_SEPARATOR . 'icon.png');
                }
            }
            $files = [
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'auto.js',
		            'text' => trim($autoJs) != '' ? $autoJs : null,
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'auto.css',
		            'text' => trim($autoCss) != '' ? $autoCss : null,
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'view.js',
		            'text' => isset($postData['view_js']) && is_string($postData['view_js']) && trim($postData['view_js']) != '' ? $postData['view_js'] : null,
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'view.css',
		            'text' => isset($postData['view_css']) && is_string($postData['view_css']) && trim($postData['view_css']) != '' ? $postData['view_css'] : null,
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'config.json',
		            'text' => json_encode($postData),
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'view.php',
		            'text' => !empty($viewFields) ? implode(PHP_EOL, $viewFields) : null,
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'db.xml',
		            'text' => self::buildSchema($dbTables),
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'form.php',
		            'text' => !empty($formFields) ? implode(PHP_EOL . PHP_EOL, $formFields) : null,
	            ],
	            [
		            'name' => $blockTypeFolder . DIRECTORY_SEPARATOR . 'controller.php',
		            'text' => '<?php namespace Application\Block\\' . $blockHandleNamespaced . ';

defined("C5_EXECUTE") or die("Access Denied.");' . (trim($controllerUsesString) != '' ? PHP_EOL . PHP_EOL . $controllerUsesString : null) . '
class Controller extends BlockController
{
' . self::getTabs(1) . (!empty($controllerVariablesArray) ? implode(PHP_EOL . self::getTabs(1), $controllerVariablesArray) : null) . '
' . self::getTabs(1) . (!empty($controllerFunctionsArray) ? PHP_EOL . self::getTabs(1) . implode(PHP_EOL . PHP_EOL . self::getTabs(1), $controllerFunctionsArray) : null) . '
}'
	            ],
            ];
            foreach ($files as $file) {
                if (trim($file['text']) != '') {
                    $fileService->append($file['name'], $file['text']);
                }
            }
            return true;
        }
    }

	protected function varExportArray($array, $arrayStrip = false){
		$valueGenerator = version_compare(Config::get('concrete.version'), '8.0.0', '>=');
		$export = $valueGenerator ? (new ValueGenerator($array, ValueGenerator::TYPE_ARRAY_SHORT))->setIndentation('  ')->generate() : var_export($array, true);
		if ($arrayStrip) {
			$arrayEndString = $valueGenerator ? '\]' : '\)';
			$arrayEndStringReplace = $valueGenerator ? ']' : ')';
			$patterns = ['(\d+\s=>)', "/\s+/", "/\s([?.!])/", '/,'.$arrayEndString.'/', '/\',\'/', '/=>/', '/'.$arrayEndString.',\'/'];
			$replacer = ['', '', '$1', $arrayEndStringReplace, "', '", ' => ', $arrayEndStringReplace . ', \''];
			$export = preg_replace($patterns, $replacer, $export);
		}
		return $export;
	}

    protected function sortArrayByArray(array $array, array $orderArray)
    {
        $ordered = [];
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

    public function buildSchema($tables = [])
    {
        $html = '<?xml version="1.0"?>' . PHP_EOL . '<schema version="0.3">';
        foreach ($tables as $k => $v) {
            $fields = [];
            if (isset($v['fields']) && is_array($v['fields']) && !empty($v['fields'])) {
                foreach ($v['fields'] as $field) {
                    if (is_array($field) && isset($field['name'], $field['type'])) {
                        $inside = [];
                        if (isset($field['attributes']) && is_array($field['attributes'])) {
                            foreach ($field['attributes'] as $attributeKey => $attributeValue) {
                                switch (strtoupper($attributeKey)) {
                                    case 'DEFDATE':
                                    case 'DEFTIMESTAMP':
                                    case 'NOQUOTE':
                                    case 'CONSTRAINTS':
                                        // Not sure what the above ones do
                                    case 'AUTOINCREMENT':
                                    case 'KEY':
                                    case 'PRIMARY':
                                        if ((bool)$attributeValue === true) {
                                            $inside[] = '<' . strtolower($attributeKey) . '/>';
                                        }
                                        break;
                                    case 'DEF':
                                    case 'DEFAULT':
                                        $inside[] = '<default value="' . $attributeValue . '"/>';
                                        break;
                                    case 'UNSIGNED':
                                    case 'NOTNULL':
                                        if ((bool)$attributeValue === true) {
                                            $inside[] = '<unsigned/>';
                                        }
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                        $fields[] = '<field name="' . $field['name'] . '" type="' . $field['type'] . '"' . (isset($field['size']) && trim($field['size']) != '' ? ' size="' . $field['size'] . '"' : null) . '>' . (!empty($inside) ? PHP_EOL . self::getTabs(3) . implode(PHP_EOL . self::getTabs(3), $inside) . PHP_EOL . self::getTabs(2) : null) . '</field>';
                    }
                }
            }
            if (!empty($fields)) {
                $html .= PHP_EOL . self::getTabs(1) . '<table name="' . $k . '">' . PHP_EOL . self::getTabs(2) . implode(PHP_EOL . self::getTabs(2), $fields) . PHP_EOL . self::getTabs(1) . '</table>';
            }
        }
        $html .= PHP_EOL . '</schema>';
        return $html;
    }

    private function getTabs($count = 1)
    {
        $return = '';
        if ($count >= 1) {
            for ($i = 1; $i <= $count; $i++) {
                $return .= '    ';
            }
        }
        return $return;
    }
}