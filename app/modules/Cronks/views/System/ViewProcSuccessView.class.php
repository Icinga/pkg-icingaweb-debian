<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}


class Cronks_System_ViewProcSuccessView extends CronksBaseView {

    /**
     * @var Web_Icinga_ApiContainerModel
     */
    private $api = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);
        
    }

    private function getTemplateFile(AgaviRequestDataHolder $rd) {
        try {
            return AppKitFileUtil::getAlternateFilename(AgaviConfig::get('modules.cronks.xml.path.grid'), $rd->getParameter('template'), '.xml');
        } catch (AppKitFileUtilException $e) {
            AppKitAgaviUtil::log('Could not find template for '. $rd->getParameter('template'), AgaviLogger::ERROR);
            throw $e;
        }
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {

        $this->setupHtml($rd);

        try {
            $file = $this->getTemplateFile($rd);

            $template = new CronkGridTemplateXmlParser($file->getRealPath(), $this->getContext());
            $template->parseTemplate();

            $worker = CronkGridTemplateWorkerFactory::createWorker($template, $this->getContext());

            $layout_class = $template->getSectionParams('option')->getParameter('layout');
            $layout = AppKitClassUtil::createInstance($layout_class);

            $layout->setContainer($this->getContainer());
            $layout->setWorker($worker);
            $layout->setParameters($rd);

            return $layout->getLayoutContent();
        } catch (AppKitFileUtilException $e) {
            return $this->getContext()->getTranslationManager()->_('Sorry, could not find a xml file for %s', null, null, array($rd->getParameter('template')));
        }
    }

    public function executeJson(AgaviRequestDataHolder $rd) {
        $data = array();

        try {

            $file = $this->getTemplateFile($rd);

            $template = new CronkGridTemplateXmlParser($file->getRealPath(), $this->getContext());
            $template->parseTemplate();
            $connection = $rd->getParameter("connection","icinga");

            $worker = CronkGridTemplateWorkerFactory::createWorker($template, $this->getContext(), $connection);

            
            if (is_numeric($rd->getParameter('page_start')) && is_numeric($rd->getParameter('page_limit'))) {
                $worker->setResultLimit($rd->getParameter('page_start'), $rd->getParameter('page_limit'));
            } else {
                $user = $this->context->getUser();
                $worker->setResultLimit(
                    0,
                    $user->getPrefVal('org.icinga.grid.pagerMaxItems',
                        AgaviConfig::get('modules.cronks.grid.pagerMaxItems', 25)
                    )
                );
            }


            if ($rd->getParameter('sort_field', null) !== null) {
                $worker->setOrderColumn($rd->getParameter('sort_field'), $rd->getParameter('sort_dir', 'ASC'));
            
                if($rd->getParameter('additional_sort_field',null) !== null) {
                    $worker->addOrderColumn($rd->getParameter('additional_sort_field'), $rd->getParameter('sort_dir', 'ASC'));
                }
            }

            // Apply the filter to our template worker
            if (is_array($rd->getParameter('f'))) {
                $pm = $this->getContext()->getModel('System.ViewProcFilterParams', 'Cronks');
                $pm->setParams($rd->getParameter('f'));
                $pm->applyToWorker($worker);
            }

            $worker->buildAll();

            // var_dump($worker->fetchDataArray());

            $data['resultRows'] = $worker->fetchDataArray();
            $data['resultCount'] = $worker->countResults();

            // OK hopefully all done
            $data['resultSuccess'] = true;

        } catch (AppKitFileUtilException $e) {
            $data['resultSuccess'] = true;
            $data['resultCount'] = 0;
            $data['resultRows'] = null;
        }

        return json_encode($data);
    }
}
