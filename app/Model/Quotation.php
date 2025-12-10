<?php
class Quotation extends AppModel
{
    public $useTable = 'quotations';

    public $validate = array(
        'quotation_id' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'filename' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    /**
     * Upload Quotation details PDF file and save Quotation.
     */
    public function uploadQuotationDetailsPdf($quotation)
    {
        $filename = $quotation['quotation_id'] . ".pdf";
        $filename = preg_replace("/[^a-zA-Z0-9.]/", "", $filename);

        $data = base64_decode($quotation['base_64']);
        $fileDir = ConstantsFilePaths::QUOTATION_DETAILS_PDFS_ABSOLUTE . DS . $filename;

        $success = file_put_contents($fileDir, $data);
        if ($success) {
            if (\Configure::read('AZURE_FILES')) {
                FileManager::upload_file(ConstantsFilePaths::QUOTATION_DETAILS_PDFS_ABSOLUTE . DS . $filename, ConstantsFilePaths::QUOTATION_DETAILS_PDFS_RELATIVE, $filename, ConstantsFileType::FILE, true);
            }
            if (!$this->saveQuotation($quotation, ConstantsBooleans::NO, $filename)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Save Quotation.
     */
    public function saveQuotation($quotation, $deleted, $filename = null)
    {
        $quotation = array(
            'id_leadgen' => $quotation['id'],
            'quotation_id' => $quotation['quotation_id'],
            'network_id' => $quotation['network_id'],
            'garage_id' => $quotation['garage_id'],
            'filename' => $filename,
            'file_guid' => CakeText::uuid(),
            'deleted' => $deleted,
            'creation_date' => date('Y-m-d H:i:s')
        );

        $this->create();
        return $this->save($quotation);
    }

    /**
     * Remove Quotation details PDF file and update Quotation to mark it as deleted.
     */
    public function deleteQuotationDetailsPdfs($quotationId)
    {
        $quotation = $this->findById($quotationId);
        if ($quotation) {
            $isFileDeleted = FileManager::delete_file(
                WWW_ROOT,
                ConstantsPath::DIR_QUOTATION_DETAILS_PDFS . DS . $quotation['Quotation']['filename'],
                true
            );
            if ($isFileDeleted) {
                $quotation['Quotation']['deleted'] = ConstantsBooleans::YES;
                if ($this->save($quotation)) {
                    return true;
                }
            }
        }
        return false;
    }
}
