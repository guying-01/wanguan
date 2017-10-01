<?PHP
/**
 * @Description    通用函数文件
 * @Author         William Gao(23217924@qq.com)
**/

require_once("connect.php");
//require_once( dirname( __FILE__ ) . '/../Excel/reader.php' );

/**
 * 计算优秀率
 * @param array $all_score    所有成绩
 * @param int   $total_score  总成绩
 * @return float 优秀率
**/
function getExcellentRate( $all_score, $total_score ) {
    $total = count( $all_score );
    $excellent_count = getOverValueCount( $all_score, EXCELLENT_RATE * $total_score );
    return sprintf( '%.2f', ( $excellent_count / $total ) * 100 );
}

/**
 * 计算及格率
 * @param  array $all_score    所有成绩
 * @param  int   $total_score  总成绩
 * @return float 标准差值
**/
function getPassRate( $all_score, $total_score ) {
    $total = count( $all_score );
    $pass_count = getOverValueCount( $all_score, PASS_RATE * $total_score );
    return sprintf( '%.2f', ( $pass_count / $total ) * 100 );
}

/**
 * 计算平均分
 * @param  Array   $all_score 所有成绩
 * @param  Bool    $flag      是否返回格式化后的浮点数，默认为false，不返回
 * @return float   平均分
**/
function getAverage( $all_score, $flag = false ) {
    if ( $flag ) {
        return array_sum( $all_score ) / count( $all_score );
    } else {
        return sprintf( '%.2f', array_sum( $all_score ) / count( $all_score ) );
    }
}

/**
 * 计算极差
 * @param  Array $all_score 所有成绩
 * @return int   极差
**/
function getRange( $all_score ) {
    return max( $all_score ) - min( $all_score );
}

/**
 * 计算标准差
 * @param  float $avg  平均值
 * @param  Array $list 队列数组
 * @return float 标准差值
**/
function getVariance( $avg, $list ) {
    $total_var = 0;
    foreach ($list as $lv){
         $total_var += pow( ($lv - $avg), 2 );
    }
    
    return sqrt( $total_var / (count($list) - 1 ) );
}

/**
 * 计算差异系数
 * @param float $avg 平均值
 * @param Array $list 队列数组
 * @return Array 所有成绩
**/
function getCV( $avg, $list ) {
    return sprintf( '%.2f', getVariance( $avg, $list ) / $avg );
}

/**
 * 计算等级
 * @param float $avg 平均值
 * @param Array $list 队列数组
 * @return Array 所有成绩
**/
function getLevel( $all_score, $total_score ) {
    $level = array();
    $level['A'] = getExcellentRate( $all_score, $total_score );
    $level['C'] = sprintf( '%.2f', 100.00 - getPassRate( $all_score, $total_score ) );
    $level['B'] = sprintf( '%.2f', 100.00 - $level['A'] - $level['C'] );
    return $level;
}

/**
 * 计算T分数
 * @param  String  $exam_time 计算T分数的考试时间
 * @return bool    $t_scroe   T分数
**/
function getTScore( $average, $zone_average, $variance ) {
    $t_scroe = 0;
    $t_scroe = sprintf( '%.2f', ( ( $average - $zone_average ) / $variance ) * 10 + 50 );
    return $t_scroe;
}

