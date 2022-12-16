<?php

/*********************************************************************************
* Описание: Модуль вспомогательных отадочных методов
* Организация: Lebedev IP.
* Автор: Лебедев Д.В.
* Система контроля версий
* Версия 1.0.0.1 от 11.07.2019
* Время начала: 14:08
* Комментарий: Добавлен метод ReadDataFromFile.
* Время окончания: 14:08
* Версия 1.0.0.2 от 24.07.2019
* Время начала: 11:07
* Комментарий: Добавлен метод html_wrap_former.
* Время окончания: 11:07
* Версия 1.0.0.3 от 24.07.2019
* Время начала: 11:14
* Комментарий: Исправлена ошибка в html_wrap_former.
* Время окончания: 11:14
* Версия 1.0.0.4 от 27.08.2019
* Время начала: 13:38
* Комментарий: Добавлен метод read_data_from_file.
* Время окончания: 13:39
* Версия 1.0.0.5 от 30.08.2019
* Время начала: 13:57
* Комментарий: Добавлен метод write_data_to_file.
* Время окончания: 13:57
* Версия 1.0.0.6 от 30.08.2019
* Время начала: 19:35
* Комментарий: Добавлен метод rus_2_translit.
* Время окончания: 13:57
* Версия 1.0.0.7 от 29.09.2019
* Время начала: 21:58
* Комментарий: Добавлен метод zero_adder.
* Время окончания: 21:58
* Версия 1.0.0.8 от 08.11.2019
* Время начала: 12:09
* Комментарий: Добавлен метод utf_8_for_web_printer.
* Время окончания: 12:09
*********************************************************************************/

?>

<?php

    class HelpClass{
		
		CONST MODULE_VERTION = '1.0.0.8';
		
		function utf_8_for_web_printer($text_for_print) {
			
		    $header_text = '<html><head><meta charset="UTF-8"></head>';
			$footer_text = '<body></body></html>';
			
			print_r($header_text);  print_r('<pre>'); print_r($text_for_print); print_r('</pre>'); print_r($footer_text); 
			
		}
        
        function zero_adder($input_string, $symbol_amount, $symbol, $dir_flag){
            $input_string_tmp = $input_string;
            while(strlen($input_string_tmp) < $symbol_amount) {
                $input_string_tmp = ($dir_flag == 0) ? $input_string_tmp.$symbol : $symbol.$input_string_tmp;
            }
            return $input_string_tmp;
        }       
        
        function rus_2_translit($string) {
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
            );
            return strtr($string, $converter);
        }
        
        function write_data_to_file($file_path, $data_array) {

            $data_array = serialize($data_array);
            file_put_contents($file_path, $data_array);
            $fp = fopen ($file_path, "w"); // Открытие файла на чтение
            fwrite($fp, $data_array);
            fclose($fp);
            return $data_array;
            
        }        
        
        function read_data_from_file($file_path, $serial_mode) {    

            $data = file_get_contents($file_path);
            $data_array = ($serial_mode == 0) ? ($data) : unserialize($data);
            return $data_array;
            
        }

        function GETRequestSaverToFile($file_name, $data_array) {    

            $data_array = serialize($data_array);
            
            // Запись в файл общий файл //

            file_put_contents($file_name, $data_array);
            // Запись в уникальный файл //
            $fp = fopen ($file_name, "w"); // Открытие файла на чтение
            fwrite($fp, $data_array);
            fclose($fp);
            
        } 
        
        function ReadDataFromFile($file_path) {    

            // Чтение.
            $data = file_get_contents($file_path);
            $data_array = unserialize($data);
            
            return $data_array;
            
        }
		
		function html_wrap_former($hedader_or_footer) {
			
			$header_html = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<!--<meta http-equiv="refresh" content="15">-->
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			</head>
			<body style="background: #FFFFFF; color: #000000;"><!--color: #00FF90;">-->
			';
			
			$footer_html = '
			</body>
			</html>';
			
			if ($hedader_or_footer == '0') {
				print_r($header_html);
			}
			else if($hedader_or_footer =='1'){
				print_r($footer_html);
			}	
			
			return true;
		}
    
    }

?>

