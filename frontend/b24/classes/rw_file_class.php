<?

/*********************************************************************************
* Описание: Класс для чтения / записи файлов и имитации работы с БД.
* Организация: Lebedev IP.
* Автор: Лебедев Д.В.
* Система контроля версий
* Версия 1.0.0.0 от 10.12.2020
* Время начала: 09:00
* Комментарий: Создан отдельный класс.
* Время окончания: 09:00
*********************************************************************************/
            
            // Classes //
            
            class rw_file_class {

                public function __construct() {
                    
                    $this -> LOCK_FILE_PATH = 'lock.lck';
                    $this -> RESULT_FILE_DIR_PATH = 'db/';
                    $this -> LOCK_FILE_CHECK_TIMES = 5;
                    $this -> LOCK_FILE_CHECK_INTERVAL = 2;
                    $this -> DB_FILE_NAME = 'main.db';
                    
                }
                
                public static function data_write_to_db_file($file_data) {
                    
                    try{
                        
                        if (file_exists(RESULT_FILE_DIR_PATH.LOCK_FILE_PATH)){
                            for ($repeat_count = 1; $repeat_count <= LOCK_FILE_CHECK_TIMES; $repeat_count++) {
                                sleep(LOCK_FILE_CHECK_INTERVAL);
                            }
                            unlink(RESULT_FILE_DIR_PATH.LOCK_FILE_PATH);
                        }
                        else {
                            
                            self :: write_data_to_file(RESULT_FILE_DIR_PATH.LOCK_FILE_PATH, array());
                            if (file_exists(RESULT_FILE_DIR_PATH.DB_FILE_NAME)){
                                $db_data = self :: read_data_from_file(RESULT_FILE_DIR_PATH.DB_FILE_NAME, 1);
                                $db_data[count($db_data)] = $file_data;
                                self :: write_data_to_file(RESULT_FILE_DIR_PATH.DB_FILE_NAME, $db_data);
                            }
                            else {
                                $db_data['0'] = $file_data;
                                self :: write_data_to_file(RESULT_FILE_DIR_PATH.DB_FILE_NAME, $db_data);
                            }

                            unlink(RESULT_FILE_DIR_PATH.LOCK_FILE_PATH);
                            return $db_data;
                        }
                        
                    } catch (Exception $e) {
                        return('error');
                    }
                    
                } // public static function data_write_in_db_file ($file_data)

                public static function write_data_to_file($file_path, $data_array) {

                    try{

                        $data_array = serialize($data_array);
                        file_put_contents($file_path, $data_array);
                        $fp = fopen ($file_path, "w");
                        fwrite($fp, $data_array);
                        fclose($fp);
                        return $data_array;
                        
                    } catch (Exception $e) {
                        return('error');
                    }

                } // public static function write_data_to_file($file_path, $data_array)
                
                public static function read_data_from_file($file_path, $serial_mode) {    

                    try{

                        $data = file_get_contents($file_path);
                        $data_array = ($serial_mode == 0) ? ($data) : unserialize($data);
                        return $data_array;
                        
                    } catch (Exception $e) {
                        return('error');
                    }

                }
               
            }   // class data_to_file_class
?>