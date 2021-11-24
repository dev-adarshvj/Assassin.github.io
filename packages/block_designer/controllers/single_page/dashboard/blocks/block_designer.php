<?php namespace Concrete\Package\BlockDesigner\Controller\SinglePage\Dashboard\Blocks;

defined('C5_EXECUTE') or die("Access Denied.");

use AssetList;
use Block;
use BlockType;
use Concrete\Core\File\Service\File;
use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Database;
use Environment;
use Exception;
use Package;
use Page;
use RamonLeenders\BlockDesigner\BlockDesignerProcessor;
use TaskPermission;

class BlockDesigner extends DashboardPageController
{
	public $helpers = ['form'];
	public $packageHandle = 'block_designer';

	private function blockTypeExists($btHandle)
	{
		return BlockType::getByHandle($btHandle) ? true : false;
	}

	public function delete_folder($slug)
	{
		$result = [
			'success' => false,
		];
		if (!$this->blockTypeExists($slug)) {
			$directory = BlockDesignerProcessor::getBlockTypeFolder($slug);
			if (file_exists($directory) && $this->deleteDirectory($directory)) {
				$result = [
					'success' => true,
				];
			}
		}
		echo json_encode($result);
		exit;
	}

	private function deleteDirectory($dir)
	{
		$fileService = new File();
		return $fileService->removeAll($dir, true);
	}

	private function getBlockTypeSets($includeNone = false)
	{
		$db = Database::connection();
		$blockTypeSets = $db->fetchAll('SELECT * from BlockTypeSets ORDER BY btsDisplayOrder');
		$options = [];
		if ($includeNone) {
			$options[''] = t('-- %s --', t('None'));
		}
		foreach ($blockTypeSets as $blockTypeSet) {
			$options[$blockTypeSet['btsID']] = $blockTypeSet['btsName'];
		}
		return $options;
	}

	public function install($btHandle = null, $btsID = false)
	{
		$installed = $this->blockTypeExists($btHandle);
		if (!$installed) {
			$tp = new TaskPermission();
			if ($tp->canInstallPackages()) {
				$blockTypeFolder = BlockDesignerProcessor::getBlockTypeFolder($btHandle);
				if (file_exists($blockTypeFolder) && file_exists($blockTypeFolder . DIRECTORY_SEPARATOR . 'controller.php')) {
					$env = Environment::get();
					$env->clearOverrideCache();
					try {
						$resp = BlockType::installBlockType($btHandle);
						$btID = $resp->getBlockTypeID();
						if ($btID > 0 && $btsID) {
							$blockTypeSets = ($this->getBlockTypeSets(true));
							if (array_key_exists($btsID, $blockTypeSets)) {
								$db = Database::connection();
								$order = 0;
								if ($displayOrder = $db->fetchAssoc('SELECT (MAX(displayOrder) + 1) AS displayOrder FROM BlockTypeSetBlockTypes WHERE btsID = ?', [$btsID])) {
									$order = (int)$displayOrder['displayOrder'];
								}
								$insert_values = [$btID, $btsID, $order];
								$db->executeQuery('INSERT INTO BlockTypeSetBlockTypes (btID, btsID, displayOrder) values(?,?,?)', $insert_values);
							}
						}
						$this->redirect('/dashboard/blocks/' . $this->packageHandle, 'installed');
					} catch (Exception $e) {
						$this->error->add($e);
					}
				} else {
					$this->error->add(t('Block type <strong>%s</strong> does not exist (anymore).', $btHandle));
				}
			} else {
				$this->error->add(t('You do not have permission to install custom block types or add-ons.'));
			}
		} else {
			$this->error->add(t('Block type <strong>%s</strong> is already installed.', $btHandle));
		}
		$this->view();
	}

	public function installed()
	{
		$this->set('success', t('Block type installed successfully.'));
		$this->view();
	}

