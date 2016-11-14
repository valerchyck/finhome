<?php
namespace app\models;

use yii\helpers\FileHelper;

class Helper {
	public static function xlsToCsv($file) {
		$objReader = \PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcelReader = $objReader->load($file);

		$loadedSheetNames = $objPHPExcelReader->getSheetNames();
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcelReader, 'CSV');

		if (!file_exists('upload'))
			FileHelper::createDirectory('upload', 0775);

		$filename = '';
		foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
			$filename = 'upload/' . $loadedSheetName . '.csv';
			$objWriter->save($filename);
			chmod($filename, 0775);
		}

		return $filename;
	}

	public static function isEmpty($value){
		if (is_array($value))
			return count($value) == 0;

		return empty($value);
	}

	public static function checkExtension($filename, $extensions) {
		if (!is_array($extensions))
			throw new \InvalidArgumentException('second argument must be array');

		return in_array(pathinfo($filename, PATHINFO_EXTENSION), $extensions);
	}
}