/**
 * 计算全科及格率
 * @param  Array   $all_scores 所有的成绩数组
 * @param  Array   $fullMarks  各科的满分数组
 * @return float   全科及格率
**/
function getAllPass( $all_scores, $fullMarks ) {
    $total = count( $all_scores );
    $pass = 0;
   
    /**
     * Changed by Jarod qi 2014-09-18 :filter the class 3(english) and class 10(japnese)
    **/

    //判断考试中是否同时包含日语和英语
    if ( array_key_exists( "3", $fullMarks ) and array_key_exists( "10", $fullMarks ) ) {
        //用于过滤英语的临时数组
        $ri = $fullMarks;
        //用于过滤日语的临时数组
        $ying = $fullMarks;
        //过滤掉英语计算全及格人数
        $key = 3;
        foreach ( $ri AS $k=> $v ) {
            if( $k == $key ) {
                unset( $ri[$k] );
            }
        }
        foreach ( $all_scores AS $s ) {
            $flag = true;
            foreach ( $ri AS $k => $v ) {
                if ( $s[$k] < $v * PASS_RATE ) {
                    $flag = false;
                    break;
                }
            }
            if ( $flag ) {
                $pass++;
            }
        }
        //过滤掉日语计算全科及格人数
        $key = 10; 
        foreach ( $ying AS $k => $v ) {
            if ( $k == $key ) {
                unset( $ying[$k] );    
            }
        }
        foreach ( $all_scores AS $s ) {
            $flag = true;
            foreach ( $ying AS $k => $v ) {
                if ( $s[$k] < $v * PASS_RATE ) {
                    $flag = false;
                    break;
                }
            }
            if ( $flag ) {
                $pass++;
            }
        }

    } else {
        foreach ( $all_scores AS $s ) {
            $flag = true;
            foreach ( $fullMarks AS $k => $v ) {
                if ( $s[$k] < $v * PASS_RATE ) {
                    $flag = false;
                    break;
                }
            }
            if ( $flag ) {
                $pass++;
            }
        }
    }
    /**
     * End by Jarod qi
    **/

    return sprintf( '%.2f', ( $pass / $total ) * 100 );
}

/**
 * 获取所有成绩数据，并以数组返回
 * @param  string  $file  包含成绩的xls文件
 * @param  bool    $flag  是否按xls文件的行与列返回数组，默认false。 true -- 按行列返回二维数组, false -- 返回一维数组
 * @param  string  $type  上传文件所属类型, 默认s。 s -- 成绩文件， a -- 全科及格率文件， d -- 双向细目表文件，e -- 试卷分析文件
 * @return Array   所有成绩
**/
function getAllScoreAsArray( $file, $flag = false, $type = 's' ) {
    $data = new Spreadsheet_Excel_Reader();
    $data->setOutputEncoding('UTF-8');
    $data->setUTFEncoder('mb');
    $data->read( dirname( __FILE__ ) . '/../' . $type . 'File/' . trim( $file ) );

    $excel_obj = $data->sheets;
    $all_score = array();
    $sheets = count( $excel_obj );

    $rows = $excel_obj[0]['numRows'];
    $cols = getNotNullCount( $excel_obj[0]['cells'][1] );
    for( $j = 1; $j <= $rows; $j++ ) {
        for( $k = 1; $k <= $cols; $k++ ) {
            if ( $flag ) {
                //changed by Jarod qi 过滤空行的数据
                if ( $excel_obj[0]['cells'][$j][$k] == '' ) {
                    if ( $type == 's' || $type == 'a' ) {
                        unset( $all_score[$j] );
                    }
                } else {
                    $all_score[$j][$k] = $excel_obj[0]['cells'][$j][$k];
                }
                //End by Jarod qi 
            } else {
                $all_score[] = $excel_obj[0]['cells'][$j][$k];
            }
        }
    }

    return $all_score;
}

/**
 * 获取数组非空元素数量
 * @param array  $array 给定数组
 * @return int   元素数量
**/
function getNotNullCount( $array ) {
	$n = count( $array );
	for( $i = $n - 1; $i >= 0; $i-- ) {
		if ( empty( $array) ) {
			array_pop( $array );
		} else {
			return count( $array );
		}
	}
}

/**
 * 获取数组中大于某个值的元素数量
 * @param array  $array 给定数组
 * @param int    $int   给定数值
 * @return int   元素数量
**/
function getOverValueCount( $array, $int ) {
    $count = 0;
    foreach ( $array AS $v ) {
        if ( $v >= $int ) {
            $count++;
        }
    }
    return $count;
}

/**
 * 根据当前月份获取学年与学期
 * @return  string   学年学期
**/
function getTrimester() {
    $month = date( "n" );
    $year = date( "Y" );
    if ( $month >= 4 && $month < 10 ) {
        return ( $year - 1 ) . '-' . $year . '学年下半学期';
    } else {
        return $year . '-' . ( $year + 1 ) . '学年上半学期';
    }
}

/**
 * 根据当前月份获取学年与学期
 * @return  int  1/2
**/
function getTrimesterNumber() {
    $month = date( "n" );
    if ( in_array( $month, array( 5, 6, 7, 10, 11, 12 ) ) ) {
        return 1;
    } else {
        return 2;
    }
}

/**
 * 更新学校信息
 * @param array  $id    数组键值
 * @param array  $array 需要修改的数组信息( key, value )
 * @return bool  true/false
**/
function updateSchool( $id, $array = array() ) {
    global $all_schools;
    if ( empty( $array ) ) {
        return true;
    }
    foreach ( $array AS $k => $v ) {
        if ( isset( $all_schools[$id][$k] ) && !empty( $v ) ) {
            echo $k . ' => ' . $v;
            $all_schools[$id][$k] = $v;
        }
    }
    return updateInformationFile( 'school', $all_schools );
}

/**
 * 更新信息文件
 * @param  array $type  信息文件类型
 * @param  int   $array 写入文件的数组
 * @return bool  true/false
**/
function updateInformationFile( $type, $array ) {
    $file = dirname( __FILE__ ) . '/all_'.$type.'s.php';
    
    $file_content = "<?PHP\n\treturn array(\n\t\t";
    if( is_array( $array ) && !empty( $array ) ) {
        foreach ( $array AS $id => $a ) {
            $file_content .= "'" . $id . "' => array(\n";
            foreach ( $a AS $k => $v ) {
                $file_content .= "\t\t\t'" . $k . "' => '" . $v . "',\n";
            }
            $file_content .= "\t\t),\n\t\t";
        }
    }
    $file_content .= "\n\t);\n?>";

    $fh = fopen( $file, 'w+' );
    $bool = fwrite( $fh, $file_content );
    fclose( $fh );

    return $bool;
}

/**
 * 通过code或者name查找学校ID
 * @param  String $type  code/name
 * @param  String $value code或者name的值
 * @return String $id 学校ID
**/
function findSchoolId( $type, $value ) {
    global $all_schools;

    $i = 0;
    $id = '';
    foreach ( $all_schools AS $k => $v ) {
        if ( $v[$type] == $value ) {
            $id = $k;
            $i++;
        }
    }
    if ( $i > 1 ) {
        return array( 'error' => "有具有相同代码的学校，请核对！" );
    }
    if ( $id == '' ) {
        return array( 'error' => "没有找到此代码指定的学校，请核对！" );
    }

    return $id;
}

/**
 * 创建数据库连接
 * @return  resource  $conn MySQL数据库连接资源
**/
function createMySQLConnect() {
    $conn = connect_main();
    //mysql_query( "SET NAMES " . MYSQL_CHAR );
    //mysql_query( "USE " . MYSQL_NAME );
    return $conn;
}

/**
 * 创建数据库连接
 * @param  string   $sql    SQL查询语句
 * @param  resource $conn   MySQL数据库连接资源
 * @return array    $result 查询结果(ACOSS数组)
**/
function fetchAll( $sql, $conn = '' ) {
    if ( $conn == '' ) {
        $flag = true;
        $conn = createMySQLConnect();
    }

    $tmp = mysql_query( $sql, $conn ) or die( '无法完成查询' );
    $result = array();
    while ( $temp = mysql_fetch_assoc( $tmp ) ) {
        array_push( $result, $temp );
    }

    if ( $flag ) {
        closeMySQLConnect( $conn );
    }
    return $result;
}

/**
 * 创建数据库连接
 * @param  string   $sql    SQL查询语句
 * @param  resource $conn   MySQL数据库连接资源
 * @return string   $result 查询结果
**/
function fetchOne( $sql, $conn = '' ) {
    $flag = false;
    if ( $conn == '' ) {
        $flag = true;
        $conn = createMySQLConnect();
    }

    $tmp = mysql_query( $sql, $conn ) or die( '无法完成查询' );
    $result = '';
    $temp = mysql_fetch_array( $tmp, MYSQL_NUM );
    $result = $temp[0];

    if ( $flag ) {
        closeMySQLConnect( $conn );
    }
    return $result;
}

