<?php 
//Класс для работы с Моим складом через АПИ

namespace backend\components;

class MoySklad{
	
	// логин/пароль дотупа к Моему складу 
	protected $msklad_login = 'admin@mskladall';
	protected $msklad_pass = 'aeb254f52c';
	// ссылка до Моего склада
	protected $sklad_link = 'https://online.moysklad.ru/api/remap/1.1/';
	protected $link_add_get = 'entity/assortment?scope=product';
	protected $link_add_edit = 'entity/product/';
	protected $link_add_groups = 'entity/productfolder';
	protected $link_add_currency = 'entity/currency';
	protected $link_add_attributes = 'entity/product/metadata/';
	//текущий массив данных
	protected $arr = '';
	//количество записей всего в запрашиваемой выборке
	protected $record_count = 0;
	//количество полученных записей 
	protected $record_processed = 0;
	//Лимит получаемых записей от Моего склада
	protected $rec_count_limit = 100;
	//Название поля для id
	protected $id_field = 'id';
	protected $currency_meta = array();
	protected $attributes = array();
	//Название поля для названия
	protected $name_field = 'name';
	//ошибки
	protected $errors = '';
	public $next_link = '';
	private $sort_string;
	private $filter_string;
	private $groups = array();
	// конструктор
	public function __construct($params = array()) {
		$this->errors = '';
		if(isset($params['id_field'])){
			$this->id_field = $params['id_field'];
		}
		if(isset($params['name_field'])){
			$this->name_field = $params['name_field'];
		}
		if(isset($params['msklad_login'])){
			$this->msklad_login = $params['msklad_login'];
		}
		if(isset($params['msklad_pass'])){
			$this->msklad_pass = $params['msklad_pass'];
		}
	}
	//Получение полного количества записей
	public function get_record_count() {
		return $this->record_count;
	}
	//Получение количества обработанных записей
	public function get_record_processed() {
		return $this->record_processed;
	}

	// установка параметров сортировки
	public function set_sort($sort)
    {
        $this->sort_string = '';
        $arr = [];
        foreach ($sort as $attribute => $sort_order) {
            $arr[] = $attribute.','.($sort_order == SORT_ASC ? 'asc' : 'desc');
        }
        $this->sort_string = implode(';', $arr);
    }

    // установка параметров фильтрации
    public function set_filter($filter_name, $filter_sign, $filter_value)
    {
        $this->filter_string = $filter_name . $filter_sign . $filter_value;
    }
	//Получение массива заданного количества записей (limit)
	public function get($limit = 10, $offset = 0, $get_image = false) {
		$limits = array();
		if ($limit > $this->rec_count_limit) {
			$cnt = (int)($limit/$this->rec_count_limit);
			for ($i1 = 1; $i1 <= $cnt; $i1++) {
				$limits[] = array(
					'limit' => $this->rec_count_limit,
					'offset' => $offset + ($i1 - 1)*$this->rec_count_limit,
				);
			}
			$limits[] = array(
				'limit' => $limit - $cnt*$this->rec_count_limit,
				'offset' => $offset + $cnt*$this->rec_count_limit,
			);
		} else {
			$limits[] = array(
				'limit' => $limit,
				'offset' => $offset,
			);
		}
		$this->arr = array();
		foreach ($limits as $lim_chunk) {
			$this->arr = array_merge($this->arr, $this->get_msklad($lim_chunk['limit'], $lim_chunk['offset'], $this->sort_string, $this->filter_string, $get_image));
			if (!$this->next_link) {
				break;
			}
		}
		
		return $this->arr;
	}
	//Получение ссылки на следующий блок записей
	public function get_next_link() {
		return $this->next_link;
	}
	//Получение ошибки
	public function get_errors() {
		return $this->errors;
	}
	//Получение массива всех записей, удовлетворяющих фильтру
	public function get_all($get_image = false) {
		
		$this->next_link = '1';
		$this->arr = array();
		while ($this->next_link) {
			$arr_t = $this->get_chunk($this->next_link, $this->rec_count_limit, 0, $this->filter_string);
			$this->arr = array_merge($this->arr, $arr_t);
		}
		
		return $this->arr;
	}
 	//Получение части массива и ссылку на следующую страницу $this->next_link
	public function get_chunk($link, $limit = 10, $offset = 0, $sort_string = '', $filter_string = '') {
		if (!$link || $link == '1') {
            $link = $this->sklad_link . $this->link_add_get . '&limit=' . $limit . '&offset=' . $offset;
            if ($filter_string) {
                $link .= '&filter=' . $filter_string;
            }
            if ($sort_string) {
                $link .= '&order=' . $sort_string;
            }
            $link = str_replace(array(' ', '>'), array('%20', '%3E'), $link);
		}

		$this->next_link = '';
		
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);
		// get запрос
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		
		$this->errors = '';
		$arr = array();
		
		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
			$this->record_processed = 0;
			$this->record_count = 0;
			$this->next_link = '';
		} else {
			
			$results = json_decode($result);	

	//		echo $results;
			
			if (isset($results->errors)) {
				$this->errors = 'Ошибка сервиса "Мой склад": "' . $results->errors[0]->error . '"';
				$this->record_processed = 0;
				$this->record_count = 0;
				$this->next_link = '';
			} else {

				$record_processed = 0;
				if (isset($results->rows)) {
					$rows = $results->rows;
					foreach ($rows as $row) {
						
						$fields = array();
						$fields[$this->id_field] = $row->{$this->id_field};
						$fields['quantity'] = $row->quantity;
						$fields['name'] = $row->name;
						$fields['pathName'] = $row->pathName;
						$fields['art'] = $row->code;
						
						$arr[] = $fields;
						$record_processed++;
					}
				}
				$this->record_processed = $record_processed;
				$this->record_count = $results->meta->size;
				if (isset($results->meta->nextHref)) {
					$this->next_link = $results->meta->nextHref;
				} else {
					$this->next_link = '';
				}
			}
		}
		$this->do_delay('get_chunk');
