<?php
class ControllerExtensionModuleGeneratorKeyword extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/generator_keyword');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('generator_keyword', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_code'] = $this->language->get('entry_code');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_code'] = $this->language->get('help_code');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/generator_keyword', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['categories'] = $this->url->link('extension/module/generator_keyword/categories', 'user_token=' . $this->session->data['user_token'], true);

		$data['products'] = $this->url->link('extension/module/generator_keyword/products', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['generator_keyword_code'])) {
			$data['generator_keyword_code'] = $this->request->post['generator_keyword_code'];
		} else {
			$data['generator_keyword_code'] = $this->config->get('generator_keyword_code');
		}

		if (isset($this->request->post['generator_keyword_status'])) {
			$data['generator_keyword_status'] = $this->request->post['generator_keyword_status'];
		} else {
			$data['generator_keyword_status'] = $this->config->get('generator_keyword_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/generator_keyword', $data));
	}

	public function categories() {

		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/generator_keyword/categories', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$data['action'] = $this->url->link('extension/module/generator_keyword/saveCategories', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['categories'] = array();

		$results = $this->getCategories();
		$keywords = array();

		foreach ($results as $result) {

			$category_info = $this->model_catalog_category->getCategory($result['category_id']);

			$keyword = $this->seo_keyword($category_info['name']);

			$dublicate = in_array($keyword, $keywords);

			$keywords[] = $keyword;

			if ($dublicate) {
				$keyword .= "-" . $result['category_id'];
			}

			$data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name'        => $category_info['name'],
				'keyword'	  => $keyword,
				'dublicate'	  => (int) $dublicate
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/module/generator_keyword/categories', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/module/generator_keyword/categories', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = "";

		$data['results'] = "";

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/generator_keyword_categories', $data));
	}

	public function products() {

		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['action'] = $this->url->link('extension/module/generator_keyword/saveProducts', 'user_token=' . $this->session->data['user_token'], true);

		$data['saveAll'] = $this->url->link('extension/module/generator_keyword/saveProductsAll', 'user_token=' . $this->session->data['user_token'], true);

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_image'    => $filter_image,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * 1000,
			'limit'           => 1000
		);

		$this->load->model('tool/image');

		$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_product->getProducts($filter_data);


		$keywords = array();

		foreach ($results as $result) {

			$keyword = $this->seo_keyword($result['name']);

			$dublicate = in_array($keyword, $keywords);

			$keyword = preg_replace('/[^a-zA-Z0-9]/', '', $keyword);
			if ($keyword == '') {
				$length = 10;
				$randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
				$keyword = $randomletter;
			}
			$keywords[] = $keyword;

			if ($dublicate) {
				$keyword .= "-" . $result['product_id'];
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'name'       => $result['name'],
				'keyword'	 => $keyword,
				'dublicate'  => (int) $dublicate
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] =  $this->session->data['user_token'];
		$data['user_token'] =  $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		// $pagination->limit = 1000;
		$pagination->limit = 1000;
		$pagination->url = $this->url->link('extension/module/generator_keyword/products', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * 1000) + 1 : 0, ((($page - 1) * 1000) > ($product_total - 1000)) ? $product_total : ((($page - 1) * 1000) + 1000), $product_total, ceil($product_total / 1000));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;
		$data['filter_image'] = $filter_image;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/generator_keyword_products', $data));
	}

	public function saveCategories() {

		echo '<table width="100%" border="0" cellpadding="4">';
		echo '<style>';
		echo 'tr {
			    text-align: left;
			    background: #ccc;
			    width: 99.99%;
			    float: left;
			    font-size:14px;
			};
			td{
				    width: 49.99%;
    				float: left;
			}
		';
		echo '</style>';
		foreach ($this->request->post['keyword'] as $key => $keyword) {
			$keyword = $this->rus2translit($keyword);
			$keyword = strtolower($keyword);
			$keyword = str_replace("'", "",$keyword);
			$keyword = str_replace(".", "",$keyword);
			$keyword = str_replace(":", "",$keyword);
			$keyword = str_replace("/", "",$keyword);
			$keyword = str_replace('"', '',$keyword);
			if ($keyword) {
			   $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$key . "'");
				$q1 = $this->db->query("INSERT " . DB_PREFIX. "seo_url SET query = 'category_id="
					. (int)$key
					. "', keyword = '" . $this->db->escape($keyword)
					. "', store_id = '0"
					. "', language_id = '0" .
					"'");
			   echo '<tr>
			    <td>Сгенерирован url для категории: '.$key.'</td>
			    <td>Keyword:'.$keyword.'</td>
			   </tr>';
			}else{
				echo '<br>';
				var_dump('================ no keyword =================');
			}
		}
		echo '</table>';
		echo '<a href="javascript:history.back()">Go back</a>';
	}

	public function saveProducts() {

		foreach ($this->request->post['keyword'] as $key => $keyword) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$key . "'");

			if ($keyword) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$key . "', keyword = '" . $this->db->escape($keyword) . "'");
			}
		}

		$this->response->redirect($this->url->link('extension/module/generator_keyword', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

	}

	public function rus2translit($string) {
	    $converter = array(
	        'а' => 'a',   'б' => 'b',   'в' => 'v',
	        'г' => 'g',   'д' => 'd',   'е' => 'e',
	        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
	        'и' => 'i',   'й' => 'y',   'к' => 'k',
	        'л' => 'l',   'м' => 'm',   'н' => 'n',
	        'о' => 'o',   'п' => 'p',   'р' => 'r',
	        'с' => 's',   'т' => 't',   'у' => 'u',
	        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
	        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
	        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
	        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

	        'і' => 'i',   'ї' => 'ji',  'є' => 'je',

	        'А' => 'A',   'Б' => 'B',   'В' => 'V',
	        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
	        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
	        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
	        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
	        'О' => 'O',   'П' => 'P',   'Р' => 'R',
	        'С' => 'S',   'Т' => 'T',   'У' => 'U',
	        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
	        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
	        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
	        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',

	        'І' => 'I',   'Ї' => 'JI',  'Є' => 'JE',
	        ' ' => '-', '+' => '', '  ' => '',
 	    );
	    return strtr($string, $converter);
	}

	public function saveProductsAll() {

		$this->load->language('catalog/product');
		$this->load->model('catalog/product');

		$data['products'] = array();

		$filter_data = array(
			'start'           => 0,
			'limit'           => 30000
		);

		$results = $this->model_catalog_product->getProducts($filter_data);


		$keywords = array();

		// $this->db->save_queries = TRUE;

		ini_set('max_execution_time', 0);

		echo '<table width="100%" border="0" cellpadding="4">';
		echo '<style>';
		echo 'tr {
			    text-align: left;
			    background: #ccc;
			    width: 99.99%;
			    float: left;
			    font-size:14px;
			};
			td{
				    width: 49.99%;
    				float: left;
			}
		';
		echo '</style>';
		foreach ($results as $result) {

			$keyword = $this->rus2translit($result['name']);
			$keyword = strtolower($keyword);
			$keyword = str_replace("'", "",$keyword);
			$keyword = str_replace(".", "",$keyword);
			$keyword = str_replace(":", "",$keyword);
			$keyword = str_replace("/", "",$keyword);
			$keyword = str_replace('"', '',$keyword);
			$keyword = $keyword.'.html';

			$dublicate = in_array($keyword, $keywords);

			$keywords[] = $keyword;

			if ($dublicate) {
				$keyword .= "-" . $result['product_id'];
			}

			// $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$result['product_id'] . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$result['product_id'] . "'");


			if ($keyword) {
				$q1 = $this->db->query("INSERT " . DB_PREFIX. "seo_url SET query = 'product_id="
					. (int)$result['product_id']
					. "', keyword = '" . $this->db->escape($keyword)
					. "', store_id = '0"
					. "', language_id = '0" .
					"'");
			   echo '<tr>
			    <td>Сгенерирован url для товара: '.$result['product_id'].'</td>
			    <td>Keyword:'.$keyword.'</td>
			   </tr>';
			}else{
				echo '<br>';
				var_dump('================ no keyword =================');
			}
		}
		echo '</table>';
		// $this->response->redirect($this->url->link('extension/module/generator_keyword', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		echo '<a href="javascript:history.back()">Go back</a>';
	}

	public function seo_keyword($string) {

	    $string = trim($string);

	    $string = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a'), $string );
	    $string = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'e', 'e', 'e', 'e'), $string );
	    $string = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'i', 'i', 'i', 'i'), $string );
	    $string = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'o', 'o', 'o', 'o'), $string );
	    $string = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'u', 'u', 'u', 'u'), $string );
	    $string = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'n', 'c', 'c',), $string );
	    //Esta parte se encarga de eliminar cualquier caracter extraño

	    $t =array("","*", "¨", "º", "~", "#", "@", "|", "!", '"',"·", "$", " % ", " & ", " / ", "(", ")", " ? ", "'", "¡", "¿", "[", "^", "`", "]", "+", "}", "{", "¨", "´",">", "< ", ";", ",", ":", ".");

	    $string = str_replace($t, '', $string);

	    $string = str_replace( " ", "-", $string);

	    $string = strtolower($string);

	    return $string;

	}

	public function getCategories($data = array()) {

		$sql = "SELECT category_id FROM " . DB_PREFIX . "category";
		$query = $this->db->query($sql);

		return $query->rows;
	}


	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/generator_keyword')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