/**
 * 关闭数据库连接
 * @param  resource $conn  MySQL数据库连接资源
 * @return bool   true/false
**/
function closeMySQLConnect( $conn ) {
    return mysql_close( $conn );
}

/**
 * 验证错误信息并显示
 * @param  array  $error  包含错误信息的数组
 * @return N/A
**/
function validateError( $error ) {
    if ( !empty( $error ) ) {
        echo '<div class="spacer"></div><fieldset class="infor_zone"><label>错误</label><br /><br />';
        $err_no = 1;
        foreach ( $error AS $v ) {
            echo '<div class="error_info">'.$err_no++.'. '.$v.'</div>';
        }
        echo '</fieldset>';
    }
}

/**
 * 验证成功信息并显示
 * @param  array  $success  包含错误信息的数组
 * @return N/A
**/
function validateSuccess( $success ) {
    if ( !empty( $success ) ) {
        echo '<div class="spacer"></div><fieldset class="infor_zone"><label>操作成功</label><br /><br />';
        $suc_no = 1;
        foreach ( $success AS $v ) {
            echo '<div class="notice">'.$suc_no++.'. '.$v.'</div>';
        }
        echo '</fieldset>';
    }
}

/**
 * 格式化考试时间为YYYY-MM-DD格式
 * @param  string $string       未格式化前的时间字符串
 * @return string $exam_time    YYYY-MM-DD格式的时间
**/
function getExamTime( $string ) {
    $exam_time = false;
    if ( preg_match( "/^\d{4}-\d{2}-\d{2}$/", $string ) ) {
        $exam_time = $string;
    } elseif ( preg_match( "/^(\d{4})(\d{2})(\d{2})$/", $string, $m ) ) {
        $exam_time = $m[1] . '-' . $m[2] . '-' . $m[3];
    } elseif ( preg_match( "/^(\d{4})\/(\d{2})\/(\d{2})$/", $string, $m ) ) {
        $exam_time = $m[1] . '-' . $m[2] . '-' . $m[3];
    }
    return $exam_time;
}

/**
 * 获取某次考试的ID
 * @param  string   $time       考试的时间 
 * @param  string   $class      参加考试的年级
 * @param  resource $conn       数据库资源，默认为空
 * @return string   考试的ID
**/
function getExamId( $time, $class, $conn = '' ) {
    $sql = "SELECT id FROM " . MYSQL_TABLE_PREFIX . "exams WHERE time='".$time."' AND class='".$class."' LIMIT 1";
    return fetchOne( $sql, $conn );
}

/**
 * 获取某次考试的ID
 * @param  string   $start      查询考试的开始时间
 * @param  string   $end        查询考试的结束时间 
 * @param  string   $class      参加考试的年级
 * @param  resource $conn       数据库资源，默认为空
 * @return string   考试的ID
**/
function getExamIdByTime( $start, $end, $class, $conn = '' ) {
    $sql = "SELECT id FROM " . MYSQL_TABLE_PREFIX . "exams WHERE time>='".$start."' AND time <= '".$end."' AND class='".$class."'";
    $tmp = fetchAll( $sql, $conn );
    foreach ( $tmp AS $v ) {
        $exams[] = $v['id'];
    }
    return $exams;
}

/**
 * 获取某年级某学科的满分成绩
 * @param  string   $course    学科
 * @param  string   $class     年级
 * @param  string   $conn      MySQL连接资源
 * @return string   总分
**/
function getFullMarks( $course, $class, $conn = '' ) {
    $sql = "SELECT fullMarks FROM " . MYSQL_TABLE_PREFIX . "fullmarks WHERE course='".$course."' AND class='".$class."' LIMIT 1";
    return fetchOne( $sql, $conn );
}

/**
 * 根据学校名称来获取学校的ID值
 * @param  string   $name      学校名称
 * @return string   学校ID
**/
function getSchoolIdByName ( $name ) {
    global $all_schools;
    $school_ids = array_flip( $all_schools );
    return $school_ids[$name];
}