	public function config($btHandle = null)
	{
		if (trim($btHandle) != '') {
			if ($bt = BlockType::getByHandle($btHandle)) {
				$env = Environment::get();
				$env->clearOverrideCache();
				$pkgHandle = false;
				$pkgID = $bt->getPackageID();
				if ($pkgID > 0) {
					$pkg = Package::getByID($pkgID);
					$pkgHandle = $pkg->getPackageHandle();
				}
				$btFolder = dirname($env->getPath(DIRNAME_BLOCKS . '/' . $btHandle . '/' . FILENAME_CONTROLLER, $pkgHandle));
			} else {
				$btFolder = BlockDesignerProcessor::getBlockTypeFolder($btHandle);
			}
			if (file_exists($btFolder)) {
				$config = [
					'file'     => $btFolder . DIRECTORY_SEPARATOR . 'config.json',
					'contents' => '',
				];
				if (file_exists($config['file'])) {
					$fileService = new File();
					$config['contents'] = $fileService->getContents($config['file']);
					$config['values'] = json_decode($config['contents']);
					$this->view((array)$config['values']);
				} else {
					$this->error->add(t('The block type does not seem to have an existing config file.'));
					$this->view();
				}
			} else {
				$this->error->add(t('The block type does not seem to exist.'));
				$this->view();
			}
		}
	}

	private function sortArrayByArray(Array $array, Array $orderArray)
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

	private function blockHandleCheck($blockHandle = null)
	{
		$errors = [];
		preg_match('/^([a-z]+)([a-z_]+)[a-z]$/', $blockHandle, $matches);
		if ($matches) {
			if ($this->blockTypeExists($blockHandle)) {
				$errors[] = t('Block type with handle <strong>%s</strong> already installed. Please use a different handle, before continuing this process.', $blockHandle);
			} else {
				$blockTypeFolder = BlockDesignerProcessor::getBlockTypeFolder($blockHandle);
				if (file_exists($blockTypeFolder)) {
					$cp = Page::getByPath('/dashboard/blocks/block_designer');
					$errors[] = t('Block type folder <strong>%s</strong> already exists. You want to <a href="%s" class="delete_folder"><strong>delete this block type folder</strong></a>?', $blockHandle, $cp->getCollectionLink() . '/delete_folder/' . $blockHandle);
				} else {
					$reservedKeywords = ['abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor', 'yield'];
					if (in_array($blockHandle, $reservedKeywords)) {
						$errors[] = t('<strong>%s</strong> is in the blacklist of to be used block handles. Please try another handle.', $blockHandle, $blockHandle);
					}
				}
			}
		} else {
			$errors[] = t('Block handle <strong>%s</strong> does not match our pattern.', $blockHandle);
		}
		return $errors;
	}

	public function handle_check($blockHandle = null)
	{
		if (trim($blockHandle) == '') {
			$errors = [t('There was no block handle found. Please enter a block handle.')];
		} else {
			$errors = $this->blockHandleCheck($blockHandle);
		}
		if (!empty($errors)) {
			echo json_encode(
				[
					'error' => implode('</br>', $errors),
				]);
		} else {
			echo json_encode(
				[
					'success' => true,
				]);
		}
		exit;
	}

	private function _baseConfig()
	{
		return [
			'view_css'                                   => '',
			'view_js'                                    => '',
			'block_name'                                 => '',
			'block_description'                          => '',
			'block_type_set'                             => '',
			'cache_block_output_lifetime'                => '',
			'block_install'                              => '0',
			'ignore_page_theme_grid_framework_container' => '0',
			'cache_block_record'                         => '1',
			'cache_block_output'                         => '1',
			'cache_block_output_on_post'                 => '1',
			'cache_block_output_for_registered_users'    => '1',
			'table_prefix'                               => '',
			'interface_width'                            => '400',
			'interface_height'                           => '500',
			'fields'                                     => [],
		];
	}