//		echo $this->errors;
		return $arr;
	}
	
   //Получение произвольного массива
	private function get_msklad($limit = 1, $offset = 0, $sort_string = '', $filter_string = '', $get_image = false, $get_id_only = false) {

		$temp_arr = array();

        $link = $this->sklad_link . $this->link_add_get . '&limit=' . $limit . '&offset=' . $offset;
		if ($filter_string) {
			$link .= '&filter=' . $filter_string;
		}
		if ($sort_string) {
			$link .= '&order=' . $sort_string;
		}
		$link = str_replace(array(' ', '>'), array('%20', '%3E'), $link);

		$this->next_link = '';
							
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);       
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		// get запрос
		curl_setopt($curl, CURLOPT_URL, $link);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);	
		
		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
		} else {
			
			$results = json_decode($result);	

			if (isset($results->errors)) {
				$this->errors = 'Ошибка сервиса "Мой склад": "' . $results->errors[0]->error . '"';
			} else {
								
				if (isset($results->rows)) {
					$rows = $results->rows;
					foreach ($rows as $row) {
						
						$fields = array();

						$fields[$this->id_field] = $row->{$this->id_field};
						if (!$get_id_only) {
							$fields['art'] = $row->code;
							$fields['sku'] = $row->code;
							$fields['name'] = $row->name;
							$fields['description'] = $row->description;
							$fields['zprice'] = $row->buyPrice->value/100;
							$fields['weight'] = $row->weight;
							$fields['quantity'] = $row->quantity;
							$fields['pathName'] = $row->pathName;
							$fields['miniature'] = (isset($row->image->tiny->href) ? $row->image->tiny->href : '');
						}
						if ($get_image && isset ($row->image) && isset($row->image->miniature) && isset($row->image->miniature->href) && $row->image->miniature->href) {
							$fields['image'] = $this->get_image($row->image->miniature->href, $row->image->filename);
						}
											
						$temp_arr[] = $fields;
					}
				}
				$this->record_count = $results->meta->size;
				if (isset($results->meta->nextHref)) {
					$this->next_link = $results->meta->nextHref;
				} else {
					$this->next_link = '';
				}
			}
		}
		$this->do_delay('get_msklad');

		return $temp_arr;
	}
	//Получение записи по id
	public function get_one($id, $get_image = false, $get_id_only = false){

		return $this->get_msklad(1, 0, $this->id_field.'='.$id, $get_image, $get_id_only);
		
	}
	//Удаление элемента по id
	public function del($id) {
		
		$link = $this->sklad_link . $this->link_add_edit;
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_URL, $link . $id);
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		
		$result = curl_exec($curl);	

		$this->do_delay('delete');
		
		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
			$result_arr['rows_failed'] = count($res_array);
		} else {
			
			if (isset($results->errors)) {
				$this->errors = 'Ошибка сервиса "Мой склад": "' . $results['errors'][0]['error'] . '"';
				$result_arr['rows_failed'] = count($res_array);
			} else {
				$results = json_decode($result, true);
				return true;
			}
		}
		return false;
	}
	//Удаление нескольких элементов по id
	public function del1($ids){
		foreach ($ids as $id) {
			$this->del($id);
		}
	}
	//Добавление новой записи - пока пусто
	public function add($fields){
		return false;
	}
	// получение групп товаров в древесном формате
	public function getGroups() {
		$link = $this->sklad_link . $this->link_add_groups . '?limit=' . $this->rec_count_limit;
		$groups = array();
		$this->groups = array();
		
		while ($link) {
			$link = $this->getGroupParts($link, $groups);
		}
		return $groups;
	}
	// получение массива групп товаров в виде meta с ключом = pathName
	public function getGroupsMeta() {
		return $this->groups;
	}
	// получение групп товаров в древесном формате - частями по 100 (максимум Моего склада)
	private function getGroupParts($link, &$groups) {

		if (!$link) {
			$link = $this->sklad_link . $this->link_add_groups . '?limit=' . $this->rec_count_limit;;
		}
		$next_link = '';
		
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);       
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		// get запрос
		curl_setopt($curl, CURLOPT_URL, $link);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);	

		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
		} else {
			
			$results = json_decode($result);	

			if (isset($results->errors)) {
				$this->errors = 'Ошибка сервиса "Мой склад": "' . $results->errors[0]->error . '"';
			} else {								
				if (isset($results->rows)) {
					$rows = $results->rows;
					foreach ($rows as $row) {
						
						if (!$row->pathName) {
							eval('$groups["' . $row->name . '"] = array("href" => "' . $row->meta->href . '");');
						} else {
							$path_arr = explode('/', $row->pathName);
							$str1 = '["'.implode('"]["', $path_arr).'"]["'.$row->name.'"]';
							eval('$groups' . $str1 . ' = array("href" => "' . $row->meta->href . '");');
						}					
						$this->groups[($row->pathName ? $row->pathName . '/' : '') . $row->name] = $row->meta;
					}
				}
				if (isset($results->meta->nextHref)) {
					$next_link = $results->meta->nextHref;
				}
			}
		}
		$this->do_delay('getGroupParts');
		return $next_link;
	}

