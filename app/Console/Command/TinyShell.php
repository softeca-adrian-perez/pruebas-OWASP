<?php
App::uses('Tiny', 'Lib');
class TinyShell extends Shell
{
    public $uses = array('GarageNetworkImage', 'Network', 'NetworkRecommended');

    /**
     * Optimise the size of all images
     *
     * console\cake tiny optimiseImageSize
     */
    public function optimiseImageSize()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Tiny Scheduled Tasks - optimize_image_size_with_tiny - Start", true));
            }
            $tiny = new Tiny();
            $models = ['GarageNetworkImage', 'Network', 'NetworkRecommended'];

            foreach ($models as $model) {
                switch ($model) {
                    case 'GarageNetworkImage':
                        $column = 'source_name';
                        $columnBis = '';
                        $absoluteRoute = ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_ABSOLUTE;
                        $relativeRoute = ConstantsFilePaths::GARAGES_NETWORKS_IMAGES_RELATIVE;
                        $isPrivateContainer = false;
                        break;
                    case 'Network':
                        $column = 'image';
                        $columnBis = '';
                        $absoluteRoute = ConstantsFilePaths::NETWORK_IMAGES_ABSOLUTE;
                        $relativeRoute = ConstantsFilePaths::NETWORK_IMAGES_RELATIVE;
                        $isPrivateContainer = false;
                        break;
                    case 'NetworkRecommended':
                        $column = 'image_recommended';
                        $columnBis = 'image_recommended_list';
                        $absoluteRoute = ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE;
                        $relativeRoute = ConstantsFilePaths::NETWORKS_RECOMMENDED_IMAGES_RELATIVE;
                        $isPrivateContainer = false;
                        break;
                    default:
                        $column = '';
                        $columnBis = '';
                        $absoluteRoute = '';
                        $relativeRoute = '';
                        $isPrivateContainer = false;
                }

                $images = $this->$model->findAllByOptimized(ConstantsBooleans::NO);
                foreach ($images as $image) {
                    if ($image[$model][$column] != null) {
                        $tiny->optimiseImageSize($image, $column, $absoluteRoute, $relativeRoute, $model, $isPrivateContainer);
                    }
                    if ($columnBis != '' && $image[$model][$columnBis] != null) {
                        $tiny->optimiseImageSize($image, $columnBis, $absoluteRoute, $relativeRoute, $model, $isPrivateContainer);
                    }
                }
            }
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Tiny Scheduled Tasks - optimize_image_size_with_tiny - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Tiny Scheduled Tasks - optimize_image_size_with_tiny - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