	public function view($post_data = [])
	{
		$yesNo = [
			0 => t('No'),
			1 => t('Yes'),
		];
		$block_type_sets = $this->getBlockTypeSets(true);
		$errors = [];
		if (empty($post_data)) {
			$post_data = $this->_baseConfig();
		}
		$blocksFolder = BlockDesignerProcessor::getBlocksFolder();
		if (!file_exists($blocksFolder)) {
			$existing_folders = [];
			foreach (explode(DIRECTORY_SEPARATOR, $blocksFolder) as $folder) {
				$existing_folders[] = $folder;
				$folderDir = implode(DIRECTORY_SEPARATOR, $existing_folders);
				if (!file_exists($folderDir)) {
					$errors[] = t('Directory <strong>%s</strong> does not exist and needs to exist before you can use this package. Please create this in your environment and give it read, write and execute permissions (<strong>0777</strong>).', $folderDir);
				}
			}
		}
		if ($_POST) {
			if (isset($_POST['block_handle']) && trim($_POST['block_handle']) != '') {
				$post_data['block_handle'] = BlockDesignerProcessor::blockHandle($_POST['block_handle']);
				$blockHandleErrors = $this->blockHandleCheck($post_data['block_handle']);
				if (!empty($blockHandleErrors)) {
					$errors = array_merge($errors, $blockHandleErrors);
				}
			} else {
				$errors[] = t('Field <strong>%s</strong> is required', t('Block handle'));
			}

			/* YES/NO values */
			if (isset($_POST['block_install']) && array_key_exists($_POST['block_install'], $yesNo)) {
				$post_data['block_install'] = (int)$_POST['block_install'];
			}

			if (isset($_POST['cache_block_record']) && array_key_exists($_POST['cache_block_record'], $yesNo)) {
				$post_data['cache_block_record'] = (int)$_POST['cache_block_record'];
			}

			if (isset($_POST['cache_block_output']) && array_key_exists($_POST['cache_block_output'], $yesNo)) {
				$post_data['cache_block_output'] = (int)$_POST['cache_block_output'];
			}

			if (isset($_POST['cache_block_output_on_post']) && array_key_exists($_POST['cache_block_output_on_post'], $yesNo)) {
				$post_data['cache_block_output_on_post'] = (int)$_POST['cache_block_output_on_post'];
			}

			if (isset($_POST['cache_block_output_for_registered_users']) && array_key_exists($_POST['cache_block_output_for_registered_users'], $yesNo)) {
				$post_data['cache_block_output_for_registered_users'] = (int)$_POST['cache_block_output_for_registered_users'];
			}

			if (isset($_POST['ignore_page_theme_grid_framework_container']) && array_key_exists($_POST['ignore_page_theme_grid_framework_container'], $yesNo)) {
				$post_data['ignore_page_theme_grid_framework_container'] = (int)$_POST['ignore_page_theme_grid_framework_container'];
			}
			/* YES/NO values */

			if (isset($_POST['block_type_set']) && array_key_exists($_POST['block_type_set'], $block_type_sets)) {
				$post_data['block_type_set'] = (int)$_POST['block_type_set'];
			}

			if (isset($_POST['block_name']) && trim($_POST['block_name']) != '') {
				$post_data['block_name'] = $_POST['block_name'];
			} else {
				$errors[] = t('Field <strong>%s</strong> is required', t('Block name'));
			}

			if (isset($_POST['block_description']) && trim($_POST['block_description']) != '') {
				$post_data['block_description'] = $_POST['block_description'];
			}

			if (isset($_POST['table_prefix']) && trim($_POST['table_prefix']) != '') {
				$post_data['table_prefix'] = $_POST['table_prefix'];
				preg_match('/^[a-zA-Z]{2,15}$/', $post_data['table_prefix'], $matches);
				if (empty($matches)) {
					$post_data['table_prefix'] = null;
				}
			}

			if (!isset($_POST['fields']) || (!is_array($_POST['fields']) || empty($_POST['fields']))) {
				$errors[] = t('One or multiple fields are required to build a block.');
			} else {
				$post_data['fields'] = $_POST['fields'];
			}

			if (isset($_POST['cache_block_output_lifetime']) && trim($_POST['cache_block_output_lifetime']) != '' && (int)$_POST['cache_block_output_lifetime'] >= 0) {
				$post_data['cache_block_output_lifetime'] = (int)$_POST['cache_block_output_lifetime'];
			}

			if (isset($_POST['interface_width']) && (int)$_POST['interface_width'] >= 400 && (int)$_POST['interface_height'] <= 1800) {
				$post_data['interface_width'] = $_POST['interface_width'];
			}

			if (isset($_POST['interface_height']) && (int)$_POST['interface_height'] >= 500 && (int)$_POST['interface_height'] <= 1000) {
				$post_data['interface_height'] = $_POST['interface_height'];
			}

			if (isset($_POST['view_css']) && is_string($_POST['view_css']) && trim($_POST['view_css']) != '') {
				$post_data['view_css'] = $_POST['view_css'];
			}

			if (isset($_POST['view_js']) && is_string($_POST['view_js']) && trim($_POST['view_js']) != '') {
				$post_data['view_js'] = $_POST['view_js'];
			}

			if (isset($_POST['default_set']) && is_string($_POST['default_set']) && trim($_POST['default_set']) != '') {
				$post_data['default_set'] = $_POST['default_set'];
			}

			if (empty($errors)) {
				if (BlockDesignerProcessor::run($post_data)) {
					$redirect = '/dashboard/blocks/' . $this->packageHandle;
					if (isset($post_data['block_install']) && $post_data['block_install'] == '1') {
						$redirect .= '/install/' . $post_data['block_handle'] . (isset($post_data['block_type_set']) ? '/' . $post_data['block_type_set'] : null);
						$this->redirect($redirect);
					}
					$this->set('success', t('Block type %s created successfully.', $post_data['block_handle']));
					$post_data = $this->_baseConfig();
					$_POST = [];
				} else {
					$errors = array_merge($errors, BlockDesignerProcessor::runErrors());
				}
			}
		}

		$al = AssetList::getInstance();
		$al->register('css', 'responsive-tabs', 'css/responsiveTabs.css', [], $this->packageHandle);
		$al->register('css', 'block-designer-view', 'css/block_designer.view.css', [], $this->packageHandle);
		$al->register('javascript', 'responsive-tabs', 'js/jquery.responsiveTabs.js', [], $this->packageHandle);
		$al->register('javascript', 'form-validator', 'js/form_validator/jquery.form-validator.min.js', [], $this->packageHandle);
		$al->register('javascript', 'form-validator-file', 'js/form_validator/file.js', [], $this->packageHandle);
		$al->register('javascript', 'form-validator-date', 'js/form_validator/date.js', [], $this->packageHandle);
		$al->register('javascript', 'block-designer', 'js/jquery.blockDesigner.js', [], $this->packageHandle);
		$al->register('javascript', 'block-designer-view', 'js/block_designer.view.js', [], $this->packageHandle);
		$al->register('javascript', 'handlebars', 'js/handlebars-v4.0.5.js', ['minify' => false], $this->packageHandle);
		$al->register('javascript', 'handlebars-helpers', 'js/handlebars-helpers.js', [], $this->packageHandle);

		$this->requireAsset('css', 'responsive-tabs');
		$this->requireAsset('css', 'font-awesome');
		$this->requireAsset('css', 'block-designer-view');
		$this->requireAsset('javascript', 'responsive-tabs');
		$this->requireAsset('javascript', 'form-validator');
		$this->requireAsset('javascript', 'form-validator-file');
		$this->requireAsset('javascript', 'form-validator-date');
		$this->requireAsset('javascript', 'block-designer');
		$this->requireAsset('javascript', 'block-designer-view');
		$this->requireAsset('javascript', 'handlebars');
		$this->requireAsset('javascript', 'handlebars-helpers');

		$fieldTypes = BlockDesignerProcessor::getFieldTypes();

		foreach ($fieldTypes as $k => $fieldType) {
			if ($jsFile = $fieldType['class']->getFieldTypeJavascript()) {
				$al->register('javascript', 'field-type-' . $k . '-js', $jsFile, [], $fieldType['class']->pkgHandle);
				$this->requireAsset('javascript', 'field-type-' . $k . '-js');
			}
			if ($cssFile = $fieldType['class']->getFieldTypeCss()) {
				$al->register('css', 'field-type-' . $k . '-css', $cssFile, [], $fieldType['class']->pkgHandle);
				$this->requireAsset('css', 'field-type-' . $k . '-css');
			}
		}
		$this->set('pkg', Package::getByHandle($this->packageHandle));
		$this->set('yesNo', $yesNo);
		$this->set('block_type_sets', $block_type_sets);
		$this->set('errors', $errors);
		$this->set('field_types', $this->sortFieldTypes($fieldTypes));
		$this->set('ft_hide', Config::get($this->packageHandle . '.ft_hide'));
		$this->set('post_data', $post_data);
		$this->set('package_handle', $this->packageHandle);
	}

	private function sortFieldTypes($fieldTypes = [])
	{
		$ftOrder = Config::get('block_designer.ft_order');
		$ftSort = Config::get('block_designer.ft_sort');
		switch ($ftSort) {
			case 'krsort':
			case 'ksort':
				$ftSort($fieldTypes);
				break;
			case 'usort':
			case 'uksort':
				$fieldTypes = $this->sortArrayByArray($fieldTypes, $ftOrder);
				break;
			default:
			case 'asort':
			case 'arsort':
			case 'natsort':
			case 'natcasesort':
				if (!in_array($ftSort, ['asort', 'arsort', 'natsort', 'natcasesort'])) {
					$ftSort = 'asort';
				}
				$fieldTypesNames = [];
				foreach ($fieldTypes as $k => $v) {
					$fieldTypesNames[$k] = $v['name'];
				}
				$ftSort($fieldTypesNames);
				$fieldTypes = $this->sortArrayByArray($fieldTypes, array_keys($fieldTypesNames));
				break;
		}
		return $fieldTypes;
	}
}