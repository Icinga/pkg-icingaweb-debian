<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2015 Icinga Developer Team.
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


class AppKit_DataProvider_GroupProviderSuccessView extends AppKitBaseView {

    
    public function executeJson(AgaviRequestDataHolder $rd) {
        $roleadmin = $this->getContext()->getModel('RoleAdmin', 'AppKit');

        $disabled = $rd->getParameter('hideDisabled',false) == "false";
        $result = $this->getAttribute("role",false);
        if($result !== false)
            return json_encode(array("sucess"=>true,"role" => $result));
        
        $result = $this->getAttribute("roles",false);
        if($result !== false) {
            $totals = $roleadmin->getRoleCount($disabled);
            return json_encode(array("success"=>true,"roles" => $result, "totalCount" => $totals));
        }
        $error = $this->getAttribute("error");
        if($error)
            return json_encode(array("success"=>false,"error" => $error));
        else
            return json_encode(array("success"=>true,"role"=>array()));
    }

    public function executeHtml(AgaviRequestDataHolder $rd) {
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Admin.DataProvider.GroupProvider');
    }
}

?>