/**
 * 双向细目表纪录排序专用函数
**/
function forTWDSort ( $a, $b ) {
    if ( $a['id'] == $b['id'] ) {
        return 0;
    }
    return ( $a['id'] < $b['id'] ) ? -1 : 1;
}

/**
 * 输出绘制T分数图表的JS函数
**/
function creatTScoreGraph ( $cate, $title, $data, $id ) {
    $function_name = 'drawTScore_' . rand( 1, 10000000 );
    $function = "function " . $function_name . " () { $('#".$id."').highcharts({ xAxis: { categories: ['".implode("','", $cate)."'] }, title: { text: '".$title."T分数图表'},".
                "yAxis: { min: 0, title: { text: 'T分数' } }, tooltip: { pointFormat: '<span style=".'"color:{series.color}"'.">{series.name}</span>: <b>{point.y}</b>',shared: true }, ".
                "plotOptions:{scatter:{dataLabels:{enabled:true},enableMouseTracking:false}},series: [ { type: 'scatter', name: 'T分数',data: [".implode(',', $data)."], marker: { radius: 4 } } ] }); } " . $function_name . "();";
    echo $function;
}

/**
 * 输出绘制差异系数图表的JS函数
**/
function creatCVGraph ( $cate, $title, $data, $id ) {
    $function_name = 'drawCV_' . rand( 1, 10000000 );
    $function = "function " . $function_name . " () { $('#".$id."').highcharts({chart: {type: 'line'},title: {text: '".$title."差异系数图表'},".
                "xAxis: {categories: ['".implode("','", $cate)."']},yAxis: {min: 0,title: {text: '差异系数'}},".
                "plotOptions: {line: {dataLabels: {enabled: true },enableMouseTracking: false}},series: [{name: '差异系数',".
                "data: [".implode(',', $data)."]}]});} " . $function_name . "();";
    echo $function;
}

/**
 * 输出绘制等级图表的JS函数
**/
function creatLevelGraph ( $cate, $title, $dataA, $dataB, $dataC, $id ) {
    $function_name = 'drawLevel_' . rand( 1, 10000000 );
    $function = "function " . $function_name . " () { $('#".$id."').highcharts({chart: {type: 'column'},title: {text: '".$title."等级图表'},xAxis: {categories: ['".implode("','",$cate)."']},yAxis: {min: 0,title: {text: '等级分数百分比'}},".
                "tooltip: {pointFormat: '<span style=".'"color:{series.color}"'.">{series.name}</span>: <b>{point.y}%</b><br/>',shared: true},plotOptions: {column: {stacking: 'percent',enableMouseTracking:false,dataLabels:{enabled: true,format:'{point.y}%'}}},".
                "series: [{name: '等级A',color: '#CC0000',data: [".implode(',',$dataA)."]},{name: '等级B', color: '#5B9BD5', data: [".implode(',',$dataB)."]},{name: '等级C', color: '#ED7D31', data: [".implode(',',$dataC)."]}]});} " . $function_name . "();";
    echo $function;
}

/**
 * 输出绘制全科及格率图表的JS函数
**/
function creatAllPass ( $cate, $title, $all_pass_datas, $id ) {
    $function_name = 'drawAllPass_' . rand( 1, 10000000 );
    $function = "function " . $function_name . "() { $('#show_zone_all_pass_" . $id . "').highcharts({ chart: { type: 'column' }," .
                "title: { text: '" . $title . "' }, xAxis: { categories: ['" . implode( "','", $cate ) . "'] }," .
                "yAxis: { min: 0, title: { text: '全科及格率（%）' } }, tooltip: { pointFormat: '<span style=" . '"color:{series.color}"' . 
                ">{series.name}</span>: <b>{point.y}%</b>', shared: true },plotOptions:{column:{dataLabels:{enabled:true},enableMouseTracking:false}}," .
                "series: [ { name: '全科及格率', data: [" . implode( ',', $all_pass_datas ) . "]}]});} " . $function_name . "();";
    echo $function;
}

