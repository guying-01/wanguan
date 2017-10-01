<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->load->helper('download');

		require_once (APPPATH . 'libraries/phpexcel/PHPExcel.php');
    }
	
	//客户
	public function downloadtemplate1() {
		$info = read_file('./data/download/customer.xls');
		$this->common_model->logs('下载文件名:customer.xls');
		force_download('customer.xls', $info); 
	}
	
	//供应商
	public function downloadtemplate2() {
		$info = read_file('./data/download/vendor.xls');
		$this->common_model->logs('下载文件名:vendor.xls');
		force_download('vendor.xls', $info); 
	}
	
	//商品
	public function downloadtemplate3() {
		$info = read_file('./data/download/goods.xls');
		$this->common_model->logs('下载文件名:goods.xls');
		force_download('goods.xls', $info);  
	}

	// 商品类别
	public function downloadtemplate4() {
		$info = read_file('./data/download/category_trade.xls');
		$this->common_model->logs('下载文件名:goods_category.xls');
		force_download('category_trade.xls', $info);
	}
	

	public function findDataImporter() {

	}
	
	//上传文件
	public function upload()
	{
		if (!isset($_FILES['import_file']) || $_FILES['import_file']['size'] <= 0)
		{
			return;
		}

		$category = $this->input->post('category', TRUE);
		$category = strtolower($category);
		$method = 'import' . ucfirst($category);
		if (method_exists($this, $method))
		{
			$dir = dirname($_SERVER['SCRIPT_FILENAME']) . '/data/import/' . $category . '/';

			if (!is_dir($dir))
			{
				mkdir($dir, 0755, true);
			}
			$file = $dir . $category . '_' . md5($_FILES['import_file']['name'] . time()) . '.' . end(explode('.', $_FILES['import_file']['name']));

			move_uploaded_file($_FILES['import_file']['tmp_name'], $file);
			$data = $this->readFile($file);
			$this->$method($data, $file);
		}

	}

	private function importCustomer($customers, $file)
	{
		$this->load->library('lib_pinyin');
		$insert = $update = $fail = 0;
		$result = array();
		$count = count($customers);
		for ($num = 2; $num <= $count; $num++)
		{
			$customer = $customers[$num];
			// 根据A列，客户编号来标识客户
			if (empty($customer['A'])) continue;

			// B列，客户名称，必填
			if (empty($customer['B']))
			{
				$fail++;
				$result[$num] = '客户名称不能为空！';
				continue;
			}
			// C列，客户类别，必填
			if (empty($customer['C']))
			{
				$fail++;
				$result[$num] = '客户类别不能为空！';
				continue;
			}

			$data = array();
			$data['number'] 		= $customer['A'];
			$data['name'] 			= $customer['B'];

			$data['cCategory'] 		= $this->mysql_model->get_row(CATEGORY,
				"name = '{$customer['C']}' AND typeNumber = 'customertype' AND 'isDelete' = 0", 'id');
			if (empty($data['cCategory']))
			{
				$fail++;
				$result[$num] = '客户类别不存在！';
				continue;
			}
			$data['cCategoryName'] 	= $customer['C'];

			$data['cLevel'] 		= $this->mysql_model->get_row(CATEGORY,
				"name = '{$customer['D']}' AND typeNumber = 'CustomLevel' AND 'isDelete' = 0", 'id');
			if (empty($data['cLevel']))
			{
				$fail++;
				$result[$num] = '客户等级不存在！';
				continue;
			}
			$data['cLevelName'] 	= $customer['D'];

			$data['beginDate'] 		= $customer['E'] ? date('Y-m-d', intval(($customer['E'] - 25569) * 3600 * 24)) : null;
			$data['amount'] 		= (float)$customer['F'];
			$data['periodMoney'] 	= (float)$customer['G'];
			$data['difMoney']		= $data['amount'] - $data['periodMoney'];
			$data['remark'] 		= $customer['H'];
			$data['pinYin']        	= $this->lib_pinyin->str2pinyin($data['name']);
			$data['contact']       	= $data['number'] . ' ' . $data['name'];

			$data['type'] 			= -10;
			$linkMans = array(array(
				'linkName'			=> $customer['I'],
				'linkMobile'		=> $customer['J'],
				'linkPhone'			=> $customer['K'],
				'linkIm'			=> $customer['L'],
				'province'			=> $customer['M'],
				'city'				=> $customer['N'],
				'county'			=> $customer['O'],
				'address'			=> $customer['P'],
				'linkFirst'			=> $customer['Q'] == '是' ? 1 : 0,
			));
			$data['linkMans']		= json_encode($linkMans);

			$id = $this->mysql_model->get_row(CONTACT, "number = '{$data['number']}' AND isDelete = 0", 'id');

			if ($id)
			{
				if ($this->mysql_model->update(CONTACT, $data, array('id' => $id)))
				{
					$update++;
					$result[$num] = '更新客户成功！';
					$this->common_model->logs('导入更新客户:' . $data['number']);
				}
				else
				{
					$fail++;
					$result[$num] = '更新客户失败！';
				}
			}
			else
			{
				if ($this->mysql_model->insert(CONTACT, $data))
				{
					$insert++;
					$result[$num] = '新增客户成功！';
					$this->common_model->logs('导入新增客户:' . $data['number']);
				}
				else
				{
					$fail++;
					$result[$num] = '新增客户失败！';
				}
			}
		}
		$total = $insert + $update + $fail;
		$return = array();
		$return['status'] = 200;
		$return['data']['total'] = $total;
		$return['data']['insert'] = $insert;
		$return['data']['update'] = $update;
		$return['data']['fail'] = $fail;
		$return['data']['msg'] = "客户导入完毕。<br/>共：{$total}条数据，成功新增：{$insert}条数据，成功更新：{$update}条数据，失败：{$fail}条数据。";
		// 记录日志
		if (!empty($result))
		{
			$result[1] = '结果';
			$this->saveImportResult($file, $result, 'R');
		}
		$result_url = base_url() . substr($file, strlen(dirname($_SERVER['SCRIPT_FILENAME'])) + 1);
		$this->common_model->logs($return['data']['msg'] . $result_url);
		$this->mysql_model->clean();
		$return['data']['url'] = $result_url;
		echo json_encode($return);
	}

	private function importVendor($vendors, $file)
	{
		$this->load->library('lib_pinyin');
		$insert = $update = $fail = 0;
		$result = array();
		$count = count($vendors);
		for ($num = 2; $num <= $count; $num++)
		{
			$vendor = $vendors[$num];

			// 根据A列，供应商编号来标识供应商
			if (empty($vendor['A'])) continue;

			// B列，供应商名称，必填
			if (empty($vendor['B']))
			{
				$fail++;
				$result[$num] = '供应商名称不能为空！';
				continue;
			}
			// C列，供应商类别，必填
			if (empty($vendor['C']))
			{
				$fail++;
				$result[$num] = '供应商类别不能为空！';
				continue;
			}

			$data = array();
			$data['number'] 		= $vendor['A'];
			$data['name'] 			= $vendor['B'];

			$data['cCategory'] 		= $this->mysql_model->get_row(CATEGORY,
				"name = '{$vendor['C']}' AND typeNumber = 'supplytype' AND isDelete = 0", 'id');
			if (empty($data['cCategory']))
			{
				$fail++;
				$result[$num] = '供应商类别不存在！';
				continue;
			}
			$data['cCategoryName'] 	= $vendor['C'];

			$data['beginDate'] 		= $vendor['D'] ? date('Y-m-d', intval(($vendor['D'] - 25569) * 3600 * 24)) : null;
			$data['amount'] 		= (float)$vendor['E'];
			$data['periodMoney'] 	= (float)$vendor['F'];
			$data['difMoney']		= $data['amount'] - $data['periodMoney'];
			$data['remark'] 		= $vendor['G'];
			$data['pinYin']        	= $this->lib_pinyin->str2pinyin($data['name']);
			$data['contact']       	= $data['number'] . ' ' . $data['name'];

			$data['type'] 			= 10;
			$linkMans = array(array(
				'linkName'			=> $vendor['H'],
				'linkMobile'		=> $vendor['I'],
				'linkPhone'			=> $vendor['J'],
				'linkIm'			=> $vendor['K'],
				'province'			=> $vendor['L'],
				'city'				=> $vendor['M'],
				'county'			=> $vendor['N'],
				'address'			=> $vendor['O'],
				'linkFirst'			=> $vendor['P'] == '是' ? 1 : 0,
			));
			$data['linkMans']		= json_encode($linkMans);

			$id = $this->mysql_model->get_row(CONTACT, "number = '{$data['number']}' AND isDelete = 0", 'id');

			if ($id)
			{
				if ($this->mysql_model->update(CONTACT, $data, array('id' => $id)))
				{
					$update++;
					$result[$num] = '更新供应商成功！';
					$this->common_model->logs('导入更新供应商:' . $data['number']);
				}
				else
				{
					$fail++;
					$result[$num] = '更新供应商失败！';
				}
			}
			else
			{
				if ($this->mysql_model->insert(CONTACT, $data))
				{
					$insert++;
					$result[$num] = '新增供应商成功！';
					$this->common_model->logs('导入新增供应商:' . $data['number']);
				}
				else
				{
					$fail++;
					$result[$num] = '新增供应商失败！';
				}
			}
		}
		$total = $insert + $update + $fail;
		$return = array();
		$return['status'] = 200;
		$return['data']['total'] = $total;
		$return['data']['insert'] = $insert;
		$return['data']['update'] = $update;
		$return['data']['fail'] = $fail;
		$return['data']['msg'] = "供应商导入完毕。<br/>共：{$total}条数据，成功新增：{$insert}条数据，成功更新：{$update}条数据，失败：{$fail}条数据。";
		// 记录日志
		if (!empty($result))
		{
			$result[1] = '结果';
			$this->saveImportResult($file, $result, 'Q');
		}
		$result_url = base_url() . substr($file, strlen(dirname($_SERVER['SCRIPT_FILENAME'])) + 1);
		$this->common_model->logs($return['data']['msg'] . $result_url);
		$this->mysql_model->clean();
		$return['data']['url'] = $result_url;
		echo json_encode($return);
	}

	private function importGoods($goodses, $file)
	{
		$this->load->library('lib_cn2pinyin');
		$insert = $update = $fail = 0;
		$result = array();
		$currentGoods = null;
		$count = count($goodses);
		for ($num = 2; $num <= $count; $num++)
		{
			$goods = $goodses[$num];
			// 根据A列，商品编号来标识商品
			if (!empty($goods['A']))
			{
				$currentGoods = null;

				// B列，商品名称，必填
				if (empty($goods['B']))
				{
					$fail++;
					$result[$num] = '商品名称不能为空！';
					continue;
				}
				// E列，商品类别，必填
				if (empty($goods['E']))
				{
					$fail++;
					$result[$num] = '商品类别不能为空！';
					continue;
				}

				// I列，计量单位，必填
				if (empty($goods['I']))
				{
					$fail++;
					$result[$num] = '计量单位不能为空！';
					continue;
				}

				$data = array();
				$data['number'] 		= $goods['A'];
				$data['name'] 			= $goods['B'];
				$data['barCode'] 		= $goods['C'];
				$data['spec'] 			= $goods['D'];

				$category = $this->mysql_model->get_row(CATEGORY,
					"code = '{$goods['E']}' AND typeNumber = 'trade' AND isDelete = 0", '*');
				if (empty($category))
				{
					$fail++;
					$result[$num] = '商品类别不存在！';
					continue;
				}
				$data['categoryId'] 	= $category['id'];
				$data['categoryName']	= $category['name'];

				if (!empty($goods['F']))
				{
					$location = $this->mysql_model->get_row(STORAGE, "locationNo = '{$goods['F']}' AND isDelete = 0 AND disable = 0", '*');
					if (empty($location))
					{
						$fail++;
						$result[$num] = '首选仓库不存在或不可用！';
						continue;
					}
					$data['locationId'] 	= $location['id'];
					$data['locationName']	= $location['name'];
				}

				$data['lowQty'] 		= (float)$goods['G'];
				$data['highQty'] 		= (float)$goods['H'];

				$data['baseUnitId'] 	= $this->mysql_model->get_row(UNIT, "name = '{$goods['I']}' AND isDelete = 0", 'id');
				if (empty($data['baseUnitId']))
				{
					$fail++;
					$result[$num] = '计量单位不存在！';
					continue;
				}
				$data['unitName'] 		= $goods['I'];

				$data['purPrice'] 		= (float)$goods['J'];
				$data['salePrice'] 		= (float)$goods['K'];
				$data['wholesalePrice'] = (float)$goods['L'];
				$data['vipPrice'] 		= (float)$goods['M'];
				$data['discountRate1'] 	= (float)$goods['N'];
				$data['discountRate2'] 	= (float)$goods['O'];
				$data['remark'] 		= $goods['P'];
				$data['pinYin'] 		= $this->lib_cn2pinyin->encode($data['name']);

				$data['properties']		= array();
				$currentGoods = $data;
			}
			if (empty($currentGoods))
			{
				continue;
			}
			// 处理期初数量
			if (!empty($goods['R']))
			{
				$location = $this->mysql_model->get_row(STORAGE, "locationNo = '{$goods['R']}' AND isDelete = 0 AND disable = 0", 'id');
				if (empty($location))
				{
					empty($currentGoods) ? null : $fail++;
					$result[$num] = '仓库不存在或不可用！';
					$currentGoods = null;
					continue;
				}
				$init = array(
					'locationId'	=> $location,
					'qty'			=> (float)$goods['S'],
					'price'			=> (float)$goods['T'],
				);
				if ($currentGoods)
				{
					$currentGoods['properties'][] = $init;
				}
			}

			if ($currentGoods && ($num == $count || !empty($goodses[$num + 1]['A'])))
			{
				// save
				$data = $currentGoods;
				$properties = $data['properties'];
				unset($data['properties']);

				$this->db->trans_begin();
				$id = $this->mysql_model->get_row(GOODS, "number = '{$data['number']}' AND isDelete = 0", 'id');
				$save = false;
				if ($id)
				{
					if ($this->mysql_model->update(GOODS, $data, array('id' => $id)))
					{
						$save = true;
						$update++;
						$result[$num] = '更新商品成功！';
						$this->common_model->logs('导入更新商品:' . $data['number']);
					}
					else
					{
						$fail++;
						$result[$num] = '更新商品失败！';
					}
				}
				else
				{
					if ($id = $this->mysql_model->insert(GOODS, $data))
					{
						$save = true;
						$insert++;
						$result[$num] = '新增商品成功！';
						$this->common_model->logs('导入新增商品:' . $data['number']);
					}
					else
					{
						$fail++;
						$result[$num] = '新增商品失败！';
					}
				}
				if ($save)
				{
					$this->mysql_model->delete(INVOICE_INFO, array('invId' => $id, 'billType' => 'INI'));
					if (!empty($properties))
					{
						$invoice = array();
						foreach ($properties as $property) {
							$invoice[] = array(
								'invId'			=> $id,
								'locationId'	=> $property['locationId'],
								'qty'			=> $property['qty'],
								'price'			=> $property['price'],
								'amount'		=> $property['qty'] * $property['price'],
								'skuId' 		=> 0,
								'billDate'		=> date('Y-m-d'),
								'billNo'		=> '期初数量',
								'billType'		=> 'INI',
								'transTypeName'	=> '期初数量',
							);
						}
						$this->mysql_model->insert(INVOICE_INFO, $invoice);
					}
				}
				if ($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
				}
				else
				{
					$this->db->trans_commit();
				}
				$currentGoods = null;
			}
		}

		$total = $insert + $update + $fail;
		$return = array();
		$return['status'] = 200;
		$return['data']['total'] = $total;
		$return['data']['insert'] = $insert;
		$return['data']['update'] = $update;
		$return['data']['fail'] = $fail;
		$return['data']['msg'] = "商品导入完毕。<br/>共：{$total}条数据，成功新增：{$insert}条数据，成功更新：{$update}条数据，失败：{$fail}条数据。";
		// 记录日志
		if (!empty($result))
		{
			$result[1] = '结果';
			$this->saveImportResult($file, $result, 'U');
		}
		$result_url = base_url() . substr($file, strlen(dirname($_SERVER['SCRIPT_FILENAME'])) + 1);
		$this->common_model->logs($return['data']['msg'] . $result_url);
		$this->mysql_model->clean();
		$return['data']['url'] = $result_url;
		echo json_encode($return);
	}

	private function importCategory_trade($trades, $file)
	{
		$insert = $update = $fail = 0;
		$result = array();
		$count = count($trades);

		// 整理导入文档中数据, ['code' => [], 'code' => [], ...];
		$tree = array();
		for ($num = 2; $num <= $count; $num++)
		{
			$trade = $trades[$num];
			$code = trim($trade['A']);
			$parent = trim($trade['C']);
			if (empty($code)) continue;
			if ($code == $parent)
			{
				$fail++;
				$result[$num] = '当前分类和上级分类不能相同！';
				continue;
			}
			else
			{
				$result[$num] = '上级分类数据有误！'; // 设置个默认值，成功更新或插入时会进行覆盖
			}
			$item = array(
				'code' 		=> $code,
				'name' 		=> trim($trade['B']),
				'parent' 	=> $parent,
				'remark'	=> trim($trade['D']),
				'num'		=> $num,
			);
			$tree[$item['code']] = $item;
		}

		// 记录导入文档中未找到上级的code
		$unfoundparent = array();
		// 处理层级, ['code' => [...,'children' => []], 'code' => [...,'children' => []], ...]
		foreach ($tree as &$item)
		{
			if (isset($tree[$item['parent']]))
			{
				$tree[$item['parent']]['children'][] = &$item;
			}
			else
			{
				$unfoundparent[] = $item['code'];
			}
		}

		foreach ($unfoundparent as $code)
		{
			$this->processTrade($code, $tree, $insert, $update, $result);
		}

		$total = $num - 2;
		$return = array();
		$return['status'] = 200;
		$return['data']['total'] = $total;
		$return['data']['insert'] = $insert;
		$return['data']['update'] = $update;
		$return['data']['fail'] = $fail = $total - $insert - $update;
		$return['data']['msg'] = "商品类别导入完毕。<br/>共：{$total}条数据，成功新增：{$insert}条数据，成功更新：{$update}条数据，失败：{$fail}条数据。";
		// 记录日志
		if (!empty($result))
		{
			$result[1] = '结果';
			$this->saveImportResult($file, $result, 'E');
		}
		$result_url = base_url() . substr($file, strlen(dirname($_SERVER['SCRIPT_FILENAME'])) + 1);
		$this->common_model->logs($return['data']['msg'] . $result_url);
		$this->mysql_model->clean();
		$return['data']['url'] = $result_url;
		echo json_encode($return);
	}

	private function saveImportResult($file, $result, $column)
	{
		$PHPReader = PHPExcel_IOFactory::createReader('Excel5');

		$PHPExcel = $PHPReader->load($file);
		$currentSheet = $PHPExcel->getSheet(0);
		foreach ($result as $key => $cell)
		{
			$currentSheet->setCellValue($column . $key, $cell);
			$currentSheet->getStyle($column . $key)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		}
		$currentSheet->getColumnDimension($column)->setWidth(30);
		$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
		$objWriter->save($file);
	}

	private function readFile($file)
	{
		$PHPReader = PHPExcel_IOFactory::createReader('Excel5');
		$PHPExcel = $PHPReader->load($file);

		$currentSheet = $PHPExcel->getSheet(0);
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
		$data = array();
		for ($currentRow = 1; $currentRow <= $allRow; $currentRow++)
		{
			for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++)
			{
				$address = $currentColumn.$currentRow;
				$cell = $currentSheet->getCell($address)->getValue();
				if ($cell instanceof PHPExcel_RichText)
				{
					$cell = $cell->__toString();
				}
				$data[$currentRow][$currentColumn] = $cell;
			}
		}
		return $data;
	}

	private function processTrade($code, $tree, &$insert, &$update, &$result)
	{
		$trade = $tree[$code];
		$num = $trade['num'];

		$oldparent = $newparent = null;
		$change = true;	// 上级类别是否变更
		$processChildren = false; // 是否处理子分类

		// 原类别
		$old = $this->mysql_model->get_row(CATEGORY, "typeNumber = 'trade' AND code = '{$code}' AND isDelete = 0", '*');

		if (!empty($old))
		{
			// 原类别为顶级类别
			if (empty($old['parentId']))
			{
				// 判断导入是否变更了上级类别
				$change = !empty($trade['parent']);
			}
			else
			{
				$oldparent = $this->mysql_model->get_row(CATEGORY, "typeNumber = 'trade' AND id = '{$old['parentId']}' AND isDelete = 0", '*');
				$change = $oldparent['code'] != $trade['parent'];
			}
		}
		if ($change && !empty($trade['parent']))
		{
			$newparent = $this->mysql_model->get_row(CATEGORY, "typeNumber = 'trade' AND code = '{$trade['parent']}' AND isDelete = 0", '*');
		}

		// 商品类别名称，必填
		if (empty($trade['name']))
		{
			$result[$trade['num']] = '商品类别名称不能为空！';
		}
		else
		{
			$data = array();
			$data['code'] 		= $trade['code'];
			$data['name'] 		= $trade['name'];
			$data['remark'] 	= $trade['remark'];
			$data['typeNumber'] = 'trade';

			$this->db->trans_begin();

			// 商品类别已存在，更新
			if ($old)
			{
				// 上级变更了，需要修改path, level, parentId, 同时修改原已存在类别的子类别
				if ($change)
				{
					$data['parentId'] = $newparent ? $newparent['id'] : 0;
					$data['level'] = $newparent ? intval($newparent['level']) + 1 : 1;
					$data['path'] = $newparent ? $newparent['path'] . ',' . $old['id'] : $old['id'];
				}
				if ($this->mysql_model->update(CATEGORY, $data, array('id' => $old['id'])))
				{
					if ($change)
					{
						$oldchildren = $this->mysql_model->get_results(CATEGORY, "(id <> '{$old['id']}') AND find_in_set('{$old['id']}', path)");
						$newchildren = array();
						foreach ($oldchildren as $item)
						{
							$child = array();
							$child['id'] = $item['id'];
							$child['path'] = $data['path'] . substr($item['path'], strlen($old['path']));
							$child['level'] = substr_count($child['path'], ',') + 1;
							$newchildren[] = $child;
						}
						$this->mysql_model->update(CATEGORY, $newchildren, 'id');
					}
					$update++;
					$processChildren = true;
					$result[$num] = '更新商品类别成功！';
					$this->common_model->logs('导入更新商品类别:' . $data['name']);

					// 如果类别名称变更了，则修改goods表中类别名称
					if ($old['name'] != $data['name'])
					{
						$this->mysql_model->update(GOODS, array('categoryName' => $data['name']) , array('categoryId' => $old['id']));
					}
				}
				else
				{
					$fail++;
					$result[$num] = '更新商品类别失败！';
					if (!$change)
					{
						$processChildren = true;
					}
				}
			}
			else
			{
				$data['parentId'] = $newparent ? $newparent['id'] : 0;
				$data['level'] = $newparent ? (intval($newparent['level']) + 1) : 1;
				// 商品类别不存在，插入
				if ($id = $this->mysql_model->insert(CATEGORY, $data))
				{
					// 修改path
					$this->mysql_model->update(CATEGORY, array('path' => $newparent ? $newparent['path'] . ',' . $id : $id), '(id='.$id.')');
					$insert++;
					$processChildren = true;
					$result[$num] = '新增商品类别成功！';
					$this->common_model->logs('导入新增商品类别:' . $data['name']);
				}
				else
				{
					$fail++;
					$result[$num] = '新增商品类别失败！';
				}
			}
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
			}
			else
			{
				$this->db->trans_commit();
			}
		}

		if ($processChildren && !empty($trade['children']))
		{
			foreach ($trade['children'] as $item)
			{
				$this->processTrade($item['code'], $tree, $insert, $update, $result);
			}
		}
	}
	
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */