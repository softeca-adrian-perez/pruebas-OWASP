<?php
App::uses('Leadgen', 'Lib');

class QuotationsController extends AppController
{
    public $uses = array(
        "Quotation",
        "GarageNetwork",
        "Garage",
        "Network",
        "Booking"
    );

    /**
     * View to get all quotations of a garage.
     *
     * @param garageNetworkId Garage Network ID
     */
    public function home($garageNetworkId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);
        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        if (!$garageNetwork || !$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        $network = $this->Network->find('first', array(
            'conditions' => array('id' => $networkId),
            'fields' => array('with_vat')
        ));
        $withVat = $network['Network']['with_vat'];

        $leadgen = new Leadgen();

        $searcher = $this->request->query;
        $this->request->data['Search'] = $searcher;

        $networksGarage = CakeSession::read('Auth.User.networks');
        if (empty($networksGarage)) {
            $networksGarage = array($networkId);
        }

        $paginationPage = isset($this->request->named['page']) ? $this->request->named['page'] : 1;
        $paginationSize = ConstantsPagination::SIZE_PAGE_SMALL;

        $quotationsGarage = $leadgen->getQuotations($garageId, $paginationPage, $paginationSize, $networksGarage, $searcher);

        if (!empty($quotationsGarage)) {
            foreach ($quotationsGarage['quotations'] as &$quotationGarage) {
                $quotationGarage['price'] = !empty($quotationGarage['total_price_discount']) &&
                    $quotationGarage['total_price_discount'] < $quotationGarage['total_price'] ?
                    $quotationGarage['total_price_discount'] : $quotationGarage['total_price'];

                $quotation = $this->Quotation->findByIdLeadgen($quotationGarage['id']);
                $quotationExist = !empty($quotation);
                if ($quotationExist) {
                    $quotationGarage['deleted'] = $quotation['Quotation']['deleted'];
                    $quotationGarage['file_guid'] = $quotation['Quotation']['file_guid'];
                }
                $quotationGarage['quotation_exist'] = $quotationExist;

                $booking = $this->Booking->findByQuotationIdAndNetworkIdAndGarageId($quotationGarage['quotation_id'], $networkId, $garageId);
                $quotationGarage['booking_exist'] = !empty($booking);
            }
        }

        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'garages_networks',
                'action' => 'view',
                $garageNetworkId
            )
        );

        $quotations = empty($quotationsGarage['quotations']) ? array() : $quotationsGarage['quotations'];

        $this->set(array(
            'cancel_action' => $cancelAction,
            'garage_id' => $garageId,
            'garage_name' => $garage['Garage']['name'],
            'garage_network_id' => $garageNetworkId,
            'quotations' => $quotations,
            'with_vat' => $withVat,
            'network_id' => $networkId,
            'quoting_views_active' => $garageNetwork['GarageNetwork']['quoting_views_active']
        ));

        $this->set('pagination_page', $paginationPage);
        $this->set('pagination_size', $paginationSize);
        $this->set('pagination_count', count($quotations));
    }

    /**
     * Download the Quotation details PDF obtained from Leadgen in case it hasn't
     * been obtained with the scheduled task.
     *
     * @param garageNetworkId Garage Network ID
     * @param quotationId Quotation ID
     */
    public function download_details_pdf($garageNetworkId, $quotationId, $controller, $quotationExist = false)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);
        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        if (!$garageNetwork || !$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        //If quotation id is valid then que can ask lg for the quotation.
        if (strlen($quotationId) < ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO) {
            if (!$quotationExist) {
                if (ctype_alnum($quotationId)) {
                    $leadgen = new Leadgen();
                    $respuestaLeadgen = $leadgen->getQuotationPdf($quotationId);
                    if ($respuestaLeadgen && isset($respuestaLeadgen['success']) && $respuestaLeadgen['success']) {
                        $base64 = $respuestaLeadgen['result'];
                        $filename = "quotation_" . $quotationId . ".pdf";
                        $this->download_base64_file($base64, $filename);
                    }
                }
            } else {
                //If the quotation file is saved in GNM then we get it by the file guid
                return $this->downloadFile($quotationId, ConstantsPrivateFilesTypes::QUOTATION_PDF);
            }
        }

        $this->redirect(
            array(
                'controller' => $controller == '1' ? 'bookings' : 'quotations',
                'action' => 'home',
                $garageNetworkId
            )
        );
    }

    /**
     * Quotations Excel generation.
     */
    public function quotations_excel($garageNetworkId)
    {
        $garageNetwork = $this->GarageNetwork->findById($garageNetworkId);
        $garageId = $garageNetwork['GarageNetwork']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));

        if (!$garageNetwork || !$garage) {
            throw new UnauthorizedException();
        }

        $this->Acceso->checkGarageAccess($garageId);

        $networkId = $garageNetwork['GarageNetwork']['network_id'];

        $leadgen = new Leadgen();

        $searcher = $this->request->query;
        $this->request->data['Search'] = $searcher;

        $networksGarage = CakeSession::read('Auth.User.networks');
        if (empty($networksGarage)) {
            $networksGarage = array($networkId);
        }

        $page = ConstantsPagination::FIRST_PAGE;
        $pageSize = 0;
        $quotationsGarage = $leadgen->getQuotations($garageId, $page, $pageSize, $networksGarage, $searcher);

        if (!empty($quotationsGarage)) {
            foreach ($quotationsGarage['quotations'] as &$quotationGarage) {
                $quotationGarage['price'] = !empty($quotationGarage['total_price_discount']) &&
                    $quotationGarage['total_price_discount'] < $quotationGarage['total_price'] ?
                    $quotationGarage['total_price_discount'] : $quotationGarage['total_price'];

                $quotation = $this->Quotation->findByIdLeadgen($quotationGarage['id']);
                $quotationExist = !empty($quotation);
                if ($quotationExist) {
                    $quotationGarage['deleted'] = $quotation['Quotation']['deleted'];
                }
                $quotationGarage['quotation_exist'] = $quotationExist;

                $booking = $this->Booking->findByQuotationIdAndNetworkIdAndGarageId($quotationGarage['quotation_id'], $networkId, $garageId);
                $quotationGarage['booking_exist'] = !empty($booking);
            }
        }

        $quotations = empty($quotationsGarage['quotations']) ? array() : $quotationsGarage['quotations'];
        $config = CakeSession::read('Auth.User.Config');

        $this->set(array(
            'quotations' => $quotations,
            'config' => $config,
        ));

        set_time_limit(18000);
        ini_set('memory_limit', '-1');

        $this->render('/Quotations/Elements/export_excel_quotations');
        $this->response->type('xlsx');
        $this->layout = false;
    }
}
