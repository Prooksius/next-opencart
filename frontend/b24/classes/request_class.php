<?php

/*********************************************************************************
* Описание: Модуль работы формирования POST и GET запросов.
* Организация: Lebedev IP.
* Автор: Лебедев Д.В.
* Система контроля версий
* Версия 1.0.0.1 от 09.10.2018
* Время начала: 11:19
* Комментарий: Добавлен метод GETUrlFormer.
* Время окончания: 11:20
* Версия 1.0.0.2 от 11.07.2019
* Время начала: 14:01
* Комментарий: Добавлен метод POSTSenderFGC, ибо POSTSenderWithParams не корректно
* работает с вложенными массивами.
* Время окончания: 14:03
* Версия 1.0.0.3 от 15.07.2019
* Время начала: 10:54
* Комментарий: Добавлен метод устаревший метод GetUrlFormerByArray для 
* совместимости.
* Время окончания: 10:45
* Версия 1.0.0.4 от 13.09.2019
* Время начала: 20:54
* Комментарий: Добавлен метод put_sender.
* Время окончания: 20:54
* Версия 1.0.0.5 от 19.09.2019
* Время начала: 17:18
* Комментарий: Добавлены методы post_sender, get_sender.
* Время окончания: 17:20
*********************************************************************************/

?>

<?php

	// Main class information // 	
	
	class RequestsClass{
		
		const SoftVertion = '1.0.0.5';
        
		function get_sender($input_url, $params_array, $header_array) {
			
			$get_url = $input_url;
			
			$main_count = 0;
			foreach ($params_array as &$param) {
				$get_url = $get_url.(($main_count == 0) ? '?' : '&').array_search($param, $params_array).'='.$param;
				$main_count++;
			}
			unset($param);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $get_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header_array);
			$resp = curl_exec($curl);
			curl_close($curl);
			return $resp;
			
		}
        
		function post_sender($input_url, $data_array, $header_array) {
				
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_URL, $input_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data_array));
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header_array);
			$resp = curl_exec($curl);
			echo $out;
			curl_close($curl);
			return $resp;
			
		}

		function put_sender($input_url, $data_array, $header_array) {
            
            // array('Content-Type: application/json','Content-Length: ' 
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $input_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_array)));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_array);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response  = curl_exec($curl);
            curl_close($curl);
			return $response;
		}
		
		// Get url former //
		
		function GETUrlFormer($input_url, $params_array) {
			
			$get_url = $input_url;
			
			$main_count = 0;
			foreach ($params_array as &$param) {
				$get_url = $get_url.(($main_count == 0) ? '?' : '&').array_search($param, $params_array).'='.$param;
				$main_count++;
			}
			unset($param);

			return $get_url;
		}	
			
		// Метод отправки POST запроса через curl //
			
		function GETSenderWithParams($input_url, $params_array, $header_array) {
			
			$get_url = $input_url;
			
			$main_count = 0;
			foreach ($params_array as &$param) {
				$get_url = $get_url.(($main_count == 0) ? '?' : '&').array_search($param, $params_array).'='.$param;
				$main_count++;
			}
			unset($param);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $get_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header_array);
			$resp = curl_exec($curl);
			curl_close($curl);
			return $resp;
			
		}
		
		// GET Url former //
		
		function GETUrlByParamsArrayFormer($input_url, $params_array) {
			
			$get_url = $input_url;
			
			$main_count = 0;
			foreach ($params_array as &$param) {
				$get_url = $get_url.(($main_count == 0) ? '?' : '&').array_search($param, $params_array).'='.$param;
				$main_count++;
			}
			unset($param);
			
			return $get_url;
			
		}
		
		// Отправка POST запроса через curl //
				
		function POSTSenderWithParams($input_url, $data_array, $header_array) {
				
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_URL, $input_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data_array);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header_array);
			$resp = curl_exec($curl);
			echo $out;
			curl_close($curl);
			return $resp;
			
		}
        
		// Отправка POST запроса через file_get_contents //
				
		function POSTSenderFGC($input_url, $data_array, $header_array) {
				
                $result = file_get_contents($input_url, false, stream_context_create(array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => $header_array,  //'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($data_array)
                    )
                )));
                
			return $result;
		}
        
        // Формирование url для GET-запроса //
        
        function GetUrlFormerByArray($get_link, $params_array) {
            
            $get_url = $get_link;
            
            $for_count = '0';
            foreach ($params_array as &$param) {
                $get_url = $get_url.(($for_count == 0) ? '?' : '&').array_search($param, $params_array).'='.$param;
                $for_count++;
            }
            unset($param);
        
            return $get_url;
        }	        
		
	}
	
?>