// массовое добавления товаров на Мой склад	
	public function multi_add(&$rows) {
		
		$result_arr = array(
			'rows_added' => 0,
			'rows_failed' => 0,
		);
		
		if (!$this->currency_meta) {
			$this->currency_meta = $this->get_currency();
		}
		$res_array = array();
		$res_update = '';
		
		foreach($rows as $data) {
			if (!isset($data['global_id']) || !$data['global_id']) {
				$queryData = array();			
				
				if (isset($data['groups_meta']) && $data['groups_meta']) {
					$queryData['productFolder'] = array(
						'meta' => $data['groups_meta'],
					);
				}
				if (isset($data['name']) && $data['name']) {
					$queryData['name'] = $data['name'];
				}
				if (isset($data['art']) && $data['art']) {
					$queryData['code'] = $data['art'];
				}
				if (isset($data['price']) && $data['price']) {
					$queryData['salePrices'][] = array(
						'value' => $data['price']*100,
						'currency' => array(
							'meta' => $this->currency_meta,
						),
						"priceType" => 'Цена продажи',
					);
				}
				if (isset($data['zprice']) && $data['zprice']) {
					$queryData['buyPrice'] = array(
						'value' => $data['zprice']*100,
						'currency' => array(
							'meta' => $this->currency_meta,
						),
					);
				}
				if (isset($data['msklad_image_data']) && isset($data['msklad_img_name']) && $data['msklad_image_data']) {
					$queryData['image'] = array(
						'filename' => $data['msklad_img_name'],
						'content' => $data['msklad_image_data'],
					);
				}
				$res_array[] = $queryData;
			}
		}
		
		if (count($res_array)) {
			$res_update = json_encode($res_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			file_put_contents('data/querydata_update.txt', $res_update);

			$link = $this->sklad_link . $this->link_add_edit;
			$curl = curl_init($link);
			curl_setopt($curl, CURLOPT_URL, $link . $id);
			curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_VERBOSE, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $res_update);
			
			$result = curl_exec($curl);	

			$this->do_delay('multi_update');
			
			if (!$result) {
				$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
				$result_arr['rows_failed'] = count($res_array);
			} else {
				
				if (isset($results->errors)) {
					$this->errors = 'Ошибка сервиса "Мой склад": "' . $results['errors'][0]['error'] . '"';
					$result_arr['rows_failed'] = count($res_array);
				} else {
					$results = json_decode($result, true);
					
					if (is_array($results)) {
						foreach($rows as $key => $data) {
							$found = 0;
							foreach($results as $res_row) {
								if ($res_row['name'] == $data['name'] && $res_row['code'] == $data['art']) {
									$rows[$key]['global_id'] = $res_row['id'];
									unset($rows[$key]['msklad_img_name']);
									unset($rows[$key]['msklad_image_data']);
									unset($rows[$key]['groups_meta']);
									$result_arr['rows_added']++;
									$found = 1;
									break;
								}
							}
							if (!$found) {
								unset($rows[$key]);
								$result_arr['rows_failed']++;
							}
						}
					} else {
						$result_arr['rows_failed'] = count($res_array);
					}
				}
			}
		}
		return $result_arr;
	}
	
	//копирование - пока пусто
	public function copy($ids){
		return false;
	}

	private function rus2translit($st) { 
		$st = mb_strtolower($st, "utf-8"); 
		$st = str_replace(array( 
		'?','!','.',',',':',';','*','(',')','{','}','[',']','%','#','№','@','$','^','-','+','/','\\','=','|','"','\'', 
		'а','б','в','г','д','е','ё','з','и','й','к', 
		'л','м','н','о','п','р','с','т','у','ф','х', 
		'ъ','ы','э',' ','ж','ц','ч','ш','щ','ь','ю','я' 
		), array( 
		'_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_', 
		'a','b','v','g','d','e','e','z','i','y','k', 
		'l','m','n','o','p','r','s','t','u','f','h', 
		'j','i','e','_','zh','ts','ch','sh','shch', 
		'','yu','ya' 
		), $st);
		
		$st = preg_replace("/[^a-z0-9_]/", "", $st); 
		$st = trim($st, '_'); 

		$prev_st = ''; 
		do { 
			$prev_st = $st; 
			$st = preg_replace("/_[a-z0-9]_/", "_", $st); 
		} while($st != $prev_st); 

		$st = preg_replace("/_{2,}/", "_", $st); 
		return $st; 
	}

    //Получение метаданных валюты
	private function get_currency() {

		$temp_arr = array();
		
		$link = $this->sklad_link . $this->link_add_currency . '?limit=1&filter=isoCode=RUB';
		
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);       
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		// get запрос
		curl_setopt($curl, CURLOPT_URL, $link);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);	
		
		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
		} else {
			
			$results = json_decode($result, true);	

	//		echo $results;
			
			if (isset($results['errors'])) {
				$this->errors = 'Ошибка сервиса "Мой склад": "' . $results['errors'][0]['error'] . '"';
			} else {
								
				$temp_arr = $results['rows'][0]['meta'];
			}
		}
		$this->do_delay('get_currency');
		
		return $temp_arr;
	}
	
    //Получение метаданных валюты
	public function add_product_folder($folder_name, $parent_folder_meta) {

		$this->errors = '';
		$temp_arr = array();
		
		$queryData = array(
			'name' => $folder_name,
		);
		if ($parent_folder_meta) {
			$queryData['productFolder'] = array(
				'meta' => $parent_folder_meta,
			);
		}
		$link = $this->sklad_link . $this->link_add_groups;
		
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($queryData));
		
		$result = curl_exec($curl);	
		
		if (!$result) {
			$this->errors = 'Ошибка cURL: ' . (curl_error($curl) ? curl_error($curl) : 'false') . '; Error  No: '. curl_errno($curl); 
		} else {
			
			$results = json_decode($result, true);	

			if (isset($results['errors'])) {
				$this->errors = 'Ошибка сервиса "Мой склад": "' . $results['errors'][0]['error'] . '"';
			} else {								
				$temp_arr = $results['meta'];
			}
		}
		$this->do_delay('add_group');
		
		if ($temp_arr) {
			return $temp_arr;
		} else {
			return false;
		}
	}
	
    //Получение изображения
	private function get_image($href, $filename) {

		$temp_image = '';
		
		$href = str_replace('?miniature=true', '', $href);
		$curl = curl_init($href);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/octet-stream")); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);       
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		// get запрос
		curl_setopt($curl, CURLOPT_URL, $href);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		$result = curl_exec($curl);	
		
		$this->do_delay('get_image');
		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
		} else {
			$info = curl_getinfo($curl); // Получим информацию об операции
			$file = file_get_contents($info['url']);//если редирект есть то скачиваем файл по ссылке
	//		echo $results;
			
			$temp_image = $results;
			file_put_contents('msklad/images/' . $filename, $file);
			return 'msklad/images/' . $filename;
		}
		
		return '';
	}
	
    //Получение id атрибутов
	private function get_attributes() {

		$temp_attributes = array (
			0 => array (
			  'id' => 'd896f2fd-6d75-11e8-9ff4-315000b769d8',
			  'name' => 'Title',
			),
			1 => array (
			  'id' => '218dbfd6-6fa7-11e8-9107-50480002f0a3',
			  'name' => 'Description',
			),
			2 => array (
			  'id' => 'd896ea1b-6d75-11e8-9ff4-315000b769d2',
			  'name' => 'Keywords',
			),
			3 => array (
			  'id' => 'd896fabc-6d75-11e8-9ff4-315000b769de',
			  'name' => 'URL',
			),
			4 => array (
			  'id' => 'd8971103-6d75-11e8-9ff4-315000b769ef',
			  'name' => 'Название для каталога',
			),
			5 => array (
			  'id' => 'b6a853a8-6fa0-11e8-9107-5048000222ea',
			  'name' => 'Категория',
			),
			6 => array (
			  'id' => 'd89707e6-6d75-11e8-9ff4-315000b769e8',
			  'name' => 'Категория 1',
			),
			7 => array (
			  'id' => 'd896f444-6d75-11e8-9ff4-315000b769d9',
			  'name' => 'Тип',
			),
			8 => array (
			  'id' => 'd8970d95-6d75-11e8-9ff4-315000b769ec',
			  'name' => 'Размер',
			),
			9 => array (
			  'id' => 'd896e36e-6d75-11e8-9ff4-315000b769cf',
			  'name' => 'Фотография мини',
			),
			10 => array (
			  'id' => 'd896fe9b-6d75-11e8-9ff4-315000b769e1',
			  'name' => 'Фотография мини 2',
			),
			11 => array (
			  'id' => 'd896e72a-6d75-11e8-9ff4-315000b769d0',
			  'name' => 'Изображение',
			),
			12 => array (
			  'id' => 'd896ffe2-6d75-11e8-9ff4-315000b769e2',
			  'name' => 'Галерея',
			),
			13 => array (
			  'id' => '3f493a27-6d63-11e8-9ff4-315000b2b61d',
			  'name' => 'Размеры',
			),
			14 => array (
			  'id' => '3f493b42-6d63-11e8-9ff4-315000b2b61e',
			  'name' => 'Материал',
			),
			15 => array (
			  'id' => 'd896eb68-6d75-11e8-9ff4-315000b769d3',
			  'name' => 'Цвет',
			),
			16 => array (
			  'id' => '3f4937af-6d63-11e8-9ff4-315000b2b61b',
			  'name' => 'Цвет фурнитуры',
			),
			17 => array (
			  'id' => 'd896e8b5-6d75-11e8-9ff4-315000b769d1',
			  'name' => 'Ручки',
			),
			18 => array (
			  'id' => 'd8970942-6d75-11e8-9ff4-315000b769e9',
			  'name' => 'Застёжка',
			),
			19 => array (
			  'id' => 'd8971032-6d75-11e8-9ff4-315000b769ee',
			  'name' => 'Основные внутренние отделения',
			),
			20 => array (
			  'id' => 'd896ec97-6d75-11e8-9ff4-315000b769d4',
			  'name' => 'В комплекте',
			),
			21 => array (
			  'id' => '84b64367-6e1d-11e8-9109-f8fc0026322e',
			  'name' => 'Показывать',
			),
			22 => array (
			  'id' => '84b64ca6-6e1d-11e8-9109-f8fc00263232',
			  'name' => 'В наличии',
			),
			23 => array (
			  'id' => '84b646af-6e1d-11e8-9109-f8fc0026322f',
			  'name' => 'Акция',
			),
			24 => array (
			  'id' => '84b64838-6e1d-11e8-9109-f8fc00263230',
			  'name' => 'Новинка',
			),
			25 => array (
			  'id' => '84b64a84-6e1d-11e8-9109-f8fc00263231',
			  'name' => 'Редкое',
			),
			26 => array (
			  'id' => '84b64dff-6e1d-11e8-9109-f8fc00263233',
			  'name' => 'Распродажа',
			),
			27 => array (
			  'id' => '3f49311a-6d63-11e8-9ff4-315000b2b61a',
			  'name' => 'Гарантия',
			),
			28 => array (
			  'id' => 'd896eeb7-6d75-11e8-9ff4-315000b769d5',
			  'name' => 'Порядок вывода',
			),
			29 => array (
			  'id' => '1adeff35-6fca-11e8-9ff4-34e80007b201',
			  'name' => 'ID на сайте',
			),
			30 => array (
			  'id' => 'd896fc08-6d75-11e8-9ff4-315000b769df',
			  'name' => 'Класс товара',
			),
			31 => array (
			  'id' => '5b464482-6f98-11e8-9107-5048000150ef',
			  'name' => 'Сайты',
			),
			32 => array (
			  'id' => 'd8c13e43-6d75-11e8-9ff4-315000b76d33',
			  'name' => 'Пол',
			),
			33 => array (
			  'id' => '07b95159-6ecb-11e8-9ff4-34e8002b67aa',
			  'name' => 'Внутрений материал',
			),
			34 => array (
			  'id' => '07b95612-6ecb-11e8-9ff4-34e8002b67ab',
			  'name' => 'Подошва',
			),
			35 => array (
			  'id' => 'd896f444-6d75-11e8-9ff4-1111111111111',
			  'name' => 'Тип 1',
			),
			36 => array (
			  'id' => 'd896f444-6d75-11e8-9ff4-2222222222222',
			  'name' => 'Тип 2',
			),
			37 => array (
			  'id' => 'd896f444-6d75-11e8-9ff4-3333333333333',
			  'name' => 'Тип 3',
			),
			48 => array (
			  'id' => 'd89707e6-6d75-11e8-9ff4-4444444444444',
			  'name' => 'Категория 2',
			),
			49 => array (
			  'id' => 'd89707e6-6d75-11e8-9ff4-5555555555555',
			  'name' => 'Категория 3',
			),
		);
		
		$temp_arr = array();
		
		$link = $this->sklad_link . $this->link_add_attributes;
		
		$curl = curl_init($link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);       
		curl_setopt($curl, CURLOPT_USERPWD, $this->msklad_login . ":" . $this->msklad_pass);       
		// get запрос
		curl_setopt($curl, CURLOPT_URL, $link);       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);	
		
		if (!$result) {
			$this->errors = 'Ошибка CURL: "' . curl_error($curl) . '"';
		} else {
			
			$results = json_decode($result);	
			
			if (isset($results->errors)) {
				$this->errors = $results->errors[0]->error;
			} else {
				
				// массив атрибутов - доп. полей
				$attributes = $temp_attributes;//$results->attributes;
				foreach ($attributes as $attribute) {
//					$temp_arr[$attribute->name] = $attribute->id;
					$temp_arr[$attribute['name']] = $attribute['id'];
				}				
			}
		}
		$this->do_delay('get_attributes');
		
		return $temp_arr;
	}
	private function do_delay($place) {
		$temp_mtime = (microtime(true) - $_SESSION['msklad_microtime']) * 1000000;
		if ($temp_mtime < 70000) {
			usleep(70000 - $temp_mtime);
		}
		$_SESSION['msklad_microtime'] = microtime(true);
	}
}
?>