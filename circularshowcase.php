<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class CircularShowcase extends Module
{
	public function __construct() { $this->name = 'circularshowcase';
    $this->tab = 'front_office_features';
    $this->version = '1.0';
    $this->author = 'shota.k';
    $this->need_instance = 0;
    //$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.5.4.1'); 
 
    parent::__construct();
 
    $this->displayName = $this->l('CircularShowCase');
    $this->description = $this->l('CircularShowCase');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  }

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);
		 
		return parent::install() &&
		$this->registerHook('home') &&
		$this->registerHook('header');
	}

	public function uninstall()
	{
		if (!parent::uninstall()||!Configuration::deleteByName('circularshowcase'))
			return false;
		return true;
	}

	public function hookDisplayHome($params)
	{
		if(strval(Configuration::get('SHOWING_PRODUCTS')) == 'featured'){
		$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
		$nb = (int)(Configuration::get('HOME_FEATURED_NBR'));
		$products = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 10)); 
		}else if(strval(Configuration::get('SHOWING_PRODUCTS')) == 'new'){
			$products = Product::getNewProducts((int)($params['cookie']->id_lang), 0, (int)(Configuration::get('NEW_PRODUCTS_NBR')));
		}

		if (!$products && !Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))
			return;

		$this->context->smarty->assign(
			array(
				'showing_products' => Configuration::get('SHOWING_PRODUCTS')
			)
		);
		$this->smarty->assign(
			array(
				'new_products' => $products,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			)
		); 
		return $this->display(__FILE__, 'circularshowcase.tpl');
	}
					 
	public function hookDisplayHeader()
	{
		$this->context->controller->addJS($this->_path.'js/jquery.easing.1.3.js');
		$this->context->controller->addJS($this->_path.'js/jquery.mousewheel.js');
		$this->context->controller->addJS($this->_path.'js/jquery.contentcarousel.js');
		$this->context->controller->addCSS($this->_path.'css/circularshowcase.css', 'all');
		$this->context->controller->addCSS($this->_path.'css/jquery.jscrollpane.css', 'all');
		$this->context->controller->addCSS($this->_path.'css/style.css', 'all');
	}

	public function getContent()
	{
		$output = null;
	 
		if (Tools::isSubmit('submit'.$this->name))
		{
			$showing_products = strval(Tools::getValue('SHOWING_PRODUCTS'));
			if (!$showing_products  || empty($showing_products) || !Validate::isGenericName($showing_products))
				$output .= $this->displayError( $this->l('Invalid Configuration value') );
			else
			{
				Configuration::updateValue('SHOWING_PRODUCTS', $showing_products); 
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		// Get default Language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
						 
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => array(
				array(
			 		'type' => 'radio',
			 		'label' => $this->l('Showing products'),
			 		'name' => 'SHOWING_PRODUCTS',
			 		'class' => 't',
			 		'values' => array(                                              
						array(
							'name' => 'SHOWING_PRODUCTS',
							'value' => 'new',
							'selected' => 'selected',
							'label' => $this->l('New Prodcuts')
						),
						array(
							'name' => 'SHOWING_PRODUCTS',
							'value' => 'featured',
							'label' => $this->l('Featured Products')
						)
			 		),
			 		'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);
							 
			$helper = new HelperForm();
						 
			$helper->module = $this;
			$helper->name_controller = $this->name;
			$helper->token = Tools::getAdminTokenLite('AdminModules');
			$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
						 
			$helper->default_form_language = $default_lang;
			$helper->allow_employee_form_lang = $default_lang;
						 
			$helper->title = $this->displayName;
			$helper->show_toolbar = true;
			$helper->toolbar_scroll = true;

			$helper->submit_action = 'submit'.$this->name;
			$helper->toolbar_btn = array(
			'save' =>
				array(
					'desc' => $this->l('Save'),
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
						'&token='.Tools::getAdminTokenLite('AdminModules'),
						),
					'back' => array(
						'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
						'desc' => $this->l('Back to list')
					)
				);
			 
			// Load current value
			$helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
			return Configuration::get('SHOWING_PRODUCTS') == 'featured' ? str_replace('value="featured"', 'value="featured" checked', $helper->generateForm($fields_form)): str_replace('value="new"', 'value="new" checked', $helper->generateForm($fields_form));
		}
}
?>
