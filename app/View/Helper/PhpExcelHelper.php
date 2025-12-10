<?php
App::uses('AppHelper', 'Helper');

/**
 * Helper for working with PHPExcel class.
 * PHPExcel has to be in the vendors directory.
 */

class PhpExcelHelper extends AppHelper {
	/**
	 * Instance of PHPExcel class
	 * @var object
	 */
	public $xls;
	/**
	 * Pointer to actual row
	 * @var int
	 */
	protected $row = 1;
	/**
	 * Internal table params
	 * @var array
	 */
	protected $tableParams;
    /**
     * @var array estilo por defecto para la cabecera
     */
    public $estiloCabecera = array(
        'name' => 'Calibri',
        'bold' => true,
        'offset' => 0,
    );

	/**
	 * Constructor
	 */
	public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
    }

	/**
	 * Create new worksheet
	 */
	public function createWorksheet() {
		$this->loadEssentials();
		$this->xls = new PHPExcel();
	}

	public function cambiarTituloHoja($nombre = 'Worksheet', $color_tab = 'FFFFFFFF'){
		$this->xls->getActiveSheet()->setTitle($nombre);
		$this->xls->getActiveSheet()->getTabColor()->setARGB($color_tab);
	}

    public function addHoja($nombre = 'Worksheet'){
        $hoja = $this->xls->createSheet();
        $hoja->SetTitle($nombre);
    }

    public function irAHoja($numero = 0){
        $this->xls->setActiveSheetIndex($numero);
    }

	/**
	 * Create new worksheet from existing file
	 */
	public function loadWorksheet($path) {
		$this->loadEssentials();
		$this->xls = PHPExcel_IOFactory::load($path);
	}

	/**
	 * Set row pointer
	 */
	public function setRow($to) {
		$this->row = (int)$to;
	}

	/**
	 * Set default font
	 */
	public function setDefaultFont($name, $size) {
		$this->xls->getDefaultStyle()->getFont()->setName($name);
		$this->xls->getDefaultStyle()->getFont()->setSize($size);
	}

    public function setDefaultBorder() {
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $this->xls->getDefaultStyle()->applyFromArray($styleArray);
    }

	/**
	 * Start table
	 * inserts table header and sets table params
	 * Possible keys for data:
	 * 	label 	-	table heading
	 * 	width	-	"auto" or units
	 * 	filter	-	true to set excel filter for column
	 * 	wrap	-	true to wrap text in column
	 * Possible keys for params:
	 * 	offset	-	column offset (numeric or text)
	 * 	font	-	font name
	 * 	size	-	font size
	 * 	bold	-	true for bold text
	 * 	italic	-	true for italic text
	 *
	 */
	public function addTableHeader( $data, $params = array(), $rgbColor = 'FFFFFF', $rgbBackgroundColor = '00B0F0', $set_gnm_legend = true ) {

        if(empty($params)){
            $params = $this->estiloCabecera;
        }
		// offset
		if (array_key_exists('offset', $params))
			$offset = is_numeric($params['offset']) ? (int)$params['offset'] : PHPExcel_Cell::columnIndexFromString($params['offset']);
		// font name
		if (array_key_exists('font', $params))
			$this->xls->getActiveSheet()->getStyle($this->row)->getFont()->setName($params['font_name']);
		// font size
		if (array_key_exists('size', $params))
			$this->xls->getActiveSheet()->getStyle($this->row)->getFont()->setSize($params['font_size']);
		// bold
		if (array_key_exists('bold', $params))
			$this->xls->getActiveSheet()->getStyle($this->row)->getFont()->setBold($params['bold']);
		// italic
		if( array_key_exists('italic', $params ))
			$this->xls->getActiveSheet()->getStyle($this->row)->getFont()->setItalic($params['italic']);

        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => $rgbColor),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => $rgbBackgroundColor),
            ),
        );

        /*$styleArray2 = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                //'color' => array('rgb'=>'115372'),
                'color' => array('rgb'=>'319FEE'),
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                )
            ),
        );*/

        $this->xls->getActiveSheet()->getStyle($this->row)->applyFromArray( $styleArray );



        //$this->xls->getActiveSheet()->getStyle($this->row)->applyFromArray( $styleArray2 );



		// set internal params that need to be processed after data are inserted
		$this->tableParams = array(
			'header_row' => $this->row,
			'offset' => $offset,
			'row_count' => 0,
			'auto_width' => array(),
			'filter' => array(),
			'wrap' => array()
		);

		foreach( $data as $d ){
			// set label
            if( !empty( $d['file'] ) ){
                if( !$this->addImage( $d['file'], PHPExcel_Cell::stringFromColumnIndex( $offset ), $this->row ) ){
                    $this->xls->getActiveSheet()->setCellValueExplicitByColumnAndRow($offset, $this->row, $d['label']);
                }
            } else{
                $this->xls->getActiveSheet()->setCellValueByColumnAndRow($offset, $this->row, $d['label']);
            }
			// set width
            //$this->tableParams['auto_width'][] = $offset;// siempre auto
			if (array_key_exists('width', $d)) {
				if ($d['width'] == 'auto')
					$this->tableParams['auto_width'][] = $offset;
				else
					$this->xls->getActiveSheet()->getColumnDimensionByColumn($offset)->setWidth((float)$d['width']);
			}
			// filter
			if (array_key_exists('filter', $d) && $d['filter'])
				$this->tableParams['filter'][] = $offset;
			// wrap
			if (array_key_exists('wrap', $d) && $d['wrap'])
				$this->tableParams['wrap'][] = $offset;

			$offset++;
		}
		$this->row++;

        if( $set_gnm_legend ) {
            $this->xls->getActiveSheet()->setCellValue('A1', "provided by\nGNM INTERNATIONAL");
            $this->xls->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);

            $this->xls->getActiveSheet()->getStyle('A1')->applyFromArray(array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '0000FF'),
                    'size' => 7,
                    'name' => 'Calibri'
                )
            ));
        }

	}

    public $columnasTiposNumericos = array();

    public function addColumnasTiposNumericos($columnas) {
        $this->columnasTiposNumericos = $columnas;
    }

    /**
     * Add Columns type Date
     */
    public $columnasTiposDate = array();

    public function addColumnDateType($column) {
        $this->columnDateType = $column;
    }
    /**
     * Add Columns type format date
     */
    public $columnasTiposDateFormat = array();

    public function addColumnDateFormat($format = PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY_SLASH) {
        $this->columnDateFormat = $format;
    }

	/**
	 * Write array of data to actual row
	 */
	public function addTableRow($data, $font_color = '808080') {

        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => $font_color),
            ),
        );
        $this->xls->getActiveSheet()->getStyle($this->row)->applyFromArray($styleArray);

		$offset = $this->tableParams['offset'];

		foreach ($data as $d) {
            if( strpos($d, '.png') === false ){
                if (in_array($offset, $this->columnasTiposNumericos)) {
                    $this->xls->getActiveSheet()->setCellValueExplicitByColumnAndRow($offset++, $this->row, $d, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                }elseif (isset($this->columnDateType) && is_array($this->columnDateType) && in_array($offset, $this->columnDateType) && !empty($d)) {
                        $datePHPExcel = PHPExcel_Shared_Date::PHPToExcel(new DateTime($d));
                        $this->xls->getActiveSheet()->setCellValueExplicitByColumnAndRow($offset, $this->row, $datePHPExcel, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                        // Apply date format
                        $this->xls->getActiveSheet()->getStyleByColumnAndRow($offset++, $this->row)
                                                    ->getNumberFormat()
                                                    ->setFormatCode(isset($this->columnDateFormat) && !empty($this->columnDateFormat) 
                                                        ? $this->columnDateFormat 
                                                        : PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY_SLASH
                                                    );
                } else {
                    $this->xls->getActiveSheet()->setCellValueExplicitByColumnAndRow($offset++, $this->row, $d);
                }
            } else{
                if( !$this->addImage( $d, PHPExcel_Cell::stringFromColumnIndex( $offset ), $this->row ) ){
                    $this->xls->getActiveSheet()->setCellValueExplicitByColumnAndRow($offset++, $this->row, 'NO IMAGE');
                }
            }
            //$this->xls->getActiveSheet()->setCellValueExplicitByColumnAndRow($offset++, $this->row, $d);
		}
		$this->row++;
		$this->tableParams['row_count']++;
	}

    public function setRowBorderBottomMedium($rowNumber, $colNumberStart, $colNumberEnd)
    {
        $colLetterStart = PHPExcel_Cell::stringFromColumnIndex($colNumberStart);
        $colLetterEnd = PHPExcel_Cell::stringFromColumnIndex($colNumberEnd);

        $this->xls->getActiveSheet()->getStyle($colLetterStart.$rowNumber . ":" . $colLetterEnd.$rowNumber)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    }

    /**
     * change color
     * first row is 1, first col is 0
     */
    public function changeCellColorAndBackground($rgbColor, $rgbBackgroundColor, $rowNumber, $colNumber){
        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colNumber);

        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => $rgbColor),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => $rgbBackgroundColor),
            )
        );
        $this->xls->getActiveSheet()->getStyle($colLetter.$rowNumber)->applyFromArray($styleArray);
    }

    /**
     * change font
     * first row is 1, first col is 0
     */
    public function changeCellFont($size, $bold, $rowNumber, $colNumber){
        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colNumber);

        $styleArray = array(
            'font' => array(
                'size' => $size,
                'bold' => $bold,
            ),
        );
        $this->xls->getActiveSheet()->getStyle($colLetter.$rowNumber)->applyFromArray($styleArray);
    }

    /**
     * merge cells of one column
     * first row is 1, first col is 0
     */
    public function mergeCellsInColumn($colNumber, $startRow, $endRow)
    {
        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colNumber);

        $this->xls->getActiveSheet()->mergeCells($colLetter.$startRow.':'.$colLetter.$endRow);
        $this->xls->getActiveSheet()->getStyle($colLetter.$startRow)->applyFromArray(array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        ));
    }

    /**
     * expose row property
     */
    public function getActualRowNumber()
    {
        return $this->row;
    }

    /**
     * merge cells of one row
     * first row is 1, first col is 0
     */
    public function mergeCellsInRow($rowNumber, $startCol, $endCol, $center = true)
    {
        $startColLetter = PHPExcel_Cell::stringFromColumnIndex($startCol);
        $endColLetter = PHPExcel_Cell::stringFromColumnIndex($endCol);

        $this->xls->getActiveSheet()->mergeCells($startColLetter.$rowNumber.':'.$endColLetter.$rowNumber);
        if($center) {
            $this->xls->getActiveSheet()->getStyle($startColLetter . $rowNumber)->applyFromArray(array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            ));
        }
    }

	/**
	 * End table
	 * sets params and styles that required data to be inserted
	 */
	public function addTableFooter() {
		// auto width
		foreach ($this->tableParams['auto_width'] as $col)
			$this->xls->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
		// filter (has to be set for whole range)
		if (count($this->tableParams['filter']))
			$this->xls->getActiveSheet()->setAutoFilter(PHPExcel_Cell::stringFromColumnIndex($this->tableParams['filter'][0]).($this->tableParams['header_row']).':'.PHPExcel_Cell::stringFromColumnIndex($this->tableParams['filter'][count($this->tableParams['filter']) - 1]).($this->tableParams['header_row'] + $this->tableParams['row_count']));
		// wrap
		foreach ($this->tableParams['wrap'] as $col)
			$this->xls->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).($this->tableParams['header_row'] + 1).':'.PHPExcel_Cell::stringFromColumnIndex($col).($this->tableParams['header_row'] + $this->tableParams['row_count']))->getAlignment()->setWrapText(true);
	}

	/**
	 * Write array of data to actual row starting from column defined by offset
	 * Offset can be textual or numeric representation
	 */
	public function addData($data, $offset = 0) {
		// solve textual representation
		if (!is_numeric($offset))
			$offset = PHPExcel_Cell::columnIndexFromString($offset);

		foreach ($data as $d) {
			$this->xls->getActiveSheet()->setCellValueByColumnAndRow($offset++, $this->row, $d);
		}
		$this->row++;
	}

	/**
	 * Output file to browser
	 */
	public function output($filename = 'export.xlsx') {
		// set layout
		$this->_View->layout = '';
		// headers
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// writer
		$objWriter = PHPExcel_IOFactory::createWriter($this->xls, 'Excel2007');
        // $objWriter = PHPExcel_IOFactory::createWriter($this->xls, 'Excel5');
		$objWriter->save('php://output');
		// clear memory
		$this->xls->disconnectWorksheets();
	}

    /**
     * Save to a file
     */
    public function save($file) {
        $objWriter = PHPExcel_IOFactory::createWriter($this->xls, 'Excel5');
        return $objWriter->save($file);
    }

	/**
	 * Load vendor classes
	 */
	protected function loadEssentials() {
		// load vendor class
		App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
		if (!class_exists('PHPExcel'))
			throw new CakeException('Vendor class PHPExcel not found!');
	}

    /**
     * add image to cell
     * first row is 1, first col is 0
     */
    public function addImage( $file, $rowNumber, $colNumber ){
        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colNumber);

        if( file_exists( $file ) ){
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($file);
            $objDrawing->setCoordinates( $colLetter.$rowNumber );
            $objDrawing->setWorksheet($this->xls->getActiveSheet());
        }
    }

    public function setRowHeight( $rowNumber, $height )
    {
        $this->xls->getActiveSheet()->getRowDimension($rowNumber)->setRowHeight($height);
    }
    public function setColumnWidth( $colNumber, $width )
    {
        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colNumber);
        $this->xls->getActiveSheet()->getColumnDimension($colLetter)->setWidth($width);
    }
}