/**
 * 输出绘制成绩箱线图表的JS函数
**/
function creatBoxesGraph ( $title, $all_datas, $exam, $course, $class, $id ) {
    $function_name = 'drawBoxes_' . rand( 1, 10000000 );
    $cate = array();
    $data = '';
    $sub_data = '';
    $i = 0;
    foreach ( $all_datas AS $sc => $v ) {
        $cate[$i] = $v;
        $sql = "SELECT score FROM " . MYSQL_TABLE_PREFIX . "scores WHERE school = '" . $sc . "' AND class = '" . $class . "' AND course = '" . $course . "' AND exam = '" . $exam . "' ORDER BY score ASC";
        $tmp = fetchAll( $sql );
        $all_scores = array();
        foreach ( $tmp AS $v ) {
            $all_scores[] = $v['score'];
        }
        sort( $all_scores );
        $avg = getAverage( $all_scores );
        $min = min( $all_scores );
        $max = max( $all_scores );
        $sub_data .= "[$i," . $min . "],[$i," .$avg . "],[$i," . $max . '],'; 
        $len = count( $all_scores );
        $one = $two = $three = $four = $five = 0;
        $one = $all_scores[round($len*0.05)];
        $two = $all_scores[round($len*0.25)];
        if ( $len % 2 == 0 ) {
            $three = ( $all_scores[$len/2] + $all_scores[$len/2 - 1] ) / 2;
        } else {
            $three = $all_scores[intval($len/2)];
        }
        $four = $all_scores[round($len*0.75)];
        $five = $all_scores[round($len*0.95)];
        $data .= '[' . $one . ', ' . $two . ', ' . $three . ', ' . $four . ', ' . $five . '],';
        $i++;
    }
    $data = substr( $data, 0, -1 );
    $sub_data = substr( $sub_data, 0, -1 );
    $function = "function " . $function_name . "() { $('#".$id."').highcharts({chart: {type: 'boxplot'},title: {text: '".$title."成绩箱线图'}," .
                "legend: {enabled: false},xAxis: {categories: ['".implode("','", $cate)."'],title: {text: '成绩箱线图'}}," .
                "yAxis: {title: {text: '成绩分数'},},series: [{name: '成绩箱线',data: [".$data."],},{name: null," .
                "color: Highcharts.getOptions().colors[0],type: 'scatter',data: [".$sub_data."],marker: {fillColor: 'white',lineWidth: 1,lineColor:" .
                "Highcharts.getOptions().colors[0],radius: 2},enableMouseTracking:false,dataLabels: {enabled: true}}]});} " . $function_name . "();";
    echo $function;
}

/**
 * 输出绘制成绩分段图表的JS函数
**/
function creatSegmentalGraph ( $title, $all_datas, $id, $flag = true ) {
    $function_name = 'drawSegmental_' . rand( 1, 10000000 );
    $data = '';
    $format = $flag ? ",format: '{point.y:.2f}%'" : '';
    $y_title = $flag ? '占总分百分比' : '人数';
    ksort( $all_datas, SORT_NUMERIC );
    foreach ( $all_datas AS $s => $v ) {
        $data .= '{ name:"' . $v['name'] . '", y:' . $v['y'] . ', drilldown: ' . $v['drilldown'] . '},';
    }
    $data = substr( $data, 0, -1 );
    $function = "function " . $function_name . "() { $('#".$id."').highcharts({chart: {type: 'column'},title: {text: '".$title."'},xAxis: {type: 'category', labels:{rotation: -45,style:{fontSize:'10px',fontWeight:500}}},yAxis: {title: " .
                "{text: '".$y_title."'}},legend: {enabled: false},plotOptions: {series: {borderWidth: 0,enableMouseTracking:false,dataLabels: {enabled: true".$format."}}}," .
                "tooltip: {headerFormat: '<span style=".'"font-size:11px"'.">{series.name}</span><br>',pointFormat: '<span style=".'"color:{point.color}"'.">" .
                "{point.name}</span>成绩<b>占总数的{point.y:.2f}%</b><br/>'}, series: [{name: '成绩分段图',colorByPoint: true,data: [".$data."]}]}) }" . $function_name . "();";
    echo $function;
}




?>