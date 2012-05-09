<?php
/**
 * Api wrapping agavi model
 *
 * Provides access to preconfigured IcingaApi
 *
 * @author mhein
 * @package icinga-web
 * @subpackage icinga
 */
class Web_Icinga_ApiContainerModel extends IcingaWebBaseModel
    implements AgaviISingletonModel {
    static $TYPES = array(
                        "DOCTRINE_API" => 0,
                        "LEGACY_API" => 1
                    );
    protected $model;
    protected $type = 0;

    public function setType($type) {
        if (isset(self::$TYPES[$type])) {
            if (self::$TYPES[$type] == $this->type) {
                return;
            }

            $this->type = self::$TYPES[$type];
            $this->model = $this->getModel();
        }
    }

    public function getType() {
        return $this->type;
    }

    public function initialize(AgaviContext $ctx,array $parameters = array()) {
        parent::initialize($ctx,$parameters);

        if (isset($parameters["type"])) {
            $this->setType($parameters["type"]);
        } else {
            $this->model = $this->getModel();
        }
    }
    private function getModel() {
        if ($this->type === 1) {
            return $this->getContext()->getModel("DeprecatedApiContainer","Api");
        } else {
            return $this->getContext()->getModel("LegacyApiContainer","Api");
        }
    }

    public function getConnection() {
        return $this->model->getConnection();
    }

    /**
     * Abstracts the API->getConnection(...)->createSearch(ICINGA::...)
     * to a api bound method
     * @return IcingaApiSearch
     * @author mhein
     */
    public function createSearch() {
        return $this->model->createSearch(func_get_args());
    }
    public function __set($key,$val) {
        $this->model-> {$key} = $val;
    }
    public function __get($key) {
        return $this->model-> {$key};
    }

    /**
     * Checks if command dispatcher exists
     * @return boolean
     * @author mhein
     */
    public function checkDispatcher() {
        return $this->model->checkDispatcher();
    }

    /**
     * Sends a single IcingaApi command definition
     * @param IcingaApiCommand $cmd
     * @return boolean
     * @author mhein
     */
    public function dispatchCommand(IcingaApiCommand &$cmd) {
        return $this->model->dispatchCommand($cmd);
    }

    private function getDispatcherByInstance($instance_name) {
        return $this->model->getDispatcherByInstance($instance_name);
    }

    /**
    * Same as ::dispatchCommand(). Sends an array
    * of command definitions
    * @see Web_Icinga_ApiContainerModel::getConnection()
    * @param array $arry
    * @return unknown_type
    * @author mhein
    */
    public function dispatchCommandArray(array $arry) {
        return $this->model->dispatchCommandArray($arry);

    }

    public function getLastErrors($flush = true) {
        return $this->model->getLastErrors($flush);
    }

}

?>
