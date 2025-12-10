<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Communication.Communications'),
                array(
                    'controller' => 'communications',
                    'action' => 'maintenance_communications'
                )
            ),
            __t('Communication.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'communications',
                'action' => 'maintenance_communications',
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'communications',
                'action' => 'edit',
                $communication['Communication']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
            )
        );
        ?>
    </div>
</div>
<div class="row p-1">
    <div class="columns medium-12">
        <fieldset class="columns fieldset-garage-list">
            <div class="row">
                <div class="columns medium-7 m-top-1">
                    <div class="columns medium-6 p-1">
                        <strong><?php echo __t('Communication.Title') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($communication['Communication']['title']); ?></div>
                    </div>
                    <div class="columns medium-6 p-1">
                        <strong><?php echo __t('Communication.Subtitle') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($communication['Communication']['subtitle']); ?></div>
                    </div>
                    <div class="columns medium-5 p-1">
                        <strong><?php echo __t('Communication.Communication_section') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($communication_sections[$communication['Communication']['communication_section_id']]); ?>
                        </div>
                    </div>
                    <div class="columns medium-5 p-1">
                        <strong><?php echo __t('Communication.Communication_subsection') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo h($sections_subsections[$communication['Communication']['section_subsection_id']]); ?>
                        </div>
                    </div>
                    <div class="columns medium-2 p-1">
                        <strong><?php echo __t('Communication.Active') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input">
                            <?php echo ($communication['Communication']['active']) ? __t('General.Yes') : __t('General.No'); ?>
                        </div>
                    </div>
                    <div class="columns medium-12">
                        <strong><?php echo __t('Communication.Body') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($communication['Communication']['body']); ?></div>
                    </div>
                    <div class="columns medium-12 p-top-1">
                        <strong><?php echo __t('Network.Networks') ?>: </strong>
                        <br>
                    </div>
                    <div class="columns medium-12 p-top-1">
                        <div class="d-inline-block cont-services w-100p">
                            <?php
                            $cont = 0;
                            foreach ($networks as $key_network => $network) { ?>
                                <div class="medium-3 columns end <?php echo  $cont % 4 == 0 ? 'clear' : ''; ?>">
                                    <?php
                                    $class_icono = 'grayscale_icons';

                                    foreach ($communications_networks as $key => $communication_network) {
                                        if (isset($network) && $network['Network']['id'] == strval($key)) {
                                            $class_icono = '';
                                        }
                                    }
                                    if ($class_icono == '') {
                                        $cont++;
                                    ?>
                                        <div class="p-bottom-1 ta-center">
                                            <?php echo $this->Html->image(
                                                '/img/networks/' . $network['Network']['image'],
                                                array(
                                                    'class' => $class_icono
                                                )
                                            ) . "<br>" . $network['Network']['name'];
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="columns medium-12 p-top-1">
                        <strong><?php echo __t('Network.Trading_groups') ?>: </strong>
                        <br>
                    </div>
                    <div class="columns medium-12 p-top-1">
                        <div class="d-inline-block cont-services w-100p">
                            <?php
                            $cont = 0;
                            foreach ($trading_groups as $key_trading_group => $trading_group) { ?>
                                <div class="medium-3 columns end <?php echo  $cont % 4 == 0 ? 'clear' : '' ?>">
                                    <?php
                                    $class_icono = 'grayscale_icons';
                                    foreach ($communications_trading_groups as $key => $communications_trading_group) {
                                        if (isset($trading_group) && $trading_group['TradingGroup']['id'] == strval($key)) {
                                            $class_icono = '';
                                        }
                                    }
                                    if ($class_icono == '') {
                                        $cont++;
                                    ?>
                                        <div class="p-bottom-1 ta-center">
                                            <?php echo $this->Html->image(
                                                '/img/trading_groups/' . $trading_group['TradingGroup']['image'],
                                                array(
                                                    'class' => $class_icono
                                                )
                                            ) . "<br>" . $trading_group['TradingGroup']['name']; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="columns medium-5 m-top-1">
                    <div class="columns medium-12 p-1">
                        <strong><?php echo __t('Communication.Url') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h($communication['Communication']['url']); ?></div>
                    </div>
                    <div class="columns medium-6  clear p-1">
                        <strong><?php echo __t('Communication.Start_date') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h(Fecha::toFormatoVista($communication['Communication']['start_date'])); ?></div>
                    </div>
                    <div class="columns medium-6 p-1">
                        <strong><?php echo __t('Communication.End_date') ?>: </strong>
                        <br>
                        <div class="b-bottom-1 height_input"><?php echo h(Fecha::toFormatoVista($communication['Communication']['end_date'])); ?></div>
                    </div>
                    <div class="columns medium-12">
                        <strong><?php echo __t('General.Image') ?>: </strong>
                        <br>
                    </div>
                    <div class="columns medium-12 p-top-1 ta-center">
                        <img src="<?php echo FileManager::get_url(FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication['Communication']['image']); ?>" style="max-height: 125px">
                    </div>
                    <div class="columns medium-12 p-1">
                        <strong><?php echo __t('General.Files') ?>: </strong>
                        <br>
                        <div>
                            <?php foreach ($communication_files as $file) { ?>
                                <div class="medium-12 columns">
                                    <?php
                                    echo $this->Html->link(
                                        $file['CommunicationFile']['file'],
                                        array(
                                            'controller' => 'communications_files',
                                            'action' => 'download_file',
                                            $file['CommunicationFile']['id']
                                        )
                                    );
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>